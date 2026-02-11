<?php
header_remove('Content-Security-Policy');
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche d'objets - Takalo Vrai</title>
    <link rel="stylesheet" href="/assets/css/search.css">
</head>

<body>
    <?php include __DIR__ . '/components/side_nav.php'; ?>

    <main class="dashboard-container">
        <!-- Search Section -->
        <div class="search-section">
            <h2 class="section-title">Rechercher des objets</h2>

            <form action="/search/results" method="POST" class="search-form">
                <div class="search-grid">
                    <!-- Mot clé -->
                    <div class="search-field">
                        <label for="keyword" class="search-label">
                            <span class="search-icon">🔍</span>
                            Mot clé
                        </label>
                        <input type="text" id="keyword" name="keyword" class="search-input"
                            placeholder="Téléphone, livre, chaise..."
                            value="<?= htmlspecialchars($_POST['keyword'] ?? '') ?>">
                    </div>

                    <!-- Catégorie -->
                    <div class="search-field">
                        <label for="categorie" class="search-label">
                            <span class="search-icon">📂</span>
                            Catégorie
                        </label>
                        <select name="categorie" id="categorie" class="search-select">
                            <option value="">Toutes les catégories</option>
                            <?php foreach ($listeCat as $cat): ?>
                                <option value="<?= $cat['id_categorie'] ?>" <?= (isset($_POST['categorie']) && $_POST['categorie'] == $cat['id_categorie']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nom_categorie']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="search-field search-button-field">
                        <button type="submit" class="btn btn-primary btn-search">
                            <span class="search-icon">🔎</span>
                            Rechercher
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Results Section -->
        <?php if (isset($objets)): ?>
            <div class="results-section">
                <div class="results-header">
                    <h3 class="results-title">
                        <?php if (count($objets) > 0): ?>
                            📦 <?= count($objets) ?> objet(s) trouvé(s)
                        <?php else: ?>
                            📦 Aucun résultat
                        <?php endif; ?>
                    </h3>

                    <?php if (!empty($_POST['keyword']) || !empty($_POST['categorie'])): ?>
                        <a href="/search" class="btn-reset">
                            ✖ Réinitialiser
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (count($objets) > 0): ?>
                    <div class="objets-grid">
                        <?php foreach ($objets as $objet): ?>
                            <div class="objet-card">
                                <?php
                                $images = $images_objet[$objet['id_objet']] ?? [];
                                $image_src = !empty($images) ? htmlspecialchars($images[0]['url_image']) : '/assets/images/no-image.png';
                                ?>
                                <img src="<?= $image_src; ?>" alt="<?= htmlspecialchars($objet['nom_objet']); ?>"
                                    class="objet-image">

                                <div class="objet-content">
                                    <h3 class="objet-title">
                                        <?= htmlspecialchars($objet['nom_objet']); ?>
                                    </h3>

                                    <div class="objet-meta">
                                        <div class="objet-meta-item">
                                            <span class="objet-meta-icon">📅</span>
                                            <span><?= htmlspecialchars($objet['date_acquisition']); ?></span>
                                        </div>
                                        <div class="objet-meta-item">
                                            <span class="objet-meta-icon">🏷️</span>
                                            <span>
                                                <?php
                                                $catName = '';
                                                foreach ($listeCat as $cat) {
                                                    if ($cat['id_categorie'] == $objet['id_categorie']) {
                                                        $catName = $cat['nom_categorie'];
                                                        break;
                                                    }
                                                }
                                                echo htmlspecialchars($catName);
                                                ?>
                                            </span>
                                        </div>
                                        <div class="objet-meta-item">
                                            <span class="objet-meta-icon">👤</span>
                                            <span><?= htmlspecialchars($objet['nom_proprietaire'] ?? 'Propriétaire ID: ' . $objet['id_user']); ?></span>
                                        </div>
                                    </div>

                                    <div class="objet-price">
                                        💰 <?= number_format((float) $objet['prix_estime'], 0, ',', ' '); ?> Ar
                                    </div>
                                    <div class="objet-actions">
                                        <a href="/detail-objet?id=<?= $objet['id_objet']; ?>" class="btn btn-primary">👁️ Voir</a>
                                        <a href="/proposer-echange/<?= $objet['id_objet']; ?>" class="btn btn-secondary">🔄
                                            Proposer</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">🔍</div>
                        <h3 class="empty-state-title">Aucun objet trouvé</h3>
                        <p class="empty-state-text">
                            Essayez avec d'autres mots-clés ou catégories
                        </p>
                        <a href="/search" class="btn btn-primary">✖ Réinitialiser</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
</body>

</html>