<?php

/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 14:52
 *
 * Vererbungsfolge der (Basis) - Klassen:
 * ===>	Base									Adam/Eva
 * 		'-> SystemConfig						Child
 * 		   	'-> DefaultConfig					Child
 * 				'-> Messages					Child
 * 					'-> Debug					Child
 * 						'-> Core				Child
 * 							|-> MySQLDB			Child
 * 							|-> ConcreteClass1	Core - Child - AnyCreature
 * 							|-> ...				Core - Child - AnyCreatures
 * 							|-> ConcreteClass20	Core - Child - AnyCreature
 *
 */
class Base
{

    public $gBase = array();





    function __construct()
    {

        // Debug - Classname ausgeben?!
        Debug::debugInitOnLoad('Class', __CLASS__);

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





    // Methode gibt ein Array zurück
    function getArgumentAsArray($arg)
    {
        $myArg = array();

        if (!is_array($arg))
            $myArg['AUTO'] = $arg;
        else
            $myArg = $arg;

        RETURN $myArg;

    }





    // INITIAL Methode "säubert" GET und POST - Argumente
    function getCleanInput($arg)
    {
        $myArg = $this->getArgumentAsArray($arg);

        //FIXME SICHERHEIT - GET und POST - Argumente "säubern" eventuell mit foreach durchgehen?

        RETURN $myArg;
    }





    // Methode prüft eine String-Länge und gibt true oder false zurück
    function checkLenMinMax($varToCheck, $minLen, $maxLen = '')
    {

        // String ist zu kurz?
        if (strlen($varToCheck) < $minLen)
            RETURN FALSE;


        // String ist zu lang?
        if ( ($maxLen > 0) && (strlen($varToCheck) > $maxLen) )
            RETURN FALSE;


        // String ist ok!
        RETURN TRUE;

    }	// END function checkLen(...)





}   // END class Base
