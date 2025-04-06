<?php
declare(strict_types=1);
$page_title = "Actualité Climat-Météo SkyView";
$page_footer_info = "Actualité";

// Inclusion du header
include 'include/header.inc.php';
?>

<main>
     <section class="actualites">
        <h1>Actualités Météo</h1>
        
        <article>
            <h2>Qu'est-ce qu'un anticyclone ?</h2>
            <p>Les anticyclones sont des zones de haute pression atmosphérique qui influencent grandement notre météo. 
               Ils sont souvent synonymes de beau temps, mais peuvent aussi entraîner des vagues de chaleur ou de froid intense.</p>
            <a href="article.php?id=anticyclone">Lire plus</a>
            <div class="actu"><img src="images/vortex.png" alt="vortex"></div>
        </article>

        <article>
            <h2>Les orages : comment se forment-ils ?</h2>
            <p>Les orages sont le résultat de la rencontre de masses d'air chaud et humide avec de l'air froid. 
               Ce choc thermique provoque la formation de nuages cumulonimbus qui génèrent éclairs, tonnerre et précipitations intenses.</p>
            <a href="article.php?id=orages">Lire plus</a>
                    <div class="actu"><img src="images/tornade.png" alt="tornade"></div>

        </article>

        <article>
            <h2>La formation des tempêtes de neige</h2>
            <p>Les tempêtes de neige se forment lorsque des vents froids traversent une source d'humidité, entraînant des précipitations neigeuses abondantes.
               Ces conditions météorologiques peuvent réduire la visibilité et rendre les déplacements dangereux.</p>
            <a href="article.php?id=tempetes">Lire plus</a>
            <div class="actu"><img src="images/notreplanete.webp" alt="notreplanete"></div>
        </article>
        
        <article>
            <h2>Le dôme de chaleur : un phénomène météorologique extrême</h2>
            <p>Un dôme de chaleur se forme lorsque de l'air chaud est piégé par une zone de haute pression, entraînant une augmentation brutale des températures.
               Ce phénomène peut causer des canicules extrêmes et augmenter le risque d'incendies.</p>
            <a href="article.php?id=dome_de_chaleur">Lire plus</a>
            <div class="actu"><img src="images/vortex.png" alt="vortex"></div>
        </article>
        
        <article>
            <h2>Les conséquences du réchauffement climatique sur les ouragans</h2>
            <p>Le réchauffement climatique accentue la fréquence et l’intensité des ouragans. 
               Avec des océans plus chauds, ces tempêtes gagnent en puissance et peuvent causer des dégâts considérables sur les zones côtières.</p>
            <a href="article.php?id=ouragans_climat">Lire plus</a>
            <div class="actu"><img src="images/vortex.png" alt="vortex"></div>
        </article>
    </section>




</main>

<?php include 'include/footer.inc.php'; ?>