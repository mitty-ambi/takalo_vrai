<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Historique - Takalo Vrai</title>
    <link rel="stylesheet" href="/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/object_history.css">
</head>
<body>

<div class="container py-4">
    <header class="row align-items-center gy-3">
        <div class="col-6 col-md-4">
            <a href="/gerer-objets" class="btn btn-back btn-sm px-3">
                &larr; Retour
            </a>
        </div>
        <div class="col-12 col-md-4 text-md-center order-3 order-md-2">
            <h1 class="h4 mb-0 fw-bold" style="color: #1e293b;">Historique de l'objet</h3>
        </div>
        <div class="col-6 col-md-4 text-end order-2 order-md-3">
            <span class="badge obj-id-badge py-2 px-3 rounded-pill">
              <h1>#<?= htmlspecialchars($id_objet ?? '000') ?></h1> 
            </span>
        </div>
    </header>

    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            
            <?php if (!empty($history) && is_array($history)): ?>
                <div class="history-timeline">
                    <?php foreach ($history as $index => $h): ?>
                        <div class="timeline-item">
                            <div class="timeline-card">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <div class="owner-name">
                                            <?= htmlspecialchars(($h['prenom'] ?? '') . ' ' . ($h['nom'] ?? '')) ?>
                                            <?php if($index === 0): ?>
                                                <small class="ms-2 badge bg-primary" style="font-size: 0.65rem; vertical-align: middle;">ACTUEL</small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="owner-id mt-1">
                                            ID : <?= htmlspecialchars($h['id_proprietaire'] ?? '') ?>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="date-badge">
                                            <?= htmlspecialchars($h['date_echange'] ?? 'Date inconnue') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-light border text-center py-5 shadow-sm">
                    <p class="text-muted mb-0">Aucun historique de transfert trouvé.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>