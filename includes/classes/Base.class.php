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




}   // END class Base
