<?php
namespace app\models;
use Flight;

class EchangeMere
{
    public $id_echange;
    public $id_user1;
    public $id_user2;
    public $date_demande;
    public $date_finalisation;
    public $statut;

    public function __construct($id_echange, $id_user1, $id_user2, $date_demande, $date_finalisation, $statut)
    {
        $this->id_echange = $id_echange;
        $this->id_user1 = $id_user1;
        $this->id_user2 = $id_user2;
        $this->date_demande = $date_demande;
        $this->date_finalisation = $date_finalisation;
        $this->statut = $statut;
    }
    public function getAllEchange()
    {
        $DBH = \Flight::db();
        $sql = $DBH->prepare('SELECT * FROM Echange ORDER BY date_demande');
        $sql->execute();
        $data = [];
        while ($x = $sql->fetch(\PDO::FETCH_ASSOC)) {
            $data[] = $x;
        }
        return $data;
    }

    /**
     * Retourne le nombre d'échanges effectués (statut 'accepte') par date_finalisation.
     * Format: [ ['date' => '2026-02-01', 'count' => 2], ... ]
     */
    public function getExchangesPerDay()
    {
        $DBH = \Flight::db();
        $sql = $DBH->prepare("SELECT DATE(date_finalisation) AS date, COUNT(*) AS count FROM Echange WHERE statut = 'accepte' AND date_finalisation IS NOT NULL GROUP BY DATE(date_finalisation) ORDER BY DATE(date_finalisation) ASC");
        $sql->execute();
        $data = [];
        while ($x = $sql->fetch(\PDO::FETCH_ASSOC)) {
            $data[] = $x;
        }
        return $data;
    }
    public static function getCountExchange()
    {
        $DBH = \Flight::db();
        $sql = $DBH->prepare("SELECT count(*) as totalEchange FROM Echange WHERE statut = 'accepte'");
        $sql->execute();
        $count = $sql->fetch(\PDO::FETCH_ASSOC);
        if ($count) {
            return $count['totalEchange'];
        }
        return 0;
    }
}