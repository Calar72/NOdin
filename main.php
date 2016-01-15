<?php
/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 13:48
 *
 * 0010) Initial - Starte Session - Management
 *
 * 0020) PHP Error - Handling
 *
 * 0030) System - Check durchführen
 *
 * 0040) Core - Klassen implementieren
 *
 * 0050) Debug - Dateinamen ausgeben?!
 *
 * 0060) Lade die systemMain - Webseite ... in ihr wird das eigentliche Frame-Gebilde erzeugt
 *          head und Body werden dort geladen
 *          Action wird dort verarbeitet bzw. includet
 *
 * 0070) Debug - Variable augeben?!
 *
 * 0080) Dynamischer Include HTML - Footer
 *          head und Body wurdn in der includes/system/systemMain.inc.php geladen!
 *
 */

// 0010) Initial - Starte Session - Management
session_start();




// 0020) PHP Error - Handling
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




// 0030) System - Check durchführen
require_once 'includes/system/systemCheck.inc.php';

// PHP Error - Handling
//indexErrorHandling();





// 0040) Core - Klassen implementieren
require 'includes/system/systemClassLoad.inc.php';

// PHP Error - Handling
//indexErrorHandling();





// 0050) Debug - Dateinamen ausgeben?!
$hCore->debugInitOnLoad('File',__FILE__);





// 0060) Lade die systemMain - Webseite ... in ihr wird das eigentliche Frame-Gebilde erzeugt
require 'includes/system/systemMain.inc.php';






// 0070) Debug - Variable augeben?!
//TODO JETZT IN DER systemMain.inc.php




print ('<div id="footer_container">');

    // 0080) Dynamischer Include HTML - Footer
    // Wird in der main.php geladen!
    // head und Body werden in der systemMain.inc.php geladen!
    // Grund: Eine formatierte Ausgabe der Debug und Zusatzinformationen ist sonst nicht möglich.
    // Erzeuge Footer - Klassen - Objekt (Dynamisch nach Default (s.o. systemMain.inc.php) und ggf. Änderungen durch die Action (s.o. systemMain.inc.php)
    $getLeadToFooterClass     =   $hCore->gCore['getLeadToFooterClass'];    // Aktuellen Wert aus gCore holen
    $getLeadToFooterSite      =   $hCore->gCore['getLeadToFooterSite'];     // Aktuellen Wert aus gCore holen
    $getLeadToFooterMethod    =   $hCore->gCore['getLeadToFooterMethod'];   // Aktuellen Wert aus gCore holen
    $getLeadToFooterArg       =   $hCore->gCore['getLeadToFooterArg'];      // Aktuellen Wert aus gCore holen

    // Core Klasse aufgerufen? -> Dann das aktuelle und globale hCore - Objekt verwenden
    if ($getLeadToFooterClass == 'Core')
        $hFooter = $hCore;
    else
        $hFooter = new $getLeadToFooterClass($hCore);       // Footer - Klassen - Objekt erzeugen

    $hFooter->$getLeadToFooterMethod($getLeadToFooterArg);  // Footer - Methode aufrufen
    include $getLeadToFooterSite . '.inc.php';              // Footer - HTML - Seite includen

print ('</div>');





print ('</body></html>');
