<?php
namespace app\models;

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

    public function insert_user()
    {
        try {
            $DBH = \Flight::db();
            $sql = $DBH->prepare("INSERT INTO Utilisateur (nom,prenom,email,mdp_hash,type_user) VALUES (?,?,?,?,?)");

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
    public function login()
    {
        $DBH = \Flight::db();
        $sql = $DBH->prepare("SELECT id_user, mdp_hash FROM Utilisateur WHERE email = ?");
        $sql->execute([$this->email]);
        $user = $sql->fetch(\PDO::FETCH_ASSOC);

        if ($user && password_verify($this->mdp_hash, $user['mdp_hash'])) {
            return $user['id_user'];
        }

        return false;
    }
}
?>