-- ============================================
-- IMPORT DES DONNÉES - 17 Février 2026
-- ============================================
-- Vider les tables existantes (optionnel - décommenter si besoin)
-- TRUNCATE TABLE Besoin;
-- TRUNCATE TABLE Dons;
-- TRUNCATE TABLE Matiere;
-- TRUNCATE TABLE Ville;
-- TRUNCATE TABLE Categorie;
-- ============================================
-- 1. CATEGORIES
-- ============================================
INSERT INTO
    Categorie (id_categorie, nom)
VALUES
    (1, 'nature');

INSERT INTO
    Categorie (id_categorie, nom)
VALUES
    (2, 'materiel');

INSERT INTO
    Categorie (id_categorie, nom)
VALUES
    (3, 'argent');

-- ============================================
-- 2. REGIONS
-- ============================================
INSERT INTO
    Region (id_region, nom_region)
VALUES
    (1, 'Atsinanana');

INSERT INTO
    Region (id_region, nom_region)
VALUES
    (2, 'Vatovavy-Fitovinany');

INSERT INTO
    Region (id_region, nom_region)
VALUES
    (3, 'Atsimo-Atsinanana');

INSERT INTO
    Region (id_region, nom_region)
VALUES
    (4, 'Diana');

INSERT INTO
    Region (id_region, nom_region)
VALUES
    (5, 'Menabe');

-- ============================================
-- 3. VILLES
-- ============================================
INSERT INTO
    Ville (
        id_ville,
        nom_ville,
        id_region,
        nombres_sinistres
    )
VALUES
    (1, 'Toamasina', 1, 500);

INSERT INTO
    Ville (
        id_ville,
        nom_ville,
        id_region,
        nombres_sinistres
    )
VALUES
    (2, 'Mananjary', 2, 350);

INSERT INTO
    Ville (
        id_ville,
        nom_ville,
        id_region,
        nombres_sinistres
    )
VALUES
    (3, 'Farafangana', 3, 400);

INSERT INTO
    Ville (
        id_ville,
        nom_ville,
        id_region,
        nombres_sinistres
    )
VALUES
    (4, 'Nosy Be', 4, 200);

INSERT INTO
    Ville (
        id_ville,
        nom_ville,
        id_region,
        nombres_sinistres
    )
VALUES
    (5, 'Morondava', 5, 450);

-- ============================================
-- 3. MATIERES
-- ============================================
-- Nature (id_categorie = 1)
INSERT INTO
    Matiere (
        id_matiere,
        nom_matiere,
        prix_unitaire,
        id_categorie
    )
VALUES
    (1, 'Riz (kg)', 3000, 1);

INSERT INTO
    Matiere (
        id_matiere,
        nom_matiere,
        prix_unitaire,
        id_categorie
    )
VALUES
    (2, 'Eau (L)', 1000, 1);

INSERT INTO
    Matiere (
        id_matiere,
        nom_matiere,
        prix_unitaire,
        id_categorie
    )
VALUES
    (3, 'Huile (L)', 6000, 1);

INSERT INTO
    Matiere (
        id_matiere,
        nom_matiere,
        prix_unitaire,
        id_categorie
    )
VALUES
    (4, 'Haricots', 4000, 1);

-- Materiel (id_categorie = 2)
INSERT INTO
    Matiere (
        id_matiere,
        nom_matiere,
        prix_unitaire,
        id_categorie
    )
VALUES
    (5, 'Tôle', 25000, 2);

INSERT INTO
    Matiere (
        id_matiere,
        nom_matiere,
        prix_unitaire,
        id_categorie
    )
VALUES
    (6, 'Bâche', 15000, 2);

INSERT INTO
    Matiere (
        id_matiere,
        nom_matiere,
        prix_unitaire,
        id_categorie
    )
VALUES
    (7, 'Clous (kg)', 8000, 2);

INSERT INTO
    Matiere (
        id_matiere,
        nom_matiere,
        prix_unitaire,
        id_categorie
    )
VALUES
    (8, 'Bois', 10000, 2);

INSERT INTO
    Matiere (
        id_matiere,
        nom_matiere,
        prix_unitaire,
        id_categorie
    )
VALUES
    (9, 'groupe', 6750000, 2);

-- Argent (id_categorie = 3)
INSERT INTO
    Matiere (
        id_matiere,
        nom_matiere,
        prix_unitaire,
        id_categorie
    )
VALUES
    (10, 'Argent', 1, 3);

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(51, 1, 800, 1, '2026-02-16 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(52, 2, 1500, 1, '2026-02-15 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(53, 5, 120, 1, '2026-02-16 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(54, 6, 200, 1, '2026-02-15 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(55, 10, 12000000, 1, '2026-02-16 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(56, 9, 3, 1, '2026-02-15 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(57, 1, 500, 2, '2026-02-15 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(58, 3, 120, 2, '2026-02-16 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(59, 5, 80, 2, '2026-02-15 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(60, 7, 60, 2, '2026-02-16 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(61, 10, 6000000, 2, '2026-02-15 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(62, 1, 600, 3, '2026-02-16 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(63, 2, 1000, 3, '2026-02-15 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(64, 6, 150, 3, '2026-02-16 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(65, 8, 100, 3, '2026-02-15 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(66, 10, 8000000, 3, '2026-02-16 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(67, 1, 300, 4, '2026-02-15 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(68, 4, 200, 4, '2026-02-16 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(69, 5, 40, 4, '2026-02-15 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(70, 7, 30, 4, '2026-02-16 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(71, 10, 4000000, 4, '2026-02-15 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(72, 1, 700, 5, '2026-02-16 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(73, 2, 1200, 5, '2026-02-15 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(74, 6, 180, 5, '2026-02-16 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(75, 8, 150, 5, '2026-02-15 00:00:00');

INSERT INTO
    bngrc.Besoin (
        id_besoin,
        id_matiere,
        quantite,
        id_ville,
        date_du_demande
    )
VALUES
(76, 10, 10000000, 5, '2026-02-16 00:00:00');

-- ============================================
-- 4. BESOINS (heure = ordre en secondes)
-- ============================================
-- Ordre 1: Toamasina, Bâche
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (6, 1, 200, '2026-02-15 00:00:01');

-- Ordre 2: Nosy Be, Tôle
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (5, 4, 40, '2026-02-15 00:00:02');

-- Ordre 3: Mananjary, Argent
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (10, 2, 6000000, '2026-02-15 00:00:03');

-- Ordre 4: Toamasina, Eau
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (2, 1, 1500, '2026-02-15 00:00:04');

-- Ordre 5: Nosy Be, Riz
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (1, 4, 300, '2026-02-15 00:00:05');

-- Ordre 6: Mananjary, Tôle
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (5, 2, 80, '2026-02-15 00:00:06');

-- Ordre 7: Nosy Be, Argent
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (10, 4, 4000000, '2026-02-15 00:00:07');

-- Ordre 8: Farafangana, Bâche
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (6, 3, 150, '2026-02-16 00:00:08');

-- Ordre 9: Mananjary, Riz
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (1, 2, 500, '2026-02-15 00:00:09');

-- Ordre 10: Farafangana, Argent
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (10, 3, 8000000, '2026-02-16 00:00:10');

-- Ordre 11: Morondava, Riz
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (1, 5, 700, '2026-02-16 00:00:11');

-- Ordre 12: Toamasina, Argent
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (10, 1, 12000000, '2026-02-16 00:00:12');

-- Ordre 13: Morondava, Argent
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (10, 5, 10000000, '2026-02-16 00:00:13');

-- Ordre 14: Farafangana, Eau
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (2, 3, 1000, '2026-02-15 00:00:14');

-- Ordre 15: Morondava, Bâche
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (6, 5, 180, '2026-02-16 00:00:15');

-- Ordre 16: Toamasina, groupe
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (9, 1, 3, '2026-02-15 00:00:16');

-- Ordre 17: Toamasina, Riz
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (1, 1, 800, '2026-02-16 00:00:17');

-- Ordre 18: Nosy Be, Haricots
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (4, 4, 200, '2026-02-16 00:00:18');

-- Ordre 19: Mananjary, Clous
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (7, 2, 60, '2026-02-16 00:00:19');

-- Ordre 20: Morondava, Eau
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (2, 5, 1200, '2026-02-15 00:00:20');

-- Ordre 21: Farafangana, Riz
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (1, 3, 600, '2026-02-16 00:00:21');

-- Ordre 22: Morondava, Bois
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (8, 5, 150, '2026-02-15 00:00:22');

-- Ordre 23: Toamasina, Tôle
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (5, 1, 120, '2026-02-16 00:00:23');

-- Ordre 24: Nosy Be, Clous
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (7, 4, 30, '2026-02-16 00:00:24');

-- Ordre 25: Mananjary, Huile
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (3, 2, 120, '2026-02-16 00:00:25');

-- Ordre 26: Farafangana, Bois
INSERT INTO
    Besoin (id_matiere, id_ville, quantite, date_du_demande)
VALUES
    (8, 3, 100, '2026-02-15 00:00:26');

-- ============================================
-- 5. DONS (id_ville = 0 = non distribués)
-- ============================================
-- Argent
INSERT INTO
    Dons (id_matiere, quantite, date_don, id_ville)
VALUES
    (10, 5000000, '2026-02-16', 0);

INSERT INTO
    Dons (id_matiere, quantite, date_don, id_ville)
VALUES
    (10, 3000000, '2026-02-16', 0);

INSERT INTO
    Dons (id_matiere, quantite, date_don, id_ville)
VALUES
    (10, 4000000, '2026-02-17', 0);

INSERT INTO
    Dons (id_matiere, quantite, date_don, id_ville)
VALUES
    (10, 1500000, '2026-02-17', 0);

INSERT INTO
    Dons (id_matiere, quantite, date_don, id_ville)
VALUES
    (10, 6000000, '2026-02-17', 0);

INSERT INTO
    Dons (id_matiere, quantite, date_don, id_ville)
VALUES
    (10, 20000000, '2026-02-19', 0);

-- Nature
INSERT INTO
    Dons (id_matiere, quantite, date_don, id_ville)
VALUES
    (1, 400, '2026-02-16', 0);

INSERT INTO
    Dons (id_matiere, quantite, date_don, id_ville)
VALUES
    (2, 600, '2026-02-16', 0);

INSERT INTO
    Dons (id_matiere, quantite, date_don, id_ville)
VALUES
    (4, 100, '2026-02-17', 0);

INSERT INTO
    Dons (id_matiere, quantite, date_don, id_ville)
VALUES
    (4, 88, '2026-02-17', 0);

INSERT INTO
    Dons (id_matiere, quantite, date_don, id_ville)
VALUES
    (1, 2000, '2026-02-18', 0);

INSERT INTO
    Dons (id_matiere, quantite, date_don, id_ville)
VALUES
    (2, 5000, '2026-02-18', 0);

-- Materiel
INSERT INTO
    Dons (id_matiere, quantite, date_don, id_ville)
VALUES
    (5, 50, '2026-02-17', 0);

INSERT INTO
    Dons (id_matiere, quantite, date_don, id_ville)
VALUES
    (6, 70, '2026-02-17', 0);

INSERT INTO
    Dons (id_matiere, quantite, date_don, id_ville)
VALUES
    (5, 300, '2026-02-18', 0);

INSERT INTO
    Dons (id_matiere, quantite, date_don, id_ville)
VALUES
    (6, 500, '2026-02-19', 0);

-- ============================================
-- FIN
-- ============================================