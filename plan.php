<?php
declare(strict_types=1);
$title = "Plan du Site ";

include "./include/header.inc.php";
?>
<main>
    <section class="plan">
        <h2>Navigation du site</h2>

        <p>Retrouvez facilement toutes les pages du site :</p>

        <nav>
            <ul class="site-map">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="meteo.php">Météo</a></li>
                <li><a href="stat.php">Statistique</a></li>
                <li><a href="tech.php">Tech</a></li>
                <li><a href="actualite.php">Actualité Méteo</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </section>
</main>

<?php include './include/footer.inc.php'; ?>
