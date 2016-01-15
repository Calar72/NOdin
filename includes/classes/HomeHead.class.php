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





// Wechselt die Ausgabe/Anzeige des angegebenen Debug-Fensters (div-Tag) (an/aus)
    public function OLDdebugViewChange($arg)
    {

        $hCore = $this->hCore;

        // Message Ausgabe vorebeiten
        $hCore->gCore['Messages']['Type'][]      = 'Info';
        $hCore->gCore['Messages']['Code'][]      = 'Debug';
        $hCore->gCore['Messages']['Headline'][]  = 'Debug Informations- Fenster ein/aus!';


        if ($_SESSION['systemConfig']['Debug'][$arg] == 'yes'){
            $_SESSION['systemConfig']['Debug'][$arg] = 'no';

            // Message Ausgabe vorebeiten
            $hCore->gCore['Messages']['Message'][] = 'Debug Informations- Fenster ausgeschaltet!';

            RETURN TRUE;
        }


        // Message Ausgabe vorebeiten
        $hCore->gCore['Messages']['Message'][] = 'Debug Informations- Fenster eingeschaltet!';

        $_SESSION['systemConfig']['Debug'][$arg] = 'yes';


        RETURN TRUE;

    }





}   // END class HomeHead extends Core
