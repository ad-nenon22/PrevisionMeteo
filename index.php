<?php
declare(strict_types=1);
$page_title = "Accueil Météo News";
$page_footer_info = "Accueil";
// Inclusion du header
require "include/header.inc.php";
?>

<main>
    
    <section class="banner">
        <h2>Panoramas urbains de France</h2>
        <div style="width: 100%; height: 300px; overflow: hidden;">
            <?php
            // Chemin du dossier contenant les images
            $dossierImages = 'aleatoire/';
            // Liste des fichiers dans le dossier
            $images = glob($dossierImages . '*.{jpg,png,gif,webp,avif}', GLOB_BRACE);
            
            // Vérifier si des images ont été trouvées
            if (!empty($images)) {
                // Sélection aléatoire d'une image
                $imageAleatoire = $images[array_rand($images)];
                // Affichage de l'image comme bannière
                echo '<figure class="banner-image">';
                echo '<img src="' . $imageAleatoire . '" alt="Bannière aléatoire" class="banner-img">';
                echo '</figure>';
            } else {
                echo '<p>Aucune image trouvée dans le dossier.</p>';
            }
            ?>
        </div>
    </section>
    
    <section class="lang">
        <span id="lang">
            <a href="index.php?lang=fr">
                <img src="images/fr.png" alt="drapeau français" style="width: 20px; height: auto;">
            </a>
            <a href="index.php?lang=en">
                <img src="images/uk.png" alt="drapeau US" style="width: 20px; height: auto;">
            </a>
        </span>
        <?php
        if(isset($_GET["lang"]) && !empty($_GET["lang"]) && $_GET["lang"]==="en"){
            $lang="include/english.inc.php";
        }else{
            $lang="include/french.inc.php";
        }
        require "$lang";
        ?>    
    </section>
    
    <section class="educational-content">
        <h2>Comprendre les phénomènes climatique</h2>
        <div class="articles">
            <article>
                <div class="icon"><img src="images/icon3.png" alt="Anticyclone"></div>
                <h3>Un anticyclone ?</h3>
                <p>Découvrez comment les anticyclones influencent notre météo quotidienne.</p>
                <a href="actualite.php">Lire plus</a>
            </article>
            <article>
                <div class="icon"><img src="images/icon4.png" alt="Orage"></div>
                <h3>Les orages ?</h3>
                <p>Un phénomène fascinant qui résulte d'une combinaison unique de facteurs.</p>
                <a href="actualite.php">Lire plus</a>
            </article>
            <article>
                <div class="icon"><img src="images/icon2.png" alt="Tempête de neige"></div>
                <h3>Tempêtes de neige</h3>
                <p>Découvrez pourquoi certaines régions sont plus sujettes aux tempêtes de neige.</p>
                <a href="actualite.php">Lire plus</a>
            </article>
            <article>
                <div class="icon"><img src="images/icon1.png" alt="Tempête de neige"></div>
                <h3>La Canicule !</h3>
                <p>Découvrez pourquoi certaines régions sont plus exposées aux vagues de chaleur intenses.</p>
                <a href="actualite.php">Lire plus</a>
            </article>
        </div>
        <p>Consultez les villes <a href="stat.php">Les plus consultées</a> !</p>
    </section>
</main>

<style>
    .banner-image {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
    }
    .banner-img {
        width: 100%;
        height: 300px;
        object-fit: cover; /* Cette propriété ajuste l'image à la taille du conteneur */
    }
    .banner h2 {
        text-align: center;
        margin-bottom: 15px;
        color: #2c3e50;
    }
</style>

<?php
    require "./include/footer.inc.php";
?>