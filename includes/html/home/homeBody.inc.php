<?php
// Debug - Dateinamen ausgeben?!
$hCore->debugInitOnLoad('File',__FILE__);


// Lade den Body Content (Navigation | Content)
?>
<table border=0 class="standard" style="width:100%;">
	<tr>
		<td style="width:140px; padding-bottom:0px" valign="top"><?php include 'body/bodyLeftNavigation.inc.php'; ?></td>

		<td style="width:%; padding-bottom:0px" valign="top"><?php include 'body/bodyContent.inc.php'; ?></td>
	</tr>
</table>
