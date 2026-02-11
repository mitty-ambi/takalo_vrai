<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche d'objets - Takalo Vrai</title>
    <!-- Bootstrap local -->
    <link rel="stylesheet" href="/assets/vendor/bootstrap/css/bootstrap.min.css">
    <style>
        /* Petites adaptations pour garder une bonne lisibilité */
        .search-section { padding: 1.25rem 0; }
        .objet-no-image {
            height: 180px;
            display:flex;
            align-items:center;
            justify-content:center;
            background:#f8f9fa;
            border:1px dashed #dee2e6;
            color:#6c757d;
            font-size:2rem;
        }
        .objet-image {
            object-fit:cover;
            height:180px;
            width:100%;
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/components/side_nav.php'; ?>

    <main class="container py-4">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-3">Rechercher des objets</h2>
            </div>
        </div>

        <form action="/search/results" method="GET" class="mb-4">
            <div class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="keyword" class="form-label">Mot clé</label>
                    <div class="input-group">
                        <span class="input-group-text">🔍</span>
                        <input type="text" id="keyword" name="keyword" class="form-control"
                               placeholder="Téléphone, livre, chaise..."
                               value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="categorie" class="form-label">Catégorie</label>
                    <select name="categorie" id="categorie" class="form-select">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($listeCat as $cat): ?>
                            <option value="<?= $cat['id_categorie'] ?>"
                                <?= (isset($_GET['categorie']) && $_GET['categorie'] == $cat['id_categorie']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nom_categorie']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary w-100">
                        🔎 Rechercher
                    </button>
                </div>
            </div>
        </form>

        <div class="row mb-3 align-items-center">
            <div class="col">
                <?php if (isset($objets) && count($objets) > 0): ?>
                    <h5 class="m-0">📦 <?= count($objets) ?> objet(s) trouvé(s)</h5>
                <?php else: ?>
                    <h5 class="m-0">📦 Aucun résultat</h5>
                <?php endif; ?>
            </div>
            <div class="col-auto">
                <?php if (isset($_GET['keyword']) || isset($_GET['categorie'])): ?>
                    <a href="/search" class="btn btn-outline-secondary btn-sm">✖ Réinitialiser</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (isset($objets) && count($objets) > 0): ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                <?php foreach ($objets as $objet): ?>
                    <div class="col">
                        <div class="card h-100">
                            <?php if (isset($images_par_objet[$objet['id_objet']]) && count($images_par_objet[$objet['id_objet']]) > 0): ?>
                                <img src="<?= htmlspecialchars($images_par_objet[$objet['id_objet']][0]['url_image']); ?>"
                                     class="card-img-top objet-image" alt="<?= htmlspecialchars($objet['nom_objet']); ?>">
                            <?php else: ?>
                                <div class="objet-no-image"></div>
                            <?php endif; ?>

                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title mb-1"><?= htmlspecialchars($objet['nom_objet']); ?></h6>

                                <div class="mb-2 text-muted small">
                                    <div>📅 <?= htmlspecialchars($objet['date_acquisition']); ?></div>
                                    <div>
                                        🏷️
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
                                    </div>
                                    <div>👤 Propriétaire: ID <?= htmlspecialchars($objet['id_user']); ?></div>
                                </div>

                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <div class="fw-bold text-success">
                                        💰 <?= number_format((float)$objet['prix_estime'], 2, ',', ' '); ?> Ar
                                    </div>

                                    <div class="btn-group">
                                        <a href="/objet/<?= $objet['id_objet']; ?>" class="btn btn-sm btn-outline-primary">👁️ Détails</a>
                                        <a href="/proposer-echange/<?= $objet['id_objet']; ?>" class="btn btn-sm btn-outline-secondary">🔄 Proposer</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif (isset($objets)): ?>
            <div class="row">
                <div class="col-12 text-center py-5">
                    <div class="mb-3" style="font-size:3rem;">🔍</div>
                    <h4>Aucun objet trouvé</h4>
                    <p class="text-muted">Essayez avec d'autres mots-clés ou catégories.</p>
                    <a href="/search" class="btn btn-primary">✖ Réinitialiser la recherche</a>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>