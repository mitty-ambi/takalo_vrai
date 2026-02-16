<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="/assets/css/gerer_besoins.css">
    <title>BNGRC - Accueil</title>
</head>

<body>
    <?php include __DIR__ . '/navbar.php'; ?>
    <h1>Tableau récapitulant la liste des villes avec Besoins</h1>

    <form method="get" action="/" class="filter-form" style="margin-bottom:12px;">
        <label for="nom_ville">Filtrer par ville :</label>
        <input class="filter-input" type="text" id="nom_ville" name="nom_ville" value="<?= htmlspecialchars($nom_ville ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <button class="filter-btn" type="submit">Filtrer</button>
        <a class="filter-reset" href="/">Réinitialiser</a>
    </form>

    <table border="1px">
        <tr>
            <th>Nom Ville</th>
            <th>Région</th>
            <th>Nombre de sinistres</th>
            <th>Actions</th>
        </tr>
        <?php if (!empty($listeVille) && is_array($listeVille)) : ?>
            <?php foreach ($listeVille as $ville) : ?>
                <tr>
                    <td><?= htmlspecialchars($ville['nom_ville'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($ville['nom_region'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int) ($ville['nombres_sinistres'] ?? 0) ?></td>
                    <td><a href="/StatsVille?id_ville=<?= urlencode($ville['id_ville'] ?? '') ?>" class="btn btn-primary">voir les statistiques</a></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">Aucune ville trouvée.</td></tr>
        <?php endif; ?>
    </table>
</body>

</html>