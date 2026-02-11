<?php
namespace app\models;

use Flight;

class User
{
    public $id_user;
    public $nom;
    public $prenom;
    public $email;
    public $mdp_hash;
    public $type_user;

    public function __construct($nom = '', $prenom = '', $email = '', $mdp_hash = '', $type_user = 'normal', $id_user = null)
    {
        $this->id_user = $id_user;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->mdp_hash = $mdp_hash;
        $this->type_user = $type_user;
    }
    public function StatsRegister()
    {
        $DBH = \Flight::db();
        $sql = $DBH->prepare('SELECT * FROM Utilisateur GROUP BY date_creation,id_user');
        $sql->execute();
        $data = [];
        while ($x = $sql->fetch(\PDO::FETCH_ASSOC)) {
            $data[] = $x;
        }
        return $data;
    }
    public function getRegistrationsPerDay()
    {
        $DBH = \Flight::db();
        $sql = $DBH->prepare('SELECT DATE(date_creation) AS date, COUNT(*) AS count FROM Utilisateur GROUP BY DATE(date_creation) ORDER BY DATE(date_creation) ASC');
        $sql->execute();
        $data = [];
        while ($x = $sql->fetch(\PDO::FETCH_ASSOC)) {
            $data[] = $x;
        }
        return $data;
    }
    public function insert_user()
    {
        try {
            $DBH = \Flight::db();
            $sql = $DBH->prepare("INSERT INTO Utilisateur (nom,prenom,email,mdp_hash,type_user,date_creation) VALUES (?,?,?,?,?,NOW())");

            $sql->bindValue(1, $this->nom, \PDO::PARAM_STR);
            $sql->bindValue(2, $this->prenom, \PDO::PARAM_STR);
            $sql->bindValue(3, $this->email, \PDO::PARAM_STR);
            $sql->bindValue(4, $this->mdp_hash, \PDO::PARAM_STR);
            $sql->bindValue(5, $this->type_user, \PDO::PARAM_STR);

            $result = $sql->execute();
            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public static function emailExists($email)
    {
        $DBH = \Flight::db();
        $sql = $DBH->prepare("SELECT * FROM Utilisateur WHERE email = ?");
        $sql->bindValue(1, $email, \PDO::PARAM_STR);
        $sql->execute();
        $type = $sql->fetch(\PDO::FETCH_ASSOC);
        if ($type) {
            return true;
        }
        return null;
    }
    public function login()
    {
        $DBH = \Flight::db();
        $sql = $DBH->prepare("SELECT * FROM Utilisateur WHERE email = ?");
        $sql->execute([$this->email]);
        $user = $sql->fetch(\PDO::FETCH_ASSOC);

        if ($user && password_verify($this->mdp_hash, $user['mdp_hash'])) {
            return $user['id_user'];
        }
        return false;
    }
    public function get_user_by_id($id_user)
    {
        $DBH = \Flight::db();
        $sql = $DBH->prepare("SELECT * FROM Utilisateur WHERE id_user = ?");
        $sql->execute([$id_user]);
        return $sql->fetch(\PDO::FETCH_ASSOC);
    }
    public static function type($id_user)
    {
        $DBH = \Flight::db();
        $sql = $DBH->prepare("SELECT type_user FROM Utilisateur WHERE id_user= ?");
        $sql->bindValue(1, $id_user, \PDO::PARAM_INT);
        $sql->execute();
        $type = $sql->fetch(\PDO::FETCH_ASSOC);
        if ($type) {
            return $type['type_user'];
        }
        return null;
    }
    public static function adminOrNot($id_user)
    {
        if (User::type($id_user) === "admin") {
            return true;
        }
        return false;
    }
    public function getAllUser()
    {
        $DBH = \Flight::db();
        $sql = $DBH->prepare('SELECT * FROM Utilisateur');
        $sql->execute();
        $data = [];
        while ($x = $sql->fetch(\PDO::FETCH_ASSOC)) {
            $data[] = $x;
        }
        return $data;
    }
    public static function getUserbyID($id)
    {
        $user = new User(null, null, null, null, null);
        $listeUser = $user->getAllUser();
        foreach ($listeUser as $u) {
            if ($u['id_user'] === $id) {
                return $u;
            }
        }
        return null;
    }
}
?>