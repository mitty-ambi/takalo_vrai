<?php /* simple view */ ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Historique de l'objet</title>
    <link rel="stylesheet" href="/assets/vendor/bootstrap/css/bootstrap.min.css">
</head>
<body>
<div class="container py-4">
    <a href="/objet/<?= htmlspecialchars($id_objet ?? '') ?>" class="btn btn-link">&larr; Retour</a>

    <h3 class="mb-3">Historique des propriétaires — Objet #<?= htmlspecialchars($id_objet ?? '') ?></h3>

    <?php if (!empty($history) && is_array($history)): ?>
        <ul class="list-group">
            <?php foreach ($history as $h): ?>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div>
                        <strong><?= htmlspecialchars(($h['prenom'] ?? '') . ' ' . ($h['nom'] ?? '')) ?></strong>
                        <div class="small text-muted">ID propriétaire: <?= htmlspecialchars($h['id_proprietaire'] ?? '') ?></div>
                    </div>
                    <div class="text-end small text-secondary">
                        <?= htmlspecialchars($h['date_echange'] ?? '') ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <div class="alert alert-secondary">Aucun historique disponible pour cet objet.</div>
    <?php endif; ?>
</div>

<script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>