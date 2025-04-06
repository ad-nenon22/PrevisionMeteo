<?php
// Inclusion des fonctions
require_once '../include/functions.inc.php';

// Récupération des régions via l'API
$regions = getRegions();

// Définition des dimensions de l'image
$largeur = 800;
$hauteur = 600;

// Création d'une image vide
$image = imagecreatetruecolor($largeur, $hauteur);

// Définition des couleurs
$blanc = imagecolorallocate($image, 255, 255, 255);
$noir = imagecolorallocate($image, 0, 0, 0);
$bleu = imagecolorallocate($image, 100, 149, 237);
$gris_clair = imagecolorallocate($image, 240, 240, 240);
$gris = imagecolorallocate($image, 200, 200, 200);
$rouge = imagecolorallocate($image, 231, 76, 60);
$vert = imagecolorallocate($image, 46, 204, 113);
$jaune = imagecolorallocate($image, 241, 196, 15);
$orange = imagecolorallocate($image, 230, 126, 34);
$violet = imagecolorallocate($image, 155, 89, 182);
$turquoise = imagecolorallocate($image, 26, 188, 156);

// Remplissage du fond en blanc
imagefill($image, 0, 0, $blanc);

// Ajout d'un titre avec une police plus grande
$titre = "Carte des régions de France";
$police = 5; // Taille de police plus grande
imagestring($image, $police, ($largeur / 2) - 120, 20, $titre, $noir);

// Définition des coordonnées et styles pour chaque région
$coordonnees_regions = [
    "01" => ["top" => "45%", "left" => "60%", "width" => "12%", "height" => "15%"],
    "02" => ["top" => "20%", "left" => "55%", "width" => "15%", "height" => "20%"],
    "03" => ["top" => "30%", "left" => "70%", "width" => "15%", "height" => "15%"],
    "04" => ["top" => "65%", "left" => "65%", "width" => "15%", "height" => "15%"],
    "06" => ["top" => "70%", "left" => "50%", "width" => "15%", "height" => "15%"],
    "11" => ["top" => "55%", "left" => "35%", "width" => "15%", "height" => "15%"],
    "24" => ["top" => "45%", "left" => "25%", "width" => "15%", "height" => "15%"],
    "27" => ["top" => "35%", "left" => "15%", "width" => "15%", "height" => "15%"],
    "28" => ["top" => "25%", "left" => "35%", "width" => "15%", "height" => "15%"],
    "32" => ["top" => "15%", "left" => "15%", "width" => "15%", "height" => "15%"],
    "44" => ["top" => "15%", "left" => "40%", "width" => "15%", "height" => "15%"],
    "52" => ["top" => "55%", "left" => "15%", "width" => "15%", "height" => "15%"],
    "53" => ["top" => "65%", "left" => "30%", "width" => "15%", "height" => "15%"],
    "75" => ["top" => "5%", "left" => "55%", "width" => "15%", "height" => "15%"],
    "76" => ["top" => "5%", "left" => "25%", "width" => "15%", "height" => "15%"],
    "84" => ["top" => "55%", "left" => "50%", "width" => "15%", "height" => "15%"],
    "93" => ["top" => "35%", "left" => "40%", "width" => "15%", "height" => "15%"],
    "94" => ["top" => "25%", "left" => "70%", "width" => "15%", "height" => "15%"]
];

// Couleurs pour les régions
$couleurs_regions = [
    $bleu, $vert, $jaune, $orange, $violet, $turquoise,
    $rouge, $gris, $bleu, $vert, $jaune, $orange, $violet
];

// Dessiner les régions avec des rectangles plus grands
foreach ($regions as $index => $region) {
    // Utiliser les coordonnées par défaut si non définies
    $coords = isset($coordonnees_regions[$region['code']]) 
        ? $coordonnees_regions[$region['code']] 
        : ["top" => "50%", "left" => "50%", "width" => "15%", "height" => "15%"];
    
    // Conversion des pourcentages en pixels
    $x1 = intval(($largeur * floatval(str_replace("%", "", $coords['left'])) / 100));
    $y1 = intval(($hauteur * floatval(str_replace("%", "", $coords['top'])) / 100));
    $x2 = intval($x1 + ($largeur * floatval(str_replace("%", "", $coords['width'])) / 100));
    $y2 = intval($y1 + ($hauteur * floatval(str_replace("%", "", $coords['height'])) / 100));
    
    // Dessin d'un rectangle pour représenter la région
    $couleur = $couleurs_regions[$index % count($couleurs_regions)];
    imagefilledrectangle($image, $x1, $y1, $x2, $y2, $couleur);
    imagerectangle($image, $x1, $y1, $x2, $y2, $noir);
    
    // Ajout du nom de la région avec une police plus grande
    $centre_x = intval(($x1 + $x2) / 2);
    $centre_y = intval(($y1 + $y2) / 2);
    
    // Créer un fond pour le texte
    $nom_region = $region['nom'];
    $largeur_texte = strlen($nom_region) * 6;
    $hauteur_texte = 15;
    
    // Rectangle semi-transparent pour le texte
    imagefilledrectangle(
        $image, 
        $centre_x - ($largeur_texte / 2) - 5, 
        $centre_y - 10, 
        $centre_x + ($largeur_texte / 2) + 5, 
        $centre_y + 10, 
        $blanc
    );
    
    // Dessiner le texte
    imagestring($image, 3, $centre_x - ($largeur_texte / 2), $centre_y - 5, $nom_region, $noir);
    
    // Ajouter le code de la région
    imagestring($image, 2, $centre_x - 5, $centre_y + 10, $region['code'], $noir);
}

// Ajout d'une légende
imagestring($image, 3, 50, $hauteur - 30, "Cliquez sur une région pour la sélectionner", $noir);

// Envoi de l'image au navigateur
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
?>

<main>

     


<?php
    require "./include/footer.inc.php";
?>
