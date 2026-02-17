<?php $base_url = rtrim(Flight::get('flight.base_url'), '/'); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/all.min.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/navbar.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/edit_don_page.css">
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
                <form method="POST" action="<?= $base_url ?>/crud_dons/update" class="edit-form">
                    <input type="hidden" name="id_don" value="<?= htmlspecialchars($don['id_don']) ?>">

                    <div class="form-group">
                        <label for="id_matiere">Matière:</label>
                        <select name="id_matiere" id="id_matiere" required class="form-control">
                            <option value="">-- Sélectionner une matière --</option>
                            <?php foreach ($matieres as $matiere): ?>
                                <option value="<?= htmlspecialchars($matiere['id_matiere']) ?>"
                                    <?= $matiere['id_matiere'] == $don['id_matiere'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($matiere['nom_matiere']) ?>
                                    (<?= number_format((float) $matiere['prix_unitaire'], 2, ',', ' ') ?> Ar)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quantite">Quantité:</label>
                        <input type="number" name="quantite" id="quantite" value="<?= htmlspecialchars($don['quantite']) ?>"
                            required min="1" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="date_don">Date du Don:</label>
                        <input type="date" name="date_don" id="date_don" value="<?= htmlspecialchars($don['date_don']) ?>"
                            required class="form-control">
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
                        <a href="<?= $base_url ?>/crud_dons" class="btn btn-secondary">← Annuler</a>
                        <button type="submit" class="btn btn-primary">✓ Enregistrer</button>
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                Don non trouvé
            </div>
            <a href="<?= $base_url ?>/crud_dons" class="btn btn-secondary">← Retour</a>
        <?php endif; ?>
    </div>
    <?php include("footer.php") ?>

</body>

</html>