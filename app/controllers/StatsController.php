<?php

namespace app\controllers;

use app\models\Besoin;
use app\models\Dons;
use app\models\Matiere;
use app\models\Ville;

class StatsController
{
    /**
     * Récupérer les statistiques récapitulatives globales
     * Retourne: montant_total, montant_satisfait, montant_restant
     */
    public static function getRecap()
    {
        $DBH = \Flight::db();

        // Besoins totaux en montant (TOUS les besoins de TOUTES les villes)
        $query_total = "SELECT COALESCE(SUM(b.quantite * m.prix_unitaire), 0) as montant_total
                        FROM Besoin b
                        JOIN Matiere m ON b.id_matiere = m.id_matiere
                        WHERE b.id_ville > 0";
        $total = $DBH->query($query_total)->fetch(\PDO::FETCH_ASSOC);

        // Calcul du satisfait : pour chaque besoin, MIN(quantité reçue, quantité demandée)
        $query_satisfait = "SELECT 
                                b.id_besoin,
                                b.id_ville,
                                b.id_matiere,
                                b.quantite as besoin,
                                m.prix_unitaire,
                                COALESCE((SELECT SUM(d.quantite) 
                                         FROM Dons d 
                                         WHERE d.id_ville = b.id_ville 
                                         AND d.id_matiere = b.id_matiere), 0) as recu
                            FROM Besoin b
                            JOIN Matiere m ON b.id_matiere = m.id_matiere
                            WHERE b.id_ville > 0";
        $besoins = $DBH->query($query_satisfait)->fetchAll(\PDO::FETCH_ASSOC);

        $montant_satisfait = 0;
        foreach ($besoins as $besoin) {
            $quantite_satisfaite = min((float) $besoin['besoin'], (float) $besoin['recu']);
            $montant_satisfait += $quantite_satisfaite * (float) $besoin['prix_unitaire'];
        }

        // Besoins restants
        $montant_restant = $total['montant_total'] - $montant_satisfait;

        return [
            'success' => true,
            'montant_total' => (float) $total['montant_total'],
            'montant_satisfait' => (float) $montant_satisfait,
            'montant_restant' => (float) $montant_restant
        ];
    }

    /**
     * Récupérer les statistiques par ville
     * Retourne: tableau de villes avec montants total, satisfait, restant
     * Satisfait = MIN(dons reçus, besoin) pour chaque matière - on ne peut pas satisfaire plus que demandé
     */
    public static function getStatsByVilles()
    {
        $DBH = \Flight::db();

        // Récupérer toutes les villes
        $villes = $DBH->query("SELECT id_ville, nom_ville FROM Ville ORDER BY nom_ville")->fetchAll(\PDO::FETCH_ASSOC);

        $result = [];
        foreach ($villes as $ville) {
            $id_ville = $ville['id_ville'];

            // Besoins totaux de la ville
            $query_total = "SELECT COALESCE(SUM(b.quantite * m.prix_unitaire), 0) as montant_total
                            FROM Besoin b
                            JOIN Matiere m ON b.id_matiere = m.id_matiere
                            WHERE b.id_ville = :id_ville";
            $stmt = $DBH->prepare($query_total);
            $stmt->execute([':id_ville' => $id_ville]);
            $montant_total = (float) $stmt->fetch(\PDO::FETCH_ASSOC)['montant_total'];

            // Besoins satisfaits (plafonné au besoin)
            $query_besoins = "SELECT 
                                b.quantite as besoin,
                                m.prix_unitaire,
                                COALESCE((SELECT SUM(d.quantite) 
                                         FROM Dons d 
                                         WHERE d.id_ville = b.id_ville 
                                         AND d.id_matiere = b.id_matiere), 0) as recu
                            FROM Besoin b
                            JOIN Matiere m ON b.id_matiere = m.id_matiere
                            WHERE b.id_ville = :id_ville";
            $stmt = $DBH->prepare($query_besoins);
            $stmt->execute([':id_ville' => $id_ville]);
            $besoins = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $montant_satisfait = 0;
            foreach ($besoins as $besoin) {
                $quantite_satisfaite = min((float) $besoin['besoin'], (float) $besoin['recu']);
                $montant_satisfait += $quantite_satisfaite * (float) $besoin['prix_unitaire'];
            }

            $result[] = [
                'id_ville' => $id_ville,
                'nom_ville' => $ville['nom_ville'],
                'montant_total' => $montant_total,
                'montant_satisfait' => $montant_satisfait,
                'montant_restant' => $montant_total - $montant_satisfait
            ];
        }

        return $result;
    }

    /**
     * Récupérer les besoins restants (non achetés)
     */
    public static function getBesoinsRemaining()
    {
        $DBH = \Flight::db();
        $query = "SELECT b.*, m.nom_matiere, m.prix_unitaire, v.nom_ville
                  FROM Besoin b
                  JOIN Matiere m ON b.id_matiere = m.id_matiere
                  JOIN Ville v ON b.id_ville = v.id_ville
                  WHERE b.id_besoin NOT IN (SELECT id_besoin FROM Achats WHERE id_besoin IS NOT NULL)
                  ORDER BY v.nom_ville, m.nom_matiere";
        $stmt = $DBH->query($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer les dons non distribués (id_ville = 0)
     */
    public static function getDonsNonDistribues()
    {
        $DBH = \Flight::db();
        $query = "SELECT d.id_don, d.id_matiere, m.nom_matiere, d.quantite, m.prix_unitaire, 
                         d.date_don, d.id_ville
                  FROM Dons d
                  JOIN Matiere m ON d.id_matiere = m.id_matiere
                  WHERE d.id_ville = 0
                  ORDER BY d.date_don DESC";
        $stmt = $DBH->query($query);
        $dons = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Convertir les montants en float
        foreach ($dons as &$don) {
            $don['quantite'] = (float) $don['quantite'];
            $don['prix_unitaire'] = (float) $don['prix_unitaire'];
        }

        return $dons;
    }

    /**
     * Simuler le dispatch des dons non distribués
     */
    public static function simulerDispatch()
    {
        $DBH = \Flight::db();

        // Récupérer tous les dons non distribués
        $query = "SELECT d.id_don, d.id_matiere, m.nom_matiere, d.quantite, m.prix_unitaire, d.id_ville
                  FROM Dons d
                  JOIN Matiere m ON d.id_matiere = m.id_matiere
                  WHERE d.id_ville = 0
                  ORDER BY d.date_don";

        $stmt = $DBH->query($query);
        $dons_non_distribues = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($dons_non_distribues)) {
            return [];
        }

        // Récupérer les besoins restants par matière et ville (exclure les dons non distribués)
        $query_besoins = "SELECT b.id_besoin, b.id_ville, b.id_matiere, v.nom_ville,
                                 b.quantite - COALESCE(SUM(d.quantite), 0) as quantite_restante
                          FROM Besoin b
                          LEFT JOIN Dons d ON b.id_matiere = d.id_matiere AND b.id_ville = d.id_ville AND d.id_ville > 0
                          LEFT JOIN Ville v ON b.id_ville = v.id_ville
                          WHERE b.id_ville > 0
                          GROUP BY b.id_besoin, b.id_ville, b.id_matiere, v.nom_ville
                          HAVING quantite_restante > 0";

        $stmt_besoins = $DBH->query($query_besoins);
        $besoins_restants = $stmt_besoins->fetchAll(\PDO::FETCH_ASSOC);

        // Simulation simple : on répartit chaque don au besoin correspondant
        $simulation = [];
        foreach ($dons_non_distribues as $don) {
            // Chercher un besoin correspondant pour cette matière
            foreach ($besoins_restants as &$besoin) {
                if ($besoin['id_matiere'] == $don['id_matiere'] && $besoin['id_ville'] > 0 && $besoin['quantite_restante'] > 0) {
                    $quantite_a_donner = min((float) $don['quantite'], (float) $besoin['quantite_restante']);

                    $simulation[] = [
                        'id_don' => $don['id_don'],
                        'id_matiere' => $don['id_matiere'],
                        'nom_matiere' => $don['nom_matiere'],
                        'quantite' => $quantite_a_donner,
                        'prix_unitaire' => (float) $don['prix_unitaire'],
                        'id_ville' => $besoin['id_ville'],
                        'nom_ville' => $besoin['nom_ville']
                    ];

                    // Réduire la quantité restante
                    $besoin['quantite_restante'] -= $quantite_a_donner;
                    break;
                }
            }
        }

        return $simulation;
    }

    /**
     * Valider et dispatcher tous les dons non distribués
     */
    public static function validerDispatch()
    {
        $DBH = \Flight::db();

        // Récupérer tous les dons non distribués
        $query = "SELECT d.id_don, d.id_matiere, d.quantite, d.id_ville
                  FROM Dons d
                  WHERE d.id_ville = 0";

        $stmt = $DBH->query($query);
        $dons_non_distribues = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($dons_non_distribues)) {
            return [
                'success' => false,
                'message' => 'Aucun don à distribuer'
            ];
        }

        // Récupérer les besoins restants par matière et ville
        $query_besoins = "SELECT b.id_besoin, b.id_ville, b.id_matiere, 
                                 SUM(b.quantite) - COALESCE(SUM(d.quantite), 0) as quantite_restante
                          FROM Besoin b
                          LEFT JOIN Dons d ON b.id_matiere = d.id_matiere AND b.id_ville = d.id_ville AND d.id_ville > 0
                          WHERE b.id_ville > 0
                          GROUP BY b.id_besoin, b.id_ville, b.id_matiere
                          HAVING quantite_restante > 0";

        $stmt_besoins = $DBH->query($query_besoins);
        $besoins_restants = $stmt_besoins->fetchAll(\PDO::FETCH_ASSOC);

        $nombre_dons_distribues = 0;

        try {
            // Répartir les dons aux villes selon les besoins
            foreach ($dons_non_distribues as $don) {
                // Chercher un besoin correspondant
                foreach ($besoins_restants as &$besoin) {
                    if ($besoin['id_matiere'] == $don['id_matiere'] && $besoin['id_ville'] > 0 && $besoin['quantite_restante'] > 0) {
                        // Assigner ce don à cette ville
                        $update_query = "UPDATE Dons SET id_ville = :id_ville WHERE id_don = :id_don";
                        $stmt_update = $DBH->prepare($update_query);
                        $stmt_update->execute([
                            ':id_ville' => $besoin['id_ville'],
                            ':id_don' => $don['id_don']
                        ]);

                        // Réduire la quantité restante
                        $besoin['quantite_restante'] -= $don['quantite'];
                        $nombre_dons_distribues++;
                        break;
                    }
                }
            }

            return [
                'success' => true,
                'message' => 'Tous les dons ont été distribués',
                'nombre_dons' => $nombre_dons_distribues
            ];
        } catch (\Exception $e) {
            error_log('[ERROR] Erreur lors du dispatch: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors du dispatch'
            ];
        }
    }
}
