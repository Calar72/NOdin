<!DOCTYPE html>
<html>
<head>
<meta charset="<?php print($_SESSION['customConfig']['TextCharset']['Website']); ?>">
<title><?php print ($_SESSION['customConfig']['Titles']['Website']); ?></title>


<link rel="stylesheet" type="text/css" href="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>includes/css/defaultCSS.css" />
<link rel="stylesheet" type="text/css" href="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>includes/css/sizeCSS.css" />
<link rel="stylesheet" type="text/css" href="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>includes/css/divCSS.css" />
<link rel="stylesheet" type="text/css" href="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>includes/css/buttonCSS.css" />
<link rel="stylesheet" href="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>includes/css/font-awesome-4.5.0/css/font-awesome.min.css" />


<script type="text/javascript" src="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>includes/javascript/defaultJavaScript.js"></script>


</head>


<body>
<?php
// Debug - Dateinamen ausgeben?!
$hCore->debugInitOnLoad('File',__FILE__);


// Lade die head - Leiste (Home - Bild | Message - Fenster | Benutzer Informationen)
?>
<table border=0 class="standard" style="width:100%;">
    <tr>
        <td style="width:240px; padding-bottom:0px"><?php include 'head/headLeftInfo.inc.php'; ?></td>

        <td style="width:60%; padding-bottom:0px"><?php include 'head/headCenterInfo.inc.php'; ?></td>

        <td style="width:300px; padding-bottom:0px"><?php include 'head/headRightInfo.inc.php'; ?></td>
    </tr>
</table>