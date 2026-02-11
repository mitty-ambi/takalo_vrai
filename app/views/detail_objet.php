<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($objet['nom_objet'] ?? 'Objet'); ?> - Takalo Vrai</title>
    <link rel="stylesheet" href="/assets/css/sidebar.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/detail_objet.css">
    
</head>
<body>
    <header class="dashboard-header">
        <div class="header-content">
            <div>
                <h1 class="header-title">Takalo Vrai</h1>
                <p class="header-subtitle">Détail de l'objet</p>
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
        <button class="btn-back" onclick="history.back()">← Retour</button>

        <?php if ($objet && !empty($objet)): ?>
            <div class="detail-container">
                <div class="image-section">
                    <?php 
                    $images = $images ?? [];
                    $image_src = !empty($images) ? htmlspecialchars($images[0]['url_image']) : '/assets/images/no-image.png';
                    ?>
                    <img src="<?= $image_src; ?>" alt="<?= htmlspecialchars($objet['nom_objet']); ?>" class="main-image" id="mainImage">
                    
                    <?php if (count($images) > 1): ?>
                        <div class="thumbnail-container">
                            <?php foreach ($images as $index => $image): ?>
                                <img src="<?= htmlspecialchars($image['url_image']); ?>" 
                                     alt="Photo <?= $index + 1; ?>" 
                                     class="thumbnail <?= $index === 0 ? 'active' : ''; ?>"
                                     onclick="document.getElementById('mainImage').src='<?= htmlspecialchars($image['url_image']); ?>'; document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active')); this.classList.add('active');">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="info-section">
                    <h1 class="objet-title"><?= htmlspecialchars($objet['nom_objet']); ?></h1>

                    <div class="objet-owner-info">
                        <div class="owner-avatar"><?= strtoupper(substr($proprietaire['prenom'] ?? 'P', 0, 1)); ?></div>
                        <div class="owner-details">
                            <h3><?= htmlspecialchars($proprietaire['prenom'] . ' ' . $proprietaire['nom']); ?></h3>
                            <p>Propriétaire de l'objet</p>
                        </div>
                    </div>

                    <div class="objet-price"><?= htmlspecialchars($objet['prix_estime']); ?> Ar</div>

                    <div class="objet-details">
                        <?php if (!empty($objet['description'])): ?>
                            <div class="detail-item">
                                <div class="detail-label">Description</div>
                                <div class="objet-description"><?= htmlspecialchars($objet['description']); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($objet['date_acquisition'])): ?>
                            <div class="detail-item">
                                <div class="detail-label">Date d'acquisition</div>
                                <div class="detail-value"><?= htmlspecialchars($objet['date_acquisition']); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($nom_categorie)): ?>
                            <div class="detail-item">
                                <div class="detail-label">Catégorie</div>
                                <div class="detail-value"><?= htmlspecialchars($nom_categorie); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="action-buttons">
                        <button id="proposeExchangeBtn" class="btn-primary">💱 Proposer un échange</button>
                        <button id="viewOtherObjectsBtn" class="btn-secondary">Voir d'autres objets</button>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem; background: #f9f9f9; border-radius: 8px;">
                <h3 style="color: #999;">Objet non trouvé</h3>
            </div>
        <?php endif; ?>
    </main>

    <div id="exchangeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Proposer un échange</div>
            <form method="POST" action="/proposer-echange">
                <div class="modal-body">
                    <input type="hidden" name="id_objet_receiver" value="<?= htmlspecialchars($objet['id_objet'] ?? ''); ?>">
                    
                    <div class="form-group">
                        <label for="myObject">Quel objet proposez-vous en échange ?</label>
                        <select id="myObject" name="id_objet_sender" required>
                            <option value="">-- Sélectionnez un objet --</option>
                            <?php foreach ($mes_objets ?? [] as $obj): ?>
                                <option value="<?= $obj['id_objet']; ?>"><?= htmlspecialchars($obj['nom_objet']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message">Message (optionnel)</label>
                        <textarea id="message" name="message" placeholder="Ajoutez un message pour le propriétaire..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" id="cancelExchangeBtn" class="btn-cancel">Annuler</button>
                    <button type="submit" class="btn-submit">Proposer</button>
                </div>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/components/footer.php'; ?>

    <script nonce="<?= Flight::app()->get('csp_nonce') ?>">
        function openExchangeModal() {
            const mesObjets = <?= json_encode($mes_objets ?? []); ?>;
            if (!mesObjets || mesObjets.length === 0) {
                alert('Vous devez ajouter des objets avant de pouvoir proposer un échange.');
                window.location.href = '/ajouter-objet';
            } else {
                const modal = document.getElementById('exchangeModal');
                modal.style.display = 'block';
            }
        }

        function closeExchangeModal() {
            document.getElementById('exchangeModal').style.display = 'none';
        }

        // Ajouter les event listeners au chargement du DOM
        document.addEventListener('DOMContentLoaded', function() {
            // Bouton "Proposer un échange"
            const proposeBtn = document.getElementById('proposeExchangeBtn');
            if (proposeBtn) {
                proposeBtn.addEventListener('click', openExchangeModal);
            }

            // Bouton "Voir d'autres objets"
            const viewOthersBtn = document.getElementById('viewOtherObjectsBtn');
            if (viewOthersBtn) {
                viewOthersBtn.addEventListener('click', function() {
                    window.location.href = '/parcourir';
                });
            }

            // Bouton "Annuler" du modal
            const cancelBtn = document.getElementById('cancelExchangeBtn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', closeExchangeModal);
            }
        });

        // Fermer le modal si on clique en dehors
        window.onclick = function(event) {
            const modal = document.getElementById('exchangeModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>



