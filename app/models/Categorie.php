<?php

namespace app\models;

use Flight;

class Categorie
{
    public $id_categorie;
    public $nom_categorie;

    public function __construct($id_categorie = null, $nom_categorie = null)
    {
        $this->id_categorie = $id_categorie;
        $this->nom_categorie = $nom_categorie;
    }

    /**
     * Insert a new categorie into database
     */
    public static function insert_base($nom_categorie)
    {
        $db = Flight::db();
        $stmt = $db->prepare("INSERT INTO Categorie (nom_categorie) VALUES (:nom_categorie)");
        $stmt->bindValue(':nom_categorie', $nom_categorie, \PDO::PARAM_STR);
        return $stmt->execute();
    }

    /**
     * Get all categories
     */
    public static function getAll()
    {
        $db = Flight::db();
        $query = $db->query("SELECT * FROM Categorie ORDER BY nom_categorie ASC");
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get category by ID
     */
    public static function getById($id_categorie)
    {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM Categorie WHERE id_categorie = :id_categorie");
        $stmt->bindValue(':id_categorie', $id_categorie, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Update a category
     */
    public static function update($id_categorie, $nom_categorie = null)
    {
        if ($nom_categorie === null) {
            return false;
        }

        $db = Flight::db();
        $stmt = $db->prepare("UPDATE Categorie SET nom_categorie = :nom_categorie WHERE id_categorie = :id_categorie");
        $stmt->bindValue(':nom_categorie', $nom_categorie, \PDO::PARAM_STR);
        $stmt->bindValue(':id_categorie', $id_categorie, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Delete a category
     */
    public static function delete($id_categorie)
    {
        $db = Flight::db();
        $stmt = $db->prepare("DELETE FROM Categorie WHERE id_categorie = :id_categorie");
        $stmt->bindValue(':id_categorie', $id_categorie, \PDO::PARAM_INT);
        return $stmt->execute();
    }
}
