<?php
// Fichier pour récupérer les départements d'une région
header('Content-Type: application/json');

// Vérification du paramètre région
if (!isset($_GET['region']) || empty($_GET['region'])) {
    echo json_encode(['error' => 'Paramètre région manquant']);
    exit;
}

$region_code = $_GET['region'];

// Chargement des données des départements
$departements_json = file_get_contents('data/departements.json');
$departements_data = json_decode($departements_json, true);

// Vérification si la région existe
if (!isset($departements_data[$region_code])) {
    echo json_encode(['error' => 'Région inconnue']);
    exit;
}

// Retour des départements de la région
echo json_encode(['departements' => $departements_data[$region_code]]);
?>
