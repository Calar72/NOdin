<?php
/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 13:48
 *
 * 001) Initial - Starte Session - Management
 * 002) System - Check durchführen
 * 003) Core - Klassen implementieren
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




// 002) System - Check durchführen
require_once 'includes/system/systemCheck.inc.php';




// 003) Core - Klassen implementieren
require 'includes/system/systemClassLoad.inc.php';

print_r($_GET);




