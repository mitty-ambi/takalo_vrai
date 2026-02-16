<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/navbar.css">
    <link rel="stylesheet" href="/assets/css/crud_dons_page.css">
    <title>Gérer les Dons - BNGRC</title>
</head>
<body>
    <?php include("navbar.php"); ?>
    
    <div class="container">
        <div class="page-header">
            <h1>📋 Gestion des Dons</h1>
            <p>Modifier ou supprimer des dons</p>
        </div>

        <?php if (isset($message_success)): ?>
            <div class="alert alert-success">
                ✅ <?= htmlspecialchars($message_success) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($message_error)): ?>
            <div class="alert alert-danger">
                ❌ <?= htmlspecialchars($message_error) ?>
            </div>
        <?php endif; ?>

        <div class="dons-grid">
            <?php if (empty($dons)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <h3>Aucun don trouvé</h3>
                    <p>Il n'y a pas de dons à gérer</p>
                </div>
            <?php else: ?>
                <?php foreach ($dons as $don): ?>
                    <div class="don-card-crud">
                        <div class="don-card-header">
                            <h3>📦 <?= htmlspecialchars($don['nom_matiere']) ?></h3>
                            <div class="don-id">ID: #<?= htmlspecialchars($don['id_don']) ?></div>
                        </div>

                        <div class="don-card-body">
                            <div class="info-row">
                                <div class="info-label">QUANTITÉ:</div>
                                <div class="info-value"><?= htmlspecialchars($don['quantite']) ?> unités</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">PRIX UNITAIRE:</div>
                                <div class="info-value"><?= number_format((float)$don['prix_unitaire'], 2, ',', ' ') ?> Ar</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">DATE DON:</div>
                                <div class="info-value"><?= htmlspecialchars($don['date_don']) ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">VILLE:</div>
                                <div class="info-value">
                                    <?php if ($don['id_ville'] == 0): ?>
                                        <span class="badge-pending">⏳ En attente</span>
                                    <?php else: ?>
                                        <span class="badge-assigned">✓ <?= htmlspecialchars($don['nom_ville'] ?? 'Ville #' . $don['id_ville']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="don-card-actions">
                            <!-- Bouton Modifier -->
                            <a href="/crud_dons/edit/<?= $don['id_don'] ?>" class="btn btn-edit">
                                ✏️ Modifier
                            </a>

                            <!-- Formulaire Supprimer -->
                            <form method="POST" action="/crud_dons/delete" class="form-delete" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce don?');">
                                <input type="hidden" name="id_don" value="<?= htmlspecialchars($don['id_don']) ?>">
                                <button type="submit" class="btn btn-delete">
                                    🗑️ Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
