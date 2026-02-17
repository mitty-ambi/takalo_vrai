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
        if (empty($repartition)) {
            return ['success' => false, 'message' => 'Aucune répartition fournie'];
        }
        
        $DBH = \Flight::db();
        $DBH->beginTransaction();
        
        try {
            foreach ($repartition as $attribution) {
                $id_ville = $attribution['id_ville'];
                $quantite_a_dispatcher = $attribution['quantite'];
                $id_besoin = $attribution['id_besoin'];
                
                // Récupérer le id_matiere du besoin
                $besoin = Besoin::getMatiereByBesoin($id_besoin);
                
                if (!$besoin) continue;
                
                $id_matiere = $besoin['id_matiere'];
                
                // Récupérer les dons non distribués de cette matière
                $dons = Dons::getDonsNonDistribuesPourDispatch($id_matiere);
                
                // Dispatcher les dons
                $quantite_restante = $quantite_a_dispatcher;
                foreach ($dons as $don) {
                    if ($quantite_restante <= 0) break;
                    
                    $quantite_a_prendre = min($don['quantite'], $quantite_restante);
                    
                    // Mettre à jour le don
                    Dons::dispatcherDon($don['id_don'], $id_ville, $quantite_a_prendre);
                    
                    $quantite_restante -= $quantite_a_prendre;
                }
            }
            
            $DBH->commit();
            return ['success' => true, 'message' => 'Dispatch validé avec succès'];
        } catch (\Exception $e) {
            $DBH->rollBack();
            return ['success' => false, 'message' => 'Erreur lors du dispatch: ' . $e->getMessage()];
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
