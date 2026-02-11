<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Objets des autres utilisateurs - Takalo Vrai</title>
    <link rel="stylesheet" href="/assets/css/sidebar.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/parcourir_objets.css">
    
</head>
<body>
    <header class="dashboard-header">
        <div class="header-content">
            <div>
                <h1 class="header-title">Takalo Vrai</h1>
                <p class="header-subtitle">Découvrez les objets disponibles</p>
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
        <h2 class="section-title">Objets disponibles</h2>
        
        <div class="filters">
            <input type="text" class="filter-input" id="searchInput" placeholder="🔍 Rechercher un objet..." style="flex: 1; min-width: 200px;">
        </div>

        <?php if (!empty($objets) && count($objets) > 0): ?>
            <div class="objets-grid" id="objetsGrid">
                <?php foreach ($objets as $objet): ?>
                    <div class="objet-card" data-name="<?= htmlspecialchars($objet['nom_objet']); ?>">
                        <a href="/detail-objet?id=<?= $objet['id_objet']; ?>">
                            <?php 
                            $images = $images_objet[$objet['id_objet']] ?? [];
                            $image_src = !empty($images) ? htmlspecialchars($images[0]['url_image']) : '/assets/images/no-image.png';
                            ?>
                            <img src="<?= $image_src; ?>" alt="<?= htmlspecialchars($objet['nom_objet']); ?>" class="objet-image">
                            
                            <div class="objet-content">
                                <div class="objet-title"><?= htmlspecialchars($objet['nom_objet']); ?></div>
                                <div class="objet-description"><?= htmlspecialchars(substr($objet['description'] ?? '', 0, 100)); ?></div>
                                
                                <div class="objet-info">
                                    <span class="objet-price"><?= htmlspecialchars($objet['prix_estime']); ?> Ar</span>
                                    <span class="objet-owner"><?= htmlspecialchars($user_names[$objet['id_user']]['prenom'] ?? ''); ?></span>
                                </div>
                            </div>
                        </a>
                        <button class="btn-view" onclick="window.location.href='/detail-objet?id=<?= $objet['id_objet']; ?>'">Voir détails</button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="objets-grid">
                <div class="empty-state">
                    <h3>Aucun objet disponible</h3>
                    <p>Il n'y a pas encore d'objets à échanger.</p>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/components/footer.php'; ?>

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const cards = document.querySelectorAll('.objet-card');
            
            cards.forEach(card => {
                const name = card.getAttribute('data-name').toLowerCase();
                if (name.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>



