

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Bienvenue dans takalo vrai <?= $user_data['prenom'];?></h1>
    <p>Vos objets : </p>
    <?php foreach($objets as $objet) {?>
        <div>
            <h2><?= $objet['nom']; ?></h2>
            <p>Description: <?= $objet['description']; ?></p>
            <p>Date d'acquisition: <?= $objet['date_acquisition']; ?></p>
            <p>Prix estimé: <?= $objet['prix_estime']; ?></p>
            <?php if (isset($images_par_objet[$objet['id']])) { ?>
                <div>
                    <?php foreach ($images_par_objet[$objet['id']] as $image) { ?>
                        <img src="<?= $image['url_image']; ?>" alt="Image de l'objet" style="width: 100px; height: 100px;">
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p>Aucune image disponible pour cet objet.</p>
            <?php } ?>
        </div>
    <?php }?>
</body>
</html>