<?php

namespace app\controllers;

use app\models\Dons;
use PDO;

class DonsController
{
    public static function getDonsVille($id_ville)
    {
        return Dons::getDonsVille($id_ville);
    }

    public static function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_matiere = $_POST['matiere'] ?? null;
            $quantite = $_POST['quantite'] ?? null;
            $date_don = $_POST['date_don'] ?? null;
            $id_ville = $_POST['id_ville'] ?? null;

            if (!$id_matiere || !$quantite || !$date_don) {
                return [
                    'success' => false,
                    'message' => 'Les champs matière, quantité et date sont obligatoires'
                ];
            }

            $don = new Dons(null, $id_matiere, $quantite, $date_don, $id_ville);

            if ($don->insert_base()) {
                return [
                    'success' => true,
                    'message' => 'Don créé avec succès'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la création du don'
                ];
            }
        }
    }

    public static function getAll()
    {
        return \app\models\Dons::getAll();
    }

    public static function getById($id_don)
    {
        return \app\models\Dons::getById($id_don);
    }

    public static function getByVille($id_ville)
    {
        return \app\models\Dons::getByVille($id_ville);
    }

    public static function update($id_don, $id_matiere, $quantite, $date_don, $id_ville)
    {
        return \app\models\Dons::update($id_don, $id_matiere, $quantite, $date_don, $id_ville);
    }

    /**
     * Supprimer un don
     */
    public static function delete($id_don)
    {
        return \app\models\Dons::delete($id_don);
    }

    /**
     * Récupérer les dons groupés par matière avec totaux par ville
     */
    public static function getDonsVilleGrouped($id_ville)
    {
        $DBH = \Flight::db();
        $query = "SELECT m.nom_matiere, SUM(d.quantite) as total_quantite, m.prix_unitaire, MAX(d.date_don) as last_date
                  FROM Dons d
                  JOIN Matiere m ON d.id_matiere = m.id_matiere
                  WHERE d.id_ville = :id_ville
                  GROUP BY d.id_matiere, m.nom_matiere, m.prix_unitaire
                  ORDER BY m.nom_matiere";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_ville', (int) $id_ville, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer les besoins restants (Besoins - Dons) groupés par matière
     */
    public static function getBesoinRestantVille($id_ville)
    {
        $DBH = \Flight::db();
        $query = "SELECT 
                    m.id_matiere,
                    m.nom_matiere, 
                    m.prix_unitaire,
                    COALESCE(SUM(b.quantite), 0) as total_besoin,
                    COALESCE(SUM(d.quantite), 0) as total_don,
                    COALESCE(SUM(b.quantite), 0) - COALESCE(SUM(d.quantite), 0) as reste
                  FROM Matiere m
                  LEFT JOIN Besoin b ON m.id_matiere = b.id_matiere AND b.id_ville = :id_ville
                  LEFT JOIN Dons d ON m.id_matiere = d.id_matiere AND d.id_ville = :id_ville
                  GROUP BY m.id_matiere, m.nom_matiere, m.prix_unitaire
                  HAVING COALESCE(SUM(b.quantite), 0) > 0 AND COALESCE(SUM(b.quantite), 0) - COALESCE(SUM(d.quantite), 0) > 0
                  ORDER BY m.nom_matiere";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_ville', (int) $id_ville, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
