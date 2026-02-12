<?php
namespace app\controllers;
use app\models\Categorie;
use app\models\User;
use app\models\Users;
use app\models\Objet;

class ObjetController
{
    protected Engine $app;
    public function __construct($app)
    {
        $this->app = $app;
    }
    public static function search($keyword = null, $categorie_id = null)
    {
        return Objet::search($keyword, $categorie_id);
    }
}
?>