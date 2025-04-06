<?php

    // Définition des variables spécifiques à cette page
$page_title = " Page Tech ";
$page_heading = " Page Tech - API JSON & XML";
$page_footer_info = "Développement Web-tech";
require "include/header.inc.php";


?>
<div class="apod">
    <section>
        <h2>Page Tech - API JSON & XML</h2>
        <?php


/**
 * Récupère l'Astronomy Picture of the Day (APOD) de la NASA.
 *
 * @return string Le code HTML représentant l'image ou la vidéo APOD et son explication.
 */
function apod(): string {
    $apiUrl = 'https://api.nasa.gov/planetary/apod?api_key=UfGTnHbpDt7Dw5ZEj4MarIpSwtbacUW94ftwITZg';
    $defaultImage = 'images/come.jpg'; // Remplace par l'URL de ton image par défaut

    $jsonData = @file_get_contents($apiUrl);
    $data = json_decode($jsonData);
    $result = '';

    if ($data !== null) {
        $imageUrl = $defaultImage; // Image par défaut
        $description = isset($data->explanation) ? htmlspecialchars($data->explanation) : 'Aucune description disponible.';

        if (isset($data->url)) {
            $lastThreeCharacters = substr($data->url, -3);
            $lastFourCharacters = substr($data->url, -4);

            if ($lastThreeCharacters == "png" || $lastThreeCharacters == "jpg" || $lastFourCharacters == "jpeg") {
                $imageUrl = $data->url; // Utilisation de l'image NASA si disponible
            } else {
                $result .= '<video src="' . htmlspecialchars($data->url) . '" controls>Votre navigateur ne supporte pas la lecture de cette vidéo.</video>';
            }
        }

        // Affichage de l'image (NASA ou par défaut)
        $result .= '<div class="apod-image-container">';
        $result .= '<img src="' . htmlspecialchars($imageUrl) . '" alt="Astronomy Picture of the Day" class="apod-image" />';
        $result .= '</div>';

        // Affichage de la description
        $result .= '<h3>Description de l\'image</h3>';
        $result .= '<p>' . $description . '</p>';
    } else {
        $result .= '<p>Erreur : Impossible de récupérer les données de la NASA.</p>';
    }

    return $result;
}


/**
 * Récupère les informations de localisation à partir de l'adresse IP de l'utilisateur.
 * FLUX xml geo plug
 * @return string Les informations de localisation au format HTML.
 */
function localisation(): string {
    $ip = $_SERVER['REMOTE_ADDR'];
    $url = "http://www.geoplugin.net/xml.gp?ip=$ip";

    $xmlData = @file_get_contents($url);
    if ($xmlData === false) {
        return '<p>Erreur : Impossible de récupérer les informations de localisation.</p>';
    }

    $xml = simplexml_load_string($xmlData);
    if ($xml === false) {
        return '<p>Erreur : Données XML invalides.</p>';
    }

    // Vérification et récupération sécurisée des valeurs
    $latitude = !empty($xml->geoplugin_latitude) ? htmlspecialchars((string)$xml->geoplugin_latitude) : 'Inconnu';
    $longitude = !empty($xml->geoplugin_longitude) ? htmlspecialchars((string)$xml->geoplugin_longitude) : 'Inconnu';
    $city = !empty($xml->geoplugin_city) ? htmlspecialchars((string)$xml->geoplugin_city) : 'Non disponible';
    $region = !empty($xml->geoplugin_regionName) ? htmlspecialchars((string)$xml->geoplugin_regionName) : 'Non disponible';
    $countryName = !empty($xml->geoplugin_countryName) ? htmlspecialchars((string)$xml->geoplugin_countryName) : 'Non disponible';
    $continentName = !empty($xml->geoplugin_continentName) ? htmlspecialchars((string)$xml->geoplugin_continentName) : 'Non disponible';

    return "<p><strong>Latitude</strong> : $latitude</p>
    <p><strong>Longitude</strong> : $longitude</p>
    <p><strong>Ville</strong> : $city</p>
    <p><strong>Région</strong> : $region</p>
    <p><strong>Pays</strong> : $countryName</p>
    <p><strong>Continent</strong> : $continentName</p>";
}

?>


<section>
   <h3>Astronomy picture of the day</h3>                
   <?php

   echo apod();                      
   ?>
</section>

</section>

<section>
    <h3>Position Géographique  de l'utilisateur (avec Geoplugin)
    </h3>
    <?php
    echo localisation();

    ?>
</section>



</div>
</main>


<?php
require "include/footer.inc.php";
?>
