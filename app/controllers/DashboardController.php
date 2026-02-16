<?php

namespace app\controllers;

use app\models\Dons;
use app\models\Besoin;
use app\models\Matiere;
use app\models\Ville;
use Flight;

class DashboardController
{
    /**
     * Get dashboard statistics
     */
    public static function getStats()
    {
        $db = Flight::db();

        // Total donations and status
        $total_dons = count(Dons::getAll());
        $dons_non_distribuees = count(Dons::getDonsNonDistribuee());
        $dons_distribuees = $total_dons - $dons_non_distribuees;

        // Total needs
        $query = $db->query("SELECT COUNT(*) as count FROM Besoin");
        $result = $query->fetch(\PDO::FETCH_ASSOC);
        $total_besoins = $result['count'] ?? 0;

        // Number of affected cities
        $villes = Ville::getAll();
        $nb_villes = count($villes);

        // Number of material types
        $matieres = Matiere::getAll();
        $nb_matieres = count($matieres);

        return [
            'total_dons' => $total_dons,
            'dons_distribuees' => $dons_distribuees,
            'dons_non_distribuees' => $dons_non_distribuees,
            'total_besoins' => $total_besoins,
            'nb_villes' => $nb_villes,
            'nb_matieres' => $nb_matieres
        ];
    }

    /**
     * Get top materials in donations
     */
    public static function getTopMatieresDons($limit = 5)
    {
        $db = Flight::db();
        $query = $db->query("
            SELECT m.nom_matiere, SUM(d.quantite) as total_quantite
            FROM Dons d
            JOIN Matiere m ON d.id_matiere = m.id_matiere
            GROUP BY d.id_matiere, m.nom_matiere
            ORDER BY total_quantite DESC
            LIMIT " . (int)$limit
        );
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get top cities with needs
     */
    public static function getTopVillesBesoins($limit = 5)
    {
        $db = Flight::db();
        $query = $db->query("
            SELECT v.nom_ville, SUM(b.quantite) as total_quantite
            FROM Besoin b
            JOIN Ville v ON b.id_ville = v.id_ville
            GROUP BY b.id_ville, v.nom_ville
            ORDER BY total_quantite DESC
            LIMIT " . (int)$limit
        );
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get donations by material
     */
    public static function getDonationsByMatiere()
    {
        $db = Flight::db();
        $query = $db->query("
            SELECT m.nom_matiere, SUM(d.quantite) as total_quantite
            FROM Dons d
            JOIN Matiere m ON d.id_matiere = m.id_matiere
            GROUP BY d.id_matiere, m.nom_matiere
            ORDER BY m.nom_matiere
        ");
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get needs by material
     */
    public static function getBesoinesByMatiere()
    {
        $db = Flight::db();
        $query = $db->query("
            SELECT m.nom_matiere, SUM(b.quantite) as total_quantite
            FROM Besoin b
            JOIN Matiere m ON b.id_matiere = m.id_matiere
            GROUP BY b.id_matiere, m.nom_matiere
            ORDER BY m.nom_matiere
        ");
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get donations by city
     */
    public static function getDonationsByVille()
    {
        $db = Flight::db();
        $query = $db->query("
            SELECT v.nom_ville, COUNT(d.id_don) as total_dons
            FROM Dons d
            JOIN Ville v ON d.id_ville = v.id_ville
            WHERE d.id_ville != 0
            GROUP BY d.id_ville, v.nom_ville
            ORDER BY v.nom_ville
        ");
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get recent donations
     */
    public static function getRecentDonations($limit = 10)
    {
        $db = Flight::db();
        $query = $db->query("
            SELECT d.id_don, m.nom_matiere, d.quantite, d.date_don, 
                   COALESCE(v.nom_ville, 'Non assignée') as nom_ville
            FROM Dons d
            JOIN Matiere m ON d.id_matiere = m.id_matiere
            LEFT JOIN Ville v ON d.id_ville = v.id_ville AND d.id_ville != 0
            ORDER BY d.date_don DESC
            LIMIT " . (int)$limit
        );
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Prepare chart data for materials donations
     */
    public static function prepareMatieresChartData()
    {
        $data = self::getDonationsByMatiere();
        $labels = array_map(function ($item) {
            return $item['nom_matiere'];
        }, $data);
        $values = array_map(function ($item) {
            return $item['total_quantite'];
        }, $data);

        return [
            'labels' => $labels,
            'data' => $values
        ];
    }

    /**
     * Prepare chart data for materials needs
     */
    public static function prepareBesoinsChartData()
    {
        $data = self::getBesoinesByMatiere();
        $labels = array_map(function ($item) {
            return $item['nom_matiere'];
        }, $data);
        $values = array_map(function ($item) {
            return $item['total_quantite'];
        }, $data);

        return [
            'labels' => $labels,
            'data' => $values
        ];
    }

    /**
     * Prepare chart data for cities donations
     */
    public static function prepareVillesChartData()
    {
        $data = self::getDonationsByVille();
        $labels = array_map(function ($item) {
            return $item['nom_ville'];
        }, $data);
        $values = array_map(function ($item) {
            return $item['total_dons'];
        }, $data);

        return [
            'labels' => $labels,
            'data' => $values
        ];
    }

    /**
     * Get all dashboard data
     */
    public static function getDashboardData()
    {
        $stats = self::getStats();
        $top_matieres_dons = self::getTopMatieresDons();
        $top_villes_besoins = self::getTopVillesBesoins();
        $matieres_chart = self::prepareMatieresChartData();
        $besoins_chart = self::prepareBesoinsChartData();
        $villes_chart = self::prepareVillesChartData();
        $recent_donations = self::getRecentDonations();

        return array_merge(
            $stats,
            [
                'top_matieres_dons' => $top_matieres_dons,
                'top_villes_besoins' => $top_villes_besoins,
                'matieres_labels' => $matieres_chart['labels'],
                'matieres_data' => $matieres_chart['data'],
                'besoins_labels' => $besoins_chart['labels'],
                'besoins_data' => $besoins_chart['data'],
                'villes_labels' => $villes_chart['labels'],
                'villes_data' => $villes_chart['data'],
                'recent_donations' => $recent_donations
            ]
        );
    }
}
