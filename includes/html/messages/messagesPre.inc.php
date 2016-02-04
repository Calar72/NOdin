<?php
/**
 * Copyright (c) 2016 by Markus Melching (TKRZ)
 */

/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 13.01.2016
 * Time: 13:05
 */



    $myDisplayValue = 'block';
    $tmpClassDebugValue = 'fa fa-check-square-o';

?>





<?php
// Div-Tag: Message Ausgabe "wieder" einblenden
?>
<div style="display: block" id="divDebugOptionShowMessages" class="divDebugOptionShow divDebugOptionShowMessages">
    &nbsp;&nbsp;<a class="std" href="" onclick="javascript:show('divMessageValue'); return false"><i class="fa fa-info"></i>&nbsp;&nbsp;Message Value</a>&nbsp;&nbsp;|
</div>






<?php
// Div-Tag: Ausgabe Debug Value
?>
<div style="display: <?php print ($myDisplayValue); ?>" id="divMessageValue" class="divMessagesValueOuter">
    <table border="0" width="100%" class="messagesHeadlineInformation">
        <tr>
            <td class="messagesHeadlineInformation">&nbsp;Message Value</td>
            <td align="right"><a class="std" href="" onclick="javascript:show('divMessageValue'); return false">&nbsp;<i class="fa fa-times"></i>&nbsp;&nbsp;</a></td>
        </tr>
        <tr>
            <td colspan="2">
                <div style="display: block" id="divMessageValue" class="divMessagesValueInner">
                    <code>
