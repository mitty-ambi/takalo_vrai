<?php
namespace app\controllers;
use app\models\Objet;
use app\models\Users;

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