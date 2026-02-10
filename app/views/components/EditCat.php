<?php
use Flight;

$category = $category ?? null;
if (empty($category)) {
    $id = $_GET['id_categorie'] ?? $_GET['id'] ?? null;
    if ($id) {
        $DBH = \Flight::db();
        $stmt = $DBH->prepare('SELECT * FROM Categorie WHERE id_categorie = ?');
        $stmt->execute([$id]);
        $category = $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/Cat.css">
    <title>Modifier la catégorie</title>
</head>

<body>
    <?php include(__DIR__ . '/side_nav.php'); ?>
    <div class="admin-container">
        <h1>Modifier la catégorie</h1>
        <form action="/EditCat" method="POST" class="form-group">
            <input type="hidden" name="id" value="<?= htmlspecialchars($category['id_categorie'] ?? '') ?>">
            <div class="input-wrapper">
                <label for="cat-name">Nom de la catégorie</label>
                <input type="text" id="cat-name" name="nom" required
                    value="<?= htmlspecialchars($category['nom_categorie'] ?? '') ?>">
            </div>
            <div style="margin-top:12px">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="/AdminCat" class="btn">Annuler</a>
            </div>
        </form>
    </div>
</body>

</html>