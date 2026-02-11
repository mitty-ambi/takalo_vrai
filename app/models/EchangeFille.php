<?php
namespace app\models;
use Flight;

class EchangeFille
{
    public $id_echange_fille;
    public $id_echange_mere;
    public $id_objet;
    public $quantite;
    public $id_proprietaire;

    public function __construct($id_echange_fille, $id_echange_mere, $id_objet, $quantite, $id_proprietaire)
    {
        $this->id_echange_fille = $id_echange_fille;
        $this->id_echange_mere = $id_echange_mere;
        $this->id_objet = $id_objet;
        $this->quantite = $quantite;
        $this->id_proprietaire = $id_proprietaire;
    }
    public function getAllEchangeFille()
    {
        $DBH = \Flight::db();
        $sql = $DBH->prepare('SELECT * FROM Echange_fille');
        $sql->execute();
        $data = [];
        while ($x = $sql->fetch(\PDO::FETCH_ASSOC)) {
            $data[] = $x;
        }
        return $data;
    }
}