<?php
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;
use app\models\Matiere;
use app\models\Dons;
use app\models\Ville;
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
        $villes = \app\models\Ville::getAll();
        $matieres = \app\models\Matiere::getAll();
        $app->render('gerer_besoins', [
            'villes' => $villes,
            'matieres' => $matieres
        ]);
    });
    $router->get('/gerer_dons', function () use ($app) {
        $matieres = \app\models\Matiere::getAll();
        $app->render('gerer_dons', [
            'matieres' => $matieres
        ]);
    });
    $router->get('/valider_dons', function () use ($app) {
        $matieres = \app\models\Matiere::getAll();
        $app->render('gerer_dons', [
            'matieres' => $matieres
        ]);
    });
    $router->get('/dispatch', function () use ($app) {
        $dons_non_distribuees = \app\models\Dons::getDonsNonDistribuee();
        $villes = \app\models\Ville::getAll();
        $app->render('dispatch', [
            'dons_non_distribuees' => $dons_non_distribuees,
            'villes' => $villes
        ]);
    });
    $router->post('/valider_dons', function () use ($app) {
        $id_matiere = $_POST['matiere'] ?? null;
        $quantite = $_POST['quantite'] ?? null;
        $date_don = $_POST['date_don'] ?? null;

        if (!$id_matiere || !$quantite || !$date_don) {
            error_log('[ERROR] Champs manquants pour créer un don');
            $app->redirect('/gerer_dons');
            return;
        }

        $don = new \app\models\Dons(null, $id_matiere, $quantite, $date_don, 0);
        
        if ($don->insert_base()) {
            error_log('[SUCCESS] Don créé avec succès');
            $app->redirect('/gerer_dons');
        } else {
            error_log('[ERROR] Erreur lors de la création du don');
            $app->redirect('/gerer_dons');
        }
    });
    $router->post('/update-dons', function () use ($app) {
        $id_don = $_POST['id_don'] ?? null;
        $id_ville = $_POST['id_ville'] ?? null;

        if (!$id_don || !$id_ville) {
            error_log('[ERROR] Données manquantes pour update don');
            $app->redirect('/dispatch');
            return;
        }

        if (\app\models\Dons::update($id_don, null, null, null, $id_ville)) {
            error_log('[SUCCESS] Don #' . $id_don . ' assigné à la ville #' . $id_ville);
            $app->redirect('/dispatch');
        } else {
            error_log('[ERROR] Erreur lors de l\'assignation du don');
            $app->redirect('/dispatch');
        }
    });
}, [SecurityHeadersMiddleware::class]);

