<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/register.css">
</head>

<body class="register-bg">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card register-card">
                    <div class="card-header text-center">
                        <h4>Inscription utilisateur</h4>
                    </div>
                    <div class="card-body">

                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success">Inscription réussie ✅</div>
                        <?php endif; ?>

                        <form id="registerForm" method="post" action="/register">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom</label>
                                <input id="nom" name="nom" class="form-control" value="<?= $values['nom'] ?? '' ?>">
                            </div>

                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input id="prenom" name="prenom" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input id="email" name="email" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input id="password" name="password" type="password" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label for="type_user" class="form-label">Type utilisateur</label>
                                <select id="type_user" name="type_user" class="form-control">
                                    <option value="admin">admin
                                    </option>
                                    <option value="normal">normal</option>
                                </select>
                            </div>
                            <button class="btn btn-primary w-100" type="submit">S'inscrire</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>