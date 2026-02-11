<?php

namespace app\models;

use PDO;

class Image_objet
{
    public $id_image;
    public $id_objet;
    public $url_image;

    public function __construct($id_image = null, $id_objet = null, $url_image = null)
    {
        $this->id_image = $id_image;
        $this->id_objet = $id_objet;
        $this->url_image = $url_image;
    }

    public function insert_base()
    {
        $DBH = \Flight::db();
        $query = "INSERT INTO Image_objet (id_objet, url_image) VALUES (:id_objet, :url_image)";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_objet', (int) $this->id_objet, \PDO::PARAM_INT);
        $stmt->bindValue(':url_image', $this->url_image);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function get_image_by_objet($id_objet)
    {
        $DBH = \Flight::db();
        $query = "SELECT * FROM Image_objet WHERE id_objet = :id_objet";
        $stmt = $DBH->prepare($query);
        $stmt->bindValue(':id_objet', (int) $id_objet, \PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }
}
?>