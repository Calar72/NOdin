<?php
// Debug - Dateinamen ausgeben?!
$hCore->debugInitOnLoad('File',__FILE__);
?>

<table border=1 class="standard" style="width:400px">
	<tr>
		<td>Debug Frame wenn on</td>
	</tr>
</table>

<?php
$hCore->detaileout('$gCore',$hCore->gCore); // gCore - Variable ausgeben
$hCore->debugInitDebugVarOutput();
?>