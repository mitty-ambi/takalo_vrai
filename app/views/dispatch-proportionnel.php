<?php $base_url = rtrim(Flight::get('flight.base_url'), '/'); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/navbar.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/all.min.css">
    <title>Dispatch Proportionnel - BNGRC</title>
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
            background: white;
            border-radius: 4px;
            overflow: hidden;
        }

        .repartition-table thead {
            background: #003366;
            color: white;
        }

        .repartition-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #FF6600;
        }

        .repartition-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .repartition-table tbody tr:hover {
            background: #f5f5f5;
        }

        .repartition-table .ville {
            font-weight: bold;
            color: #003366;
        }

        .repartition-table .quantite {
            background: #e8f4f8;
            padding: 5px 10px;
            border-radius: 4px;
            color: #003366;
        }

        .repartition-table .date {
            font-size: 0.9rem;
            color: #666;
        }

        .repartition-table .attribution {
            background: #fffde7;
            font-weight: bold;
            color: #f57f17;
            padding: 5px 10px;
            border-radius: 4px;
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
    </style>
</head>

<body>
    <?php include("navbar.php"); ?>

    <div class="container">
        <div class="header">
            <h1>📊 Dispatch Proportionnel</h1>
            <p>Distribution basée sur le ratio: quantité demandée / nombre de dons disponibles</p>
        </div>

        <div class="info-box">
            <strong>ℹ️ Méthode:</strong> Chaque ville reçoit quantité_demandée / nombre_de_dons_disponibles (arrondi à
            la valeur basse).
            Par exemple: si Tana demande 5 unités et il y a 6 dons disponibles, Tana reçoit floor(5/6) = 0 unités.
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
                        <?php $nb_dons = count($dons_non_distribues[$id_matiere]); ?>
                        <?php foreach ($dons_non_distribues[$id_matiere] as $don): ?>
                            <div class="don-item">
                                <strong>Don #<?= $don['id_don'] ?></strong> -
                                <strong><?= $don['quantite'] ?> unités</strong>
                                (<?= date('d/m/Y', strtotime($don['date_don'])) ?>)
                            </div>
                            <?php $total_dons += $don['quantite']; ?>
                        <?php endforeach; ?>
                        <div class="don-item" style="background: #fffde7; border-left-color: #FBC02D;">
                            <strong>Total disponible: <?= $total_dons ?> unités | Nombre de dons: <?= $nb_dons ?></strong>
                        </div>
                    </div>

                    <!-- Besoins et distribution -->
                    <div class="besoins-distribution">
                        <h3>🏙️ Distribution proportionnelle aux villes</h3>
                        <table class="repartition-table">
                            <thead>
                                <tr>
                                    <th>Ville</th>
                                    <th>Quantité demandée</th>
                                    <th>Formule: (demande × total) / (somme demandes)</th>
                                    <th>Attribution</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $somme_demandes = 0;
                                foreach ($matiere_data['besoins'] as $besoin) {
                                    $somme_demandes += $besoin['quantite'];
                                }

                                $distributions = [];
                                $total_attribue = 0;
                                foreach ($matiere_data['besoins'] as $besoin) {
                                    $part_exacte = ($besoin['quantite'] * $total_dons) / $somme_demandes;
                                    $part_entiere = floor($part_exacte);
                                    $decimale = $part_exacte - $part_entiere;

                                    $distributions[] = [
                                        'besoin' => $besoin,
                                        'part_exacte' => $part_exacte,
                                        'part_entiere' => $part_entiere,
                                        'decimale' => $decimale
                                    ];
                                    $total_attribue += $part_entiere;
                                }

                                usort($distributions, function ($a, $b) {
                                    return $b['decimale'] <=> $a['decimale'];
                                });

                                $reste = $total_dons - $total_attribue;
                                for ($i = 0; $i < $reste && $i < count($distributions); $i++) {
                                    if ($distributions[$i]['decimale'] > 0) {
                                        $distributions[$i]['part_entiere']++;
                                    }
                                }

                                foreach ($distributions as $dist):
                                    $a = 0;
                                    $besoin = $dist['besoin'];
                                    $attribution = $dist['part_entiere'];
                                    ?>
                                    <tr data-id-besoin="<?= $besoin['id_besoin'] ?>" data-quantite="<?= $attribution ?>"
                                        data-id-ville="<?= $besoin['id_ville'] ?>">
                                        <td class="ville"><?= htmlspecialchars($besoin['nom_ville']) ?></td>
                                        <td class="quantite"><?= $besoin['quantite'] ?> unités</td>
                                        <td class="date">(<?= $besoin['quantite'] ?> × <?= $total_dons ?>) /
                                            <?= $somme_demandes ?> = <?= number_format($dist['part_exacte'], 2) ?>
                                        </td>
                                        <td class="attribution"><?= $attribution ?> unités</td>
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
</body>

</html>