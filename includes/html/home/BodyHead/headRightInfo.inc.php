<?php
// Debug - Dateinamen ausgeben?!
$hCore->debugInitOnLoad('File',__FILE__);
?>

<table border=0 class="headUserInfo" align="right" style="width:300px">
	<tr>
		<td align="right">

			<table class="textRight">
				<tr>
					<td class="bottomLine rPaddingSix">Benutzer:</td><td class="bottomLine"><?php print ($_SESSION['Login']['User']['userName']); ?></td>
				</tr>
				<tr>
					<td class="rPaddingSix">Status:</td><td><?php print ($_SESSION['Login']['User']['roleName']); ?></td>
				</tr>
				<tr>
					<td class="rPaddingSix">Login:</td><td><?php print ($_SESSION['Login']['User']['dateCurLogin']); ?></td>
				</tr>
				<tr>
					<td class="bottomLine rPaddingSix">Letzter Login:</td><td class="bottomLine"><?php print ($_SESSION['Login']['User']['dateLastLogin']); ?></td>
				</tr>
				<tr>
					<td colspan="2"><a class="std" href="callLogout"><i class="fa fa-power-off"></i>&nbsp;Logout&nbsp;</a></td>
				</tr>
			</table>

		</td>
	</tr>
</table>
