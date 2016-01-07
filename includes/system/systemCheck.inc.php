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
require_once 'includes/configs/systemConfig.inc.php';


// PHP Version ausreichend?
if (version_compare(phpversion(), $_SESSION['systemConfig']['requirePHPVersion'], '<')) {
    die ('<hr>FEHLER bei der Systemprüfung:<br>- PHP Version auf verwendetem System unzureichend! (Version: PHP '.$_SESSION['systemConfig']['requirePHPVersion'].' oder höher erwartet.)<br><hr>');
}