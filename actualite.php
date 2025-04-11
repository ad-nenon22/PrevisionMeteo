<?php

 declare(strict_types=1);
 $page_title = "Actualité Climat-Météo SkyView";
 $page_footer_info = "Actualité";
 
 // Inclusion du header
 include 'include/header.inc.php';
 ?>
 
 <style>
 /* Général */
 .actualites {
     max-width: 1000px;
     margin: 0 auto;
     padding: 2rem;
     font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
 }
 
 .actualites h1 {
     text-align: center;
     margin-bottom: 2rem;
     font-size: 2.5em;
     color: #2c3e50;
 }
 
 article {
     background-color: #f9f9f9;
     border: 1px solid #ddd;
     border-radius: 12px;
     padding: 1.5rem;
     margin-bottom: 2rem;
     box-shadow: 0 2px 8px rgba(0,0,0,0.05);
 }
 
 /* Titres des articles */
 article h2 {
     color: #34495e;
     font-size: 1.8em;
     margin-bottom: 0.5rem;
 }
 
 /* Paragraphes */
 article p {
     font-size: 1.05em;
     line-height: 1.6;
     color: #333;
 }
 
 /* Lien "Lire plus" */
 article a {
     display: inline-block;
     margin-top: 0.8rem;
     padding: 0.5rem 1rem;
     background-color: #2980b9;
     color: white;
     border-radius: 8px;
     text-decoration: none;
     transition: background-color 0.3s ease;
 }
 
 article a:hover {
     background-color: #1c5980;
 }
 
 /* Images */
 .actu {
     margin-top: 1rem;
     text-align: center;
 }
 
 .actu img {
     max-width: 100%;
     height: 250px;
     object-fit: cover;
     border-radius: 10px;
     border: 1px solid #ccc;
 }
 </style>
 
 <main>
      <section class="actualites">
         <h1>Actualités Météo</h1>
         
         
         <article>
             <h2>Qu'est-ce qu'un anticyclone ?</h2>
             <p>
                 Un <strong>anticyclone</strong> est une zone de haute pression atmosphérique où l'air descend et se réchauffe, 
                 ce qui empêche la formation de nuages. Cela se traduit souvent par un <em>temps stable et ensoleillé</em>.
                 Toutefois, en hiver, il peut aussi provoquer du <strong>brouillard persistant</strong> ou des <strong>vagues de froid</strong>.
             </p>
             <p><a href="https://fr.wikipedia.org/wiki/Anticyclone" target="_blank">En savoir plus sur les anticyclones</a></p>
             <div class="actu"><img src="images/vortex.jpg" alt="Illustration d'un anticyclone"></div>
         </article>
 
 <article>
             <h2>Les orages : comment se forment-ils ?</h2>
             <p>
                 Les <strong>orages</strong> se produisent quand de l'<em>air chaud et humide</em> monte rapidement, rencontre de l'air froid 
                 et forme des <strong>nuages cumulonimbus</strong>. Ils s'accompagnent souvent d'éclairs, de tonnerre et de pluies intenses.
             </p>
             <p><a href="https://meteofrance.com/dossiers-thematiques/orages" target="_blank">Comprendre les orages avec Météo France</a></p>
             <div class="actu"><img src="images/tornade.jpg" alt="Illustration d'un orage"></div>
         </article>
 
         <article>
             <h2>La formation des tempêtes de neige</h2>
             <p>
                 Une <strong>tempête de neige</strong> se forme quand de l'<em>air froid rencontre de l'humidité</em>, provoquant des 
                 précipitations neigeuses intenses. Elles peuvent <strong>réduire la visibilité</strong>, perturber les transports et provoquer des <strong>coupures d'électricité</strong>.
             </p>
             <p><a href="https://www.futura-sciences.com/planete/definitions/meteo-tempete-neige-4520/" target="_blank">Plus d'infos sur les tempêtes de neige</a></p>
             <div class="actu"><img src="images/neige.jpg" alt="Tempête de neige"></div>
         </article>
 
         <article>
             <h2>Le dôme de chaleur : un phénomène météorologique extrême</h2>
             <p>
                 Un <strong>dôme de chaleur</strong> survient quand une <em>zone de haute pression emprisonne l'air chaud</em> pendant plusieurs jours. 
                 Cela entraîne des <strong>canicules extrêmes</strong>, dangereuses pour la santé et l'environnement.
             </p>
             <p><a href="https://www.nationalgeographic.fr/environnement/le-dome-de-chaleur-cette-vague-de-chaleur-extreme" target="_blank">Découvrir le phénomène</a></p>
             <div class="actu"><img src="images/dome.png" alt="Dôme de chaleur"></div>
         </article>
 
         <article>
             <h2>Les ouragans et le réchauffement climatique</h2>
             <p>
                 Le <strong>réchauffement climatique</strong> augmente la température des océans, fournissant plus d'énergie aux <strong>ouragans</strong>. 
                 Cela les rend plus <em>fréquents, intenses et destructeurs</em>, en particulier dans les régions tropicales.
             </p>
             <p><a href="https://www.greenpeace.fr/ouragans-et-rechauffement-climatique/" target="_blank">Impact du climat sur les ouragans</a></p>
             <div class="actu"><img src="images/ouragans.jpg" alt="Ouragan vu de l'espace"></div>
         </article>
     </section>
 
 
 
 
 </main>
 
 <?php include 'include/footer.inc.php'; ?>