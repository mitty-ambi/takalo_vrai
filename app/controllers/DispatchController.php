<?php

namespace app\controllers;

use app\models\Besoin;
use app\models\Dons;
use app\models\Achat;

class DispatchController
{
    /**
     * Valider et dispatcher les dons selon la répartition donnée
     * @param array $repartition
     * @return array ['success' => bool, 'message' => string]
     */
    public static function validerDispatch($repartition)
    {
        error_log('[DEBUG] validerDispatch appelé avec repartition: ' . json_encode($repartition));

        if (empty($repartition)) {
            error_log('[ERROR] Repartition vide');
            return ['success' => false, 'message' => 'Aucune répartition fournie'];
        }

        $DBH = \Flight::db();
        $DBH->beginTransaction();

        try {
            $total_updates = 0;
            foreach ($repartition as $attribution) {
                $id_ville = $attribution['id_ville'];
                $quantite_a_dispatcher = $attribution['quantite'];
                $id_besoin = $attribution['id_besoin'];

                error_log('[DEBUG] Traitement attribution: besoin=' . $id_besoin . ', ville=' . $id_ville . ', quantite=' . $quantite_a_dispatcher);

                $besoin = Besoin::getMatiereByBesoin($id_besoin);

                if (!$besoin) {
                    error_log('[WARN] Besoin non trouvé: ' . $id_besoin);
                    continue;
                }

                $id_matiere = $besoin['id_matiere'];
                error_log('[DEBUG] id_matiere du besoin: ' . $id_matiere);

                // Récupérer les dons non distribués de cette matière
                $dons = Dons::getDonsNonDistribuesPourDispatch($id_matiere);
                error_log('[DEBUG] Dons disponibles pour matiere ' . $id_matiere . ': ' . count($dons));

                // Dispatcher les dons
                $quantite_restante = $quantite_a_dispatcher;
                foreach ($dons as $don) {
                    if ($quantite_restante <= 0)
                        break;

                    $quantite_a_prendre = min($don['quantite'], $quantite_restante);

                    error_log('[DEBUG] Dispatch don ' . $don['id_don'] . ' vers ville ' . $id_ville . ', quantite=' . $quantite_a_prendre);

                    // Mettre à jour le don
                    $ok = Dons::dispatcherDon($don['id_don'], $id_ville, $quantite_a_prendre);
                    error_log('[DEBUG] Résultat dispatcherDon: ' . ($ok ? 'OK' : 'FAIL'));

                    $quantite_restante -= $quantite_a_prendre;
                    $total_updates++;
                }
            }

            error_log('[DEBUG] Total updates effectués: ' . $total_updates);

            // Supprimer les dons avec quantité 0 (résidus du dispatch)
            $query_delete_zeros = "DELETE FROM Dons WHERE quantite <= 0";
            $deleted_count = $DBH->exec($query_delete_zeros);
            error_log('[DEBUG] Dons avec quantité 0 supprimés: ' . $deleted_count);

            $DBH->commit();
            return ['success' => true, 'message' => 'Dispatch validé avec succès (' . $total_updates . ' splits, ' . $deleted_count . ' dons supprimés)'];
        } catch (\Exception $e) {
            error_log('[ERROR] Exception dans validerDispatch: ' . $e->getMessage());
            $DBH->rollBack();
            return ['success' => false, 'message' => 'Erreur lors du dispatch: ' . $e->getMessage()];
        }
    }

    /**
     * Dispatcher simple : assigner les dons non distribués aux villes avec besoins
     * Optionnellement : dispatcher une seule matière
     * @param int|null $id_matiere Si fourni, dispatcher seulement cette matière
     * @return array ['success' => bool, 'message' => string]
     */
    public static function dispatcherSimple($id_matiere = null)
    {
        $DBH = \Flight::db();
        $DBH->beginTransaction();

        try {
            $updated = 0;

            // Récupérer tous les besoins, triés par date de demande
            $query_besoins = "SELECT b.id_besoin, b.id_matiere, b.id_ville, b.quantite 
                              FROM Besoin b WHERE b.quantite > 0";
            if ($id_matiere !== null) {
                $query_besoins .= " AND b.id_matiere = :id_matiere";
            }
            $query_besoins .= " ORDER BY b.date_du_demande ASC";
            
            $stmt_besoins = $DBH->prepare($query_besoins);
            if ($id_matiere !== null) {
                $stmt_besoins->bindValue(':id_matiere', (int) $id_matiere, \PDO::PARAM_INT);
            }
            $stmt_besoins->execute();
            $besoins = $stmt_besoins->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($besoins)) {
                $DBH->rollBack();
                return ['success' => false, 'message' => 'Aucun besoin trouvé'];
            }

            // Pour chaque besoin, distribuer à partir des dons non distribués
            foreach ($besoins as $besoin) {
                $quantite_restante = $besoin['quantite'];
                
                // Récupérer les dons non distribués FRAIS (re-query à chaque fois pour voir l'état actuel)
                $dons = Dons::getDonsNonDistribuesPourDispatch($besoin['id_matiere']);
                
                if (empty($dons)) continue;

                foreach ($dons as $don) {
                    if ($quantite_restante <= 0) break;
                    if ($don['quantite'] <= 0) continue;

                    $quantite_a_prendre = min($don['quantite'], $quantite_restante);

                    // Utiliser dispatcherDon qui divise correctement le don
                    $ok = Dons::dispatcherDon($don['id_don'], $besoin['id_ville'], $quantite_a_prendre);

                    if ($ok) {
                        $quantite_restante -= $quantite_a_prendre;
                        $updated++;
                    }
                }
            }

            // Supprimer les dons avec quantité 0 (résidus du dispatch)
            $query_delete_zeros = "DELETE FROM Dons WHERE quantite <= 0";
            $deleted_count = $DBH->exec($query_delete_zeros);
            error_log('[DEBUG] Dons avec quantité 0 supprimés dans dispatcherSimple: ' . $deleted_count);

            $DBH->commit();
            return ['success' => true, 'message' => $updated . ' dons dispatchés, ' . $deleted_count . ' dons supprimés'];
        } catch (\Exception $e) {
            $DBH->rollBack();
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Réinitialiser toutes les données (dons et achats)
     * @return array ['success' => bool, 'message' => string]
     */
    public static function reinitialiser()
    {
        $DBH = \Flight::db();
        $DBH->beginTransaction();

        try {
            // Supprimer tous les achats
            Achat::deleteAll();
            error_log('[INFO] Tous les achats supprimés');

            // Réinitialiser tous les dons (id_ville = 0)
            Dons::reinitialiserTous();
            error_log('[INFO] Tous les dons réinitialisés (id_ville = 0)');

            $DBH->commit();
            return [
                'success' => true,
                'message' => 'Réinitialisation complète réussie. Tous les dons sont maintenant non distribués, les besoins sont réinitialisés à leurs valeurs initiales, et tous les achats ont été supprimés.'
            ];
        } catch (\Exception $e) {
            $DBH->rollBack();
            return ['success' => false, 'message' => 'Erreur lors de la réinitialisation: ' . $e->getMessage()];
        }
    }
}
