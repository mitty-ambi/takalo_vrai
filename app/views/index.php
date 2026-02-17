<?php $base_url = rtrim(Flight::get('flight.base_url'), '/');
$csp_nonce = Flight::app()->get('csp_nonce') ?? '';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/all.min.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/navbar.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/index.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/gerer_besoins.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/recap.css">
    <title>BNGRC - Accueil</title>
    <style>
        .btn-reset {
            background: #dc3545 !important;
            color: white !important;
            padding: 8px 16px !important;
            border: none !important;
            border-radius: 4px !important;
            cursor: pointer !important;
            margin-left: 10px !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-reset:hover {
            background: #c82333 !important;
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }

        .btn-reset:active {
            transform: scale(0.98);
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/navbar.php'; ?>

    <!-- Section Récapitulation -->
    <div class="recap-section">
        <div class="recap-title">
            <span>📊 Récapitulation Générale</span>
            <button class="btn-refresh" id="btnRefresh">🔄 Actualiser</button>
            <button class="btn-reset" id="btnReinitialiser">🔴 Réinitialiser</button>
        </div>
        <div class="recap-grid">
            <div class="recap-card">
                <div class="recap-card-label">Besoins Totaux</div>
                <div class="recap-card-value" id="montantTotal">-</div>
            </div>
            <div class="recap-card">
                <div class="recap-card-label">Besoins Satisfaits</div>
                <div class="recap-card-value" id="montantSatisfait">-</div>
            </div>
            <div class="recap-card">
                <div class="recap-card-label">Besoins Restants</div>
                <div class="recap-card-value" id="montantRestant">-</div>
            </div>
        </div>
    </div>

    <h1>Tableau récapitulant la liste des villes avec Besoins</h1>

    <form method="get" action="<?= $base_url ?>/" class="filter-form" style="margin-bottom:12px;">
        <label for="nom_ville">Filtrer par ville :</label>
        <input class="filter-input" type="text" id="nom_ville" name="nom_ville"
            value="<?= htmlspecialchars($nom_ville ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <button class="filter-btn" type="submit">Filtrer</button>
        <a class="filter-reset" href="<?= $base_url ?>/">Réinitialiser</a>
    </form>

    <table border="1px">
        <tr>
            <th>Nom Ville</th>
            <th>Région</th>
            <th>Nombre de sinistres</th>
            <th>Actions</th>
        </tr>
        <?php if (!empty($listeVille) && is_array($listeVille)): ?>
            <?php foreach ($listeVille as $ville): ?>
                <tr>
                    <td><?= htmlspecialchars($ville['nom_ville'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($ville['nom_region'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int) ($ville['nombres_sinistres'] ?? 0) ?></td>
                    <td><a href="<?= $base_url ?>/StatsVille?id_ville=<?= urlencode($ville['id_ville'] ?? '') ?>"
                            class="btn btn-primary">voir les statistiques</a></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">Aucune ville trouvée.</td>
            </tr>
        <?php endif; ?>
    </table>

    <script nonce="<?= $csp_nonce ?>">
        const baseUrl = '<?= $base_url ?>';

        // Formater un montant en Ar avec séparateurs
        function formatMontant(montant) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'MGA'
            }).format(montant).replace(/\s/g, ' ');
        }

        // Charger les stats
        function chargerStats() {
            const section = document.querySelector('.recap-section');
            const btnRefresh = document.getElementById('btnRefresh');

            section.classList.add('loading');
            btnRefresh.disabled = true;

            fetch(baseUrl + '/api/stats/recap')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('montantTotal').textContent = formatMontant(data.montant_total);
                    document.getElementById('montantSatisfait').textContent = formatMontant(data.montant_satisfait);
                    document.getElementById('montantRestant').textContent = formatMontant(data.montant_restant);

                    section.classList.remove('loading');
                    btnRefresh.disabled = false;
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    document.getElementById('montantTotal').textContent = 'Erreur';
                    document.getElementById('montantSatisfait').textContent = 'Erreur';
                    document.getElementById('montantRestant').textContent = 'Erreur';

                    section.classList.remove('loading');
                    btnRefresh.disabled = false;
                });
        }

        // Event listener pour le bouton actualiser
        document.getElementById('btnRefresh').addEventListener('click', chargerStats);

        // Event listener pour le bouton réinitialiser
        document.getElementById('btnReinitialiser').addEventListener('click', function () {
            if (confirm('⚠️ ATTENTION! Cette action va:\n\n✓ Supprimer TOUS les achats\n✓ Réinitialiser TOUS les dons comme non distribués\n✓ Conserver les besoins avec leur quantité initiale\n\nCette action est IRRÉVERSIBLE!\n\nÊtes-vous sûr(e)?')) {
                if (confirm('Êtes-vous TRÈS sûr(e)? Cette action ne peut pas être annulée!')) {
                    // Créer un formulaire et soumettre
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = baseUrl + '/api/reinitialiser';
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        });

        // Charger les stats au chargement de la page
        window.addEventListener('load', chargerStats);
    </script>
    <?php include("footer.php"); ?>
</body>

</html>