<?php

namespace app\models;

use PDO;
namespace app\models;
use Flight;

class EchangeFille
{
    public $id_echange_fille;
    public $id_echange_mere;
    public $id_objet;
    public $quantite;
    public $id_proprietaire;

    public function __construct($id_echange_fille = null, $id_echange_mere = null, $id_objet = null, $quantite = 1, $id_proprietaire = null)
    {
        $this->id_echange_fille = $id_echange_fille;
        $this->id_echange_mere = $id_echange_mere;
        $this->id_objet = $id_objet;
        $this->quantite = $quantite;
        $this->id_proprietaire = $id_proprietaire;
    }

    public function create()
    {
        $DBH = \Flight::db();
        $a = 1;
        $query = "INSERT INTO Echange_fille (id_echange_mere, id_objet, quantite, id_proprietaire) VALUES (:id_echange_mere, :id_objet, :quantite, :id_proprietaire)";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_echange_mere', (int) $this->id_echange_mere, PDO::PARAM_INT);
        $stmt->bindValue(':id_objet', (int) $this->id_objet, PDO::PARAM_INT);
        $stmt->bindValue(':quantite', (int) $this->quantite, PDO::PARAM_INT);
        $stmt->bindValue(':id_proprietaire', (int) $this->id_proprietaire, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $DBH->lastInsertId();
        } else {
            return false;
        }
    }

    public static function get_by_echange($id_echange)
    {
        $DBH = \Flight::db();
        $query = "SELECT ef.*, o.nom_objet, o.prix_estime, u.nom, u.prenom 
                  FROM Echange_fille ef
                  JOIN Objet o ON ef.id_objet = o.id_objet
                  JOIN Utilisateur u ON ef.id_proprietaire = u.id_user
                  WHERE ef.id_echange_mere = :id_echange";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_echange', $id_echange, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log('[ERROR] get_by_echange: ' . $e->getMessage());
            return false;
        }
    }

    public function delete()
    {
        $DBH = \Flight::db();
        $query = "DELETE FROM Echange_fille WHERE id_echange_fille = :id_echange_fille";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_echange_fille', (int) $this->id_echange_fille, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public static function delete_by_echange($id_echange)
    {
        $DBH = \Flight::db();
        $query = "DELETE FROM Echange_fille WHERE id_echange_mere = :id_echange";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_echange', $id_echange, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
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