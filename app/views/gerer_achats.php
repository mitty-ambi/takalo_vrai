<?php $base_url = rtrim(Flight::get('flight.base_url'), '/'); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/all.min.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/navbar.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #ddd;
        }

        .page-header h1 {
            color: #333;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .page-header p {
            color: #666;
            margin: 0;
        }

        .filters {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .filter-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .filter-group .form-group {
            flex: 1;
            min-width: 200px;
            margin-bottom: 0;
        }

        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .btn-filter {
            padding: 8px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn-filter:hover {
            background-color: #0056b3;
        }

        .btn-reset {
            padding: 8px 20px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn-reset:hover {
            background-color: #5a6268;
        }

        .achats-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #333;
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: #f5f5f5;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-pending {
            background-color: #ffc107;
            color: #333;
        }

        .badge-completed {
            background-color: #28a745;
            color: white;
        }

        .badge-failed {
            background-color: #dc3545;
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-edit {
            background-color: #17a2b8;
            color: white;
        }

        .btn-edit:hover {
            background-color: #138496;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #666;
        }

        .empty-state h3 {
            color: #999;
            margin-bottom: 10px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border-left: 4px solid;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left-color: #17a2b8;
        }

        .montant {
            font-weight: 600;
            color: #28a745;
        }

        .frais {
            font-size: 12px;
            color: #666;
        }

        .no-results {
            text-align: center;
            padding: 30px;
            color: #666;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <?php include("navbar.php"); ?>

    <div class="container">
        <div class="page-header">
            <h1>💰 Gestion des Achats</h1>
            <p>Achetez les besoins avec les dons en argent selon les prix unitaires</p>
        </div>

        <!-- Messages d'alerte -->
        <?php if (isset($message_success)): ?>
            <div class="alert alert-success">
                <strong>✓ Succès!</strong> <?= htmlspecialchars($message_success) ?>
            </div>
        <?php endif; ?>
        <?php if (isset($message_error)): ?>
            <div class="alert alert-danger">
                <strong>✗ Erreur!</strong> <?= htmlspecialchars($message_error) ?>
            </div>
        <?php endif; ?>

        <!-- Filtres -->
        <div class="filters">
            <form method="GET" action="<?= $base_url ?>/gerer_achats" class="filter-group">
                <div class="form-group">
                    <label for="id_ville">Filtrer par ville:</label>
                    <select name="id_ville" id="id_ville">
                        <option value="">-- Toutes les villes --</option>
                        <?php foreach ($villes as $ville): ?>
                            <option value="<?= htmlspecialchars($ville['id_ville']) ?>" 
                                <?= (isset($filter_ville) && $filter_ville == $ville['id_ville']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ville['nom_ville']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="statut">Filtrer par statut:</label>
                    <select name="statut" id="statut">
                        <option value="">-- Tous les statuts --</option>
                        <option value="en_attente" <?= (isset($filter_statut) && $filter_statut == 'en_attente') ? 'selected' : '' ?>>En attente</option>
                        <option value="completed" <?= (isset($filter_statut) && $filter_statut == 'completed') ? 'selected' : '' ?>>Complété</option>
                        <option value="failed" <?= (isset($filter_statut) && $filter_statut == 'failed') ? 'selected' : '' ?>>Échoué</option>
                    </select>
                </div>

                <button type="submit" class="btn-filter">🔍 Filtrer</button>
                <a href="<?= $base_url ?>/gerer_achats" class="btn-reset">↻ Réinitialiser</a>
            </form>
        </div>

        <!-- Tableau des achats -->
        <?php if (empty($achats)): ?>
            <div class="no-results">
                <h3>Aucun achat trouvé</h3>
                <p>Créez des achats à partir de la page des besoins restants</p>
            </div>
        <?php else: ?>
            <div class="achats-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ville</th>
                            <th>Matière</th>
                            <th>Quantité</th>
                            <th>Prix Unitaire</th>
                            <th>Frais (%)</th>
                            <th>Prix Total</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($achats as $achat): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($achat['id_achat']) ?></td>
                                <td><?= htmlspecialchars($achat['nom_ville']) ?></td>
                                <td><?= htmlspecialchars($achat['nom_matiere']) ?></td>
                                <td><?= htmlspecialchars($achat['quantite']) ?></td>
                                <td><?= number_format($achat['prix_unitaire'], 2, '.', ' ') ?> Ar</td>
                                <td><span class="frais"><?= htmlspecialchars($achat['frais_pourcentage']) ?>%</span></td>
                                <td><span class="montant"><?= number_format($achat['prix_total_achat'], 2, '.', ' ') ?> Ar</span></td>
                                <td><?= date('d/m/Y H:i', strtotime($achat['date_achat'])) ?></td>
                                <td>
                                    <?php
                                    $badge_class = 'badge-pending';
                                    $statut_text = $achat['statut'];
                                    if ($achat['statut'] === 'completed') {
                                        $badge_class = 'badge-completed';
                                        $statut_text = 'Complété';
                                    } elseif ($achat['statut'] === 'failed') {
                                        $badge_class = 'badge-failed';
                                        $statut_text = 'Échoué';
                                    } else {
                                        $statut_text = 'En attente';
                                    }
                                    ?>
                                    <span class="badge <?= $badge_class ?>"><?= htmlspecialchars($statut_text) ?></span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <form method="POST" action="<?= $base_url ?>/gerer_achats/delete" style="display: inline;">
                                            <input type="hidden" name="id_achat" value="<?= htmlspecialchars($achat['id_achat']) ?>">
                                            <button type="submit" class="btn-sm btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet achat?')">🗑️ Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 4px;">
                <strong>Résumé:</strong>
                <ul style="margin-bottom: 0; padding-left: 20px;">
                    <li>Total d'achats: <strong><?= count($achats) ?></strong></li>
                    <li>Montant total: <strong><?= number_format(array_sum(array_column($achats, 'prix_total_achat')), 2, '.', ' ') ?> Ar</strong></li>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <br><br>
    <?php include("footer.php") ?>
</body>

</html>
