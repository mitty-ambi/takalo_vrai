<?php

namespace app\models;

use PDO;
use app\models\Connection;
class Objet
{
    public $id_objet;
    public $nom_objet;
    public $id_categorie;
    public $id_user;
    public $date_acquisition;
    public $description;
    public $prix_estime;

    public function __construct($id_objet = null, $nom_objet = null, $id_categorie = null, $id_user = null, $date_acquisition = null, $description = null, $prix_estime = null)
    {
        $this->id_objet = $id_objet;
        $this->nom_objet = $nom_objet;
        $this->id_categorie = $id_categorie;
        $this->id_user = $id_user;
        $this->date_acquisition = $date_acquisition;
        $this->description = $description;
        $this->prix_estime = $prix_estime;
    }
    public function set_categorie_base($id_categorie, $id_objet)
    {
        $DBH = \Flight::db();
        $query = "UPDATE Objet SET id_categorie = :id_categorie WHERE id_objet = :id_objet";
        $stmt = $DBH->prepare($query);
        $stmt->bindParam(':id_categorie', $id_categorie);
        $stmt->bindParam(':id_objet', $id_objet);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function set_user_base($id_user, $id_objet)
    {
        $DBH = \Flight::db();
        $query = "UPDATE Objet SET id_user = :id_user WHERE id_objet = :id_objet";
        $stmt = $DBH->prepare($query);
        $stmt->bindParam(':id_user', $id_user);
        $stmt->bindParam(':id_objet', $id_objet);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function insert_base()
    {
        $DBH = \Flight::db();
        $query = "INSERT INTO Objet (nom_objet, id_categorie, id_user, date_acquisition, prix_estime, description) VALUES (:nom_objet, :id_categorie, :id_user, :date_acquisition, :prix_estime, :description)";
        $stmt = $DBH->prepare($query);
        error_log('[DEBUG Objet::insert_base] Query: ' . $query);
        error_log('[DEBUG Objet::insert_base] Values: nom_objet=' . $this->nom_objet . ', id_categorie=' . $this->id_categorie . ', id_user=' . $this->id_user . ', prix_estime=' . $this->prix_estime);
        $stmt->bindValue(':nom_objet', $this->nom_objet);
        $stmt->bindValue(':id_categorie', (int) $this->id_categorie, \PDO::PARAM_INT);
        $stmt->bindValue(':id_user', (int) $this->id_user, \PDO::PARAM_INT);
        $stmt->bindValue(':date_acquisition', $this->date_acquisition);
        $stmt->bindValue(':prix_estime', $this->prix_estime);
        $stmt->bindValue(':description', $this->description);
        try {
            $result = $stmt->execute();
            error_log('[DEBUG Objet::insert_base] Execute result: ' . var_export($result, true));
            if ($result) {
                $last_id = $DBH->lastInsertId();
                error_log('[DEBUG Objet::insert_base] lastInsertId: ' . $last_id);
                return $last_id;
            } else {
                error_log('[DEBUG Objet::insert_base] Execute failed');
                error_log('[DEBUG Objet::insert_base] Error: ' . json_encode($stmt->errorInfo()));
                return false;
            }
        } catch (\Exception $e) {
            error_log('[DEBUG Objet::insert_base] Exception: ' . $e->getMessage());
            return false;
        }
    }
    public static function get_objet_by_id_user($id_user)
    {
        $DBH = \Flight::db();
        $query = "SELECT * FROM Objet WHERE id_user = :id_user";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);
        try {
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log('[DEBUG] Query: ' . $query . ' with id_user: ' . $id_user);
            error_log('[DEBUG] Result count: ' . count($result));
            return $result;
        } catch (\Exception $e) {
            error_log('[ERROR] get_objet_by_id_user: ' . $e->getMessage());
            return false;
        }
    }

    public static function get_by_id($id_objet)
    {
        $DBH = \Flight::db();
        $query = "SELECT * FROM Objet WHERE id_objet = :id_objet";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_objet', $id_objet, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log('[ERROR] get_by_id: ' . $e->getMessage());
            return false;
        }
    }

    public function update()
    {
        $DBH = \Flight::db();
        $query = "UPDATE Objet SET nom_objet = :nom_objet, id_categorie = :id_categorie, description = :description, prix_estime = :prix_estime WHERE id_objet = :id_objet";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':nom_objet', $this->nom_objet);
        $stmt->bindValue(':id_categorie', (int) $this->id_categorie, \PDO::PARAM_INT);
        $stmt->bindValue(':description', $this->description);
        $stmt->bindValue(':prix_estime', $this->prix_estime);
        $stmt->bindValue(':id_objet', (int) $this->id_objet, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete()
    {
        $DBH = \Flight::db();
        $query = "DELETE FROM Objet WHERE id_objet = :id_objet";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_objet', (int) $this->id_objet, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function get_objets_autre_user($id_user)
    {
        $DBH = \Flight::db();
        $query = "SELECT * FROM Objet WHERE id_user != :id_user ORDER BY id_objet DESC";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_user', (int) $id_user, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    public static function get_all()
    {
        $DBH = \Flight::db();
        $query = "SELECT * FROM Objet ORDER BY id_objet DESC";
        $stmt = $DBH->prepare($query);
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log('[ERROR] get_all: ' . $e->getMessage());
            return false;
        }
    }
    public static function switch_proprietaires_objets($id_user_1, $id_user_2, $id_objet_1, $id_objet_2)
    {
        $DBH = \Flight::db();
        $query = "UPDATE Objet set id_user = :id_user where id_objet = :id_objet";
        $query1 = "UPDATE Objet set id_user = :id_user where id_objet = :id_objet";
        $stmt = $DBH->prepare($query);
        $stmt1 = $DBH->prepare($query1);
        $stmt->bindValue(':id_user', (int) $id_user_1, \PDO::PARAM_INT);
        $stmt->bindValue(':id_objet', (int) $id_objet_2, \PDO::PARAM_INT);

        $stmt1->bindValue(':id_user', (int) $id_user_2, \PDO::PARAM_INT);
        $stmt1->bindValue(':id_objet', (int) $id_objet_1, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt1->execute();
        } // <-- ajouter cette accolade pour fermer la méthode précédente
    }
    public static function search($keyword = null, $categorie_id = null)
    {
        $DBH = \Flight::db();

        public static function search($keyword = null, $categorie_id = null)
        {
            $DBH = \Flight::db();

            $conditions = [];
            $params = [];
            $sql = "SELECT o.*, c.nom_categorie
                FROM Objet o
                JOIN Categorie c ON o.id_categorie = c.id_categorie ";

            if (!empty($keyword)) {
                $conditions[] = "o.nom_objet LIKE ?";
                $params[] = "%$keyword%";
            }

            if (!empty($categorie_id)) {
                $conditions[] = "o.id_categorie = ?";
                $params[] = $categorie_id;
            }

            $where = "";
            if (!empty($conditions)) {
                $where = "WHERE " . implode(" AND ", $conditions);
            }
            $sql += $where;
        $where = "";
        if (!empty($conditions)) {
            $where = "WHERE " . implode(" AND ", $conditions);
        }
        $sql .= $where;

            $stmt = $DBH->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        public static function history(int $id_objet)
        {
            $allo = 0;
            $DBH = \Flight::db();
            $sql = "     
                SELECT
                    ef.id_echange_fille,
                    ef.id_echange_mere,
                    COALESCE(e.date_finalisation, e.date_demande) AS date_echange,
                    ef.id_proprietaire,
                    u.nom,
                    u.prenom
                FROM Echange_fille ef
                JOIN Echange e ON ef.id_echange_mere = e.id_echange
                JOIN Utilisateur u ON ef.id_proprietaire = u.id_user
                WHERE ef.id_objet = :id_objet
                ORDER BY date_echange ASC, ef.id_echange_fille ASC
            ";
            $stmt = $DBH->prepare($sql);
            $stmt->bindValue(':id_objet', $id_objet, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
    }
?>