<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="/valider_dons" method="post">
        <p>Matiere</p>
        <select name="matiere">
            <?php foreach ($matieres as $matiere): ?>
                <option value="<?= htmlspecialchars($matiere['id_matiere']) ?>">
                    <?= htmlspecialchars($matiere['nom_matiere']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p>Quantite : <input type="number" name="quantite"></p>
        <p>Date_don : <input type="date" name="date_don"></p>
        <input type="submit" value="Valider le don">
    </form>
</body>
</html>