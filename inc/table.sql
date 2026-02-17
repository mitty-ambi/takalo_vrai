create database bngrc;
use bngrc;
create table Region(
    id_region int primary key auto_increment,
    nom_region varchar(100) not null
);
create table Ville(
    id_ville int primary key auto_increment,
    nom_ville varchar(100) not null,
    id_region int,
    nombres_sinistres int default 0
);
create table Dons(
    id_don int primary key auto_increment,
    id_matiere int,
    quantite int,
    date_don date,
    id_ville int
);
create table Matiere(
    id_matiere int primary key auto_increment,
    nom_matiere varchar(100),
    prix_unitaire float,
    id_categorie INT
);

create table Besoin(
    id_besoin int primary key auto_increment,
    id_matiere int,
    quantite int,
    id_ville int,
    date_du_demande datetime default current_timestamp
);
create table Achats(
    id_achat int primary key auto_increment,
    id_besoin int,
    id_ville int,
    id_matiere int,
    quantite int,
    prix_unitaire float,
    frais_pourcentage float,
    prix_total_achat float,
    date_achat datetime default current_timestamp,
    statut varchar(50) default 'en_attente'
);

CREATE TABLE Categorie(
    id_categorie INT primary key auto_increment,
    nom varchar(100),
    date_creation DATE
);
INSERT INTO Categorie
(id_categorie, nom)
VALUES(1, 'Électronique');
INSERT INTO Categorie
(id_categorie, nom)
VALUES(3, 'Livres');
INSERT INTO Categorie
(id_categorie, nom)
VALUES(4, 'Meubles');
INSERT INTO Categorie
(id_categorie, nom)
VALUES(5, 'Jouets');
INSERT INTO Categorie
(id_categorie, nom)
VALUES(6, 'Sports');
INSERT INTO Categorie
(id_categorie, nom)
VALUES(7, 'Cuisine');
INSERT INTO Categorie
(id_categorie, nom)
VALUES(8, 'Bijoux');

-- 1. Insertion des régions
INSERT INTO Region (nom_region) VALUES
('Analamanga'),
('Atsinanana'),
('Haute Matsiatra'),
('Boeny'),
('Atsimo Andrefana');

-- 2. Insertion des villes
INSERT INTO Ville (nom_ville, id_region, nombres_sinistres) VALUES
('Antananarivo', 1, 2500),
('Toamasina', 2, 1800),
('Fianarantsoa', 3, 1200),
('Mahajanga', 4, 900),
('Toliara', 5, 1500),
('Antsirabe', 1, 800),
('Morondava', 5, 600);

-- 3. Insertion des matières (articles)
INSERT INTO Matiere (nom_matiere, prix_unitaire) VALUES
('Riz', 2500),
('Huile', 5000),
('Tôles', 15000),
('Clous', 500),
('Ciment', 12000),
('Eau (bouteille)', 2000),
('Couvertures', 8000),
('Médicaments', 10000),
('Lait', 3000),
('Savon', 1000);

-- 4. Insertion des besoins
INSERT INTO Besoin (id_matiere, quantite, id_ville) VALUES
-- Antananarivo
(1, 1000, 1),  -- Riz
(2, 500, 1),   -- Huile
(3, 300, 1),   -- Tôles
(7, 200, 1),   -- Couvertures

-- Toamasina
(1, 800, 2),   -- Riz
(4, 1000, 2),  -- Clous
(6, 500, 2),   -- Eau
(8, 100, 2),   -- Médicaments

-- Fianarantsoa
(1, 600, 3),   -- Riz
(5, 200, 3),   -- Ciment
(9, 300, 3),   -- Lait

-- Mahajanga
(2, 400, 4),   -- Huile
(3, 150, 4),   -- Tôles
(10, 500, 4),  -- Savon

-- Toliara
(1, 700, 5),   -- Riz
(6, 600, 5),   -- Eau
(7, 100, 5);   -- Couvertures

-- 5. Insertion des dons
INSERT INTO Dons (id_matiere, quantite, date_don, id_ville) VALUES
-- Dons pour Antananarivo
(1, 400, '2024-01-10', 1),  -- Riz
(2, 200, '2024-01-12', 1),  -- Huile
(3, 100, '2024-01-15', 1),  -- Tôles

-- Dons pour Toamasina
(1, 300, '2024-01-11', 2),  -- Riz
(4, 500, '2024-01-13', 2),  -- Clous
(6, 200, '2024-01-14', 2),  -- Eau

-- Dons pour Fianarantsoa
(1, 200, '2024-01-12', 3),  -- Riz
(5, 50, '2024-01-16', 3),   -- Ciment

-- Dons pour Mahajanga
(2, 150, '2024-01-14', 4),  -- Huile
(10, 200, '2024-01-15', 4), -- Savon

-- Dons pour Toliara
(1, 250, '2024-01-13', 5),  -- Riz
(6, 200, '2024-01-17', 5);  -- Eau

-- 6. Vérification des données
SELECT 'Régions' AS Table_name, COUNT(*) AS Total FROM Region
UNION ALL
SELECT 'Villes', COUNT(*) FROM Ville
UNION ALL
SELECT 'Matières', COUNT(*) FROM Matiere
UNION ALL
SELECT 'Besoins', COUNT(*) FROM Besoin
UNION ALL
SELECT 'Dons', COUNT(*) FROM Dons;

-- 7. Requête exemple : Besoins par ville avec détails
SELECT 
    v.nom_ville,
    r.nom_region,
    m.nom_matiere,
    b.quantite AS besoin_quantite,
    m.prix_unitaire,
    (b.quantite * m.prix_unitaire) AS montant_total_besoin
FROM Besoin b
JOIN Ville v ON b.id_ville = v.id_ville
JOIN Region r ON v.id_region = r.id_region
JOIN Matiere m ON b.id_matiere = m.id_matiere
ORDER BY v.nom_ville, m.nom_matiere;

-- 8. Requête exemple : Dons par ville
SELECT 
    v.nom_ville,
    m.nom_matiere,
    d.quantite AS don_quantite,
    d.date_don,
    (d.quantite * m.prix_unitaire) AS valeur_don
FROM Dons d
JOIN Ville v ON d.id_ville = v.id_ville
JOIN Matiere m ON d.id_matiere = m.id_matiere
ORDER BY d.date_don DESC;

-- 9. Vue pratique pour le dashboard
CREATE VIEW vue_dashboard_ville AS
SELECT 
    v.id_ville,
    v.nom_ville,
    r.nom_region,
    v.nombres_sinistres,
    COUNT(DISTINCT b.id_besoin) AS total_besoins,
    COUNT(DISTINCT d.id_don) AS total_dons,
    COALESCE(SUM(b.quantite * m.prix_unitaire), 0) AS montant_besoins,
    COALESCE(SUM(d.quantite * m2.prix_unitaire), 0) AS montant_dons
FROM Ville v
LEFT JOIN Region r ON v.id_region = r.id_region
LEFT JOIN Besoin b ON v.id_ville = b.id_ville
LEFT JOIN Matiere m ON b.id_matiere = m.id_matiere
LEFT JOIN Dons d ON v.id_ville = d.id_ville
LEFT JOIN Matiere m2 ON d.id_matiere = m2.id_matiere
GROUP BY v.id_ville;

-- Tester la vue
SELECT * FROM vue_dashboard_ville;
ALTER table Besoin ADD column date_du_demande datetime default current_timestamp;