<?php

namespace app\controllers;

use app\models\Achat;
use app\models\Besoin;
use app\models\Matiere;
use app\models\Dons;

class AchatController
{
    /**
     * Créer un achat depuis un besoin avec dons en argent
     */
    public static function createFromBesoin($id_besoin, $quantite = null)
    {
        $besoin = Besoin::getById($id_besoin);
        if (!$besoin) {
            return [
                'success' => false,
                'message' => 'Besoin non trouvé'
            ];
        }
        if (Achat::existeForBesoin($id_besoin)) {
            return [
                'success' => false,
                'message' => 'Un achat existe déjà pour ce besoin dans les dons restants'
            ];
        }

        // Récupérer la matière et son prix
        $matiere = Matiere::getById($besoin['id_matiere']);
        if (!$matiere) {
            return [
                'success' => false,
                'message' => 'Matière non trouvée'
            ];
        }

        // Utiliser la quantité fournie ou celle du besoin
        $quantite_achat = $quantite ?? $besoin['quantite'];

        // Récupérer la configuration du frais
        $config = \Flight::app()->config();
        $frais_achat = $config['frais_achat'] ?? 10; // Par défaut 10%

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

        if ($achat->insert_base()) {
            return [
                'success' => true,
                'message' => 'Achat créé avec succès',
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
     */
    public static function creerAchat($id_matiere, $id_ville, $montant)
    {
        $DBH = \Flight::db();
        $frais_achat = 10;

        // Récupérer le prix unitaire de la matière
        $sql_prix = "SELECT prix_unitaire FROM Matiere WHERE id_matiere = :id_matiere";
        $stmt_prix = $DBH->prepare($sql_prix);
        $stmt_prix->bindValue(':id_matiere', (int) $id_matiere, \PDO::PARAM_INT);
        $stmt_prix->execute();
        $matiere = $stmt_prix->fetch(\PDO::FETCH_ASSOC);
        
        if (!$matiere) {
            return ['success' => false, 'message' => 'Matière non trouvée'];
        }

        // Calculer la quantité: montant / prix_unitaire
        $quantite = $montant / $matiere['prix_unitaire'];

        // Insérer dans Dons
        $sql_don = "INSERT INTO Dons (id_matiere, quantite, date_don, id_ville) VALUES (:id_matiere, :quantite, NOW(), :id_ville)";
        $stmt_don = $DBH->prepare($sql_don);
        $stmt_don->bindValue(':id_matiere', (int) $id_matiere, \PDO::PARAM_INT);
        $stmt_don->bindValue(':quantite', (int) $quantite, \PDO::PARAM_INT);
        $stmt_don->bindValue(':id_ville', (int) $id_ville, \PDO::PARAM_INT);
        $stmt_don->execute();

        // Insérer dans Achats
        $sql_achat = "INSERT INTO Achats (id_ville, id_matiere, quantite, prix_unitaire, frais_pourcentage, prix_total_achat, date_achat, statut) 
                      VALUES (:id_ville, :id_matiere, 1, :montant, :frais, :montant_total, NOW(), 'completé')";
        $stmt_achat = $DBH->prepare($sql_achat);
        $stmt_achat->bindValue(':id_ville', (int) $id_ville, \PDO::PARAM_INT);
        $stmt_achat->bindValue(':id_matiere', (int) $id_matiere, \PDO::PARAM_INT);
        $stmt_achat->bindValue(':montant', (float) $montant, \PDO::PARAM_STR);
        $stmt_achat->bindValue(':frais', (float) $frais_achat, \PDO::PARAM_STR);
        $stmt_achat->bindValue(':montant_total', (float) $montant, \PDO::PARAM_STR);
        $stmt_achat->execute();

        return ['success' => true, 'message' => 'Achat créé'];
    }

    /**
     * Simuler un achat (sans vraiment l'enregistrer)
     */
    public static function simulerAchat($id_matiere, $id_ville, $montant)
    {
        $DBH = \Flight::db();
        $frais_achat = 10;

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
        $sql_restant = "SELECT 
                            COALESCE(SUM(b.quantite), 0) as total_besoin,
                            COALESCE(SUM(d.quantite), 0) as total_don,
                            COALESCE(SUM(b.quantite), 0) - COALESCE(SUM(d.quantite), 0) as reste
                        FROM Matiere m
                        LEFT JOIN Besoin b ON m.id_matiere = b.id_matiere AND b.id_ville = :id_ville
                        LEFT JOIN Dons d ON m.id_matiere = d.id_matiere AND d.id_ville = :id_ville
                        WHERE m.id_matiere = :id_matiere";
        $stmt_restant = $DBH->prepare($sql_restant);
        $stmt_restant->bindValue(':id_matiere', (int) $id_matiere, \PDO::PARAM_INT);
        $stmt_restant->bindValue(':id_ville', (int) $id_ville, \PDO::PARAM_INT);
        $stmt_restant->execute();
        $restant_data = $stmt_restant->fetch(\PDO::FETCH_ASSOC);

        // Calculer les valeurs
        $quantite_achetee = $montant / $matiere['prix_unitaire'];
        $montant_avec_frais = $montant * (1 + $frais_achat / 100);
        $montant_restant_theorique = ($restant_data['reste'] - $quantite_achetee) * $matiere['prix_unitaire'];

        // Vérifications
        if ($montant > ($restant_data['reste'] * $matiere['prix_unitaire'])) {
            return [
                'success' => false, 
                'message' => 'Le montant dépasse le besoin restant',
                'montant_demande' => $montant,
                'montant_restant' => $restant_data['reste'] * $matiere['prix_unitaire'],
                'quantite_restante' => $restant_data['reste']
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
            'quantite_restante' => $restant_data['reste'],
            'quantite_apres_achat' => $restant_data['reste'] - $quantite_achetee,
            'montant_restant_apres' => $montant_restant_theorique
        ];
    }

    /**
     * Valider et enregistrer un achat
     */
    public static function validerAchat($id_matiere, $id_ville, $montant)
    {
        $DBH = \Flight::db();
        
        // Vérifier d'abord avec la simulation
        $simulation = self::simulerAchat($id_matiere, $id_ville, $montant);
        if (!$simulation['success']) {
            return $simulation;
        }

        $frais_achat = 10;
        $matiere = [
            'prix_unitaire' => $simulation['prix_unitaire']
        ];

        // Calculer la quantité: montant / prix_unitaire
        $quantite = $montant / $matiere['prix_unitaire'];
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
            $stmt_achat->bindValue(':prix_unitaire', (float) $matiere['prix_unitaire'], \PDO::PARAM_STR);
            $stmt_achat->bindValue(':frais', (float) $frais_achat, \PDO::PARAM_STR);
            $stmt_achat->bindValue(':montant_total', (float) $montant_avec_frais, \PDO::PARAM_STR);
            $stmt_achat->execute();

            return [
                'success' => true, 
                'message' => 'Achat enregistré avec succès',
                'montant_don' => $montant,
                'montant_achat_avec_frais' => $montant_avec_frais,
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