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
class DBExport extends Core
{

    public $gDBExport = array();

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




    public function getExportsBaseDataCentron()
    {
        $hCore = $this->hCore;

        // Typ bekannt!
        $req_sourceTypeID   = $hCore->gCore['getGET']['subAction'];

        // System bekannt!
        $req_sourceSystemID = $hCore->gCore['getGET']['valueAction'];

        // Daten einlesen

        // Summe der Datensätze
        $query = "SELECT COUNT(*) AS sumBaseData FROM baseDataCentron WHERE 1";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows == '1'){
            $getSumBaseData = 0;
        }
        else{
            $row = $result->fetch_object();
            $getSumBaseData = $row->sumBaseData;
        }
        $hCore->gCore['baseDataInfo']['getSumBaseData'] = $getSumBaseData;
        $this->gCoreDB->free_result($result);



        // Ältester Datensatz
        $query = "SELECT lastUpdate FROM baseDataCentron WHERE 1 ORDER BY lastUpdate ASC LIMIT 1";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows == '1'){
            $getOldestBaseData = 0;
        }
        else{
            $row = $result->fetch_object();
            $getOldestBaseData = $row->lastUpdate;
        }
        $hCore->gCore['baseDataInfo']['getOldestBaseData'] = $getOldestBaseData;
        $this->gCoreDB->free_result($result);



        // Aktuellste Datensatz
        $query = "SELECT lastUpdate FROM baseDataCentron WHERE 1 ORDER BY lastUpdate DESC LIMIT 1";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows == '1'){
            $getNewestBaseData = 0;
        }
        else{
            $row = $result->fetch_object();
            $getNewestBaseData = $row->lastUpdate;
        }
        $hCore->gCore['baseDataInfo']['getNewestBaseData'] = $getNewestBaseData;
        $this->gCoreDB->free_result($result);



        // Benutzer
        $query = "SELECT userName FROM user u, baseDataCentron as b WHERE u.userID = b.userID GROUP BY u.userID";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            $userNames[] = '';
        }
        else{
            while($row = $result->fetch_object()){
                $userNames[] = $row->userName;
            }
        }
        $hCore->gCore['baseDataInfo']['userNames'] = $userNames;
        $this->gCoreDB->free_result($result);




        // Sammelkonten
        $query = "SELECT Sammelkonto FROM baseDataCentron WHERE 1 GROUP BY Sammelkonto";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            $Sammelkonten[] = '';
        }
        else{
            while($row = $result->fetch_object()){
                $Sammelkonten[] = $row->Sammelkonto;
            }
        }
        $hCore->gCore['baseDataInfo']['Sammelkonten'] = $Sammelkonten;
        $this->gCoreDB->free_result($result);




        // Zahlungsart
        $query = "SELECT Zahlungsart FROM baseDataCentron WHERE 1 GROUP BY Zahlungsart";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            $Zahlungsarten[] = '';
        }
        else{
            while($row = $result->fetch_object()){
                $Zahlungsarten[] = $row->Zahlungsart;
            }
        }
        $hCore->gCore['baseDataInfo']['Zahlungsarten'] = $Zahlungsarten;
        $this->gCoreDB->free_result($result);

        RETURN TRUE;
    }













    public function doExportsBaseDataCentron()
    {
        $hCore = $this->hCore;

        // Feldnamen einlesen
        $query = "SHOW COLUMNS FROM baseDataCentron";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            RETURN FALSE;
        }

        while($row = $result->fetch_object()) {
            $dbFieldnames[] = $row->Field;
        }
        $this->gCoreDB->free_result($result);






        // Stammdaten einlesen
        $query = "SELECT * FROM baseDataCentron ORDER BY Name1, Name2";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            RETURN FALSE;
        }


        $cntIndex = 0;
        while($row = $result->fetch_object()) {

            foreach ($dbFieldnames as $curFieldname){
                $hCore->gCore['csvDaten'][$cntIndex][$curFieldname] = $row->$curFieldname;  // Automatisch die Feldnamen als Variable benutzen
            }
            $cntIndex++;
        }
        $this->gCoreDB->free_result($result);





        $csv = "";

        $cnt_kunden = 0;

        foreach ($hCore->gCore['csvDaten'] as $kunde){

            $cnt_kunden++;

            $personenkonto 	= trim($kunde['Personenkonto']);	// Personenkonto sprich Kundennummer

            $tilde = '~';

            $csv .= "S~";
            $csv .= $personenkonto . $tilde;    // Personenkonto
            $csv .= $kunde['Name1'] . "~";               // Name1
            $csv .= $kunde['Name2'] . "~";               // Name2
            $csv .= $kunde['Sammelkonto']. "~";                  // Sammelkonto
            $csv .= $kunde['Zahlungsart']. "~";                      // Zahlungsart
            $csv .= "~";                        // Mandatsreferenznummer
            $csv .= "~";                        // Ländercode
            $csv .= "~";                        // BLZ
            $csv .= "~";                        // BIC
            $csv .= "~";                        // Kontonummer
            $csv .= "~";                        // IBAN
            $csv .= "~";                        // Anrede Brief
            $csv .= "~";                        // Anschrift - Anrede
            $csv .= $kunde['Anschrift_Name1'] . "~";    // Anschrift - Name1
            $csv .= $kunde['Anschrift_Name2'] . "~";    // Anschrift - Name2
            $csv .= "~";                        // Anschrift - Name3
            $csv .= "~";                        // Anschrift - Länderkennzeichen
            $csv .= $kunde['Anschrift_PLZ'] . $tilde;              // Anschrift - PLZ
            $csv .= $kunde['Anschrift_Ort'] . $tilde;              // Anschrift - Ort
            $csv .= $kunde['Anschrift_Strasse'] . "~";        // Anschrift - Straße
            $csv .= $kunde['Anschrift_Hausnummer'] . "~";          // Anschrift - Hausnummer
            $csv .= $kunde['Zusatzhausnummer'] . "~";    // Zusatzhausnummer
            $csv .= "~";                        // Anschrift - Postfach
            $csv .= "~";                        // Anschrift Name1 abw. Kontoinhaber
            $csv .= "~";                        // Anschrift Name2 abw. Kontoinhaber
            $csv .= "~";                        // Anschrift PLZ abw. Kontoinhaber
            $csv .= "~";                        // Anschrift Ort abw. Kontoinhaber
            $csv .= "~";                        // Anschrift Stra�e abw. Kontoinhaber
            $csv .= "~";                        // Anschrift Hnr abw. Kontoinhaber
            $csv .= "~";                        // Anschrift zus. Hnr abw. Kontoinhaber
            $csv .= $kunde['Telefon'] . $tilde;          // Telefon
            $csv .= "~";                        // Fax
            $csv .= $kunde['Email'] . $tilde;            // Email
            $csv .= "~";                        // Aktennummer
            $csv .= "~";                        // Sortierkennzeichen
            $csv .= "~";                        // EG-Identnummer
            $csv .= "~";                        // Branche
            $csv .= "~";                        // Zahl-bed. Auftr.wes
            $csv .= "~";                        // Preisgruppe Auftr.wes

            $csv .= "\r\n";

        }   // END foreach ($hCore->gCore['csvDaten'] as $kunde){

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
        $typeIndex = array_search($hCore->gCore['getGET']['subAction'], $hCore->gCore['LNav']['ConvertTypeID']);
        $typeInfo = $hCore->gCore['LNav']['ConvertType'][$typeIndex];

        $systemIndex = array_search($hCore->gCore['getGET']['valueAction'], $hCore->gCore['LNav']['ConvertSystemID']);
        $systemInfo = $hCore->gCore['LNav']['ConvertSystem'][$systemIndex];


        // TODO Export - Verzeichnis Funktion erstellen (Centron)

        $downloadLink = 'CentronStammdatenExport';

        // '/var/www/html/www/uploads/';
        $exportpath = $_SESSION['customConfig']['WebLinks']['MAINUPLOADPATH'];
        $storeFile = 'uploads/' . $downloadLink . '_exp.csv';
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


        RETURN TRUE;

    }   // END public function doExportsBaseDataCentron()









    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // INITIAL Methode: Export Centron Buchungsdaten
    public function getExportsBookingDataCentronInitial()
    {

        $hCore = $this->hCore;

        // Initial Aufruf für Centron Buchungssatz export
        $this->doExportsBookingDataCentronInitial();


        RETURN TRUE;

    }   // END public function getExportsBookingDataCentronInitial()







    private function doExportsBookingDataCentronInitial()
    {

        $hCore = $this->hCore;

        // Benötigte Kundendaten anhand der anstehenden Rechnungen und KundenNr. ermitteln
        $this->getRelevantBaseData();

        // Buchungssatz einlesen
        $this->getBookingData();

        // Kundendaten zu Buchungssatz einlesen

        // A B C Stamm aufbauen
        $this->generateSets();

        // TODO HIER!
        // csv-Datei erstellen
        $this->generateBooginCSV();

        RETURN TRUE;

    }   // END private function doExportsBookingDataCentronInitial()






    // Benötigte Kundendaten anhand der anstehenden Rechnungen und KundenNr. ermitteln
    private function getRelevantBaseData()
    {
        $hCore = $this->hCore;

        // Feldnamen einlesen
        $query = "SHOW COLUMNS FROM baseDataCentron";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            RETURN FALSE;
        }

        while($row = $result->fetch_object()) {
            $dbFieldnames[] = $row->Field;
        }
        $this->gCoreDB->free_result($result);




        $query = "SELECT c.*
                    FROM bookingDataCentron as r,
                         baseDataCentron as c
                    WHERE c.Personenkonto = r.KundenNummer
                    GROUP BY r.KundenNummer
                    ORDER BY r.KundenNummer";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            RETURN FALSE;
        }


        $cntIndex = 0;
        while($row = $result->fetch_object()) {

            foreach ($dbFieldnames as $curFieldname){
                $hCore->gCore['CustomerData'][$row->Personenkonto][$curFieldname] = $row->$curFieldname;  // Automatisch die Feldnamen als Variable benutzen
            }
            $cntIndex++;
        }

        $this->gCoreDB->free_result($result);

        RETURN TRUE;

    }   // END private function getBookingData()








    // Buchungssatz einlesen
    private function getBookingData()
    {
        $hCore = $this->hCore;


        // Feldnamen einlesen
        $query = "SHOW COLUMNS FROM bookingDataCentron";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            RETURN FALSE;
        }

        while($row = $result->fetch_object()) {
            $dbFieldnames[] = $row->Field;
        }
        $this->gCoreDB->free_result($result);





        // Buchungssatz einlesen
        $query = "SELECT * FROM bookingDataCentron WHERE 1 ORDER BY KundenNummer, RechnungsNr";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            RETURN FALSE;
        }


        $cntIndex = 0;
        while($row = $result->fetch_object()) {

            foreach ($dbFieldnames as $curFieldname){
                $hCore->gCore['BuchungsDaten'][$cntIndex][$curFieldname] = $row->$curFieldname;  // Automatisch die Feldnamen als Variable benutzen
            }
            $cntIndex++;
        }
        $this->gCoreDB->free_result($result);


        RETURN TRUE;

    }   // END private function getBookingData()







    private function generateSets()
    {

        $hCore = $this->hCore;

        // Initial Variable definieren
        $lastCustomerNumber = 0;
        $lastBookingNumber = 0;

        $curIndex = 0;
        $curIndexC = 0;
        $curIndexB = 0;
        $curIndexA = 0;

        $mainArrayIndex = 0;


        $indexMain = 0;
        $indexA = 0;
        $indexB = 0;
        $indexC =0;

        $setCnt =0;

        foreach ($hCore->gCore['BuchungsDaten'] as $bookingSet){


            $curCustomerNumber = $bookingSet['KundenNummer'];
            $curBookingNumber = $bookingSet['RechnungsNr'];


            // Wenn die aktuelle KundenNR. nicht der vorherigen entspricht, haben wir einen neuen Satz A

            // Wenn die aktuelle RechnungsNr. nicht der vorherigen entspricht, haben wir einen neuen Satz B

            // ... Satz C (Also Detail) anfügen





            // A Satz
            if ($curCustomerNumber != $lastCustomerNumber){

                $indexB = 0;


                //2016-01-12
                preg_match_all("/(\d+)\-(\d+)\-(\d+)/i", $bookingSet['Datum'], $splitDate);
                $curBuchungsperiode = $splitDate[1][0] . '.' . $splitDate[2][0];
                $curBelegdatum      = $splitDate[1][0] . $splitDate[2][0]. $splitDate[3][0];

                $curBuchungsdatum = $curBelegdatum;

                // $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']][''];


                if (!isset($hCore->gCore['CustomerData'][$bookingSet['KundenNummer']])){
                    // Habe den Stammdatensatz nicht!
                    // Message Ausgabe vorebeiten
                    $hCore->gCore['Messages']['Type'][]      = 'Fehler';
                    $hCore->gCore['Messages']['Code'][]      = 'Error';
                    $hCore->gCore['Messages']['Headline'][]  = 'DB - Exort';
                    $hCore->gCore['Messages']['Message'][]   = 'FEHLER: fehlender Stammdatensat KDNr.: ' . $bookingSet['KundenNummer'] . '<br>';
                    continue;
                }


                // A Satz hinzufügen
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Satzart']                    = 'A';                                      // Satzart

                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Personenkonto']              = $bookingSet['KundenNummer'];              // Personenkonto

                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Belegnummer']                = $bookingSet['KundenNummer'];              // Belegnummer

                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Rechnungsnummer']            = sprintf("%'.012d", $bookingSet['bookingDataCentronID']);      // Rechnungsnummer

                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Buchungsperiode']            = $curBuchungsperiode;                      // Buchungsperiode

                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Belegdatum']                 = $curBelegdatum;                           // Belegdatum

                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Buchungsdatum']              = $curBuchungsdatum;                        // Buchungsdatum

                // PFLICHT (Wird im B - Teil behandelt und gesetzt)
//                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Bruttobetrag'] = ''; // Bruttobetrag

                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Waehrung']                   = $_SESSION['customConfig']['Centron']['Waehrung'];   // Waehrung

                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Skonto']                     = '';    // Skontofähiger Betrag

                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Zahlungsbedingungen']        = '';    // Zahlungsbedingungen

                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['ABWZahlungsart']             = '';   // Abweichende Zahlungsart
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Faelligkeit']                = '';   // Fälligkeit
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Valuta']                     = '';   // Valuta
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['PLZ']                        = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_PLZ'];      // PLZ
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Ort']                        = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Ort'];   // Ort
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Strasse']                    = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Strasse'];   // Strasse
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Hausnummer']                 = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Hausnummer'];   // Hausnummer
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Zusatzhausnummer']           = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Zusatzhausnummer'];   // Zusatzhausnummer
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Wohnungsnummer']             = '';   // Wohnungsnummer
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['ABWKontoInhaberName1']       = '';   // Abweichen-der-Kontoinhaber_Name1
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['ABWKontoInhaberName2']       = '';   // Abweichen-der-Kontoinhaber_Name2
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Laendercode']                = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Laendercode'];   // Laendercode
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['ABWBLZ']                     = '';   // BLZ abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['ABWKontoNr']                 = '';   // Konto_Nr abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['ABWIBAN']                    = '';   // IBAN abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['ABWAnschriftName1']          = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Name1_abw_Kontoinhaber'];   // Anschrift - Name 1 abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['ABWAnschriftName2']          = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Name2_abw_Kontoinhaber'];   // Anschrift - Name 2 abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['ABWAnschriftPLZ']            = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_PLZ_abw_Kontoinhaber'];   // Anschrift - PLZ abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['ABWAnschriftOrt']            = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Ort_abw_Kontoinhaber'];   // Anschrift - Ort abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['ABWAnschriftStrasse']        = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Strasse_abw_Kontoinhaber'];   // Anschrift - Strasse abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['ABWAnschriftHausNr']         = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Hnr_abw_Kontoinhaber'];   // Anschrift - HausNr. abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['ABWAnschriftHausNrZusatz']   = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_zus_Hnr_abw_Kontoinhaber'];   // Anschrift - Zus. HausNr. abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Prenotifcation']             = 'j';  // Prenotification erfolgt (J)
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['MandatsRefNr']               = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Mandatsreferenznummer'];   // Mandatsreferenz-nummer
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['AnkZahlungseinzgZum']        = '';   // Anküendigung des Zahlungseinzugs zum
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['AnkZahlungseinzgAm']         = '';   // Ankündigung des Zahlungseinzugs am
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['ABWAnkZahlungseinzg']        = '';   // Ankündigung des Zahlunseinzugs am für den abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['BuchungszeichenAvviso']      = '';   // Buchungszeichen Avviso
            }











            // B Satz hinzufügen
            if ($curBookingNumber != $lastBookingNumber){


                // B Satz hinzufügen
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['BSetArray'][$indexB]['Satzart']              = 'B';                            // Satzart
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['BSetArray'][$indexB]['Bruttobetrag']         = $bookingSet['Brutto'];   // Bruttobetrag
                //$hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['BSetArray'][$indexB]['Nettobetrag']        = '';                       // Nettobetrag
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['BSetArray'][$indexB]['Steuerkennzeichen']    = $bookingSet['MwSt'];      // Steuerkennzeichen
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['BSetArray'][$indexB]['Geschaeftsbereich']    = $_SESSION['customConfig']['Centron']['GeschaeftsbereichNonPrivate'];    // Geschäftsbereich
                $indexC = 0;


                // A Brutto Betrag
                if (isset($hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Bruttobetrag'])){
                    $curABrutto = $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Bruttobetrag'];
                }
                else{
                    $curABrutto = 0;
                }
                $curNewABrutto = $curABrutto + $bookingSet['Brutto'];
                $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['Bruttobetrag'] = $curNewABrutto;

            }












            // C Satz
            // C Satz hinzufügen

            $a = $bookingSet['Brutto'];
            $b = $bookingSet['MwSt'];
            $curCSteuerbetrag =  $a * ($b/100);
            $curCNetto = $a - $curCSteuerbetrag;

            $curBNetto = 0;
            if (isset($hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['BSetArray'][$indexB]['Nettobetrag'])){
                $curBNetto = $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['BSetArray'][$indexB]['Nettobetrag'];
            }
            $curNewNetto = $curBNetto + $curCNetto;

            // B Brutto Betrag:
            $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['BSetArray'][$indexB]['Nettobetrag'] = $curNewNetto;

            $info = "Brutto: ".$a . " Netto: " . $curCNetto . " Betrag: " . $curCSteuerbetrag;
            $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['BSetArray'][$indexB]['CSetArray'][$indexC]['Satzart']        = 'C';                                 // Satzart
            $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['BSetArray'][$indexB]['CSetArray'][$indexC]['Erloeskonto']    = $bookingSet['Erloeskonto'];          // Konto/Erlöskonto
            $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['BSetArray'][$indexB]['CSetArray'][$indexC]['Nettobetrag']    = $curCNetto;                          // Nettobetrag
            $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['BSetArray'][$indexB]['CSetArray'][$indexC]['Steuerbetrag']   = $curCSteuerbetrag;                   // Steuerbetrag
            $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['BSetArray'][$indexB]['CSetArray'][$indexC]['KST']            = $bookingSet['Kostenstelle'];         // KST
            $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['BSetArray'][$indexB]['CSetArray'][$indexC]['KTR']            = '';                                  // KTR
            $hCore->gCore['ExportBuchungsDaten']['KdNr'][$curCustomerNumber]['ASatz']['BSetArray'][$indexB]['CSetArray'][$indexC]['Buchungstext']   = $bookingSet['Buchungstext'];         // Buchungstext
            $indexC++;

            if ($curBookingNumber != $lastBookingNumber){
                $indexB++;
            }



            // Verarbeitete Rechnungsnummer speichern
            $lastBookingNumber = $curBookingNumber;
            $lastCustomerNumber = $curCustomerNumber;


        }   // END foreach ($hCore->gCore['BuchungsDaten'] as $bookingSet){


        RETURN TRUE;

    }   // END private function generateSetA()


















    // CSV - Datei Buchungssatz erstellen
    private function generateBooginCSV()
    {

        $hCore = $this->hCore;


        if (!isset($hCore->gCore['ExportBuchungsDaten']['KdNr'])){
            RETURN FALSE;
        }


        $tilde = '~';

        $csv = '';

        $cntA = 0;
        $cntB = 0;
        $cntC = 0;
        $cntSum = 0;
        $sumABrutto = 0;
        $sumBBrutto = 0;
        $sumCNetto = 0;
        $sumCSteuerBetrag = 0;

        foreach ($hCore->gCore['ExportBuchungsDaten']['KdNr'] AS $kdNummer=>$setArray){

            $cntA++;
            $sumABrutto = $sumABrutto + $setArray['ASatz']['Bruttobetrag'];

            // A Satz erstellen
            $csv .= $setArray['ASatz']['Satzart'] . $tilde;
            $csv .= $setArray['ASatz']['Personenkonto'] . $tilde;
            $csv .= $setArray['ASatz']['Belegnummer'] . $tilde;
            $csv .= $setArray['ASatz']['Rechnungsnummer'] . $tilde;
            $csv .= $setArray['ASatz']['Buchungsperiode'] . $tilde;
            $csv .= $setArray['ASatz']['Belegdatum'] . $tilde;
            $csv .= $setArray['ASatz']['Buchungsdatum'] . $tilde;
            $csv .= $setArray['ASatz']['Bruttobetrag'] . $tilde;
            $csv .= $setArray['ASatz']['Waehrung'] . $tilde;
            $csv .= $setArray['ASatz']['Skonto']. $tilde;
            $csv .= $setArray['ASatz']['Zahlungsbedingungen'] . $tilde;
            $csv .= $setArray['ASatz']['ABWZahlungsart'] . $tilde;
            $csv .= $setArray['ASatz']['Faelligkeit'] . $tilde;
            $csv .= $setArray['ASatz']['Valuta'] . $tilde;
            $csv .= $setArray['ASatz']['Valuta'] . $tilde;
            $csv .= $setArray['ASatz']['PLZ'] . $tilde;
            $csv .= $setArray['ASatz']['Ort'] . $tilde;
            $csv .= $setArray['ASatz']['Strasse'] . $tilde;
            $csv .= $setArray['ASatz']['Hausnummer'] . $tilde;
            $csv .= $setArray['ASatz']['Zusatzhausnummer'] . $tilde;
            $csv .= $setArray['ASatz']['Wohnungsnummer'] . $tilde;
            $csv .= $setArray['ASatz']['ABWKontoInhaberName1'] . $tilde;
            $csv .= $setArray['ASatz']['ABWKontoInhaberName2'] . $tilde;
            $csv .= $setArray['ASatz']['Laendercode'] . $tilde;
            $csv .= $setArray['ASatz']['ABWBLZ'] . $tilde;
            $csv .= $setArray['ASatz']['ABWKontoNr'] . $tilde;
            $csv .= $setArray['ASatz']['ABWIBAN'] . $tilde;
            $csv .= $setArray['ASatz']['ABWAnschriftName1'] . $tilde;
            $csv .= $setArray['ASatz']['ABWAnschriftName2'] . $tilde;
            $csv .= $setArray['ASatz']['ABWAnschriftPLZ'] . $tilde;
            $csv .= $setArray['ASatz']['ABWAnschriftOrt'] . $tilde;
            $csv .= $setArray['ASatz']['ABWAnschriftStrasse'] . $tilde;
            $csv .= $setArray['ASatz']['ABWAnschriftHausNr'] . $tilde;
            $csv .= $setArray['ASatz']['ABWAnschriftHausNrZusatz'] . $tilde;
            $csv .= $setArray['ASatz']['Prenotifcation'] . $tilde;
            $csv .= $setArray['ASatz']['MandatsRefNr'] . $tilde;
            $csv .= $setArray['ASatz']['AnkZahlungseinzgZum'] . $tilde;
            $csv .= $setArray['ASatz']['AnkZahlungseinzgAm'] . $tilde;
            $csv .= $setArray['ASatz']['ABWAnkZahlungseinzg'] . $tilde;
            $csv .= $setArray['ASatz']['BuchungszeichenAvviso'] . $tilde;
            $cntSum++;
            $csv .= "\r\n";



            // B Satz erstellen
            foreach ($setArray['ASatz']['BSetArray'] as $bIndex=>$bArray){

                if (!isset($bArray['Satzart'])){
                    continue;
                }
                $cntB++;
                $sumBBrutto = $sumBBrutto + $bArray['Bruttobetrag'];
                $csv .= $bArray['Satzart'] . $tilde;
                $csv .= $bArray['Bruttobetrag'] . $tilde;
                $csv .= $bArray['Nettobetrag'] . $tilde;
                $csv .= $bArray['Steuerkennzeichen'] . $tilde;
                $csv .= $bArray['Geschaeftsbereich'] . $tilde;
                $cntSum++;
                $csv .= "\r\n";



                // C Satz erstellen
                foreach ($bArray['CSetArray'] as $cIndex=>$cArray){

                    $cntC++;
                    $sumCNetto = $sumCNetto + $cArray['Nettobetrag'];
                    $sumCSteuerBetrag = $sumCSteuerBetrag + $cArray['Steuerbetrag'];
                    $csv .= $cArray['Satzart'] . $tilde;
                    $csv .= $cArray['Erloeskonto'] . $tilde;
                    $csv .= $cArray['Nettobetrag'] . $tilde;
                    $csv .= $cArray['Steuerbetrag'] . $tilde;
                    $csv .= $cArray['KST'] . $tilde;
                    $csv .= $cArray['KTR'] . $tilde;
                    $csv .= $cArray['Buchungstext'] . $tilde;
                    $cntSum++;
                    $csv .= "\r\n";

                }   // END foreach ($bArray['CSetArray'] as $cIndex=>$cArray){



            }   // END foreach ($setArray['ASatz']['BSetArray']){



        }   // END foreach ($hCore->gCore['ExportBuchungsDaten']['KdNr'] AS $kdNummer=>$setArray){

        // Prüfsumme
        $csv .= "P~";
        $csv .= "~";                    // Gesamtanzahl der Sätze "S" innerhalb der Datei
        $csv .= $cntA . "~";            // Gesamtanzahl der Sätze "A" innerhalb der Datei
        $csv .= $sumABrutto. "~";       // Gesamtsumme aller Bruttobeträge der Sätze "A"
        $csv .= $cntB . "~";            // Gesamtanzahl der Sätze "B" innerhalb der Datei
        $csv .= $sumBBrutto. "~";       // Gesamtsumme aller Bruttobeträge der Sätze "B"
        $csv .= $cntC . "~";            // Gesamtanzahl der Sätze "C" innerhalb der Datei
        $csv .= $sumCNetto. "~";        // Gesamtsumme aller Nettobeträge der Sätze "A"
        $csv .= $sumCSteuerBetrag . "~";  // Gesamtsumme aller Steuerbeträge der Sätze "A"
        $csv .= "\r\n";


        $hCore->gCore['BookingCSV'] = $csv;

        RETURN TRUE;

    }   // END private function generateBooginCSV()




    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////





















}   // END class DBExport extends Core
