<?php

namespace app\controllers;

use app\models\Besoin;
use PDO;

class BesoinController
{
    public static function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_matiere = $_POST['id_matiere'] ?? null;
            $id_ville = $_POST['id_ville'] ?? null;
            $id_categorie = $_POST['id_categorie'] ?? null;
            $quantite = $_POST['quantite'] ?? null;
            if (!$id_matiere || !$id_ville || !$quantite) {
                return [
                    'success' => false,
                    'message' => 'Tous les champs sont obligatoires'
                ];
            }

            $besoin = new Besoin(null, $id_matiere, $id_ville, $quantite);

            if ($besoin->insert_base()) {
                return [
                    'success' => true,
                    'message' => 'Besoin créé avec succès'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la création du besoin'
                ];
            }
        }
    }

    public static function getAll()
    {
        return \app\models\Besoin::getAll();
    }

    public static function getById($id_besoin)
    {
        return \app\models\Besoin::getById($id_besoin);
    }


    public static function delete($id_besoin)
    {
        return \app\models\Besoin::delete($id_besoin);
    }

    public static function update($id_besoin, $id_matiere, $id_ville, $quantite)
    {
        return \app\models\Besoin::update($id_besoin, $id_matiere, $id_ville, $quantite);
    }
    public static function getBesoinVIlle($id_ville)
    {
        return Besoin::getBesoinVIlle($id_ville);
    }

    /**
     * Récupérer les besoins restants (non achetés)
     */
    public static function getRemainingBesoins()
    {
        $DBH = \Flight::db();
        $query = "SELECT b.*, m.nom_matiere, m.prix_unitaire, m.id_categorie, v.nom_ville, c.nom as nom_categorie
                  FROM Besoin b
                  JOIN Matiere m ON b.id_matiere = m.id_matiere
                  JOIN Ville v ON b.id_ville = v.id_ville
                  LEFT JOIN Categorie c ON m.id_categorie = c.id_categorie
                  WHERE b.id_besoin NOT IN (SELECT COALESCE(id_besoin, 0) FROM Achats WHERE id_besoin IS NOT NULL)
                  ORDER BY v.nom_ville, m.nom_matiere";
        $stmt = $DBH->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer les besoins groupés par matière avec totaux par ville
     */
    public static function getBesoinVilleGrouped($id_ville)
    {
        $DBH = \Flight::db();
        $query = "SELECT m.nom_matiere, SUM(b.quantite) as total_quantite, m.prix_unitaire
                  FROM Besoin b
                  JOIN Matiere m ON b.id_matiere = m.id_matiere
                  WHERE b.id_ville = :id_ville
                  GROUP BY b.id_matiere, m.nom_matiere, m.prix_unitaire
                  ORDER BY m.nom_matiere";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_ville', (int) $id_ville, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

