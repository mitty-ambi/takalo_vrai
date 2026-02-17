<?php $base_url = rtrim(Flight::get('flight.base_url'), '/'); ?>
<!-- navbar -->
<nav class="bngrc-navbar">
    <div class="container">
        <div class="nav-wrapper">
            <a href="<?= $base_url ?>/" class="nav-logo">
                <i class="fas fa-hand-holding-heart"></i>
                <span>BNGRC</span>
            </a>

            <button class="nav-mobile-btn" id="mobileBtn">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <ul class="nav-menu" id="navMenu">
                <li><a href="<?= $base_url ?>/" class="nav-link active">Accueil</a></li>
                <li><a href="<?= $base_url ?>/gerer_besoins" class="nav-link">Besoins</a></li>
                <li><a href="<?= $base_url ?>/gerer_achats" class="nav-link">💰 Achats</a></li>
                <li><a href="<?= $base_url ?>/simulation" class="nav-link">🛒 Simulation</a></li>
                <li><a href="<?= $base_url ?>/crud_dons" class="nav-link">Dons</a></li>
                <li class="nav-dropdown">
                    <a href="#" class="nav-link">📦 Dispatch</a>
                    <ul class="nav-submenu">
                        <li><a href="<?= $base_url ?>/dispatch-par-date" class="nav-link">📅 Par Date</a></li>
                        <li><a href="<?= $base_url ?>/dispatch-par-min" class="nav-link">📊 Par Quantité Min</a></li>
                        <li><a href="<?= $base_url ?>/dispatch-proportionnel" class="nav-link">⚖️ Proportionnel</a></li>
                    </ul>
                </li>
                <li><a href="<?= $base_url ?>/recap" class="nav-link">📊 Récap</a></li>
                <li><a href="<?= $base_url ?>/gerer_dons" class="nav-link nav-don">Donner</a></li>
            </ul>
        </div>
    </div>
</nav>

<style>
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mobileBtn = document.getElementById('mobileBtn');
        const navMenu = document.getElementById('navMenu');

        if (mobileBtn) {
            mobileBtn.addEventListener('click', function () {
                this.classList.toggle('active');
                navMenu.classList.toggle('show');
            });
        }

        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                // Vérifier si c'est un lien dropdown
                const parentDropdown = this.closest('.nav-dropdown');
                if (parentDropdown && window.innerWidth <= 992) {
                    e.preventDefault();
                    parentDropdown.classList.toggle('show');
                    return;
                }

                if (window.innerWidth <= 992) {
                    mobileBtn.classList.remove('active');
                    navMenu.classList.remove('show');
                }
            });
        });

        // Gestion du lien actif
        const currentPage = window.location.pathname.split('/').pop() || 'index.html';
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && href.endsWith(currentPage)) {
                link.classList.add('active');
            }
        });
    });
</script>