<?php
use app\controllers\BesoinController;
use app\controllers\DonsController;
use app\controllers\AchatController;
use app\controllers\CategorieController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;
use app\models\Matiere;
use app\models\Dons;
use app\models\Ville;
use app\models\Besoin;
use app\models\Achat;
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
        $a = 0;
        $search = trim($_GET['nom_ville'] ?? '');
        $listeVille = Ville::getAll();
        if ($search !== '') {
            $listeVille = array_values(array_filter($listeVille, function ($v) use ($search) {
                return isset($v['nom_ville']) && stripos($v['nom_ville'], $search) !== false;
            }));
        }
        $app->render('index', ['listeVille' => $listeVille, 'nom_ville' => $search]);
    });
    $router->get('/StatsVille', function () use ($app) {
        $id_ville = $_GET['id_ville'];
        $listeBesoin = BesoinController::getBesoinVilleGrouped($id_ville);
        $listeDons = DonsController::getDonsVilleGrouped($id_ville);
        $besoinRestant = DonsController::getBesoinRestantVille($id_ville);
        $app->render('StatsVille', ['listeBesoin' => $listeBesoin, 'listeDons' => $listeDons, 'besoinRestant' => $besoinRestant, 'nomville' => Ville::getNomVIlle($id_ville)]);
    });
    $router->get('/gerer_besoins', function () use ($app) {
        $villes = \app\models\Ville::getAll();
        $matieres = \app\models\Matiere::getAll();
        $categorie = CategorieController::getAll();
        $app->render('gerer_besoins', [
            'villes' => $villes,
            'matieres' => $matieres,
            'categories' => $categorie
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
    $router->get('/simulation', function () use ($app) {
        $villes = \app\models\Ville::getAll();
        $matieres = \app\models\Matiere::getAll();
        $app->render('simulation', [
            'villes' => $villes,
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
        $categories = CategorieController::getAll();
        $app->render('edit_don', [
            'don' => $don,
            'matieres' => $matieres,
            'villes' => $villes,
            'categories' => $categories
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

    // Routes pour les achats
    $router->get('/gerer_achats', function () use ($app) {
        $filter_ville = $_GET['id_ville'] ?? null;
        $filter_statut = $_GET['statut'] ?? null;

        $villes = Ville::getAll();
        $achats = Achat::getAll();

        // Filtrer par ville
        if ($filter_ville) {
            $achats = array_filter($achats, function ($a) use ($filter_ville) {
                return $a['id_ville'] == $filter_ville;
            });
        }

        // Filtrer par statut
        if ($filter_statut) {
            $achats = array_filter($achats, function ($a) use ($filter_statut) {
                return $a['statut'] === $filter_statut;
            });
        }

        $app->render('gerer_achats', [
            'achats' => array_values($achats),
            'villes' => $villes,
            'filter_ville' => $filter_ville,
            'filter_statut' => $filter_statut
        ]);
    });

    $router->post('/gerer_achats/create', function () use ($app) {
        $id_besoin = $_POST['id_besoin'] ?? null;
        $quantite = $_POST['quantite'] ?? null;

        if (!$id_besoin) {
            $app->redirect('/gerer_besoins?error=Besoin requis');
            return;
        }

        $result = AchatController::createFromBesoin($id_besoin, $quantite);

        if ($result['success']) {
            $app->redirect('/gerer_achats?success=' . urlencode($result['message']));
        } else {
            $app->redirect('/gerer_besoins?error=' . urlencode($result['message']));
        }
    });

    $router->post('/gerer_achats/delete', function () use ($app) {
        $id_achat = $_POST['id_achat'] ?? null;

        if (!$id_achat) {
            error_log('[ERROR] ID achat manquant pour suppression');
            $app->redirect('/gerer_achats');
            return;
        }

        if (AchatController::delete($id_achat)) {
            error_log('[SUCCESS] Achat #' . $id_achat . ' supprimé avec succès');
        } else {
            error_log('[ERROR] Erreur lors de la suppression de l\'achat');
        }

        $app->redirect('/gerer_achats');
    });

    // API pour vérifier les dons non distribués avant achat
    $router->get('/api/achats/check-undistributed/@id_matiere/@id_ville', function ($id_matiere, $id_ville) use ($app) {
        $dons_non_distribues = \app\models\Dons::getDonsNonDistribuesByMatiere($id_matiere);
        $quantite_disponible = \app\models\Dons::getQuantiteTotalNonDistribuee($id_matiere);
        
        if ($quantite_disponible > 0) {
            \Flight::json([
                'success' => false,
                'has_undistributed_dons' => true,
                'quantite_disponible' => $quantite_disponible,
                'dons' => $dons_non_distribues,
                'id_matiere' => $id_matiere,
                'id_ville' => $id_ville,
                'message' => 'Dons non distribués disponibles pour cette matière'
            ]);
        } else {
            \Flight::json([
                'success' => true,
                'has_undistributed_dons' => false,
                'message' => 'Aucun don non distribué'
            ]);
        }
    });

    // API pour acheter un besoin (AJAX)
    $router->post('/api/achats/create', function () use ($app) {
        $id_matiere = $_POST['id_matiere'] ?? null;
        $id_ville = $_POST['id_ville'] ?? null;
        $montant = $_POST['montant'] ?? null;

        if (!$id_matiere || !$id_ville || !$montant) {
            \Flight::json(['success' => false, 'message' => 'Paramètres requis']);
            return;
        }

        $result = AchatController::creerAchat($id_matiere, $id_ville, (float) $montant);
        \Flight::json($result);
    });

    // API pour simuler un achat (prévisualisation)
    $router->post('/api/achats/simuler', function () use ($app) {
        $id_matiere = $_POST['id_matiere'] ?? null;
        $id_ville = $_POST['id_ville'] ?? null;
        $montant = $_POST['montant'] ?? null;

        if (!$id_matiere || !$id_ville || !$montant) {
            \Flight::json(['success' => false, 'message' => 'Paramètres requis']);
            return;
        }

        $result = AchatController::simulerAchat($id_matiere, $id_ville, (float) $montant);
        \Flight::json($result);
    });

    // API pour valider et enregistrer un achat
    $router->post('/api/achats/valider', function () use ($app) {
        $id_matiere = $_POST['id_matiere'] ?? null;
        $id_ville = $_POST['id_ville'] ?? null;
        $montant = $_POST['montant'] ?? null;

        if (!$id_matiere || !$id_ville || !$montant) {
            \Flight::json(['success' => false, 'message' => 'Paramètres requis']);
            return;
        }

        $result = AchatController::validerAchat($id_matiere, $id_ville, (float) $montant);
        \Flight::json($result);
    });

    // API pour stats récapitulatives globales
    $router->get('/api/stats/recap', function () use ($app) {
        $DBH = \Flight::db();

        // Besoins totaux en montant
        $query_total = "SELECT COALESCE(SUM(b.quantite * m.prix_unitaire), 0) as montant_total
                        FROM Besoin b
                        JOIN Matiere m ON b.id_matiere = m.id_matiere";
        $total = $DBH->query($query_total)->fetch(\PDO::FETCH_ASSOC);

        // Besoins satisfaits en montant
        $query_satisfait = "SELECT COALESCE(SUM(d.quantite * m.prix_unitaire), 0) as montant_satisfait
                           FROM Dons d
                           JOIN Matiere m ON d.id_matiere = m.id_matiere";
        $satisfait = $DBH->query($query_satisfait)->fetch(\PDO::FETCH_ASSOC);

        // Besoins restants
        $montant_restant = $total['montant_total'] - $satisfait['montant_satisfait'];

        \Flight::json([
            'montant_total' => (float) $total['montant_total'],
            'montant_satisfait' => (float) $satisfait['montant_satisfait'],
            'montant_restant' => (float) $montant_restant
        ]);
    });

    // API pour récupérer les besoins restants (non achetés)
    $router->get('/api/besoins/remaining', function () use ($app) {
        $DBH = \Flight::db();
        $query = "SELECT b.*, m.nom_matiere, m.prix_unitaire, v.nom_ville
                  FROM Besoin b
                  JOIN Matiere m ON b.id_matiere = m.id_matiere
                  JOIN Ville v ON b.id_ville = v.id_ville
                  WHERE b.id_besoin NOT IN (SELECT id_besoin FROM Achats WHERE id_besoin IS NOT NULL)
                  ORDER BY v.nom_ville, m.nom_matiere";
        $stmt = $DBH->query($query);
        $besoins = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        \Flight::json($besoins);
    });
    $router->get('/simulation', function () use ($app) {
        $app->render('simulation'); // affichera app/views/simulation.php
    });

    // Dispatch par date de demande (priorité aux demandes les plus anciennes)
    $router->get('/dispatch-par-date', function () use ($app) {
        $DBH = \Flight::db();
        
        // Récupérer tous les besoins groupés par matière avec les villes et leurs demandes
        $query = "SELECT b.id_matiere, m.nom_matiere, m.id_categorie, c.nom as nom_categorie,
                         b.id_ville, v.nom_ville, b.quantite, b.date_du_demande, b.id_besoin
                  FROM Besoin b
                  JOIN Matiere m ON b.id_matiere = m.id_matiere
                  LEFT JOIN Categorie c ON m.id_categorie = c.id_categorie
                  JOIN Ville v ON b.id_ville = v.id_ville
                  ORDER BY b.id_matiere, b.date_du_demande ASC";
        
        $besoins_par_matiere = [];
        $stmt = $DBH->query($query);
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            if (!isset($besoins_par_matiere[$row['id_matiere']])) {
                $besoins_par_matiere[$row['id_matiere']] = [
                    'nom_matiere' => $row['nom_matiere'],
                    'nom_categorie' => $row['nom_categorie'],
                    'id_categorie' => $row['id_categorie'],
                    'besoins' => []
                ];
            }
            $besoins_par_matiere[$row['id_matiere']]['besoins'][] = $row;
        }
        
        // Récupérer tous les dons non distribués
        $query_dons = "SELECT d.*, m.nom_matiere, m.id_matiere
                       FROM Dons d
                       JOIN Matiere m ON d.id_matiere = m.id_matiere
                       WHERE d.id_ville = 0
                       ORDER BY d.id_matiere, d.date_don ASC";
        
        $dons_non_distribues = [];
        $stmt_dons = $DBH->query($query_dons);
        foreach ($stmt_dons->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            if (!isset($dons_non_distribues[$row['id_matiere']])) {
                $dons_non_distribues[$row['id_matiere']] = [];
            }
            $dons_non_distribues[$row['id_matiere']][] = $row;
        }
        
        $app->render('dispatch-par-date', [
            'besoins_par_matiere' => $besoins_par_matiere,
            'dons_non_distribues' => $dons_non_distribues
        ]);
    });

    // Dispatch par minimum de demande (priorité aux besoins les plus petits)
    $router->get('/dispatch-par-min', function () use ($app) {
        $DBH = \Flight::db();
        
        // Récupérer tous les besoins groupés par matière avec les villes et leurs demandes
        $query = "SELECT b.id_matiere, m.nom_matiere, m.id_categorie, c.nom as nom_categorie,
                         b.id_ville, v.nom_ville, b.quantite, b.date_du_demande, b.id_besoin
                  FROM Besoin b
                  JOIN Matiere m ON b.id_matiere = m.id_matiere
                  LEFT JOIN Categorie c ON m.id_categorie = c.id_categorie
                  JOIN Ville v ON b.id_ville = v.id_ville
                  ORDER BY b.id_matiere, b.quantite ASC";
        
        $besoins_par_matiere = [];
        $stmt = $DBH->query($query);
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            if (!isset($besoins_par_matiere[$row['id_matiere']])) {
                $besoins_par_matiere[$row['id_matiere']] = [
                    'nom_matiere' => $row['nom_matiere'],
                    'nom_categorie' => $row['nom_categorie'],
                    'id_categorie' => $row['id_categorie'],
                    'besoins' => []
                ];
            }
            $besoins_par_matiere[$row['id_matiere']]['besoins'][] = $row;
        }
        
        // Récupérer tous les dons non distribués
        $query_dons = "SELECT d.*, m.nom_matiere, m.id_matiere
                       FROM Dons d
                       JOIN Matiere m ON d.id_matiere = m.id_matiere
                       WHERE d.id_ville = 0
                       ORDER BY d.id_matiere, d.date_don ASC";
        
        $dons_non_distribues = [];
        $stmt_dons = $DBH->query($query_dons);
        foreach ($stmt_dons->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            if (!isset($dons_non_distribues[$row['id_matiere']])) {
                $dons_non_distribues[$row['id_matiere']] = [];
            }
            $dons_non_distribues[$row['id_matiere']][] = $row;
        }
        
        $app->render('dispatch-par-min', [
            'besoins_par_matiere' => $besoins_par_matiere,
            'dons_non_distribues' => $dons_non_distribues
        ]);
    });

    // Dispatch proportionnel (ratio besoin/nombre de dons, arrondi à la valeur basse)
    $router->get('/dispatch-proportionnel', function () use ($app) {
        $DBH = \Flight::db();
        
        // Récupérer tous les besoins groupés par matière avec les villes et leurs demandes
        $query = "SELECT b.id_matiere, m.nom_matiere, m.id_categorie, c.nom as nom_categorie,
                         b.id_ville, v.nom_ville, b.quantite, b.date_du_demande, b.id_besoin
                  FROM Besoin b
                  JOIN Matiere m ON b.id_matiere = m.id_matiere
                  JOIN Categorie c ON m.id_categorie = c.id_categorie
                  JOIN Ville v ON b.id_ville = v.id_ville
                  WHERE b.quantite > 0
                  ORDER BY b.id_matiere, b.id_besoin ASC";
        
        $besoins_par_matiere = [];
        $stmt = $DBH->query($query);
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            if (!isset($besoins_par_matiere[$row['id_matiere']])) {
                $besoins_par_matiere[$row['id_matiere']] = [
                    'nom_matiere' => $row['nom_matiere'],
                    'nom_categorie' => $row['nom_categorie'],
                    'id_categorie' => $row['id_categorie'],
                    'besoins' => []
                ];
            }
            $besoins_par_matiere[$row['id_matiere']]['besoins'][] = $row;
        }
        
        // Récupérer tous les dons non distribués
        $query_dons = "SELECT d.*, m.nom_matiere, m.id_matiere
                       FROM Dons d
                       JOIN Matiere m ON d.id_matiere = m.id_matiere
                       WHERE d.id_ville = 0
                       ORDER BY d.id_matiere, d.date_don ASC";
        
        $dons_non_distribues = [];
        $stmt_dons = $DBH->query($query_dons);
        foreach ($stmt_dons->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            if (!isset($dons_non_distribues[$row['id_matiere']])) {
                $dons_non_distribues[$row['id_matiere']] = [];
            }
            $dons_non_distribues[$row['id_matiere']][] = $row;
        }
        
        $app->render('dispatch-proportionnel', [
            'besoins_par_matiere' => $besoins_par_matiere,
            'dons_non_distribues' => $dons_non_distribues
        ]);
    });

    // API pour valider et dispatcher les dons
    $router->post('/api/dispatch/valider', function () use ($app) {
        $type_dispatch = $_POST['type_dispatch'] ?? null;
        $repartition_json = $_POST['repartition'] ?? '[]';
        
        if (!$type_dispatch) {
            \Flight::redirect('/dispatch-par-date');
            return;
        }
        
        // Décoder les données de repartition
        $repartition = json_decode($repartition_json, true);
        
        if (empty($repartition)) {
            \Flight::redirect('/dispatch-par-date');
            return;
        }
        
        $DBH = \Flight::db();
        $DBH->beginTransaction();
        
        try {
            // Pour chaque attribution, mettre à jour les dons
            foreach ($repartition as $attribution) {
                $id_ville = $attribution['id_ville'];
                $quantite_a_dispatcher = $attribution['quantite'];
                $id_besoin = $attribution['id_besoin'];
                
                // Récupérer le id_matiere du besoin
                $query = "SELECT id_matiere FROM Besoin WHERE id_besoin = :id_besoin";
                $stmt = $DBH->prepare($query);
                $stmt->bindValue(':id_besoin', $id_besoin, \PDO::PARAM_INT);
                $stmt->execute();
                $besoin = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                if (!$besoin) continue;
                
                $id_matiere = $besoin['id_matiere'];
                
                // Récupérer les dons non distribués de cette matière (par ordre)
                $query_dons = "SELECT id_don, quantite FROM Dons 
                              WHERE id_matiere = :id_matiere AND id_ville = 0
                              ORDER BY date_don ASC";
                $stmt_dons = $DBH->prepare($query_dons);
                $stmt_dons->bindValue(':id_matiere', $id_matiere, \PDO::PARAM_INT);
                $stmt_dons->execute();
                $dons = $stmt_dons->fetchAll(\PDO::FETCH_ASSOC);
                
                // Dispatcher les dons
                $quantite_restante = $quantite_a_dispatcher;
                foreach ($dons as $don) {
                    if ($quantite_restante <= 0) break;
                    
                    $quantite_a_prendre = min($don['quantite'], $quantite_restante);
                    
                    // Mettre à jour le don
                    $update_query = "UPDATE Dons SET id_ville = :id_ville, quantite = quantite - :quantite_a_prendre 
                                   WHERE id_don = :id_don";
                    $update_stmt = $DBH->prepare($update_query);
                    $update_stmt->bindValue(':id_ville', $id_ville, \PDO::PARAM_INT);
                    $update_stmt->bindValue(':quantite_a_prendre', $quantite_a_prendre, \PDO::PARAM_INT);
                    $update_stmt->bindValue(':id_don', $don['id_don'], \PDO::PARAM_INT);
                    $update_stmt->execute();
                    
                    $quantite_restante -= $quantite_a_prendre;
                }
            }
            
            $DBH->commit();
            error_log('[SUCCESS] Dispatch validé avec succès');
            \Flight::redirect('/dashboard?success=Dispatch validé avec succès');
        } catch (Exception $e) {
            $DBH->rollBack();
            error_log('[ERROR] Erreur lors du dispatch: ' . $e->getMessage());
            \Flight::redirect('/dispatch-par-date?error=' . urlencode($e->getMessage()));
        }
    });

    // Route pour réinitialiser toutes les données
    $router->post('/api/reinitialiser', function () use ($app) {
        $DBH = \Flight::db();
        $DBH->beginTransaction();
        
        try {
            // Supprimer tous les achats
            $query_delete_achats = "DELETE FROM Achats";
            $DBH->exec($query_delete_achats);
            error_log('[INFO] Tous les achats supprimés');
            
            // Réinitialiser tous les dons (id_ville = 0)
            $query_reset_dons = "UPDATE Dons SET id_ville = 0";
            $DBH->exec($query_reset_dons);
            error_log('[INFO] Tous les dons réinitialisés (id_ville = 0)');
            
            // Les besoins restent avec leur quantité initiale (pas de suppression)
            // Les besoins sont déjà créés avec leur quantité originale
            error_log('[INFO] Besoins conservés avec leur quantité initiale');
            
            $DBH->commit();
            error_log('[SUCCESS] Réinitialisation complète réussie');
            \Flight::redirect('/?success=Réinitialisation complète réussie. Tous les dons sont maintenant non distribués, les besoins sont réinitialisés à leurs valeurs initiales, et tous les achats ont été supprimés.');
        } catch (Exception $e) {
            $DBH->rollBack();
            error_log('[ERROR] Erreur lors de la réinitialisation: ' . $e->getMessage());
            \Flight::redirect('/?error=' . urlencode('Erreur lors de la réinitialisation: ' . $e->getMessage()));
        }
    });
}, [SecurityHeadersMiddleware::class]);


// ZXFQM6vH