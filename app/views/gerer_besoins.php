<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/navbar.css">
    <link rel="stylesheet" href="/assets/css/gerer_besoins.css">
    <title>Gérer les Besoins - BNGRC</title>
</head>
<body>
    <?php include("navbar.php"); ?>
    
    <div class="container">
        <div class="page-header">
            <h1>📋 Gérer les Besoins</h1>
            <p>Créez et gérez les besoins par ville et matière</p>
        </div>

        <div class="form-container">
            <h2>➕ Créer un Besoin</h2>
            <form method="POST" action="/registre" class="edit-form">
                <div class="form-group">
                    <label for="id_ville">Ville:</label>
                    <select name="id_ville" id="id_ville" required>
                        <option value="">-- Sélectionner une ville --</option>
                        <?php foreach ($villes as $ville): ?>
                            <option value="<?= htmlspecialchars($ville['id_ville']) ?>">
                                <?= htmlspecialchars($ville['nom_ville']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

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
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantite">Quantité:</label>
                    <input type="number" name="quantite" id="quantite" required min="1" placeholder="Entrez la quantité">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">✓ Créer le Besoin</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('id_categorie').addEventListener('change', function() {
            const selectedCategorie = this.value;
            const matiereSelect = document.getElementById('id_matiere');
            const options = matiereSelect.querySelectorAll('option');

            // Reset to first empty option
            options.forEach(option => {
                if (option.value === '') {
                    option.style.display = 'block';
                } else {
                    const optionCategorie = option.getAttribute('data-categorie');
                    if (selectedCategorie === '') {
                        option.style.display = 'block';
                    } else if (optionCategorie === selectedCategorie) {
                        option.style.display = 'block';
                    } else {
                        option.style.display = 'none';
                    }
                }
            });
            
            // Reset selected value
            matiereSelect.value = '';
        });
    </script>
</body>
</html>