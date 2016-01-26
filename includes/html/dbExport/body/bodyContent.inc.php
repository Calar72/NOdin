<?php
// Debug - Dateinamen ausgeben?!
$hCore->debugInitOnLoad('File',__FILE__);



// Info - Navigationsleiste
include 'bodyInfo.inc.php';

$preForm = $_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT'];
$postForm = $hCore->gCore['getGET']['callAction'] . '/' . $hCore->gCore['getGET']['subAction'] . '/' . $hCore->gCore['getGET']['valueAction'];

$formAction = $preForm . $postForm;
?>




<form action="<?php print ($formAction); ?>" method="post" enctype="multipart/form-data">

<br>

    <table border=0 class="standard" style="width:100%">
        <tr>
            <th colspan="8">
                Dateiauswahl
            </th>
        </tr>

        <tr>
            <td class="bottomLine">Auswahl</td>
            <td class="bottomLine">File ID</td>
            <td class="bottomLine">Dateiname</td>
            <td class="bottomLine">Upload Datum</td>
            <td class="bottomLine">Größe</td>
            <td class="bottomLine">Upload Benutzer</td>
            <td class="bottomLine">&sum; Imports</td>
            <td class="bottomLine">Download</td>
        </tr>

        <?php

        if (isset($hCore->gCore['DBImportFiles'])){

            foreach ($hCore->gCore['DBImportFiles'] as $cntIndex=>$value){
                print ('<tr>');
                print ("<td class=\"bottomLine\"><input type=\"radio\" name=\"sel_fileUploadID\" value=\"".$value['fileUploadID']."\"></td>");
                print ("<td class=\"bottomLine\">".$value['fileUploadID']."</td>");
                print ("<td class=\"bottomLine\">".$value['fileOriginName']."</td>");
                print ("<td class=\"bottomLine\">".$value['uploadDateTime']."</td>");
                print ("<td class=\"bottomLine\">".$value['fileSize']."</td>");
                print ("<td class=\"bottomLine\">".$value['userName']."</td>");
                print ("<td class=\"bottomLine\">".$value['importCounter']."</td>");
                print ("<td class=\"bottomLine\"><a class=\"std\" href=\"".$_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT'].$value['downloadLink']."\">Download</a></td>");
                print ('</tr>');
            }

        }


        ?>


        <tr>
            <td colspan="8" class="standardButtonTD">
                <button type="reset" class="choice" id="reset">Reset</button>
                <button type="submit" class="choice" id="send">Senden</button>
            </td>
        </tr>
    </table>

</form>



