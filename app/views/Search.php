<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche d'objets - Takalo Vrai</title>
    <!-- <link rel="stylesheet" href="/assets/css/sidebar.css"> -->
    <link rel="stylesheet" href="/assets/css/search.css">
</head>

<body>
    <?php include __DIR__ . '/components/side_nav.php'; ?>
    <!-- Main Content -->
    <main class="dashboard-container">
        <!-- Search Section -->
        <div class="search-section">
            <h2 class="section-title">Rechercher des objets</h2>
            
            <form action="/search/results" method="GET" class="search-form">
                <div class="search-grid">
                    <!-- Mot clé -->
                    <div class="search-field">
                        <label for="keyword" class="search-label">
                            <span class="search-icon">🔍</span>
                            Mot clé
                        </label>
                        <input type="text" 
                               id="keyword" 
                               name="keyword" 
                               class="search-input" 
                               placeholder="Téléphone, livre, chaise..."
                               value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
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
                                <option value="<?= $cat['id_categorie'] ?>" 
                                    <?= (isset($_GET['categorie']) && $_GET['categorie'] == $cat['id_categorie']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nom_categorie']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Bouton recherche -->
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
        <div class="results-section">
            <div class="results-header">
                <h3 class="results-title">
                    <?php if (isset($objets) && count($objets) > 0): ?>
                        📦 <?= count($objets) ?> objet(s) trouvé(s)
                    <?php else: ?>
                        📦 Aucun résultat
                    <?php endif; ?>
                </h3>
                
                <?php if (isset($_GET['keyword']) || isset($_GET['categorie'])): ?>
                    <a href="/search" class="btn-reset">
                        ✖ Réinitialiser
                    </a>
                <?php endif; ?>
            </div>

            <!-- Objects Grid -->
            <?php if (isset($objets) && count($objets) > 0): ?>
                <div class="objets-grid">
                    <?php foreach ($objets as $objet): ?>
                        <div class="objet-card">
                            <!-- Image -->
                            <div class="objet-image-container">
                                <?php if (isset($images_par_objet[$objet['id_objet']]) && count($images_par_objet[$objet['id_objet']]) > 0): ?>
                                    <img src="<?= htmlspecialchars($images_par_objet[$objet['id_objet']][0]['url_image']); ?>"
                                        alt="<?= htmlspecialchars($objet['nom_objet']); ?>">
                                <?php else: ?>
                                    <div class="objet-no-image">
                                        📸
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Content -->
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
                                                // Trouver le nom de la catégorie
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
                                        <span>Propriétaire: ID <?= htmlspecialchars($objet['id_user']); ?></span>
                                    </div>
                                </div>

                                <div class="objet-price">
                                    💰 <?= number_format((float) $objet['prix_estime'], 2, ',', ' '); ?> Ar
                                </div>

                                <div class="objet-actions">
                                    <a href="/objet/<?= $objet['id_objet']; ?>" class="btn btn-primary">👁️ Voir détails</a>
                                    <a href="/proposer-echange/<?= $objet['id_objet']; ?>" class="btn btn-secondary">🔄 Proposer échange</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php elseif (isset($objets)): ?>
                <!-- Empty State -->
                <div class="empty-state">
                    <div class="empty-state-icon">🔍</div>
                    <h3 class="empty-state-title">Aucun objet trouvé</h3>
                    <p class="empty-state-text">
                        Essayez avec d'autres mots-clés ou catégories
                    </p>
                    <a href="/search" class="btn btn-primary">✖ Réinitialiser la recherche</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>