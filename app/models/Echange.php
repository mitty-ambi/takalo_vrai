<?php

namespace app\models;

use PDO;

class Echange
{
    public $id_echange;
    public $id_user_1;
    public $id_user_2;
    public $date_demande;
    public $date_finalisation;
    public $statut;

    public function __construct($id_echange = null, $id_user_1 = null, $id_user_2 = null, $date_demande = null, $date_finalisation = null, $statut = 'en attente')
    {
        $this->id_echange = $id_echange;
        $this->id_user_1 = $id_user_1;
        $this->id_user_2 = $id_user_2;
        $this->date_demande = $date_demande;
        $this->date_finalisation = $date_finalisation;
        $this->statut = $statut;
    }

    public function create()
    {
        $DBH = \Flight::db();
        $query = "INSERT INTO Echange (id_user_1, id_user_2, date_demande, statut) VALUES (:id_user_1, :id_user_2, NOW(), :statut)";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_user_1', (int) $this->id_user_1, \PDO::PARAM_INT);
        $stmt->bindValue(':id_user_2', (int) $this->id_user_2, \PDO::PARAM_INT);
        $stmt->bindValue(':statut', $this->statut);
        if ($stmt->execute()) {
            return $DBH->lastInsertId();
        } else {
            return false;
        }
    }

    public static function get_by_id($id_echange)
    {
        $DBH = \Flight::db();
        $query = "SELECT * FROM Echange WHERE id_echange = :id_echange";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_echange', $id_echange, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log('[ERROR] get_by_id: ' . $e->getMessage());
            return false;
        }
    }

    public static function get_by_user($id_user)
    {
        $DBH = \Flight::db();
        $query = "SELECT * FROM Echange WHERE id_user_1 = :id_user OR id_user_2 = :id_user ORDER BY date_demande DESC";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log('[ERROR] get_by_user: ' . $e->getMessage());
            return false;
        }
    }

    public static function get_pending_received($id_user)
    {
        $DBH = \Flight::db();
        $query = "SELECT * FROM Echange WHERE id_user_2 = :id_user AND statut = 'en attente' ORDER BY date_demande DESC";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log('[ERROR] get_pending_received: ' . $e->getMessage());
            return false;
        }
    }

    public static function get_pending_sent($id_user)
    {
        $DBH = \Flight::db();
        $query = "SELECT * FROM Echange WHERE id_user_1 = :id_user AND statut = 'en attente' ORDER BY date_demande DESC";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log('[ERROR] get_pending_sent: ' . $e->getMessage());
            return false;
        }
    }

    public function update_status($statut)
    {
        $DBH = \Flight::db();
        $query = "UPDATE Echange SET statut = :statut";
        if ($statut === 'accepte') {
            $query .= ", date_finalisation = NOW()";
        }
        $query .= " WHERE id_echange = :id_echange";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':statut', $statut);
        $stmt->bindValue(':id_echange', (int) $this->id_echange, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete()
    {
        $DBH = \Flight::db();
        $query = "DELETE FROM Echange WHERE id_echange = :id_echange";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_echange', (int) $this->id_echange, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
