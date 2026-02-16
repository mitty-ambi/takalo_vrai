<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispatch des Dons - BNGRC</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/dispatch.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 Dispatch des Dons</h1>
            <p>Gestion et distribution des dons reçus</p>
        </div>

        <?php if (!empty($dons_non_distribuees)): ?>
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?= count($dons_non_distribuees) ?></div>
                    <div class="stat-label">Dons en attente</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= array_sum(array_column($dons_non_distribuees, 'quantite')) ?></div>
                    <div class="stat-label">Quantité totale</div>
                </div>
            </div>
        <?php endif; ?>

        <h2>Dons à Distribuer</h2>
        
        <?php if (empty($dons_non_distribuees)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <h3>Aucun don en attente</h3>
                <p>Tous les dons ont déjà été distribués!</p>
            </div>
        <?php else: ?>
            <?php foreach ($dons_non_distribuees as $don): ?>
                <div class="don-card">
                    <div class="don-header">
                        <div class="don-titre">
                            📦 <?= htmlspecialchars($don['nom_matiere']) ?>
                        </div>
                        <div class="don-date">
                            📅 <?= htmlspecialchars($don['date_don']) ?>
                        </div>
                    </div>

                    <div class="don-details">
                        <div class="detail-item">
                            <div class="detail-label">QUANTITÉ</div>
                            <div class="detail-value"><?= htmlspecialchars($don['quantite']) ?> unités</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">PRIX UNITAIRE</div>
                            <div class="detail-value"><?= number_format((float)$don['prix_unitaire'], 2, ',', ' ') ?> Ar</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">VALEUR TOTALE</div>
                            <div class="detail-value"><?= number_format((float)$don['prix_unitaire'] * $don['quantite'], 2, ',', ' ') ?> Ar</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">STATUT</div>
                            <div class="detail-value">
                                <span style="background: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px; font-size: 0.85em;">
                                    ⏳ En attente
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire inline pour assigner la ville -->
                    <form method="POST" action="/update-dons" class="don-form">
                        <input type="hidden" name="id_don" value="<?= htmlspecialchars($don['id_don']) ?>">
                        
                        <div class="don-form-group">
                            <label for="ville_<?= $don['id_don'] ?>">Assigner à une ville:</label>
                            <div class="don-form-row">
                                <select name="id_ville" id="ville_<?= $don['id_don'] ?>" required class="don-select">
                                    <option value="">-- Choisir une ville --</option>
                                    <?php foreach ($villes as $ville): ?>
                                        <option value="<?= htmlspecialchars($ville['id_ville']) ?>">
                                            <?= htmlspecialchars($ville['nom_ville']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-success btn-small">✓ Assigner</button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>