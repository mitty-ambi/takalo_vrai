<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/navbar.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <title>BNGRC - Tableau de Bord</title>
</head>

<body>
    <?php include __DIR__ . '/navbar.php'; ?>

    <main class="dashboard-container">
        <!-- Header Section -->
        <div class="dashboard-header">
            <div class="header-content">
                <h1>📊 Tableau de Bord</h1>
                <p class="subtitle">Gestion intégrée des donations et besoins en cas de sinistre</p>
            </div>
        </div>

        <!-- Key Statistics -->
        <section class="stats-section">
            <h2 class="section-title">Statistiques Clés</h2>
            <div class="stats-grid">
                <div class="stat-card stat-blue">
                    <div class="stat-number"><?= isset($total_dons) ? number_format($total_dons, 0, ',', ' ') : '0' ?></div>
                    <div class="stat-label">Donations Totales</div>
                    <div class="stat-icon">📦</div>
                </div>

                <div class="stat-card stat-green">
                    <div class="stat-number"><?= isset($dons_distribuees) ? number_format($dons_distribuees, 0, ',', ' ') : '0' ?></div>
                    <div class="stat-label">Distribuées</div>
                    <div class="stat-icon">✓</div>
                </div>

                <div class="stat-card stat-orange">
                    <div class="stat-number"><?= isset($dons_non_distribuees) ? number_format($dons_non_distribuees, 0, ',', ' ') : '0' ?></div>
                    <div class="stat-label">En Attente</div>
                    <div class="stat-icon">⏳</div>
                </div>

                <div class="stat-card stat-red">
                    <div class="stat-number"><?= isset($total_besoins) ? number_format($total_besoins, 0, ',', ' ') : '0' ?></div>
                    <div class="stat-label">Besoins Déclarés</div>
                    <div class="stat-icon">🆘</div>
                </div>

                <div class="stat-card stat-purple">
                    <div class="stat-number"><?= isset($nb_villes) ? number_format($nb_villes, 0, ',', ' ') : '0' ?></div>
                    <div class="stat-label">Villes Affectées</div>
                    <div class="stat-icon">📍</div>
                </div>

                <div class="stat-card stat-teal">
                    <div class="stat-number"><?= isset($nb_matieres) ? number_format($nb_matieres, 0, ',', ' ') : '0' ?></div>
                    <div class="stat-label">Types de Matière</div>
                    <div class="stat-icon">📋</div>
                </div>
            </div>
        </section>

        <!-- Charts Section -->
        <section class="charts-section">
            <h2 class="section-title">Analyse Visuelle</h2>
            <div class="charts-grid">
                <!-- Distribution Status -->
                <div class="chart-card">
                    <h3 class="chart-title">État de Distribution</h3>
                    <div class="chart-wrapper">
                        <canvas id="distributionChart"></canvas>
                    </div>
                </div>

                <!-- Donations by Material -->
                <div class="chart-card">
                    <h3 class="chart-title">Donations par Matière</h3>
                    <div class="chart-wrapper">
                        <canvas id="matiereChart"></canvas>
                    </div>
                </div>

                <!-- Needs by Material -->
                <div class="chart-card">
                    <h3 class="chart-title">Besoins par Matière</h3>
                    <div class="chart-wrapper">
                        <canvas id="besoinsChart"></canvas>
                    </div>
                </div>

                <!-- Donations by City -->
                <div class="chart-card">
                    <h3 class="chart-title">Donations par Ville</h3>
                    <div class="chart-wrapper">
                        <canvas id="villeChart"></canvas>
                    </div>
                </div>
            </div>
        </section>

        <!-- Top Lists Section -->
        <section class="rankings-section">
            <h2 class="section-title">Classements</h2>
            <div class="rankings-grid">
                <!-- Top Materials -->
                <div class="ranking-card">
                    <h3 class="ranking-title">🏆 Top Matières Distribuées</h3>
                    <div class="ranking-list">
                        <?php if (isset($top_matieres_dons) && !empty($top_matieres_dons)): ?>
                            <?php foreach ($top_matieres_dons as $idx => $item): ?>
                                <div class="ranking-item" style="--rank: <?= $idx + 1 ?>">
                                    <div class="ranking-badge"><?= $idx + 1 ?></div>
                                    <div class="ranking-content">
                                        <div class="ranking-name"><?= htmlspecialchars($item['nom_matiere'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></div>
                                        <div class="ranking-bar">
                                            <div class="ranking-fill" style="width: <?= min(100, ($item['total_quantite'] ?? 0) * 10) ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="ranking-value"><?= number_format($item['total_quantite'] ?? 0, 0, ',', ' ') ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="empty-message">Aucune donnée</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Top Cities with Needs -->
                <div class="ranking-card">
                    <h3 class="ranking-title">📍 Top Villes en Besoin</h3>
                    <div class="ranking-list">
                        <?php if (isset($top_villes_besoins) && !empty($top_villes_besoins)): ?>
                            <?php foreach ($top_villes_besoins as $idx => $item): ?>
                                <div class="ranking-item" style="--rank: <?= $idx + 1 ?>">
                                    <div class="ranking-badge"><?= $idx + 1 ?></div>
                                    <div class="ranking-content">
                                        <div class="ranking-name"><?= htmlspecialchars($item['nom_ville'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></div>
                                        <div class="ranking-bar">
                                            <div class="ranking-fill" style="width: <?= min(100, ($item['total_quantite'] ?? 0) * 5) ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="ranking-value"><?= number_format($item['total_quantite'] ?? 0, 0, ',', ' ') ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="empty-message">Aucune donnée</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Recent Activity Section -->
        <section class="activity-section">
            <h2 class="section-title">Activité Récente</h2>
            <div class="activity-timeline">
                <?php if (isset($recent_donations) && !empty($recent_donations)): ?>
                    <?php foreach (array_slice($recent_donations, 0, 5) as $donation): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <div class="timeline-time">
                                    <?= date('d M Y', strtotime($donation['date_don'] ?? 'now')) ?>
                                </div>
                                <div class="timeline-desc">
                                    <strong><?= htmlspecialchars($donation['nom_matiere'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></strong>
                                    - Quantité: <?= number_format($donation['quantite'] ?? 0, 0, ',', ' ') ?>
                                    <?php if (!empty($donation['nom_ville']) && $donation['nom_ville'] !== 'Non assignée'): ?>
                                        → <em><?= htmlspecialchars($donation['nom_ville'], ENT_QUOTES, 'UTF-8') ?></em>
                                    <?php else: ?>
                                        → <em style="color: #FF6600;">En attente de distribution</em>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="empty-state">Aucune donnée récente</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
        // Distribution Status Chart
        const distributionCtx = document.getElementById('distributionChart').getContext('2d');
        new Chart(distributionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Distribuées', 'Non Distribuées'],
                datasets: [{
                    data: [
                        <?= isset($dons_distribuees) ? $dons_distribuees : 0 ?>,
                        <?= isset($dons_non_distribuees) ? $dons_non_distribuees : 0 ?>
                    ],
                    backgroundColor: ['#00AA44', '#FF6600'],
                    borderColor: ['#fff', '#fff'],
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 14, family: "'Segoe UI', sans-serif" },
                            padding: 20,
                            color: '#333'
                        }
                    }
                }
            }
        });

        // Donations by Matiere Chart
        const matiereCtx = document.getElementById('matiereChart').getContext('2d');
        new Chart(matiereCtx, {
            type: 'bar',
            data: {
                labels: <?= isset($matieres_labels) ? json_encode($matieres_labels) : '[]' ?>,
                datasets: [{
                    label: 'Quantité de Donations',
                    data: <?= isset($matieres_data) ? json_encode($matieres_data) : '[]' ?>,
                    backgroundColor: 'rgba(0, 102, 204, 0.8)',
                    borderColor: '#003366',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { color: '#666', font: { size: 12 } },
                        grid: { color: '#eee' }
                    },
                    y: {
                        ticks: { color: '#666', font: { size: 12 } }
                    }
                },
                plugins: {
                    legend: {
                        labels: { font: { size: 12 } }
                    }
                }
            }
        });

        // Needs by Matiere Chart
        const besoinsCtx = document.getElementById('besoinsChart').getContext('2d');
        new Chart(besoinsCtx, {
            type: 'bar',
            data: {
                labels: <?= isset($besoins_labels) ? json_encode($besoins_labels) : '[]' ?>,
                datasets: [{
                    label: 'Quantité de Besoins',
                    data: <?= isset($besoins_data) ? json_encode($besoins_data) : '[]' ?>,
                    backgroundColor: 'rgba(204, 51, 0, 0.8)',
                    borderColor: '#CC3300',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { color: '#666', font: { size: 12 } },
                        grid: { color: '#eee' }
                    },
                    y: {
                        ticks: { color: '#666', font: { size: 12 } }
                    }
                },
                plugins: {
                    legend: {
                        labels: { font: { size: 12 } }
                    }
                }
            }
        });

        // Donations by City Chart
        const villeCtx = document.getElementById('villeChart').getContext('2d');
        new Chart(villeCtx, {
            type: 'line',
            data: {
                labels: <?= isset($villes_labels) ? json_encode($villes_labels) : '[]' ?>,
                datasets: [{
                    label: 'Donations par Ville',
                    data: <?= isset($villes_data) ? json_encode($villes_data) : '[]' ?>,
                    borderColor: '#FF6600',
                    backgroundColor: 'rgba(255, 102, 0, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#FF6600',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#666', font: { size: 12 } },
                        grid: { color: '#eee' }
                    },
                    x: {
                        ticks: { color: '#666', font: { size: 12 } }
                    }
                },
                plugins: {
                    legend: {
                        labels: { font: { size: 12 } }
                    }
                }
            }
        });
    </script>
</body>

</html>
