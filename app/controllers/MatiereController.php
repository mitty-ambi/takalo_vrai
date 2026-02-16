<?php

namespace app\controllers;

use app\models\Matiere;

class MatiereController
{
    /**
     * Créer une nouvelle matière
     */
    public static function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom_matiere = $_POST['nom_matiere'] ?? null;
            $prix_unitaire = $_POST['prix_unitaire'] ?? null;

            if (!$nom_matiere || !$prix_unitaire) {
                return [
                    'success' => false,
                    'message' => 'Tous les champs sont obligatoires'
                ];
            }

            $matiere = new Matiere(null, $nom_matiere, $prix_unitaire);
            
            if ($matiere->insert_base()) {
                return [
                    'success' => true,
                    'message' => 'Matière créée avec succès'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la création de la matière'
                ];
            }
        }
    }

    /**
     * Récupérer toutes les matières
     */
    public static function getAll()
    {
        return \app\models\Matiere::getAll();
    }

    /**
     * Récupérer une matière par ID
     */
    public static function getById($id_matiere)
    {
        return \app\models\Matiere::getById($id_matiere);
    }

    /**
     * Mettre à jour une matière
     */
    public static function update($id_matiere, $nom_matiere, $prix_unitaire)
    {
        return \app\models\Matiere::update($id_matiere, $nom_matiere, $prix_unitaire);
    }

    /**
     * Supprimer une matière
     */
    public static function delete($id_matiere)
    {
        return \app\models\Matiere::delete($id_matiere);
    }

    /**
     * Chercher une matière par nom
     */
    public static function searchByNom($nom_matiere)
    {
        return \app\models\Matiere::getByNom($nom_matiere);
    }
}
