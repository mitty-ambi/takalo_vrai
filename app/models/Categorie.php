<?php
class Categorie
{
    public $id_categorie;
    public $nom_categorie;

    public function __construct($id_categorie = null, $nom_categorie = null)
    {
        $this->id_categorie = $id_categorie;
        $this->nom_categorie = $nom_categorie;
    }
    public function insert()
    {
        $DBH = \Flight::db();
        $stmt = $DBH->prepare("INSERT INTO Categorie (nom_categorie) VALUES (:nom_categorie)");
        $stmt->bindParam(':nom_categorie', $this->nom_categorie);
        return $stmt->execute();
    }
    public function delete_cat()
    {
        $DBH = \Flight::db();
        $sql = $DBH->prepare("DELETE FROM Categorie WHERE id_categorie = ?");
        $sql->bindValue(1, $this->id_categorie, \PDO::PARAM_INT);
        $sql->execute();
    }
    public function getAll()
    {
    
    }
}
?>