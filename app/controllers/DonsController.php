<?php

namespace app\controllers;

use app\models\Dons;
use PDO;
class DonsController
{
    public static function getDonsVille($id_ville)
    {
        return Dons::getDonsVille($id_ville);

class DonsController
{
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
}
