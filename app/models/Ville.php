<?php

namespace app\models;

use PDO;

class Ville
{
    public $id_ville;
    public $nom_ville;
    public $id_region;
    public $nombres_sinistres;

    public function __construct($id_ville, $nom_ville, $id_region, $nombres_sinistres)
    {
        $this->id_ville = $id_ville;
        $this->nom_ville = $nom_ville;
        $this->id_region = $id_region;
        $this->nombres_sinistres = $nombres_sinistres;
    }

    public function insert_base()
    {
        $DBH = \Flight::db();
        $query = "INSERT INTO Ville (nom_ville) VALUES (:nom_ville)";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':nom_ville', $this->nom_ville);
        return $stmt->execute();
    }

    public static function getAll()
    {
        $DBH = \Flight::db();
        $query = "SELECT * FROM Ville JOIN Region ON Ville.id_region = Region.id_region";
        $stmt = $DBH->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getNomVIlle($id_ville)
    {
        $DBH = \Flight::db();
        $sql = $DBH->prepare("SELECT nom_ville FROM Ville WHERE id_ville = ?");
        $sql->bindValue(1, $id_ville, PDO::PARAM_INT);
        $sql->execute();
        $nom = $sql->fetch(PDO::FETCH_ASSOC);
        if ($nom) {
            return $nom['nom_ville'];
        }
        return null;
    }
}

