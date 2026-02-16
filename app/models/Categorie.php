<?php

namespace app\models;

use PDO;

class Categorie
{
    public $id_categorie;
    public $nom_categorie;

    public function __construct($id_achat, $nom_categorie)
    {
        $this->id_achat = $id_achat;
        $this->nom_categorie = $nom_categorie;
    }
    public static function getAll()
    {
        $DBH = \Flight::db();
        $sql = $DBH->prepare("SELECT * FROM Categorie");
        $sql->execute();
        $data = [];
        while ($x = $sql->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $x;
        }
        return $data;
    }
}
