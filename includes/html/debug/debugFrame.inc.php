<?php
/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 13.01.2016
 * Time: 13:05
 */


// SICHERHEIT
// 1. Abfangen ob der Benutzer eingeloggt ist
// 2. Abfangen ob der Benutzer den Status 'Entwickler' hat
// Wenn nicht, hier abbrechen und nichts weiter ausgeben

if ( (isset($_SESSION['Login']['User']['roleID'])) && ($_SESSION['Login']['User']['roleID'] == '1') ) {


    // Message unterdrücken wegen php-redirect und header-already-send - Problem?
    if ( (isset($hCore->gCore['showNoMessage'])) && ($hCore->gCore['showNoMessage'] == 'yes') ) {
        // Keine Ausgabe!
        // Logout aufgerufen ... werde header neu laden!
    }
    else {
        include 'debugOptions.inc.php';
    }


}

