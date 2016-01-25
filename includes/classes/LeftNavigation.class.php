<?php

/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 15:08
 *
 * Vererbungsfolge der (Basis) - Klassen:
 *  	Base									            Adam/Eva
 *  	'-> SystemConfig						            Child
 *  	   	'-> DefaultConfig					            Child
 *  			'-> Messages					            Child
 *  				'-> Debug					            Child
 * 					    '-> MySQLDB			                Child
 *  					    '-> Query		                Child
 *      					    '-> Core			        Child
 * ===>	        					|-> ConcreteClass1	    Core - Child - AnyCreature
 * 			        				|-> ...				    Core - Child - AnyCreatures
 * 				        			|-> ConcreteClass20	    Core - Child - AnyCreature
 *
 */
class LeftNavigation extends Core
{

    public $gLeftNavigation = array();

    private $hCore;	            // Privates Core Objekt





    function __construct($hCore)
    {

        // Debug - Classname ausgeben?!
        $this->debugInitOnLoad('Class', $this->getClassName(false));


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





    // NULL - Funktion ... wird benötigt in der Action - Steuerung und dient als Platzhalter bzw. als Default - Aufruf
    function doNothing()
    {

        RETURN TRUE;

    }





    // INITIAL Erstellt die Navigationspunkte auf der linken Seite im Body
    public function leftNavigationGetLeftNavigation()
    {

        // Konvertierungs - Typen einlesen (Stammdaten / Buchungssatz)
        $this->getConvertTypes();

        // Datensatz Ursprung einlesen (Dimari, Centron usw.)
        $this->getConvertSystems();

        RETURN TRUE;

    }   // END public function leftNavigationGetLeftNavigation()




    // Liest die Konvertierungs - Typen ein
    private function getConvertTypes()
    {

        $hCore = $this->hCore;

        // Hole mir die Query für die Konvertierungs - Typen
        $query = $this->gCoreQuery->getQuery('leftNavigationGetConvertTypes');

        // Resultat der Konvertierungs - Typen
        $result = $this->gCoreDB->query($query);

        // Betroffene Zeilen, bzw. erhaltene
        $num_rows = $this->gCoreDB->num_rows($result);

        if ($num_rows > 0){
            while ($row = $result->fetch_object()){
                $hCore->gCore['LNav']['ConvertType'][]       = $row->sourceTypeName;
                $hCore->gCore['LNav']['ConvertTypeID'][]     = $row->sourceTypeID;
                $hCore->gCore['LNav']['ConvertTypeNum'][]    = $num_rows;
            }

            RETURN TRUE;
        }

        RETURN FALSE;

    }   // END private function getConvertType()







    // Liest die Konvertierungs - Typen ein
    private function getConvertSystems()
    {

        $hCore = $this->hCore;

        // Hole mir die Query für die Konvertierungs - Typen
        $query = $this->gCoreQuery->getQuery('leftNavigationGetConvertSystems');


        // Resultat der Konvertierungs - Typen
        $result = $this->gCoreDB->query($query);

        // Betroffene Zeilen, bzw. erhaltene
        $num_rows = $this->gCoreDB->num_rows($result);

        if ($num_rows > 0){
            while ($row = $result->fetch_object()){
                $hCore->gCore['LNav']['ConvertSystem'][]       = $row->sourceSystemName;
                $hCore->gCore['LNav']['ConvertSystemID'][]     = $row->sourceSystemID;
                $hCore->gCore['LNav']['ConvertSystemNum'][]    = $num_rows;
            }

            $this->gCoreDB->free_result($result);

            RETURN TRUE;
        }

        RETURN FALSE;

    }   // END private function getConvertSystems()



}   // END class LeftNavigation extends Core
