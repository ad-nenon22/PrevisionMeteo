<!DOCTYPE html>
<html lang="fr">
<head>

  <?php
  $lang = "fr";
  if(isset($_GET["lang"])){
    $lang = $_GET["lang"];
}
if(isset($_GET['style'])) {
    $style = $_GET['style'];
    setcookie('style', $style, time() + 3600, '/');
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit(); 
}
if(isset($_COOKIE['style'])) {
    if($_COOKIE['style']=="dark.css") {
        $style = "dark.css";
        $other_style = "styles.css";
        $sourceimage = "./images/ligth.png";
        $logo_image = "./images/logo.png"; // Logo pour le mode sombre
    }
    else if($_COOKIE['style']=="styles.css") {
        $style = "styles.css";
        $other_style = "dark.css";
        $sourceimage = "./images/dark.png";
        $logo_image = "./images/logo.png"; // Logo pour le mode clair pareil
    }
}
else {
    $style = "styles.css";
    $other_style = "dark.css";
    $sourceimage = "./images/dark.png";
    $logo_image = "./images/logo.png"; // Logo par défaut (mode cmlair)
}
?>
<meta charset="UTF-8"/>
<meta name="author" content="Admira"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="description" content="<?php echo $page_des ?? 'Mété news'; ?>"/>

<title><?php echo $title ?? 'Skyview Météo'; ?></title>
<link rel="stylesheet" href="<?php echo $style; ?>"/>
<link rel="icon" href="images/fav.ico" type="image/x-icon"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


</head>

<body>
    <header class="header">

     <div class = "header-top">
        <a href="index.php" class="logo">
            <img src="<?php echo $logo_image; ?>" alt="Logo du site"/>
        </a>
        <h1 class="title"> Météo News </h1>
    </div>
    <nav class="menu">
        <figure id="viewmode">
            <a href="?style=<?php echo $other_style ?>"><img src="<?php echo $sourceimage ?>" alt="Change viewmode"/></a>
        </figure>

        <ul>
            <li><a href="index.php"><i class="fas fa-home" style= "color:white"></i> Accueil</a></li>
            <li><a href="meteo.php"><i class="fas fa-cloud-sun-rain" style= "color:white"></i> Prévision</a></li>
            <li><a href="stat.php"><i class="fas fa-chart-line" style="color: white"></i> Statistique</a></li>
            <li><a href="contact.php"><i class="fas fa-envelope" style= "color: white"></i> Contact</a></li>

        </ul>
    </nav>
</header>
