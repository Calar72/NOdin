<?php
/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 14:31
 */



// Develop Modus?
$_SESSION['systemConfig']['Develop']['enableDevelop'] = 'yes';          // Enable - Disble den Develop-Modus (yes/no)               (Default = no)



// Debug Modus?
$_SESSION['systemConfig']['Debug']['enableDebug']   = 'yes';            // Enable - Disble jegliche Debug - Aktivität (yes/no)      (Default = no)
$_SESSION['systemConfig']['Debug']['ShowOnScreen']  = 'yes';            // Debug - Meldungen auf dem Bildschirm ausgeben? (yes/no)  (Default = yes)
$_SESSION['systemConfig']['Debug']['ShowGET']       = 'yes';            // $_GET - Variable ausgeben? (yes/no)                      (Default = no)
$_SESSION['systemConfig']['Debug']['ShowPOST']      = 'yes';            // $_POST - Variable ausgeben? (yes/no)                     (Default = no)
$_SESSION['systemConfig']['Debug']['ShowSession']   = 'yes';            // $_SESSION - Variable ausgeben? (yes/no)                  (Default = no)
$_SESSION['systemConfig']['Debug']['ShowGLOBALS']   = 'no';             // $GLOBALS - Variable ausgeben? (yes/no)                   (Default = no)
$_SESSION['systemConfig']['Debug']['PHPErrors']     = 'all';            // PHP Fehler ausgeben? (PHP error_reporting)               (Default = all)
$_SESSION['systemConfig']['Debug']['ShowFilename']  = 'no';             // Jeweiligen Dateinamen ausgeben? (yes/no)                 (Default = no)
$_SESSION['systemConfig']['Debug']['ShowClassname'] = 'no';             // Jeweiligen Klassennamen ausgeben? (yes/no)               (Default = no)
$_SESSION['systemConfig']['Debug']['DieOnSystemCheckWarning'] = 'no';  // Ausgabe stoppen wenn ein "Warning" bei der System-Prüfung festgestellt wurde? (yes/no) (Default = no)



// Benötigte PHP - Version (mindestens oder höher)?
$_SESSION['systemConfig']['Requirement']['requirePHPVersion'] = '5.3.0';



// Datenbank soll permanent - Verbunding genutzt werden? (connect/pconnect) (Default = pconnect)
$_SESSION['systemConfig']['Setting']['DBConnectionType'] = 'pconnect';

