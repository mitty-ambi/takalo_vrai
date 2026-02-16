<?php

namespace app\models;

use PDO;

class Achat
{
    public $id_achat;
    public $id_besoin;
    public $id_ville;
    public $id_matiere;
    public $quantite;
    public $prix_unitaire;
    public $frais_pourcentage;
    public $prix_total_achat;
    public $date_achat;
    public $statut;

    public function __construct($id_achat = null, $id_besoin = null, $id_ville = null, $id_matiere = null, $quantite = null, $prix_unitaire = null, $frais_pourcentage = null, $prix_total_achat = null, $date_achat = null, $statut = 'en_attente')
    {
        $this->id_achat = $id_achat;
        $this->id_besoin = $id_besoin;
        $this->id_ville = $id_ville;
        $this->id_matiere = $id_matiere;
        $this->quantite = $quantite;
        $this->prix_unitaire = $prix_unitaire;
        $this->frais_pourcentage = $frais_pourcentage;
        $this->prix_total_achat = $prix_total_achat;
        $this->date_achat = $date_achat ?? date('Y-m-d H:i:s');
        $this->statut = $statut;
    }

    public function insert_base()
    {
        $DBH = \Flight::db();
        $query = "INSERT INTO Achats (id_besoin, id_ville, id_matiere, quantite, prix_unitaire, frais_pourcentage, prix_total_achat, date_achat, statut) 
                  VALUES (:id_besoin, :id_ville, :id_matiere, :quantite, :prix_unitaire, :frais_pourcentage, :prix_total_achat, :date_achat, :statut)";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_besoin', (int) $this->id_besoin, PDO::PARAM_INT);
        $stmt->bindValue(':id_ville', (int) $this->id_ville, PDO::PARAM_INT);
        $stmt->bindValue(':id_matiere', (int) $this->id_matiere, PDO::PARAM_INT);
        $stmt->bindValue(':quantite', $this->quantite);
        $stmt->bindValue(':prix_unitaire', (float) $this->prix_unitaire);
        $stmt->bindValue(':frais_pourcentage', (float) $this->frais_pourcentage);
        $stmt->bindValue(':prix_total_achat', (float) $this->prix_total_achat);
        $stmt->bindValue(':date_achat', $this->date_achat);
        $stmt->bindValue(':statut', $this->statut);

        return $stmt->execute();
    }

    /**
     * Récupérer tous les achats
     */
    public static function getAll()
    {
        $DBH = \Flight::db();
        $query = "SELECT a.*, m.nom_matiere, v.nom_ville 
                  FROM Achats a 
                  JOIN Matiere m ON a.id_matiere = m.id_matiere 
                  JOIN Ville v ON a.id_ville = v.id_ville 
                  ORDER BY a.date_achat DESC";
        $stmt = $DBH->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer les achats par ville
     */
    public static function getByVille($id_ville)
    {
        $DBH = \Flight::db();
        $query = "SELECT a.*, m.nom_matiere, v.nom_ville 
                  FROM Achats a 
                  JOIN Matiere m ON a.id_matiere = m.id_matiere 
                  JOIN Ville v ON a.id_ville = v.id_ville 
                  WHERE a.id_ville = :id_ville 
                  ORDER BY a.date_achat DESC";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_ville', (int) $id_ville, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer un achat par ID
     */
    public static function getById($id_achat)
    {
        $DBH = \Flight::db();
        $query = "SELECT a.*, m.nom_matiere, v.nom_ville 
                  FROM Achats a 
                  JOIN Matiere m ON a.id_matiere = m.id_matiere 
                  JOIN Ville v ON a.id_ville = v.id_ville 
                  WHERE a.id_achat = :id_achat";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_achat', (int) $id_achat, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Mettre à jour un achat
     */
    public static function update($id_achat, $quantite, $statut = null)
    {
        $DBH = \Flight::db();
        $fields = ['quantite = :quantite'];
        $bindings = [':quantite' => $quantite];

        if ($statut !== null) {
            $fields[] = 'statut = :statut';
            $bindings[':statut'] = $statut;
        }

        $query = "UPDATE Achats SET " . implode(', ', $fields) . " WHERE id_achat = :id_achat";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_achat', (int) $id_achat, PDO::PARAM_INT);
        foreach ($bindings as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        return $stmt->execute();
    }

    /**
     * Supprimer un achat
     */
    public static function delete($id_achat)
    {
        $DBH = \Flight::db();
        $query = "DELETE FROM Achats WHERE id_achat = :id_achat";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_achat', (int) $id_achat, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Vérifier si un achat existe déjà pour ce besoin
     */
    public static function existeForBesoin($id_besoin)
    {
        $DBH = \Flight::db();
        $query = "SELECT COUNT(*) as count FROM Achats WHERE id_besoin = :id_besoin";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_besoin', (int) $id_besoin, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Vérifier s'il y a des dons suffisants pour le besoin
     */
    public static function hasSufficientDons($id_besoin)
    {
        $DBH = \Flight::db();
        $query = "SELECT b.id_matiere, b.quantite 
                  FROM Besoin b 
                  WHERE b.id_besoin = :id_besoin";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_besoin', (int) $id_besoin, PDO::PARAM_INT);
        $stmt->execute();
        $besoin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$besoin) {
            return false;
        }

        // Vérifier la quantité de dons disponibles
        $query2 = "SELECT SUM(quantite) as total_dons FROM Dons WHERE id_matiere = :id_matiere";
        $stmt2 = $DBH->prepare($query2);
        $stmt2->bindValue(':id_matiere', (int) $besoin['id_matiere'], PDO::PARAM_INT);
        $stmt2->execute();
        $dons = $stmt2->fetch(PDO::FETCH_ASSOC);

        return ($dons['total_dons'] ?? 0) >= $besoin['quantite'];
    }

    /**
     * Calculer le prix total avec frais
     */
    public static function calculateTotal($prix_base, $frais_pourcentage)
    {
        return $prix_base * (1 + ($frais_pourcentage / 100));
    }
}
