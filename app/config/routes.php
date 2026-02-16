<?php
use app\controllers\BesoinController;
use app\controllers\DonsController;
use app\controllers\DashboardController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;
use app\models\Matiere;
use app\models\Dons;
use app\models\Ville;
use app\models\Besoin;
use Flight;
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/**
 * @var Router $router
 * @var Engine $app
 */

$router->group('', function (Router $router) use ($app) {
    $router->get('/', function () use ($app) {
        // optional search filter by city name
        $search = trim($_GET['nom_ville'] ?? '');
        $listeVille = Ville::getAll();

        if ($search !== '') {
            $listeVille = array_values(array_filter($listeVille, function ($v) use ($search) {
                return isset($v['nom_ville']) && stripos($v['nom_ville'], $search) !== false;
            }));
        }

        $app->render('index', ['listeVille' => $listeVille, 'nom_ville' => $search]);
    });

    $router->get('/dashboard', function () use ($app) {
        $data = \app\controllers\DashboardController::getDashboardData();
        $app->render('dashboard', $data);
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

    $router->get('/crud_dons', function () use ($app) {
        $dons = \app\models\Dons::getAllWithMatiere();
        $app->render('crud_dons', [
            'dons' => $dons
        ]);
    });

    $router->get('/crud_dons/edit/@id_don', function ($id_don) use ($app) {
        $don = \app\models\Dons::getByIdWithMatiere($id_don);

        if (!$don) {
            $app->render('crud_dons', [
                'dons' => \app\models\Dons::getAllWithMatiere(),
                'message_error' => 'Don non trouvé'
            ]);
            return;
        }

        $matieres = \app\models\Matiere::getAll();
        $villes = \app\models\Ville::getAll();

        $app->render('edit_don', [
            'don' => $don,
            'matieres' => $matieres,
            'villes' => $villes
        ]);
    });

    $router->post('/crud_dons/update', function () use ($app) {
        $id_don = $_POST['id_don'] ?? null;
        $id_matiere = $_POST['id_matiere'] ?? null;
        $quantite = $_POST['quantite'] ?? null;
        $date_don = $_POST['date_don'] ?? null;
        $id_ville = $_POST['id_ville'] ?? null;

        if (!$id_don || !$id_matiere || !$quantite || !$date_don) {
            error_log('[ERROR] Champs manquants pour update don');
            $app->redirect('/crud_dons');
            return;
        }

        if (\app\models\Dons::update($id_don, $id_matiere, $quantite, $date_don, $id_ville)) {
            error_log('[SUCCESS] Don #' . $id_don . ' mis à jour avec succès');
            $app->redirect('/crud_dons');
        } else {
            error_log('[ERROR] Erreur lors de la mise à jour du don');
            $app->redirect('/crud_dons');
        }
    });

    $router->post('/crud_dons/delete', function () use ($app) {
        $id_don = $_POST['id_don'] ?? null;

        if (!$id_don) {
            error_log('[ERROR] ID don manquant pour suppression');
            $app->redirect('/crud_dons');
            return;
        }

        if (\app\models\Dons::delete($id_don)) {
            error_log('[SUCCESS] Don #' . $id_don . ' supprimé avec succès');
        } else {
            error_log('[ERROR] Erreur lors de la suppression du don');
        }

        $app->redirect('/crud_dons');
    });
    $router->post('/registre', function () use ($app) {

        $result = \app\controllers\BesoinController::create();

        \Flight::json($result);
    });
}, [SecurityHeadersMiddleware::class]);

