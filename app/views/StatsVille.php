<?php $base_url = rtrim(Flight::get('flight.base_url'), '/'); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/all.min.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/navbar.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/stats.css">
    <title>Statistiques de la Ville - BNGRC</title>
    <style>
        .tab-button {
            cursor: pointer;
        }

        .tab-button.active {
            font-weight: bold;
            border-bottom: 3px solid #007bff;
        }
    </style>
</head>

<body>
    <?php include("navbar.php"); ?>

    <div class="stats-container">
        <!-- Header avec nom de la ville -->
        <div class="ville-header">
            <h1>
                <i class="fas fa-map-marker-alt"></i>
                <?= htmlspecialchars($nomville) ?>
            </h1>
            <div class="sous-titre">
                <i class="fas fa-chart-line"></i>
                Statistiques détaillées des besoins et dons
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="quick-stats">
            <div class="quick-stat-item">
                <div class="stat-value"><?= count($listeBesoin) ?></div>
                <div class="stat-label">Besoins</div>
            </div>
            <div class="quick-stat-item">
                <div class="stat-value"><?= count($listeDons) ?></div>
                <div class="stat-label">Dons</div>
            </div>
            <div class="quick-stat-item">
                <div class="stat-value">
                    <?php
                    $totalBesoins = array_sum(array_column($listeBesoin, 'total_quantite'));
                    echo number_format($totalBesoins);
                    ?>
                </div>
                <div class="stat-label">Unités totales</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs-container">
            <div class="tabs">
                <button class="tab-button active" data-tab="besoins">
                    <i class="fas fa-clipboard-list"></i>
                    Besoins
                </button>
                <button class="tab-button" data-tab="dons">
                    <i class="fas fa-gift"></i>
                    Dons
                </button>
                <button class="tab-button" data-tab="remaining">
                    <i class="fas fa-hourglass-half"></i>
                    Besoins Restants
                </button>
            </div>
        </div>

        <!-- Onglet Besoins -->
        <div id="besoins" class="tab-content active" style="display: block;">
            <h3>
                <i class="fas fa-clipboard-list"></i>
                Liste des besoins
            </h3>
            <div class="table-container">
                <?php if (count($listeBesoin) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Matière</th>
                                <th>Catégorie</th>
                                <th>Quantité</th>
                                <th>Prix Unitaire</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($listeBesoin as $besoin): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars(string: $besoin['nom_matiere']) ?></strong></td>
                                    <td><span
                                            class="categorie-badge"><?= htmlspecialchars($besoin['nom_categorie'] ?? 'Non catégorisé') ?></span>
                                    </td>
                                    <td><?= number_format($besoin['total_quantite'], 0, '.', ' ') ?></td>
                                    <td><?= number_format($besoin['prix_unitaire'], 0, '.', ' ') ?> Ar</td>
                                    <td><span
                                            class="amount-badge"><?= number_format($besoin['total_quantite'] * $besoin['prix_unitaire'], 0, '.', ' ') ?>
                                            Ar</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-message">
                        <i class="fas fa-inbox"></i>
                        Aucun besoin enregistré
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Onglet Dons -->
        <div id="dons" class="tab-content" style="display: none;">
            <h3>
                <i class="fas fa-gift"></i>
                Liste des dons
            </h3>
            <div class="table-container">
                <?php if (count($listeDons) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Matière</th>
                                <th>Catégorie</th>
                                <th>Quantité</th>
                                <th>Date</th>
                                <th>Valeur</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($listeDons as $don): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($don['nom_matiere']) ?></strong></td>
                                    <td><span
                                            class="categorie-badge"><?= htmlspecialchars($don['nom_categorie'] ?? 'Non catégorisé') ?></span>
                                    </td>
                                    <td><?= number_format($don['total_quantite'], 0, '.', ' ') ?></td>
                                    <td><?= date('d/m/Y', strtotime($don['last_date'])) ?></td>
                                    <td><span
                                            class="amount-badge"><?= number_format($don['total_quantite'] * $don['prix_unitaire'], 0, '.', ' ') ?>
                                            Ar</span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-message">
                        <i class="fas fa-gift"></i>
                        Aucun don enregistré
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Onglet Besoins Restants -->
        <div id="remaining" class="tab-content" style="display: none;">
            <h3>
                <i class="fas fa-hourglass-half"></i>
                Besoins restants (Besoins - Dons)
            </h3>
            <div class="table-container">
                <?php if (count($besoinRestant) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Matière</th>
                                <th>Besoin Total</th>
                                <th>Dons Reçus</th>
                                <th>Reste</th>
                                <th>Prix Unitaire</th>
                                <th>Valeur Restante</th>
                                <th>Acheter</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalReste = 0;
                            foreach ($besoinRestant as $reste):
                                $valeurReste = $reste['reste'] * $reste['prix_unitaire'];
                                $totalReste += $valeurReste;
                                ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($reste['nom_matiere']) ?></strong></td>
                                    <td><?= number_format($reste['total_besoin'], 0, '.', ' ') ?></td>
                                    <td><?= number_format($reste['total_don'], 0, '.', ' ') ?></td>
                                    <td><span class="badge-warning"><?= number_format($reste['reste'], 0, '.', ' ') ?></span>
                                    </td>
                                    <td><?= number_format($reste['prix_unitaire'], 0, '.', ' ') ?> Ar</td>
                                    <td><span class="amount-badge"><?= number_format($valeurReste, 0, '.', ' ') ?> Ar</span>
                                    </td>
                                    <td>
                                        <button class="btn-acheter"
                                            data-matiere="<?= htmlspecialchars($reste['nom_matiere']) ?>"
                                            data-id-matiere="<?= $reste['id_matiere'] ?>"
                                            data-quantite-restante="<?= $reste['reste'] ?>"
                                            data-prix="<?= $reste['prix_unitaire'] ?>">
                                            🛒 Acheter
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr style="background-color: #f0f0f0; font-weight: bold; border-top: 2px solid #ddd;">
                                <td colspan="5" style="text-align: right;">TOTAL RESTANT:</td>
                                <td><span class="amount-badge"><?= number_format($totalReste, 0, '.', ' ') ?> Ar</span></td>
                            </tr>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-message">
                        <i class="fas fa-check-circle"></i>
                        ✅ Tous les besoins sont satisfaits
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal d'achat -->
    <div id="achatModal"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div
            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 8px; width: 90%; max-width: 500px;">
            <h3>Acheter <span id="modalMatiere"></span></h3>
            <form id="achatForm" method="POST" action="<?= $base_url ?>/api/achats/create">
                <div style="margin-bottom: 15px;">
                    <label>Valeur Restante: <strong><span id="modalValeurRestante"></span> Ar</strong></label>
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="pourcentage">Pourcentage à acheter (%):</label>
                    <input type="range" id="pourcentage" name="pourcentage" min="0" max="100" value="50"
                        style="width: 100%;">
                    <div style="text-align: center; margin-top: 5px; font-weight: bold;">
                        <span id="pourcentageValue">50</span>% = <span id="montantCalcule">0</span> Ar
                    </div>
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="fraisInput">Frais d'achat (%):</label>
                    <input type="number" id="fraisInput" name="frais" min="0" max="100" step="0.1" value="10"
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                        oninput="calculerMontant()">
                    <small style="color: #666;">Modifiable : ex. achat de 100 avec 10% = coût 110</small>
                    <div style="background: #f0f0f0; padding: 10px; border-radius: 4px; margin-top: 5px;">
                        <strong>Total avec frais: <span id="montantTotal">0</span> Ar</strong>
                    </div>
                </div>

                <input type="hidden" id="id_matiere" name="id_matiere">
                <input type="hidden" id="montantFinal" name="montant">
                <input type="hidden" id="fraisFinal" name="frais" value="10">
                <input type="hidden" id="id_ville" name="id_ville"
                    value="<?= isset($_GET['id_ville']) ? htmlspecialchars($_GET['id_ville']) : '' ?>">

                <div style="display: flex; gap: 10px;">
                    <button type="submit"
                        style="flex: 1; padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Confirmer
                        l'achat</button>
                    <button type="button" onclick="fermerModal()"
                        style="flex: 1; padding: 10px; background: #ccc; color: #333; border: none; border-radius: 4px; cursor: pointer;">Annuler</button>
                </div>
            </form>
        </div>
    </div>

    <script nonce="<?= Flight::app()->get('csp_nonce') ?>">
        document.addEventListener('DOMContentLoaded', function () {
            // Event listeners pour les onglets
            var tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(function (button) {
                button.addEventListener('click', function (event) {
                    var tabName = this.getAttribute('data-tab');
                    switchTab(tabName);
                });
            });

            // Event listeners pour les boutons d'achat
            var achatButtons = document.querySelectorAll('.btn-acheter');
            achatButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    // Vérifier d'abord les dons non distribués
                    var idMatiere = this.getAttribute('data-id-matiere');
                    var idVille = '<?= isset($_GET['id_ville']) ? htmlspecialchars($_GET['id_ville']) : '' ?>';
                    
                    fetch('<?= $base_url ?>/api/achats/check-undistributed/' + idMatiere + '/' + idVille)
                        .then(response => response.json())
                        .then(data => {
                            if (data.has_undistributed_dons) {
                                // Afficher modal de dispatch
                                afficherModalDispatch(data);
                            } else {
                                // Pas de dons non distribués, ouvrir le formulaire d'achat
                                ouvrirModal(this);
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Erreur lors de la vérification des dons');
                        });
                });
            });

            // Event listener pour le slider de pourcentage
            var pourcentageSlider = document.getElementById('pourcentage');
            if (pourcentageSlider) {
                pourcentageSlider.addEventListener('input', function () {
                    calculerMontant();
                });
            }

            // Event listener pour la soumission du formulaire d'achat
            var achatForm = document.getElementById('achatForm');
            if (achatForm) {
                achatForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    
                    var idMatiere = document.getElementById('id_matiere').value;
                    var idVille = document.getElementById('id_ville').value;
                    var montant = document.getElementById('montantFinal').value;
                    var frais = document.getElementById('fraisFinal').value;
                    
                    // Envoyer la requête AJAX
                    fetch('<?= $base_url ?>/api/achats/create', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'id_matiere=' + encodeURIComponent(idMatiere) + 
                              '&id_ville=' + encodeURIComponent(idVille) + 
                              '&montant=' + encodeURIComponent(montant) +
                              '&frais=' + encodeURIComponent(frais)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Achat créé avec succès!');
                            fermerModal();
                            location.reload();
                        } else {
                            alert('Erreur: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Erreur lors de la création de l\'achat');
                    });
                });
            }

        });

        function switchTab(tabName) {
            // Masquer tous les onglets
            var allTabs = document.getElementsByClassName('tab-content');
            for (var i = 0; i < allTabs.length; i++) {
                allTabs[i].style.display = 'none';
            }

            // Afficher l'onglet sélectionné
            var activeTab = document.getElementById(tabName);
            if (activeTab) {
                activeTab.style.display = 'block';
            }

            // Désactiver tous les boutons
            var allButtons = document.getElementsByClassName('tab-button');
            for (var i = 0; i < allButtons.length; i++) {
                allButtons[i].classList.remove('active');
            }

            // Activer le bouton cliqué
            var activeButton = document.querySelector('[data-tab="' + tabName + '"]');
            if (activeButton) {
                activeButton.classList.add('active');
            }
        }

        function ouvrirModal(btn) {
            var matiere = btn.getAttribute('data-matiere');
            var idMatiere = btn.getAttribute('data-id-matiere');
            var quantiteRestante = parseInt(btn.getAttribute('data-quantite-restante'));
            var prixUnitaire = parseInt(btn.getAttribute('data-prix'));

            var valeurRestante = quantiteRestante * prixUnitaire;

            document.getElementById('modalMatiere').textContent = matiere;
            document.getElementById('modalValeurRestante').textContent = valeurRestante.toLocaleString('fr-FR');
            document.getElementById('id_matiere').value = idMatiere;
            document.getElementById('pourcentage').value = 50;

            // Stocker les valeurs pour le calcul
            window.currentPrix = prixUnitaire;
            window.currentValeurRestante = valeurRestante;

            calculerMontant();

            document.getElementById('achatModal').style.display = 'block';
        }

        function fermerModal() {
            document.getElementById('achatModal').style.display = 'none';
        }

        function afficherModalDispatch(data) {
            // Créer le modal de dispatch dynamiquement
            var modal = document.createElement('div');
            modal.id = 'dispatchModal';
            modal.style.cssText = 'display: block; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1001;';
            
            var modalContent = document.createElement('div');
            modalContent.style.cssText = 'position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 8px; width: 90%; max-width: 600px; max-height: 80vh; overflow-y: auto;';
            
            var title = document.createElement('h2');
            title.innerHTML = '⚠️ Dons non distribués disponibles';
            
            var message = document.createElement('p');
            message.innerHTML = '<strong>' + data.quantite_disponible + ' unités</strong> de <strong>' + data.nom_matiere + '</strong> sont disponibles en dons non distribués. Veuillez les dispatcher à cette ville avant d\'acheter.';
            message.style.cssText = 'color: #666; margin-bottom: 20px; line-height: 1.6;';
            
            // Afficher la liste des dons non distribués
            var table = document.createElement('table');
            table.style.cssText = 'width: 100%; border-collapse: collapse; margin-bottom: 20px;';
            
            var thead = document.createElement('thead');
            thead.innerHTML = '<tr style="background: #f0f0f0;"><th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Date du Don</th><th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Quantité</th><th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Dispatcher</th></tr>';
            table.appendChild(thead);
            
            var tbody = document.createElement('tbody');
            data.dons.forEach(function(don) {
                var tr = document.createElement('tr');
                tr.innerHTML = '<td style="padding: 10px; border: 1px solid #ddd;">' + new Date(don.date_don).toLocaleDateString('fr-FR') + '</td>' +
                              '<td style="padding: 10px; border: 1px solid #ddd;"><strong>' + don.quantite + '</strong></td>' +
                              '<td style="padding: 10px; border: 1px solid #ddd;"><a href="/dispatch?id_don=' + don.id_don + '&id_ville=' + data.id_ville + '" target="_blank" class="btn btn-primary" style="padding: 5px 10px; background: #007bff; color: white; text-decoration: none; border-radius: 4px;">Dispatcher</a></td>';
                tbody.appendChild(tr);
            });
            table.appendChild(tbody);
            
            // Boutons d'action
            var buttonContainer = document.createElement('div');
            buttonContainer.style.cssText = 'display: flex; gap: 10px; justify-content: flex-end;';
            
            var btnDispatcher = document.createElement('button');
            btnDispatcher.textContent = '🔄 Aller aux dons';
            btnDispatcher.style.cssText = 'padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;';
            btnDispatcher.onclick = function() {
                window.location.href = '/dispatch';
            };
            
            var btnFermer = document.createElement('button');
            btnFermer.textContent = 'Fermer';
            btnFermer.style.cssText = 'padding: 10px 20px; background: #ccc; color: #333; border: none; border-radius: 4px; cursor: pointer;';
            btnFermer.onclick = function() {
                document.getElementById('dispatchModal').remove();
            };
            
            buttonContainer.appendChild(btnDispatcher);
            buttonContainer.appendChild(btnFermer);
            
            // Assembler le modal
            modalContent.appendChild(title);
            modalContent.appendChild(message);
            modalContent.appendChild(table);
            modalContent.appendChild(buttonContainer);
            modal.appendChild(modalContent);
            
            // Ajouter au DOM
            document.body.appendChild(modal);
            
            // Fermer en cliquant dehors
            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.remove();
                }
            });
        }


        function calculerMontant() {
            var pourcentage = parseInt(document.getElementById('pourcentage').value);
            var valeurRestante = window.currentValeurRestante || 0;
            var frais = parseFloat(document.getElementById('fraisInput').value) || 0;

            document.getElementById('pourcentageValue').textContent = pourcentage;

            var montantAchete = (valeurRestante * pourcentage) / 100;
            var montantAvecFrais = montantAchete * (1 + frais / 100);

            document.getElementById('montantCalcule').textContent = Math.round(montantAchete).toLocaleString('fr-FR');
            document.getElementById('montantTotal').textContent = Math.round(montantAvecFrais).toLocaleString('fr-FR');
            // On envoie le montant de BASE (sans frais) à l'API, le backend applique les frais
            document.getElementById('montantFinal').value = Math.round(montantAchete);
            document.getElementById('fraisFinal').value = frais;
        }

        // Fermer le modal en cliquant dehors
        document.addEventListener('click', function (event) {
            var modal = document.getElementById('achatModal');
            if (event.target === modal) {
                fermerModal();
            }
        });
    </script>
    <?php include("footer.php"); ?>
</body>

</html>