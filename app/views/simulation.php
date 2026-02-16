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
    <title>Simulation d'Achat - BNGRC</title>
</head>

<body>
    <?php include __DIR__ . '/navbar.php'; ?>

    <div class="simulation-container">
        <div class="simulation-header">
            <h1>🛒 Simulation d'Achat</h1>
            <p>Prévisualisez votre achat avant de le valider. Le système affichera les montants réels avec les frais appliqués.</p>
        </div>

        <!-- Formulaire de simulation -->
        <div class="simulation-form">
            <form id="simulationForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="id_ville">Ville:</label>
                        <select id="id_ville" name="id_ville" required>
                            <option value="">-- Choisir une ville --</option>
                            <?php if (!empty($villes)): ?>
                                <?php foreach ($villes as $ville): ?>
                                    <option value="<?= htmlspecialchars($ville['id_ville']) ?>">
                                        <?= htmlspecialchars($ville['nom_ville']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_matiere">Matière:</label>
                        <select id="id_matiere" name="id_matiere" required>
                            <option value="">-- Choisir une matière --</option>
                            <?php if (!empty($matieres)): ?>
                                <?php foreach ($matieres as $matiere): ?>
                                    <option value="<?= htmlspecialchars($matiere['id_matiere']) ?>" data-prix="<?= htmlspecialchars($matiere['prix_unitaire']) ?>">
                                        <?= htmlspecialchars($matiere['nom_matiere']) ?> (<?= number_format($matiere['prix_unitaire'], 0, ',', ' ') ?> Ar/u)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="montant">Montant en Ar:</label>
                    <input type="number" id="montant" name="montant" required min="0" step="100" placeholder="Entrez le montant du don en Ar">
                    <small style="color: #666; margin-top: 5px; display: block;">
                        💡 Conseil: Le montant ne doit pas dépasser le besoin restant pour cette matière et ville.
                    </small>
                </div>

                <div class="button-group">
                    <button type="button" class="btn btn-primary" id="btnSimuler">
                        👁️ Simuler
                    </button>
                </div>
            </form>
        </div>

        <!-- Résultats de simulation -->
        <div id="simulationResult" class="simulation-result">
            <div class="result-title">
                <span class="result-icon" id="resultIcon">✓</span>
                <span id="resultTitle">Résultat</span>
            </div>

            <div id="resultContent"></div>

            <div id="validationButtons" style="display: none;">
                <div class="button-group" style="margin-top: 20px;">
                    <button type="button" class="btn btn-success" id="btnValider">
                        ✓ Valider et Enregistrer
                    </button>
                    <button type="button" class="btn btn-primary" id="btnRetour" style="background-color: #a0aec0;">
                        ← Modifier
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script nonce="<?= $csp_nonce ?>">
        const baseUrl = '<?= $base_url ?>';
        const fraisDefault = 10; // 10%

        // Formater un montant
        function formatMontant(montant) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'MGA'
            }).format(montant).replace(/\s/g, ' ');
        }

        // Formater un nombre avec séparateurs
        function formatNombre(nombre) {
            return new Intl.NumberFormat('fr-FR').format(nombre);
        }

        // Afficher le résultat
        function afficherResultat(data) {
            const result = document.getElementById('simulationResult');
            const resultTitle = document.getElementById('resultTitle');
            const resultIcon = document.getElementById('resultIcon');
            const resultContent = document.getElementById('resultContent');
            const validationButtons = document.getElementById('validationButtons');

            result.classList.remove('error', 'warning', 'success');

            if (!data.success) {
                // Erreur
                result.classList.add('error');
                resultTitle.textContent = '❌ Simulation Échouée';
                resultIcon.textContent = '✗';

                let html = `
                    <div class="alert alert-error">
                        <strong>Erreur:</strong> ${data.message}
                    </div>
                `;

                if (data.montant_demande && data.montant_restant) {
                    html += `
                        <div class="result-grid">
                            <div class="result-card">
                                <div class="result-label">Montant Demandé</div>
                                <div class="result-value">${formatMontant(data.montant_demande)}</div>
                            </div>
                            <div class="result-card warning">
                                <div class="result-label">Montant Maximum Autorisé</div>
                                <div class="result-value">${formatMontant(data.montant_restant)}</div>
                            </div>
                        </div>
                        <table class="comparison-table">
                            <tr>
                                <th>Élément</th>
                                <th>Valeur</th>
                                <th>Statut</th>
                            </tr>
                            <tr>
                                <td>Quantité Restante</td>
                                <td>${formatNombre(data.quantite_restante)} unités</td>
                                <td><span class="negative">Insuffisant</span></td>
                            </tr>
                        </table>
                    `;
                }

                resultContent.innerHTML = html;
                validationButtons.style.display = 'none';
            } else {
                // Succès
                result.classList.add('success');
                resultTitle.textContent = '✓ Simulation Valide';
                resultIcon.textContent = '✓';

                const montantSansFrais = data.montant_base;
                const montantFrais = data.frais_montant;
                const montantTotal = data.montant_total_achat;

                let html = `
                    <div class="alert alert-info">
                        <strong>Bon à savoir:</strong> Le montant en Dons sera ${formatMontant(montantSansFrais)} 
                        et le montant en Achats sera ${formatMontant(montantTotal)} (avec ${data.frais_pourcentage}% de frais).
                    </div>

                    <div class="result-grid">
                        <div class="result-card">
                            <div class="result-label">Matière</div>
                            <div class="result-value" style="font-size: 16px;">${data.matiere}</div>
                        </div>
                        <div class="result-card">
                            <div class="result-label">Prix Unitaire</div>
                            <div class="result-value">${formatMontant(data.prix_unitaire)}</div>
                        </div>
                        <div class="result-card">
                            <div class="result-label">Quantité à Acheter</div>
                            <div class="result-value">${formatNombre(data.quantite_achetee)}</div>
                        </div>
                    </div>

                    <table class="comparison-table">
                        <tr>
                            <th colspan="2">Calcul du Montant</th>
                            <th>Montant</th>
                        </tr>
                        <tr>
                            <td colspan="2">Montant de Base (Don)</td>
                            <td><strong>${formatMontant(montantSansFrais)}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2">Frais (${data.frais_pourcentage}%)</td>
                            <td><strong class="negative">+ ${formatMontant(montantFrais)}</strong></td>
                        </tr>
                        <tr style="background-color: #f7fafc;">
                            <td colspan="2"><strong>Montant Total (Achat)</strong></td>
                            <td><strong class="positive">${formatMontant(montantTotal)}</strong></td>
                        </tr>
                    </table>

                    <div style="margin-top: 20px; padding: 15px; background-color: #f0f4ff; border-radius: 5px;">
                        <strong>État de la Matière:</strong><br>
                        Quantité Restante: <strong>${formatNombre(data.quantite_restante)}</strong> unités<br>
                        Quantité Après Achat: <strong>${formatNombre(data.quantite_apres_achat)}</strong> unités<br>
                        Montant Restant: <strong>${formatMontant(data.montant_restant_apres)}</strong>
                    </div>
                `;

                resultContent.innerHTML = html;
                validationButtons.style.display = 'block';
            }

            result.classList.add('show');
            result.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // Simuler
        document.getElementById('btnSimuler').addEventListener('click', function() {
            const id_matiere = document.getElementById('id_matiere').value;
            const id_ville = document.getElementById('id_ville').value;
            const montant = document.getElementById('montant').value;

            if (!id_matiere || !id_ville || !montant) {
                alert('Veuillez remplir tous les champs');
                return;
            }

            this.disabled = true;
            this.innerHTML = '<span class="loading-spinner"></span> Simulation en cours...';

            fetch(baseUrl + '/api/achats/simuler', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    id_matiere: id_matiere,
                    id_ville: id_ville,
                    montant: montant
                })
            })
            .then(response => response.json())
            .then(data => {
                afficherResultat(data);
                this.disabled = false;
                this.innerHTML = '👁️ Simuler';
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la simulation');
                this.disabled = false;
                this.innerHTML = '👁️ Simuler';
            });
        });

        // Valider
        document.getElementById('btnValider').addEventListener('click', function() {
            if (!confirm('Êtes-vous sûr de vouloir enregistrer cet achat?')) {
                return;
            }

            const id_matiere = document.getElementById('id_matiere').value;
            const id_ville = document.getElementById('id_ville').value;
            const montant = document.getElementById('montant').value;

            this.disabled = true;
            this.innerHTML = '<span class="loading-spinner"></span> Enregistrement...';

            fetch(baseUrl + '/api/achats/valider', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    id_matiere: id_matiere,
                    id_ville: id_ville,
                    montant: montant
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const result = document.getElementById('simulationResult');
                    const resultContent = document.getElementById('resultContent');
                    
                    let html = `
                        <div class="alert alert-info">
                            <strong>✓ Achat Enregistré avec Succès!</strong><br>
                            Le don de ${formatMontant(data.montant_don)} a été dispatché.<br>
                            Le montant de l'achat (avec frais) est ${formatMontant(data.montant_achat_avec_frais)}.
                        </div>
                        <div class="result-grid">
                            <div class="result-card success">
                                <div class="result-label">Quantité Enregistrée</div>
                                <div class="result-value">${formatNombre(data.quantite)}</div>
                            </div>
                            <div class="result-card success">
                                <div class="result-label">Montant Don</div>
                                <div class="result-value">${formatMontant(data.montant_don)}</div>
                            </div>
                            <div class="result-card success">
                                <div class="result-label">Montant Achat (avec frais)</div>
                                <div class="result-value">${formatMontant(data.montant_achat_avec_frais)}</div>
                            </div>
                        </div>
                    `;

                    result.classList.remove('error');
                    result.classList.add('success');
                    resultContent.innerHTML = html;
                    document.getElementById('validationButtons').innerHTML = `
                        <div class="button-group" style="margin-top: 20px;">
                            <button type="button" class="btn btn-primary" onclick="location.reload()">
                                🔄 Nouvelle Simulation
                            </button>
                            <a href="${baseUrl}/" class="btn btn-primary" style="text-decoration: none; display: flex; align-items: center; justify-content: center;">
                                🏠 Retour Accueil
                            </a>
                        </div>
                    `;
                    
                    document.getElementById('simulationForm').style.display = 'none';
                } else {
                    alert('Erreur: ' + data.message);
                    this.disabled = false;
                    this.innerHTML = '✓ Valider et Enregistrer';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'enregistrement');
                this.disabled = false;
                this.innerHTML = '✓ Valider et Enregistrer';
            });
        });

        // Retour
        document.getElementById('btnRetour').addEventListener('click', function() {
            document.getElementById('simulationResult').classList.remove('show');
            document.getElementById('simulationForm').style.display = 'block';
        });

        // Permettre d'appuyer sur Enter pour simuler
        document.getElementById('montant').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('btnSimuler').click();
            }
        });
    </script>
</body>

</html>
<?php
// ...existing code...
?>
<form id="simulationForm" style="max-width:600px;" aria-labelledby="simTitle" novalidate>
  <h3 id="simTitle">Simulation de don</h3>

  <fieldset>
    <legend>Type de don</legend>
    <label>
      <input type="radio" name="don_type" value="argent" checked> Don en argent
    </label>
  </fieldset>

  <br>

  <label for="besoin">Sélection du besoin :</label><br>
  <select name="besoin" id="besoin" required>
    <option value="" disabled selected>-- Choisir un besoin --</option>
    <option value="sante" data-fee="5">Santé</option>
    <option value="education" data-fee="3">Éducation</option>
    <option value="urgence" data-fee="7">Urgence</option>
    <option value="autre" data-fee="4">Autre</option>
  </select>
  <br><br>

  <label for="montant">Montant à utiliser (€) :</label><br>
  <input type="number" id="montant" name="montant" step="0.01" min="0.01" inputmode="decimal" required placeholder="0.00">
  <br><br>

  <div>
    Affichage du taux de frais : <strong><span id="tauxFrais">-</span> %</strong>
  </div>
  <br>

  <button type="button" id="btnSimuler">Simuler</button>
</form>

<br>
<div id="resultatSimulation" aria-live="polite" style="border:1px solid #ddd;padding:10px;max-width:600px;">
  Aucun résultat.
</div>

<script>
(function(){
  const form = document.getElementById('simulationForm');
  const besoinSel = document.getElementById('besoin');
  const tauxFraisEl = document.getElementById('tauxFrais');
  const montantEl = document.getElementById('montant');
  const btn = document.getElementById('btnSimuler');
  const resEl = document.getElementById('resultatSimulation');

  function formatEUR(v){
    return Number(v).toLocaleString('fr-FR', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' €';
  }

  function updateTaux(){
    const opt = besoinSel.selectedOptions[0];
    if(!opt || !opt.dataset.fee){
      tauxFraisEl.textContent = '-';
      return;
    }
    const fee = parseFloat(opt.dataset.fee || '0');
    tauxFraisEl.textContent = fee.toFixed(2);
  }

  function validInputs(){
    return besoinSel.value && parseFloat(montantEl.value) > 0;
  }

  function simuler(){
    if(!validInputs()){
      resEl.innerHTML = '<em>Veuillez sélectionner un besoin valide et saisir un montant supérieur à 0.</em>';
      return;
    }
    const feePct = parseFloat(tauxFraisEl.textContent) || 0;
    const montant = Math.max(0, parseFloat(montantEl.value) || 0);
    const frais = +(montant * feePct / 100).toFixed(2);
    const net = +(montant - frais).toFixed(2);

    resEl.innerHTML = '<strong>Résultat :</strong><br>' +
      'Montant entrant : ' + formatEUR(montant) + '<br>' +
      'Frais (' + feePct.toFixed(2) + '%) : ' + formatEUR(frais) + '<br>' +
      'Montant disponible pour le besoin : ' + formatEUR(net);
  }

  besoinSel.addEventListener('change', updateTaux);
  montantEl.addEventListener('input', () => {
    if(montantEl.value && parseFloat(montantEl.value) < 0) montantEl.value = '';
  });

  btn.addEventListener('click', simuler);

  form.addEventListener('submit', function(e){
    e.preventDefault();
    simuler();
  });

  updateTaux();
})();
</script>
<?php
// ...existing code...
?>
