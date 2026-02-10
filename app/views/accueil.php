<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Takalo Vrai</title>
    <link rel="stylesheet" href="/assets/css/sidebar.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
</head>
<body>
    <!-- Header -->
     
    <header class="dashboard-header">
        
        <div class="header-content">
            <div>
                <h1 class="header-title">Takalo Vrai</h1>
                <p class="header-subtitle">Bienvenue, <?= htmlspecialchars($user_data['prenom'] ?? 'Utilisateur'); ?> 👋</p>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    <?= strtoupper(substr($user_data['prenom'] ?? 'U', 0, 1)); ?>
                </div>
                <div class="user-name">
                    <?= htmlspecialchars($user_data['nom'] ?? 'Utilisateur'); ?>
                </div>
            </div>
        </div>
    </header>
    <?php include __DIR__ . '/components/side_nav.php'; ?>

    <!-- Main Content -->
    <main class="dashboard-container">
        <!-- Section Title with Action Buttons -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            <h2 class="section-title" style="margin-bottom: 0; flex: 1; min-width: 200px;">Mes Objets en Échange</h2>
            <div style="display: flex; gap: 1rem;">
                <a href="/add-object" class="btn btn-primary">➕ Ajouter un objet</a>
                <a href="/browse" class="btn btn-primary">🔍 Voir les objets des autres</a>
            </div>
        </div>

        <!-- Objects Grid -->
        <?php if (count($objets) > 0): ?>
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
                                    <span>Catégorie ID: <?= htmlspecialchars($objet['id_categorie']); ?></span>
                                </div>
                            </div>

                            <div class="objet-price">
                                💰 <?= number_format((float)$objet['prix_estime'], 2, ',', ' '); ?> Ar
                            </div>

                            <div class="objet-actions">
                                <a href="/edit-object/<?= $objet['id_objet']; ?>" class="btn btn-primary">✏️ Modifier</a>
                                <a href="/delete-object/<?= $objet['id_objet']; ?>" class="btn btn-secondary" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet objet ?');">🗑️ Supprimer</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-state-icon">📦</div>
                <h3 class="empty-state-title">Aucun objet ajouté</h3>
                <p class="empty-state-text">Commencez à ajouter vos objets pour les échanger avec la communauté</p>
                <button class="btn btn-primary">➕ Ajouter un objet</button>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="dashboard-footer">
        <p>© 2026 Takalo Vrai — Fait avec ❤️ par la communauté</p>
    </footer>
</body>
</html>