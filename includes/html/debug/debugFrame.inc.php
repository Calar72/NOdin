<?php
/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 13.01.2016
 * Time: 13:05
 */

// Default und gespeicherte Einstellung der Debug-Leiste ermitteln
if ( (isset($_SESSION['systemConfig']['Debug']['enableDebugFrame'])) && ($_SESSION['systemConfig']['Debug']['enableDebugFrame'] == 'yes') ){
    $myDisplay = 'block';
    $tmpClass = 'fa fa-check-square-o';
}
else {
    $myDisplay = 'none';
    $tmpClass = 'fa fa-square-o';
}

// Default und gespeicherte Einstellung der Debug-Value ermitteln
if ( (isset($_SESSION['systemConfig']['Debug']['enableShowDebugValue'])) && ($_SESSION['systemConfig']['Debug']['enableShowDebugValue'] == 'yes') ){
    $myDisplayValue = 'block';
    $tmpClass = 'fa fa-check-square-o';
}
else {
    $myDisplayValue = 'none';
    $tmpClass = 'fa fa-square-o';
}


?>
<div style="display: <?php print ($myDisplay); ?>" id="debugOptions" class="debugOptionLeiste">
    <table border=1 class="standard" style="width:100%">
        <tr>
            <td class="debugOptions rightLine" style="width:160px">&nbsp;<a class="std" href="callDebugFrame"><i class="fa fa-floppy-o"></i>&nbsp;Debug Optionen (on/off)&nbsp;</a></td>

            <td class="debugOptions rightLine" style="width:160px">&nbsp;<a class="std" href="callDebugValue"><i class="fa fa-floppy-o"></i>&nbsp;Debug Value (on/off)&nbsp;</a></td>

            <td class="debugOptions rightLine"><a class="std" href="" onclick="javascript:show('debugOutput'); return false"><i class="'.$tmpClass.'"></i>&nbsp;Show Debug Informationen&nbsp;</a></td>
        </tr>
    </table>

    <div style="display: <?php print ($myDisplayValue); ?>" id="debugOutput" class="showDebugValues">
        <table border=0 class="standard" style="width:100%;">
            <tr>
                <td class="showDebugValuesDetail">
                    <?php
                    $hCore->detaileout('$gCore',$hCore->gCore); // gCore - Variable ausgeben
                    $hCore->debugInitDebugVarOutput();
                    ?>
                </td>
            </tr>
        </table>
    </div>

</div>

