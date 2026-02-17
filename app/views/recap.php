<?php $base_url = rtrim(Flight::get('flight.base_url'), '/'); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/all.min.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/navbar.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/recap.css">
    <title>Récapitulation - BNGRC</title>
</head>

<body>
    <?php include __DIR__ . '/navbar.php'; ?>

    <div class="container" style="padding: 40px 20px;">
        <div style="margin-bottom: 40px;">
            <h1 style="text-align: center; color: #333; margin-bottom: 10px;">📊 Récapitulation Globale</h1>
            <p style="text-align: center; color: #666; font-size: 16px;">État actuel des besoins et des dons</p>
        </div>

        <!-- Section Récapitulation -->
        <div class="recap-section">
            <div class="recap-title">
                <span>💰 Bilan des Besoins</span>
                <button type="button" class="btn-refresh" id="btnActualiser">🔄 Actualiser</button>
            </div>

            <div class="recap-grid" id="recapContent">
                <div class="recap-card">
                    <div class="recap-card-label">Total Besoins</div>
                    <div class="recap-card-value" id="totalBesoins">0</div>
                    <div style="font-size: 12px; color: rgba(255,255,255,0.8); margin-top: 5px;">Ar</div>
                </div>

                <div class="recap-card">
                    <div class="recap-card-label">Dons Reçus</div>
                    <div class="recap-card-value" id="totalDons">0</div>
                    <div style="font-size: 12px; color: rgba(255,255,255,0.8); margin-top: 5px;">Ar</div>
                </div>

                <div class="recap-card" style="border-left-color: #ffc107;">
                    <div class="recap-card-label">Besoins Restants</div>
                    <div class="recap-card-value" id="totalRestant">0</div>
                    <div style="font-size: 12px; color: rgba(255,255,255,0.8); margin-top: 5px;">Ar</div>
                </div>

                <div class="recap-card" style="border-left-color: #28a745;">
                    <div class="recap-card-label">Taux Satisfaction</div>
                    <div class="recap-card-value" id="pourcentageSatisfaction">0%</div>
                    <div style="font-size: 12px; color: rgba(255,255,255,0.8); margin-top: 5px;">Satisfait</div>
                </div>
            </div>
        </div>

        <!-- Détails par ville -->
        <div style="margin-top: 40px;">
            <h2 style="color: #333; margin-bottom: 20px; border-bottom: 2px solid #007bff; padding-bottom: 10px;">
                📍 Détails par Ville
            </h2>
            <div id="villesContent" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                <!-- Chargé via AJAX -->
                <p style="text-align: center; color: #999;">Chargement...</p>
            </div>
        </div>
    </div>

    <script nonce="<?= Flight::app()->get('csp_nonce') ?>">
        const baseUrl = '<?= $base_url ?>';

        function formatMontant(montant) {
            return new Intl.NumberFormat('fr-FR').format(Math.round(montant)) + ' Ar';
        }

        function actualiserRecap() {
            const btn = document.getElementById('btnActualiser');
            btn.disabled = true;
            btn.textContent = '⏳ Actualisation...';

            fetch(baseUrl + '/api/stats/recap')
                .then(response => response.json())
                .then(data => {
                    if (data.success === false) {
                        console.error('Erreur:', data.message);
                        btn.disabled = false;
                        btn.textContent = '🔄 Actualiser';
                        return;
                    }

                    // Mettre à jour les cartes
                    document.getElementById('totalBesoins').textContent = formatMontant(data.montant_total);
                    document.getElementById('totalDons').textContent = formatMontant(data.montant_satisfait);
                    document.getElementById('totalRestant').textContent = formatMontant(data.montant_restant);

                    // Calculer le pourcentage
                    const pourcentage = data.montant_total > 0 
                        ? Math.round((data.montant_satisfait / data.montant_total) * 100)
                        : 0;
                    document.getElementById('pourcentageSatisfaction').textContent = pourcentage + '%';

                    // Couleur dynamique
                    if (pourcentage >= 75) {
                        document.getElementById('pourcentageSatisfaction').parentElement.style.borderLeftColor = '#28a745';
                    } else if (pourcentage >= 50) {
                        document.getElementById('pourcentageSatisfaction').parentElement.style.borderLeftColor = '#ffc107';
                    } else {
                        document.getElementById('pourcentageSatisfaction').parentElement.style.borderLeftColor = '#dc3545';
                    }

                    btn.disabled = false;
                    btn.textContent = '🔄 Actualiser';
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    btn.disabled = false;
                    btn.textContent = '🔄 Actualiser';
                });

            // Charger les détails par ville
            chargerVilles();
        }

        function chargerVilles() {
            fetch(baseUrl + '/api/stats/villes')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('villesContent');
                    
                    if (!Array.isArray(data) || data.length === 0) {
                        container.innerHTML = '<p style="text-align: center; color: #999; grid-column: 1/-1;">Aucune données disponible</p>';
                        return;
                    }

                    let html = '';
                    data.forEach(ville => {
                        const pourcentageVille = ville.montant_total > 0 
                            ? Math.round((ville.montant_satisfait / ville.montant_total) * 100)
                            : 0;

                        html += `
                            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border-left: 4px solid #007bff;">
                                <h3 style="margin: 0 0 15px 0; color: #333;">${ville.nom_ville}</h3>
                                <div style="margin-bottom: 10px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                        <span style="font-size: 14px; color: #666;">Besoins totaux:</span>
                                        <strong>${formatMontant(ville.montant_total)}</strong>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                        <span style="font-size: 14px; color: #666;">Dons reçus:</span>
                                        <strong style="color: #28a745;">${formatMontant(ville.montant_satisfait)}</strong>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                        <span style="font-size: 14px; color: #666;">Restant:</span>
                                        <strong style="color: #dc3545;">${formatMontant(ville.montant_restant)}</strong>
                                    </div>
                                </div>
                                <div style="background: #f0f0f0; padding: 8px; border-radius: 4px; text-align: center;">
                                    <div style="font-size: 12px; color: #666; margin-bottom: 3px;">Taux satisfaction</div>
                                    <div style="font-size: 20px; font-weight: bold; color: ${pourcentageVille >= 75 ? '#28a745' : pourcentageVille >= 50 ? '#ffc107' : '#dc3545'};">
                                        ${pourcentageVille}%
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    container.innerHTML = html;
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    document.getElementById('villesContent').innerHTML = '<p style="text-align: center; color: #999; grid-column: 1/-1;">Erreur lors du chargement</p>';
                });
        }

        // Initialiser au chargement
        document.addEventListener('DOMContentLoaded', function() {
            actualiserRecap();

            // Actualiser toutes les 10 secondes
            setInterval(actualiserRecap, 10000);

            // Bouton actualiser
            document.getElementById('btnActualiser').addEventListener('click', actualiserRecap);
        });
    </script>
</body>

</html>
