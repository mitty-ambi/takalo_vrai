<?php 
use app\models\Ville;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <title>Document</title>
</head>

<body>
    <?php include("navbar.php") ?>
    <h1>Liste des besoins de la ville : <?= $nomville  ?></h1>
    <table border="1px">
        <tr>
            <td>Matiere</td>
            <td>quantite</td>
        </tr>
        <?php foreach ($listeBesoin as $besoin) { ?>
            <tr>
                <td><?= $besoin['nom_matiere'] ?></td>
                <td><?=  $besoin['quantite'] ?></td>
            </tr>
        <?php } ?>
    </table>
    <h1>Liste des Dons de la ville : <?= $nomville  ?></h1>
    <table border="1px">
        <tr>
            <td>Matiere</td>
            <td>quantite</td>
            <td>Date de donation</td>
        </tr>
        <?php foreach ($listeDons as $dons) { ?>
            <tr>
                <td><?=  $dons['nom_matiere'] ?></td>
                <td><?=  $dons['quantite'] ?></td>
                <td><?=  $dons['date_don']  ?></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>