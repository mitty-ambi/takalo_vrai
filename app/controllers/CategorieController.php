<?php
namespace app\controllers;
use app\models\Categorie;
use app\models\User;
use app\models\Users;

class CategorieController
{
    protected Engine $app;
    public function __construct($app)
    {
        $this->app = $app;
    }
    public static function getAll($nom = null)
    {
        return Categorie::get_all_categories();
    }
}
?>