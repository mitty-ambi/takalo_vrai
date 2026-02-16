<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <title>BNGRC - Accueil</title>
</head>

<body>
    <?php include("navbar.php") ?>
    <h1>Tableau recapitullant la liste des villes avec Besoins</h1>
    <table border="1px">
        <tr>
            <td>Nom Ville</td>
            <td>Region</td>
            <td>nombre de sinistres</td>
            <td>Actions</td>
        </tr>
        <?php foreach ($listeVille as $ville) { ?>
            <tr>
                <td><?= $ville['nom_ville'] ?></td>
                <td><?= $ville['nom_region'] ?></td>
                <td><?= $ville['nombres_sinistres'] ?></td>
                <td><a href="/StatsVille?id_ville=<?= $ville['id_ville'] ?>">voir les statistiques</a></td>
            </tr>
        <?php } ?>
    </table>
</body>

</html>