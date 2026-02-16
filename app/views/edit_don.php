<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/navbar.css">
    <link rel="stylesheet" href="/assets/css/edit_don_page.css">
    <title>Éditer un Don - BNGRC</title>
</head>
<body>
    <?php include("navbar.php"); ?>
    
    <div class="container">
        <div class="page-header">
            <h1>✏️ Éditer un Don</h1>
            <p>Modifiez les informations du don</p>
        </div>

        <?php if (isset($message_error)): ?>
            <div class="alert alert-danger">
                ❌ <?= htmlspecialchars($message_error) ?>
            </div>
        <?php endif; ?>

        <?php if ($don): ?>
            <div class="form-container">
                <form method="POST" action="/crud_dons/update" class="edit-form">
                    <input type="hidden" name="id_don" value="<?= htmlspecialchars($don['id_don']) ?>">

                    <div class="form-group">
                        <label for="id_categorie">Catégorie:</label>
                        <select name="id_categorie" id="id_categorie" required class="form-control">
                            <option value="">-- Sélectionner une catégorie --</option>
                            <?php foreach ($categories as $categorie): ?>
                                <option value="<?= htmlspecialchars($categorie['id_categorie']) ?>"
                                    <?php 
                                        // Find the category of the current matiere
                                        $current_categorie = null;
                                        foreach ($matieres as $m) {
                                            if ($m['id_matiere'] == $don['id_matiere']) {
                                                $current_categorie = $m['id_categorie'];
                                                break;
                                            }
                                        }
                                        echo $categorie['id_categorie'] == $current_categorie ? 'selected' : '';
                                    ?>>
                                    <?= htmlspecialchars($categorie['nom_categorie']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_matiere">Matière:</label>
                        <select name="id_matiere" id="id_matiere" required class="form-control">
                            <option value="">-- Sélectionner une matière --</option>
                            <?php foreach ($matieres as $matiere): ?>
                                <option value="<?= htmlspecialchars($matiere['id_matiere']) ?>" 
                                    data-categorie="<?= htmlspecialchars($matiere['id_categorie']) ?>"
                                    <?= $matiere['id_matiere'] == $don['id_matiere'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($matiere['nom_matiere']) ?> (<?= number_format((float)$matiere['prix_unitaire'], 2, ',', ' ') ?> Ar)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quantite">Quantité:</label>
                        <input type="number" name="quantite" id="quantite" value="<?= htmlspecialchars($don['quantite']) ?>" required min="1" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="date_don">Date du Don:</label>
                        <input type="date" name="date_don" id="date_don" value="<?= htmlspecialchars($don['date_don']) ?>" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="id_ville">Ville:</label>
                        <select name="id_ville" id="id_ville" class="form-control">
                            <option value="0" <?= $don['id_ville'] == 0 ? 'selected' : '' ?>>-- Non assigné --</option>
                            <?php foreach ($villes as $ville): ?>
                                <option value="<?= htmlspecialchars($ville['id_ville']) ?>" 
                                    <?= $ville['id_ville'] == $don['id_ville'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ville['nom_ville']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-actions">
                        <a href="/crud_dons" class="btn btn-secondary">← Annuler</a>
                        <button type="submit" class="btn btn-primary">✓ Enregistrer</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                Don non trouvé
            </div>
            <a href="/crud_dons" class="btn btn-secondary">← Retour</a>
        <?php endif; ?>
    </div>

    <script>
        document.getElementById('id_categorie').addEventListener('change', function() {
            const selectedCategory = this.value;
            const matiereSelect = document.getElementById('id_matiere');
            const options = matiereSelect.querySelectorAll('option');
            
            // Reset the matiere selection
            matiereSelect.value = '';
            
            // Show/hide options based on category
            options.forEach(option => {
                if (option.value === '') {
                    option.style.display = 'block';
                } else {
                    const optionCategory = option.getAttribute('data-categorie');
                    if (selectedCategory === '' || optionCategory === selectedCategory) {
                        option.style.display = 'block';
                    } else {
                        option.style.display = 'none';
                    }
                }
            });
        });
    </script>
</body>
</html>
