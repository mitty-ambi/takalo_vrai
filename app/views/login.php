<?php
// DÉBUT DU FICHIER login.php
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Connexion - Takalo</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/login.css">
    <link rel="stylesheet" href="/assets/css/sidebar.css">
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>

<body class="login-bg">
    <?php
    // TESTS - Mets ça EN HAUT pour voir ce qui se passe
    echo "<div style='position:fixed; top:10px; right:10px; background:#fff; padding:10px; border-radius:5px; z-index:9999;'>";
    echo "Session ID: " . ($_SESSION["id_user"] ?? 'NON DÉFINI');
    echo "</div>";

    if (isset($error)) {
        echo "<div style='position:fixed; top:50px; right:10px; background:#f8d7da; padding:10px; border-radius:5px; z-index:9999;'>";
        echo "Erreur: " . htmlspecialchars($error);
        echo "</div>";
    }
    ?>

    <?php include __DIR__ . '/components/side_nav.php'; ?>

    <main class="auth-main">
        <div class="auth-content">
            <div class="auth-card">
                <div class="text-center mb-3">
                    <h4>Connexion</h4>
                    <div class="text-muted">Entrez votre email et mot de passe</div>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <!-- Change action et méthode -->
                <form id="loginForm" method="POST" action="/login" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" name="email" type="email" class="form-control" required>
                        <div class="invalid-feedback" id="emailError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input id="password" name="password" type="password" class="form-control" required>
                        <div class="invalid-feedback" id="passwordError"></div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                </form>

                <div class="auth-aux mt-3">Pas encore inscrit ? <a href="/register">Créez un compte</a></div>
            </div>
        </div>
    </main>

    <!-- Optionnel : Pour debug -->
    <script>
        console.log("Session ID User:", <?= json_encode($_SESSION['id_user'] ?? null) ?>);
    </script>
</body>

</html>