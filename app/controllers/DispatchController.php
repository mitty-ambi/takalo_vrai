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

            $DBH->commit();
            return ['success' => true, 'message' => 'Dispatch validé avec succès (' . $total_updates . ' updates)'];
        } catch (\Exception $e) {
            error_log('[ERROR] Exception dans validerDispatch: ' . $e->getMessage());
            $DBH->rollBack();
            return ['success' => false, 'message' => 'Erreur lors du dispatch: ' . $e->getMessage()];
        }
    }

    /**
     * Dispatcher simple : assigner les dons non distribués aux villes avec besoins
     * @return array ['success' => bool, 'message' => string]
     */
    public static function dispatcherSimple()
    {
        $dons_non_distribues = Dons::getDonsNonDistribuee();

        if (empty($dons_non_distribues)) {
            return ['success' => false, 'message' => 'Aucun don à dispatcher'];
        }

        $DBH = \Flight::db();
        $DBH->beginTransaction();

        try {
            $updated = 0;

            // Récupérer tous les besoins
            $besoins = Besoin::getAll();

            // Pour chaque besoin, chercher un don correspondant
            foreach ($besoins as $besoin) {
                $id_ville = $besoin['id_ville'];
                $id_matiere = $besoin['id_matiere'];

                // Chercher un don correspondant à cette matière et non encore distribué
                foreach ($dons_non_distribues as $don) {
                    if ($don['id_matiere'] == $id_matiere && $don['id_ville'] == 0) {
                        // Dispatcher ce don à la ville
                        $query = "UPDATE Dons SET id_ville = :id_ville WHERE id_don = :id_don";
                        $stmt = $DBH->prepare($query);
                        $stmt->bindValue(':id_ville', (int) $id_ville, \PDO::PARAM_INT);
                        $stmt->bindValue(':id_don', (int) $don['id_don'], \PDO::PARAM_INT);

                        if ($stmt->execute()) {
                            $updated++;
                        }
                        break;
                    }
                }
            }

            $DBH->commit();
            return ['success' => true, 'message' => $updated . ' dons dispatchés'];
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
