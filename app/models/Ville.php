<?php

namespace app\models;

use PDO;

class Ville{
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
        $query = "SELECT * FROM Ville";
        $stmt = $DBH->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}