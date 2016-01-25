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





    // INITIAL Daten aufbereiten und in DB speichern
    public function importFileToDB()
    {
        // OBSchnittstelle klassenspezifisch aufrufen
        $this->OBSchnittstelleCentron();

        RETURN TRUE;

    }   // END public function importFileToDB()





    // csv Daten aufbereiten
    private function OBSchnittstelleCentron()
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

            $tilde = '~';

            $csv .= "S~";
            $csv .= $personenkonto . $tilde;    // Personenkonto
            $csv .= $name1 . "~";               // Name1
            $csv .= $name2 . "~";               // Name2
            $csv .= "122800~";                  // Sammelkonto
            $csv .= "SZ~";                      // Zahlungsart
            $csv .= "~";                        // Mandatsreferenznummer
            $csv .= "~";                        // Ländercode
            $csv .= "~";                        // BLZ
            $csv .= "~";                        // BIC
            $csv .= "~";                        // Kontonummer
            $csv .= "~";                        // IBAN
            $csv .= "~";                        // Anrede Brief
            $csv .= "~";                        // Anschrift - Anrede
            $csv .= $anschrifts_name1 . "~";    // Anschrift - Name1
            $csv .= $anschrifts_name2 . "~";    // Anschrift - Name2
            $csv .= "~";                        // Anschrift - Name3
            $csv .= "~";                        // Anschrift - Länderkennzeichen
            $csv .= $PLZ . $tilde;              // Anschrift - PLZ
            $csv .= $Ort . $tilde;              // Anschrift - Ort
            $csv .= $strassenname . "~";        // Anschrift - Straße
            $csv .= $hausnummer . "~";          // Anschrift - Hausnummer
            $csv .= $hausnummerzusatz . "~";    // Zusatzhausnummer
            $csv .= "~";                        // Anschrift - Postfach
            $csv .= "~";                        // Anschrift Name1 abw. Kontoinhaber
            $csv .= "~";                        // Anschrift Name2 abw. Kontoinhaber
            $csv .= "~";                        // Anschrift PLZ abw. Kontoinhaber
            $csv .= "~";                        // Anschrift Ort abw. Kontoinhaber
            $csv .= "~";                        // Anschrift Stra�e abw. Kontoinhaber
            $csv .= "~";                        // Anschrift Hnr abw. Kontoinhaber
            $csv .= "~";                        // Anschrift zus. Hnr abw. Kontoinhaber
            $csv .= $Telefon . $tilde;          // Telefon
            $csv .= "~";                        // Fax
            $csv .= $Email . $tilde;            // Email
            $csv .= "~";                        // Aktennummer
            $csv .= "~";                        // Sortierkennzeichen
            $csv .= "~";                        // EG-Identnummer
            $csv .= "~";                        // Branche
            $csv .= "~";                        // Zahl-bed. Auftr.wes
            $csv .= "~";                        // Preisgruppe Auftr.wes

            $csv .= "\r\n";
        }

        // Prüfsumme
        $csv .= "P~";
        $csv .= $cnt_kunden . "~";
        $csv .= "~"; // Gesamtanzahl der Sätze "A" innerhalb der Datei
        $csv .= "~"; // Gesamtsumme aller Bruttobeträge der Sätze "A"
        $csv .= "~"; // Gesamtanzahl der Sätze "B" innerhalb der Datei
        $csv .= "~"; // Gesamtsumme aller Bruttobeträge der Sätze "B"
        $csv .= "~"; // Gesamtanzahl der Sätze "C" innerhalb der Datei
        $csv .= "~"; // Gesamtsumme aller Nettobeträge der Sätze "A"
        $csv .= "~"; // Gesamtsumme aller Steuerbeträge der Sätze "A"

        $csv .= "\r\n";


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
            $hMessage->storeMessage('ERROR','Export-Datei nicht erstellt! Fehler bei folgenden Kundennummer(n): '.$infoOut);



            // Message Ausgabe vorebeiten
            $hCore->gCore['Messages']['Type'][]      = 'Error';
            $hCore->gCore['Messages']['Code'][]      = 'DBImport';
            $hCore->gCore['Messages']['Headline'][]  = 'Fehler bei: DB - Import <i class="fa fa-arrow-right"></i> '.$typeInfo.' <i class="fa fa-arrow-right"></i> '.$systemInfo;
            $hCore->gCore['Messages']['Message'][]   = 'Fehler bei: DB - Import!<br>Export-Datei nicht erstellt! Fehler bei folgenden Kundennummer(n):<br>'.$infoOut;
        }
        else{

            // TODO Export - Verzeichnis Funktion erstellen (Centron)

            // '/var/www/html/www/uploads/';
            $exportpath = $_SESSION['customConfig']['WebLinks']['MAINUPLOADPATH'];
            $storeFile = $downloadLink . '_exp.csv';
            $newDownloadLink = $_SESSION['customConfig']['WebLinks']['EXTHOMESHORT'].$storeFile;

            $fp = fopen($storeFile, 'w');
            fwrite($fp, $csv);
            fclose($fp);

            // Message Ausgabe vorebeiten
            $hCore->gCore['Messages']['Type'][]      = 'Done';
            $hCore->gCore['Messages']['Code'][]      = 'DBImport';
            $hCore->gCore['Messages']['Headline'][]  = 'DB - Import <i class="fa fa-arrow-right"></i> '.$typeInfo.' <i class="fa fa-arrow-right"></i> '.$systemInfo;
            $hCore->gCore['Messages']['Message'][]   = 'DB - Import erfolgreich!<br>Die Datei kann jetzt <a href="'.$newDownloadLink.'" class="std" target=_blank>HIER</a> heruntergeladen werden!';


            $hCore->gCore['getLeadToBodySite']          = 'includes/html/home/homeBody';    // Webseite die geladen werden soll
        }



        /*
        // Fehler aufgetreten?
        if ( (count($errorArray) > 0) && ($_SESSION['Develop']['ForceExportBaseData'] == 'no') ){

            $infoOut = '';
            foreach ($errorArray as $key){

                foreach ($key as $varname=>$info)
                    $infoOut .= "<br>" . $varname . ": " . $info;

            }
            $hMessage->storeMessage('ERROR','Export-Datei nicht erstellt! Fehler bei folgenden Kundennummer(n): '.$infoOut);
        }
        else{

            // TODO Export - Verzeichnis Funktion erstellen (Centron)

            // '/var/www/html/www/uploads/';
            $exportpath = $_SESSION['CONFIG']['cMainUploadPath'];
            $storeFile = $downloadLink . '_exp.csv';

            $fp = fopen($storeFile, 'w');
            fwrite($fp, $csv);
            fclose($fp);

            $hMessage->storeMessage('DONE','Export-Datei erstellt! Download: <a href="'.$storeFile.'" class="std" target=_blank>HIER</a>!');
        }
        */


        /*
        echo "<pre><hr>";
        print_r($csv);
        echo "<hr>Fehler:<br><br>";
        print_r($errorArray);
        echo "<hr></pre>";
        */
    }   // END private function OBSchnittstelleCentron(...)











}   // END class DBImportCentron extends Core
