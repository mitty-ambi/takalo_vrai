<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Éditer un objet - Takalo Vrai</title>
    <link rel="stylesheet" href="/assets/css/sidebar.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/editer_objet.css">
    
</head>
<body>
    <header class="dashboard-header">
        <div class="header-content">
            <div>
                <h1 class="header-title">Takalo Vrai</h1>
                <p class="header-subtitle">Éditer un objet</p>
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
        <?php if ($objet): ?>
            <div class="form-container">
                <h2 class="form-title">Éditer: <?= htmlspecialchars($objet['nom_objet']); ?></h2>

                <form method="POST" action="/editer-objet" enctype="multipart/form-data">
                    <input type="hidden" name="id_objet" value="<?= $objet['id_objet']; ?>">

                    <div class="form-group">
                        <label for="nom_objet" class="form-label">Nom de l'objet *</label>
                        <input type="text" id="nom_objet" name="nom_objet" class="form-input" value="<?= htmlspecialchars($objet['nom_objet']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="id_categorie" class="form-label">Catégorie *</label>
                        <select id="id_categorie" name="id_categorie" class="form-select" required>
                            <option value="">-- Sélectionnez une catégorie --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id_categorie']; ?>" <?= $cat['id_categorie'] == $objet['id_categorie'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($cat['nom_categorie']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-textarea" placeholder="Décrivez votre objet..."><?= htmlspecialchars($objet['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="prix_estime" class="form-label">Prix estimatif (Ar) *</label>
                        <input type="number" id="prix_estime" name="prix_estime" class="form-input" value="<?= htmlspecialchars($objet['prix_estime']); ?>" step="0.01" required>
                    </div>

                    <div class="images-section">
                        <h3>Photos</h3>
                        
                        <?php if (!empty($images)): ?>
                            <div>
                                <h4 style="font-size: 1rem; margin-bottom: 0.5rem;">Photos actuelles :</h4>
                                <div class="existing-images">
                                    <?php foreach ($images as $image): ?>
                                        <div class="image-item">
                                            <img src="<?= htmlspecialchars($image['url_image']); ?>" alt="Photo">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="images" class="form-label">Ajouter des photos</label>
                            <input type="file" id="images" name="images[]" class="form-input" accept="image/*" multiple>
                            <p style="font-size: 0.85rem; color: #999; margin-top: 0.5rem;">Vous pouvez ajouter plusieurs photos. Formats acceptés: JPG, PNG, GIF, etc.</p>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">✓ Enregistrer les modifications</button>
                        <button type="button" class="btn-cancel" onclick="window.location.href='/gerer-objets'">✕ Annuler</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem; background: #f9f9f9; border-radius: 8px;">
                <h3 style="color: #999;">Objet non trouvé</h3>
            </div>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/components/footer.php'; ?>
</body>
</html>



