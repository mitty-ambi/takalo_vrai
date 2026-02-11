<?php
/**
 * View: AdminStats
 * Affiche un graphique du nombre d'inscriptions par jour et la liste des utilisateurs.
 */
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques - Inscriptions</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js" nonce="<?= Flight::app()->get('csp_nonce') ?>"></script>
    <link rel="stylesheet" href="/assets/css/auth.css">
    <link rel="stylesheet" href="/assets/css/AdminStats.css">
</head>

<body>
    <?php include("components/side_nav.php") ?>
    <div class="stats-container">
        <div class="chart-card">
            <div class="chart-header">
                <h2>Inscriptions par jour</h2>
                <div class="small-muted">Total utilisateurs : <span
                        class="stat-bubble"><?= count($statsParJour) ?></span></div>
            </div>
            <canvas id="registrationsChart" height="120"></canvas>
        </div>

        <div>
            <h3 style="margin-top:18px">Liste des utilisateurs</h3>
            <table class="user-list">
                <thead>
                    <tr>
                        <th>id user</th>
                        <th>nom</th>
                        <th>prenom</th>
                        <th>email</th>
                        <th>type d'utilisateur</th>
                        <th>Date creation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($statsParJour as $stats) { ?>
                        <tr>
                            <td><?= htmlspecialchars($stats['id_user']) ?></td>
                            <td><?= htmlspecialchars($stats['nom']) ?></td>
                            <td><?= htmlspecialchars($stats['prenom']) ?></td>
                            <td><?= htmlspecialchars($stats['email']) ?></td>
                            <td><?= htmlspecialchars($stats['type_user']) ?></td>
                            <td><?= htmlspecialchars($stats['date_creation']) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div>
            <h3 style="margin-top:18px">Liste des utilisateurs</h3>
            <table class="user-list">
                <thead>
                    <tr>
                        <th>id user</th>
                        <th>nom</th>
                        <th>prenom</th>
                        <th>email</th>
                        <th>type d'utilisateur</th>
                        <th>Date creation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($statsParJour as $stats) { ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($stats['id_user']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($stats['nom']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($stats['prenom']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($stats['email']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($stats['type_user']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($stats['date_creation']) ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>

    <script nonce="<?= Flight::app()->get('csp_nonce') ?>">
        const registerCounts = <?= json_encode($registerCounts ?? []) ?>;

        console.log("Données RAW:", registerCounts);

        if (registerCounts && registerCounts.length > 0) {
            registerCounts.sort((a, b) => new Date(a.date) - new Date(b.date));
        }

        const labels = (registerCounts && registerCounts.length > 0)
            ? registerCounts.map(r => {
                const date = new Date(r.date);
                const dd = date.getDate().toString().padStart(2, '0');
                const mm = (date.getMonth() + 1).toString().padStart(2, '0');
                const yyyy = date.getFullYear();
                return `${dd}/${mm}/${yyyy}`;
            })
            : ['Aucun'];

        const dataValues = (registerCounts && registerCounts.length > 0)
            ? registerCounts.map(r => parseInt(r.count, 10))
            : [0];

        const ctx = document.getElementById('registrationsChart').getContext('2d');

        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0.05)');

        // Création du graphique (toujours affiché)
        const registrationsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Inscriptions par jour',
                    data: dataValues,
                    borderColor: 'rgba(99, 102, 241, 1)',
                    backgroundColor: gradient,
                    pointBackgroundColor: 'rgba(99, 102, 241, 1)',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                animation: {
                    duration: 800,
                    easing: 'easeOutQuart'
                },
                scales: {
                    x: {
                        display: true,
                        grid: { display: true, color: 'rgba(0,0,0,0.05)' }
                    },
                    y: {
                        display: true,
                        beginAtZero: true,
                        grid: { display: true, color: 'rgba(0,0,0,0.05)' },
                        ticks: { precision: 0 }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return `${context.parsed.y} inscription${context.parsed.y > 1 ? 's' : ''}`;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>