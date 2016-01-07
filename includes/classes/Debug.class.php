<?php

/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 15:08
 *
 * Vererbungsfolge der (Basis) - Klassen:
 *  	Base									Adam/Eva
 *  	'-> SystemConfig						Child
 *  	   	'-> DefaultConfig					Child
 *  			'-> Messages					Child
 * ===>				'-> Debug					Child
 * 						'-> Core				Child
 * 							|-> MySQLDB			Child
 * 							|-> ConcreteClass1	Core - Child - AnyCreature
 * 							|-> ...				Core - Child - AnyCreatures
 * 							|-> ConcreteClass20	Core - Child - AnyCreature
 *
 */
class Debug extends Messages
{

    public $gDebug = array();





    function __construct()
    {

        // Debug - Classname ausgeben?!
        $this->initDebugOnLoad('Class', __CLASS__);


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





    // INITIAL Methode ... die Methode steuert grundlegende Debug - Funktionen
    // Wird aufgerufen beim laden einer Datei
    static function initDebugOnLoad($getType, $getValue)
    {

        // Debug eingeschaltet?
        if (!self::getDebugStatus('enableDebug'))
            RETURN FALSE;


        // Debug auf Monitor ausgeben?
        if (self::getDebugStatus('ShowOnScreen')){

            // Klassennamen ausgeben?
            if ( ($getType == 'Class') && (self::getDebugStatus('ShowClassname')) )
                    self::simpleout('Ich bin Klasse: '.$getValue);


            // Dateinamen ausgeben?
            elseif ( ($getType == 'File') && (self::getDebugStatus('ShowFilename')) )
                    self::simpleout(basename($getValue));

        }

        RETURN TRUE;

    }   // END function initDebugOnLoad(...)





    // Prüft ob ein Debug Einstellungswert yes/no ist
    private function getDebugStatus($arg)
    {
        if ( (isset($_SESSION['systemConfig']['Debug'][$arg])) && ($_SESSION['systemConfig']['Debug'][$arg] == 'yes') )
            RETURN TRUE;

        RETURN FALSE;

    }   // END private function getDebugStatus(...)





    // Debug - GET, POST, SESSSION, GLOBAL - Variable ausgeben?
    function initDebugVarOutput()
    {

        $curVarArray = array(	'ShowGET' 		=> $_GET,
                                'ShowPOST' 		=> $_POST,
                                'ShowSession' 	=> $_SESSION,
                                'ShowGLOBALS' 	=> $GLOBALS
        );


        $curNameArray = array(	'ShowGET' 		=> '$_GET',
                                'ShowPOST' 		=> '$_POST',
                                'ShowSession' 	=> '$_SESSION',
                                'ShowGLOBALS' 	=> '$GLOBALS'
        );


        foreach ($curVarArray as $key=>$var){

            // <hr> Tag ausgeben? - Steuerung
            $htmlHRTagDone = false;

            // Soll der Schlüssel bzw.die Variable ausgegeben werden?
            if ($this->getDebugStatus($key)){

                // <hr> Tag ausgeben?
                if (!$htmlHRTagDone)
                    $this->simpleout('<hr>');

                // <hr> Tag ausgegeben, also auf true setzen
                $htmlHRTagDone = true;

                // Variable plus Headline ausgeben
                $this->detaileout($curNameArray[$key], $var);

            }

        }

        RETURN TRUE;

    }   // END function initDebugVarOutput()





}   // END class Debug extends Messages
