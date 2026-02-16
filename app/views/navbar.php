<!-- navbar.html -->
<nav class="bngrc-navbar">
    <div class="container">
        <div class="nav-wrapper">
            <a href="index.html" class="nav-logo">
                <i class="fas fa-hand-holding-heart"></i>
                <span>BNGRC</span>
            </a>

            <button class="nav-mobile-btn" id="mobileBtn">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <ul class="nav-menu" id="navMenu">
                <li><a href="index.html" class="nav-link active">Accueil</a></li>
                <li><a href="dashboard.html" class="nav-link">Dashboard</a></li>
                <li><a href="/gerer_besoins" class="nav-link">Besoins</a></li>
                <li><a href="/crud_dons" class="nav-link">Dons</a></li>
                <li><a href="/dispatch" class="nav-link">Dispatch</a></li>
                <li><a href="/gerer_dons" class="nav-link nav-don">Donner</a></li>
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
            link.addEventListener('click', function () {
                if (window.innerWidth <= 992) {
                    mobileBtn.classList.remove('active');
                    navMenu.classList.remove('show');
                }
            });
        });

        // Gestion du lien actif
        const currentPage = window.location.pathname.split('/').pop() || 'index.html';
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active');
            }
        });
    });
</script>