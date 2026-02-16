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

    public function insert_base()
    {
        $DBH = \Flight::db();
        $query = "INSERT INTO Dons (id_matiere, quantite, date_don, id_ville) VALUES (:id_matiere, :quantite, :date_don, :id_ville)";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_matiere', (int) $this->id_matiere, PDO::PARAM_INT);
        $stmt->bindValue(':quantite', $this->quantite);
        $stmt->bindValue(':date_don', $this->date_don);
        $stmt->bindValue(':id_ville', (int) ($this->id_ville ?? 0), PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Récupérer tous les dons
     */
    public static function getAll()
    {
        $DBH = \Flight::db();
        $query = "SELECT * FROM Dons ORDER BY date_don DESC";
        $stmt = $DBH->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer un don par ID
     */
    public static function getById($id_don)
    {
        $DBH = \Flight::db();
        $query = "SELECT * FROM Dons WHERE id_don = :id_don";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_don', (int) $id_don, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer les dons par ville
     */
    public static function getByVille($id_ville)
    {
        $DBH = \Flight::db();
        $query = "SELECT d.*, m.nom_matiere, m.prix_unitaire 
                  FROM Dons d 
                  JOIN Matiere m ON d.id_matiere = m.id_matiere 
                  WHERE d.id_ville = :id_ville 
                  ORDER BY d.date_don DESC";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_ville', (int) $id_ville, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mettre à jour un don
     */
    public static function update($id_don, $id_matiere, $quantite, $date_don, $id_ville)
    {
        $DBH = \Flight::db();
        $query = "UPDATE Dons SET id_matiere = :id_matiere, quantite = :quantite, date_don = :date_don, id_ville = :id_ville WHERE id_don = :id_don";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_don', (int) $id_don, PDO::PARAM_INT);
        $stmt->bindValue(':id_matiere', (int) $id_matiere, PDO::PARAM_INT);
        $stmt->bindValue(':quantite', $quantite);
        $stmt->bindValue(':date_don', $date_don);
        $stmt->bindValue(':id_ville', (int) $id_ville, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Supprimer un don
     */
    public static function delete($id_don)
    {
        $DBH = \Flight::db();
        $query = "DELETE FROM Dons WHERE id_don = :id_don";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_don', (int) $id_don, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Récupérer les dons non distribués (id_ville = 0) ordonnés par date la plus récente
     */
    public static function getDonsNonDistribuee()
    {
        $DBH = \Flight::db();
        $query = "SELECT d.*, m.nom_matiere, m.prix_unitaire 
                  FROM Dons d 
                  JOIN Matiere m ON d.id_matiere = m.id_matiere 
                  WHERE d.id_ville = 0 
                  ORDER BY d.date_don DESC";
        $stmt = $DBH->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer tous les dons avec le nom de la matière
     */
    public static function getAllWithMatiere()
    {
        $DBH = \Flight::db();
        $query = "SELECT d.*, m.nom_matiere, m.prix_unitaire, v.nom_ville
                  FROM Dons d 
                  JOIN Matiere m ON d.id_matiere = m.id_matiere 
                  LEFT JOIN Ville v ON d.id_ville = v.id_ville
                  ORDER BY d.date_don DESC";
        $stmt = $DBH->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer un don par ID avec le nom de la matière
     */
    public static function getByIdWithMatiere($id_don)
    {
        $DBH = \Flight::db();
        $query = "SELECT d.*, m.nom_matiere, m.prix_unitaire, v.nom_ville
                  FROM Dons d 
                  JOIN Matiere m ON d.id_matiere = m.id_matiere 
                  LEFT JOIN Ville v ON d.id_ville = v.id_ville
                  WHERE d.id_don = :id_don";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_don', (int) $id_don, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
