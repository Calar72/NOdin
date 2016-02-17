<?php
/**
 * Copyright (c) 2016 by Markus Melching (TKRZ)
 */

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
            <th colspan="6">
                Export Bestätigung
            </th>
        </tr>

        <tr>
            <td class="bottomLine">&sum; Datensätze</td>
            <td class="bottomLine">Aktuellster Datensatz</td>
            <td class="bottomLine">Ältester Datensatz</td>
            <td class="bottomLine">Benutzer (Liste)</td>
            <td class="bottomLine">Erlöskonten (Liste)</td>
            <td class="bottomLine">Kostenstellen (Liste)</td>
        </tr>

        <tr>
            <td class="bottomLine" valign="top"><?php print ($hCore->gCore['bookingDataInfo']['getSumBookingData']); ?></td>
            <td class="bottomLine" valign="top"><?php print ($hCore->gCore['bookingDataInfo']['getNewestBookingData']); ?></td>
            <td class="bottomLine" valign="top"><?php print ($hCore->gCore['bookingDataInfo']['getOldestBookingData']); ?></td>
            <td class="bottomLine" valign="top"><?php foreach ($hCore->gCore['bookingDataInfo']['userNames'] as $name){ print ($name . '<br>'); } ?></td>
            <td class="bottomLine" valign="top"><?php foreach ($hCore->gCore['bookingDataInfo']['Erloeskonten'] as $Erloskonto){ print ($Erloskonto . '<br>'); } ?></td>
            <td class="bottomLine" valign="top"><?php foreach ($hCore->gCore['bookingDataInfo']['Kostenstellen'] as $Kostenstelle){ print ($Kostenstelle . '<br>'); } ?></td>
        </tr>

        <tr>
            <td colspan="8" class="standardButtonTD">
                <button type="reset" class="choice" id="reset">Reset</button>
                <button type="submit" class="choice" id="send">Senden</button>
            </td>
        </tr>
    </table>

    <input type="hidden" name="getUserOK" value="yes">

</form>



