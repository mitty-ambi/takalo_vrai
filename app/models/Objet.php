<?php

use app\models\Connection;
class Objet{
    public $id;
    public $nom;
    public $id_categorie;
    public $id_user;
    public $date_acquisition;
    public $description;
    public $prix_estime;

    public function __construct($id = null, $nom = null, $id_categorie = null, $id_user = null, $date_acquisition = null, $description = null, $prix_estime = null) {
        $this->id = $id;
        $this->nom = $nom;
        $this->id_categorie = $id_categorie;
        $this->id_user = $id_user;
        $this->date_acquisition = $date_acquisition;
        $this->description = $description;
        $this->prix_estime = $prix_estime;
    }
    public function set_categorie_base($id_categorie, $id_objet){
        $DBH = Connection::dbconnect();
        $query = "UPDATE Objet SET id_categorie = :id_categorie WHERE id = :id";
        $stmt = $DBH->prepare($query);
        $stmt->bindParam(':id_categorie', $id_categorie);
        $stmt->bindParam(':id', $id_objet);
        if ($stmt->execute()) {
            return true;
        }else{
            return false;
        }
    }
    public function set_user_base($id_user, $id_objet){
        $DBH = Connection::dbconnect();
        $query = "UPDATE Objet SET id_user = :id_user WHERE id = :id";
        $stmt = $DBH->prepare($query);
        $stmt->bindParam(':id_user', $id_user);
        $stmt->bindParam(':id', $id_objet);
        if ($stmt->execute()) {
            return true;
        }else{
            return false;
        }
    }
    public function insert_base(){
        $DBH = Connection::dbconnect();
        $query = "INSERT INTO Objet (nom, id_categorie, id_user, date_acquisition, description, prix_estime) VALUES (:nom, :id_categorie, :id_user, :date_acquisition, :description, :prix_estime)";
        $stmt = $DBH->prepare($query);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':id_categorie', $this->id_categorie);
        $stmt->bindParam(':id_user', $this->id_user);
        $stmt->bindParam(':date_acquisition', $this->date_acquisition);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':prix_estime', $this->prix_estime);
        if ($stmt->execute()) {
            return true;
        }else{
            return false;
        }
    }
    public function get_objet_by_id_user($id_user){
        $DBH = Connection::dbconnect();
        $query = "SELECT * FROM Objet WHERE id_user = :id_user";
        $stmt = $DBH->prepare($query);
        $stmt->bindParam(':id_user', $id_user);
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }
    public function get_objets_autre_user($id_user){
        $DBH = Connection::dbconnect();
        $query = "SELECT * FROM Objet WHERE id_user != :id_user";
        $stmt = $DBH->prepare($query);
        $stmt->bindParam(':id_user', $id_user);
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }
}
?>