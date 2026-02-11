<?php
use app\controllers\CategorieController;
use app\controllers\UserController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;
use app\models\User;
use app\models\Objet;
use app\models\Image_objet;
use app\models\Categorie;
use app\models\EchangeMere;
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
    $router->get('/search', function () use ($app) {
        $listeCat = CategorieController::getAll();
        $app->render('Search', ['listeCat' => $listeCat]);
    });
    $router->post('/api/search', function () use ($app) {
        
    });
    $router->get('/AdminStats', function () use ($app) {
        $user = new User(null, null, null, null, null);
        $statsParJour = UserController::StatsRegister($user);
        $registerCounts = [];
        $exchangeCounts = [];
        $allExchanges = [];
        $countExchange = EchangeMere::getCountExchange();
        try {
            $registerCounts = $user->getRegistrationsPerDay();
            error_log("registerCounts: " . print_r($registerCounts, true));
        } catch (\Throwable $e) {
            error_log('Erreur getRegistrationsPerDay: ' . $e->getMessage());
        }

        try {
            $echange = new EchangeMere(null, null, null, null, null, null);
            $exchangeCounts = $echange->getExchangesPerDay();
            $allExchanges = $echange->getAllEchange();
            error_log("exchangeCounts: " . print_r($exchangeCounts, true));
        } catch (\Throwable $e) {
            error_log('Erreur getExchangesPerDay: ' . $e->getMessage());
        }

        $app->render('AdminStats', [
            'statsParJour' => $statsParJour,
            'registerCounts' => $registerCounts,
            'exchangeCounts' => $exchangeCounts,
            'allExchanges' => $allExchanges,
            'totalEchange' => $countExchange
        ]);
    });
    $router->get('/register', function () use ($app) {
        $app->render('register', ['ls_donnees_prod' => 'a']);
    });
    $router->get('/EditCat', function () use ($app) {
        $id = $_GET['id_categorie'] ?? null;
        $category = null;
        if ($id) {
            $DBH = \Flight::db();
            $stmt = $DBH->prepare('SELECT * FROM Categorie WHERE id_categorie = ?');
            $stmt->execute([$id]);
            $category = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        $app->render('components/EditCat', ['category' => $category]);
    });

    $router->post('/EditCat', function () use ($app) {
        $id = $_POST['id'] ?? null;
        $nom = $_POST['nom'] ?? null;
        if ($id && $nom !== null) {
            $cat = new Categorie($id, $nom);
            try {
                $cat->update();
            } catch (\Throwable $e) {
                error_log('EditCat update error: ' . $e->getMessage());
            }
        }
        $app->redirect('/AdminCat');
    });
    $router->get('/login', function () use ($app) {
        $app->render('login', ['connected' => '1']);
    });
    $router->get('/AdminCat', function () use ($app) {

        $cat = new Categorie(0, null);
        $listeCat = $cat->getAll();
        $app->render('AdminCat', ['listeCat' => $listeCat]);
    });
    $router->get('/dashboard', function () use ($app) {
        if (isset($_SESSION['id_user'])) {
            $user_data = $_SESSION['user_data'] ?? [];
            $objets = Objet::get_objet_by_id_user($_SESSION['id_user']);
            $image_objet = new Image_objet();
            $images_par_objet = [];
            foreach ($objets as &$objet) {
                $images = $image_objet->get_image_by_objet($objet['id_objet']);

                if ($images) {
                    $images_par_objet[$objet['id_objet']] = $images;
                } else {
                    $images_par_objet[$objet['id_objet']] = [];
                }
            }
            $app->render('accueil', ['user_data' => $user_data, 'objets' => $objets, 'images_par_objet' => $images_par_objet]);
        } else {
            $app->redirect('/login');
        }
    });

    $router->post('/register', function () use ($app) {
        echo "[DEBUG] POST /register recu" . PHP_EOL;
        echo "[DEBUG] Data: " . json_encode($_POST) . PHP_EOL;

        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $email = $_POST['email'] ?? '';
        if (User::emailExists($email)) {
            $app->render('register', ['error_login' => 'email existe déjà']);
        }
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
    $router->post('/addCat', function () use ($app) {
        if (isset($_POST['cat'])) {
            $nom_cat = $_POST['cat'];
            $cat = new Categorie(0, $nom_cat);
            $cat->insert();
        }
        $app->redirect('/AdminCat');
    });

    $router->get('/dashboard', function () use ($app): void {
        if (isset($_SESSION['id_user'])) {
            $user_data = $_SESSION['user_data'] ?? [];
            error_log('[DEBUG] ID utilisateur: ' . $_SESSION['id_user']);
            $objetClass = 'app\\models\\Objet';
            if (class_exists($objetClass)) {
                $objets = call_user_func([$objetClass, 'get_objet_by_id_user'], $_SESSION['id_user']);
                error_log('[DEBUG] Résultat get_objet_by_id_user: ' . json_encode($objets));
            } else {
                error_log('Classe introuvable: ' . $objetClass);
                $objets = [];
            }
            $image_objet = null;
            $images_par_objet = [];
            $imageObjetClass = 'app\\models\\Image_objet';
            if (class_exists($imageObjetClass)) {
                $image_objet = new $imageObjetClass();
            } else {
                error_log('Classe introuvable: ' . $imageObjetClass);
            }
            foreach ($objets as &$objet) {
                $images = [];
                if ($image_objet) {
                    $images = $image_objet->get_image_by_objet($objet['id_objet']);
                } else {
                    $images = [];
                }
                if ($images) {
                    $images_par_objet[$objet['id_objet']] = $images;
                } else {
                    $images_par_objet[$objet['id_objet']] = [];
                }
            }
            $app->render('accueil', ['user_data' => $user_data, 'objets' => $objets, 'images_par_objet' => $images_par_objet]);
        } else {
            $app->redirect('/login');
        }
    });
    $router->get('/add-object', function () use ($app) {
        $CategorieObject = 'app\\models\\Categorie';
        if (class_exists($CategorieObject)) {
            $categories = call_user_func([$CategorieObject, 'get_all_categories']);
        } else {
            error_log('Classe introuvable: ' . $CategorieObject);
            $categories = [];
        }
        $app->render('ajouter_objet', [
            'user_data' => $_SESSION['user_data'] ?? [],
            'categories' => $categories,
        ]);
    });

    $router->post('/deleteCat', function () use ($app) {
        $id = $_POST['id'] ?? null;
        $cat = new Categorie($id, null);
        try {
            $cat->delete_cat();
            header_remove('Content-Security-Policy');
            header('Content-Type: text/plain');
            echo "success";
            return;
        } catch (\Throwable $e) {
            header('Content-Type: text/plain', true, 500);
            echo "Erreur : " . $e->getMessage();
            return;
        }
    });

    $router->post('/api/editCat', function () use ($app) {
        $id = $_POST['id'] ?? null;
        $nom = $_POST['nom'] ?? null;
        if (!$id || $nom === null) {
            header('Content-Type: application/json', true, 400);
            echo json_encode(['success' => false, 'error' => 'Missing parameters']);
            return;
        }
        $cat = new Categorie($id, $nom);
        try {
            $ok = $cat->update();
            header('Content-Type: application/json');
            echo json_encode(['success' => (bool) $ok]);
        } catch (\Throwable $e) {
            header('Content-Type: application/json', true, 500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    });
}, [SecurityHeadersMiddleware::class]);
