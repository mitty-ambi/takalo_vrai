<?php $base_url = rtrim(Flight::get('flight.base_url'), '/'); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/navbar.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/all.min.css">
    <title>Dispatch par Minimum de Demande - BNGRC</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            margin: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            margin: 0;
            color: #003366;
        }

        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }

        .matiere-section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .matiere-title {
            background: #f0f0f0;
            padding: 15px;
            border-left: 5px solid #FF6600;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .matiere-title h2 {
            margin: 0;
            color: #003366;
        }

        .matiere-title .badge {
            display: inline-block;
            background: #FF6600;
            color: white;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 10px;
        }

        .dons-list {
            margin-bottom: 20px;
        }

        .don-item {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #007bff;
            border-radius: 4px;
        }

        .don-item strong {
            color: #003366;
        }

        .besoins-distribution {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .repartition-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .repartition-table th {
            background: #003366;
            color: white;
            padding: 12px;
            text-align: left;
        }

        .repartition-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .repartition-table tr:hover {
            background: #f5f5f5;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .btn-valider {
            background: #28a745;
            color: white;
            flex: 1;
        }

        .btn-valider:hover {
            background: #218838;
        }

        .btn-retour {
            background: #ccc;
            color: #333;
            flex: 1;
        }

        .btn-retour:hover {
            background: #bbb;
        }

        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .info-box strong {
            color: #1976D2;
        }

        .attribution {
            background: #e8f5e9;
            padding: 5px 10px;
            border-radius: 4px;
            color: #2e7d32;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include("navbar.php"); ?>

    <div class="container">
        <div class="header">
            <h1>📊 Dispatch par Minimum de Demande</h1>
            <p>Les villes avec les plus petits besoins sont prioritaires</p>
        </div>

        <div class="info-box">
            <strong>ℹ️ Méthode:</strong> Les dons sont distribués aux villes ayant les besoins les plus petits en
            premier.
            Les besoins plus importants reçoivent ce qui reste après satisfaction des besoins plus petits.
        </div>

        <form method="POST" action="<?= $base_url ?>/api/dispatch/valider">

            <?php foreach ($besoins_par_matiere as $id_matiere => $matiere_data): ?>
                <?php if (!isset($dons_non_distribues[$id_matiere]) || empty($dons_non_distribues[$id_matiere])): ?>
                    <?php continue; ?>
                <?php endif; ?>

                <div class="matiere-section">
                    <div class="matiere-title">
                        <h2><?= htmlspecialchars($matiere_data['nom_matiere']) ?></h2>
                        <span class="badge"><?= htmlspecialchars($matiere_data['nom_categorie'] ?? 'N/A') ?></span>
                    </div>

                    <!-- Dons disponibles -->
                    <div class="dons-list">
                        <h3>📦 Dons non distribués (<?= count($dons_non_distribues[$id_matiere]) ?> don(s))</h3>
                        <?php $total_dons = 0; ?>
                        <?php foreach ($dons_non_distribues[$id_matiere] as $don): ?>
                            <div class="don-item">
                                <strong>Don #<?= $don['id_don'] ?></strong> -
                                <strong><?= $don['quantite'] ?> unités</strong>
                                (<?= date('d/m/Y', strtotime($don['date_don'])) ?>)
                            </div>
                            <?php $total_dons += $don['quantite']; ?>
                        <?php endforeach; ?>
                        <div class="don-item" style="background: #fffde7; border-left-color: #FBC02D;">
                            <strong>Total disponible: <?= $total_dons ?> unités</strong>
                        </div>
                    </div>

                    <!-- Besoins et distribution -->
                    <div class="besoins-distribution">
                        <h3>🏙️ Distribution aux villes (par ordre de besoin minimal)</h3>
                        <table class="repartition-table">
                            <thead>
                                <tr>
                                    <th>Ville</th>
                                    <th>Quantité demandée</th>
                                    <th>Date de demande</th>
                                    <th>Attribution</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $quantite_distribuee = 0;
                                foreach ($matiere_data['besoins'] as $besoin):
                                    $quantite_a_attribuer = min($besoin['quantite'], $total_dons - $quantite_distribuee);
                                    $quantite_distribuee += $quantite_a_attribuer;
                                    ?>
                                    <tr data-id-besoin="<?= $besoin['id_besoin'] ?>"
                                        data-quantite="<?= $quantite_a_attribuer ?>" data-id-ville="<?= $besoin['id_ville'] ?>">
                                        <td><strong><?= htmlspecialchars($besoin['nom_ville']) ?></strong></td>
                                        <td><?= $besoin['quantite'] ?> unités</td>
                                        <td><?= date('d/m/Y H:i', strtotime($besoin['date_du_demande'])) ?></td>
                                        <td>
                                            <span class="attribution">
                                                <?php if ($quantite_a_attribuer > 0): ?>
                                                    ✓ <?= $quantite_a_attribuer ?> unités
                                                <?php else: ?>
                                                    ✗ 0 unités (insuffisant)
                                                <?php endif; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Bouton pour valider cette matière -->
                    <div style="display: flex; gap: 10px; margin-top: 15px;">
                        <form method="POST" action="<?= $base_url ?>/api/dispatch/valider-matiere" style="flex: 1;">
                            <input type="hidden" name="id_matiere" value="<?= $id_matiere ?>">
                            <button type="submit" class="btn btn-valider" style="width: 100%; background: #FF6600;">✓
                                Valider cette matière</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="btn-group">
                <button type="button" class="btn btn-retour" onclick="history.back()">← Retour</button>
                <button type="submit" class="btn btn-valider">✓ Valider le Dispatch</button>
            </div>
        </form>
    </div>

    <script>
        // Pas de JavaScript complexe - simple POST
    </script>
</body>

</html>