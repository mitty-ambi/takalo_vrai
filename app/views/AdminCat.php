<?php header_remove('Content-Security-Policy'); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/Cat.css">
    <title>Gestion Catégories - Admin</title>
    <style>
    </style>
</head>

<body>
    <br>
    <?php include("components/side_nav.php") ?>
    <div class="admin-container">
        <h1>Gestion des Catégories</h1>
        <p class="subtitle">Interface d'administration - Panel de gestion des catégories</p>

        <!-- Formulaire d'ajout -->
        <section class="form-section">
            <h2>Ajouter une nouvelle catégorie</h2>     
            <form action="/addCat" method="POST" class="form-group">
                <div class="input-wrapper">
                    <label for="cat">Nom de la catégorie</label>
                    <input type="text" id="cat" name="cat" placeholder="Ex: Électronique, Livres, Vêtements..."
                        required>
                </div>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </form>
        </section>

        <!-- Tableau des catégories -->
        <section class="table-section">
            <h2>Liste des catégories <span class="badge" style="color: black;"><?= count($listeCat) ?> catégories</span>
            </h2>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom Catégorie</th>
                            <th>Date création</th>
                            <th>Objets associés</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listeCat as $cat) { ?>
                            <tr data-id="<?= $cat['id_categorie'] ?>">
                                <td class="id-cell">#<?= $cat['id_categorie'] ?></td>
                                <td class="category-cell"><?= htmlspecialchars($cat['nom_categorie']) ?></td>
                                <td><?= $cat['date_creation'] ?></td>
                                <td><strong>42</strong> objets</td>
                                <td><span class="badge badge-success">Active</span></td>
                                <td class="actions-cell">
                                    <a class="btn-action btn-edit"
                                        href="/EditCat?id_categorie=<?= $cat['id_categorie'] ?>">✏️ Modifier</a>
                                    <button class="btn-action btn-delete" type="button"
                                        aria-label="Supprimer cette catégorie">🗑️ Supprimer</button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Statistiques -->
            <div class="stats">
                <div class="stat-card">
                    <h3><?= count($listeCat) ?></h3>
                    <p>Catégories totales</p>
                </div>
                <div class="stat-card">
                    <h3>225</h3>
                    <p>Objets au total</p>
                </div>
                <div class="stat-card">
                    <h3><?= count($listeCat) ?></h3>
                    <p>Catégories actives</p>
                </div>
                <div class="stat-card">
                    <h3>0</h3>
                    <p>Catégories archivées</p>
                </div>
            </div>
        </section>
    </div>
</body>
<!-- Remplace ton script par : -->
<script nonce="<?= Flight::app()->get('csp_nonce') ?>">
    window.addEventListener('load', function () {
        const buttons = document.querySelectorAll('.btn-delete');

        buttons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const tr = this.closest('tr');
                if (!tr) return;
                const id = tr.dataset.id;
                if (!id) return;

                if (!confirm('Supprimer cette catégorie ?')) return;

                fetch('/deleteCat', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + encodeURIComponent(id)
                }).then(response => {
                    if (!response.ok) throw new Error('Erreur réseau');
                    return response.text();
                }).then(text => {
                    if (text.trim() === 'success') {
                        tr.remove();
                        // Optionally update counts on page
                        const badges = document.querySelectorAll('.badge');
                        // update first badge showing total categories
                        const totalBadge = document.querySelector('.table-section .badge');
                        if (totalBadge) {
                            const m = totalBadge.textContent.match(/(\d+)/);
                            if (m) {
                                const newVal = parseInt(m[1], 10) - 1;
                                totalBadge.textContent = newVal + ' catégories';
                            }
                        }
                    } else {
                        alert('Erreur suppression: ' + text);
                    }
                }).catch(err => {
                    console.error(err);
                    alert('Erreur lors de la suppression');
                });
            });
        });
    });
</script>

</html>