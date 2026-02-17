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

        // Besoins satisfaits en montant (dons assignés à une ville ET qui correspondent à un besoin de cette ville)
        $query_satisfait = "SELECT COALESCE(SUM(d.quantite * m.prix_unitaire), 0) as montant_satisfait
                           FROM Dons d
                           JOIN Matiere m ON d.id_matiere = m.id_matiere
                           WHERE d.id_ville > 0 
                           AND EXISTS (
                               SELECT 1 FROM Besoin b 
                               WHERE b.id_ville = d.id_ville 
                               AND b.id_matiere = d.id_matiere
                           )";
        $satisfait = $DBH->query($query_satisfait)->fetch(PDO::FETCH_ASSOC);

        // Besoins restants
        $montant_restant = $total['montant_total'] - $satisfait['montant_satisfait'];

        return [
            'montant_total' => (float) $total['montant_total'],
            'montant_satisfait' => (float) $satisfait['montant_satisfait'],
            'montant_restant' => (float) $montant_restant
        ];
    }
}
