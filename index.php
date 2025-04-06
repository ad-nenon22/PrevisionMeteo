<?php
declare(strict_types=1);
$page_title = "Accueil SkyView";
$page_footer_info = "Accueil";

// Inclusion du header
include 'include/header.inc.php';
?>

<main>
            <h1> Météo News </h1>


    <section class="lang">

      <span id="lang">
        <a href="index.php?lang=fr">
            <img src="images/fr.png" alt="drapeau français" style="width: 25px; height: auto;">
        </a>
        <a href="index.php?lang=en">
            <img src="images/uk.png" alt="drapeau US" style="width: 25px; height: auto;">
        </a>
    </span>



    <?php
    if(isset($_GET["lang"]) && !empty($_GET["lang"]) && $_GET["lang"]==="en"){
        $lang="include/english.inc.php";
    }else{
        $lang="include/french.inc.php";
    }
    require"$lang";

    ?>


</section>

<section class="gallery">
        <h2>Galerie météo</h2>
        <div class="gallery-container">
            
     <?php
                // Chemin du dossier contenant les images
                $dossierImages = 'aleatoire/';

                // Liste des fichiers dans le dossier
                $images = glob($dossierImages . '*.{jpg,png,gif}', GLOB_BRACE);

                // Sélection aléatoire d'une image
                $imageAleatoire = $images[array_rand($images)];

                // Affichage de l'image sur la page d'accueil
                echo '<figure>';
                echo '<img src="' . $imageAleatoire . '" alt="Image aléatoire">';
                echo '</figure>';
                ?>

        </div>
    </section>


 <section class="educational-content">
    <h2>Comprendre la météo</h2>
    <div class="articles">
        <article>
            <div class="icon"><img src="images/icon3.png" alt="Anticyclone"></div>
            <h3>Qu'est-ce qu'un anticyclone ?</h3>
            <p>Découvrez comment les anticyclones influencent notre météo quotidienne.</p>
            <a href="actualite.php">Lire plus</a>
        </article>
        <article>
            <div class="icon"><img src="images/icon4.png" alt="Orage"></div>
            <h3>Les orages : comment se forment-ils ?</h3>
            <p>Un phénomène fascinant qui résulte d’une combinaison unique de facteurs.</p>
            <a href="actualite.php">Lire plus</a>
        </article>
        <article>
            <div class="icon"><img src="images/icon2.png" alt="Tempête de neige"></div>
            <h3>La formation des tempêtes de neige</h3>
            <p>Découvrez pourquoi certaines régions sont plus sujettes aux tempêtes de neige.</p>
            <a href="actualite.php">Lire plus</a>
        </article>
        <article>
            <div class="icon"><img src="images/icon1.png" alt="Tempête de neige"></div>
            <h3>Canicule</h3>
            <p>Découvrez pourquoi certaines régions sont plus sujettes aux tempêtes de neige.</p>
            <a href="actualite.php">Lire plus</a>
        </article>
    </div>
</section>


        <p>Consultez les villes <a href="stat.php"> Les plus consultées</a> !</p>
    </section>
</main>

<?php include 'include/footer.inc.php'; ?>