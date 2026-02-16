<?php
use app\controllers\BesoinController;
use app\controllers\DonsController;
use app\models\Ville;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/**
 * @var Router $router
 * @var Engine $app
 */

$router->group('', function (Router $router) use ($app) {
    $router->get('/', function () use ($app) {
        $listeVille = Ville::getAll();
        $app->render('index', ['listeVille' => $listeVille]);
    });
    $router->get('/StatsVille', function () use ($app) {
        $id_ville = $_GET['id_ville'];
        $listeBesoin = BesoinController::getBesoinVIlle($id_ville);
        $listeDons = DonsController::getDonsVille($id_ville);
        $app->render('StatsVille', ['listeBesoin' => $listeBesoin, 'listeDons' => $listeDons, 'nomville' => Ville::getNomVIlle($id_ville)]);
    });
    $router->get('/gerer_besoins', function () use ($app) {
        $villes = \app\models\Ville::getAll();
        $matieres = \app\models\Matiere::getAll();
        $app->render('gerer_besoins', [
            'villes' => $villes,
            'matieres' => $matieres
        ]);
    });
}, [SecurityHeadersMiddleware::class]);

