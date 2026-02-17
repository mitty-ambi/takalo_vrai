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

        // Besoins totaux en montant
        $query_total = "SELECT COALESCE(SUM(b.quantite * m.prix_unitaire), 0) as montant_total
                        FROM Besoin b
                        JOIN Matiere m ON b.id_matiere = m.id_matiere";
        $total = $DBH->query($query_total)->fetch(PDO::FETCH_ASSOC);

        // Besoins satisfaits en montant
        $query_satisfait = "SELECT COALESCE(SUM(d.quantite * m.prix_unitaire), 0) as montant_satisfait
                           FROM Dons d
                           JOIN Matiere m ON d.id_matiere = m.id_matiere";
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
