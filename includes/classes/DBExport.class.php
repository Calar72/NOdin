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
        //$this->getRelevantBaseData();

        // Buchungssatz einlesen
        $this->getBookingData();

        // Kundendaten zu Buchungssatz einlesen

        // A Stamm aufbauen
        $this->generateSets();

        // B Stamm aufbauen

        // C stamm aufbauen

        RETURN TRUE;

    }   // END private function doExportsBookingDataCentronInitial()






    //TODO brauch ich das?
    // Benötigte Kundendaten anhand der anstehenden Rechnungen und KundenNr. ermitteln
    private function getRelevantBaseData()
    {
        $hCore = $this->hCore;

        // Benötigte Kundendaten anhand der anstehenden Rechnungen und KundenNr. ermitteln
        $query = "SELECT r.*, c.KundenNummer
                    FROM bookingDataCentron as r,
                         baseDataCentron as c
                    WHERE c.Personenkonto = r.KundenNummer
                    GROUP BY r.KundenNummer
                    ORDER BY r.KundenNummer";

        $this->simpleout($query);

        /*
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
*/

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

        echo "hier";

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
                // A Satz hinzufügen
                $hCore->gCore['ExportBuchungsDaten'][$setCnt]['A']['Satzart']         = 'A';                            // Satzart

                $indexB = 0;
            }



            // B Satz
            if ($curBookingNumber != $lastBookingNumber){
                // B Satz hinzufügen
                $hCore->gCore['ExportBuchungsDaten'][$setCnt]['B'][$indexB]['Satzart']    = 'B';

                // Zähler erhöhen falls innerhalb von A noch ein Durchlauf mit B kommt
                $indexB++;
            }



            // C Satz




            // Neuer Satz?
            if ($curCustomerNumber != $lastCustomerNumber)
            {
                $setCnt++;
            }


/*
            // A Satz
            // Wenn aktuelle KundenNr. != der vorherigen... dann gibt es einen neuen Array eintrag
            if ($curCustomerNumber != $lastCustomerNumber){

                // Array Zähler erhöhen
                $mainArrayIndex++;

                // Array Zähler für B resetten
                $indexB = 0;

                // Array Zähler für C resetten
                $indexC = 0;

                // Erzeuge A Satz
                $hCore->gCore['ExportBuchungsDaten'][$mainArrayIndex][$bookingSet['KundenNummer']]['A']['Satzart']         = 'A';                            // Satzart
                $hCore->gCore['ExportBuchungsDaten'][$mainArrayIndex][$bookingSet['KundenNummer']]['A']['Personenkonto']   = $bookingSet['KundenNummer'];    // Personenkonto
                $hCore->gCore['ExportBuchungsDaten'][$mainArrayIndex][$bookingSet['KundenNummer']]['A']['Belegnummer']     = $bookingSet['KundenNummer'];    // Belegnummer
                $hCore->gCore['ExportBuchungsDaten'][$mainArrayIndex][$bookingSet['KundenNummer']]['A']['Rechnungsnummer'] = $bookingSet['RechnungsNr'];     // Rechnungsnummer
            }


            // B Satz
            // Wenn aktuelle Rechnungsnummer != der vorherigen... dann gibt es einen neuen B eintrag
            if ($curBookingNumber != $lastBookingNumber){

                // Array Zähler für C resetten
                $indexC = 0;

                $hCore->gCore['ExportBuchungsDaten'][$mainArrayIndex][$bookingSet['KundenNummer']]['B'][$indexB]['Satzart'] = 'B';                          // Satzart

//                // B Index Zähler erhöhen
//                $indexB++;
            }


            // C Satz
            //$hCore->gCore['ExportBuchungsDaten'][$mainArrayIndex][$bookingSet['KundenNummer']]['C'][$indexB][$indexC]['Satzart'] = 'C';                          // Satzart
            $hCore->gCore['ExportBuchungsDaten'][$mainArrayIndex][$bookingSet['KundenNummer']]['B'][$indexB]['C'][$indexC]['Satzart'] = 'C';                          // Satzart

            //B Index Zähler erhöhen
            $indexB++;
            $indexC++;

*/

            /*

            // Wenn die aktuelle KundenNR. nicht der vorherigen entspricht, haben wir einen neuen Satz A
            if ($curCustomerNumber != $lastCustomerNumber){
                // Neuen A Satz erzeugen
//                echo "Erzeuge A KNr: $curCustomerNumber<br>";
                $curIndexC = 0;
                $curIndexB = 0;

                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['Satzart']         = 'A';  // Satzart
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['Personenkonto']         = $bookingSet['KundenNummer'];  // Personenkonto
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['Belegnummer']     = $bookingSet['KundenNummer']; // Belegnummer
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['Rechnungsnummer'] = $bookingSet['RechnungsNr']; // Rechnungsnummer

                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['Buchungsperiode'] = '';    // Buchungsperiode
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['Belegnummer'] = ''; // Belegdatum
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['Buchungsdatum'] = ''; // Buchungsdatum

                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['Bruttobetrag'] = ''; // Bruttobetrag
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['Waehrung'] = $_SESSION['customConfig']['Centron']['Waehrung'];   // Waehrung
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['Skonto'] = '';            // Skontofähiger Betrag
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['Zahlungsbedingungen'] = '';    // Zahlungsbedingungen

                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['ABWZahlungsart'] = '';   // Abweichende Zahlungsart
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['Faelligkeit'] = '';   // Fälligkeit
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['Valuta'] = '';   // Valuta
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['Ort'] = '';   // Ort
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['Strasse'] = '';   // Strasse
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['Hausnummer'] = '';   // Hausnummer
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['Zusatzhausnummer'] = '';   // Zusatzhausnummer
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['Wohnungsnummer'] = '';   // Wohnungsnummer
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['ABWKontoInhaberName1'] = '';   // Abweichen-der-Kontoinhaber_Name1
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['ABWKontoInhaberName2'] = '';   // Abweichen-der-Kontoinhaber_Name2
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['Laendercode'] = '';   // Laendercode
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['ABWBLZ'] = '';   // BLZ abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['ABWKontoNr'] = '';   // Konto_Nr abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['ABWIBAN'] = '';   // IBAN abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['ABWAnschriftName1'] = '';   // Anschrift - Name 1 abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['ABWAnschriftName2'] = '';   // Anschrift - Name 2 abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['ABWAnschriftPLZ'] = '';   // Anschrift - PLZ abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['ABWAnschriftOrt'] = '';   // Anschrift - Ort abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['ABWAnschriftStrasse'] = '';   // Anschrift - Strasse abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['ABWAnschriftHausNr'] = '';   // Anschrift - HausNr. abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['ABWAnschriftHausNrZusatz'] = '';   // Anschrift - Zus. HausNr. abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['Prenotifcation'] = '';   // Prenotification erfolgt (J)
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['MandatsRefNr'] = '';   // Mandatsreferenz-nummer
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['AnkZahlungseinzgZum'] = '';   // Anküendigung des Zahlungseinzugs zum
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['AnkZahlungseinzgAm'] = '';   // Ankündigung des Zahlungseinzugs am
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['ABWAnkZahlungseinzg'] = '';   // Ankündigung des Zahlunseinzugs am für den abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexA]['A']['BuchungszeichenAvviso'] = '';   // Buchungszeichen Avviso



                $curIndexA = 0;

            }
            else {
                $curIndexA++;
            }




            // Wenn die aktuelle RechnungsNr. nicht der vorherigen entspricht, haben wir einen neuen Satz B
            if ($curBookingNumber != $lastBookingNumber){
                // Neuen B Satz erzeugen
//                echo "&nbsp;&nbsp;&nbsp;&nbsp;Erzeuge B RNr: $curBookingNumber<br>";
                $curIndexC = 0;

                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexB]['B']['Satzart'] = 'B';                          // Satzart
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexB]['B']['Bruttobetrag'] = '';                      // Bruttobetrag
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexB]['B']['Nettobetrag'] = '';                       // Nettobetrag
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexB]['B']['Nettobetrag'] = $bookingSet['MwSt'];      // Steuerkennzeichen
                $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexB]['B']['Geschaeftsbereich'] = $_SESSION['customConfig']['Centron']['GeschaeftsbereichNonPrivate'];    // Geschäftsbereich
                $curIndexB++;
            }



            // ... Satz C (Also Detail) anfügen
//            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Erzeuge C KNr: $curCustomerNumber | RNr: $curBookingNumber<br>";
//            $hCore->gCore['ExportBuchungsDaten'][''] =


            $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexC]['C']['Satzart'] = 'C';                          // Satzart
            $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexC]['C']['Erloeskonto'] = $bookingSet['Erloeskonto'];   // Konto/Erlöskonto
            $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexC]['C']['Nettobetrag'] = '';                       // Nettobetrag
            $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexC]['C']['Steuerbetrag'] = '';                      // Steuerbetrag
            $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexC]['C']['KST'] = $bookingSet['Kostenstelle'];      // KST
            $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexC]['C']['KTR'] = '';                               // KTR
            $hCore->gCore['ExportBuchungsDaten'][$curCustomerNumber][$curBookingNumber][$curIndexC]['C']['Buchungstext'] = 'Buchungstext';          // Buchungstext

            $curIndexC++;
*/
//


            // Wenn die aktuell Rechnungsnummer nicht der vorhergegangenen entspricht, haben wir eine neue Rechnug
//            echo "<pre>";
//            print_r($curBookingNumber);
//            echo "</pre>";
//            echo "<br>";



            // Verarbeitete Rechnungsnummer speichern
            $lastBookingNumber = $curBookingNumber;
            $lastCustomerNumber = $curCustomerNumber;


        }   // END foreach ($hCore->gCore['BuchungsDaten'] as $bookingSet){



        RETURN TRUE;

    }   // END private function generateSetA()








    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////





















}   // END class DBExport extends Core
