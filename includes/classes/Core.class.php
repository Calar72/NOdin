<?php

/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 15:08
 *
 * Vererbungsfolge der (Basis) - Klassen:
 *  	Base							    		        Adam/Eva
 *  	'-> SystemConfig						            Child
 *  	   	'-> DefaultConfig					            Child
 *  			'-> Messages					            Child
 *  				'-> Debug					            Child
 * 					    '-> MySQLDB			                Child
 *  					    '-> Query	    	            Child
 * ===>    					    '-> Core    			    Child
 *  	    					    |-> ConcreteClass1	    Core - Child - AnyCreature
 * 			    				    |-> ...				    Core - Child - AnyCreatures
 * 				    			    |-> ConcreteClass20	    Core - Child - AnyCreature
 *
 */
class Core extends Query
{
    //public $gCore = array();  // Ist in der Base.class.php definiert!!!





    function __construct()
    {

        // Debug - Classname ausgeben?!
        $this->debugInitOnLoad('Class', $this->getClassName(false));


        parent::__construct();


        // TODO Objekt-Handling optimieren! Aktuell werden viele Objekte doppelt erzeugt!
        // Erzeuge Datenbank und passende dazu Query - Objekt
        if (!is_object($this->gCoreDB)){
            $this->gCoreDB      = new MySQLDB();
            $this->gCoreQuery   = new Query();
        }

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





}   // END class Core extends Query
