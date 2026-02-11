<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un objet - Takalo Vrai</title>
    <link rel="stylesheet" href="/assets/css/sidebar.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/ajouter_objet.css">
</head>
<body>
    <header class="dashboard-header">
        <div class="header-content">
            <div>
                <h1 class="header-title">Takalo Vrai</h1>
                <p class="header-subtitle">Ajouter un nouvel objet</p>
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
        <div class="conteneur-formulaire">
            <h2 class="titre-formulaire">Ajouter un nouvel objet</h2>

            <form method="POST" action="/ajouter-objet" enctype="multipart/form-data">
                <div class="groupe-formulaire">
                    <label for="nom_objet" class="label-formulaire">Nom de l'objet *</label>
                    <input 
                        type="text" 
                        id="nom_objet" 
                        name="nom_objet" 
                        class="entree-formulaire" 
                        placeholder="Ex: Bicyclette, Montre, Tableau..." 
                        required>
                </div>

                <div class="groupe-formulaire">
                    <label for="id_categorie" class="label-formulaire">Catégorie *</label>
                    <select id="id_categorie" name="id_categorie" class="select-formulaire" required>
                        <option value="">-- Sélectionnez une catégorie --</option>
                        <?php foreach ($categories as $categorie): ?>
                            <option value="<?= $categorie['id_categorie']; ?>">
                                <?= htmlspecialchars($categorie['nom_categorie']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="groupe-formulaire">
                    <label for="description" class="label-formulaire">Description</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        class="zone-texte-formulaire" 
                        placeholder="Décrivez votre objet : état, caractéristiques, histoire..."></textarea>
                </div>

                <div class="groupe-formulaire">
                    <label for="prix_estime" class="label-formulaire">Prix estimatif (Ar) *</label>
                    <input 
                        type="number" 
                        id="prix_estime" 
                        name="prix_estime" 
                        class="entree-formulaire" 
                        placeholder="0.00" 
                        step="0.01" 
                        required>
                </div>

                <div class="groupe-formulaire">
                    <label for="date_acquisition" class="label-formulaire">Date d'acquisition</label>
                    <input 
                        type="date" 
                        id="date_acquisition" 
                        name="date_acquisition" 
                        class="entree-formulaire">
                </div>

                <div class="section-images">
                    <h3>Photos de l'objet</h3>
                    <p class="info-fichier">Téléchargez une ou plusieurs photos de votre objet. Formats acceptés : JPG, PNG, GIF.</p>
                    
                    <div class="groupe-formulaire">
                        <label for="images" class="label-formulaire">Ajouter des photos</label>
                        <input 
                            type="file" 
                            id="images" 
                            name="images[]" 
                            class="entree-formulaire" 
                            accept="image/*" 
                            multiple>
                    </div>
                </div>

                <div class="actions-formulaire">
                    <button type="submit" class="bouton-envoyer">✓ Ajouter l'objet</button>
                    <button type="button" class="bouton-annuler" onclick="window.location.href='/gerer-objets'">✕ Annuler</button>
                </div>
            </form>
        </div>
    </main>

    <?php include __DIR__ . '/components/footer.php'; ?>
</body>
</html>

