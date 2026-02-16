<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/navbar.css">
    <link rel="stylesheet" href="/assets/css/gerer_dons.css">
    <title>Créer un Don - BNGRC</title>
</head>
<body>
    <?php include("navbar.php"); ?>
    
    <div class="container">
        <div class="page-header">
            <h1>📦 Créer un Don</h1>
            <p>Ajoutez un nouveau don au système</p>
        </div>

        <div class="form-container">
            <h2>➕ Ajouter un Don</h2>
            <form action="/valider_dons" method="post" class="edit-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="id_categorie">Catégorie:</label>
                        <select name="id_categorie" id="id_categorie" required>
                            <option value="">-- Sélectionner une catégorie --</option>
                            <?php foreach ($categories as $categorie): ?>
                                <option value="<?= htmlspecialchars($categorie['id_categorie']) ?>">
                                    <?= htmlspecialchars($categorie['nom_categorie']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_matiere">Matière:</label>
                        <select name="id_matiere" id="id_matiere" required>
                            <option value="">-- Sélectionner une matière --</option>
                            <?php foreach ($matieres as $matiere): ?>
                                <option value="<?= htmlspecialchars($matiere['id_matiere']) ?>" data-categorie="<?= htmlspecialchars($matiere['id_categorie']) ?>">
                                    <?= htmlspecialchars($matiere['nom_matiere']) ?> 
                                    (<?= number_format((float)$matiere['prix_unitaire'], 2, ',', ' ') ?> Ar)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="quantite">Quantité:</label>
                        <input type="number" name="quantite" id="quantite" required min="1" placeholder="Entrez la quantité">
                    </div>

                    <div class="form-group">
                        <label for="date_don">Date du Don:</label>
                        <input type="date" name="date_don" id="date_don" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">✓ Valider le Don</button>
                </div>
            </form>
        </div>
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