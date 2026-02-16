<?php

namespace app\controllers;

use app\models\Categorie;
use PDO;

class CategorieController
{
    public static function getAll()
    {
        return Categorie::getAll();
    }
}