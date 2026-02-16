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
</head>

<body>
    <?php include __DIR__ . '/navbar.php'; ?>
    
    <!-- Section Récapitulation -->
    <div class="recap-section">
        <div class="recap-title">
            <span>📊 Récapitulation Générale</span>
            <button class="btn-refresh" id="btnRefresh">🔄 Actualiser</button>
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
        <input class="filter-input" type="text" id="nom_ville" name="nom_ville" value="<?= htmlspecialchars($nom_ville ?? '', ENT_QUOTES, 'UTF-8') ?>">
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
        <?php if (!empty($listeVille) && is_array($listeVille)) : ?>
            <?php foreach ($listeVille as $ville) : ?>
                <tr>
                    <td><?= htmlspecialchars($ville['nom_ville'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($ville['nom_region'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int) ($ville['nombres_sinistres'] ?? 0) ?></td>
                    <td><a href="<?= $base_url ?>/StatsVille?id_ville=<?= urlencode($ville['id_ville'] ?? '') ?>" class="btn btn-primary">voir les statistiques</a></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">Aucune ville trouvée.</td></tr>
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
        
        // Charger les stats au chargement de la page
        window.addEventListener('load', chargerStats);
    </script>
</body>

</html>