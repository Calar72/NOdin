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
class XYYZDBExport extends Core
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
//        $query = "SELECT c.*
//                    FROM bookingDataCentron as r,
//                         baseDataCentron as c
//                    WHERE c.Personenkonto = r.KundenNummer
//                    AND c.Personenkonto = '10031'
//                    GROUP BY r.KundenNummer
//                    ORDER BY r.KundenNummer";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            RETURN FALSE;
        }


        $cntIndex = 0;
        while($row = $result->fetch_object()) {

            foreach ($dbFieldnames as $curFieldname){
                $hCore->gCore['CustomerData'][$row->Personenkonto][$curFieldname] = utf8_encode($row->$curFieldname);  // Automatisch die Feldnamen als Variable benutzen
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
                $hCore->gCore['BuchungsDaten'][$cntIndex][$curFieldname] = utf8_encode($row->$curFieldname);  // Automatisch die Feldnamen als Variable benutzen
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

//        $curIndex = 0;
//        $curIndexC = 0;
//        $curIndexB = 0;
//        $curIndexA = 0;
//
//        $mainArrayIndex = 0;
//
//
//        $indexMain = 0;
//        $indexA = 0;
        $indexB = 0;
        $indexC =0;

//        $setCnt =0;

        $indexA = 0;
        foreach ($hCore->gCore['BuchungsDaten'] as $bookingSet) {

//            if ($bookingSet['KundenNummer'] != '10348'){
//                continue;
//            }

            $curCustomerNumber  = $bookingSet['KundenNummer'];
            $curBookingNumber   = $bookingSet['RechnungsNr'];


            // Wenn die aktuelle KundenNR. nicht der vorherigen entspricht, haben wir einen neuen Satz A

            // Wenn die aktuelle RechnungsNr. nicht der vorherigen entspricht, haben wir einen neuen Satz B

            // ... Satz C (Also Detail) anfügen


            // Neue Kundennummer?
            if ($curCustomerNumber != $lastCustomerNumber){

                //2016-01-12
                preg_match_all("/(\d+)\-(\d+)\-(\d+)/i", $bookingSet['Datum'], $splitDate);
                $curBuchungsperiode = $splitDate[1][0] . '.' . $splitDate[2][0];
                $curBelegdatum      = $splitDate[1][0] . $splitDate[2][0]. $splitDate[3][0];

                $curBuchungsdatum = $curBelegdatum;

                $curZahlungsbedingungen = $_SESSION['customConfig']['Centron']['Zahlungsbedingung'];


                if (!isset($hCore->gCore['CustomerData'][$bookingSet['KundenNummer']])){
                    // Habe den Stammdatensatz nicht!
                    // Message Ausgabe vorebeiten
                    $hCore->gCore['Messages']['Type'][]      = 'Fehler';
                    $hCore->gCore['Messages']['Code'][]      = 'Error';
                    $hCore->gCore['Messages']['Headline'][]  = 'DB - Exort';
                    $hCore->gCore['Messages']['Message'][]   = 'FEHLER: fehlender Stammdatensatz KDNr.: ' . $bookingSet['KundenNummer'] . '<br>';
                    continue;
                }


                // Erzeuge neuen A Satz
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Satzart'] = 'A';
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Satzart']                    = 'A';                                      // Satzart
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Personenkonto']              = $bookingSet['KundenNummer'];              // Personenkonto
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Belegnummer']                = $bookingSet['KundenNummer'];              // Belegnummer
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Rechnungsnummer']            = sprintf("%'.012d", $bookingSet['bookingDataCentronID']);      // Rechnungsnummer
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Buchungsperiode']            = $curBuchungsperiode;                      // Buchungsperiode
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Belegdatum']                 = $curBelegdatum;                           // Belegdatum
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Buchungsdatum']              = $curBuchungsdatum;                        // Buchungsdatum
                // PFLICHT (Wird im B - Teil behandelt und gesetzt)
//                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Bruttobetrag'] = ''; // Bruttobetrag
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Waehrung']                   = $_SESSION['customConfig']['Centron']['Waehrung'];   // Waehrung
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Skonto']                     = '';    // Skontofähiger Betrag
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Zahlungsbedingungen']        = $curZahlungsbedingungen;    // Zahlungsbedingungen
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['ABWZahlungsart']             = '';   // Abweichende Zahlungsart
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Faelligkeit']                = '';   // Fälligkeit
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Valuta']                     = '';   // Valuta
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['PLZ']                        = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_PLZ'];      // PLZ
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Ort']                        = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Ort'];   // Ort
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Strasse']                    = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Strasse'];   // Strasse
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Hausnummer']                 = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Hausnummer'];   // Hausnummer
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Zusatzhausnummer']           = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Zusatzhausnummer'];   // Zusatzhausnummer
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Wohnungsnummer']             = '';   // Wohnungsnummer
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['ABWKontoInhaberName1']       = '';   // Abweichen-der-Kontoinhaber_Name1
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['ABWKontoInhaberName2']       = '';   // Abweichen-der-Kontoinhaber_Name2
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Laendercode']                = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Laendercode'];   // Laendercode
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['ABWBLZ']                     = '';   // BLZ abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['ABWKontoNr']                 = '';   // Konto_Nr abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['ABWIBAN']                    = '';   // IBAN abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['ABWAnschriftName1']          = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Name1_abw_Kontoinhaber'];   // Anschrift - Name 1 abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['ABWAnschriftName2']          = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Name2_abw_Kontoinhaber'];   // Anschrift - Name 2 abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['ABWAnschriftPLZ']            = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_PLZ_abw_Kontoinhaber'];   // Anschrift - PLZ abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['ABWAnschriftOrt']            = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Ort_abw_Kontoinhaber'];   // Anschrift - Ort abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['ABWAnschriftStrasse']        = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Strasse_abw_Kontoinhaber'];   // Anschrift - Strasse abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['ABWAnschriftHausNr']         = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Hnr_abw_Kontoinhaber'];   // Anschrift - HausNr. abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['ABWAnschriftHausNrZusatz']   = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_zus_Hnr_abw_Kontoinhaber'];   // Anschrift - Zus. HausNr. abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Prenotifcation']             = 'j';  // Prenotification erfolgt (J)
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['MandatsRefNr']               = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Mandatsreferenznummer'];   // Mandatsreferenz-nummer
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['AnkZahlungseinzgZum']        = '';   // Anküendigung des Zahlungseinzugs zum
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['AnkZahlungseinzgAm']         = '';   // Ankündigung des Zahlungseinzugs am
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['ABWAnkZahlungseinzg']        = '';   // Ankündigung des Zahlunseinzugs am für den abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['BuchungszeichenAvviso']      = '';   // Buchungszeichen Avviso

                // Index B zrücksetzen
                $indexB = 0;

                // Index C resetten
                $indexC = 0;
            }









            // Neue Rechnungsnummer?
            if ($curBookingNumber != $lastBookingNumber){

                // Erzeuge neuen B Satz
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['B'][$curBookingNumber]['Satzart']              = 'B';                            // Satzart
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['B'][$curBookingNumber]['Bruttobetrag']         = $bookingSet['Brutto'];   // Bruttobetrag
                //$hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['B'][$curBookingNumber]['Nettobetrag']        = '';                       // Nettobetrag
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['B'][$curBookingNumber]['Steuerkennzeichen']    = $bookingSet['MwSt'];      // Steuerkennzeichen
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['B'][$curBookingNumber]['Geschaeftsbereich']    = $_SESSION['customConfig']['Centron']['GeschaeftsbereichNonPrivate'];    // Geschäftsbereich

                // A Brutto Betrag
                if (isset($hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Bruttobetrag'])){
                    $curABrutto = $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Bruttobetrag'];
                }
                else{
                    $curABrutto = 0;
                }
                $curNewABrutto = $curABrutto + $bookingSet['Brutto'];
                $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['Bruttobetrag'] = $curNewABrutto;

                // Index C resetten
                $indexC = 0;
            }







            // C Satz
            $a = $bookingSet['Brutto'];
            $b = $bookingSet['MwSt'];
            $curCSteuerbetrag =  $a * ($b/100);
            $curCNetto = $a - $curCSteuerbetrag;

            $curBNetto = 0;
            if (isset($hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['B'][$curBookingNumber]['Nettobetrag'])){
                $curBNetto = $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['B'][$curBookingNumber]['Nettobetrag'];
            }
            $curNewNetto = $curBNetto + $curCNetto;

            // B Brutto Betrag:
            $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['B'][$curBookingNumber]['Nettobetrag'] = $curNewNetto;

//            $info = "Brutto: ".$a . " Netto: " . $curCNetto . " Betrag: " . $curCSteuerbetrag;
            $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['B'][$curBookingNumber]['C'][$indexC]['Satzart']        = 'C';
            $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['B'][$curBookingNumber]['C'][$indexC]['Erloeskonto']    = $bookingSet['Erloeskonto'];          // Konto/Erlöskonto
            $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['B'][$curBookingNumber]['C'][$indexC]['Nettobetrag']    = $curCNetto;                          // Nettobetrag
            $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['B'][$curBookingNumber]['C'][$indexC]['Steuerbetrag']   = $curCSteuerbetrag;                   // Steuerbetrag
            $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['B'][$curBookingNumber]['C'][$indexC]['KST']            = $bookingSet['Kostenstelle'];         // KST
            $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['B'][$curBookingNumber]['C'][$indexC]['KTR']            = '';                                  // KTR
            $hCore->gCore['ExportBuchungsDaten']['A'][$curCustomerNumber][$indexA]['B'][$curBookingNumber]['C'][$indexC]['Buchungstext']   = $bookingSet['Buchungstext'];         // Buchungstext



            // Index A erhöhen?



            // Index B erhöhen?
            if ($curBookingNumber != $lastBookingNumber){
                $indexB++;
            }



            // Index C erhöhen
            $indexC++;



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


        if (!isset($hCore->gCore['ExportBuchungsDaten']['A'])){
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

        foreach ($hCore->gCore['ExportBuchungsDaten']['A'] AS $kdNummer=>$setArray){

            $cntA++;
            //$csv .= $setArray['ASatz']['Satzart'] . $tilde;


            foreach ($setArray as $indexA=>$setA){


                $sumABrutto = $sumABrutto + $setA['Bruttobetrag'];

                // A Satz erstellen
                $csv .= $setA['Satzart'] . $tilde;
                $csv .= $setA['Personenkonto'] . $tilde;
                $csv .= $setA['Belegnummer'] . $tilde;
                $csv .= $setA['Rechnungsnummer'] . $tilde;
                $csv .= $setA['Buchungsperiode'] . $tilde;
                $csv .= $setA['Belegdatum'] . $tilde;
                $csv .= $setA['Buchungsdatum'] . $tilde;
                $csv .= $setA['Bruttobetrag'] . $tilde;
                $csv .= $setA['Waehrung'] . $tilde;
                $csv .= $setA['Skonto']. $tilde;
                $csv .= $setA['Zahlungsbedingungen'] . $tilde;
                $csv .= $setA['ABWZahlungsart'] . $tilde;
                $csv .= $setA['Faelligkeit'] . $tilde;
                $csv .= $setA['Valuta'] . $tilde;
                $csv .= $setA['Valuta'] . $tilde;
                $csv .= $setA['PLZ'] . $tilde;
                $csv .= $setA['Ort'] . $tilde;
                $csv .= $setA['Strasse'] . $tilde;
                $csv .= $setA['Hausnummer'] . $tilde;
                $csv .= $setA['Zusatzhausnummer'] . $tilde;
                $csv .= $setA['Wohnungsnummer'] . $tilde;
                $csv .= $setA['ABWKontoInhaberName1'] . $tilde;
                $csv .= $setA['ABWKontoInhaberName2'] . $tilde;
                $csv .= $setA['Laendercode'] . $tilde;
                $csv .= $setA['ABWBLZ'] . $tilde;
                $csv .= $setA['ABWKontoNr'] . $tilde;
                $csv .= $setA['ABWIBAN'] . $tilde;
                $csv .= $setA['ABWAnschriftName1'] . $tilde;
                $csv .= $setA['ABWAnschriftName2'] . $tilde;
                $csv .= $setA['ABWAnschriftPLZ'] . $tilde;
                $csv .= $setA['ABWAnschriftOrt'] . $tilde;
                $csv .= $setA['ABWAnschriftStrasse'] . $tilde;
                $csv .= $setA['ABWAnschriftHausNr'] . $tilde;
                $csv .= $setA['ABWAnschriftHausNrZusatz'] . $tilde;
                $csv .= $setA['Prenotifcation'] . $tilde;
                $csv .= $setA['MandatsRefNr'] . $tilde;
                $csv .= $setA['AnkZahlungseinzgZum'] . $tilde;
                $csv .= $setA['AnkZahlungseinzgAm'] . $tilde;
                $csv .= $setA['ABWAnkZahlungseinzg'] . $tilde;
                $csv .= $setA['BuchungszeichenAvviso'] . $tilde;
                $csv .= "\r\n";

                $cntSum++;


                // B Satz erstellen
                foreach ($setA['B'] as $bIndex=>$bArray){

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
                    foreach ($bArray['C'] as $cIndex=>$cArray){

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
                        $csv .= "\r\n";
                        $cntSum++;

                    }   // END foreach ($bArray['CSetArray'] as $cIndex=>$cArray){


                }   // END foreach ($setArray['ASatz']['BSetArray']){

            }

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
