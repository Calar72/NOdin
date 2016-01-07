<?php
/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 14:42
 *
 * Steuert die Klassen - Implementierung
 *
 */


// PHP Klassen Auto-Loader (REQUIRE PHP 5.3.0)
spl_autoload_register(function ($class) { include 'includes/classes/' . $class . '.class.php'; } );




// Initialisiere Base->Core - Klassen - Objekt
$hCore = new Core();
