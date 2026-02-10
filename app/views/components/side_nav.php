<?php
?>
<aside class="auth-sidebar" aria-hidden="false">
    <div class="auth-sidebar-inner">
        <div class="brand mb-3">
            <a href="/" class="text-white text-decoration-none d-flex align-items-center">
                <div
                    style="width:44px;height:44px;border-radius:8px;background:rgba(255,255,255,0.06);display:flex;align-items:center;justify-content:center;margin-right:12px;font-weight:700">
                    TV</div>
                <div>
                    <h3 class="mb-0">Takalo Vrai</h3>
                    <small>Communauté & échanges</small>
                </div>
            </a>
        </div>

        <nav class="auth-nav">
            <ul>
                <li class="mb-2"><a href="/" class="auth-link">S'inscrire</a></li>
                <li class="mb-2"><a href="/login" class="auth-link active">Se connecter</a></li>
                <li class="mb-2"><a href="/" class="auth-link">Accueil</a></li>
                <li class="mt-4"><a href="/help" class="text-muted small">Aide & support</a></li>
            </ul>
        </nav>

        <div class="auth-footer mt-auto text-center small">
            © <?= date('Y') ?> Takalo — Fait avec ❤️
        </div>
    </div>
</aside>

<!-- Small toggle for mobile -->
<button class="auth-hamburger" type="button" aria-label="Toggle sidebar">☰</button>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.querySelector('.auth-hamburger');
        var sidebar = document.querySelector('.auth-sidebar');
        if (!btn || !sidebar) return;
        btn.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
    });
</script>