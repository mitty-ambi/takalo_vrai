<?php $base_url = rtrim(Flight::get('flight.base_url'), '/'); ?>
<!-- navbar.php - Version corrigée -->
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
                <li><a href="<?= $base_url ?>/" class="nav-link">Accueil</a></li>
                <li><a href="<?= $base_url ?>/gerer_besoins" class="nav-link">Besoins</a></li>
                <li><a href="<?= $base_url ?>/gerer_achats" class="nav-link">Achats</a></li>
                <li><a href="<?= $base_url ?>/simulation" class="nav-link">Simulation</a></li>
                <li><a href="<?= $base_url ?>/crud_dons" class="nav-link">Dons</a></li>

                <!-- Dropdown Dispatch corrigé -->
                <li class="nav-dropdown">
                    <a href="javascript:void(0)" class="nav-link dropdown-toggle">Dispatch <i
                            class="fas fa-chevron-down"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?= $base_url ?>/dispatch-par-date">Par Date</a></li>
                        <li><a href="<?= $base_url ?>/dispatch-par-min">Par Quantité Min</a></li>
                        <li><a href="<?= $base_url ?>/dispatch-proportionnel">Proportionnel</a></li>
                    </ul>
                </li>

                <li><a href="<?= $base_url ?>/recap" class="nav-link">Récap</a></li>
                <li><a href="<?= $base_url ?>/gerer_dons" class="nav-link btn-don">Donner</a></li>
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

        // Mobile menu toggle
        if (mobileBtn) {
            mobileBtn.addEventListener('click', function () {
                this.classList.toggle('active');
                navMenu.classList.toggle('show');
            });
        }

        // Dropdown en mobile
        if (window.innerWidth <= 992) {
            const dropdowns = document.querySelectorAll('.nav-dropdown > .nav-link');
            dropdowns.forEach(dropdown => {
                dropdown.addEventListener('click', function (e) {
                    e.preventDefault();
                    const parent = this.closest('.nav-dropdown');
                    parent.classList.toggle('show');
                });
            });
        }

        // Lien actif
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-link, .dropdown-menu a').forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });
    });
</script>