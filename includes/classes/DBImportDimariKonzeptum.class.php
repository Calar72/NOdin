<?php
/**
 * Copyright (c) 2016 by Markus Melching (TKRZ)
 */

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
class DBImportDimariKonzeptum extends Core
{

    public $gDBImportDimari = array();

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





    function importBaseDataFileToDB()
    {
        $hCore = $this->hCore;

        // Erste Reihe muss DB - Feldnamen beinhalten ... ermittel die Feldnamen und speichern
        $this->fetchFieldnamesFromCSV();


        // Datenbank vorhanden Feldnamen einlesen
        $this->fetchDBFieldnames();


        // DB - Felder anlegen?
        $this->checkDBFieldnames();


        // Reudziere auf einen Datensatz pro Kunde
        // $this->OLD_reduceToOneCustomerSet();

        // In Datenbank schreiben
        $this->writeToDB();
    }






    private function OLD_reduceToOneCustomerSet()
    {
        $tmpArray = array();

        // Bool erste Reihe überspringen
        $noFirstRow = true;

        foreach ($this->hCore->gCore['csvValue'] as $cnt=>$customerArray){

            // Erste Reihe überspringen
            if ($noFirstRow){
                $noFirstRow = false;
               // continue;
            }

            $curCustomerNumber  = $this->hCore->gCore['csvValue'][$cnt][2];
            $curEVN             = $this->hCore->gCore['csvValue'][$cnt][32];

            if ( (!isset($tmpArray[$curCustomerNumber][32])) || ($tmpArray[$curCustomerNumber][32] < 1) ){
                $tmpArray[$curCustomerNumber] = $this->hCore->gCore['csvValue'][$cnt];
            }

        }

        $this->hCore->gCore['csvValue'] = '';

        $cnt =0;
        foreach ($tmpArray as $index=>$valueArray){
            $this->hCore->gCore['csvValue'][$cnt] = $valueArray;
            $cnt++;
        }

//        echo "<pre>";
//        print_r($this->hCore->gCore['csvValue']);
//        echo "</pre><br>";
    }




    private function writeToDB()
    {
        $hCore = $this->hCore;

        // Bool erste Reihe überspringen
        $noFirstRow = true;


        // Tabelle leeren!
        $query = "TRUNCATE TABLE `baseDataDimari`";

        // Tabelle leeren!
        $this->gCoreDB->query($query);


        foreach ($this->hCore->gCore['csvValue'] as $cnt=>$customerAarray){

            // Erste Reihe überspringen
            if ($noFirstRow){
                $noFirstRow = false;
                continue;
            }

            $preQuery = "INSERT INTO `baseDataDimari`";

            $midQueryA = " (`userID`, ";
            $midQueryB = ") VALUES ('".$_SESSION['Login']['User']['userID']."', ";

            foreach ($customerAarray as $curIndex=>$curValue){

                // Überspringe DB Felder die nicht import-exportiert werden sollen?
                $calcIndex = $curIndex + $_SESSION['customConfig']['Dimari']['baseDataIndexAdd'];

                if (!$this->hCore->gCore['DBFieldnames'][$calcIndex]){
                    echo "<pre>";
                    echo "FEHERLER curIndex: $curIndex<br>";
                    echo "FEHERLER calcIndex: $calcIndex<br>";
                    echo "DBFieldnames:<br>";
                    print_r($this->hCore->gCore['DBFieldnames']);
                    echo "</pre><br>";
                    exit;
                }
                $curDBFieldname = $this->hCore->gCore['DBFieldnames'][$calcIndex];

                $midQueryA .= "`".$curDBFieldname."`, ";
                $midQueryB .= "'".$curValue."', ";

            }

            $midQueryA = substr($midQueryA, 0, -2);
            $midQueryB = substr($midQueryB, 0, -2);

            $query = $preQuery . $midQueryA . $midQueryB . ')';


            $this->gCoreDB->query($query);

        }

    }







    // Prüft ob DB - Felder angelegt werden müssen ... wenn ja... werde sie hier angelegt
    private function checkDBFieldnames()
    {
        $hCore = $this->hCore;

        $suchmuster = '/(\s)+/';
        $ersetzen = '_';

        foreach ($this->hCore->gCore['CSVFieldnames'] as $curCSVFieldname){

            $curCSVFieldname = preg_replace($suchmuster, $ersetzen, $curCSVFieldname);

            if (!in_array($curCSVFieldname, $this->hCore->gCore['DBFieldnames'])){

                $query = "ALTER TABLE `baseDataDimari` ADD ".$curCSVFieldname." VARCHAR(100)";

                $this->gCoreDB->query($query);
            }
        }

    }






    // Ermittelt die Feldnamen der Datenbank
    private function fetchDBFieldnames()
    {
        $hCore = $this->hCore;

        $query = "SHOW COLUMNS FROM `baseDataDimari`";

        // Resultat der Login - Prüfung
        $result = $this->gCoreDB->query($query);

        // Betroffene Zeilen, bzw. erhaltene
        $num_rows = $this->gCoreDB->num_rows($result);


        // Keine Import Datei gefunden!
        if (!$num_rows >= '1'){

            // Breche Methode hier ab und liefere false - Wert zurück

            RETURN FALSE;
        }

        // Ergebnis in $row speichern
        while ($row = $result->fetch_object()){
            $this->hCore->gCore['DBFieldnames'][] =  $row->Field;;
        }

        $this->gCoreDB->free_result($result);

    }





    // Ermittelt die Feldnamen durch die erste Reihe der CSV - Daten
    private function fetchFieldnamesFromCSV()
    {
        $hCore = $this->hCore;

        $this->hCore->gCore['CSVFieldnames'] = $this->hCore->gCore['csvValue'][0];

        return $this->hCore->gCore['csvValue'][0];

    }




}   // END class DBImportDimari extends Core
