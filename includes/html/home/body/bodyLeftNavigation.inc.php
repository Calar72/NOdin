<?php
// Debug - Dateinamen ausgeben?!
$hCore->debugInitOnLoad('File',__FILE__);


?>
<nav id="primary_nav_wrap">
	<ul>

		<li><a href="#"><i class="fa fa-upload"></i>&nbsp;&nbsp;Datei - Upload</a>
			<ul>
				<?php

				// Konvertierungs Typ (Stammdaten , Buchungssatz usw)
				foreach ($hCore->gCore['LNav']['ConvertType'] as $typeKey=>$souceTypeName) {

					print ('<li><a href="#">'.$souceTypeName.'</a><ul>');

						// Konvertierungs System (Dimari, Centron usw.)
						foreach ($hCore->gCore['LNav']['ConvertSystem'] as $systemKey=>$sourceSystemName){

							$sourceTypeID 	= $hCore->gCore['LNav']['ConvertTypeID'][$typeKey];
							$sourceSystemID = $hCore->gCore['LNav']['ConvertSystemID'][$systemKey];

							print ('<li><a href="'.$_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT'].'fileUpload/'.$sourceTypeID.'/'.$sourceSystemID.'">'.$sourceSystemName.'</a></li>');
						}

					print ('</ul></li>');
				}

				?>
			</ul>
		</li>



		<li><a href="#"><i class="fa fa-database"></i>&nbsp;&nbsp;DB - Import</a>
			<ul>
				<?php

				// Konvertierungs Typ (Stammdaten , Buchungssatz usw)
				foreach ($hCore->gCore['LNav']['ConvertType'] as $typeKey=>$souceTypeName) {

					print ('<li><a href="#">'.$souceTypeName.'</a><ul>');

					// Konvertierungs System (Dimari, Centron usw.)
					foreach ($hCore->gCore['LNav']['ConvertSystem'] as $systemKey=>$sourceSystemName){

						$sourceTypeID 	= $hCore->gCore['LNav']['ConvertTypeID'][$typeKey];
						$sourceSystemID = $hCore->gCore['LNav']['ConvertSystemID'][$systemKey];

						print ('<li><a href="'.$_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT'].'dbImport/'.$sourceTypeID.'/'.$sourceSystemID.'">'.$sourceSystemName.'</a></li>');
					}

					print ('</ul></li>');
				}

				?>
			</ul>
		</li>



		<li><a href="#"><i class="fa fa-database"></i>&nbsp;&nbsp;DB - Export</a>
			<ul>
				<?php

				// Konvertierungs Typ (Stammdaten , Buchungssatz usw)
				foreach ($hCore->gCore['LNav']['ConvertType'] as $typeKey=>$souceTypeName) {

					print ('<li><a href="#">'.$souceTypeName.'</a><ul>');

					// Konvertierungs System (Dimari, Centron usw.)
					foreach ($hCore->gCore['LNav']['ConvertSystem'] as $systemKey=>$sourceSystemName){

						$sourceTypeID 	= $hCore->gCore['LNav']['ConvertTypeID'][$typeKey];
						$sourceSystemID = $hCore->gCore['LNav']['ConvertSystemID'][$systemKey];

						print ('<li><a href="'.$_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT'].'dbExport/'.$sourceTypeID.'/'.$sourceSystemID.'">'.$sourceSystemName.'</a></li>');
					}

					print ('</ul></li>');
				}

				?>
			</ul>
		</li>

	</ul>
</nav>