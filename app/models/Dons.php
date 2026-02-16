<?php
class Dons
{
    public $id_don;
    public $id_matiere;
    public $quantite;
    public $date_don;

    public $id_ville;

    public function __construct($id_don, $id_matiere, $quantite, $date_don, $id_ville)
    {
        $this->id_don = $id_don;
        $this->id_matiere = $id_matiere;
        $this->$quantite = $quantite;
        $this->date_don = $date_don;
        $this->id_ville = $id_ville;
    }
}

?>