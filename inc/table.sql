CREATE DATABASE IF NOT EXISTS takalo;
USE takalo;

CREATE TABLE Categorie (
    id_categorie INT PRIMARY KEY AUTO_INCREMENT,
    nom_categorie VARCHAR(100) NOT NULL
);

CREATE TABLE Utilisateur(
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mdp_hash VARCHAR(255) NOT NULL,
    type_user VARCHAR(20) DEFAULT 'normal'
);

CREATE TABLE Objet(
    id_objet INT PRIMARY KEY AUTO_INCREMENT,
    nom_objet VARCHAR(100) NOT NULL,
    id_categorie INT NOT NULL,
    id_user INT NOT NULL,
    date_acquisition DATE,
    prix_estime DECIMAL(10,2),
    FOREIGN KEY (id_categorie) REFERENCES Categorie(id_categorie) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES Utilisateur(id_user) ON DELETE CASCADE
);

CREATE TABLE Echange(
    id_echange INT PRIMARY KEY AUTO_INCREMENT,
    id_user_1 INT NOT NULL,
    id_user_2 INT NOT NULL,
    date_demande DATE DEFAULT (CURRENT_DATE),
    date_finalisation DATE,
    statut VARCHAR(20) DEFAULT 'en attente',
    FOREIGN KEY (id_user_1) REFERENCES Utilisateur(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_user_2) REFERENCES Utilisateur(id_user) ON DELETE CASCADE,
    CONSTRAINT chk_users_different CHECK (id_user_1 <> id_user_2)
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