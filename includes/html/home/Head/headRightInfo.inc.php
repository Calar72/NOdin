<?php
// Debug - Dateinamen ausgeben?!
$hCore->debugInitOnLoad('File',__FILE__);
?>

<table border=0 class="standard" align="right" style="width:300px">
	<tr>
		<td align="right">

			<table border=0 class="standard textRight">
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

				<?php
				// Entwickler eingeloggt?
				// Wenn ja, dann Debug on/off hier ermöglichen
				if ($_SESSION['Login']['User']['roleID'] == '1'){

					// Icon - Anzeige steuern
					if ($_SESSION['systemConfig']['Debug']['enableDebugFrame'] == 'yes')
						$tmpClass = 'fa fa-check-square-o';
					else
						$tmpClass = 'fa fa-square-o';

					// Zeile ausgeben
					print ('<tr><td class="rPaddingSix"><a class="std" href="" onclick="javascript:show(\'debugOptions\'); return false"><i class="'.$tmpClass.'"></i>&nbsp;Debug Optionen&nbsp;</a></td><td colspan="1"><a class="std" href="callLogout"><i class="fa fa-power-off"></i>&nbsp;Logout&nbsp;</a></td></tr>');
				}
				else {
					print ('<tr><td colspan="2"><a class="std" href="callLogout"><i class="fa fa-power-off"></i>&nbsp;Logout&nbsp;</a></td></tr>');
				}
				?>

			</table>

		</td>
	</tr>
</table>