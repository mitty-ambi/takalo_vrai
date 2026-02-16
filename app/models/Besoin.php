<?php

namespace app\models;

use PDO;

class Besoin
{
    public $id_besoin;
    public $id_matiere;
    public $id_ville;
    public $quantite;

    public function __construct($id_besoin, $id_matiere, $id_ville, $quantite)
    {
        $this->id_besoin = $id_besoin;
        $this->id_matiere = $id_matiere;
        $this->id_ville = $id_ville;
        $this->quantite = $quantite;
    }
    public function insert_base()
    {
        $DBH = \Flight::db();
        $query = "INSERT INTO Besoin (id_matiere, id_ville, quantite) VALUES (:id_matiere, :id_ville, :quantite)";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_matiere', (int) $this->id_matiere, PDO::PARAM_INT);
        $stmt->bindValue(':id_ville', (int) $this->id_ville, PDO::PARAM_INT);
        $stmt->bindValue(':quantite', $this->quantite);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public static function getAll()
    {
        $DBH = \Flight::db();
        $query = "SELECT * FROM Besoin";
        $stmt = $DBH->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id_besoin)
    {
        $DBH = \Flight::db();
        $query = "SELECT * FROM Besoin WHERE id_besoin = :id_besoin";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_besoin', (int) $id_besoin, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function delete($id_besoin)
    {
        $DBH = \Flight::db();
        $query = "DELETE FROM Besoin WHERE id_besoin = :id_besoin";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_besoin', (int) $id_besoin, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function update($id_besoin, $id_matiere, $id_ville, $quantite)
    {
        $DBH = \Flight::db();
        $query = "UPDATE Besoin SET id_matiere = :id_matiere, id_ville = :id_ville, quantite = :quantite WHERE id_besoin = :id_besoin";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_besoin', (int) $id_besoin, PDO::PARAM_INT);
        $stmt->bindValue(':id_matiere', (int) $id_matiere, PDO::PARAM_INT);
        $stmt->bindValue(':id_ville', (int) $id_ville, PDO::PARAM_INT);
        $stmt->bindValue(':quantite', $quantite);
        return $stmt->execute();
    }
    public static function getBesoinVIlle($id_ville)
    {
        $DBH = \Flight::db();
        $sql = $DBH->prepare("SELECT * FROM Besoin JOIN Matiere ON Besoin.id_matiere = Matiere.id_matiere WHERE id_ville = ?");
        $sql->bindValue(1, $id_ville, PDO::PARAM_INT);
        $sql->execute();
        $data = [];
        while ($x = $sql->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $x;
        }
        return $data;
    }
}