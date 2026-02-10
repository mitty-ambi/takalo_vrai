<?php
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;
use app\models\User;
use app\models\Objet;
use app\models\Image_objet;
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/**
 * @var Router $router
 * @var Engine $app
 */

$router->group('', function (Router $router) use ($app) {
    $router->get('/', function () use ($app) {
        $app->render('register', ['ls_donnees_prod' => 'a']);
    });
    $router->get('/login', function () use ($app) {
        $app->render('login', ['connected' => '1']);
    });
    $router->post('/register', function () use ($app) {
        echo "[DEBUG] POST /register recu" . PHP_EOL;
        echo "[DEBUG] Data: " . json_encode($_POST) . PHP_EOL;

        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $type_user = $_POST['type_user'] ?? 'normal';

        $hash = password_hash($password, PASSWORD_DEFAULT);
        echo "[DEBUG] Hash genere pour mdp" . PHP_EOL;

        $user = new User($nom, $prenom, $email, $hash, $type_user);
        echo "[DEBUG] Objet User cree" . PHP_EOL;

        try {
            $user->insert_user();
            echo "[DEBUG] Insertion reussie!" . PHP_EOL;
        } catch (\Throwable $e) {
            echo "[DEBUG CATCH] Erreur lors insertion: " . $e->getMessage() . PHP_EOL;
            error_log('Insert user error: ' . $e->getMessage());
        }

        $app->render('login', ['connected' => '1']);
    });

    $router->post('/login', function () use ($app) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = new User(null, null, $email, $password, null);

        $user_id = $user->login();
        if ($user_id) {
            $_SESSION["id_user"] = $user_id;
            $user_data = $user->get_user_by_id($user_id);
            $_SESSION["user_data"] = $user_data;
            $app->redirect('/dashboard');
        } else {
            $app->render('login', ['error' => "Email ou mot de passe incorrect"]);
        }
    });
    $router->get('/dashboard', function () use ($app) {
        if (isset($_SESSION['id_user'])) {
            $user_data = $_SESSION['user_data'] ?? [];
            $objets = Objet::get_objet_by_id_user($_SESSION['id_user']);
            $image_objet = new Image_objet();
            $images_par_objet = [];
            foreach ($objets as &$objet) {
                $images = $image_objet->get_image_by_objet($objet['id']);
                
                if ($images) {
                    $images_par_objet[$objet['id']] = $images;
                } else {
                    $images_par_objet[$objet['id']] = [];
                }
            }
            $app->render('accueil', ['user_data' => $user_data, 'objets' => $objets, 'images_par_objet' => $images_par_objet]);
        } else {
            $app->redirect('/login');
        }
    });
}, [SecurityHeadersMiddleware::class]);
