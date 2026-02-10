document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('registerForm');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        // improved client-side validation and per-field feedback
        var pwEl = document.getElementById('password');
        var cpEl = document.getElementById('confirm_password');
        var telEl = document.getElementById('telephone');
        var emailEl = document.getElementById('email');
        var nomEl = document.getElementById('nom');
        var prenomEl = document.getElementById('prenom');

        var pw = pwEl ? pwEl.value : '';
        var cp = cpEl ? cpEl.value : '';
        var tel = telEl ? (telEl.value || '').replace(/\s+/g, '') : '';
        var email = emailEl ? (emailEl.value || '').trim() : '';
        var nom = nomEl ? (nomEl.value || '').trim() : '';
        var prenom = prenomEl ? (prenomEl.value || '').trim() : '';

        var errors = {};
        if (!nom || nom.length < 2) errors.nom = 'Le nom doit contenir au moins 2 caractères.';
        if (!prenom || prenom.length < 2) errors.prenom = 'Le prénom doit contenir au moins 2 caractères.';
        if (!email) errors.email = "L'email est obligatoire.";
        else if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) errors.email = "L'email n'est pas valide.";
        if (pw.length < 8) errors.password = 'Le mot de passe doit contenir au moins 8 caractères.';
        if (cp.length < 8) errors.confirm_password = 'Veuillez confirmer le mot de passe (min 8 caractères).';
        else if (pw !== cp) errors.confirm_password = 'Les mots de passe ne correspondent pas.';
        if (tel.length < 8 || tel.length > 15) errors.telephone = 'Le téléphone doit contenir entre 8 et 15 chiffres.';
        else if (!/^[0-9]+$/.test(tel)) errors.telephone = 'Le téléphone ne doit contenir que des chiffres.';

        // clear previous invalid state
        ['nom', 'prenom', 'email', 'password', 'confirm_password', 'telephone'].forEach(function (f) {
            var el = document.getElementById(f);
            if (el) el.classList.remove('is-invalid');
            var feed = document.getElementById(f + 'Error');
            if (feed) feed.textContent = '';
        });

        if (Object.keys(errors).length > 0) {
            e.preventDefault();
            var status = document.getElementById('formStatus');
            if (status) {
                status.classList.remove('d-none', 'alert-success');
                status.classList.add('alert', 'alert-danger');
                status.textContent = 'Veuillez corriger les erreurs du formulaire avant de continuer.';
            }

            // mark fields and show messages
            Object.keys(errors).forEach(function (k) {
                var el = document.getElementById(k);
                if (el) el.classList.add('is-invalid');
                var feed = document.getElementById(k + 'Error');
                if (feed) feed.textContent = errors[k];
            });

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
});
