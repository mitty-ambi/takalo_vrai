<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les échanges - Takalo Vrai</title>
    <link rel="stylesheet" href="/assets/css/sidebar.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/gerer_echanges.css">
    
</head>
<body>
    <header class="dashboard-header">
        <div class="header-content">
            <div>
                <h1 class="header-title">Takalo Vrai</h1>
                <p class="header-subtitle">Mes échanges</p>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    <?= strtoupper(substr($donnees_utilisateur['prenom'] ?? 'U', 0, 1)); ?>
                </div>
                <div class="user-name">
                    <?= htmlspecialchars($donnees_utilisateur['nom'] ?? 'Utilisateur'); ?>
                </div>
            </div>
        </div>
    </header>

    <?php include __DIR__ . '/components/side_nav.php'; ?>

    <main class="dashboard-container">
        <h2 class="section-title">Gérer mes échanges</h2>

        <div class="tabs">
            <button class="tab-button active" data-tab="received">
                📥 Propositions reçues (<?= count($echanges_recus ?? []); ?>)
            </button>
            <button class="tab-button" data-tab="sent">
                📤 Propositions envoyées (<?= count($echanges_envoyes ?? []); ?>)
            </button>
            <button class="tab-button" data-tab="history">
                📋 Historique
            </button>
        </div>

        <!-- Onglet Propositions Reçues -->
        <div id="received" class="tab-content active">
            <?php if (!empty($echanges_recus) && count($echanges_recus) > 0): ?>
                <?php foreach ($echanges_recus as $exchange): ?>
                    <div class="exchange-card">
                        <div class="exchange-header">
                            <div class="exchange-parties">
                                <div class="user-info">
                                    <div class="user-avatar"><?= strtoupper(substr($expediteurs[$exchange['id_user_1']]['prenom'] ?? 'P', 0, 1)); ?></div>
                                    <div class="user-details">
                                        <h4><?= htmlspecialchars($expediteurs[$exchange['id_user_1']]['prenom'] . ' ' . $expediteurs[$exchange['id_user_1']]['nom']); ?></h4>
                                        <p>Vous propose un échange</p>
                                    </div>
                                </div>
                                <div class="arrow">⇄</div>
                                <div class="user-info">
                                    <div class="user-avatar"><?= strtoupper(substr($donnees_utilisateur['prenom'] ?? 'U', 0, 1)); ?></div>
                                    <div class="user-details">
                                        <h4><?= htmlspecialchars($donnees_utilisateur['prenom'] . ' ' . $donnees_utilisateur['nom']); ?></h4>
                                        <p>Vous</p>
                                    </div>
                                </div>
                            </div>
                            <span class="status-badge status-pending">En attente</span>
                        </div>

                        <div class="exchange-objects">
                            <div class="objects-column">
                                <h5><?= htmlspecialchars($expediteurs[$exchange['id_user_1']]['prenom']); ?> propose :</h5>
                                <?php 
                                $sender_objects = $elements_echange[$exchange['id_echange']]['sender'] ?? [];
                                foreach ($sender_objects as $obj):
                                ?>
                                    <div class="object-item">
                                        <span class="object-name"><?= htmlspecialchars($obj['nom_objet']); ?></span>
                                        <span class="object-price"><?= htmlspecialchars($obj['prix_estime']); ?> Ar</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="objects-column">
                                <h5>Vous proposez :</h5>
                                <?php 
                                $receiver_objects = $elements_echange[$exchange['id_echange']]['receiver'] ?? [];
                                foreach ($receiver_objects as $obj):
                                ?>
                                    <div class="object-item">
                                        <span class="object-name"><?= htmlspecialchars($obj['nom_objet']); ?></span>
                                        <span class="object-price"><?= htmlspecialchars($obj['prix_estime']); ?> Ar</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="date-info">
                            Reçu le <?= htmlspecialchars(date('d/m/Y', strtotime($exchange['date_demande']))); ?>
                        </div>

                        <div class="exchange-actions">
                            <button class="btn btn-accept" data-action="accept" data-exchange-id="<?= $exchange['id_echange']; ?>">✓ Accepter</button>
                            <button class="btn btn-refuse" data-action="refuse" data-exchange-id="<?= $exchange['id_echange']; ?>">✗ Refuser</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>Aucune proposition reçue</h3>
                    <p>Consultez les objets des autres utilisateurs pour commencer à proposer des échanges.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Onglet Propositions Envoyées -->
        <div id="sent" class="tab-content">
            <?php if (!empty($echanges_envoyes) && count($echanges_envoyes) > 0): ?>
                <?php foreach ($echanges_envoyes as $exchange): ?>
                    <div class="exchange-card">
                        <div class="exchange-header">
                            <div class="exchange-parties">
                                <div class="user-info">
                                    <div class="user-avatar"><?= strtoupper(substr($donnees_utilisateur['prenom'] ?? 'U', 0, 1)); ?></div>
                                    <div class="user-details">
                                        <h4><?= htmlspecialchars($donnees_utilisateur['prenom'] . ' ' . $donnees_utilisateur['nom']); ?></h4>
                                        <p>Vous</p>
                                    </div>
                                </div>
                                <div class="arrow">⇄</div>
                                <div class="user-info">
                                    <div class="user-avatar"><?= strtoupper(substr($destinataires[$exchange['id_user_2']]['prenom'] ?? 'P', 0, 1)); ?></div>
                                    <div class="user-details">
                                        <h4><?= htmlspecialchars($destinataires[$exchange['id_user_2']]['prenom'] . ' ' . $destinataires[$exchange['id_user_2']]['nom']); ?></h4>
                                        <p>Destinataire</p>
                                    </div>
                                </div>
                            </div>
                            <span class="status-badge status-pending">En attente</span>
                        </div>

                        <div class="exchange-objects">
                            <div class="objects-column">
                                <h5>Vous proposez :</h5>
                                <?php 
                                $sender_objects = $elements_echange[$exchange['id_echange']]['sender'] ?? [];
                                foreach ($sender_objects as $obj):
                                ?>
                                    <div class="object-item">
                                        <span class="object-name"><?= htmlspecialchars($obj['nom_objet']); ?></span>
                                        <span class="object-price"><?= htmlspecialchars($obj['prix_estime']); ?> Ar</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="objects-column">
                                <h5><?= htmlspecialchars($destinataires[$exchange['id_user_2']]['prenom']); ?> propose :</h5>
                                <?php 
                                $receiver_objects = $elements_echange[$exchange['id_echange']]['receiver'] ?? [];
                                foreach ($receiver_objects as $obj):
                                ?>
                                    <div class="object-item">
                                        <span class="object-name"><?= htmlspecialchars($obj['nom_objet']); ?></span>
                                        <span class="object-price"><?= htmlspecialchars($obj['prix_estime']); ?> Ar</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="date-info">
                            Envoyée le <?= htmlspecialchars(date('d/m/Y', strtotime($exchange['date_demande']))); ?>
                        </div>

                        <div class="exchange-actions">
                            <button class="btn btn-refuse" data-action="cancel" data-exchange-id="<?= $exchange['id_echange']; ?>">✕ Annuler</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>Aucune proposition envoyée</h3>
                    <p>Commencez par proposer un échange sur les objets des autres utilisateurs.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Onglet Historique -->
        <div id="history" class="tab-content">
            <?php if (!empty($historique_echanges) && count($historique_echanges) > 0): ?>
                <?php foreach ($historique_echanges as $exchange): ?>
                    <div class="exchange-card" style="border-left-color: <?= $exchange['statut'] === 'accepte' ? '#4CAF50' : '#f44336'; ?>">
                        <div class="exchange-header">
                            <div class="exchange-parties">
                                <div class="user-info">
                                    <div class="user-avatar"><?= strtoupper(substr($tous_utilisateurs[$exchange['id_user_1']]['prenom'] ?? 'P', 0, 1)); ?></div>
                                    <div class="user-details">
                                        <h4><?= htmlspecialchars($tous_utilisateurs[$exchange['id_user_1']]['prenom'] . ' ' . $tous_utilisateurs[$exchange['id_user_1']]['nom']); ?></h4>
                                    </div>
                                </div>
                                <div class="arrow">⇄</div>
                                <div class="user-info">
                                    <div class="user-avatar"><?= strtoupper(substr($tous_utilisateurs[$exchange['id_user_2']]['prenom'] ?? 'P', 0, 1)); ?></div>
                                    <div class="user-details">
                                        <h4><?= htmlspecialchars($tous_utilisateurs[$exchange['id_user_2']]['prenom'] . ' ' . $tous_utilisateurs[$exchange['id_user_2']]['nom']); ?></h4>
                                    </div>
                                </div>
                            </div>
                            <span class="status-badge <?= $exchange['statut'] === 'accepte' ? 'status-accepted' : 'status-refused'; ?>">
                                <?= ucfirst($exchange['statut']); ?>
                            </span>
                        </div>

                        <div class="date-info">
                            <?= $exchange['statut'] === 'accepte' ? 'Accepté' : 'Refusé'; ?> le <?= htmlspecialchars(date('d/m/Y', strtotime($exchange['date_finalisation'] ?? $exchange['date_demande']))); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>Aucun historique</h3>
                    <p>Votre historique d'échanges acceptés ou refusés s'affichera ici.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/components/footer.php'; ?>

    <script nonce="<?= Flight::app()->get('csp_nonce') ?>">
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tabName = this.getAttribute('data-tab');
                    
                    document.querySelectorAll('.tab-content').forEach(tab => {
                        tab.classList.remove('active');
                    });
                    
                    document.querySelectorAll('.tab-button').forEach(b => {
                        b.classList.remove('active');
                    });
                    
                    document.getElementById(tabName).classList.add('active');
                    this.classList.add('active');
                });
            });

            document.querySelectorAll('[data-action="accept"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const exchangeId = this.getAttribute('data-exchange-id');
                    if (confirm('Êtes-vous sûr de vouloir accepter cet échange ?')) {
                        window.location.href = '/accepter-echange?id=' + exchangeId;
                    }
                });
            });

            document.querySelectorAll('[data-action="refuse"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const exchangeId = this.getAttribute('data-exchange-id');
                    if (confirm('Êtes-vous sûr de vouloir refuser cet échange ?')) {
                        window.location.href = '/refuser-echange?id=' + exchangeId;
                    }
                });
            });

            // Cancel exchange buttons
            document.querySelectorAll('[data-action="cancel"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const exchangeId = this.getAttribute('data-exchange-id');
                    if (confirm('Êtes-vous sûr de vouloir annuler cette proposition ?')) {
                        window.location.href = '/annuler-echange?id=' + exchangeId;
                    }
                });
            });
        });
    </script>
</body>
</html>



