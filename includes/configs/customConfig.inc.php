<?php
/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 07.01.2016
 * Time: 10:02
 */


// Path & Links
// Link - Full external link
$_SESSION['customConfig']['WebLinks']['EXTHOME'] = 'http://192.168.6.11/NOdin/index.php';

// Link - External (short) link
$_SESSION['customConfig']['WebLinks']['EXTHOMESHORT'] = 'http://192.168.6.11/NOdin/';

// Path - Internal (short) link ... Notice: leading- and end / (slash) required
$_SESSION['customConfig']['WebLinks']['INTERNHOMESHORT'] = '/NOdin/';

// Path - Upload - Directory
$_SESSION['customConfig']['WebLinks']['MAINUPLOADPATH'] = '/var/www/html/NOdin/uploads/';



// Title Header
$_SESSION['customConfig']['Titles']['Website'] = 'Odin Konverter (Development)';

// Website Charset
$_SESSION['customConfig']['TextCharset']['Website'] = 'UTF-8';



// Database Settings
$_SESSION['customConfig']['DBSettings']['DBHOST'] 		= 'localhost';
$_SESSION['customConfig']['DBSettings']['DBNAME'] 		= 'Odin';
$_SESSION['customConfig']['DBSettings']['DBUSER'] 		= 'root';
$_SESSION['customConfig']['DBSettings']['DBPASSWORD'] 	= 'OdinDev';



// Login - Conditions
$_SESSION['customConfig']['Login']['MinLenUsername'] 	= '3';
$_SESSION['customConfig']['Login']['MaxLenUsername'] 	= '30';
$_SESSION['customConfig']['Login']['MinLenPassword'] 	= '3';
$_SESSION['customConfig']['Login']['MaxLenPassword'] 	= '30';
