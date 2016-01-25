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

    public $gBase = array();

    public $gCore = array();
    public $gCoreDB;
    public $gCoreQuery;






    function __construct()
    {

        // Debug - Classname ausgeben?!
        Debug::debugInitOnLoad('Class', __CLASS__);


        //TODO Die Get/Post Variable müssen besser abgefangen werden!
        // Post und Get - Variable speichern
        $this->gCore['getGET']  = $this->getCleanInput($_GET);
        $this->gCore['getPOST'] = $this->getCleanInput($_POST);

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





    // Methode bereitet Größe lesbarer auf
    function formatSizeUnits($bytes) {
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
    }





}   // END class Base
