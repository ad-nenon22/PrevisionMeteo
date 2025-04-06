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
        $logo_image = "./images/oo.png"; // Logo pour le mode sombre
    }
    else if($_COOKIE['style']=="styles.css") {
        $style = "styles.css";
        $other_style = "dark.css";
        $sourceimage = "./images/dark.png";
        $logo_image = "./images/oo.png"; // Logo pour le mode clair pareil
    }
}
else {
    $style = "styles.css";
    $other_style = "dark.css";
    $sourceimage = "./images/dark.png";
    $logo_image = "./images/oo.png"; // Logo par défaut (mode cmlair)
}
?>


<meta charset="UTF-8"/>
<meta name="author" content="Admira"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="description" content="<?php echo $page_des ?? 'Développement Web'; ?>"/>

<title><?php echo $title ?? 'Skyview Météo'; ?></title>
<link rel="stylesheet" href="<?php echo $style; ?>"/>
<link rel="icon" href="images/fav.ico" type="image/x-icon"/>

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
            <li><a href="index.php">Accueil</a></li>
            <li><a href="meteo.php">Météo</a></li>
            <li><a href="stat.php">Statistique</a></li>
            <li><a href="tech.php">Tech</a></li>



        </ul>
    </nav>

</header>
