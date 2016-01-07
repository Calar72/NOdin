<?php
/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 13:48
 *
 * 001) Initial - Starte Session - Management
 * 002) PHP Error - Handling
 * 003) System - Check durchführen
 * 004) Core - Klassen implementieren
 *
 * 3.) GET / POST annehmen und weitergeben an...
 *
 * 4.) Action - Steuerung
 *
 * 5.) Dynamischer Include HTML - Head
 *
 * 6.) Dynamischer Include HTML - Body
 *
 * 7.) Dynamischer Include HTML - Footer
 */

// 001) Initial - Starte Session - Management
session_start();




// 002) PHP Error - Handling
// Muss hier manuell eingesetzt werden, weil alle weiteren Daein erst noch kommen
function indexErrorHandling()
{
    if ( (isset($_SESSION['systemConfig']['Debug']['enableDebug'])) && ($_SESSION['systemConfig']['Debug']['enableDebug'] == 'yes') ){
        if ( (isset($_SESSION['systemConfig']['Debug']['PHPErrors'])) && ($_SESSION['systemConfig']['Debug']['PHPErrors'] == 'all') ){
            error_reporting (E_ALL | E_STRICT);
        }
        else {
            error_reporting(E_ALL & ~E_NOTICE);
        }
        ini_set ('display_errors', 'On');
    }
    else {
        error_reporting(0);
        ini_set ('display_errors', 'Off');
    }
}
// PHP Error - Handling
indexErrorHandling();




// 003) System - Check durchführen
require_once 'includes/system/systemCheck.inc.php';

// PHP Error - Handling
//indexErrorHandling();




// 004) Core - Klassen implementieren
require 'includes/system/systemClassLoad.inc.php';

// PHP Error - Handling
//indexErrorHandling();




// Debug - Dateinamen ausgeben?!
$hCore->initDebugOnLoad('File',__FILE__);




// Lade die Main - Webseite ... in ihr wird das eigentliche Frame-Gebilde erzeugt
require 'includes/system/systemMain.inc.php';





// Debug - Variable augeben?!
$hCore->initDebugVarOutput();

