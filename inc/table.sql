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
    prix_unitaire float
);
create table Besoin(
    id_besoin int primary key auto_increment,
    id_matiere int,
    quantite int,
    id_ville int
)