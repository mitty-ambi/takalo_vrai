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
                                    <button class="btn-action btn-edit">✏️ Modifier</button>
                                    <button onclick="alert('TEST DIRECT')"
                                        style="position:fixed; top:10px; right:10px; background:red; color:white; padding:10px;">
                                        TEST CLICK
                                    </button>
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
<script>
    // Attendre que tout soit chargé
    window.addEventListener('load', function () {
        console.log('Page chargée');

        const buttons = document.querySelectorAll('.btn-delete');
        console.log('Nombre de boutons:', buttons.length);

        // Test direct : assigne onclick
        buttons.forEach(button => {
            button.onclick = function () {
                alert('CLICK !');
                return false;
            };
        });
    });
</script>

</html>