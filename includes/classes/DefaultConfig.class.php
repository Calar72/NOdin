<?php

/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 15:08
 *
 * Vererbungsfolge der (Basis) - Klassen:
 *  	Base									            Adam/Eva
 *  	'-> SystemConfig						            Child
 *  	   	'-> DefaultConfig					            Child
 *  			'-> Messages					            Child
 *  				'-> Debug					            Child
 * 					    '-> MySQLDB			                Child
 *  					    '-> Query		                Child
 *      					    '-> Core			        Child
 * ===>	        					|-> ConcreteClass1	    Core - Child - AnyCreature
 * 			        				|-> ...				    Core - Child - AnyCreatures
 * 				        			|-> ConcreteClass20	    Core - Child - AnyCreature
 *
 */
class DefaultConfig extends SystemConfig
{

    public $gDefaultConfig = array();





    function __construct()
    {

        // Debug - Classname ausgeben?!
        Debug::debugInitOnLoad('Class', __CLASS__);


        parent::__construct();

    }	// END function __construct()





    private function getMyClassName($printOnScreen=false)
    {

        if ($printOnScreen)
            print ("<br>Ich bin Klasse: " . __CLASS__ . "<br>") ;

        return __CLASS__;

    }	// END function getMyClassName(...)





    function getClassName($printOnScreen=false)
    {

        $myClassNmae = $this->getMyClassName($printOnScreen);

        return $myClassNmae;

    }	// END function getClassName(...)





    // INITIAL Lädt die System - Configurationsdatei
    function loadDefaultConfig()
    {

        // Default bzw. custom-Config einlesen
        $curConfigFile = 'includes/configs/defaultConfig.inc.ini';

        // Prüfen ob Datei existiert
        if (!file_exists($curConfigFile)){
            header('Content-Type: text/html; charset=UTF-8');
            die ('<hr>Fehler bei der Systemprüfung:<br>- Datei "'.$curConfigFile.'" nicht lesbar! (Hinweis: Leserechte richtig gesetzt?)<br><hr>');
        }

        $ini_DefaultConfig = parse_ini_file($curConfigFile, TRUE);




        // Datenbank-Setting einlesen
        $curConfigFile = 'includes/configs/databaseConfig.inc.ini';

        // Prüfen ob Datei existiert
        if (!file_exists($curConfigFile)){
            header('Content-Type: text/html; charset=UTF-8');
            die ('<hr>Fehler bei der Systemprüfung:<br>- Datei "'.$curConfigFile.'" nicht lesbar! (Hinweis: Leserechte richtig gesetzt?)<br><hr>');
        }

        $ini_DBConfig = parse_ini_file($curConfigFile, TRUE);


        $ini_Full = array_merge($ini_DefaultConfig, $ini_DBConfig);

        $_SESSION['customConfig'] = $ini_Full;
    }


}   // END class DefaultConfig extends SystemConfig
