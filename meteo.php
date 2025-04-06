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

<div class="meteo-conteneur">
    <div class="meteo-intro">
        <h2>Prévisions Météo pour la France</h2>
        <p>Consultez les prévisions météorologiques détaillées pour toutes les villes de France métropolitaine. Utilisez la recherche par région, la carte interactive ou recherchez directement une ville pour obtenir les conditions météorologiques actuelles et les prévisions sur plusieurs jours.</p>
    </div>

    
    <!-- Message d'aide -->
    <div class="message-aide">
        <h4>Comment utiliser ce service ?</h4>
        <p>Vous avez trois façons de consulter la météo : 
            <strong>1)</strong> Sélectionnez une région, puis un département et enfin une ville, 
            <strong>2)</strong> Cliquez sur une région dans la carte interactive, ou 
            <strong>3)</strong> Recherchez directement une ville par son nom.
        </p>
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
            <img src="images/france.png" alt="Carte des régions de France" usemap="#francemap" id="main-france-map">
            <map name="francemap" id="france-map">
                <!-- Guadeloupe -->
                <area shape="rect" coords="360,555,424,611" alt="Guadeloupe" href="?region=01" title="Guadeloupe">
                <!-- Martinique -->
                <area shape="rect" coords="490,568,525,610" alt="Martinique" href="?region=02" title="Martinique">
                <!-- Guyane -->
                <area shape="poly" coords="102,432,158,475,168,499,107,600,13,602,40,543,23,501,16,461,24,442,45,414" alt="Guyane" href="?region=03" title="Guyane">
                <!-- La Réunion -->
                <area shape="rect" coords="432,565,482,612" alt="La Réunion" href="?region=04" title="La Réunion">
                <!-- Mayotte -->
                <area shape="rect" coords="531,584,554,611" alt="Mayotte" href="?region=06" title="Mayotte">
                <!-- Île-de-France -->
                <area shape="poly" coords="344,195,332,184,317,186,303,167,297,148,304,140,321,138,334,142,360,144,371,157,371,183,357,178,354,187" alt="Île-de-France" href="?region=11" title="Île-de-France">
                <!-- Centre-Val de Loire -->
                <area shape="poly" coords="326,193,352,201,347,250,353,268,328,292,288,295,272,274,264,262,252,261,240,249,247,231,275,207,271,186,276,166,292,160,315,195,302,186,359,205,346,216" alt="Centre-Val de Loire" href="?region=24" title="Centre-Val de Loire">
                <!-- Bourgogne-Franche-Comté -->
                <area shape="poly" coords="390,281,364,275,357,244,359,224,365,214,358,187,377,184,385,204,402,212,427,209,441,231,459,229,480,210,498,214,513,225,509,246,496,261,485,279,470,301,461,306,445,289,428,295,419,303,400,309,399,296" alt="Bourgogne-Franche-Comté" href="?region=27" title="Bourgogne-Franche-Comté">
                <!-- Normandie -->
                <area shape="poly" coords="184,105,157,106,179,172,215,172,237,171,259,185,267,178,267,158,289,153,291,138,300,130,299,112,300,99,291,89,241,110,234,129" alt="Normandie" href="?region=28" title="Normandie">
                <!-- Hauts-de-France -->
                <area shape="poly" coords="314,27,299,39,300,76,300,87,312,112,310,134,332,135,352,136,376,150,377,127,394,117,400,94,397,75,382,75,377,65,360,64,360,49,342,45,332,30" alt="Hauts-de-France" href="?region=32" title="Hauts-de-France">
                <!-- Grand Est -->
                <area shape="poly" coords="496,123,488,121,472,116,463,120,453,112,438,104,432,111,430,81,421,93,410,92,405,113,400,129,388,132,378,178,397,204,432,201,447,221,466,207,494,197,532,231,559,150,519,138,511,143,501,135" alt="Grand Est" href="?region=44" title="Grand Est">
                <!-- Pays de la Loire -->
                <area shape="poly" coords="261,217,241,227,236,253,197,266,207,300,180,303,140,265,139,240,157,231,178,225,198,208,199,182,225,179,245,184,262,199" alt="Pays de la Loire" href="?region=52" title="Pays de la Loire">
                <!-- Bretagne -->
                <area shape="poly" coords="111,147,40,160,48,200,64,229,117,250,152,222,176,211,190,200,189,180,176,184,166,173,161,157" alt="Bretagne" href="?region=53" title="Bretagne">
                <!-- Nouvelle-Aquitaine -->
                <area shape="poly" coords="210,270,212,297,191,313,191,368,176,479,217,508,233,475,220,453,225,437,242,432,262,429,270,412,284,389,291,375,315,378,320,361,330,350,327,332,333,313,317,303,289,307,274,296,261,276,240,260" alt="Nouvelle-Aquitaine" href="?region=75" title="Nouvelle-Aquitaine">
                <!-- Occitanie -->
                <area shape="poly" coords="300,382,280,406,278,428,228,447,228,460,238,467,235,484,223,505,235,513,260,513,261,501,311,521,320,532,331,526,337,534,358,526,361,508,369,481,406,464,422,450,425,436,420,424,400,425,386,396,363,387,358,406,347,391,329,408,315,391,308,388" alt="Occitanie" href="?region=76" title="Occitanie">
                <!-- Auvergne-Rhône-Alpes -->
                <area shape="poly" coords="481,376,500,374,523,359,512,344,515,327,506,297,493,303,483,319,470,310,452,306,439,295,426,318,420,308,401,318,387,308,390,297,380,284,352,280,342,286,332,296,343,323,337,334,338,365,331,359,320,385,326,399,335,393,344,379,356,395,361,378,377,382,398,398,401,419,426,416,440,412,457,428,451,407,465,400,487,385" alt="Auvergne-Rhône-Alpes" href="?region=84" title="Auvergne-Rhône-Alpes">
                <!-- Provence-Alpes-Côte d'Azur -->
                <area shape="poly" coords="443,426,426,421,433,439,413,467,433,480,461,485,482,502,513,482,536,459,544,434,522,431,512,422,511,406,517,394,507,386,499,379,495,388,511,399,493,381,499,388,507,388,505,386,500,378,498,383,491,377,491,391,482,392,477,399,467,405,462,415,470,425,459,436,500,378,507,388,495,388,493,381,499,388" alt="Provence-Alpes-Côte d'Azur" href="?region=93" title="Provence-Alpes-Côte d'Azur">
                <!-- Corse -->
                <area shape="poly" coords="611,499,622,521,624,545,607,592,594,579,576,540" alt="Corse" href="?region=94" title="Corse">
            </map>
            
            <!-- Liste des régions avec leurs liens corrects -->
            <?php foreach ($donnees_regions as $region): ?>
            <a href="meteo.php?region=<?php echo $region['code']; ?>" class="region" id="region-<?php echo $region['code']; ?>" title="<?php echo $region['nom']; ?>">
                <div class="region-label"><?php echo $region['nom']; ?></div>
                <div class="region-code"><?php echo $region['code']; ?></div>
            </a>
            <?php endforeach; ?>
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
                <p>Exemples : <a href="?ville_nom=Paris">Paris</a>, <a href="?ville_nom=Marseille">Marseille</a>, 
                <a href="?ville_nom=Lyon">Lyon</a>, <a href="?ville_nom=Cergy">Cergy</a>, <a href="?ville_nom=Sannois">Sannois</a>, 
                <a href="?ville_nom=Neuville">Neuville</a>, <a href="?ville_nom=Ermont">Ermont</a></p>
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
        
         <!-- Affichage de la météo journalière -->
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

<script src="js/meteo.js"></script>



<?php
    require "./include/footer.inc.php";
?>