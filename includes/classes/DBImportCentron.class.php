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
class DBImportCentron extends Core
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





    // INITIAL Daten (Stammdaten) aufbereiten und in DB speichern
    public function importBaseDataFileToDB()
    {
        // OBSchnittstelle klassenspezifisch aufrufen
        $this->OBSchnittstelleBaseDataCentron();

        RETURN TRUE;

    }   // END public function importFileToDB()





    // INITIAL Daten (Buchungssatz) aufbereiten und in DB speichern
    public function importBookingDataFileToDB()
    {
        // OBSchnittstelle klassenspezifisch aufrufen
        $this->OBSchnittstelleBookingDataCentron();

        RETURN TRUE;

    }   // END public function importBookingDataFileToDB()





    // csv Daten aufbereiten
    private function OBSchnittstelleBaseDataCentron()
    {

        $hCore = $this->hCore;

        $hDB            = '';
        $hMessage       = '';
        $zeilen         = $hCore->gCore['csvValue'];
        $downloadLink   = $hCore->gCore['curDownloadLink'];
        $IDt            = $hCore->gCore['curSourceTypeID'];
        $IDs            = $hCore->gCore['curSourceSystemID'];

        $csv = "";

        $cnt_kunden = 0;

        // Die erste Reihe in der .csv - Datei ist eine "Ueberschrift"?
        $skipHeadline = false;

        $errorArray = array();


        ////////////////////////////////////////////////////////////////////
        foreach ($zeilen as $kunde){
            $daten['errorArray']['Kd.-Nr.'] = array();

            // Headline in Rohdatei? Wenn ja, überspringe ich die erste Zeile
            if ( ($skipHeadline) && ($cnt_kunden == 0) ){
                $skipHeadline= false;
                continue;
            }

            $cnt_kunden++;

            if (trim($kunde[0]) == ""){
                continue;
            }



            // Strassenstring auseinandernehmen
            if (!isset($kunde[3])){
                $strassenname = 'unset';
                $hausnummer = 'unset';
                $daten['errorArray']['Kd.-Nr.'] = $kunde[0];
            }
            else {
                $strassenname = trim($kunde[3]);
                $hausnummer = "";
                if ( preg_match('/([^\d]+)\s?(.+)/i', $strassenname, $result)) {
                    $strassenname = trim($result[1]);
                    $hausnummer = trim($result[2]);
                }
            }


            if (!isset($kunde[4])){
                $PLZ = 'unset';
                $daten['errorArray']['Kd.-Nr.'] = $kunde[0];
            }
            else {
                $PLZ = trim($kunde[4]);
            }


            if (!isset($kunde[5])){
                $Ort = 'unset';
                $daten['errorArray']['Kd.-Nr.'] = $kunde[0];
            }
            else {
                $Ort = trim($kunde[5]);
            }


            if (!isset($kunde[6])){
                $Telefon = 'unset';
                $daten['errorArray']['Kd.-Nr.'] = $kunde[0];
            }
            else {
                $Telefon = trim($kunde[6]);
            }


            if (!isset($kunde[7])){
                $Email = 'unset';
                $daten['errorArray']['Kd.-Nr.'] = $kunde[0];
            }
            else {
                $Email = trim($kunde[7]);
            }


            // Hausnummernzusatz
            $hausnummerzusatz = "";

            $matches = array();
            preg_match('/(\d+)(.*?)/', $hausnummer, $matches);
            if (isset($matches[0]) and (strlen($matches[0]) != strlen($hausnummer))) {
                $hausnummerzusatz = trim(str_replace($matches[0], "", $hausnummer));
                $hausnummer = $matches[0];
            }


            $name1 = trim($kunde[1]);
            $name2 = "";
            if (strlen($name1) > 30) {
                $name2 = substr($name1, 29);
                $name1 = substr($name1, 0, 30);
            }


            // Anschriftsname
            $anschrifts_name1 = trim($kunde[1]);
            $anschrifts_name2 = "";
            if (strlen($anschrifts_name1) > 35) {
                $anschrifts_name2 = substr($anschrifts_name1, 34);
                $anschrifts_name1 = substr($anschrifts_name1, 0, 35);
            }


            $name1 = trim($kunde[1]);
            $name2 = "";
            if (strlen($name1) > 30) {
                $name2 = substr($name1, 29);
                $name1 = substr($name1, 0, 30);
            }

            if (count($daten['errorArray']['Kd.-Nr.']) > 0){
                $errorArray[] = $daten['errorArray'];
            }


            $personenkonto 	= trim($kunde[0]);	// Personenkonto sprich Kundennummer

            $dynInsertQuery = "(
                                `userID`,
                                `Personenkonto`,
                                `Name1`,
                                `Name2`,
                                `Sammelkonto`,
                                `Zahlungsart`,
                                `Anschrift_Name1`,
                                `Anschrift_Name2`,
                                `Anschrift_PLZ`,
                                `Anschrift_Ort`,
                                `Anschrift_Strasse`,
                                `Anschrift_Hausnummer`,
                                `Zusatzhausnummer`,
                                `Telefon`,
                                `Email`
                                ) VALUES (
                                '".$_SESSION['Login']['User']['userID']."',
                                '".$personenkonto."',
                                '".$name1."',
                                '".$name2."',
                                '".$_SESSION['customConfig']['Centron']['Sammelkonto']."',
                                '".$_SESSION['customConfig']['Centron']['Zahlungsart']."',
                                '".$anschrifts_name1."',
                                '".$anschrifts_name2."',
                                '".$PLZ."',
                                '".$Ort."',
                                '".$strassenname."',
                                '".$hausnummer."',
                                '".$hausnummerzusatz."',
                                '".$Telefon."',
                                '".$Email."'
                                )
                                ";

            $dynUpdateQuery = "`userID`                 = '".$_SESSION['Login']['User']['userID']."',
                               `Name1`                  = '".$name1."',
                               `Name2`                  = '".$name2."',
                               `Sammelkonto`            = '".$_SESSION['customConfig']['Centron']['Sammelkonto']."',
                               `Zahlungsart`            = '".$_SESSION['customConfig']['Centron']['Zahlungsart']."',
                               `Anschrift_Name1`        = '".$anschrifts_name1."',
                               `Anschrift_Name2`        = '".$anschrifts_name2."',
                               `Anschrift_PLZ`          = '".$PLZ."',
                               `Anschrift_Ort`          = '".$Ort."',
                               `Anschrift_Strasse`      = '".$strassenname."',
                               `Anschrift_Hausnummer`   = '".$hausnummer."',
                               `Zusatzhausnummer`       = '".$hausnummerzusatz."',
                               `Telefon`                = '".$Telefon."',
                               `Email`                  = '".$Email."'
            ";

            // DB Eintrag erstellen oder Updaten (Query erstellen)!
            $query = "INSERT INTO baseDataCentron ".$dynInsertQuery." ON DUPLICATE KEY UPDATE ".$dynUpdateQuery;

            // DB Eintrag erstellen oder Updaten!
            $this->gCoreDB->query($query);

        }   // END foreach ($zeilen as $kunde){


        // Informationen aufbereiten
        $typeIndex = array_search($hCore->gCore['curSourceTypeID'], $hCore->gCore['LNav']['ConvertTypeID']);
        $typeInfo = $hCore->gCore['LNav']['ConvertType'][$typeIndex];

        $systemIndex = array_search($hCore->gCore['curSourceSystemID'], $hCore->gCore['LNav']['ConvertSystemID']);
        $systemInfo = $hCore->gCore['LNav']['ConvertSystem'][$systemIndex];



        // Fehler aufgetreten?
        if (count($errorArray) > 0){

            $infoOut = '';
            foreach ($errorArray as $key){

                foreach ($key as $varname=>$info)
                    $infoOut .= "<br>" . $varname . ": " . $info;

            }

            // Message Ausgabe vorebeiten
            $hCore->gCore['Messages']['Type'][]      = 'Error';
            $hCore->gCore['Messages']['Code'][]      = 'DBImport';
            $hCore->gCore['Messages']['Headline'][]  = 'Fehler bei: DB - Import <i class="fa fa-arrow-right"></i> '.$typeInfo.' <i class="fa fa-arrow-right"></i> '.$systemInfo;
            $hCore->gCore['Messages']['Message'][]   = 'Fehler bei: DB - Import!<br>Export-Datei nicht erstellt! Fehler bei folgenden Kundennummer(n):<br>'.$infoOut;
        }
        else{

            // Import Counter aktuallisieren
            $query = "UPDATE fileUpload SET importCounter = importCounter+1 WHERE fileUploadID = '".$hCore->gCore['getPOST']['sel_fileUploadID']."' LIMIT 1";
            $this->gCoreDB->query($query);

            // Message Ausgabe vorebeiten
            $hCore->gCore['Messages']['Type'][]      = 'Done';
            $hCore->gCore['Messages']['Code'][]      = 'DBImport';
            $hCore->gCore['Messages']['Headline'][]  = 'DB - Import <i class="fa fa-arrow-right"></i> '.$typeInfo.' <i class="fa fa-arrow-right"></i> '.$systemInfo;
            $hCore->gCore['Messages']['Message'][]   = 'DB - Import erfolgreich!<br>Die Datei kann jetzt über "DB - Export" exportiert werden!';

            $hCore->gCore['getLeadToBodySite']          = 'includes/html/home/homeBody';    // Webseite die geladen werden soll
        }


        RETURN TRUE;

    }   // END private function OBSchnittstelleCentron()








    /////////////////////////////////// Buchungssatz //////////////////////////////////





    // Importiert .txt Buchungsdatei in DB
    private function OBSchnittstelleBookingDataCentron()
    {
        $hCore = $this->hCore;

        $zeilen         = $hCore->gCore['csvValue'];

        foreach ($zeilen as $bookingSet){

            preg_match_all("/(\d+)\.(\d+)\.(\d+)/i", trim($bookingSet[0]), $splitDate);

            $Datum          = '20' . $splitDate[3][0] . '-' . $splitDate[2][0] . '-' . $splitDate[1][0];
            $RechnungsNr    = trim($bookingSet[1]);
            $Buchungstext   = trim($bookingSet[2]);
            $Erloeskonto    = trim($bookingSet[3]);
            $KundenNummer   = trim($bookingSet[4]);
            $Brutto         = trim($bookingSet[5]);
            $MwSt           = trim($bookingSet[6]);
            $Kostenstelle   = trim($bookingSet[7]);

            if (strlen($RechnungsNr) < 1){
                continue;
            }

            // NULL - Werte abfangen
            if (strlen($Datum) < 1) { $Datum = '0000-00-00'; }
            if (strlen($Buchungstext) < 1) { $Buchungstext = '0'; }
            if (strlen($Erloeskonto) < 1) { $Erloeskonto = '0'; }
            if (strlen($KundenNummer) < 1) { $KundenNummer = '0'; }
            if (strlen($Brutto) < 1) { $Brutto = '0'; }
            if (strlen($MwSt) < 1) { $MwSt = '0'; }
            if (strlen($Kostenstelle) < 1) { $Kostenstelle = '0'; }


            // Zeit jetzt
            $curTime = date("Y-m-d H:i:s");

            // DB Einträge erstellen
            $query = "INSERT INTO bookingDataCentron (
                                                      `importDate`,
                                                      `userID`,
                                                      `Datum`,
                                                      `RechnungsNr`,
                                                      `Buchungstext`,
                                                      `Erloeskonto`,
                                                      `KundenNummer`,
                                                      `Brutto`,
                                                      `MwSt`,
                                                      `Kostenstelle`
                                                      ) VALUES (
                                                      '".$curTime."',
                                                      '".$_SESSION['Login']['User']['userID']."',
                                                      '".$Datum."',
                                                      '".$RechnungsNr."',
                                                      '".$Buchungstext."',
                                                      '".$Erloeskonto."',
                                                      '".$KundenNummer."',
                                                      '".$Brutto."',
                                                      '".$MwSt."',
                                                      '".$Kostenstelle."'
                                                      )";

            // DB Eintrag erstellen!
            $this->gCoreDB->query($query);


        }   // END foreach ($zeilen as $bookingSet){


        // Import Counter aktuallisieren
        $query = "UPDATE fileUpload SET importCounter = importCounter+1 WHERE fileUploadID = '".$hCore->gCore['getPOST']['sel_fileUploadID']."' LIMIT 1";
        $this->gCoreDB->query($query);


        // Informationen aufbereiten
        $typeIndex = array_search($hCore->gCore['curSourceTypeID'], $hCore->gCore['LNav']['ConvertTypeID']);
        $typeInfo = $hCore->gCore['LNav']['ConvertType'][$typeIndex];

        $systemIndex = array_search($hCore->gCore['curSourceSystemID'], $hCore->gCore['LNav']['ConvertSystemID']);
        $systemInfo = $hCore->gCore['LNav']['ConvertSystem'][$systemIndex];

        // Message Ausgabe vorebeiten
        $hCore->gCore['Messages']['Type'][]      = 'Done';
        $hCore->gCore['Messages']['Code'][]      = 'DBImport';
        $hCore->gCore['Messages']['Headline'][]  = 'DB - Import <i class="fa fa-arrow-right"></i> '.$typeInfo.' <i class="fa fa-arrow-right"></i> '.$systemInfo;
        $hCore->gCore['Messages']['Message'][]   = 'DB - Import erfolgreich!<br>Die Datei kann jetzt über "DB - Export" exportiert werden!';

        $hCore->gCore['getLeadToBodySite']          = 'includes/html/home/homeBody';    // Webseite die geladen werden soll

        RETURN TRUE;

    }   // END private function OBSchnittstelleBookingDataCentron()







}   // END class DBImportCentron extends Core
