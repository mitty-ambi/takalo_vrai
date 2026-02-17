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

    /**
     * Récupérer le montant total en argent disponible pour une ville
     * (dons en argent non distribués OU distribués à cette ville)
     */
    public static function getMontantArgentDisponible($id_ville)
    {
        $DBH = \Flight::db();
        // Récupérer l'ID de la catégorie "Argent"
        $query_categorie = "SELECT id_categorie FROM Categorie WHERE nom = 'Argent'";
        $stmt_cat = $DBH->query($query_categorie);
        $categorie = $stmt_cat->fetch(PDO::FETCH_ASSOC);
        
        if (!$categorie) {
            return 0; // Catégorie "Argent" non trouvée
        }
        
        $id_categorie_argent = $categorie['id_categorie'];
        
        // Récupérer le montant total des dons en argent (non distribués OU distribués à cette ville)
        $query = "SELECT COALESCE(SUM(m.prix_unitaire * d.quantite), 0) as montant_total
                  FROM Dons d
                  JOIN Matiere m ON d.id_matiere = m.id_matiere
                  WHERE m.id_categorie = :id_categorie_argent
                  AND (d.id_ville = 0 OR d.id_ville = :id_ville)";
        
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_categorie_argent', (int) $id_categorie_argent, PDO::PARAM_INT);
        $stmt->bindValue(':id_ville', (int) $id_ville, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float) ($result['montant_total'] ?? 0);
    }

    /**
     * Récupérer les dons non distribués d'une matière spécifique
     * (id_ville = 0)
     */
    public static function getDonsNonDistribuesByMatiere($id_matiere)
    {
        $DBH = \Flight::db();
        $query = "SELECT d.*, m.nom_matiere, m.id_categorie, c.nom
                  FROM Dons d
                  JOIN Matiere m ON d.id_matiere = m.id_matiere
                  LEFT JOIN Categorie c ON m.id_categorie = c.id_categorie
                  WHERE d.id_matiere = :id_matiere
                  AND d.id_ville = 0
                  ORDER BY d.date_don ASC";
        
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_matiere', (int) $id_matiere, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer la quantité totale disponible (non distribuée) d'une matière
     */
    public static function getQuantiteTotalNonDistribuee($id_matiere)
    {
        $DBH = \Flight::db();
        $query = "SELECT COALESCE(SUM(quantite), 0) as quantite_total
                  FROM Dons
                  WHERE id_matiere = :id_matiere
                  AND id_ville = 0";
        
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_matiere', (int) $id_matiere, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($result['quantite_total'] ?? 0);
    }

    /**
     * Récupérer les dons non distribués groupés par matière
     */
    public static function getDonsNonDistribuesGroupedByMatiere()
    {
        $DBH = \Flight::db();
        $query = "SELECT d.*, m.nom_matiere, m.id_matiere
                       FROM Dons d
                       JOIN Matiere m ON d.id_matiere = m.id_matiere
                       WHERE d.id_ville = 0
                       ORDER BY d.id_matiere, d.date_don ASC";
        
        $dons_non_distribues = [];
        $stmt = $DBH->query($query);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!isset($dons_non_distribues[$row['id_matiere']])) {
                $dons_non_distribues[$row['id_matiere']] = [];
            }
            $dons_non_distribues[$row['id_matiere']][] = $row;
        }
        
        return $dons_non_distribues;
    }

    /**
     * Récupérer les dons non distribués pour une matière (pour dispatch)
     */
    public static function getDonsNonDistribuesPourDispatch($id_matiere)
    {
        $DBH = \Flight::db();
        $query = "SELECT id_don, quantite FROM Dons 
                  WHERE id_matiere = :id_matiere AND id_ville = 0
                  ORDER BY date_don ASC";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_matiere', $id_matiere, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Dispatcher un don vers une ville (mise à jour partielle)
     */
    public static function dispatcherDon($id_don, $id_ville, $quantite_a_prendre)
    {
        $DBH = \Flight::db();
        $query = "UPDATE Dons SET id_ville = :id_ville, quantite = quantite - :quantite_a_prendre 
                  WHERE id_don = :id_don";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_ville', $id_ville, PDO::PARAM_INT);
        $stmt->bindValue(':quantite_a_prendre', $quantite_a_prendre, PDO::PARAM_INT);
        $stmt->bindValue(':id_don', $id_don, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Réinitialiser tous les dons (id_ville = 0)
     */
    public static function reinitialiserTous()
    {
        $DBH = \Flight::db();
        $query = "UPDATE Dons SET id_ville = 0";
        return $DBH->exec($query);
    }
}

