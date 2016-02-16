<?php

/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 15:08
 *
 * Vererbungsfolge der (Basis) - Klassen:
 *  	Base							    		        Adam/Eva
 * ===>	'-> SystemConfig			    			        Child
 *  	   	'-> DefaultConfig		    			        Child
 *  			'-> Messages		    			        Child
 *  				'-> Debug			    		        Child
 * 					    '-> MySQLDB			                Child
 *  					    '-> Query	    	            Child
 *       					    '-> Core    			    Child
 * 		    		    			|-> ConcreteClass1	    Core - Child - AnyCreature
 * 			    	    			|-> ...				    Core - Child - AnyCreatures
 * 				        			|-> ConcreteClass20	    Core - Child - AnyCreature
 *
 */
class SystemConfig extends Base
{

    public $gSystemConfig   = array();





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
    function loadSystemConfig()
    {
        $curConfigFile = 'includes/configs/systemConfig.inc.ini';

        // Prüfen ob Datei existiert
        if (!file_exists($curConfigFile)){
            header('Content-Type: text/html; charset=UTF-8');
            die ('<hr>Fehler bei der Systemprüfung:<br>- Datei "'.$curConfigFile.'" nicht lesbar! (Hinweis: Leserechte richtig gesetzt?)<br><hr>');
        }

        // .ini / Config - Datei parsen
        $ini_ConfigArray = parse_ini_file($curConfigFile, TRUE);


        // Debug - Leiste einblenden?
        if (isset($_SESSION['systemConfig']['Debug']['enableDebugFrame']))
            $ini_ConfigArray['Debug']['enableDebugFrame'] = $_SESSION['systemConfig']['Debug']['enableDebugFrame'];
        else
            $ini_ConfigArray['Debug']['enableDebugFrame'] = 'no';


        // Debug - Values dauerhaft einblenden?
        if (isset($_SESSION['systemConfig']['Debug']['enableShowDebugValue']))
            $ini_ConfigArray['Debug']['enableShowDebugValue'] = $_SESSION['systemConfig']['Debug']['enableShowDebugValue'];
        else
            $ini_ConfigArray['Debug']['enableShowDebugValue'] = 'no';


        // Debug - Link - Leiste dauerhaft einblenden?
        if (isset($_SESSION['systemConfig']['Debug']['enableShowDebugLinks']))
            $ini_ConfigArray['Debug']['enableShowDebugLinks'] = $_SESSION['systemConfig']['Debug']['enableShowDebugLinks'];
        else
            $ini_ConfigArray['Debug']['enableShowDebugLinks'] = 'no';


        // Session Config setzen
        $_SESSION['systemConfig'] = $ini_ConfigArray;

    }










}   // END class SystemConfig extends Base
