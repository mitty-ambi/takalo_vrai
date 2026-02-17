<?php

namespace app\models;

use PDO;

class Stats
{
    /**
     * Récupérer les statistiques récapitulatives globales
     * @return array ['montant_total', 'montant_satisfait', 'montant_restant']
     */
    public static function getRecapGlobal()
    {
        $DBH = \Flight::db();

        // Besoins totaux en montant (TOUS les besoins de TOUTES les villes)
        $query_total = "SELECT COALESCE(SUM(b.quantite * m.prix_unitaire), 0) as montant_total
                        FROM Besoin b
                        JOIN Matiere m ON b.id_matiere = m.id_matiere";
        $total = $DBH->query($query_total)->fetch(PDO::FETCH_ASSOC);

        // Besoins satisfaits = MIN(quantité reçue, quantité demandée) × prix_unitaire
        // On ne peut pas satisfaire PLUS que ce qui est demandé
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
                            JOIN Matiere m ON b.id_matiere = m.id_matiere";
        $besoins = $DBH->query($query_satisfait)->fetchAll(PDO::FETCH_ASSOC);

        $montant_satisfait = 0;
        foreach ($besoins as $besoin) {
            // Plafonner au besoin : on ne peut pas être satisfait à plus de 100%
            $quantite_satisfaite = min((float) $besoin['besoin'], (float) $besoin['recu']);
            $montant_satisfait += $quantite_satisfaite * (float) $besoin['prix_unitaire'];
        }

        // Besoins restants
        $montant_restant = $total['montant_total'] - $montant_satisfait;

        return [
            'montant_total' => (float) $total['montant_total'],
            'montant_satisfait' => (float) $montant_satisfait,
            'montant_restant' => (float) $montant_restant
        ];
    }
}
