<?php
/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 25.01.2016
 * Time: 12:18
 */

// Debug - Dateinamen ausgeben?!
$hCore->debugInitOnLoad('File',__FILE__);



// Lade den Body Content (Navigation | Content)
?>




<?php
// Deklariere Div-Tag Navigation
?>
<div style="display: block" id="divLeftNavigationOuter" class="divLeftNavigationOuter">
    <?php include 'includes/html/home/body/bodyLeftNavigation.inc.php'; ?>
</div>




<?php
// Deklariere Div-Tag Content
?>
<div style="display: block" id="divContentOuter" class="divContentOuter">
    <?php include 'includes/html/fileUpload/body/bodyContent.inc.php'; ?>
</div>