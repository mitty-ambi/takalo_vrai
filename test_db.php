<?php
require 'vendor/autoload.php';
require 'app/config/bootstrap.php';

// Test connexion DB
$DBH = \Flight::db();

echo "=== Test Utilisateur ===\n";
$stmt = $DBH->prepare("SELECT * FROM Utilisateur");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
var_dump($users);

echo "\n=== Test Objet ===\n";
$stmt = $DBH->prepare("SELECT * FROM Objet");
$stmt->execute();
$objets = $stmt->fetchAll(PDO::FETCH_ASSOC);
var_dump($objets);

echo "\n=== Test Objet filtre id_user=1 ===\n";
$stmt = $DBH->prepare("SELECT * FROM Objet WHERE id_user = ?");
$stmt->execute([1]);
$objets_user1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
var_dump($objets_user1);
