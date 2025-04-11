<?php

declare(strict_types=1);
/**
 * Fonctions utilitaires pour le projet Prévisions Météo & Climat
 */

/**
 * Fonction pour appeler une API et récupérer les données
 * 
 * @param string $url URL de l'API à appeler
 * @return array Tableau contenant les données ou une erreur
 */
function callAPI($url) {
    $result = ['error' => false, 'data' => null];
    
    // Initialisation de cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    // Exécution de la requête
    $response = curl_exec($ch);
    
    // Vérification des erreurs
    if (curl_errno($ch)) {
        $result['error'] = true;
        $result['message'] = curl_error($ch);
    } else {
        $result['data'] = json_decode($response, true);
    }
    
    // Fermeture de la connexion
    curl_close($ch);
    
    return $result;
}

/**
 * Récupère la liste des régions de France
 * 
 * @return array Liste des régions
 */
function getRegions() {
    $url = "https://geo.api.gouv.fr/regions?fields=nom,code";
    $response = callAPI($url);
    
    if ($response['error'] || empty($response['data'])) {
        // En cas d'erreur, retourner un tableau vide
        return [];
    }
    
    // Formater les données pour l'affichage
    $regions = [];
    foreach ($response['data'] as $region) {
        $regions[] = [
            'code' => $region['code'],
            'nom' => $region['nom']
        ];
    }
    
    return $regions;
}

/**
 * Récupère la liste des départements d'une région
 * 
 * @param string $region_code Code de la région
 * @return array Liste des départements
 */
function getDepartementsByRegion($region_code) {
    $url = "https://geo.api.gouv.fr/regions/{$region_code}/departements?fields=nom,code";
    $response = callAPI($url);
    
    if ($response['error'] || empty($response['data'])) {
        // En cas d'erreur, retourner un tableau vide
        return [];
    }
    
    // Formater les données pour l'affichage
    $departements = [];
    foreach ($response['data'] as $departement) {
        $departements[] = [
            'code' => $departement['code'],
            'nom' => $departement['nom']
        ];
    }
    
    return $departements;
}

/**
 * Récupère la liste des villes d'un département
 * 
 * @param string $dept_code Code du département
 * @return array Liste des villes
 */
function getVillesByDepartement($dept_code) {
    $url = "https://geo.api.gouv.fr/departements/{$dept_code}/communes?fields=nom,code,centre,codesPostaux&format=json&geometry=centre";
    $response = callAPI($url);
    
    if ($response['error'] || empty($response['data'])) {
        // En cas d'erreur, retourner un tableau vide
        return [];
    }
    
    // Formater les données pour l'affichage
    $villes = [];
    $count = 0;
    
    // Limiter à 20 villes pour éviter de surcharger l'interface
    foreach ($response['data'] as $commune) {
        if ($count >= 50) break;
        
        // Vérifier que la commune a des coordonnées
        if (isset($commune['centre']) && isset($commune['centre']['coordinates'])) {
            $code_postal = isset($commune['codesPostaux'][0]) ? $commune['codesPostaux'][0] : "{$dept_code}000";
            $lon = $commune['centre']['coordinates'][0];
            $lat = $commune['centre']['coordinates'][1];
            
            $villes[] = [
                "nom" => $commune['nom'],
                "code_postal" => $code_postal,
                "lat" => $lat,
                "lon" => $lon
            ];
            
            $count++;
        }
    }
    
    return $villes;
}

/**
 * Récupère les informations d'une ville par son nom
 * 
 * @param string $ville_nom Nom de la ville
 * @return array|null Informations sur la ville ou null si non trouvée
 */
function rechercherVilleParNom($ville_nom) {
    // Encoder le nom de la ville pour l'URL
    $ville_encodee = urlencode($ville_nom);
    $url = "https://geo.api.gouv.fr/communes?nom={$ville_encodee}&fields=nom,code,centre,codesPostaux,departement&format=json&geometry=centre&boost=population";
    
    $response = callAPI($url);
    
    if ($response['error'] || empty($response['data'])) {
        return null;
    }
    
    // Prendre la première ville correspondante (la plus peuplée grâce au boost=population)
    if (isset($response['data'][0])) {
        $commune = $response['data'][0];
        
        // Vérifier que la commune a des coordonnées
        if (isset($commune['centre']) && isset($commune['centre']['coordinates'])) {
            $code_postal = isset($commune['codesPostaux'][0]) ? $commune['codesPostaux'][0] : "";
            $lon = $commune['centre']['coordinates'][0];
            $lat = $commune['centre']['coordinates'][1];
            $dept_code = isset($commune['departement']) ? $commune['departement']['code'] : "";
            
            return [
                'ville' => [
                    "nom" => $commune['nom'],
                    "code_postal" => $code_postal,
                    "lat" => $lat,
                    "lon" => $lon
                ],
                'departement' => $dept_code
            ];
        }
    }
    
    return null;
}

/**
 * Génère un nombre aléatoire entre min et max
 * 
 * @param int $min Valeur minimale
 * @param int $max Valeur maximale
 * @return int Nombre aléatoire
 */
function randomNumber($min, $max) {
    return rand($min, $max);
}

/**
 * Récupère une image aléatoire du dossier images
 * 
 * @param string $directory Dossier contenant les images
 * @param array $extensions Extensions autorisées
 * @return string|null Nom du fichier ou null si aucune image trouvée
 */
function getRandomImage($directory = 'images', $extensions = ['jpg', 'jpeg', 'png', 'gif']) {
    $images = [];
    
    // Parcourir le dossier pour trouver les images
    if ($handle = opendir($directory)) {
        while (false !== ($file = readdir($handle))) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if ($file != "." && $file != ".." && in_array($extension, $extensions)) {
                $images[] = $file;
            }
        }
        closedir($handle);
    }
    
    // Retourner une image aléatoire ou null si aucune image trouvée
    if (count($images) > 0) {
        return $images[array_rand($images)];
    }
    
    return null;
}

/**
 * Enregistre une consultation de ville dans le fichier CSV
 * 
 * @param string $ville_nom Nom de la ville consultée
 * @param string $dept_code Code du département
 * @param string $region_nom Nom de la région
 * @param float $lat Latitude
 * @param float $lon Longitude
 * @return bool Succès ou échec de l'enregistrement
 */
function enregistrerConsultation($ville_nom, $dept_code, $region_nom, $lat, $lon) {
    $date = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Chemin vers le fichier CSV
    $fichier_csv = dirname(__DIR__) . '/data/consultations.csv';
    
    // Créer le répertoire data s'il n'existe pas
    if (!file_exists(dirname($fichier_csv))) {
        mkdir(dirname($fichier_csv), 0777, true);
    }
    
    // Créer le fichier avec en-têtes s'il n'existe pas
    if (!file_exists($fichier_csv)) {
        $en_tetes = "date,ip,departement,ville,region,lat,lon\n";
        file_put_contents($fichier_csv, $en_tetes);
    }
    
    // Préparer la ligne à ajouter
    $ligne = sprintf(
        "%s,%s,%s,%s,%s,%s,%s\n",
        $date,
        $ip,
        $dept_code,
        str_replace(',', ' ', $ville_nom), // Éviter les problèmes avec les virgules
        str_replace(',', ' ', $region_nom),
        $lat,
        $lon
    );
    
    // Ajouter la ligne au fichier
    return (file_put_contents($fichier_csv, $ligne, FILE_APPEND) !== false);
}

/**
 * Récupère les données météo actuelles pour une localisation
 * 
 * Utilise l'API OpenWeather pour récupérer les conditions météo actuelles
 * à partir des coordonnées géographiques.
 * 
 * @param float $lat Latitude
 * @param float $lon Longitude
 * @return array|null Données météo ou null en cas d'erreur
 */
function getMeteoActuelle($lat, $lon) {
    $api_key = "a49709c6b79268353f0d29c8c751dfaf";
    $url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$api_key}&units=metric&lang=fr";
    
    $response = callAPI($url);
    
    if ($response['error'] || empty($response['data'])) {
        return null;
    }
    
    return $response['data'];
}

/**
 * Récupère les prévisions météo pour une localisation
 * 
 * Utilise l'API OpenWeather pour récupérer les prévisions météo
 * à partir des coordonnées géographiques.
 * 
 * @param float $lat Latitude
 * @param float $lon Longitude
 * @return array|null Données de prévisions ou null en cas d'erreur
 */
function getPrevisions($lat, $lon) {
    $api_key = "a49709c6b79268353f0d29c8c751dfaf";
    $url = "https://api.openweathermap.org/data/2.5/forecast?lat={$lat}&lon={$lon}&appid={$api_key}&units=metric&lang=fr";
    
    $response = callAPI($url);
    
    if ($response['error'] || empty($response['data'])) {
        return null;
    }
    
    return $response['data'];
}

/**
 * Récupère les données météo journalières pour une localisation
 * 
 * Utilise l'API OpenWeather pour récupérer les prévisions météo journalières
 * à partir des coordonnées géographiques.
 * 
 * @param float $lat Latitude
 * @param float $lon Longitude
 * @return array|null Données de prévisions journalières ou null en cas d'erreur
 */
function getMeteoDailyForecast($lat, $lon) {
    $api_key = "a49709c6b79268353f0d29c8c751dfaf";
    $url = "https://api.openweathermap.org/data/2.5/onecall?lat={$lat}&lon={$lon}&exclude=minutely,hourly,alerts&appid={$api_key}&units=metric&lang=fr";
    
    $response = callAPI($url);
    
    if ($response['error'] || empty($response['data'])) {
        return null;
    }
    
    return $response['data'];
}

/**
 * Enregistre les informations de la dernière ville consultée dans un cookie
 * 
 * @param array $ville_info Tableau associatif contenant les informations de la ville
 * @return bool Succès ou échec de l'enregistrement du cookie
 */
function sauvegarderDerniereVille($ville_info) {
    if (empty($ville_info) || !isset($ville_info['nom'])) {
        return false;
    }
    
    // Ajouter la date et heure de consultation
    if (!isset($ville_info['date'])) {
        $ville_info['date'] = date('Y-m-d H:i:s');
    }
    
    // Convertir en JSON
    $cookie_value = json_encode($ville_info);
    
    // Créer un fichier de log pour le débogage
    $log_file = dirname(__DIR__) . '/cookie_debug.log';
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Tentative de création du cookie: " . $cookie_value . "\n", FILE_APPEND);
    
    // Enregistrer le cookie de manière simple et compatible avec toutes les versions de PHP
    // Durée de vie: 30 jours
    $result = setcookie('derniere_ville', $cookie_value, time() + 30 * 24 * 3600, '/');
    
    // Enregistrer le résultat dans le fichier de log
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Résultat: " . ($result ? "Succès" : "Échec") . "\n", FILE_APPEND);
    
    return $result;
}

/**
 * Récupère les informations de la dernière ville consultée depuis le cookie
 * 
 * @return array|null Informations de la dernière ville ou null si aucune
 */
function getDerniereVille() {
    // Créer un fichier de log pour le débogage
    $log_file = dirname(__DIR__) . '/cookie_debug.log';
    
    if (!isset($_COOKIE['derniere_ville'])) {
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Cookie 'derniere_ville' non trouvé\n", FILE_APPEND);
        return null;
    }
    
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Cookie trouvé: " . $_COOKIE['derniere_ville'] . "\n", FILE_APPEND);
    
    $ville_info = json_decode($_COOKIE['derniere_ville'], true);
    
    // Vérifier que les données sont valides
    if (!is_array($ville_info) || !isset($ville_info['nom'])) {
        file_put_contents($log_file, date('Y-m-d H:i:s') . " - Données du cookie invalides\n", FILE_APPEND);
        return null;
    }
    
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - Cookie valide, ville: " . $ville_info['nom'] . "\n", FILE_APPEND);
    
    return $ville_info;
}

/**
 * Formate la date en français
 * 
 * @param string $date Date au format Y-m-d ou timestamp
 * @param bool $avec_heure Inclure l'heure dans le résultat
 * @return string Date formatée en français
 */
function formatDateFr($date, $avec_heure = false) {
    if (is_numeric($date)) {
        $timestamp = $date;
    } else {
        $timestamp = strtotime($date);
    }
    
    $jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    $mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    
    $jour_semaine = $jours[date('w', $timestamp)];
    $jour = date('j', $timestamp);
    $mois_nom = $mois[date('n', $timestamp) - 1];
    $annee = date('Y', $timestamp);
    
    $resultat = "$jour_semaine $jour $mois_nom $annee";
    
    if ($avec_heure) {
        $heure = date('H:i', $timestamp);
        $resultat .= " à $heure";
    }
    
    return $resultat;
}




?>
