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

<table border=0 class="standard" style="width:100%">
    <tr>
        <td>
            <input type="file" name="fileToUpload" id="fileToUpload">
        </td>
    </tr>
    <tr>
        <td colspan="2" class="standardButtonTD">
            <button type="reset" class="choice" id="reset">Reset</button>
            <button type="submit" class="choice" id="send">Senden</button>
        </td>
    </tr>
</table>

</form>



