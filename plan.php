<?php
declare(strict_types=1);
$title = "Plan du Site ";
$page_desc= "Plan du Site";
$page_heading = "Plan du Site";
$page_footer_info = "Plan du Site";

include "./include/header.inc.php";
?>


<main>
    <section class="plan">
        <h2>Navigation du site</h2>
        <p>Retrouvez facilement toutes les pages du site :</p>

        <nav>
            <ul class="site-map">
                <li><a href="./td5.php">TD5 - Introduction</a></li>
                <li><a href="./td6.php">TD6 - Programmation PHP</a></li>
                <li><a href="./td7.php">TD7 - Fonctions & Tableaux</a></li>
                <li><a href="./td8.php">TD8 - Tableaux,fonctions & liens paramétrés</a></li>
                <li><a href="./td9.php">TD9 - Exploration des formulaires HTML,et traitement en PHP</a></li>

            </ul>
        </nav>

        
    </section>
</main>

<?php include './include/footer.inc.php'; ?>
