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
    public static function StatsRegister($user)
    {
        return $user->StatsRegister();
    }
    public static function userRegistered($date)
    {
        $user = new User(null, null, null, null, null); 
        $stats = UserController::StatsRegister($user);
        foreach ($stats as $s) {
            if ($date === $s['date_creation']) {

            }
        }
    }
}
?>