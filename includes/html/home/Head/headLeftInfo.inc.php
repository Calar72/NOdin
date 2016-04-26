<?php
// Debug - Dateinamen ausgeben?!
$hCore->debugInitOnLoad('File',__FILE__);

// Logo anhand der Config ausgeben
$curLogo = 'logo_tkrz.png';

if (isset($_SESSION['gDefaultLogoLoad']))
	$curLogo = 'logo_' . $_SESSION['gDefaultLogoLoad'] . '.png';
else
	$curLogo = 'logo_tkrz.png';


?>

<table border=0 class="headInfo" style="width:230px">
	<tr>
		<td align="left">

			<table class="textLeft">
				<tr>
					<td class="rPaddingSix"><a href="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>home"><img src="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>includes/images/<?php print ($curLogo); ?>" width="213" alt="LOGO"></a></td>
				</tr>
			</table>

		</td>
	</tr>
</table>
