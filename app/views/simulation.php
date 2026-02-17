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
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/simulation.css">
    <title>Simulation de Dispatch - BNGRC</title>
</head>

<body>
    <?php include __DIR__ . '/navbar.php'; ?>

    <div class="simulation-container">
        <div class="simulation-header">
            <h1>🎯 Simulation de Dispatch</h1>
            <p>Prévisualisez la distribution des dons non distribués avant de les dispatcher réellement aux villes.</p>
        </div>

        <!-- Liste des dons non distribués -->
        <div
            style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
            <h2
                style="color: #333; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #007bff; padding-bottom: 10px;">
                📦 Dons en Attente de Distribution
            </h2>

            <div id="donsContent"
                style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
                <p style="text-align: center; color: #999;">Chargement...</p>
            </div>
        </div>

        <!-- Simulation de répartition -->
        <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h2
                style="color: #333; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #28a745; padding-bottom: 10px;">
                ✓ Actions de Dispatch
            </h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <button type="button" id="btnSimulerTout" class="btn"
                    style="padding: 15px; background-color: #007bff; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: 500; transition: all 0.3s;">
                    👁️ Simuler Distribution
                </button>
                <button type="button" id="btnValiderTout" class="btn"
                    style="padding: 15px; background-color: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: 500; transition: all 0.3s;">
                    ✓ Valider & Dispatcher
                </button>
                <a href="<?= $base_url ?>/dispatch" class="btn"
                    style="padding: 15px; background-color: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; text-align: center; font-size: 16px; font-weight: 500; transition: all 0.3s;">
                    📍 Aller à Dispatch
                </a>
            </div>
        </div>

        <!-- Résultats de simulation -->
        <div id="resultSimulation"
            style="margin-top: 30px; display: none; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h2 style="color: #333; margin-top: 0; margin-bottom: 20px;">
                📊 Résumé de la Simulation
            </h2>
            <div id="resultContent"></div>
        </div>

    </div>

    <script nonce="<?= $csp_nonce ?>">
        const baseUrl = '<?= $base_url ?>';

        function formatMontant(montant) {
            return new Intl.NumberFormat('fr-FR').format(Math.round(montant)) + ' Ar';
        }

        function chargerDons() {
            fetch(baseUrl + '/api/dons/non-distribues')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('donsContent');

                    if (!Array.isArray(data) || data.length === 0) {
                        container.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 30px; color: #999;"><i class="fas fa-check-circle" style="font-size: 48px; margin-bottom: 10px;"></i><p>✅ Tous les dons ont déjà été distribués!</p></div>';
                        document.getElementById('btnSimulerTout').disabled = true;
                        document.getElementById('btnValiderTout').disabled = true;
                        return;
                    }

                    let html = '';
                    let totalQuantite = 0;
                    let totalValeur = 0;

                    data.forEach(don => {
                        const valeur = don.quantite * don.prix_unitaire;
                        totalQuantite += don.quantite;
                        totalValeur += valeur;

                        html += `
                            <div style="border: 1px solid #ddd; padding: 15px; border-radius: 6px; background-color: #f9f9f9;">
                                <div style="margin-bottom: 10px;">
                                    <strong style="font-size: 16px;">${don.nom_matiere}</strong>
                                    <div style="font-size: 12px; color: #999; margin-top: 3px;">Donné le ${new Date(don.date_don).toLocaleDateString('fr-FR')}</div>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 13px; color: #666; margin-bottom: 10px;">
                                    <div>
                                        <span style="font-weight: 500;">Quantité:</span> ${don.quantite} unités
                                    </div>
                                    <div>
                                        <span style="font-weight: 500;">Prix unitaire:</span> ${formatMontant(don.prix_unitaire)}
                                    </div>
                                </div>
                                <div style="background-color: #e7f3ff; padding: 10px; border-radius: 4px; text-align: center; font-weight: 500; color: #0056b3;">
                                    Valeur totale: ${formatMontant(valeur)}
                                </div>
                            </div>
                        `;
                    });

                    // Ajouter un résumé
                    html += `
                        <div style="grid-column: 1/-1; background-color: #f0f4ff; padding: 20px; border-radius: 6px; border-left: 4px solid #007bff;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div>
                                    <div style="font-size: 12px; color: #666; font-weight: 500; text-transform: uppercase;">Quantité totale</div>
                                    <div style="font-size: 28px; font-weight: bold; color: #007bff;">${totalQuantite}</div>
                                    <div style="font-size: 12px; color: #999;">unités à distribuer</div>
                                </div>
                                <div>
                                    <div style="font-size: 12px; color: #666; font-weight: 500; text-transform: uppercase;">Valeur totale</div>
                                    <div style="font-size: 28px; font-weight: bold; color: #28a745;">${formatMontant(totalValeur)}</div>
                                    <div style="font-size: 12px; color: #999;">montant à distribuer</div>
                                </div>
                            </div>
                        </div>
                    `;

                    container.innerHTML = html;
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    document.getElementById('donsContent').innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #999;">Erreur lors du chargement des dons</p>';
                });
        }

        // Bouton Simuler
        document.getElementById('btnSimulerTout').addEventListener('click', function () {
            this.disabled = true;
            this.innerHTML = '⏳ Simulation en cours...';

            fetch(baseUrl + '/api/dispatch/simuler')
                .then(response => response.json())
                .then(data => {
                    const resultDiv = document.getElementById('resultSimulation');
                    const resultContent = document.getElementById('resultContent');

                    if (data.success || data.length > 0) {
                        let html = '<div style="background-color: #d4edda; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #28a745;"><strong>✓ Simulation valide</strong> - Prêt à dispatcher</div>';

                        if (Array.isArray(data)) {
                            html += '<table style="width: 100%; border-collapse: collapse; margin-top: 15px;">';
                            html += '<thead style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;"><tr><th style="padding: 12px; text-align: left; font-weight: 600;">Ville</th><th style="padding: 12px; text-align: left; font-weight: 600;">Matière</th><th style="padding: 12px; text-align: right; font-weight: 600;">Quantité</th><th style="padding: 12px; text-align: right; font-weight: 600;">Valeur</th></tr></thead><tbody>';

                            data.forEach(item => {
                                const valeur = item.quantite * item.prix_unitaire;
                                html += `<tr style="border-bottom: 1px solid #dee2e6;"><td style="padding: 12px;">${item.nom_ville || 'À assigner'}</td><td style="padding: 12px;">${item.nom_matiere}</td><td style="padding: 12px; text-align: right;">${item.quantite}</td><td style="padding: 12px; text-align: right; font-weight: 500;">${formatMontant(valeur)}</td></tr>`;
                            });

                            html += '</tbody></table>';
                        }

                        resultContent.innerHTML = html;
                        resultDiv.style.display = 'block';
                        resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    } else {
                        resultContent.innerHTML = '<div style="background-color: #f8d7da; padding: 15px; border-radius: 6px; border-left: 4px solid #dc3545;"><strong>❌ Erreur:</strong> ' + (data.message || 'Impossible de simuler') + '</div>';
                        resultDiv.style.display = 'block';
                    }

                    this.disabled = false;
                    this.innerHTML = '👁️ Simuler Distribution';
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la simulation');
                    this.disabled = false;
                    this.innerHTML = '👁️ Simuler Distribution';
                });
        });

        // Bouton Valider
        document.getElementById('btnValiderTout').addEventListener('click', function () {
            if (!confirm('Êtes-vous sûr de vouloir dispatcher tous les dons non distribués?\n\nCette action est irréversible.')) {
                return;
            }

            this.disabled = true;
            this.innerHTML = '⏳ Dispatch en cours...';

            fetch(baseUrl + '/api/dispatch/valider', {
                method: 'POST'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const resultDiv = document.getElementById('resultSimulation');
                        const resultContent = document.getElementById('resultContent');

                        resultContent.innerHTML = `
                            <div style="background-color: #d4edda; padding: 20px; border-radius: 6px; border-left: 4px solid #28a745; text-align: center;">
                                <div style="font-size: 48px; margin-bottom: 15px;">✓</div>
                                <strong style="font-size: 18px; color: #155724;">Dispatch réussi!</strong>
                                <p style="color: #155724; margin-top: 10px;">${data.message || 'Tous les dons ont été distribués'}</p>
                                <div style="margin-top: 20px; padding: 15px; background-color: rgba(255,255,255,0.5); border-radius: 4px;">
                                    <strong>${data.nombre_dons} dons distribués avec succès</strong>
                                </div>
                            </div>
                        `;
                        resultDiv.style.display = 'block';
                        resultDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                        // Recharger les dons après 2 secondes
                        setTimeout(chargerDons, 2000);
                    } else {
                        alert('Erreur: ' + (data.message || 'Erreur lors du dispatch'));
                        this.disabled = false;
                        this.innerHTML = '✓ Valider & Dispatcher';
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors du dispatch');
                    this.disabled = false;
                    this.innerHTML = '✓ Valider & Dispatcher';
                });
        });

        // Charger les dons au démarrage
        document.addEventListener('DOMContentLoaded', chargerDons);

        // Rafraîchir tous les 10 secondes
        setInterval(chargerDons, 10000);
    </script>
    <?php include("footer.php"); ?>
</body>

</html>