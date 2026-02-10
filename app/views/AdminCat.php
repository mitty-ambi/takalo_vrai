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
            <h2>Liste des catégories <span class="badge badge-primary">5 catégories</span></h2>

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
                        <tr>
                            <td class="id-cell">#001</td>
                            <td class="category-cell">Électronique</td>
                            <td>15/01/2024</td>
                            <td><strong>42</strong> objets</td>
                            <td><span class="badge badge-success">Active</span></td>
                            <td class="actions-cell">
                                <button class="btn-action btn-edit">✏️ Modifier</button>
                                <button class="btn-action btn-delete">🗑️ Supprimer</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Statistiques -->
            <div class="stats">
                <div class="stat-card">
                    <h3>5</h3>
                    <p>Catégories totales</p>
                </div>
                <div class="stat-card">
                    <h3>225</h3>
                    <p>Objets au total</p>
                </div>
                <div class="stat-card">
                    <h3>5</h3>
                    <p>Catégories actives</p>
                </div>
                <div class="stat-card">
                    <h3>0</h3>
                    <p>Catégories archivées</p>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('.btn-action');
            buttons.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const action = this.classList.contains('btn-edit') ? 'modifier' : 'supprimer';
                    const category = this.closest('tr').querySelector('.category-cell').textContent;

                    if (action === 'supprimer') {
                        if (confirm(`Voulez-vous vraiment supprimer la catégorie "${category}" ?`)) {
                            alert(`Catégorie "${category}" supprimée (simulation)`);
                        }
                    } else {
                        alert(`Modification de la catégorie "${category}" (simulation)`);
                    }
                });
            });

            const form = document.querySelector('form');
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const input = document.getElementById('cat');
                if (input.value.trim()) {
                    alert(`Catégorie "${input.value}" ajoutée avec succès !`);
                    input.value = '';
                }
            });
        });

    </script>
</body>

</html>