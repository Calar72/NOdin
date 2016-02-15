<?php
/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 14:27
 *
 * Prüfe System - Umgebung auf Requirements
 *
 */


// Require System-Config - Datei ... dort sind Basis und Default - Werte definiert
//require_once 'includes/configs/systemConfig.inc.php';
$hCore->loadSystemConfig();





// Require die Custom-Config - Datei ... dort sind die individuelle Werte definiert (z.B. DB - Verbindung, Webpfad, Uploadpfad, Farbeinstellungen usw.)
//require_once 'includes/configs/customConfig.inc.php';
$hCore->loadDefaultConfig();





// ERROR ... PHP Version ausreichend?
if (version_compare(phpversion(), $_SESSION['systemConfig']['Requirement']['requirePHPVersion'], '<')) {
    header('Content-Type: text/html; charset='.$_SESSION['customConfig']['TextCharset']['Website'].'');
    die ('<hr>FEHLER bei der Systemprüfung:<br>- PHP Version auf verwendetem System unzureichend! (Hinweis: PHP Version '.$_SESSION['systemConfig']['Requirement']['requirePHPVersion'].' oder höher erwartet.)<br><hr>');
}





// ERROR ... mod_rewrite Modul aktiviert?
if (!in_array('mod_rewrite', apache_get_modules())){
    header('Content-Type: text/html; charset='.$_SESSION['customConfig']['TextCharset']['Website'].'');
    die ('<hr>FEHLER bei der Systemprüfung:<br>- Apache fehlendes "mod_rewrite" Modul! (Hinweis: mod_rewrite aktivieren!)<br><hr>');
}





// WARNING ... Display error eingeschaltet? (sollte off sein)
if ( (isset($_SESSION['systemConfig']['Debug']['DieOnSystemCheckWarning'])) && ($_SESSION['systemConfig']['Debug']['DieOnSystemCheckWarning'] == 'yes') ){
    if (strtolower(ini_get('display_errors')) == 'on'){
        header('Content-Type: text/html; charset='.$_SESSION['customConfig']['TextCharset']['Website'].'');
        die ('<hr>WARNUNG bei der Systemprüfung:<br>- PHP Fehlerausgabe in php.ini ist aktiviert! (Hinweis: "display_errors=Off" erwartet.)<br><hr>');
    }
}

