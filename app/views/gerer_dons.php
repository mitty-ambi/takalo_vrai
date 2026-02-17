<?php $base_url = rtrim(Flight::get('flight.base_url'), '/'); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/all.min.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/navbar.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/gerer_dons.css">
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
            <form action="<?= $base_url ?>/valider_dons" method="post" class="edit-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="matiere">Matière:</label>
                        <select name="matiere" id="matiere" required>
                            <option value="">-- Sélectionner une matière --</option>
                            <?php foreach ($matieres as $matiere): ?>
                                <option value="<?= htmlspecialchars($matiere['id_matiere']) ?>">
                                    <?= htmlspecialchars($matiere['nom_matiere']) ?>
                                    (<?= number_format((float) $matiere['prix_unitaire'], 2, ',', ' ') ?> Ar)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quantite">Quantité:</label>
                        <input type="number" name="quantite" id="quantite" required min="1"
                            placeholder="Entrez la quantité">
                    </div>
                </div>

                <div class="form-row">
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
    <?php include("footer.php"); ?>
</body>

</html>