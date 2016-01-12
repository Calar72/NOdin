<?php
/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 07.01.2016
 * Time: 09:42
 *
 * Haupt - Layout - Seite für das Framework
 *
 * 0010) Debug - Dateinamen ausgeben?!
 *
 * 0020) Definiere: Default zu-landende-Datein für:
 *          Head
 *          Body
 *          Footer
 *
 * 0030) Mögliche Get und Post - Argumente werden in der Base.class.php gespeichert!
 *
 * 0040) Action - Steuerung
 *
 * 0050) Dynamischer Include HTML - Head
 *
 * 0060) Develop - Datei includen (für diverse tmp-Ausaben bei der Entwicklung)
 *
 * 0070) Dynamischer Include HTML - Body
 *
 * 0080) Dynamischer Include HTML - Footer
 *          ACHTUNG: Der Footer wird in der index.php geladen!
 *          Grund: Eine formatierte Ausgabe der Debug und Zusatzinformationen ist sonst nicht möglich.
 *
 */

//FIXME SICHERHEIT Prüfen ob Klasse vorhanden und Zugriff ok
//FIXME SICHERHEIT Prüfen ob Datei vorhanden und Zugriff ok

// 0010) Debug - Dateinamen ausgeben?!
$hCore->debugInitOnLoad('File',__FILE__);





// 0020) Definiere: Default zu-landende-Datein für:
// Default Head
// Speiechere Default - Aufruf in die Head - Steuerung
// Wird ggf. durch die Action.class.php überschrieben (s.u.)
$hCore->gCore['getLeadToHeadClass']     = 'DefaultHead';
$hCore->gCore['getLeadToHeadSite']      = 'includes/html/default/defaultHead';
$hCore->gCore['getLeadToHeadMethod']    = 'doNothing';


// Default Body
// Speiechere Default - Aufruf in die Body - Steuerung
// Wird ggf. durch die Action.class.php überschrieben (s.u.)
$hCore->gCore['getLeadToBodyClass']     = 'DefaultBody';
$hCore->gCore['getLeadToBodySite']      = 'includes/html/default/defaultBody';
$hCore->gCore['getLeadToBodyMethod']    = 'doNothing';


// Default Footer
// Speiechere Default - Aufruf in die Footer - Steuerung
// Wird ggf. durch die Action.class.php überschrieben (s.u.)
$hCore->gCore['getLeadToFooterClass']     = 'DefaultFooter';
$hCore->gCore['getLeadToFooterSite']      = 'includes/html/default/defaultFooter';
$hCore->gCore['getLeadToFooterMethod']    = 'doNothing';






// 0030) Mögliche Get und Post - Argumente speichern
// Wird jetzt in der Base.class.php durchgeführt!
//$hCore->gCore['getGET']  = $hCore->getCleanInput($_GET);
//$hCore->gCore['getPOST'] = $hCore->getCleanInput($_POST);





// 0040) Action - Steuerung
// Starte die Action Steuereung ... UEBERSCHREIBE GGF. DIE DEFAULT EINSTELLUNGEN
$hAction = new Action($hCore);





//FIXME SICHERHEIT - LeadToXYZSite muss mit Rechte abgefangen werden!




// 0050) Dynamischer Include HTML - Head
// Erzeuge Head - Klassen - Objekt (Dynamisch nach Default (s.o.) und ggf. Änderungen durch die Action (s.o.)
$getLeadToHeadClass     =   $hCore->gCore['getLeadToHeadClass'];    // Aktuellen Wert aus gCore holen
$getLeadToHeadSite      =   $hCore->gCore['getLeadToHeadSite'];     // Aktuellen Wert aus gCore holen
$getLeadToHeadMethod    =   $hCore->gCore['getLeadToHeadMethod'];   // Aktuellen Wert aus gCore holen

$hHead = new $getLeadToHeadClass($hCore);   // Head - Klassen - Objekt erzeugen
$hHead->$getLeadToHeadMethod();             // Head - Methode aufrufen
include $getLeadToHeadSite . '.inc.php';    // Head - HTML - Seite includen





// 0060) Develop - Datei includen (für diverse tmp-Ausaben bei der Entwicklung)
if ($_SESSION['systemConfig']['Develop']['enableDevelop'] == 'yes'){
    include 'includes/develop/develop.inc.php';
}





// 0070) Dynamischer Include HTML - Body
// Erzeuge Body - Klassen - Objekt (Dynamisch nach Default (s.o.) und ggf. Änderungen durch die Action (s.o.)
$getLeadToBodyClass     =   $hCore->gCore['getLeadToBodyClass'];    // Aktuellen Wert aus gCore holen
$getLeadToBodySite      =   $hCore->gCore['getLeadToBodySite'];     // Aktuellen Wert aus gCore holen
$getLeadToBodyMethod    =   $hCore->gCore['getLeadToBodyMethod'];   // Aktuellen Wert aus gCore holen

$hBody = new $getLeadToBodyClass($hCore);   // Body - Klassen - Objekt erzeugen
$hBody->$getLeadToBodyMethod();             // Body - Methode aufrufen
include $getLeadToBodySite . '.inc.php';    // Body - HTML - Seite includen





// 0080) Dynamischer Include HTML - Footer
// Wird in der index.php geladen!
// Grund: Eine formatierte Ausgabe der Debug und Zusatzinformationen ist sonst nicht möglich.
