<?php

namespace app\models;

use PDO;

class Matiere
{
    public $id_matiere;
    public $nom_matiere;
    public $prix_unitaire;
    public $id_categorie;

    public function __construct($id_matiere = null, $nom_matiere = null, $prix_unitaire = null, $id_categorie = null)
    {
        $this->id_matiere = $id_matiere;
        $this->nom_matiere = $nom_matiere;
        $this->prix_unitaire = $prix_unitaire;
        $this->id_categorie = $id_categorie;
    }

    public function insert_base()
    {
        $DBH = \Flight::db();
        $query = "INSERT INTO Matiere (nom_matiere, prix_unitaire) VALUES (:nom_matiere, :prix_unitaire)";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':nom_matiere', $this->nom_matiere);
        $stmt->bindValue(':prix_unitaire', (float) $this->prix_unitaire, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Récupérer toutes les matières
     */
    public static function getAll()
    {
        $DBH = \Flight::db();
        $query = "SELECT * FROM Matiere ORDER BY nom_matiere ASC";
        $stmt = $DBH->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer une matière par ID
     */
    public static function getById($id_matiere)
    {
        $DBH = \Flight::db();
        $query = "SELECT * FROM Matiere WHERE id_matiere = :id_matiere";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_matiere', (int) $id_matiere, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Mettre à jour une matière
     */
    public static function update($id_matiere, $nom_matiere, $prix_unitaire)
    {
        $DBH = \Flight::db();
        $query = "UPDATE Matiere SET nom_matiere = :nom_matiere, prix_unitaire = :prix_unitaire WHERE id_matiere = :id_matiere";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_matiere', (int) $id_matiere, PDO::PARAM_INT);
        $stmt->bindValue(':nom_matiere', $nom_matiere);
        $stmt->bindValue(':prix_unitaire', (float) $prix_unitaire, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * Supprimer une matière
     */
    public static function delete($id_matiere)
    {
        $DBH = \Flight::db();
        $query = "DELETE FROM Matiere WHERE id_matiere = :id_matiere";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_matiere', (int) $id_matiere, PDO::PARAM_INT);
        return $stmt->execute();
    }
    /**
     * Chercher une matière par nom
     */
    public static function getByNom($nom_matiere)
    {
        $DBH = \Flight::db();
        $query = "SELECT * FROM Matiere WHERE nom_matiere LIKE :nom_matiere";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':nom_matiere', '%' . $nom_matiere . '%');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
