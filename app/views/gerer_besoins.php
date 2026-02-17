<?php $base_url = rtrim(Flight::get('flight.base_url'), '/'); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/all.min.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/navbar.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/gerer_besoins.css">
    <title>Gérer les Besoins - BNGRC</title>
    <style>
        .container-flex {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 30px;
        }

        @media (max-width: 1024px) {
            .container-flex {
                grid-template-columns: 1fr;
            }
        }

        .besoins-list {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .besoins-list h2 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .besoin-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 6px;
            background-color: #f9f9f9;
            transition: all 0.3s;
        }

        .besoin-item:hover {
            background-color: #f5f5f5;
            border-color: #007bff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .besoin-info {
            margin-bottom: 10px;
            font-size: 14px;
        }

        .besoin-info strong {
            color: #333;
        }

        .besoin-info span {
            color: #666;
        }

        .besoin-prix {
            background-color: #e7f3ff;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
            font-weight: 500;
            color: #0056b3;
        }

        .besoin-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-achat {
            flex: 1;
            min-width: 120px;
            padding: 8px 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            transition: background-color 0.3s;
            text-align: center;
            text-decoration: none;
        }

        .btn-achat:hover {
            background-color: #218838;
        }

        .btn-achat:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border-left: 4px solid;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }

        .no-besoins {
            text-align: center;
            padding: 30px;
            color: #999;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 10px;
            color: #666;
        }

        .loading.active {
            display: block;
        }
    </style>
</head>

<body>
    <?php include("navbar.php"); ?>

    <div class="container">
        <div class="page-header">
            <h1>📋 Gérer les Besoins</h1>
            <p>Créez et gérez les besoins par ville et matière</p>
        </div>

        <!-- Messages d'alerte -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <strong>✗ Erreur!</strong> <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <strong>✓ Succès!</strong> <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>

        <div class="container-flex">
            <!-- Formulaire de création de besoin -->
            <div>
                <div class="form-container">
                    <h2>➕ Créer un Besoin</h2>
                    <form method="POST" action="<?= $base_url ?>/registre" class="edit-form">
                        <div class="form-group">
                            <label for="id_categorie">Catégorie:</label>
                            <select name="id_categorie" id="id_categorie" required>
                                <option value="">-- Sélectionner une Catégorie --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['id_categorie']) ?>">
                                        <?= htmlspecialchars($cat['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="id_ville">Ville:</label>
                            <select name="id_ville" id="id_ville" required>
                                <option value="">-- Sélectionner une ville --</option>
                                <?php foreach ($villes as $ville): ?>
                                    <option value="<?= htmlspecialchars($ville['id_ville']) ?>">
                                        <?= htmlspecialchars($ville['nom_ville']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="id_matiere">Matière:</label>
                            <select name="id_matiere" id="id_matiere" required onchange="updateMatierePrix()">
                                <option value="">-- Sélectionner une matière --</option>
                                <?php foreach ($matieres as $matiere): ?>
                                    <option value="<?= htmlspecialchars($matiere['id_matiere']) ?>"
                                        data-prix="<?= htmlspecialchars($matiere['prix_unitaire']) ?>">
                                        <?= htmlspecialchars($matiere['nom_matiere']) ?>
                                        (<?= number_format($matiere['prix_unitaire'], 0, '.', ' ') ?> Ar)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>


                        <div class="form-group">
                            <label for="quantite">Quantité:</label>
                            <input type="number" name="quantite" id="quantite" required min="1"
                                placeholder="Entrez la quantité" onchange="updateTotal()">
                        </div>

                        <div class="form-group" style="background-color: #e7f3ff; padding: 10px; border-radius: 4px;">
                            <small><strong>Prix estimé du besoin:</strong> <span id="estimated-prix">0</span> Ar</small>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">✓ Créer le Besoin</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des besoins restants -->
            <div class="besoins-list">
                <h2>📊 Besoins Restants</h2>
                <div id="besoins-container">
                    <div class="no-besoins">Chargement des besoins...</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const base_url = '<?= $base_url ?>';
        const matieres = <?= json_encode($matieres) ?>;

        function updateMatierePrix() {
            updateTotal();
        }

        function updateTotal() {
            const matiere_select = document.getElementById('id_matiere');
            const quantite = document.getElementById('quantite').value || 0;
            const selected_option = matiere_select.options[matiere_select.selectedIndex];
            const prix = selected_option.getAttribute('data-prix') || 0;
            const total = prix * quantite;
            document.getElementById('estimated-prix').textContent = new Intl.NumberFormat('fr-FR').format(total);
        }

        // Charger les besoins restants via AJAX
        function loadBesoins() {
            const container = document.getElementById('besoins-container');
            container.innerHTML = '<div class="loading active">⏳ Chargement...</div>';

            // Récupérer les besoins via une API
            fetch(base_url + '/api/besoins/remaining')
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        let html = '';
                        data.forEach(besoin => {
                            const prix_total = besoin.prix_unitaire * besoin.quantite;

                            html += `
                                <div class="besoin-item">
                                    <div class="besoin-info">
                                        <strong>Catégorie:</strong> <span>${escapeHtml(besoin.nom_categorie || 'N/A')} (ID: ${besoin.id_categorie})</span>
                                    </div>
                                    <div class="besoin-info">
                                        <strong>Ville:</strong> <span>${escapeHtml(besoin.nom_ville)}</span>
                                    </div>
                                    <div class="besoin-info">
                                        <strong>Matière:</strong> <span>${escapeHtml(besoin.nom_matiere)}</span>
                                    </div>
                                    <div class="besoin-info">
                                        <strong>Quantité:</strong> <span>${besoin.quantite}</span>
                                    </div>
                                    <div class="besoin-info">
                                        <strong>Prix Unitaire:</strong> <span>${new Intl.NumberFormat('fr-FR').format(besoin.prix_unitaire)} Ar</span>
                                    </div>
                                    <div class="besoin-prix">
                                        💰 Total (sans frais): ${new Intl.NumberFormat('fr-FR').format(prix_total)} Ar
                                    </div>
                                    <div class="besoin-actions">
                                        <form method="POST" action="${base_url}/gerer_achats/create" style="flex: 1; display: flex; gap: 8px; align-items: flex-end; flex-wrap: wrap;">
                                            <input type="hidden" name="id_besoin" value="${besoin.id_besoin}">
                                            <div style="flex: 1; min-width: 100px;">
                                                <label style="font-size: 12px; font-weight: 500;">Frais (%):</label>
                                                <input type="number" name="frais" value="10" min="0" max="100" step="0.1" 
                                                    style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                                            </div>
                                            <button type="submit" class="btn-achat">🛒 Acheter</button>
                                        </form>
                                    </div>
                                </div>
                            `;
                        });
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = '<div class="no-besoins">✅ Aucun besoin restant!</div>';
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    container.innerHTML = '<div class="no-besoins">Erreur lors du chargement des besoins</div>';
                });
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        // Charger les besoins au démarrage
        document.addEventListener('DOMContentLoaded', loadBesoins);

        // Rafraîchir les besoins toutes les 10 secondes
        setInterval(loadBesoins, 10000);
    </script>
</body>

</html>