<?php

namespace app\controllers;

use app\models\Dons;
use PDO;
class DonsController
{
    public static function getDonsVille($id_ville)
    {
        return Dons::getDonsVille($id_ville);
    }
}
