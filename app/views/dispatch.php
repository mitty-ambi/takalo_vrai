<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC - Dispatch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include __DIR__ . '/navbar.php'; ?>

    <div class="container my-4">
        <h1 class="page-title">Dispatch des dons</h1>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-warning">
                        <h5>En attente de dispatch</h5>
                    </div>
                    <div class="card-body">
                        <div class="dispatch-item">
                            <div class="d-flex justify-content-between">
                                <span><strong>Riz</strong> - 500 kg</span>
                                <span class="badge bg-info">Croix Rouge</span>
                            </div>
                            <p class="text-muted">Date: 15/01/2024</p>
                            <button class="btn btn-sm btn-primary">Dispatcher</button>
                        </div>
                        <hr>
                        <div class="dispatch-item">
                            <div class="d-flex justify-content-between">
                                <span><strong>Tôles</strong> - 200 pièces</span>
                                <span class="badge bg-info">UNICEF</span>
                            </div>
                            <p class="text-muted">Date: 14/01/2024</p>
                            <button class="btn btn-sm btn-primary">Dispatcher</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5>Derniers dispatches</h5>
                    </div>
                    <div class="card-body">
                        <div class="dispatch-item">
                            <div class="d-flex justify-content-between">
                                <span><strong>Riz</strong> → Antananarivo</span>
                                <span class="badge bg-success">Livré</span>
                            </div>
                            <p class="text-muted">15/01/2024 - 400 kg</p>
                        </div>
                        <hr>
                        <div class="dispatch-item">
                            <div class="d-flex justify-content-between">
                                <span><strong>Huile</strong> → Toamasina</span>
                                <span class="badge bg-success">Livré</span>
                            </div>
                            <p class="text-muted">14/01/2024 - 300 L</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bngrc-footer">
        <!-- ... footer content ... -->
    </footer>
</body>

</html>