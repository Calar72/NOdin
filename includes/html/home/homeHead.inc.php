<?php
// Standard head - Datei laden
//include 'includes/html/standard/standardHead.inc.php';



// Debug - Dateinamen ausgeben?!
$hCore->debugInitOnLoad('File',__FILE__);



// Lade die head - Leiste (Home - Bild | Message - Fenster | Benutzer Informationen)
?>
<table border=0 class="standard" style="width:100%;">
    <tr>
        <td style="width:240px; padding-bottom:0px"><?php include 'head/headLeftInfo.inc.php'; ?></td>

        <td style="width:60%; padding-bottom:0px"><?php include 'head/headCenterInfo.inc.php'; ?></td>

        <td style="width:300px; padding-bottom:0px"><?php include 'head/headRightInfo.inc.php'; ?></td>
    </tr>
</table>