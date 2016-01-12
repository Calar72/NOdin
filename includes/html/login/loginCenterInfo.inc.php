<?php
// Debug - Dateinamen ausgeben?!
$hCore->debugInitOnLoad('File',__FILE__);

/*
// Beispiel:
$hCore->gCore['Messages']['Type'][]      = 'Info';
$hCore->gCore['Messages']['Code'][]      = 'Login';
$hCore->gCore['Messages']['Headline'][]  = 'Erfolgreicher Login!';
$hCore->gCore['Messages']['Message'][]   = 'Willkommen '.$_SESSION['Login']['User']['userName'].'!';
*/
?>

<?php
if (array_key_exists('Messages',$hCore->gCore)){

    ?>
    <table border=0 class="headUserInfo" align="center" style="width:40%">
        <tr>
            <td style="width:12px; font-size: 80%;">M<br>E<br>S<br>S<br>A<br>G<br>E</td>

            <td>
                <div class="headInfoMessage">

                    <?php


                    foreach ($hCore->gCore['Messages']['Type'] as $index=>$typeValue){

                        $codeValue      = $hCore->gCore['Messages']['Code'][$index];
                        $headlineValue  = $hCore->gCore['Messages']['Headline'][$index];
                        $messageValue   = $hCore->gCore['Messages']['Message'][$index];

                        ?>

                        <table border=0 class="headUserInfo" style="width:100%">
                            <tr>
                                <td style="width:80px" class="topLine bottomLine leftLine">Headline:</td>
                                <td colspan="6" class="topLine bottomLine rightLine"><?php print ($headlineValue); ?></td>
                            </tr>

                            <tr>
                                <td style="width:80px; background: #CCFCFF" class="bottomLine">&nbsp;</td>
                                <td style="width:%; background: #CCFCFF" class="bottomLine">&nbsp;</td>

                                <td style="width:40px" class="bottomLine leftLine">Typ:</td>
                                <td class="bottomLine rightLine" style="width:120px"><?php print ($typeValue); ?></td>


                                <td style="width:40px" class="bottomLine leftLine">Code:</td>
                                <td class="bottomLine rightLine" style="width:120px"><?php print ($codeValue); ?></td>
                            </tr>

                            <tr>
                                <td style="width:80px" valign="top" class="bottomLine leftLine">Message:</td>
                                <td colspan="6" valign="top" class="bottomLine rightLine"><?php print ($messageValue); ?>
                                </td>
                            </tr>
                        </table>
                        <br>

                        <?php
                    }
                    ?>


                </div>
            </td>

        </tr>
    </table>

    <?php
}
else {
    print ('<div class="headInfoMessageHidden"></div>');
}

?>


