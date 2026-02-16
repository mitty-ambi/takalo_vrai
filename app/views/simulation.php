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