<?php

namespace app\controllers;

use app\models\Achat;
use app\models\Besoin;
use app\models\Matiere;
use app\models\Dons;
use Exception;
use PDO;

class AchatController
{
    /**
     * Créer un achat depuis un besoin avec dons en argent
     * Le frais est passé par l'utilisateur.
     */
    public static function createFromBesoin($id_besoin, $quantite = null, $frais_achat = 0)
    {
        $besoin = Besoin::getById($id_besoin);
        if (!$besoin) {
            return [
                'success' => false,
                'message' => 'Besoin non trouvé'
            ];
        }
        
        // Vérifier si le besoin est en argent
        $matiere = Matiere::getById($besoin['id_matiere']);
        if (!$matiere) {
            return [
                'success' => false,
                'message' => 'Matière non trouvée'
            ];
        }
        
        // Récupérer la catégorie de la matière
        $DBH = \Flight::db();
        $query_cat = "SELECT c.nom FROM Matiere m 
                      LEFT JOIN Categorie c ON m.id_categorie = c.id_categorie 
                      WHERE m.id_matiere = :id_matiere";
        $stmt_cat = $DBH->prepare($query_cat);
        $stmt_cat->bindValue(':id_matiere', (int) $besoin['id_matiere'], PDO::PARAM_INT);
        $stmt_cat->execute();
        $mat_info = $stmt_cat->fetch();
        $is_argent = ($mat_info && $mat_info['nom'] === 'Argent');
        
        // Si ce n'est pas un besoin en argent
        if (!$is_argent) {
            // ÉTAPE 1: Vérifier les dons non distribués de cette matière
            $dons_non_distribues = Dons::getDonsNonDistribuesByMatiere($besoin['id_matiere']);
            $quantite_besoin = $quantite ?? $besoin['quantite'];
            $quantite_disponible_non_distribuee = Dons::getQuantiteTotalNonDistribuee($besoin['id_matiere']);
            
            // S'il y a des dons non distribués, retourner un message avec la liste
            if ($quantite_disponible_non_distribuee > 0) {
                return [
                    'success' => false,
                    'message' => 'Dons non distribués disponibles',
                    'has_undistributed_dons' => true,
                    'quantite_disponible' => $quantite_disponible_non_distribuee,
                    'quantite_besoin' => $quantite_besoin,
                    'dons' => $dons_non_distribues,
                    'id_besoin' => $id_besoin,
                    'id_ville' => $besoin['id_ville'],
                    'nom_matiere' => $matiere['nom_matiere']
                ];
            }
            
            // ÉTAPE 2: Si pas de dons non distribués, vérifier les dons en argent disponibles
            $montant_disponible = Dons::getMontantArgentDisponible($besoin['id_ville']);
            
            // Calculer le prix total nécessaire
            $frais_achat = max(0, (float) $frais_achat);
            $prix_base = $matiere['prix_unitaire'] * $quantite_besoin;
            $prix_total = Achat::calculateTotal($prix_base, $frais_achat);
            
            // Vérifier si le montant en argent est suffisant
            if ($montant_disponible < $prix_total) {
                return [
                    'success' => false,
                    'message' => "Montant en argent insuffisant. Nécessaire: " . number_format($prix_total, 2, ',', ' ') . " Ar. Disponible: " . number_format($montant_disponible, 2, ',', ' ') . " Ar"
                ];
            }
        }
        
        if (Achat::existeForBesoin($id_besoin)) {
            return [
                'success' => false,
                'message' => 'Un achat existe déjà pour ce besoin'
            ];
        }

        // Utiliser la quantité fournie ou celle du besoin
        $quantite_achat = $quantite ?? $besoin['quantite'];
        $frais_achat = max(0, (float) $frais_achat);

        // Calculer le prix total
        $prix_base = $matiere['prix_unitaire'] * $quantite_achat;
        $prix_total = Achat::calculateTotal($prix_base, $frais_achat);

        // Créer l'achat
        $achat = new Achat(
            null,
            $id_besoin,
            $besoin['id_ville'],
            $besoin['id_matiere'],
            $quantite_achat,
            $matiere['prix_unitaire'],
            $frais_achat,
            $prix_total,
            null,
            'en_attente'
        );

        try {
            // Créer le Don (la quantité de matière acquise)
            $don = new Dons(
                null,
                $besoin['id_matiere'],
                $quantite_achat,
                date('Y-m-d'),
                $besoin['id_ville']
            );
            
            if (!$don->insert_base()) {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la création du don'
                ];
            }

            // Créer l'enregistrement d'achat
            if ($achat->insert_base()) {
                return [
                    'success' => true,
                    'message' => "Achat créé avec succès (frais {$frais_achat}%)",
                    'id_achat' => $achat->id_achat,
                    'prix_total' => $prix_total,
                    'frais' => $frais_achat
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Erreur lors de la création de l\'achat'
                ];
            }
        } catch (Exception $e) {
            error_log('[ERROR] createFromBesoin: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de la création de l\'achat: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Récupérer tous les achats
     */
    public static function getAll()
    {
        return Achat::getAll();
    }

    /**
     * Récupérer les achats par ville
     */
    public static function getByVille($id_ville)
    {
        return Achat::getByVille($id_ville);
    }

    /**
     * Récupérer un achat par ID
     */
    public static function getById($id_achat)
    {
        return Achat::getById($id_achat);
    }

    /**
     * Mettre à jour le statut d'un achat
     */
    public static function updateStatut($id_achat, $statut)
    {
        return Achat::update($id_achat, null, $statut);
    }

    /**
     * Supprimer un achat
     */
    public static function delete($id_achat)
    {
        return Achat::delete($id_achat);
    }

    /**
     * Créer un achat avec un montant spécifique (depuis les besoins restants)
     * $frais_achat est saisi par l'utilisateur.
     */
    public static function creerAchat($id_matiere, $id_ville, $montant, $frais_achat = 0)
    {
        $DBH = \Flight::db();
        $frais_achat = max(0, (float) $frais_achat);

        // ÉTAPE 1: Vérifier les dons non distribués de cette matière
        $dons_non_distribues = Dons::getDonsNonDistribuesByMatiere($id_matiere);
        $quantite_disponible_non_distribuee = Dons::getQuantiteTotalNonDistribuee($id_matiere);
        
        // S'il y a des dons non distribués, retourner un message avec la liste
        if ($quantite_disponible_non_distribuee > 0) {
            return [
                'success' => false,
                'message' => 'Dons non distribués disponibles',
                'has_undistributed_dons' => true,
                'quantite_disponible' => $quantite_disponible_non_distribuee,
                'dons' => $dons_non_distribues,
                'id_matiere' => $id_matiere,
                'id_ville' => $id_ville,
                'montant' => $montant,
                'frais_achat' => $frais_achat
            ];
        }

        // Récupérer le prix unitaire de la matière
        $sql_prix = "SELECT prix_unitaire FROM Matiere WHERE id_matiere = :id_matiere";
        $stmt_prix = $DBH->prepare($sql_prix);
        $stmt_prix->bindValue(':id_matiere', (int) $id_matiere, \PDO::PARAM_INT);
        $stmt_prix->execute();
        $matiere = $stmt_prix->fetch(\PDO::FETCH_ASSOC);
        
        if (!$matiere) {
            return ['success' => false, 'message' => 'Matière non trouvée'];
        }

        // Le montant est le montant de base (sans frais)
        // Calculer la quantité: montant / prix_unitaire
        $quantite = floor($montant / $matiere['prix_unitaire']);
        $montant_avec_frais = $montant * (1 + $frais_achat / 100);

        // Insérer dans Dons (quantité en nature obtenue)
        $sql_don = "INSERT INTO Dons (id_matiere, quantite, date_don, id_ville) VALUES (:id_matiere, :quantite, NOW(), :id_ville)";
        $stmt_don = $DBH->prepare($sql_don);
        $stmt_don->bindValue(':id_matiere', (int) $id_matiere, \PDO::PARAM_INT);
        $stmt_don->bindValue(':quantite', (int) $quantite, \PDO::PARAM_INT);
        $stmt_don->bindValue(':id_ville', (int) $id_ville, \PDO::PARAM_INT);
        $stmt_don->execute();

        // Insérer dans Achats (avec les frais)
        $sql_achat = "INSERT INTO Achats (id_ville, id_matiere, quantite, prix_unitaire, frais_pourcentage, prix_total_achat, date_achat, statut) 
                      VALUES (:id_ville, :id_matiere, :quantite, :prix_unitaire, :frais, :montant_total, NOW(), 'completé')";
        $stmt_achat = $DBH->prepare($sql_achat);
        $stmt_achat->bindValue(':id_ville', (int) $id_ville, \PDO::PARAM_INT);
        $stmt_achat->bindValue(':id_matiere', (int) $id_matiere, \PDO::PARAM_INT);
        $stmt_achat->bindValue(':quantite', (int) $quantite, \PDO::PARAM_INT);
        $stmt_achat->bindValue(':prix_unitaire', (float) $matiere['prix_unitaire'], \PDO::PARAM_STR);
        $stmt_achat->bindValue(':frais', (float) $frais_achat, \PDO::PARAM_STR);
        $stmt_achat->bindValue(':montant_total', (float) $montant_avec_frais, \PDO::PARAM_STR);
        $stmt_achat->execute();

        return [
            'success' => true,
            'message' => 'Achat créé avec succès',
            'montant_base' => $montant,
            'montant_avec_frais' => $montant_avec_frais,
            'frais_pourcentage' => $frais_achat,
            'quantite' => $quantite
        ];
    }

    /**
     * Simuler un achat (sans vraiment l'enregistrer)
     * $frais_achat est saisi par l'utilisateur.
     */
    public static function simulerAchat($id_matiere, $id_ville, $montant, $frais_achat = 0)
    {
        $DBH = \Flight::db();
        $frais_achat = max(0, (float) $frais_achat);

        // Récupérer le prix unitaire de la matière
        $sql_prix = "SELECT prix_unitaire, nom_matiere FROM Matiere WHERE id_matiere = :id_matiere";
        $stmt_prix = $DBH->prepare($sql_prix);
        $stmt_prix->bindValue(':id_matiere', (int) $id_matiere, \PDO::PARAM_INT);
        $stmt_prix->execute();
        $matiere = $stmt_prix->fetch(\PDO::FETCH_ASSOC);
        
        if (!$matiere) {
            return ['success' => false, 'message' => 'Matière non trouvée'];
        }

        // Récupérer le besoin restant pour cette matière et ville
        // Requêtes séparées pour éviter le produit cartésien
        $sql_besoin = "SELECT COALESCE(SUM(b.quantite), 0) as total_besoin
                       FROM Besoin b
                       WHERE b.id_matiere = :id_matiere AND b.id_ville = :id_ville";
        $stmt_b = $DBH->prepare($sql_besoin);
        $stmt_b->bindValue(':id_matiere', (int) $id_matiere, \PDO::PARAM_INT);
        $stmt_b->bindValue(':id_ville', (int) $id_ville, \PDO::PARAM_INT);
        $stmt_b->execute();
        $besoin_data = $stmt_b->fetch(\PDO::FETCH_ASSOC);

        $sql_dons = "SELECT COALESCE(SUM(d.quantite), 0) as total_don
                     FROM Dons d
                     WHERE d.id_matiere = :id_matiere AND d.id_ville = :id_ville";
        $stmt_d = $DBH->prepare($sql_dons);
        $stmt_d->bindValue(':id_matiere', (int) $id_matiere, \PDO::PARAM_INT);
        $stmt_d->bindValue(':id_ville', (int) $id_ville, \PDO::PARAM_INT);
        $stmt_d->execute();
        $don_data = $stmt_d->fetch(\PDO::FETCH_ASSOC);

        $total_besoin = (float) $besoin_data['total_besoin'];
        $total_don = (float) $don_data['total_don'];
        $reste = $total_besoin - $total_don;

        // Calculer les valeurs
        $quantite_achetee = floor($montant / $matiere['prix_unitaire']);
        $montant_avec_frais = $montant * (1 + $frais_achat / 100);
        $montant_restant_theorique = ($reste - $quantite_achetee) * $matiere['prix_unitaire'];

        // Vérifications
        if ($montant > ($reste * $matiere['prix_unitaire'])) {
            return [
                'success' => false, 
                'message' => 'Le montant dépasse le besoin restant',
                'montant_demande' => $montant,
                'montant_restant' => $reste * $matiere['prix_unitaire'],
                'quantite_restante' => $reste
            ];
        }

        return [
            'success' => true,
            'message' => 'Simulation valide',
            'matiere' => $matiere['nom_matiere'],
            'montant_base' => $montant,
            'quantite_achetee' => intval($quantite_achetee),
            'prix_unitaire' => $matiere['prix_unitaire'],
            'frais_pourcentage' => $frais_achat,
            'frais_montant' => $montant * ($frais_achat / 100),
            'montant_total_achat' => $montant_avec_frais,
            'quantite_restante' => $reste,
            'quantite_apres_achat' => $reste - $quantite_achetee,
            'montant_restant_apres' => $montant_restant_theorique
        ];
    }

    /**
     * Valider et enregistrer un achat
     * $frais_achat est saisi par l'utilisateur.
     */
    public static function validerAchat($id_matiere, $id_ville, $montant, $frais_achat = 0)
    {
        $DBH = \Flight::db();
        $frais_achat = max(0, (float) $frais_achat);
        
        // Vérifier d'abord avec la simulation
        $simulation = self::simulerAchat($id_matiere, $id_ville, $montant, $frais_achat);
        if (!$simulation['success']) {
            return $simulation;
        }

        $prix_unitaire = $simulation['prix_unitaire'];

        // Calculer la quantité: montant / prix_unitaire
        $quantite = floor($montant / $prix_unitaire);
        $montant_avec_frais = $montant * (1 + $frais_achat / 100);

        try {
            // Insérer dans Dons (sans les frais)
            $sql_don = "INSERT INTO Dons (id_matiere, quantite, date_don, id_ville) VALUES (:id_matiere, :quantite, NOW(), :id_ville)";
            $stmt_don = $DBH->prepare($sql_don);
            $stmt_don->bindValue(':id_matiere', (int) $id_matiere, \PDO::PARAM_INT);
            $stmt_don->bindValue(':quantite', intval($quantite), \PDO::PARAM_INT);
            $stmt_don->bindValue(':id_ville', (int) $id_ville, \PDO::PARAM_INT);
            $stmt_don->execute();

            // Insérer dans Achats (avec les frais)
            $sql_achat = "INSERT INTO Achats (id_ville, id_matiere, quantite, prix_unitaire, frais_pourcentage, prix_total_achat, date_achat, statut) 
                          VALUES (:id_ville, :id_matiere, :quantite, :prix_unitaire, :frais, :montant_total, NOW(), 'completé')";
            $stmt_achat = $DBH->prepare($sql_achat);
            $stmt_achat->bindValue(':id_ville', (int) $id_ville, \PDO::PARAM_INT);
            $stmt_achat->bindValue(':id_matiere', (int) $id_matiere, \PDO::PARAM_INT);
            $stmt_achat->bindValue(':quantite', intval($quantite), \PDO::PARAM_INT);
            $stmt_achat->bindValue(':prix_unitaire', (float) $prix_unitaire, \PDO::PARAM_STR);
            $stmt_achat->bindValue(':frais', (float) $frais_achat, \PDO::PARAM_STR);
            $stmt_achat->bindValue(':montant_total', (float) $montant_avec_frais, \PDO::PARAM_STR);
            $stmt_achat->execute();

            return [
                'success' => true, 
                'message' => 'Achat enregistré avec succès',
                'montant_don' => $montant,
                'montant_achat_avec_frais' => $montant_avec_frais,
                'frais_pourcentage' => $frais_achat,
                'quantite' => intval($quantite)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement: ' . $e->getMessage()
            ];
        }
    }
}