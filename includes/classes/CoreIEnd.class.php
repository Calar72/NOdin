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
class CoreIEnd extends CoreHOneObject
{

    public $myDynObj;   // Objekt Handler aus dem Core - Klassen - System
    public $coreValue; // Kopierte globale Variable aus der Start-Klasse

    public $myValue;    // Klassen eigene Variable


    function __construct($flagUseGlobalCoreClassObj=TRUE)
    {

        parent::__construct();

        // Benutze das globale Core-Klassen-Objekt in der Klasse
        if ($flagUseGlobalCoreClassObj)
            $this->getGlobalCoreObject();

    }


    // Benutze in der Klasse das globale Core-Klassen-Objekt
    private function getGlobalCoreObject()
    {
        // Sicher stellen das wir den Core-Klassen-Objekt - Handler der Basisklassen nur einmal haben / benutzen
        $this->myDynObj = CoreHOneObject::getSingleton();

        // Lokales startValue referenzieren
        // Macht die weitere Verarbeitung innerhalb der Klasse einfacher...
        // Die globale startValue wird somit immer mit geupdatet (wenn Flag s.o. = true)
        $this->coreValue = & $this->myDynObj->coreValue;
    }



}   // END class CoreIEnd extends CoreHOneObject