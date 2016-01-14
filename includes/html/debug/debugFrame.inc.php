<?php
/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 13.01.2016
 * Time: 13:05
 */

// Message unterdrücken wegen php-redirect und header-already-send - Problem?
if ( (isset($hCore->gCore['showNoMessage'])) && ($hCore->gCore['showNoMessage'] == 'yes') ) {
    // Keine Ausgabe!
    // Logout aufgerufen ... werde header neu laden!
}
else {
    include 'debugOptions.inc.php';
}
