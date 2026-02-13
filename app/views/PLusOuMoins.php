<?php
use app\models\Objet;


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/sidebar.css">
    <link rel="stylesheet" href="/assets/css/Plus.css">
    <title>Document</title>
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
        <?php if (!empty($ObjetPLusMoins) && count($ObjetPLusMoins) > 0): ?>
            <div class="objets-container">
                <?php foreach ($ObjetPLusMoins as $objet): ?>
                    <div class="objet-card">
                        <?php
                        $images = $images_objet[$objet['id_objet']] ?? [];
                        $image_src = !empty($images) ? htmlspecialchars($images[0]['url_image']) : '/assets/images/no-image.png';
                        ?>
                        <img src="<?= $image_src; ?>" alt="<?= htmlspecialchars($objet['nom_objet']); ?>" class="objet-image">

                        <div class="objet-content">
                            <div class="objet-title">
                                <?= htmlspecialchars($objet['nom_objet']); ?>
                            </div>
                            <div class="objet-description">
                                <?= htmlspecialchars($objet['description'] ?? ''); ?>
                            </div>
                            <div class="objet-price">
                                <?php
                                $pourcentage = 0;
                                if ($objet['prix_estime'] < $prixDeMonObjet) {
                                    $pourcentage = 100 - (($objet['prix_estime'] * 100) / $prixDeMonObjet);
                                    echo '<span class="objet-price-value">' . htmlspecialchars($objet['prix_estime']) . ' Ar</span>';
                                    echo '<p class="price-percentage">-' . number_format($pourcentage, 1) . '%</p>';
                                } else {
                                    $pourcentage = (($objet['prix_estime'] * 100) / $prixDeMonObjet) - 100;
                                    echo '<span class="objet-price-value">' . htmlspecialchars($objet['prix_estime']) . ' Ar</span>';
                                    echo '<p class="price-percentage positive">+' . number_format($pourcentage, 1) . '%</p>';
                                }
                                ?>
                            </div>

                        <div class="objet-actions">
                            <a href="/editer-objet?id=<?= $objet['id_objet']; ?>" class="btn-small btn-edit">✏️ Éditer</a>
                            <a href="/supprimer-objet?id=<?= $objet['id_objet']; ?>" class="btn-small btn-delete"
                                onclick="return confirm('Êtes-vous sûr ?');">🗑️ Supprimer</a>
                            <a href="/editer-objet?id=<?= $objet['id_objet']; ?>" class="btn-small btn-photo">📷 Photos</a>
                            <a href="/reduction?id=<?= $objet['id_objet']; ?>&valeur=10" class="btn-small btn-photo">+/-
                                10%</a>
                            <a href="/reduction?id=<?= $objet['id_objet']; ?>&valeur=20" class="btn-small btn-photo">+/-
                                20%</a>
                            <?php if ($objet['prix_estime'] < $prixDeMonObjet): ?>
                                <div class="reduction-tag">
                                    -<?= number_format(100 - (($objet['prix_estime'] * 100) / $prixDeMonObjet), 1) ?>%
                                </div>
                            <?php else: ?>
                                <div class="reduction-tag positive">
                                    +<?= number_format((($objet['prix_estime'] * 100) / $prixDeMonObjet) - 100, 1) ?>%
                                </div>
                            <?php endif; ?>
                            <div class="objet-actions">
                                <form action="/proposer-echange" method="post">
                                    <input type="hidden" name="id_objet_receiver" value="<?= $objet['id_objet']; ?>">
                                    <input type="hidden" name="id_objet_sender" value="<?= $_GET['id']; ?>">
                                    <button type="submit" class="btn-small btn-propose">Proposer un échange</button>
                                </form>
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

</body>

</html>