<?php
/**
 * Copyright (c) 2016 by Markus Melching (TKRZ)
 */

/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 17.02.2016
 * Time: 15:09
 */
class CoreTest extends CoreIEnd
{

    //public $myDynObj;   // Objekt Handler aus dem Core - Klassen - System
    //public $coreValue; // Kopierte globale Variable aus der Start-Klasse

    public $myValue;    // Klassen eigene Variable


    function __construct()
    {

        parent::__construct();

    }


    function testing()
    {
        $this->coreValue = 'Peter';
    }



}   // END class CoreIEnd extends CoreHCore