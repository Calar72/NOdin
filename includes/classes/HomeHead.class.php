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
class HomeHead extends Core
{

    public $gHomeHead = array();

    private $hCore;	            // Privates Core Objekt





    function __construct($hCore)
    {

        // Debug - Classname ausgeben?!
        $this->debugInitOnLoad('Class', $this->getClassName(false));


        // Speichere das Öffentliche hCore - Objekt zur weiteren Verwendung lokal
        $this->hCore = $hCore;


        parent::__construct();

    }    // END function __construct()





    private function getMyClassName($printOnScreen = false)
    {

        if ($printOnScreen)
            print ("<br>Ich bin Klasse: " . __CLASS__ . "<br>");

        return __CLASS__;

    }    // END function getMyClassName(...)





    function getClassName($printOnScreen = false)
    {

        $myClassNmae = $this->getMyClassName($printOnScreen);

        return $myClassNmae;

    }    // END function getClassName(...)





    // NULL - Funktion ... wird benötigt in der Action - Steuerung und dient als Platzhalter bzw. als Default - Aufruf
    function doNothing()
    {

        RETURN TRUE;

    }   // END function doNothing()





    // Wechselt die Ausgabe/Anzeige des Debug-Fensters (an/aus)
    public function homeHeadSwitchDebugFrame()
    {

        $hCore = $this->hCore;

        // Message Ausgabe vorebeiten
        $hCore->gCore['Messages']['Type'][]      = 'Info';
        $hCore->gCore['Messages']['Code'][]      = 'Debug';
        $hCore->gCore['Messages']['Headline'][]  = 'DebugFrame on/off!';


        if ($_SESSION['systemConfig']['Debug']['enableDebugFrame'] == 'yes'){
            $_SESSION['systemConfig']['Debug']['enableDebugFrame'] = 'no';

            // Message Ausgabe vorebeiten
            $hCore->gCore['Messages']['Message'][] = 'Debug - Options-Fenster ausgeschaltet!';

            RETURN TRUE;
        }


        // Message Ausgabe vorebeiten
        $hCore->gCore['Messages']['Message'][] = 'Debug - Options-Fenster eingeschaltet!';

        $_SESSION['systemConfig']['Debug']['enableDebugFrame'] = 'yes';


        RETURN TRUE;

    }   // END public function homeHeadSwitchDebugFrame()





    // Wechselt die Ausgabe/Anzeige des Debug-Fensters (an/aus)
    public function homeHeadSwitchDebugValue()
    {

        $hCore = $this->hCore;

        // Message Ausgabe vorebeiten
        $hCore->gCore['Messages']['Type'][]      = 'Info';
        $hCore->gCore['Messages']['Code'][]      = 'Debug';
        $hCore->gCore['Messages']['Headline'][]  = 'DebugValue on/off!';


        if ($_SESSION['systemConfig']['Debug']['enableShowDebugValue'] == 'yes'){
            $_SESSION['systemConfig']['Debug']['enableShowDebugValue'] = 'no';

            // Message Ausgabe vorebeiten
            $hCore->gCore['Messages']['Message'][] = 'Debug - Value - Ausgabe ausgeschaltet!';

            RETURN TRUE;
        }


        // Message Ausgabe vorebeiten
        $hCore->gCore['Messages']['Message'][] = 'Debug - Value - Ausgabe eingeschaltet!';

        $_SESSION['systemConfig']['Debug']['enableShowDebugValue'] = 'yes';


        RETURN TRUE;

    }   // END public function homeHeadSwitchDebugFrame()





}   // END class HomeHead extends Core
