<?php
use app\controllers\ObjetController;
use app\controllers\CategorieController;
use app\controllers\UserController;
use app\controllers\ObjectController;
use app\controllers\ExchangeController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;
use app\models\User;
use app\models\Image_objet;
use app\models\Categorie;
use app\models\EchangeFille;
use app\models\EchangeMere;
use app\models\Objet;
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/**
 * @var Router $router
 * @var Engine $app
 */

$router->group('', function (Router $router) use ($app) {
    $router->get('/', function () use ($app) {
        $app->render('index', ['ls_donnees_prod' => 'a']);
    });
    $router->get('/gerer_besoins', function () use ($app) {
        
        $app->render('gerer_besoins');
    });
}, [SecurityHeadersMiddleware::class]);

