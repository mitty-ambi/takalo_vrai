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
}
