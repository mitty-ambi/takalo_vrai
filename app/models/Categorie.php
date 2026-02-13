<?php
namespace app\models;

use Flight;

class Categorie
{
    public $id_categorie;
    public $nom_categorie;
    public $date_creation;

    public function __construct($id_categorie = null, $nom_categorie = null)
    {
        $this->id_categorie = $id_categorie;
        $this->nom_categorie = $nom_categorie;
    }
    public function insert()
    {
        $DBH = \Flight::db();
        $sql = $DBH->prepare("INSERT INTO Categorie (nom_categorie,date_creation) VALUES (?,NOW())");
        $sql->bindValue(1, $this->nom_categorie, \PDO::PARAM_STR);
        $sql->execute();
    }
    public function delete_cat()
    {
        $DBH = \Flight::db();
        $sql = $DBH->prepare("DELETE FROM Categorie WHERE id_categorie = ?");
        $sql->bindValue(1, $this->id_categorie, \PDO::PARAM_INT);
        $sql->execute();
    }
    public function getAll($nom = null)
    {
        $DBH = \Flight::db();

        $sql = "SELECT * FROM Categorie";
        $params = [];

        if ($nom !== null) {
            $sql .= " WHERE nom_categorie LIKE ?";
            $params[] = '%' . $nom . '%';
        }
        $sql .= " ORDER BY nom_categorie ASC";

        $stmt = $DBH->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Compatibility wrapper used by routes: get_all_categories()
     */
    public static function get_all_categories()
    {
        $c = new self(0, null);
        return $c->getAll();
    }

    /**
     * Update category name
     */
    public function update()
    {
        if (empty($this->id_categorie)) {
            return false;
        }
        $DBH = \Flight::db();
        $sql = $DBH->prepare("UPDATE Categorie SET nom_categorie = ? WHERE id_categorie = ?");
        $sql->bindValue(1, $this->nom_categorie, \PDO::PARAM_STR);
        $sql->bindValue(2, $this->id_categorie, \PDO::PARAM_INT);
        return $sql->execute();
    }
    public static function getObjetAssocies($id_categorie)
    {
        $DBH = \Flight::db();
        $sql = $DBH->prepare("SELECT COUNT(id_categorie) as objet_associes FROM Objet WHERE id_categorie = ?");
        $sql->bindValue(1, $id_categorie, \PDO::PARAM_INT);
        $sql->execute();
        $count = $sql->fetch(\PDO::FETCH_ASSOC);
        if ($count) {
            return $count['objet_associes'];
        }
        return null;
    }
}
?>