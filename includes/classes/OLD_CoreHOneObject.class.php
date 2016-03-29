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
class CoreHOneObject extends CoreGQuery
{

    protected static $obj = null;


    // Klassen eigener Konstruktor
    function __construct()
    {

        parent::__construct();

    }   // END function __construct()


    protected function __clone() { }


    // Stellt sicher, dass nur eine Instanz der Klasse erzeugt wird... aufruf dann über {klassenname}::getSigleton() ... ergibt das Objekt
    public static function getSingleton()
    {
        if (null === self::$obj)
            self::$obj = new self;

        return self::$obj;
    }




}   // END class CoreHOneObject extends CoreGQuery