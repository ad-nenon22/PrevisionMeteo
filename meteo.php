<?php
declare(strict_types=1);
require "include/functions.inc.php";
$page_title = "Méteo France en temps réel";
$page_footer_info = "Méteo";

require "include/header.inc.php";
require "include/util.inc.php";


// Démarrer la session pour stocker les informations de la dernière ville
session_start();

// Récupération des régions
$donnees_regions = getRegions();
$donnees_departements = [];
$villes = [];

// Variables pour la recherche et l'affichage
$region_selectionnee = isset($_GET['region']) ? $_GET['region'] : '';
$departement_selectionne = isset($_GET['departement']) ? $_GET['departement'] : '';
$ville_selectionnee = isset($_GET['ville']) ? $_GET['ville'] : '';
$recherche_ville = isset($_GET['ville_nom']) ? $_GET['ville_nom'] : '';
$lat = isset($_GET['lat']) ? $_GET['lat'] : '';
$lon = isset($_GET['lon']) ? $_GET['lon'] : '';

// Variables pour les données météo
$info_ville = null;
$meteo_actuelle = null;
$previsions = null;
$meteo_journaliere = null;
$erreur_ville = null;

// Si une région est sélectionnée, récupérer ses départements
if (!empty($region_selectionnee)) {
    $donnees_departements = getDepartementsByRegion($region_selectionnee);
}

// Si un département est sélectionné, récupérer ses villes
if (!empty($departement_selectionne)) {
    $villes = getVillesByDepartement($departement_selectionne);
}

// Si une ville est sélectionnée via le formulaire région > département > ville
if (!empty($ville_selectionnee) && !empty($departement_selectionne)) {
    foreach ($villes as $ville) {
        if ($ville['nom'] == $ville_selectionnee) {
            $info_ville = $ville;
            $lat = $ville['lat'];
            $lon = $ville['lon'];
            
            // Récupérer le nom de la région
            $region_nom = "";
            foreach ($donnees_regions as $region) {
                if ($region['code'] == $region_selectionnee) {
                    $region_nom = $region['nom'];
                    break;
                }
            }
            
            // Enregistrer la ville consultée
            $ville_consultee = [
                'nom' => $ville_selectionnee,
                'departement' => $departement_selectionne,
                'region' => $region_nom,
                'lat' => $lat,
                'lon' => $lon,
                'date' => date('Y-m-d H:i:s')
            ];
            
            // Utiliser la nouvelle fonction pour sauvegarder la session
            $_SESSION['derniere_ville'] = $ville_consultee;
            
            // Enregistrer dans le CSV
            enregistrerConsultation($ville_selectionnee, $departement_selectionne, $region_nom, $lat, $lon);
            break;
        }
    }
}

// Si recherche directe par nom de ville
if (!empty($recherche_ville)) {
    $info_recherche = rechercherVilleParNom($recherche_ville);
    
    if ($info_recherche) {
        $info_ville = $info_recherche['ville'];
        $lat = $info_ville['lat'];
        $lon = $info_ville['lon'];
        $dept_code = $info_recherche['departement'];
        
        // Trouver la région
        $region_code = "";
        $region_nom = "";
        
        foreach ($donnees_regions as $region) {
            $depts = getDepartementsByRegion($region['code']);
            foreach ($depts as $dept) {
                if ($dept['code'] == $dept_code) {
                    $region_code = $region['code'];
                    $region_nom = $region['nom'];
                    break 2;
                }
            }
        }
        
        // Enregistrer la ville consultée
        $ville_consultee = [
            'nom' => $info_ville['nom'],
            'departement' => $dept_code,
            'region' => $region_nom,
            'lat' => $lat,
            'lon' => $lon,
            'date' => date('Y-m-d H:i:s')
        ];
        
        // Utiliser la nouvelle fonction pour sauvegarder la session
        $_SESSION['derniere_ville'] = $ville_consultee;
        
        // Enregistrer dans le CSV
        enregistrerConsultation($info_ville['nom'], $dept_code, $region_nom, $lat, $lon);
    } else {
        $erreur_ville = "Aucune ville trouvée avec le nom \"" . htmlspecialchars($recherche_ville) . "\". Veuillez vérifier l'orthographe ou essayer une autre ville.";
    }
}

// Si on a des coordonnées, récupérer les données météo
if (!empty($lat) && !empty($lon)) {
    $meteo_actuelle = getMeteoActuelle($lat, $lon);
    $previsions = getPrevisions($lat, $lon);
    $meteo_journaliere = getMeteoDailyForecast($lat, $lon);
}

// Récupérer la dernière ville consultée depuis la session
$derniere_ville = isset($_SESSION['derniere_ville']) ? $_SESSION['derniere_ville'] : null;

// Déterminer l'onglet actif
$onglet_actif = 'recherche-region';
if (isset($_GET['ville_nom'])) {
    $onglet_actif = 'recherche-ville';
} elseif (isset($_GET['carte'])) {
    $onglet_actif = 'carte-interactive';
}

?>

<section>

<div class="meteo-conteneur">

    <div class="meteo-intro">
        <h2>Prévisions Météorologiques Nationales</h2>
       <p> Naviguez par région, explorez notre carte interactive ou saisissez directement le nom de votre ville pour consulter les conditions actuelles ainsi que les prévisions détaillées.</p>
</div>
    </div>


    <!-- Onglets de navigation -->
    <div class="onglets-navigation">
        <button class="onglet-btn <?php echo ($onglet_actif == 'recherche-region') ? 'actif' : ''; ?>" data-onglet="recherche-region">Recherche par région</button>
        <button class="onglet-btn <?php echo ($onglet_actif == 'carte-interactive') ? 'actif' : ''; ?>" data-onglet="carte-interactive">Carte interactive</button>
        <button class="onglet-btn <?php echo ($onglet_actif == 'recherche-ville') ? 'actif' : ''; ?>" data-onglet="recherche-ville">Recherche par ville</button>
    </div>
    
    <!-- Contenu des onglets -->
    <div id="recherche-region" class="contenu-onglet" <?php echo ($onglet_actif != 'recherche-region') ? 'style="display: none;"' : ''; ?>>
        <div class="conteneur-selection">
            <h3>Sélectionnez une région</h3>
            
            <form action="meteo.php" method="get" class="formulaire-meteo">
                <div class="groupe-formulaire">
                    <label for="region">Région :</label>
                    <select name="region" id="region" onchange="this.form.submit()">
                        <option value="">Sélectionnez une région</option>
                        <?php foreach ($donnees_regions as $region): ?>
                        <option value="<?php echo $region['code']; ?>" <?php echo ($region_selectionnee == $region['code']) ? 'selected' : ''; ?>>
                            <?php echo $region['nom']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
            
            <?php if (!empty($region_selectionnee)): ?>
            <form action="meteo.php" method="get" class="formulaire-meteo">
                <input type="hidden" name="region" value="<?php echo $region_selectionnee; ?>">
                
                <div class="groupe-formulaire">
                    <label for="departement">Département :</label>
                    <select name="departement" id="departement" onchange="this.form.submit()">
                        <option value="">Sélectionnez un département</option>
                        <?php foreach ($donnees_departements as $departement): ?>
                        <option value="<?php echo $departement['code']; ?>" <?php echo ($departement_selectionne == $departement['code']) ? 'selected' : ''; ?>>
                            <?php echo $departement['nom']; ?> (<?php echo $departement['code']; ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
            <?php endif; ?>
            
            <?php if (!empty($departement_selectionne)): ?>
            <form action="meteo.php" method="get" class="formulaire-meteo">
                <input type="hidden" name="region" value="<?php echo $region_selectionnee; ?>">
                <input type="hidden" name="departement" value="<?php echo $departement_selectionne; ?>">
                
                <div class="groupe-formulaire">
                    <label for="ville">Ville :</label>
                    <select name="ville" id="ville" onchange="this.form.submit()">
                        <option value="">Sélectionnez une ville</option>
                        <?php foreach ($villes as $ville): ?>
                        <option value="<?php echo $ville['nom']; ?>" <?php echo ($ville_selectionnee == $ville['nom']) ? 'selected' : ''; ?>>
                            <?php echo $ville['nom']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
    

    <div id="carte-interactive" class="contenu-onglet" <?php echo ($onglet_actif != 'carte-interactive') ? 'style="display: none;"' : ''; ?>>
        <div class="france-map-container">
            <img src="images/regions.png" alt="Carte des régions de France" usemap="#image-map" id="main-france-map"/>
            <map name="image-map" id="france-map"/>
               
                <!-- Île-de-France -->
                <area shape="poly" coords="318,131,311,136,304,139,308,149,310,162,318,174,329,188,345,191,349,202,362,202,376,189,392,184,389,175,399,166,392,156,386,147,378,138,372,138,363,137,350,138,345,131,333,129,325,130" alt="Île-de-France" href="?region=11" title="Île-de-France">

                <!-- Centre-Val de Loire -->
                <area shape="poly" coords="303,152,297,155,286,160,277,165,278,172,279,181,273,190,273,202,273,209,266,225,258,237,246,237,240,257,242,271,248,276,263,281,272,297,284,310,285,318,312,320,332,318,344,313,356,310,357,299,371,291,375,277,367,257,368,243,370,235,366,228,371,222,377,208,368,203,361,202,351,201,341,189,327,191,320,181,313,172,308,165,308,156" alt="Centre-Val de Loire" href="?region=24" title="Centre-Val de Loire">

                <!-- Bourgogne-Franche-Comté -->
                <area shape="poly" coords="372,191,377,209,376,219,372,228,372,239,369,253,372,268,374,276,375,290,383,304,391,300,398,299,405,302,412,310,418,317,419,325,421,333,433,336,441,330,446,328,458,332,467,313,480,314,492,325,500,323,512,324,516,307,531,290,531,277,555,251,551,243,562,232,555,224,544,215,537,212,526,206,512,209,500,219,495,230,478,237,461,230,461,220,452,209,443,212,429,214,417,214,407,202,396,191,383,184" alt="Bourgogne-Franche-Comté" href="?region=27" title="Bourgogne-Franche-Comté">

                <!-- Normandie -->
                <area shape="poly" coords="163,90,156,105,171,123,168,143,170,160,181,172,197,176,206,178,216,175,229,174,241,182,250,182,262,192,272,196,274,169,286,152,298,156,302,146,308,132,314,113,314,95,312,78,301,75,289,81,266,89,253,103,244,109,238,118,209,119,191,117,176,94" alt="Normandie" href="?region=28" title="Normandie">

                <!-- Hauts-de-France -->
                <area shape="poly" coords="315,17,348,7,359,24,380,25,388,41,400,48,418,55,418,71,426,85,421,94,415,105,416,113,403,117,401,127,399,138,392,147,375,135,343,129,323,127,315,121,316,105,320,89,308,72,307,61,313,53,313,42,312,26" alt="Hauts-de-France" href="?region=32" title="Hauts-de-France">

                <!-- Grand Est -->
                <area shape="poly" coords="451,68,439,76,428,80,423,92,417,110,419,120,408,115,404,128,404,135,395,150,395,174,398,187,418,211,452,207,465,222,496,222,513,205,541,212,572,238,579,209,581,184,587,160,602,137,574,130,555,129,542,130,524,110,508,111,493,106,478,110,456,89,449,77" alt="Grand Est" href="?region=44" title="Grand Est">

                <!-- Pays de la Loire -->
                <area shape="poly" coords="126,239,137,232,153,226,175,221,188,206,185,187,187,171,209,177,216,178,231,180,241,188,248,191,260,194,268,203,266,216,257,231,243,237,237,260,229,270,208,273,193,279,200,296,204,316,195,324,171,318,152,312,139,295,133,278,144,272,135,261,143,249,126,251,120,248"  href="?region=52" title="Pays de la Loire">

                <!-- Bretagne -->
                <area shape="poly" coords="24,163,51,162,68,159,80,151,99,154,115,172,137,168,145,170,162,166,173,176,185,172,186,202,174,219,153,220,139,224,123,238,114,236,95,224,84,219,71,208,57,207,43,199,30,198,44,178,41,168,22,168" alt="Bretagne" href="?region=53" title="Bretagne">

                <!-- Nouvelle-Aquitaine -->
                <area shape="poly" coords="197,280,207,319,183,325,181,340,176,354,175,361,197,397,174,383,178,394,170,414,179,431,181,439,170,445,166,458,161,496,145,522,158,534,166,544,179,551,188,554,198,561,207,553,216,547,218,535,227,527,217,517,212,505,216,491,226,486,253,478,273,468,280,456,292,432,301,417,314,416,328,416,334,409,343,390,352,390,351,372,346,359,355,350,349,333,338,321,327,320,310,323,293,324,281,313,274,305,264,288,260,278,241,277,233,270,215,272" alt="Nouvelle-Aquitaine" href="?region=75" title="Nouvelle-Aquitaine">

                <!-- Occitanie -->
                <area shape="poly" coords="211,555,227,569,246,572,254,570,256,561,282,569,295,572,314,582,317,588,331,593,341,595,353,598,365,591,375,588,375,559,396,533,429,512,436,522,444,507,454,501,460,488,466,483,455,472,450,460,438,465,424,449,404,425,392,418,380,425,376,436,363,423,351,436,333,439,328,423,318,417,299,416,294,430,276,444,281,462,273,471,259,477,246,484,232,487,225,491,220,496,218,506,224,514,224,528,222,537,214,546" alt="Occitanie" href="?region=76" title="Occitanie">

                <!-- Auvergne-Rhône-Alpes -->
                <area shape="poly" coords="342,318,357,344,349,361,351,385,349,404,337,404,332,419,343,437,356,427,366,423,374,427,381,423,392,420,414,427,419,440,441,461,459,462,479,463,492,468,508,469,495,451,501,440,511,433,521,421,531,420,532,403,550,403,566,393,568,386,558,366,553,359,560,349,551,338,549,318,536,325,532,334,513,337,518,320,502,327,493,326,477,315,470,316,466,325,459,334,448,333,434,335,419,331,420,322,412,313,403,303,396,304,374,299,359,302,356,312" alt="Auvergne-Rhône-Alpes" href="?region=84" title="Auvergne-Rhône-Alpes">

                <!-- Provence-Alpes-Côte d'Azur -->
                <area shape="poly" coords="458,466,467,483,455,499,445,516,459,524,471,523,481,523,497,530,520,539,541,540,553,536,554,521,572,511,584,498,595,483,598,475,586,473,570,467,558,448,559,435,562,431,552,417,542,406,533,409,536,417,526,421,518,428,510,435,503,445,498,455,507,467,489,468,481,467,466,464" alt="Provence-Alpes-Côte d'Azur" href="?region=93" title="Provence-Alpes-Côte d'Azur">

                <!-- Corse -->
                <area shape="poly" coords="613,515,612,538,601,540,582,552,581,564,581,572,581,582,589,598,596,610,612,621,614,612,615,600,618,580,622,565" alt="Corse" href="?region=94" title="Corse">
            </map>
            
            
        </div>
    </div>
    
    <div id="recherche-ville" class="contenu-onglet" <?php echo ($onglet_actif != 'recherche-ville') ? 'style="display: none;"' : ''; ?>>
        <div class="formulaire-recherche">
            <h3>Recherche directe par nom de ville</h3>
            <form method="GET" action="meteo.php" class="recherche-ville-form">
                <div class="champ-recherche">
                    <input type="text" name="ville_nom" id="ville_nom" placeholder="Entrez le nom d'une ville..." required 
                        value="<?php echo htmlspecialchars($recherche_ville); ?>">
                    <button type="submit" class="bouton-recherche">Rechercher</button>
                </div>
            </form>
            
            <?php if ($erreur_ville): ?>
            <div class="message-erreur">
                <?php echo $erreur_ville; ?>
            </div>
            <?php endif; ?>
            
            <div class="exemples-villes">
                <p>Exemples : <a href="?ville_nom=Cergy">Cergy</a>, <a href="?ville_nom=Orleans">Orleans</a>, 
                <a href="?ville_nom=Paris">Paris</a>, <a href="?ville_nom=Lyon">Lyon</a>, 
                <a href="?ville_nom=Marseille">Marseille</a>, <a href="?ville_nom=Toulouse">Toulouse</a></p>
            </div>
        </div>
    </div>
    
    <!-- Affichage des données météo -->
    <?php if ($meteo_actuelle && $previsions): ?>
    <div class="meteo-resultats">
        <div class="meteo-actuelle">
            <h2>Météo actuelle à <?php echo isset($info_ville['nom']) ? htmlspecialchars($info_ville['nom']) : (isset($recherche_ville) && !empty($recherche_ville) ? htmlspecialchars($recherche_ville) : 'la position sélectionnée'); ?></h2>
            
            <div class="meteo-actuelle-contenu">
                <div class="meteo-icone">
                    <img src="https://openweathermap.org/img/wn/<?php echo $meteo_actuelle['weather'][0]['icon']; ?>@4x.png" alt="<?php echo $meteo_actuelle['weather'][0]['description']; ?>">
                    <p><?php echo ucfirst($meteo_actuelle['weather'][0]['description']); ?></p>
                </div>
                
                <div class="meteo-details">
                    <div class="temperature-principale">
                        <span class="valeur"><?php echo round($meteo_actuelle['main']['temp']); ?></span>
                        <span class="unite">°C</span>
                    </div>
                    
                    <div class="details-secondaires">
                        <div class="detail">
                            <span class="label">Ressenti</span>
                            <span class="valeur"><?php echo round($meteo_actuelle['main']['feels_like']); ?>°C</span>
                        </div>
                        <div class="detail">
                            <span class="label">Humidité</span>
                            <span class="valeur"><?php echo $meteo_actuelle['main']['humidity']; ?>%</span>
                        </div>
                        <div class="detail">
                            <span class="label">Vent</span>
                            <span class="valeur"><?php echo round($meteo_actuelle['wind']['speed'] * 3.6); ?> km/h</span>
                        </div>
                    </div>
                </div>
                
                <div class="meteo-details-supplementaires">
                    <div class="detail-supp">
                        <span class="label">Pression</span>
                        <span class="valeur"><?php echo $meteo_actuelle['main']['pressure']; ?> hPa</span>
                    </div>
                    <div class="detail-supp">
                        <span class="label">Nuages</span>
                        <span class="valeur"><?php echo $meteo_actuelle['clouds']['all']; ?>%</span>
                    </div>
                    <div class="detail-supp">
                        <span class="label">Visibilité</span>
                        <span class="valeur"><?php echo round($meteo_actuelle['visibility'] / 1000, 1); ?> km</span>
                    </div>
                    <?php if (isset($meteo_actuelle['rain']['1h'])): ?>
                    <div class="detail-supp">
                        <span class="label">Pluie (1h)</span>
                        <span class="valeur"><?php echo $meteo_actuelle['rain']['1h']; ?> mm</span>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($meteo_actuelle['snow']['1h'])): ?>
                    <div class="detail-supp">
                        <span class="label">Neige (1h)</span>
                        <span class="valeur"><?php echo $meteo_actuelle['snow']['1h']; ?> mm</span>
                    </div>
                    <?php endif; ?>
                    <div class="detail-supp">
                        <span class="label">Lever du soleil</span>
                        <span class="valeur"><?php echo date('H:i', $meteo_actuelle['sys']['sunrise']); ?></span>
                    </div>
                    <div class="detail-supp">
                        <span class="label">Coucher du soleil</span>
                        <span class="valeur"><?php echo date('H:i', $meteo_actuelle['sys']['sunset']); ?></span>
                    </div>
                </div>
            </div>
        </div>
        


<!-- Remplacer le formulaire de sélection de jours par un bouton pour les détails horaires -->
<div class="details-horaires-toggle">
    <button id="toggle-horaire" class="bouton-toggle">
        <?php echo (isset($_GET['details_horaires']) && $_GET['details_horaires'] == '1') ? 'Masquer les détails horaires' : 'Afficher les détails heure par heure'; ?>
    </button>
</div>


<!-- Section pour les détails horaires -->
<?php if (isset($_GET['details_horaires']) && $_GET['details_horaires'] == '1' && isset($previsions['list'])): ?>
<div class="meteo-horaire">
    <h3>Prévisions heure par heure pour aujourd'hui</h3>
    
    <div class="horaire-liste">
        <?php 
        $jour_actuel = date('Y-m-d');
        $heures_affichees = 0;
        
        foreach ($previsions['list'] as $prevision) {
            $heure_date = date('Y-m-d', $prevision['dt']);
            
            // N'afficher que les prévisions pour aujourd'hui
            if ($heure_date == $jour_actuel) {
                $heure = date('H:i', $prevision['dt']);
        ?>
        <div class="prevision-heure">
            <div class="heure"><?php echo $heure; ?></div>
            <img src="https://openweathermap.org/img/wn/<?php echo $prevision['weather'][0]['icon']; ?>@2x.png" 
                 alt="<?php echo $prevision['weather'][0]['description']; ?>">
            <div class="temperature"><?php echo round($prevision['main']['temp']); ?>°C</div>
            <div class="details-heure">
                <div class="detail-heure">
                    <span class="label">Ressenti</span>
                    <span class="valeur"><?php echo round($prevision['main']['feels_like']); ?>°C</span>
                </div>
                <div class="detail-heure">
                    <span class="label">Humidité</span>
                    <span class="valeur"><?php echo $prevision['main']['humidity']; ?>%</span>
                </div>
                <div class="detail-heure">
                    <span class="label">Vent</span>
                    <span class="valeur"><?php echo round($prevision['wind']['speed'] * 3.6); ?> km/h</span>
                </div>
                <?php if (isset($prevision['rain']['3h'])): ?>
                <div class="detail-heure">
                    <span class="label">Pluie (3h)</span>
                    <span class="valeur"><?php echo $prevision['rain']['3h']; ?> mm</span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
                $heures_affichees++;
            }
            
            // Si pas de prévisions pour aujourd'hui, afficher celles de demain
            if ($heures_affichees == 0 && $heure_date > $jour_actuel) {
                // Afficher un message indiquant qu'on montre demain
                echo '<div class="message-info">Pas de prévisions restantes pour aujourd\'hui. Voici les prévisions pour demain :</div>';
                // Et sortir de cette condition pour afficher les données
                break;
            }
        }
        
        // Si aucune prévision n'a été affichée, montrer les premières disponibles
        if ($heures_affichees == 0) {
            $compteur = 0;
            foreach ($previsions['list'] as $prevision) {
                if ($compteur >= 8) break; // Limiter à 8 prévisions
                
                $heure = date('H:i', $prevision['dt']);
                $jour = date('d/m', $prevision['dt']);
        ?>
        <div class="prevision-heure">
            <div class="heure"><?php echo $heure; ?> <span class="jour-prevision"><?php echo $jour; ?></span></div>
            <img src="https://openweathermap.org/img/wn/<?php echo $prevision['weather'][0]['icon']; ?>@2x.png" 
                 alt="<?php echo $prevision['weather'][0]['description']; ?>">
            <div class="temperature"><?php echo round($prevision['main']['temp']); ?>°C</div>
            <div class="details-heure">
                <div class="detail-heure">
                    <span class="label">Ressenti</span>
                    <span class="valeur"><?php echo round($prevision['main']['feels_like']); ?>°C</span>
                </div>
                <div class="detail-heure">
                    <span class="label">Humidité</span>
                    <span class="valeur"><?php echo $prevision['main']['humidity']; ?>%</span>
                </div>
                <div class="detail-heure">
                    <span class="label">Vent</span>
                    <span class="valeur"><?php echo round($prevision['wind']['speed'] * 3.6); ?> km/h</span>
                </div>
                <?php if (isset($prevision['rain']['3h'])): ?>
                <div class="detail-heure">
                    <span class="label">Pluie (3h)</span>
                    <span class="valeur"><?php echo $prevision['rain']['3h']; ?> mm</span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
                $compteur++;
            }
        }
        ?>
    </div>
</div>
<?php endif; ?>




         <!-- Affichage de la météo journalière sur 7 jours -->
        <?php if ($meteo_journaliere && isset($meteo_journaliere['daily'])): ?>
        <div class="meteo-journaliere">
            <h3>Prévisions détaillées jour par jour</h3>
            
            <div class="journaliere-liste">
                <?php 
                // Limiter à 7 jours maximum
                $jours_max = min(count($meteo_journaliere['daily']), 7);
                
                for ($i = 0; $i < $jours_max; $i++): 
                    $jour = $meteo_journaliere['daily'][$i];
                    $date = $jour['dt'];
                    $jour_semaine = date('l', $date);
                    
                    // Traduire le jour de la semaine en français
                    $jours_fr = [
                        'Monday' => 'Lundi',
                        'Tuesday' => 'Mardi',
                        'Wednesday' => 'Mercredi',
                        'Thursday' => 'Jeudi',
                        'Friday' => 'Vendredi',
                        'Saturday' => 'Samedi',
                        'Sunday' => 'Dimanche'
                    ];
                    $jour_semaine_fr = $jours_fr[$jour_semaine];
                    $jour_format = date('d/m', $date);
                ?>
                <div class="jour-meteo">
                    <div class="jour-entete">
                        <div class="jour-date">
                            <span class="jour-nom"><?php echo $jour_semaine_fr; ?></span>
                            <span class="jour-numero"><?php echo $jour_format; ?></span>
                        </div>
                        <div class="jour-icone">
                            <img src="https://openweathermap.org/img/wn/<?php echo $jour['weather'][0]['icon']; ?>@2x.png" alt="<?php echo $jour['weather'][0]['description']; ?>">
                        </div>
                        <div class="jour-temperatures">
                            <div class="temp-max"><?php echo round($jour['temp']['max']); ?>°C</div>
                            <div class="temp-min"><?php echo round($jour['temp']['min']); ?>°C</div>
                        </div>
                    </div>
                    
                    <div class="jour-details">
                        <div class="jour-description">
                            <?php echo ucfirst($jour['weather'][0]['description']); ?>
                        </div>
                        
                        <div class="jour-infos">
                            <div class="jour-info">
                                <span class="info-label">Matin</span>
                                <span class="info-valeur"><?php echo round($jour['temp']['morn']); ?>°C</span>
                            </div>
                            <div class="jour-info">
                                <span class="info-label">Après-midi</span>
                                <span class="info-valeur"><?php echo round($jour['temp']['day']); ?>°C</span>
                            </div>
                            <div class="jour-info">
                                <span class="info-label">Soir</span>
                                <span class="info-valeur"><?php echo round($jour['temp']['eve']); ?>°C</span>
                            </div>
                            <div class="jour-info">
                                <span class="info-label">Nuit</span>
                                <span class="info-valeur"><?php echo round($jour['temp']['night']); ?>°C</span>
                            </div>
                        </div>
                        
                        <div class="jour-details-supplementaires">
                            <div class="jour-detail-supp">
                                <span class="detail-label">Humidité</span>
                                <span class="detail-valeur"><?php echo $jour['humidity']; ?>%</span>
                            </div>
                            <div class="jour-detail-supp">
                                <span class="detail-label">Vent</span>
                                <span class="detail-valeur"><?php echo round($jour['wind_speed'] * 3.6); ?> km/h</span>
                            </div>
                            <div class="jour-detail-supp">
                                <span class="detail-label">Probabilité de pluie</span>
                                <span class="detail-valeur"><?php echo round($jour['pop'] * 100); ?>%</span>
                            </div>
                            <?php if (isset($jour['rain'])): ?>
                            <div class="jour-detail-supp">
                                <span class="detail-label">Pluie</span>
                                <span class="detail-valeur"><?php echo $jour['rain']; ?> mm</span>
                            </div>
                            <?php endif; ?>
                            <?php if (isset($jour['snow'])): ?>
                            <div class="jour-detail-supp">
                                <span class="detail-label">Neige</span>
                                <span class="detail-valeur"><?php echo $jour['snow']; ?> mm</span>
                            </div>
                            <?php endif; ?>
                            <div class="jour-detail-supp">
                                <span class="detail-label">Indice UV</span>
                                <span class="detail-valeur"><?php echo $jour['uvi']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>

        
        <div class="previsions-jours">
            <h3>Prévisions sur 5 jours</h3>
            
            <div class="previsions-liste">
                <?php 
                $jour_actuel = '';
                $jours_affiches = 0;
                $jours_max = 5;
                $previsions_par_jour = [];
                
                // Regrouper les prévisions par jour
                foreach ($previsions['list'] as $prevision) {
                    $jour = date('Y-m-d', $prevision['dt']);
                    if (!isset($previsions_par_jour[$jour])) {
                        $previsions_par_jour[$jour] = [];
                    }
                    $previsions_par_jour[$jour][] = $prevision;
                }
                
                // Limiter à 5 jours

                $previsions_par_jour = array_slice($previsions_par_jour, 0, $jours_max);
                
                foreach ($previsions_par_jour as $jour => $previsions_jour) {
                    // Calculer les moyennes/max/min pour la journée
                    $temp_min = 100;
                    $temp_max = -100;
                    $icone_principale = '';
                    $description_principale = '';
                    
                    foreach ($previsions_jour as $prevision) {
                        if ($prevision['main']['temp_min'] < $temp_min) {
                            $temp_min = $prevision['main']['temp_min'];
                        }
                        if ($prevision['main']['temp_max'] > $temp_max) {
                            $temp_max = $prevision['main']['temp_max'];
                            $icone_principale = $prevision['weather'][0]['icon'];
                            $description_principale = $prevision['weather'][0]['description'];
                        }
                    }
                    
                    $jour_format = date('d/m', strtotime($jour));
                    $jour_semaine = date('l', strtotime($jour));
                    
                    // Traduire le jour de la semaine en français
                    $jours_fr = [
                        'Monday' => 'Lundi',
                        'Tuesday' => 'Mardi',
                        'Wednesday' => 'Mercredi',
                        'Thursday' => 'Jeudi',
                        'Friday' => 'Vendredi',
                        'Saturday' => 'Samedi',
                        'Sunday' => 'Dimanche'
                    ];
                    $jour_semaine_fr = $jours_fr[$jour_semaine];
                ?>
                <div class="prevision-jour">
                    <div class="jour"><?php echo $jour_semaine_fr; ?> <span class="date"><?php echo $jour_format; ?></span></div>
                    <img src="https://openweathermap.org/img/wn/<?php echo $icone_principale; ?>@2x.png" alt="<?php echo $description_principale; ?>">
                    <div class="temperature">
                        <span class="max"><?php echo round($temp_max); ?>°</span> / 
                        <span class="min"><?php echo round($temp_min); ?>°</span>
                    </div>
                    <div class="description"><?php echo ucfirst($description_principale); ?></div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Affichage de la dernière ville consultée -->

    <?php if ($derniere_ville): ?>
    <div class="derniere-consultation">
        <h3>Dernière ville consultée</h3>
        <div class="derniere-ville-details">
            <h4 class="ville-nom"><?php echo htmlspecialchars($derniere_ville['nom']); ?></h4>
            <p class="ville-localisation">
                <span>Département :</span> <?php echo htmlspecialchars($derniere_ville['departement']); ?> | 
                <span>Région :</span> <?php echo htmlspecialchars($derniere_ville['region']); ?>
            </p>
            <p class="date-consultation">Consultée le <?php echo formatDateFr($derniere_ville['date'], true); ?></p>
        </div>
        <div class="derniere-ville-actions">
            <form action="meteo.php" method="get" class="action-form">
                <input type="hidden" name="ville_nom" value="<?php echo htmlspecialchars($derniere_ville['nom']); ?>">
                <button type="submit" class="bouton-primaire">Consulter à nouveau</button>
            </form>
        </div>
    </div>
 
</section>



    <?php endif; ?>



<script>

// Script pour la navigation par onglets
document.addEventListener('DOMContentLoaded', function() {
    const onglets = document.querySelectorAll('.onglet-btn');
    
    onglets.forEach(function(onglet) {
        onglet.addEventListener('click', function() {
            const ongletId = this.getAttribute('data-onglet');
            
            // Masquer tous les contenus d'onglets
            document.querySelectorAll('.contenu-onglet').forEach(function(contenu) {
                contenu.style.display = 'none';
            });
            
            // Afficher le contenu de l'onglet sélectionné
            document.getElementById(ongletId).style.display = 'block';
            
            // Mettre à jour la classe active
            onglets.forEach(function(o) {
                o.classList.remove('actif');
            });
            this.classList.add('actif');
        });
    });
    
    // Script pour afficher/masquer les détails des prévisions journalières
    const joursMeteo = document.querySelectorAll('.jour-meteo');
    
    joursMeteo.forEach(function(jour) {
        jour.addEventListener('click', function() {
            this.classList.toggle('ouvert');
        });
    });
});
</script>

<script>
// Gestion du bouton pour afficher/masquer les détails horaires
document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('toggle-horaire');
    
    if (toggleButton) {
        toggleButton.addEventListener('click', function() {
            // Récupérer l'URL actuelle
            let url = new URL(window.location.href);
            let params = new URLSearchParams(url.search);
            
            // Inverser l'état actuel
            if (params.has('details_horaires') && params.get('details_horaires') === '1') {
                params.set('details_horaires', '0');
            } else {
                params.set('details_horaires', '1');
            }
            
            // Mettre à jour l'URL et recharger la page
            url.search = params.toString();
            window.location.href = url.toString();
        });
    }
});
</script>

<script src="js/meteo.js"></script>



<?php
    require "./include/footer.inc.php";
?>