<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer mes objets - Takalo Vrai</title>
    <link rel="stylesheet" href="/assets/css/sidebar.css">
    <link rel="stylesheet" href="/assets/css/gerer_objets.css">

</head>

<body>
    <header class="dashboard-header">
        <div class="header-content">
            <div>
                <h1 class="header-title">Takalo Vrai</h1>
                <p class="header-subtitle">Gérer mes objets</p>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    <?= strtoupper(substr($donnees_utilisateur['prenom'] ?? 'U', 0, 1)); ?>
                </div>
                <div class="user-name">
                    <?= htmlspecialchars($donnees_utilisateur['nom'] ?? 'Utilisateur'); ?>
                </div>
            </div>
        </div>
    </header>

    <?php include __DIR__ . '/components/side_nav.php'; ?>

    <main class="dashboard-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2 class="section-title">Mes objets</h2>
            <div style="display: flex; gap: 1rem;">
                <a href="/parcourir" class="btn-add-primary">🔍 Parcourir les objets</a>
                <a href="/gerer-echanges" class="btn-add-primary">📦 Gérer les échanges</a>
                <a href="/ajouter-objet" class="btn-add-primary">+ Ajouter un objet</a>
            </div>
        </div>

        <?php if (!empty($objets) && count($objets) > 0): ?>
            <div class="objets-container">
                <?php foreach ($objets as $objet): ?>
                    <div class="objet-card">
                        <?php
                        $images = $images_objet[$objet['id_objet']] ?? [];
                        $image_src = !empty($images) ? htmlspecialchars($images[0]['url_image']) : '/assets/images/no-image.png';
                        ?>
                        <img src="<?= $image_src; ?>" alt="<?= htmlspecialchars($objet['nom_objet']); ?>" class="objet-image">

                        <div class="objet-content">
                            <div class="objet-title"><?= htmlspecialchars($objet['nom_objet']); ?></div>
                            <div class="objet-description"><?= htmlspecialchars($objet['description'] ?? ''); ?></div>
                            <div class="objet-price"><?= htmlspecialchars($objet['prix_estime']); ?> Ar</div>

                            <div class="objet-actions">
                                <a href="/editer-objet?id=<?= $objet['id_objet']; ?>" class="btn-small btn-edit">✏️ Éditer</a>
                                <a href="/supprimer-objet?id=<?= $objet['id_objet']; ?>" class="btn-small btn-delete"
                                    onclick="return confirm('Êtes-vous sûr ?');">🗑️ Supprimer</a>
                                <a href="/editer-objet?id=<?= $objet['id_objet']; ?>" class="btn-small btn-photo">📷 Photos</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <h3>Aucun objet pour le moment</h3>
                <p>Commencez par ajouter votre premier objet à échanger.</p>
                <a href="/ajouter-objet" class="btn-add-primary" style="margin-top: 1rem;">+ Ajouter un objet</a>
            </div>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/components/footer.php'; ?>
</body>

</html>