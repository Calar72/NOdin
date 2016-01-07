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
 *  				'-> Debug					Child
 *  					'-> Core				Child
 * 							|-> MySQLDB			Child
 * ===>						|-> ConcreteClass1	Core - Child - AnyCreature
 * 							|-> ...				Core - Child - AnyCreatures
 * 							|-> ConcreteClass20	Core - Child - AnyCreature
 *
 */
class DefaultBody extends Core
{

    public $gBody = array();

    private $hCore;	            // Privates Core Objekt





    function __construct($hCore)
    {

        // Debug - Classname ausgeben?!
        $this->initDebugOnLoad('Class', $this->getClassName(false));


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





    // INITIAL liefert die zu ladende Body-Seite
    function getLeadToBodySite()
    {

        // Verwende öffentliches Objekt
        $hCore = $this->hCore;

        // Body - Include schon definiert durch Action.class.php?
        if ( (isset($hCore->gCore['Body']['setByAction']) && (strlen($hCore->gCore['Body']['setByAction']) > 0)) )
            RETURN $hCore->gCore['getLeadToBodySite'];


        // Body - Include noch nicht definiert, setze getLeadToHeadSite auf Default - Wert
        $hCore->gCore['getLeadToBodySite'] = 'html/defaultBody';


        RETURN $hCore->gCore['getLeadToBodySite'];

    }   // END function getLeadToBodySite()





}   // END class DefaultBody extends Core
