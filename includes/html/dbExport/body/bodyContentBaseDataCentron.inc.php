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
            <th colspan="6">
                Export Bestätigung
            </th>
        </tr>

        <tr>
            <td class="bottomLine">&sum; Datensätze</td>
            <td class="bottomLine">Aktuellster Datensatz</td>
            <td class="bottomLine">Ältester Datensatz</td>
            <td class="bottomLine">Benutzer (Liste)</td>
            <td class="bottomLine">Sammelkonten (Liste)</td>
            <td class="bottomLine">Zahlungsarten (Liste)</td>
        </tr>

        <tr>
            <td class="bottomLine" valign="top"><?php print ($hCore->gCore['baseDataInfo']['getSumBaseData']); ?></td>
            <td class="bottomLine" valign="top"><?php print ($hCore->gCore['baseDataInfo']['getNewestBaseData']); ?></td>
            <td class="bottomLine" valign="top"><?php print ($hCore->gCore['baseDataInfo']['getOldestBaseData']); ?></td>
            <td class="bottomLine" valign="top"><?php foreach ($hCore->gCore['baseDataInfo']['userNames'] as $name){ print ($name . '<br>'); } ?></td>
            <td class="bottomLine" valign="top"><?php foreach ($hCore->gCore['baseDataInfo']['Sammelkonten'] as $Sammelkonto){ print ($Sammelkonto . '<br>'); } ?></td>
            <td class="bottomLine" valign="top"><?php foreach ($hCore->gCore['baseDataInfo']['Zahlungsarten'] as $Zahlungsart){ print ($Zahlungsart . '<br>'); } ?></td>
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



