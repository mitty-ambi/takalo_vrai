<?php
namespace app\controllers;
use app\models\Users;

class UserController
{
    protected Engine $app;
    public function __construct($app)
    {
        $this->app = $app;
    }
    public static function insert_user($user)
    {
        $user->insert_user();
    }
}
?>