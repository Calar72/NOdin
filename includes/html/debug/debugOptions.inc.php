<?php
/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 13.01.2016
 * Time: 13:05
 */



// Default und gespeicherte Einstellung der Debug-Leiste ermitteln
if ( (isset($_SESSION['systemConfig']['Debug']['enableDebugFrame'])) && ($_SESSION['systemConfig']['Debug']['enableDebugFrame'] == 'yes') ){
    $myDisplayOptions = 'block';
    $tmpClassOptions = 'fa fa-check-square-o';
}
else {
    $myDisplayOptions = 'none';
    $tmpClassOptions = 'fa fa-square-o';
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



// Default und gespeicherte Einstellung der Debug-Value ermitteln
if ( (isset($_SESSION['systemConfig']['Debug']['enableShowDebugLinks'])) && ($_SESSION['systemConfig']['Debug']['enableShowDebugLinks'] == 'yes') ){
    $myDisplayLink = 'block';
    $tmpClassDebugLinks = 'fa fa-check-square-o';
}
else {
    $myDisplayLink = 'none';
    $tmpClassDebugLinks = 'fa fa-square-o';
}
?>







<?php
// Div-Tag: Ausgabe Debug - Options - Leiste
?>
<div style="display: <?php print ($myDisplayOptions); ?>" id="divDebugOptions" class="divDebugOptionsOuter">

    <div style="display: block" id="divDebugOptionShowOptions" class="divDebugOptionShow divDebugOptionShowOptions">
        <a class="std" href="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>callDebug/debugViewChange/enableDebugFrame"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<i class="<?php print ($tmpClassOptions); ?>"></i></a>&nbsp;&nbsp;<a class="std" href="" onclick="javascript:show('divDebugOptions'); return false">Debug Optionen</a>&nbsp;|
    </div>


    <div style="display: block" id="divDebugOptionShowValue" class="divDebugOptionShow divDebugOptionShowValue">
        |&nbsp;&nbsp;<a class="std" href="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>callDebug/debugViewChange/enableShowDebugValue"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<i class="<?php print ($tmpClassDebugValue); ?>"></i></a>&nbsp;&nbsp;<a class="std" href="" onclick="javascript:show('divDebugValue'); return false">Debug Value</a>&nbsp;&nbsp;|
    </div>


    <div style="display: block" id="divDebugOptionShowLinks" class="divDebugOptionShow divDebugOptionShowLinks">
        |&nbsp;&nbsp;<a class="std" href="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>callDebug/debugViewChange/enableShowDebugLinks"><i class="fa fa-floppy-o"></i>&nbsp;&nbsp;<i class="<?php print ($tmpClassDebugLinks); ?>"></i></a>&nbsp;&nbsp;<a class="std" href="" onclick="javascript:show('divDebugLink'); return false">Debug Links</a></a>&nbsp;&nbsp;|
    </div>

</div>





<?php
// Div-Tag: Ausgabe Debug Value
?>
<div style="display: <?php print ($myDisplayValue); ?>" id="divDebugValue" class="divDebugValueOuter">
    <table border="0" class="debugHeadlineInformation">
        <tr>
            <td class="debugHeadlineInformation">&nbsp;Debug Value</td>
        </tr>
        <tr>
            <td>
                <div style="display: block" id="divDebugValue" class="divDebugValueInner">
                    <code>
                        <?php
                        $hCore->detaileout('$gCore',$hCore->gCore); // gCore - Variable ausgeben
                        $hCore->debugInitDebugVarOutput();
                        ?>
                    </code>
                </div>
            </td>
        </tr>
    </table>
</div>





<?php
// Div-Tag: Ausgabe Debug Links
?>
<div style="display: <?php print ($myDisplayLink); ?>" id="divDebugLink" class="divDebugLinkOuter">
    <table border="0" class="debugHeadlineInformation">
        <tr>
            <td class="debugHeadlineInformation">&nbsp;Debug Links</td>
        </tr>
        <tr>
            <td>
                <div style="display: block" id="divDebugValue" class="divDebugLinkInner">
                    &bull;  <a href=<?php print ($_SESSION['customConfig']['WebLinks']['PHPMYADMIN']); ?>" target="_blank" class="std">PHP MyAdmin</a>
                    <br><br>
                    &bull;  <a href="http://fortawesome.github.io/Font-Awesome/icons/" target="_blank" class="std">Awesome Font</a>
                    <br><br>
                    &bull;  <a href="http://php.net/manual/de/" target="_blank" class="std">PHP Manual</a>
                    <br><br>
                    &bull;  <a href="http://www.w3schools.com/css/default.asp" target="_blank" class="std">CSS3</a>
                </div>
            </td>
        </tr>
    </table>
</div>
