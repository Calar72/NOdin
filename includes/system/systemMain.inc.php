<?php
/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 07.01.2016
 * Time: 09:42
 *
 * Haupt - Layout - Seite für das Framework
 *
 *
 *
 */

// Debug - Dateinamen ausgeben?!
$hCore->initDebugOnLoad('File',__FILE__);





// Default Seitensteuerung
$hCore->gCore['getLeadToHeadSite'] = 'html/defaultHead';
$getLeadToHeadClass  = 'DefaultHead';

$hCore->gCore['getLeadToBodySite'] = 'html/defaultBody';
$getLeadToBodyClass  = 'DefaultBody';

$hCore->gCore['getLeadToFooterSite'] = 'includes/html/defaultFooter';
$getLeadToFooterClass = 'DefaultFooter';





// Hier müsste jetzt die Action - Steuerung implementiert werden
// Starte die Action Steuereung

// Mögliche Get und Post - Argumente speichern
$hCore->gCore['getGET']  = $hCore->getCleanInput($_GET);
$hCore->gCore['getPOST'] = $hCore->getCleanInput($_POST);

//$hAction = new Action($hCore);


// $hCore->simpleout($hCore->classArg);
//FIXME SICHERHEIT - LeadToXYZSite muss mit Rechte abgefangen werden!


// Load Head


// Load Body


// Load Footer