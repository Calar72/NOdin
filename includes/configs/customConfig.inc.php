<?php
/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 07.01.2016
 * Time: 10:02
 */


// Weblinks
// Full external link
$_SESSION['customConfig']['WebLinks']['EXTHOME'] = 'http://192.168.6.11/NOdin/index.php';

// External (short) link
$_SESSION['customConfig']['WebLinks']['EXTHOMESHORT'] = 'http://192.168.6.11/NOdin/';

// Upload - Verzeichnis
$_SESSION['customConfig']['WebLinks']['MAINUPLOADPATH'] = '/var/www/html/NOdin/uploads/';





// Title Header
$_SESSION['customConfig']['Titles']['Website'] = 'Odin Konverter (Development)';





// Datenbank Settings
$_SESSION['customConfig']['DBSettings']['DBHOST'] 		= 'localhost';
$_SESSION['customConfig']['DBSettings']['DBNAME'] 		= 'Odin';
$_SESSION['customConfig']['DBSettings']['DBUSER'] 		= 'root';
$_SESSION['customConfig']['DBSettings']['DBPASSWORD'] 	= 'OdinDev';





// Login - Vorraussetzungen
$_SESSION['customConfig']['Login']['MinLenUsername'] 	= '3';
$_SESSION['customConfig']['Login']['MaxLenUsername'] 	= '30';
$_SESSION['customConfig']['Login']['MinLenPassword'] 	= '3';
$_SESSION['customConfig']['Login']['MaxLenPassword'] 	= '30';
