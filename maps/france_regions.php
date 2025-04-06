<?php
// Ce fichier génère une image de la carte des régions de France
// avec des coordonnées pour les balises <area>

// Définition des dimensions de l'image
$width = 600;
$height = 600;

// Création d'une image vide
$image = imagecreatetruecolor($width, $height);

// Définition des couleurs
$white = imagecolorallocate($image, 255, 255, 255);
$black = imagecolorallocate($image, 0, 0, 0);
$blue = imagecolorallocate($image, 100, 149, 237);
$gray = imagecolorallocate($image, 220, 220, 220);

// Remplissage du fond en blanc
imagefill($image, 0, 0, $white);

// Chargement des données des régions
$regions_json = file_get_contents(__DIR__ . '/../../data/regions.json');
$regions_data = json_decode($regions_json, true);

// Dessiner les régions (simplifiées pour cet exemple)
foreach ($regions_data['regions'] as $region) {
    // Extraction des coordonnées
    $coords = explode(',', $region['coords']);
    $x1 = intval($coords[0]);
    $y1 = intval($coords[1]);
    $x2 = intval($coords[2]);
    $y2 = intval($coords[3]);
    
    // Dessin d'un rectangle pour représenter la région
    imagefilledrectangle($image, $x1, $y1, $x2, $y2, $gray);
    imagerectangle($image, $x1, $y1, $x2, $y2, $black);
    
    // Ajout du nom de la région
    $center_x = $region['center'][0];
    $center_y = $region['center'][1];
    imagestring($image, 2, $center_x - 20, $center_y - 5, $region['nom'], $black);
}

// Ajout d'un titre
imagestring($image, 5, 180, 30, "Carte des regions de France", $black);

// Envoi de l'image au navigateur
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
?>

