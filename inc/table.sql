CREATE DATABASE IF NOT EXISTS takalo;
USE takalo;

CREATE TABLE Categorie (
    id_categorie INT PRIMARY KEY AUTO_INCREMENT,
    nom_categorie VARCHAR(100) NOT NULL,
    date_creation DATE NOT NULL
);

CREATE TABLE Utilisateur(
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mdp_hash VARCHAR(255) NOT NULL,
    type_user VARCHAR(20) DEFAULT 'normal',
    date_creation DATE
);

CREATE TABLE Objet (
    id_objet INT PRIMARY KEY AUTO_INCREMENT,
    nom_objet VARCHAR(100) NOT NULL,
    id_categorie INT NOT NULL,
    id_user INT NOT NULL,
    date_acquisition DATE,
    prix_estime DECIMAL(10,2),
    description TEXT,
    FOREIGN KEY (id_categorie) REFERENCES Categorie(id_categorie) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES Utilisateur(id_user) ON DELETE CASCADE
);

ALTER TABLE Objet ADD description TEXT;
CREATE TABLE Image_objet (
    id_image INT PRIMARY KEY AUTO_INCREMENT,
    id_objet INT NOT NULL,
    url_image VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_objet) REFERENCES Objet(id_objet) ON DELETE CASCADE
);

ALTER TABLE Objet ADD COLUMN description TEXT;

CREATE TABLE Echange(
    id_echange INT PRIMARY KEY AUTO_INCREMENT,
    id_user_1 INT NOT NULL,
    id_user_2 INT NOT NULL,
    date_demande DATE DEFAULT (CURRENT_DATE),
    date_finalisation DATE,
    statut VARCHAR(20) DEFAULT 'en attente',
    FOREIGN KEY (id_user_1) REFERENCES Utilisateur(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_user_2) REFERENCES Utilisateur(id_user) ON DELETE CASCADE
);

CREATE TABLE Echange_fille(
    id_echange_fille INT PRIMARY KEY AUTO_INCREMENT,
    id_echange_mere INT NOT NULL,
    id_objet INT NOT NULL,
    quantite INT DEFAULT 1,
    id_proprietaire INT NOT NULL,
    FOREIGN KEY (id_echange_mere) REFERENCES Echange(id_echange) ON DELETE CASCADE,
    FOREIGN KEY (id_objet) REFERENCES Objet(id_objet) ON DELETE CASCADE,
    FOREIGN KEY (id_proprietaire) REFERENCES Utilisateur(id_user) ON DELETE CASCADE
);

ALTER TABLE Utilisateur ADD CONSTRAINT chk_type_user 
CHECK (type_user IN ('normal', 'admin'));

ALTER TABLE Echange ADD CONSTRAINT chk_statut 
CHECK (statut IN ('en attente', 'refuse', 'accepte'));

ALTER TABLE Echange_fille ADD CONSTRAINT chk_quantite 
CHECK (quantite > 0);


-- Insérer des catégories avec NOW()
INSERT INTO Categorie (nom_categorie, date_creation) VALUES
('Électronique', NOW()),
('Vêtements', NOW()),
('Livres', NOW()),
('Meubles', NOW()),
('Jouets', NOW()),
('Sports', NOW()),
('Cuisine', NOW()),
('Bijoux', NOW());

-- Insérer 10 objets simples avec NOW()
INSERT INTO Objet (nom_objet, id_categorie, id_user, date_acquisition, prix_estime) VALUES
('Téléphone portable', 1, 1, NOW(), 300.00),
('Livre de PHP', 3, 2, NOW(), 25.50),
('Chaise de bureau', 4, 3, NOW(), 80.00),
('Ballon de football', 6, 4, NOW(), 15.00),
('Casserole en inox', 7, 5, NOW(), 35.00),
('Collier en argent', 8, 1, NOW(), 45.00),
('T-shirt coton', 2, 2, NOW(), 12.00),
('Lego Classic', 5, 3, NOW(), 28.50),
('Casque audio', 1, 4, NOW(), 60.00),
('Table basse', 4, 5, NOW(), 120.00);

-- Insérer des images pour les objets
INSERT INTO Image_objet (id_objet, url_image) VALUES
(1, 'https://example.com/phone1.jpg'),
(1, 'https://example.com/phone2.jpg'),
(2, 'https://example.com/php_book.jpg'),
(3, 'https://example.com/chair1.jpg'),
(4, 'https://example.com/football.jpg'),
(5, 'https://example.com/pan1.jpg'),
(6, 'https://example.com/necklace1.jpg'),
(7, 'https://example.com/tshirt1.jpg'),
(8, 'https://example.com/lego1.jpg'),
(9, 'https://example.com/headphones1.jpg'),
(10, 'https://example.com/table1.jpg');

-- Insérer des échanges (date_demande a DEFAULT (CURRENT_DATE) donc pas besoin de NOW())
INSERT INTO Echange (id_user_1, id_user_2, date_finalisation, statut) VALUES
(1, 2, NULL, 'en attente'),
(3, 4, DATE_ADD(NOW(), INTERVAL 3 DAY), 'accepte'),
(5, 1, NULL, 'refuse'),
(2, 3, NULL, 'en attente'),
(4, 5, DATE_ADD(NOW(), INTERVAL 3 DAY), 'accepte'),
(1, 3, NULL, 'en attente'),
(2, 5, DATE_ADD(NOW(), INTERVAL 2 DAY), 'accepte'),
(3, 1, NULL, 'en attente');

-- Insérer les échanges filles (objets échangés)
INSERT INTO Echange_fille (id_echange_mere, id_objet, quantite, id_proprietaire) VALUES
(1, 1, 1, 1),
(1, 2, 1, 2),
(2, 3, 1, 3),
(2, 4, 1, 4),
(3, 5, 1, 5),
(3, 6, 1, 1),
(4, 7, 1, 2),
(4, 8, 1, 3),
(5, 9, 1, 4),
(5, 10, 1, 5),
(6, 1, 1, 1),
(6, 3, 1, 3),
(7, 2, 1, 2),
(7, 4, 1, 4),
(8, 5, 1, 5),
(8, 7, 1, 2);