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
use app\models\Echange;
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
        $app->render('register', ['ls_donnees_prod' => 'a']);
    });
    $router->get('/search', function () use ($app) {
        $listeCat = CategorieController::getAll();
        $app->render('Search', ['listeCat' => $listeCat]);
    });
    $router->post('/search/results', function () use ($app) {
        $keyword = $_POST['keyword'] ?? null;
        $categorie_id = $_POST['categorie'] ?? null;

        $objets = ObjetController::search($keyword, $categorie_id);
        $listeCat = CategorieController::getAll();
        $images_par_objet = [];
        try {
            $imageModel = new Image_objet();
            if (is_array($objets)) {
                foreach ($objets as $objet) {
                    $images = $imageModel->get_image_by_objet($objet['id_objet']);
                    $images_par_objet[$objet['id_objet']] = $images ?: [];
                }
            }
        } catch (\Throwable $e) {
            error_log('Erreur récupération images search: ' . $e->getMessage());
        }

        $app->render('Search', [
            'listeCat' => $listeCat,
            'objets' => $objets,
            'images_objet' => $images_par_objet,
        ]);
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
    $router->get('/logout', function () use ($app) {
        session_destroy();
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
            $app->redirect('/gerer-objets');
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
    $router->get('/ajouter-objet', function () use ($app) {
        if (!isset($_SESSION['id_user'])) {
            $app->redirect('/login');
        }
        $categories = Categorie::get_all_categories();
        $app->render('ajouter_objet', [
            'donnees_utilisateur' => $_SESSION['user_data'] ?? [],
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

    $router->get('/gerer-objets', function () use ($app) {
        if (!isset($_SESSION['id_user'])) {
            $app->redirect('/login');
        }
        $user_data = $_SESSION['user_data'] ?? [];
        $objets = ObjectController::get_user_objects($_SESSION['id_user']);

        $objet_images = [];
        foreach ($objets as $objet) {
            $images = ObjectController::get_object_images($objet['id_objet']);
            $objet_images[$objet['id_objet']] = $images ?: [];
        }
        error_log(print_r($objet_images, true));
        $app->render('gerer_objets', [
            'donnees_utilisateur' => $user_data,
            'objets' => $objets,
            'images_objet' => $objet_images
        ]);
    });

    $router->post('/ajouter-objet', function () use ($app) {
        if (!isset($_SESSION['id_user'])) {
            $app->redirect('/login');
        }

        $nom_objet = $_POST['nom_objet'] ?? '';
        $description = $_POST['description'] ?? '';
        $id_categorie = $_POST['id_categorie'] ?? '';
        $prix_estime = $_POST['prix_estime'] ?? '';
        $date_acquisition = $_POST['date_acquisition'] ?? null;

        error_log('[DEBUG] ID Catégorie reçu: ' . $id_categorie);

        if ($nom_objet && $id_categorie && $prix_estime) {
            $id_objet = ObjectController::create_object(
                $nom_objet,
                $id_categorie,
                $_SESSION['id_user'],
                $description,
                $prix_estime,
                $date_acquisition
            );

            error_log('[DEBUG] ID Objet créé: ' . $id_objet);

            if ($id_objet) {
                if (isset($_FILES['images'])) {
                    foreach ($_FILES['images']['tmp_name'] as $tmp_name) {
                        if ($tmp_name) {
                            $filename = basename($tmp_name);
                            $destination = __DIR__ . '/../../public/assets/images/' . $filename;
                            if (move_uploaded_file($tmp_name, $destination)) {
                                ObjectController::add_image_to_object($id_objet, '/assets/images/' . $filename);
                            }
                        }
                    }
                }
            }

            $app->redirect('/gerer-objets');
        } else {
            $app->redirect('/ajouter-objet');
        }
    });

    $router->get('/editer-objet', function () use ($app) {
        if (!isset($_SESSION['id_user'])) {
            $app->redirect('/login');
        }

        $id_objet = $_GET['id'] ?? null;
        if (!$id_objet) {
            $app->redirect('/gerer-objets');
        }

        $objet = ObjectController::get_object_by_id($id_objet);
        if (!$objet || $objet['id_user'] !== $_SESSION['id_user']) {
            $app->redirect('/gerer-objets');
        }

        $categories = Categorie::get_all_categories();
        $images = ObjectController::get_object_images($id_objet);

        $app->render('editer_objet', [
            'donnees_utilisateur' => $_SESSION['user_data'] ?? [],
            'objet' => $objet,
            'categories' => $categories,
            'images' => $images
        ]);
    });

    $router->post('/editer-objet', function () use ($app) {
        if (!isset($_SESSION['id_user'])) {
            $app->redirect('/login');
        }

        $id_objet = $_POST['id_objet'] ?? null;
        $nom_objet = $_POST['nom_objet'] ?? '';
        $description = $_POST['description'] ?? '';
        $id_categorie = $_POST['id_categorie'] ?? '';
        $prix_estime = $_POST['prix_estime'] ?? '';

        if ($id_objet && ObjectController::update_object($id_objet, $nom_objet, $id_categorie, $description, $prix_estime)) {
            if (isset($_FILES['images'])) {
                foreach ($_FILES['images']['tmp_name'] as $tmp_name) {
                    if ($tmp_name) {
                        $filename = basename($tmp_name);
                        $destination = __DIR__ . '/../../public/assets/images/' . $filename;
                        if (move_uploaded_file($tmp_name, $destination)) {
                            ObjectController::add_image_to_object($id_objet, '/assets/images/' . $filename);
                        }
                    }
                }
            }
            $app->redirect('/gerer-objets');
        } else {
            $app->redirect('/editer-objet?id=' . $id_objet);
        }
    });

    $router->get('/supprimer-objet', function () use ($app) {
        if (!isset($_SESSION['id_user'])) {
            $app->redirect('/login');
        }

        $id_objet = $_GET['id'] ?? null;
        if ($id_objet && ObjectController::delete_object($id_objet)) {
            $app->redirect('/gerer-objets');
        }
    });

    $router->get('/parcourir', function () use ($app) {
        if (!isset($_SESSION['id_user'])) {
            $app->redirect('/login');
        }

        $user_data = $_SESSION['user_data'] ?? [];
        $objets = ObjectController::get_other_users_objects($_SESSION['id_user']);

        $objet_images = [];
        $user_names = [];
        foreach ($objets as $objet) {
            $images = ObjectController::get_object_images($objet['id_objet']);
            $objet_images[$objet['id_objet']] = $images ?: [];

            if (!isset($user_names[$objet['id_user']])) {
                $user_names[$objet['id_user']] = User::get_user_by_id_static($objet['id_user']);
            }
        }

        $app->render('parcourir_objets', [
            'donnees_utilisateur' => $user_data,
            'objets' => $objets,
            'images_objet' => $objet_images,
            'noms_utilisateurs' => $user_names
        ]);
    });

    $router->get('/detail-objet', function () use ($app) {
        if (!isset($_SESSION['id_user'])) {
            $app->redirect('/login');
        }

        $id_objet = $_GET['id'] ?? null;
        if (!$id_objet) {
            $app->redirect('/parcourir');
        }

        $objet = ObjectController::get_object_by_id($id_objet);
        if (!$objet) {
            $app->redirect('/parcourir');
        }

        // Vérifier que l'objet n'appartient pas à l'utilisateur connecté
        if ($objet['id_user'] === $_SESSION['id_user']) {
            $app->redirect('/gerer-objets');
        }

        $owner = User::get_user_by_id_static($objet['id_user']);

        $images = ObjectController::get_object_images($id_objet);

        $DBH = \Flight::db();
        $stmt = $DBH->prepare('SELECT nom_categorie FROM Categorie WHERE id_categorie = ?');
        $stmt->execute([$objet['id_categorie']]);
        $category = $stmt->fetch(\PDO::FETCH_ASSOC);
        $category_name = $category['nom_categorie'] ?? '';

        $my_objects = ObjectController::get_user_objects($_SESSION['id_user']) ?: [];

        $app->render('detail_objet', [
            'donnees_utilisateur' => $_SESSION['user_data'] ?? [],
            'objet' => $objet,
            'proprietaire' => $owner,
            'images' => $images,
            'nom_categorie' => $category_name,
            'mes_objets' => $my_objects
        ]);
    });

    $router->post('/proposer-echange', function () use ($app) {
        if (!isset($_SESSION['id_user'])) {
            $app->redirect('/login');
        }

        $id_objet_sender = $_POST['id_objet_sender'] ?? null;
        $id_objet_receiver = $_POST['id_objet_receiver'] ?? null;

        if (!$id_objet_sender || !$id_objet_receiver) {
            $app->redirect('/detail-objet?id=' . $id_objet_receiver);
        }

        $objet_sender = ObjectController::get_object_by_id($id_objet_sender);
        if (!$objet_sender || $objet_sender['id_user'] !== $_SESSION['id_user']) {
            error_log('[ERROR] User ' . $_SESSION['id_user'] . ' tried to send object ' . $id_objet_sender . ' they do not own');
            $app->redirect('/parcourir');
        }

        $objet_receiver = ObjectController::get_object_by_id($id_objet_receiver);
        if (!$objet_receiver || $objet_receiver['id_user'] === $_SESSION['id_user']) {
            error_log('[ERROR] User ' . $_SESSION['id_user'] . ' tried to exchange with themselves');
            $app->redirect('/parcourir');
        }

        $id_user_2 = $objet_receiver['id_user'];

        $id_echange = ExchangeController::create_exchange($_SESSION['id_user'], $id_user_2);

        if ($id_echange) {
            ExchangeController::add_object_to_exchange($id_echange, $id_objet_sender, $_SESSION['id_user']);
            ExchangeController::add_object_to_exchange($id_echange, $id_objet_receiver, $id_user_2);
        }

        $app->redirect('/gerer-echanges');
    });
    $router->get('/gerer-echanges', function () use ($app) {
        if (!isset($_SESSION['id_user'])) {
            $app->redirect('/login');
        }

        $user_data = $_SESSION['user_data'] ?? [];

        $received_exchanges = ExchangeController::get_pending_received_exchanges($_SESSION['id_user']) ?: [];
        $sent_exchanges = ExchangeController::get_pending_sent_exchanges($_SESSION['id_user']) ?: [];
        $all_exchanges = ExchangeController::get_user_exchanges($_SESSION['id_user']) ?: [];

        $history_exchanges = array_filter($all_exchanges, function ($e) {
            return $e['statut'] !== 'en attente';
        });

        $senders = [];
        $receivers = [];
        $exchange_items = [];
        $all_users = [];

        foreach (array_merge($received_exchanges, $sent_exchanges, $history_exchanges) as $exchange) {
            if (!isset($senders[$exchange['id_user_1']])) {
                $senders[$exchange['id_user_1']] = User::get_user_by_id_static($exchange['id_user_1']);
            }
            if (!isset($receivers[$exchange['id_user_2']])) {
                $receivers[$exchange['id_user_2']] = User::get_user_by_id_static($exchange['id_user_2']);
            }
            if (!isset($all_users[$exchange['id_user_1']])) {
                $all_users[$exchange['id_user_1']] = User::get_user_by_id_static($exchange['id_user_1']);
            }
            if (!isset($all_users[$exchange['id_user_2']])) {
                $all_users[$exchange['id_user_2']] = User::get_user_by_id_static($exchange['id_user_2']);
            }

            $items = ExchangeController::get_exchange_objects($exchange['id_echange']);
            $exchange_items[$exchange['id_echange']] = [
                'sender' => array_filter($items, function ($item) use ($exchange) {
                    return $item['id_proprietaire'] == $exchange['id_user_1'];
                }),
                'receiver' => array_filter($items, function ($item) use ($exchange) {
                    return $item['id_proprietaire'] == $exchange['id_user_2'];
                })
            ];
        }

        $app->render('gerer_echanges', [
            'donnees_utilisateur' => $user_data,
            'echanges_recus' => $received_exchanges,
            'echanges_envoyes' => $sent_exchanges,
            'historique_echanges' => $history_exchanges,
            'expediteurs' => $senders,
            'destinataires' => $receivers,
            'tous_utilisateurs' => $all_users,
            'elements_echange' => $exchange_items
        ]);
    });

    $router->get('/accepter-echange', function () use ($app) {
        if (!isset($_SESSION['id_user'])) {
            $app->redirect('/login');
        }
        $id_echange = $_GET['id'] ?? null;
        echo $id_echange;
        error_log('[DEBUG] /accepter-echange called with id: ' . $id_echange);
        $donnees = Echangefille::get_by_echange($id_echange);
        $user_1 = $donnees[0]['id_proprietaire'];
        $user_2 = $donnees[1]['id_proprietaire'];
        $user_1 = $donnees[0]['id_proprietaire'];
        $user_2 = $donnees[1]['id_proprietaire'];


        $objet_1 = $donnees[0]['id_objet'];
        $objet_2 = $donnees[1]['id_objet'];
        if ($id_echange) {
            $result = ExchangeController::accept_exchange($id_echange, $user_1, $user_2, $objet_1, $objet_2);
            error_log('[DEBUG] accept_exchange returned: ' . ($result ? 'true' : 'false'));
        }

        $app->redirect('/gerer-echanges');
    });

    $router->get('/refuser-echange', function () use ($app) {
        if (!isset($_SESSION['id_user'])) {
            $app->redirect('/login');
        }

        $id_echange = $_GET['id'] ?? null;
        error_log('[DEBUG] /refuser-echange called with id: ' . $id_echange);
        if ($id_echange) {
            $result = ExchangeController::refuse_exchange($id_echange);
            error_log('[DEBUG] refuse_exchange returned: ' . ($result ? 'true' : 'false'));
        }

        $app->redirect('/gerer-echanges');
    });

    $router->get('/annuler-echange', function () use ($app) {
        if (!isset($_SESSION['id_user'])) {
            $app->redirect('/login');
        }

        $id_echange = $_GET['id'] ?? null;
        error_log('[DEBUG] /annuler-echange called with id: ' . $id_echange);
        if ($id_echange) {
            $result = ExchangeController::cancel_exchange($id_echange);
            error_log('[DEBUG] cancel_exchange returned: ' . ($result ? 'true' : 'false'));
        }

        $app->redirect('/gerer-echanges');
    });
}, [SecurityHeadersMiddleware::class]);

Flight::route('GET /objet/@id/history', function($id) {
    $history = \app\models\Objet::history((int)$id);
    Flight::render('object_history', ['history' => $history, 'id_objet' => (int)$id]);
});
