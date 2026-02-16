<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php include("navbar.php");?>
    <h1>Creer un besoin</h1>
<form action="/registre" method="post">
        <p>Matiere</p>
        <select name="matiere">
            <?php foreach($matieres as $matiere): ?>
                <option value="<?php echo $matiere['id_matiere']; ?>">
                    <?php echo $matiere['nom_matiere']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p>quantite</p><input type="number" name="quantite" id="quantite">
        <p>ville</p>
        <select name="ville">
            <?php foreach($villes as $v): ?>
                <option value="<?php echo $v['id_ville']; ?>">
                    <?php echo $v['nom_ville']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    <input type="submit" value="valider">
    </form>
</body>
</html>
