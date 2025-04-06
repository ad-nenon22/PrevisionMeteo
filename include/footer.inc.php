<?php
declare(strict_types=1);
require_once 'util.inc.php'; 
?>

<footer>
    

    <div class="footer-content">
        <a href="index.php"> <img class="logop" src="images/logoo.jpg" alt="Logo du site"/> </a>   
        <span>Admira NENONENE & Dyhia Mokri - Université de Cergy 2025_ dev web L2 info</span>
    </div>

    
    <div>

     <span><?= $page_footer_info ?? 'SkyView'; ?></span>
     <a href="tech.php"> Page Tech</a>
     <a href="tech.php"> Plan du site</a>


 </div>
 <div>
    <span><?= get_navigateur(); ?></span>
    <span> <?php include("counter.php"); ?></span>

</div>

</footer>
</body>
</html>
