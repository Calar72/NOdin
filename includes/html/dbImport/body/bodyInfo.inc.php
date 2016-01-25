<?php
// Debug - Dateinamen ausgeben?!
$hCore->debugInitOnLoad('File',__FILE__);


$typeIndex = array_search($hCore->gCore['getGET']['subAction'], $hCore->gCore['LNav']['ConvertTypeID']);
$typeInfo = $hCore->gCore['LNav']['ConvertType'][$typeIndex];

$systemIndex = array_search($hCore->gCore['getGET']['valueAction'], $hCore->gCore['LNav']['ConvertSystemID']);
$systemInfo = $hCore->gCore['LNav']['ConvertSystem'][$systemIndex];
?>



<table border=0 class="standard" style="width:100%">
	<tr>
		<td>
			DB - Import <i class="fa fa-arrow-right"></i> <?php print ($typeInfo); ?> <i class="fa fa-arrow-right"></i> <?php print ($systemInfo); ?>
		</td>
	</tr>
</table>
