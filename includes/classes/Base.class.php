<?php

/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 14:52
 *
 * Vererbungsfolge der (Basis) - Klassen:
 * ===>	Base								    	        Adam/Eva
 *  	'-> SystemConfig					    	        Child
 *  	   	'-> DefaultConfig				    	        Child
 *  			'-> Messages			    		        Child
 *  				'-> Debug				    	        Child
 * 					    '-> MySQLDB		    	            Child
 *  					    '-> Query	    	            Child
 *       					    '-> Core    			    Child
 * 		    				    	|-> ConcreteClass1	    Core - Child - AnyCreature
 * 			    	    			|-> ...				    Core - Child - AnyCreatures
 * 				        			|-> ConcreteClass20	    Core - Child - AnyCreature
 *
 */
class Base
{

    // Klassen - Variable Base
    public $gBase = array();

    // Klassen - Variable gCore ... wird in nahezu allen Sub-Klassen verwendet und dient der Uebergabe diverser Inhalte/Parameter
    public $gCore = array();

    // Beinhaltet das MySQL - Objekt
    public $gCoreDB;

    // Beinhaltet das Klassen - Objekt fuer die Query - Bereitstellung
    public $gCoreQuery;


    /**
     * Base constructor.
     */
    function __construct()
    {

        // Debug - Classname ausgeben?!
        Debug::debugInitOnLoad('Class', __CLASS__);


        //TODO Die Get/Post Variable muessen besser abgefangen werden!
        // Post und Get - Variable speichern
        $this->gCore['getGET']  = $this->getCleanInput($_GET);
        $this->gCore['getPOST'] = $this->getCleanInput($_POST);

    }	// END function __construct()





    /**
     * Liefert den aktuellen Klassen-Namen
     * Gibt den aktuellen Klassen-Namen ggf. auf den Bildschirm aus
     *
     * @param bool $printOnScreen
     * @return string
     */
    private function getMyClassName($printOnScreen=false)
    {

        if ($printOnScreen)
            print ("<br>Ich bin Klasse: " . __CLASS__ . "<br>") ;

        return __CLASS__;

    }	// END function getMyClassName(...)





    /**
     * INITIAL - Liefert den aktuellen Klassen-Namen
     * Gibt den aktuellen Klassen-Namen ggf. auf den Bildschirm aus
     *
     * @param bool $printOnScreen
     * @return string
     */
    function getClassName($printOnScreen=false)
    {

        $myClassNmae = $this->getMyClassName($printOnScreen);

        return $myClassNmae;

    }	// END function getClassName(...)





    /**
     * Liefert ein Array zurueck
     *
     * @param $arg
     * @return array
     */
    function getArgumentAsArray($arg)
    {

        $myArg = array();

        if (!is_array($arg))
            $myArg['AUTO'] = $arg;
        else
            $myArg = $arg;

        RETURN $myArg;

    }   // END function getArgumentAsArray(...)





    /**
     * INITIAL - Liefert "saeubert" GET und POST - Argumente
     *
     * @param $arg
     * @return array
     */
    function getCleanInput($arg)
    {

        $myArg = $this->getArgumentAsArray($arg);

        //FIXME SICHERHEIT - GET und POST - Argumente "saeubern" eventuell mit foreach durchgehen?

        RETURN $myArg;

    }   // END function getCleanInput(...)






    /**
     * Methode prueft eine String-Laenge und gibt true oder false zurueck
     *
     * @param $varToCheck
     * @param $minLen
     * @param string $maxLen
     * @return bool
     */
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





    /**
     * Methode bereitet Datei-Groesse lesbarer auf
     *
     * @param $bytes
     * @return string
     */
    function formatSizeUnits($bytes)
    {

        if ($bytes >= 1073741824)
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';

        elseif ($bytes >= 1048576)
            $bytes = number_format($bytes / 1048576, 2) . ' MB';

        elseif ($bytes >= 1024)
            $bytes = number_format($bytes / 1024, 2) . ' KB';

        elseif ($bytes > 1)
            $bytes = $bytes . ' bytes';

        elseif ($bytes == 1)
            $bytes = $bytes . ' byte';

        else
            $bytes = '0 bytes';

        return $bytes;

    }   // END function formatSizeUnits(...)





}   // END class Base
