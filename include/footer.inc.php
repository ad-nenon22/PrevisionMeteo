<?php
declare(strict_types=1);
require_once 'util.inc.php'; 
?>

<footer>
    <div class="footer-content">
        <span>&copy; Météo News - Prévisions-2025 | <a href="tech.php">Tech</a> | <a href="plan.php">Plan du site</a> </span>
    </div>
   
 <div>
    <span><?= get_navigateur(); ?></span>
    <span> <?php include("counter.php"); ?></span>
</div>

</footer>
</body>
</html>

