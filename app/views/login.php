<?php
function e($v)
{
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/login.css">
</head>

<body class="login-bg">
    <div class="container vh-100 d-flex align-items-center justify-content-center">
        <div class="card login-card p-4" style="width:380px;">
            <div class="text-center mb-3">
                <h4>Connexion administrateur</h4>
                <div class="text-muted">Entrez votre email et mot de passe</div>
            </div>

            <div id="alertBox" class="alert d-none" role="alert"></div>

            <form id="loginForm" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" name="email" type="email" class="form-control">
                    <div class="invalid-feedback" id="emailError"></div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input id="password" name="password" type="password" class="form-control">
                    <div class="invalid-feedback" id="passwordError"></div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Se connecter</button>
            </form>

            <div class="footer-note mt-3 text-center">Test front-end uniquement — pas de backend</div>
        </div>
    </div>

    <script src="/assets/js/login.js" defer></script>
</body>

</html>