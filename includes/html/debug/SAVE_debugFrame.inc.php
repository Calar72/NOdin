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
    $tmpClassOptions = 'fa fa-check-square-o';
    $myStatus = 'ein';
}
else {
    $myDisplay = 'none';
    $tmpClassOptions = 'fa fa-square-o';
    $myStatus = 'aus';
}



// Default und gespeicherte Einstellung der Debug-Value ermitteln
if ( (isset($_SESSION['systemConfig']['Debug']['enableShowDebugValue'])) && ($_SESSION['systemConfig']['Debug']['enableShowDebugValue'] == 'yes') ){
    $myDisplayValue = 'block';
    $tmpClassDebugValue = 'fa fa-check-square-o';
}
else {
    $myDisplayValue = 'none';
    $tmpClassDebugValue = 'fa fa-square-o';
}



?>
<div style="display: <?php print ($myDisplay); ?>" id="debugOptions"class="debugOptionLeiste">

    <table border=1 class="standard" style="width:100%">
        <tr>
            <td class="debugOptions rightLine" style="width:160px">&nbsp;<a class="std" href="callDebugFrame"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<i class="<?php print ($tmpClassOptions); ?>"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class="std" href="" onclick="javascript:show('debugOptions'); return false">Debug Optionen</a></td>

            <td class="debugOptions rightLine" style="width:160px">&nbsp;<a class="std" href="callDebugValue"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<i class="<?php print ($tmpClassDebugValue); ?>"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class="std" href="" onclick="javascript:show('debugOutput'); return false">Debug Value</a></td>

            <td class="debugOptions rightLine" style="width:160px">&nbsp;<a class="std" href="callDebugLinks"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<i class="<?php print ($tmpClassDebugLinks); ?>"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class="std" href="" onclick="javascript:show('debugLinks'); return false">Debug Links</a></td>

            <td class="debugOptionsReverse" style="width: %"></td>
        </tr>
    </table>

    <table border=1 class="standard" style="width:100%">
        <tr>
            <td class="debugOptions rightLine" style="width:160px">&nbsp;td>

            <td class="debugOptions rightLine" style="width:160px">&nbsp;<a class="std" href="callDebugValue"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<i class="<?php print ($tmpClassDebugValue); ?>"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class="std" href="" onclick="javascript:show('debugOutput'); return false">Debug Value</a></td>

            <td class="debugOptions rightLine" style="width:160px">&nbsp;<a class="std" href="callDebugLinks"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<i class="<?php print ($tmpClassDebugLinks); ?>"></i></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a class="std" href="" onclick="javascript:show('debugLinks'); return false">Debug Links</a></td>

            <td class="debugOptionsReverse" style="width: %"></td>
        </tr>
    </table>


    <div style="display: <?php print ($myDisplayValue); ?>" id="debugOutput" class="showDebugValues">
        <table border=0 class="standard" style="width:100%;">
            <tr>
                <td class="showDebugValuesDetail">
                    <code>
                    <?php
                    $hCore->detaileout('$gCore',$hCore->gCore); // gCore - Variable ausgeben
                    $hCore->debugInitDebugVarOutput();
                    ?>
                    </code>
                </td>
            </tr>
        </table>
    </div>


    <div style="display: <?php print ($myDisplayValue); ?>" id="debugLinks" class="showDebugValues">
        <table border=0 class="standard" style="width:100%;">
            <tr>
                <td class="showDebugValuesDetail">
                    <code>
                        Paul<br>
                        Pauline
                    </code>
                </td>
            </tr>
        </table>
    </div>

</div>
