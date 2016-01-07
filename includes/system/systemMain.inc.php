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
 * 0020) Default zu-landende-Datein festlegen für:
 *          Head
 *          Body
 *          Footer
 *
 * 0030) Mögliche Get und Post - Argumente speichern
 *
 * 0040) Action - Steuerung
 *
 * 0050) Dynamischer Include HTML - Head
 *
 * 0060) Dynamischer Include HTML - Body
 *
 * 0070) Dynamischer Include HTML - Footer
 *          ACHTUNG: Der Footer wird in der index.php geladen!
 *          Grund: Eine formatierte Ausgabe der Debug und Zusatzinformationen ist sonst nicht möglich.
 *
 */

//FIXME SICHERHEIT Prüfen ob Klasse vorhanden und Zugriff ok
//FIXME SICHERHEIT Prüfen ob Datei vorhanden und Zugriff ok





// 0010) Debug - Dateinamen ausgeben?!
$hCore->initDebugOnLoad('File',__FILE__);





// 0020) Default zu-landende-Datein festlegen für:
// Default Head
$hCore->gCore['getLeadToHeadSite'] = 'html/defaultHead';
$getLeadToHeadClass  = 'DefaultHead';

// Default Body
$hCore->gCore['getLeadToBodySite'] = 'html/defaultBody';
$getLeadToBodyClass  = 'DefaultBody';

// Default Footer
$hCore->gCore['getLeadToFooterSite'] = 'includes/html/defaultFooter';
$getLeadToFooterClass = 'DefaultFooter';





//TODO Die Get/Post Variable müssen besser abgefangen werden!
// 0030) Mögliche Get und Post - Argumente speichern
$hCore->gCore['getGET']  = $hCore->getCleanInput($_GET);
$hCore->gCore['getPOST'] = $hCore->getCleanInput($_POST);




// 0040) Action - Steuerung
// Starte die Action Steuereung
//$hAction = new Action($hCore);





// $hCore->simpleout($hCore->classArg);
//FIXME SICHERHEIT - LeadToXYZSite muss mit Rechte abgefangen werden!





// 0050) Dynamischer Include HTML - Head
// Erzeuge Head - Klassen - Objekt (Dynamisch nach Default (s.o.) und ggf. Änderungen durch die Action (s.o.)
$hHead = new $getLeadToHeadClass($hCore);	// WICHTIG Übergebe hCore - Objekt

$getLeadToHeadSite = $hHead->getLeadToHeadSite();
// --> load Head
//include $getLeadToHeadSite . '.inc.php';
$hCore->simpleout('Geladene Head-Seite: ' . $getLeadToHeadSite);





// 0060) Dynamischer Include HTML - Body
// Erzeuge Body - Klassen - Objekt (Dynamisch nach Default (s.o.) und ggf. Änderungen durch die Action (s.o.)
$hBody = new $getLeadToBodyClass($hCore);	// WICHTIG Übergebe hCore - Objekt

$getLeadToBodySite = $hBody->getLeadToBodySite();
// --> load Body
//include $getLeadToBodySite . '.inc.php';
$hCore->simpleout('Geladene Body-Seite: ' . $getLeadToBodySite);





// 0070) Dynamischer Include HTML - Footer
// Wird in der index.php geladen!
// Grund: Eine formatierte Ausgabe der Debug und Zusatzinformationen ist sonst nicht möglich.
