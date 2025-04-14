<?php
declare(strict_types=1);
require_once 'util.inc.php'; 
?>

<footer>
    <div class="footer-container">

        <!-- Section 1: Liens rapides -->

        <div class="footer-section">
            <h4>Liens rapides</h4>
            <ul class="horizontal-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="tech.php">Tech</a></li>
                <li><a href="statistiques.php">Statistiques</a></li>
                <li><a href="plan.php">Plan du site</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>

        
        <!-- Section 3: À propos -->
        <div class="footer-section">
            <h4>À propos</h4>
            <p>Météo News vous propose des prévisions météorologiques fiables et précises depuis 2023.</p>
        </div>
    </div>
    
    <!-- Barre inférieure -->
    <div class="footer-bottom">
        <div class="footer-info">
            <span>&copy; Météo News - Prévisions-2025</span>
            <span class="footer-separator">|</span>
            <span><?= get_navigateur(); ?></span>
            <span class="footer-separator">|</span>
            <span class="visitor-count"><?php include("counter.php"); ?></span>
        </div>
    </div>
</footer>

</body>
</html>


