<?php

namespace app\models;

use PDO;

class Besoin
{
    public $id_besoin;
    public $id_matiere;
    public $id_ville;
    public $quantite;
    public $date_du_demande;

    public function __construct($id_besoin, $id_matiere, $id_ville, $quantite, $date_du_demande = null)
    {
        $this->id_besoin = $id_besoin;
        $this->id_matiere = $id_matiere;
        $this->id_ville = $id_ville;
        $this->quantite = $quantite;
        $this->date_du_demande = $date_du_demande;
    }
    public function insert_base()
    {
        $DBH = \Flight::db();
        $query = "INSERT INTO Besoin (id_matiere, id_ville, quantite, date_du_demande) VALUES (:id_matiere, :id_ville, :quantite, :date_du_demande)";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_matiere', (int) $this->id_matiere, PDO::PARAM_INT);
        $stmt->bindValue(':id_ville', (int) $this->id_ville, PDO::PARAM_INT);
        $stmt->bindValue(':quantite', $this->quantite);
        $stmt->bindValue(':date_du_demande', $this->date_du_demande);

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

    /**
     * Récupérer les besoins groupés par matière (pour dispatch par date)
     * Ordonnés par date de demande ASC
     */
    public static function getBesoinsGroupedByMatiereByDate()
    {
        $DBH = \Flight::db();
        $query = "SELECT b.id_matiere, m.nom_matiere, m.id_categorie, c.nom as nom_categorie,
                         b.id_ville, v.nom_ville, b.quantite, b.date_du_demande, b.id_besoin
                  FROM Besoin b
                  JOIN Matiere m ON b.id_matiere = m.id_matiere
                  LEFT JOIN Categorie c ON m.id_categorie = c.id_categorie
                  JOIN Ville v ON b.id_ville = v.id_ville
                  ORDER BY b.id_matiere, b.date_du_demande ASC";
        
        $besoins_par_matiere = [];
        $stmt = $DBH->query($query);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!isset($besoins_par_matiere[$row['id_matiere']])) {
                $besoins_par_matiere[$row['id_matiere']] = [
                    'nom_matiere' => $row['nom_matiere'],
                    'nom_categorie' => $row['nom_categorie'],
                    'id_categorie' => $row['id_categorie'],
                    'besoins' => []
                ];
            }
            $besoins_par_matiere[$row['id_matiere']]['besoins'][] = $row;
        }
        
        return $besoins_par_matiere;
    }

    /**
     * Récupérer les besoins groupés par matière (pour dispatch par minimum)
     * Ordonnés par quantité ASC
     */
    public static function getBesoinsGroupedByMatiereByQuantite()
    {
        $DBH = \Flight::db();
        $query = "SELECT b.id_matiere, m.nom_matiere, m.id_categorie, c.nom as nom_categorie,
                         b.id_ville, v.nom_ville, b.quantite, b.date_du_demande, b.id_besoin
                  FROM Besoin b
                  JOIN Matiere m ON b.id_matiere = m.id_matiere
                  LEFT JOIN Categorie c ON m.id_categorie = c.id_categorie
                  JOIN Ville v ON b.id_ville = v.id_ville
                  ORDER BY b.id_matiere, b.quantite ASC";
        
        $besoins_par_matiere = [];
        $stmt = $DBH->query($query);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!isset($besoins_par_matiere[$row['id_matiere']])) {
                $besoins_par_matiere[$row['id_matiere']] = [
                    'nom_matiere' => $row['nom_matiere'],
                    'nom_categorie' => $row['nom_categorie'],
                    'id_categorie' => $row['id_categorie'],
                    'besoins' => []
                ];
            }
            $besoins_par_matiere[$row['id_matiere']]['besoins'][] = $row;
        }
        
        return $besoins_par_matiere;
    }

    /**
     * Récupérer les besoins groupés par matière pour dispatch proportionnel
     * Filtre les besoins avec quantité > 0
     */
    public static function getBesoinsGroupedByMatiereProportionnel()
    {
        $DBH = \Flight::db();
        $query = "SELECT b.id_matiere, m.nom_matiere, m.id_categorie, c.nom as nom_categorie,
                         b.id_ville, v.nom_ville, b.quantite, b.date_du_demande, b.id_besoin
                  FROM Besoin b
                  JOIN Matiere m ON b.id_matiere = m.id_matiere
                  JOIN Categorie c ON m.id_categorie = c.id_categorie
                  JOIN Ville v ON b.id_ville = v.id_ville
                  WHERE b.quantite > 0
                  ORDER BY b.id_matiere, b.id_besoin ASC";
        
        $besoins_par_matiere = [];
        $stmt = $DBH->query($query);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!isset($besoins_par_matiere[$row['id_matiere']])) {
                $besoins_par_matiere[$row['id_matiere']] = [
                    'nom_matiere' => $row['nom_matiere'],
                    'nom_categorie' => $row['nom_categorie'],
                    'id_categorie' => $row['id_categorie'],
                    'besoins' => []
                ];
            }
            $besoins_par_matiere[$row['id_matiere']]['besoins'][] = $row;
        }
        
        return $besoins_par_matiere;
    }

    /**
     * Récupérer les besoins restants (non achetés)
     */
    public static function getBesoinsRestants()
    {
        $DBH = \Flight::db();
        $query = "SELECT b.*, m.nom_matiere, m.prix_unitaire, v.nom_ville
                  FROM Besoin b
                  JOIN Matiere m ON b.id_matiere = m.id_matiere
                  JOIN Ville v ON b.id_ville = v.id_ville
                  WHERE b.id_besoin NOT IN (SELECT id_besoin FROM Achats WHERE id_besoin IS NOT NULL)
                  ORDER BY v.nom_ville, m.nom_matiere";
        $stmt = $DBH->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupérer le besoin par id_besoin (id_matiere)
     */
    public static function getMatiereByBesoin($id_besoin)
    {
        $DBH = \Flight::db();
        $query = "SELECT id_matiere FROM Besoin WHERE id_besoin = :id_besoin";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_besoin', $id_besoin, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}