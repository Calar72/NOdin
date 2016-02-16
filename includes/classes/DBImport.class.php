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
class DBImport extends Core
{

    public $gDBImport = array();

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





    // INITIAL Daten Importieren
    public function getImports()
    {
        $hCore = $this->hCore;

        // Typ bekannt!
        $req_sourceTypeID   = $hCore->gCore['getGET']['subAction'];

        // System bekannt!
        $req_sourceSystemID = $hCore->gCore['getGET']['valueAction'];


        // Daten einlesen

        $query = "SELECT *
                      FROM `fileUpload`
                        LEFT JOIN `sourceSystem`  ON sourceSystem.sourceSystemID 	= fileUpload.sourceSystemID
                        LEFT JOIN `sourceType` 	  ON sourceType.sourceTypeID 	    = fileUpload.sourceTypeID
                        LEFT JOIN `user`          ON user.userID                    = fileUpload.userID
                      WHERE fileUpload.sourceTypeID     = '".$req_sourceTypeID."'
                        AND fileUpload.sourceSystemID   = '".$req_sourceSystemID."'
                        AND sourceType.active           = 'yes'
                        AND sourceSystem.active         = 'yes'
                      ORDER BY fileUpload.uploadDateTime DESC
                        ";

        // Resultat der Login - Prüfung
        $result = $this->gCoreDB->query($query);

        // Betroffene Zeilen, bzw. erhaltene
        $num_rows = $this->gCoreDB->num_rows($result);


        // Keine Import Datein gefunden!
        if (!$num_rows >= '1'){

            // Breche Methode hier ab und liefere false - Wert zurück

            RETURN FALSE;
        }

        $indexCnt = 0;
        while ($row = $result->fetch_object()){

           $this->hCore->gCore['DBImportFiles'][$indexCnt]['fileUploadID'] = $row->fileUploadID;
           $this->hCore->gCore['DBImportFiles'][$indexCnt]['fileOriginName'] = $row->fileOriginName;
           $this->hCore->gCore['DBImportFiles'][$indexCnt]['uploadDateTime'] = $row->uploadDateTime;
           $this->hCore->gCore['DBImportFiles'][$indexCnt]['fileSize'] = $this->formatSizeUnits($row->fileSize);
           $this->hCore->gCore['DBImportFiles'][$indexCnt]['userName'] = $row->userName;
           $this->hCore->gCore['DBImportFiles'][$indexCnt]['importCounter'] = $row->importCounter;
           $this->hCore->gCore['DBImportFiles'][$indexCnt]['downloadLink'] = $row->downloadLink;

            $indexCnt++;
        }

        $this->gCoreDB->free_result($result);


        RETURN TRUE;

    }   // END public function getImports()





    // INITIAL Datei in DB importieren
    public function dbImportPerformImport()
    {

        $hCore = $this->hCore;

        // Daten (Ort auf Server usw.) der zu importierenden Datei ermitteln
        $query = "SELECT *
                    FROM `fileUpload`
                    WHERE fileUploadID = '".$hCore->gCore['getPOST']['sel_fileUploadID']."'
                    LIMIT 1";

        // Resultat der Login - Prüfung
        $result = $this->gCoreDB->query($query);

        // Betroffene Zeilen, bzw. erhaltene
        $num_rows = $this->gCoreDB->num_rows($result);


        // Keine Import Datei gefunden!
        if (!$num_rows == '1'){

            // Breche Methode hier ab und liefere false - Wert zurück

            RETURN FALSE;
        }

        // Ergebnis in $row speichern
        $row = $result->fetch_object();

        $this->hCore->gCore['curDownloadLink'] 	    = $row->downloadLink;       // Link zur gewählten Datei
        $this->hCore->gCore['curSourceTypeID']      = $row->sourceTypeID;       // IDt = ID Type    (Stammdaten, Buchungssatz)
        $this->hCore->gCore['curSourceSystemID']    = $row->sourceSystemID;     // IDs = ID System  (Diamri, Centron usw)

        // Datei öffnen und via getcsv in Array speichern
        $filepath = $row->fileTargetFullPath;

        //$Data = array_map('str_getcsv', file($filepath));
        // $Data = file($filepath);
        $preData = file($filepath);

        foreach ($preData as $newLine){
            $myNewData[][0] = $newLine;
        }
        $Data = $myNewData;


        // Centron Buchungsdaten?
        if ( ($this->hCore->gCore['curSourceTypeID'] == '2') && ($this->hCore->gCore['curSourceSystemID'] == '2') ){
            $newData = array();
            foreach ($Data as $value){
               $newData[][0] = trim($value[0]) . ',' . trim($value[1]);
            }

            $Data = $newData;
        }


        foreach ($Data as $index=>$row){

            // Centron Buchungsdaten?
            if ( ($this->hCore->gCore['curSourceTypeID'] == '2') && ($this->hCore->gCore['curSourceSystemID'] == '2') ){
                $eachValueArray = str_getcsv($row[0], "\t");
            }
            else{
                $eachValueArray = str_getcsv($row[0], ";");
            }

            $myData[$index] = $eachValueArray;
        }

        // Speichere csv - Daten zur weiteren Verarbeitung in der globalen - Klassen - Variable
        $this->hCore->gCore['csvValue'] = $myData;


        // Rufe Schnittstellen - Controller auf... in dem wird zwischen den verschiedenen Systemen unterschieden
        $this->OBSchnittstellenController();


        // Gebe DB - Speicher wieder frei
        $this->gCoreDB->free_result($result);


        RETURN TRUE;

    }   // END public function dbImportPerformImport()





    // OBSchnittstellen - Controller
    // Hier wird zwischen Systemen und Typen unterschieden, entsprechend werden weitere Methoden hier aufgerufen
    private function OBSchnittstellenController()
    {

        $hCore = $this->hCore;

        // Stammdaten
        if ($this->hCore->gCore['curSourceTypeID'] == '1'){

            // Dimari - System?
            if ($this->hCore->gCore['curSourceSystemID'] == '1'){
                //TODO Dimari Stammdaten - Import
                $hDBImport = new DBImportDimari($hCore);
                $hDBImport->importBaseDataFileToDB();
            }

            // Centron - System?
            elseif ($this->hCore->gCore['curSourceSystemID'] == '2'){
                //TODO Centron Stammdaten - Import
                $hDBImport = new DBImportCentron($hCore);
                $hDBImport->importBaseDataFileToDB();
            }

            // Webfakt - System?
            elseif ($this->hCore->gCore['curSourceSystemID'] == '3'){
                //TODO Centron Stammdaten - Import
            }

        }


        // Buchnungssatz
        elseif ($this->hCore->gCore['curSourceTypeID'] == '2'){

            // Dimari - System?
            if ($this->hCore->gCore['curSourceSystemID'] == '1'){
                //TODO Dimari Buchungssatz - Import
            }

            // Centron - System?
            elseif ($this->hCore->gCore['curSourceSystemID'] == '2'){
                //TODO Centron Buchungssatz - Import
                $hDBImport = new DBImportCentron($hCore);
                $hDBImport->importBookingDataFileToDB();
            }

            // Webfakt - System?
            elseif ($this->hCore->gCore['curSourceSystemID'] == '3'){
                //TODO Centron Buchungssatz - Import
            }
        }

        RETURN TRUE;

    }   // END private function OBSchnittstellenController()






}   // END class DBImport extends Core
