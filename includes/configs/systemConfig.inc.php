<?php
/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 14:31
 */


// Debug Modus?
$_SESSION['systemConfig']['Debug']['enableDebug']   = 'yes';    // Enable - Disble jegliche Debug - Aktivität
$_SESSION['systemConfig']['Debug']['ShowOnScreen']  = 'yes';    // Debug - Meldungen auf dem Bildschirm ausgeben? (yes/no)
$_SESSION['systemConfig']['Debug']['ShowGET']       = 'yes';    // $_GET - Variable ausgeben? (yes/no)
$_SESSION['systemConfig']['Debug']['ShowPOST']      = 'yes';    // $_POST - Variable ausgeben? (yes/no)
$_SESSION['systemConfig']['Debug']['ShowSession']   = 'yes';    // $_SESSION - Variable ausgeben? (yes/no)
$_SESSION['systemConfig']['Debug']['ShowGLOBALS']   = 'no';     // $GLOBALS - Variable ausgeben? (yes/no)
$_SESSION['systemConfig']['Debug']['PHPErrors']     = 'all';    // PHP Fehler ausgeben? (PHP error_reporting)
$_SESSION['systemConfig']['Debug']['ShowFilename']  = 'no';     // Jeweiligen Dateinamen ausgeben? (yes/no)
$_SESSION['systemConfig']['Debug']['ShowClassname'] = 'no';     // Jeweiligen Klassennamen ausgeben? (yes/no)





// Benötigte PHP - Version (mindestens oder höher)?
$_SESSION['systemConfig']['Requirement']['requirePHPVersion'] = '5.3.0';




