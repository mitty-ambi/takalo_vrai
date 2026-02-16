<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC - Besoins</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include __DIR__ . '/navbar.php'; ?>

    <div class="container my-4">
        <h1 class="page-title">Gestion des besoins</h1>

        <!-- Formulaire -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Ajouter un besoin</h5>
            </div>
            <div class="card-body">
                <form>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label>Ville</label>
                            <select class="form-control">
                                <option>Antananarivo</option>
                                <option>Toamasina</option>
                                <option>Fianarantsoa</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Type</label>
                            <select class="form-control">
                                <option>Nature</option>
                                <option>Matériaux</option>
                                <option>Argent</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Article</label>
                            <input type="text" class="form-control" placeholder="Ex: Riz">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Quantité</label>
                            <input type="number" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Prix unit.</label>
                            <input type="number" class="form-control">
                        </div>
                    </div>
                    <button class="btn btn-primary-custom">Enregistrer</button>
                </form>
            </div>
        </div>

        <!-- Liste des besoins -->
        <div class="card">
            <div class="card-header">
                <h5>Liste des besoins</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ville</th>
                            <th>Article</th>
                            <th>Quantité</th>
                            <th>Prix unit.</th>
                            <th>Total</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Antananarivo</td>
                            <td>Riz</td>
                            <td>1000 kg</td>
                            <td>2,500 Ar</td>
                            <td>2,500,000 Ar</td>
                            <td><span class="badge bg-warning">En attente</span></td>
                        </tr>
                        <tr>
                            <td>Toamasina</td>
                            <td>Tôles</td>
                            <td>500 pièces</td>
                            <td>15,000 Ar</td>
                            <td>7,500,000 Ar</td>
                            <td><span class="badge bg-danger">Urgent</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer (identique) -->
    <footer class="bngrc-footer">
        <!-- ... footer content ... -->
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>