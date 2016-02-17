    <?php
/**
 * Copyright (c) 2016 by Markus Melching (TKRZ)
 */

/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 17.02.2016
 * Time: 15:09
 */
class CoreBSystemConfig extends CoreABase
{

    // Pfad zur Config - Datei
    CONST SYSTEMCONFIGFILE = 'includes/configs/systemConfig.inc.ini';



    // Klassen eigener Konstruktor
    function __construct()
    {

        parent::__construct();

    }   // END function __construct()





    function loadSystemConfig()
    {

        // Prüfen ob Datei existiert ... wenn nicht, Programm hier und jetzt beenden
        if (!file_exists(self::SYSTEMCONFIGFILE)){
            die ('<hr>Fehler bei der Systempruefung ... fuehrt zu "die()":<br>- Datei "'.self::SYSTEMCONFIGFILE.'" nicht lesbar! (Hinweis: Leserechte richtig gesetzt?)<br><hr>');
        }

        // .ini / Config - Datei parsen
        if (!$ConfigArray = parse_ini_file(self::SYSTEMCONFIGFILE, TRUE)){
            die ('<hr>Fehler bei der Systempruefung ... fuehrt zu "die()":<br>- Datei "'.self::SYSTEMCONFIGFILE.'" Syntax Fehler! (Hinweis: Eintraege und Kommentare pruefen.)<br><hr>');
        }

        echo "<pre>";
        print_r($ConfigArray);
        echo "</pre>";

    }   // END function loadSystemConfig()


}   // END class CoreBSystemConfig extends CoreABase