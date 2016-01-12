<?php

/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 15:08
 *
 * Vererbungsfolge der (Basis) - Klassen:
 *  	Base							    		        Adam/Eva
 *  	'-> SystemConfig				    		        Child
 *  	   	'-> DefaultConfig			    		        Child
 * ===>			'-> Messages			    		        Child
 *  				'-> Debug			    		        Child
 * 					    '-> MySQLDB		    	            Child
 *  					    '-> Query	    	            Child
 *       					    '-> Core    			    Child
 * 		    					    |-> ConcreteClass1	    Core - Child - AnyCreature
 * 			    				    |-> ...				    Core - Child - AnyCreatures
 * 				    			    |-> ConcreteClass20	    Core - Child - AnyCreature
 *
 */
class Messages extends DefaultConfig
{

    public $gMessages = array();





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





    // INITIAL - Gibt den übergebenen Content in einem HTML Pre-Tag aus!
    function simpleout($arg, $splitArray=FALSE)
    {

        // Wurde $getContent als Array übergeben?
        if ( (is_array($arg)) && ($splitArray) ){

            foreach ($arg as $curContent)
                $this->simpleoutString($curContent);	// Übergebe an Sub-Ausgabe-Methode

        }
        else
            $this->simpleoutString($arg);		// Übergebe an Sub-Ausgabe-Methode

        RETURN TRUE;

    }	// END function simpleout(...)





    // SUB - Gibt den übergebenen Content in einem HTML Pre-Tag aus!
    private function simpleoutString($value)
    {

        print ("<br><pre><br>");
            print_r($value);
        print ("<br></pre><br>");

        RETURN TRUE;

    }	// END simpleoutString(...)





    // INITIAL - Gibt den übergebenen Content in einem HTML Pre-Tag aus!
    // Zusaetzlich muss eine Überschrift angegeben werden
    function detaileout($getHeadline, $getContent, $splitArray=FALSE)
    {

        // Wurde $getContent als Array übergeben?
        if ( (is_array($getContent)) && ($splitArray) ){

            foreach ($getContent as $curContent)
                $this->detailoutString($curContent, $getHeadline);	// Übergebe an Sub-Ausgabe-Methode

        }
        else
            $this->detailoutString($getContent, $getHeadline);		// Übergebe an Sub-Ausgabe-Methode


        RETURN TRUE;

    }	// END function detaileout(...)





    // Gibt den übergebenen Content in einem HTML Pre-Tag aus!
    private function detailoutString($value, $getHeadline = '')
    {
        //TODO CSS Für debugHeadLine anlegen
        if (strlen($getHeadline) > 0)
            print ('<br><pre><div class="debugHeadline" style="text-decoration: underline;">&nbsp;' .$getHeadline. '&nbsp;</div><br>');
        else
            print ("<br><pre><br>");

        print_r($value);
        print ("<br></pre><br>");

        RETURN TRUE;

    }	// END detailoutString(...)





}   // END class Messages extends DefaultConfig
