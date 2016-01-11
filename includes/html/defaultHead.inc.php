<!DOCTYPE html>
<html>
<head>
<meta charset="<?php print($_SESSION['customConfig']['TextCharset']['Website']); ?>">
<title><?php print ($_SESSION['customConfig']['Titles']['Website']); ?></title>


<link rel="stylesheet" type="text/css" href="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>includes/css/defaultCSS.css" />
<link rel="stylesheet" type="text/css" href="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>includes/css/sizeCSS.css" />
<link rel="stylesheet" type="text/css" href="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>includes/css/buttonCSS.css" />
<link rel="stylesheet" href="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>includes/css/font-awesome-4.5.0/css/font-awesome.min.css" />


<script type="text/javascript" src="<?php print ($_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT']); ?>includes/javascript/defaultJavaScript.js"></script>


</head>


<body>
<?php
// Debug - Dateinamen ausgeben?!
$hCore->debugInitOnLoad('File',__FILE__);
?>
