<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC - Dons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include __DIR__ . '/navbar.php'; ?>

    <div class="container my-4">
        <h1 class="page-title">Gestion des dons</h1>

        <div class="card mb-4">
            <div class="card-header">
                <h5>Enregistrer un don</h5>
            </div>
            <div class="card-body">
                <form>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label>Type de don</label>
                            <select class="form-control">
                                <option>Nature</option>
                                <option>Matériaux</option>
                                <option>Argent</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Article</label>
                            <input type="text" class="form-control" placeholder="Ex: Riz">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Quantité</label>
                            <input type="number" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Montant</label>
                            <input type="number" class="form-control">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Donateur</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>
                    <button class="btn btn-success">Enregistrer</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Liste des dons</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Donateur</th>
                            <th>Article</th>
                            <th>Quantité</th>
                            <th>Montant</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>15/01/2024</td>
                            <td>Croix Rouge</td>
                            <td>Riz</td>
                            <td>500 kg</td>
                            <td>1,250,000 Ar</td>
                            <td><span class="badge bg-success">Distribué</span></td>
                        </tr>
                        <tr>
                            <td>14/01/2024</td>
                            <td>UNICEF</td>
                            <td>Tôles</td>
                            <td>200 pièces</td>
                            <td>3,000,000 Ar</td>
                            <td><span class="badge bg-warning">En cours</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bngrc-footer">
        <!-- ... footer content ... -->
    </footer>
</body>

</html>