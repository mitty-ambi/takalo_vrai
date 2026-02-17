<?php $base_url = rtrim(Flight::get('flight.base_url'), '/'); ?>
<footer class="bngrc-footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Colonne 1: Logo et description -->
            <div class="footer-col">
                <div class="footer-logo">
                    <i class="fas fa-hand-holding-heart"></i>
                    <span>BNGRC</span>
                </div>
                <p class="footer-description">
                    Bureau National de Gestion des Risques et Catastrophes - Madagascar
                </p>
                <div class="footer-social">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <!-- Colonne 2: Liens rapides -->
            <div class="footer-col">
                <h4>Liens rapides</h4>
                <ul class="footer-links">
                    <li><a href="<?= $base_url ?>/"><i class="fas fa-chevron-right"></i> Accueil</a></li>
                    <li><a href="<?= $base_url ?>/"><i class="fas fa-chevron-right"></i> Tableau de bord</a></li>
                    <li><a href="<?= $base_url ?>/gerer_besoins"><i class="fas fa-chevron-right"></i> Besoins
                            urgents</a></li>
                    <li><a href="<?= $base_url ?>/gerer_dons"><i class="fas fa-chevron-right"></i> Faire un don</a></li>
                    <li><a href="<?= $base_url ?>/dispatch"><i class="fas fa-chevron-right"></i> Dispatch</a></li>
                </ul>
            </div>
            <!-- Colonne 3: Contact -->
            <div class="footer-col">
                <h4>Contact</h4>
                <ul class="footer-contact">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Antananarivo 101, Madagascar</span>
                    </li>
                    <li>
                        <i class="fas fa-phone-alt"></i>
                        <span>+261 34 00 000 00</span>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span>ETU003943-ETU003954-ETU004213</span>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <span>Lun-Ven: 8h - 17h</span>
                    </li>
                </ul>
            </div>

            <!-- Colonne 4: Newsletter -->
            <div class="footer-col">
                <h4>Restez informé</h4>
                <p class="newsletter-text">Recevez les alertes et mises à jour</p>
                <form class="newsletter-form">
                    <input type="email" placeholder="Votre email" class="newsletter-input">
                    <button type="submit" class="newsletter-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
                <div class="footer-badge">
                    <i class="fas fa-shield-alt"></i>
                    <span>Site officiel BNGRC</span>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="footer-bottom">
            <div class="copyright">
                <p>&copy; <?= date('Y') ?> BNGRC - Tous droits réservés</p>
            </div>
            <div class="footer-bottom-links">
                <a href="#">Mentions légales</a>
                <a href="#">Politique de confidentialité</a>
                <a href="#">CGU</a>
            </div>
        </div>
    </div>
</footer>

<style>
    /* ===== FOOTER BNGRC STYLES ===== */
    .bngrc-footer {
        background: linear-gradient(135deg, #002244 0%, #003366 100%);
        color: #fff;
        padding: 60px 0 20px;
        margin-top: 60px;
        position: relative;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .bngrc-footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #FF6600, #FFA500, #FF6600);
        animation: gradientMove 3s ease infinite;
    }

    @keyframes gradientMove {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Grille du footer */
    .footer-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px;
        margin-bottom: 40px;
    }

    /* Colonnes */
    .footer-col {
        animation: fadeInUp 0.6s ease;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Logo */
    .footer-logo {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .footer-logo i {
        font-size: 2rem;
        color: #FF6600;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    .footer-logo span {
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #fff, #FF6600);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Description */
    .footer-description {
        color: #adb5bd;
        line-height: 1.6;
        margin-bottom: 20px;
        font-size: 0.95rem;
    }

    /* Réseaux sociaux */
    .footer-social {
        display: flex;
        gap: 10px;
    }

    .social-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        color: #fff;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .social-link:hover {
        background: #FF6600;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(255, 102, 0, 0.3);
    }

    /* Titres */
    .footer-col h4 {
        color: #fff;
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 10px;
    }

    .footer-col h4::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 2px;
        background: #FF6600;
        transition: width 0.3s ease;
    }

    .footer-col:hover h4::after {
        width: 60px;
    }

    /* Liens */
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links li {
        margin-bottom: 12px;
    }

    .footer-links a {
        color: #adb5bd;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .footer-links a i {
        font-size: 0.8rem;
        color: #FF6600;
        transition: transform 0.3s ease;
    }

    .footer-links a:hover {
        color: #FF6600;
        padding-left: 5px;
    }

    .footer-links a:hover i {
        transform: translateX(3px);
    }

    /* Contact */
    .footer-contact {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-contact li {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 15px;
        color: #adb5bd;
    }

    .footer-contact li i {
        color: #FF6600;
        width: 20px;
        margin-top: 3px;
    }

    .footer-contact li span {
        flex: 1;
        line-height: 1.5;
    }

    /* Newsletter */
    .newsletter-text {
        color: #adb5bd;
        margin-bottom: 15px;
        font-size: 0.95rem;
    }

    .newsletter-form {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .newsletter-input {
        flex: 1;
        padding: 12px 15px;
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.05);
        color: #fff;
        outline: none;
        transition: all 0.3s ease;
    }

    .newsletter-input:focus {
        border-color: #FF6600;
        background: rgba(255, 255, 255, 0.1);
    }

    .newsletter-input::placeholder {
        color: #6c757d;
    }

    .newsletter-btn {
        width: 45px;
        height: 45px;
        border: none;
        border-radius: 8px;
        background: #FF6600;
        color: #fff;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .newsletter-btn:hover {
        background: #CC3300;
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(255, 102, 0, 0.3);
    }

    .newsletter-btn i {
        font-size: 1.1rem;
    }

    /* Badge */
    .footer-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px;
        background: rgba(255, 102, 0, 0.1);
        border-radius: 8px;
        border: 1px solid rgba(255, 102, 0, 0.3);
    }

    .footer-badge i {
        color: #FF6600;
        font-size: 1.2rem;
    }

    .footer-badge span {
        color: #fff;
        font-size: 0.9rem;
        font-weight: 500;
    }

    /* Bottom bar */
    .footer-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        flex-wrap: wrap;
        gap: 15px;
    }

    .copyright p {
        color: #adb5bd;
        margin: 0;
        font-size: 0.9rem;
    }

    .footer-bottom-links {
        display: flex;
        gap: 20px;
    }

    .footer-bottom-links a {
        color: #adb5bd;
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.3s ease;
        position: relative;
    }

    .footer-bottom-links a:not(:last-child)::after {
        content: '|';
        position: absolute;
        right: -12px;
        color: rgba(255, 255, 255, 0.2);
    }

    .footer-bottom-links a:hover {
        color: #FF6600;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .footer-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .bngrc-footer {
            padding: 40px 0 20px;
        }

        .footer-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }

        .footer-col {
            text-align: center;
        }

        .footer-logo {
            justify-content: center;
        }

        .footer-social {
            justify-content: center;
        }

        .footer-col h4::after {
            left: 50%;
            transform: translateX(-50%);
        }

        .footer-links a {
            justify-content: center;
        }

        .footer-contact li {
            justify-content: center;
        }

        .footer-bottom {
            flex-direction: column;
            text-align: center;
        }

        .footer-bottom-links {
            flex-wrap: wrap;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .footer-bottom-links {
            flex-direction: column;
            gap: 10px;
        }

        .footer-bottom-links a:not(:last-child)::after {
            display: none;
        }
    }
</style>