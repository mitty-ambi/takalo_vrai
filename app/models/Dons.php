<?php
namespace app\models;

use PDO;

class Dons
{
    public $id_don;
    public $id_matiere;
    public $quantite;
    public $date_don;
    public $id_ville;

    public function __construct($id_don = null, $id_matiere = null, $quantite = null, $date_don = null, $id_ville = null)
    {
        $this->id_don = $id_don;
        $this->id_matiere = $id_matiere;
        $this->quantite = $quantite;
        $this->date_don = $date_don;
        $this->id_ville = $id_ville;
    }

    public static function getDonsVille($id_ville)
    {
        $DBH = \Flight::db();
        $sql = $DBH->prepare("SELECT * FROM Dons JOIN Matiere ON Dons.id_matiere = Matiere.id_matiere WHERE id_ville = ?");
        $sql->bindValue(1, $id_ville, PDO::PARAM_INT);
        $sql->execute();
        $data = [];
        while ($x = $sql->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $x;
        }
        return $data;
    }
}
