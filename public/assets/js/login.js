document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('loginForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // gather
        var emailEl = document.getElementById('email');
        var pwEl = document.getElementById('password');
        var email = emailEl ? (emailEl.value || '').trim() : '';
        var pw = pwEl ? (pwEl.value || '') : '';

        // clear
        ['email', 'password'].forEach(function (k) {
            var el = document.getElementById(k);
            if (el) el.classList.remove('is-invalid');
            var feed = document.getElementById(k + 'Error');
            if (feed) feed.textContent = '';
        });
        var alertBox = document.getElementById('alertBox');
        if (alertBox) { alertBox.className = 'alert d-none'; alertBox.textContent = ''; }

        var errors = {};
        if (!email) errors.email = "L'email est requis.";
        else if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) errors.email = "Email invalide.";
        if (pw.length < 8) errors.password = 'Le mot de passe doit contenir au moins 8 caractères.';

        if (Object.keys(errors).length) {
            // show field errors
            Object.keys(errors).forEach(function (k) {
                var el = document.getElementById(k);
                if (el) el.classList.add('is-invalid');
                var feed = document.getElementById(k + 'Error');
                if (feed) feed.textContent = errors[k];
            });

            if (alertBox) {
                alertBox.className = 'alert alert-danger';
                alertBox.textContent = 'Veuillez corriger les erreurs du formulaire.';
                alertBox.scrollIntoView({ behavior: 'smooth' });
            }
            return;
        }

        // fake success (front-end only)
        if (alertBox) {
            alertBox.className = 'alert alert-success';
            alertBox.textContent = 'Connexion réussie (front-end uniquement) ✅';
            alertBox.scrollIntoView({ behavior: 'smooth' });
        }

        // optionally clear form
        // form.reset();
    });
});
