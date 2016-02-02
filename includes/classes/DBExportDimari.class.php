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
 *
 */
class DBExportDimari extends Core
{

    public $gDBExportDimari = array();

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




    public function getExportsBaseDataDimari()
    {
        $hCore = $this->hCore;

        // Typ bekannt!
        $req_sourceTypeID   = $hCore->gCore['getGET']['subAction'];

        // System bekannt!
        $req_sourceSystemID = $hCore->gCore['getGET']['valueAction'];

        // Daten einlesen

        // Summe der Datensätze
        $query = "SELECT COUNT(*) AS sumBaseData FROM baseDataDimari WHERE 1";
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
        $query = "SELECT lastUpdate FROM baseDataDimari WHERE 1 ORDER BY lastUpdate ASC LIMIT 1";
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
        $query = "SELECT lastUpdate FROM baseDataDimari WHERE 1 ORDER BY lastUpdate DESC LIMIT 1";
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
        $query = "SELECT userName FROM user u, baseDataDimari as b WHERE u.userID = b.userID GROUP BY u.userID";
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
        $query = "SELECT SAMMELKONTO FROM baseDataDimari WHERE 1 GROUP BY SAMMELKONTO";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            $Sammelkonten[] = '';
        }
        else{
            while($row = $result->fetch_object()){
                $Sammelkonten[] = $row->SAMMELKONTO;
            }
        }
        $hCore->gCore['baseDataInfo']['Sammelkonten'] = $Sammelkonten;
        $this->gCoreDB->free_result($result);




        // Zahlungsart
        $query = "SELECT ZAHLART FROM baseDataDimari WHERE 1 GROUP BY ZAHLART";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            $Zahlungsarten[] = '';
        }
        else{
            while($row = $result->fetch_object()){
                $Zahlungsarten[] = $row->ZAHLART;
            }
        }
        $hCore->gCore['baseDataInfo']['Zahlungsarten'] = $Zahlungsarten;
        $this->gCoreDB->free_result($result);

        RETURN TRUE;
    }



    // INITIAL
    public function doExportsBaseDataDimari()
    {
        // Datenstamm aus DB lesen wo log_baseDataImportsID = der gewaehlten ImportID ist
        $zeilen = $this->readDatensatz();

//$this->simpleout($zeilen);
        $return['csv'] = $this->OBSchnittstelleDimari($zeilen);

        RETURN TRUE;
    }




    function OBSchnittstelleDimari($zeilen)
    {

        $hCore = $this->hCore;

        // .csv Variable initialisieren
        $csv = "";

        // .csv Array initialisieren
        $csvA = array();

        // Kundenz�hler (Durchlauf) initialisieren, den brauchen wir auch f�r die Summenpr�fung ganz am Ende der .csv
        $cntKunden = 0;

        // Durchlaufz�hler
        $cnt = 0;

        // Wenn die erste Zeile in der (import) .csv Datei �berschriften sprich Feldnamen beinhaltet, hier auf TRUE setzen!
        $skipHeadline = false;

        // Fehler und Warning-Array initialisieren
        $errorArray 	= array();
        $warningArray	= array();

        // cfgSatz einlesen
        $myCfgSatz = array();
        $cfgSatz = $this->readCfgSatz();
        // TODO Wenn $cfgSatz leer ... Fehler abhandeln

        // Refferenz Array fuer Change-Index erstellen
        $refArray = array();
        $refArray = $this->generateChangeIndexCfgSatz($cfgSatz);


//        echo "<pre>";
//        print_r($zeilen); echo "<br>";
//        echo "</pre>";


        // Jede Zeile der .csv Durchlaufen und dann die enthaltenen Felder auf ihre Gültigkeit prüfen
        foreach ($zeilen as $index=>$kunde){

            // Durchlaufzähler im Gegensatz zum Kundenzähler auf jeden Fall erhöhen
            $cnt++;

            // Headline in Rohdatei? Wenn ja, ueberspringe ich die erste Zeile
            if ( ($skipHeadline) && ($cntKunden == 0) ){
                $skipHeadline = FALSE;
                continue;
            }


            // Gültige Kundennummer vorhanden?
            if (trim($kunde['PERSONENKONTO']) == ""){
                continue;
            }
            else {
                $personenkonto = trim($kunde['PERSONENKONTO']);
            }

            $search = '/^(\d+)/';
            $matches = "";
            preg_match($search, $personenkonto, $matches);

            if ( (isset($matches[0])) && ($matches > 0) ){
                // gültige KdNr.
                $csvA['PERSONENKONTO'] = trim($matches[0]);
            }
            else {
                // ungültige KdNr.
                // TODO Message Fehler hier:
                $errorArray[$cnt]['000000']['PERSONENKONTO'] = 'Kein gültige Kundennummer/Personenkonto';
                continue;
            }

            /////////////////////////////////////////////////////////////////////////
            // Ab hier geht es nur weiter wenn eine gütlite Kundennummer vorliegt! //

            // Durchlauf sprich Kundenzähler erhöhen
            $cntKunden++;

            // Speicher die Index-Refferenzierung in indexKunde
            $indexKunde = $kunde;
            unset($kunde);

            // Index Nummer durch Kennung tauschen
// 			$kunde = $this->changeIndex($refArray, $indexKunde);
            $kunde = $indexKunde;

            // Speicher die Kunden - Daten bevor sie "gesäubert" werden sprich strlen usw.
            $dirtKunde = $kunde;
            unset($kunde);


            // Aktuellen Kunden-Datensatz (sprich die aktuelle Reihe) zum Pr�fen geben
            $getReturn = $this->checkCustomerRowValue($cfgSatz, $dirtKunde);
            // $getReturn['errorArray'][{indexkennung}] = 'bla bla Fehlerbeschreibung bla bla';
            // $getReturn['kundenDaten'][{indexkennung}] = 'Inhalt z.B. Vorname, PLZ uvw.';
            $kunde 		= $getReturn['kundenDaten'];
            if (isset($getReturn['errorArray'])){
                // TODO Message Fehlre hier:
                $errorArray[] = $getReturn['errorArray'];
            }


            // Bereinigte Daten in Export- .csv Datei f�r kVASy - System schreiben
            $csv .= $this->writeToCSVSingleCustomer($kunde);
        }

        // Prüfsumme
        $csv .= "P~";
        $csv .= $cntKunden . "~";	// Gesamtanzahl der S�tze �S� innerhalb der Datei
        $csv .= "~"; 				// Gesamtanzahl der S�tze �A� innerhalb der Datei
        $csv .= "~"; 				// Gesamtsumme aller Bruttobetr�ge der S�tze �A�
        $csv .= "~";				// Gesamtanzahl der S�tze �B� innerhalb der Datei
        $csv .= "~"; 				// Gesamtsumme aller Bruttobetr�ge der S�tze �B�
        $csv .= "~"; 				// Gesamtanzahl der S�tze �C� innerhalb der Datei
        $csv .= "~"; 				// Gesamtsumme aller Nettobetr�ge der S�tze �A�
        $csv .= "~"; 				// Gesamtsumme aller Steuerbetr�ge der S�tze �A�

        $csv .= "\r\n";

        // 		$this->simpleout($errorArray);


        // Informationen aufbereiten
        $typeIndex = array_search($hCore->gCore['getGET']['subAction'], $hCore->gCore['LNav']['ConvertTypeID']);
        $typeInfo = $hCore->gCore['LNav']['ConvertType'][$typeIndex];

        $systemIndex = array_search($hCore->gCore['getGET']['valueAction'], $hCore->gCore['LNav']['ConvertSystemID']);
        $systemInfo = $hCore->gCore['LNav']['ConvertSystem'][$systemIndex];


        // TODO Export - Verzeichnis Funktion erstellen (Dimari)

        $downloadLink = 'DimariStammdatenExport';

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
        $hCore->gCore['Messages']['Headline'][]  = 'DB - Export <i class="fa fa-arrow-right"></i> '.$typeInfo.' <i class="fa fa-arrow-right"></i> '.$systemInfo;
        $hCore->gCore['Messages']['Message'][]   = 'DB - Export erfolgreich!<br>Die Datei kann jetzt <a href="'.$newDownloadLink.'" class="std" target=_blank>HIER</a> heruntergeladen werden!';


        $hCore->gCore['getLeadToBodySite']          = 'includes/html/home/homeBody';    // Webseite die geladen werden soll

        return $csv;

    }	// END function OBSchnittstelleDimari(...) {










    function writeToCSVSingleCustomer($kunde)
    {

        $csv = "";
        $tilde = '~';

        $csv .= "S~";									// Satzart
        $csv .= $kunde['PERSONENKONTO'] . $tilde;		// Personenkonto
        $csv .= $kunde['NAME1_FULL'] . $tilde; 			// Name1
        $csv .= $kunde['NAME2_REST'] . $tilde; 			// Name2
        $csv .= $kunde['SAMMELKONTO'] . $tilde;			// Sammelkonto					// TODO KLAEREN: Was soll ich hier eintragen A?
        $csv .= $kunde['ZAHLART'] . $tilde;				// Zahlungsart					// TODO KLAEREN: Was soll ich hier eintragen B?
        $csv .= "~"; 									// Mandatsreferenznummer
        $csv .= "~"; 									// L�ndercode
        $csv .= $kunde['BLZ'] . $tilde; 				// BLZ
        $csv .= $kunde['BIC'] . $tilde; 				// BIC
        $csv .= $kunde['KONTONUMMER'] . $tilde; 		// Kontonummer
        $csv .= $kunde['IBAN'] . $tilde; 				// IBAN
        $csv .= $kunde['ANREDEBRIEF'] . $tilde; 		// Anrede Brief
        $csv .= $kunde['ANREDEANSCHRIFT'] . $tilde; 	// Anschrift - Anrede
        $csv .= $kunde['NAME1_FULL'] . $tilde;			// Anschrift - Name1
        $csv .= $kunde['NAME2_REST'] . $tilde;			// Anschrift - Name2
        $csv .= "~";									// Anschrift - Name3
        $csv .= "~"; 									// Anschrift - L�nderkennzeichen
        $csv .= $kunde['PLZ'] . $tilde;					// Anschrift - PLZ
        $csv .= $kunde['ORT'] . $tilde;					// Anschrift - Ort
        $csv .= $kunde['STRASSE'] . $tilde; 			// Anschrift - Stra�e
        $csv .= $kunde['HAUSNUMMER'] . $tilde; 			// Anschrift - Hausnummer
        $csv .= $kunde['HAUSNUMMERZUSATZ'] . $tilde; 	// Zusatzhausnummer
        $csv .= "~"; 									// Anschrift - Postfach
        $csv .= "~"; 									// Anschrift Name1 abw. Kontoinhaber
        $csv .= "~"; 									// Anschrift Name2 abw. Kontoinhaber
        $csv .= "~"; 									// Anschrift PLZ abw. Kontoinhaber
        $csv .= "~"; 									// Anschrift Ort abw. Kontoinhaber
        $csv .= "~"; 									// Anschrift Stra�e abw. Kontoinhaber
        $csv .= "~"; 									// Anschrift Hnr abw. Kontoinhaber
        $csv .= "~"; 									// Anschrift zus. Hnr abw. Kontoinhaber
        $csv .= $kunde['TELEFON1'] . $tilde;	 		// Telefon
        $csv .= "~"; 									// Fax
        $csv .= $kunde['EMAIL'] . $tilde;				// Email
        $csv .= "~"; 									// Aktennummer
        $csv .= "~"; 									// Sortierkennzeichen
        $csv .= "~"; 									// EG-Identnummer
        $csv .= "~"; 									// Branche
        $csv .= "~"; 									// Zahl-bed. Auftr.wes
        $csv .= "~"; 									// Preisgruppe Auftr.wes

        $csv .= "\r\n";

        RETURN $csv;

    }	// END function writeToCSV(...){












    function checkCustomerRowValue($cfgSatz, $kunde)
    {

        $newKundeData 	= array();
        $myErrorArray 	= array();
        $myMatches 		= array();


        // ANMERKUNG:
        // Das Array $cfgSatz wird aus der Datenbank gelesen
        // siehe Funktion readCfgSatz aufgerufen in der OBSchnittstelleDimari

        $indexCnt = 0;

        $newKunde = array();

        foreach ($kunde as $indexKennung=>$value){

            $pflicht 		= $cfgSatz['S'][$indexKennung]['PFLICHT'];
            $vorbedingung 	= $cfgSatz['S'][$indexKennung]['VORBEDINGUNG'];
            $maxLen 		= $cfgSatz['S'][$indexKennung]['MAXLEN'];

            $clearValue = '';

            $tmpCustomerNumber = trim($kunde['PERSONENKONTO']);

            // SAMMELKONTO? ... Hardcodet setzen
            if ($indexKennung == 'SAMMELKONTO')
                $value = $_SESSION['customConfig']['Dimari']['Sammelkonto'];

            // Sonderfall Name 2 und nicht Name 1 gegeben?
            if ($indexKennung == 'NAME1'){
                if ( (strlen($value) < 1) && (strlen($kunde['NAME2']) > 0) ){
                    $value = $kunde['NAME2'];
                }
            }


            // Pflichtfeld?
            // JA Pflichtfeld
            if ($pflicht == 'YES'){

                // Wurden überhaupt Daten übergeben?
                if (strlen($value) < 1){
                    // TODO Message Fehlre hier:
                    $myErrorArray[$tmpCustomerNumber][$indexKennung] = 'Fehlende Daten bei Pflicht-Datensatz';
                }
                else {
                    // Datenlänge ok?
                    $tmpValue = $this->initSubstrStrLen($value, $maxLen);
                    $clearValue = $tmpValue[0];
                }
            }


            // NEIN kein Pflichtfeld
            elseif ($pflicht == 'NO'){

                // Wurden überhaupt Daten übergeben?
                if (strlen($value) > 0){
                    $tmpValue = $this->initSubstrStrLen($value, $maxLen);
                    $clearValue = $tmpValue[0];
                }
            }


            // JA WENN Vorbedingung
            elseif ($pflicht == 'YESIF'){
                // Prüfen wir nach dem foreach-Durchlauf... dann haben alle bereinigten Daten zur Prüfung vorliegen
                $laterCheck[$kunde['PERSONENKONTO']][$indexKennung] = $vorbedingung;

                // Wurden überhaupt Daten übergeben?
                if (strlen($value) > 0){
                    $tmpValue = $this->initSubstrStrLen($value, $maxLen);
                    $clearValue = $tmpValue[0];
                }
            }


            else {
                // ??? Kenne die Bedingungen für Pflichtfeld nicht
            }


            // Neuen Kunden-Datensatz füllen
            $newKunde[$indexKennung] = $clearValue;

            $indexCnt++;

        }	// ENDE foreach ($kunde as $indexKennung=>$value){



        //////////////////////////////////////////////////////////////////////////////////////////////////
        // AUSNAHMEN UND MANUELLE ERSTELLUNGEN

        // NAME1 soll später Vor- und Nachname beinhalten ... ich lege die Daten in einen neuen Index
        $tmpNAME1_FULL = $newKunde['NAME1'] . " " . $newKunde['NAME2'];

        // Datenlänge ok?
        $tmpValue = $this->initSubstrStrLen($tmpNAME1_FULL, $cfgSatz['S']['NAME1']['MAXLEN']);
        $newKunde['NAME1_FULL'] = trim($tmpValue[0]);
        if (isset($tmpValue[1]))
            $newKunde['NAME2_REST'] = trim($tmpValue[1]);
        else
            $newKunde['NAME2_REST'] = '';


        // Zahlart für kVASY - eigene - Kennung passend setzen
        // Die Kennung wird in der (derzeit) defaultConfig.inc.php gesetzt
        if ($newKunde['ZAHLART'] == '1')
            $newKunde['ZAHLART'] = $_SESSION['customConfig']['Dimari']['Zahlart'][1];
        else
            $newKunde['ZAHLART'] = $_SESSION['customConfig']['Dimari']['Zahlart'][0];


        // Pflichtfeld wenn ... Abhandeln
        if (count($laterCheck) > 0){
            // 			$this->simpleout($laterCheck);
            // 			$this->simpleout($kunde);

            foreach ($laterCheck as $customerNumber=>$requireInformationArray){

                foreach ($requireInformationArray as $feldKennung=>$targetFeldKennung){

                    // Zahlart gesondert abfangen
                    if ( ($targetFeldKennung == 'ZAHLART') && ($kunde['ZAHLART'] < 1) ){
                        // Kontonummer usw. nicht pflicht
                        continue;
                    }

                    if (strlen($kunde[$targetFeldKennung]) > 0){

                        $value 				= $kunde[$feldKennung];
                        $tmpCustomerNumber 	= $customerNumber;
                        $indexKennung 		= $feldKennung;
                        $maxLen 			= $cfgSatz['S'][$indexKennung]['MAXLEN'];

                        // Wurden überhaupt Daten übergeben?
                        if (strlen($value) < 1){
                            // Kontonummer?
                            // Wenn keine Kontonummer ... aber IBAN ... dann ist das ok
                            if ( ($feldKennung == 'KONTONUMMER') && (strlen($kunde ['IBAN']) > 1) ){
                                // Alles ok, Kontonummer nicht zwingend notwendig
                            }
                            else {
                                $myErrorArray[$tmpCustomerNumber][$indexKennung] = 'Fehlende Daten bei Pflicht-Datensatz';
                            }
                        }
                        else {
                            // Datenlänge ok?
                            $tmpValue = $this->initSubstrStrLen($value, $maxLen);
                            $clearValue = $tmpValue[0];
                        }
                    }

                }

            }
        }

        $ret['kundenDaten'] = $newKunde;
        if (count($myErrorArray) > 0)
            $ret['errorArray'] = $myErrorArray;


        return $ret;

    }	// END function checkCustomerRowValue(...){


















    function initSubstrStrLen($checkArray, $maxlen){

        // Array als übergebenes Argument notwendig!
        // Wenn wir kein Array erhalten haben, erstellen wir hier eine passende Übergabe!
        if (!is_array($checkArray)){

            $tmpStr = $checkArray;

            $checkArray = array();
            $checkArray[] = $tmpStr;

        }


        // Funktion soll sich selber (erneut) aufrufen?
        $recall = false;


        // Jeden Array - Eintrag pr�fen
        foreach ($checkArray as $index=>$curCheck){

            $curCheck = trim($curCheck);

            if (strlen($curCheck)>$maxlen){
                $recall = true;	// Neuer "selbst"-Aufruf notwendig

                // An substr geben
                $newArrayEntry 	= substr($curCheck, 0, $maxlen);
                $rest 			= substr($curCheck, $maxlen);

                $checkArray[$index] = trim($newArrayEntry);
                $checkArray[] 		= trim($rest);

                break;
            }

        }

        // Selbstaufruf soll durchgef�hrt werden!
        if ($recall)
            $this->initSubstrStrLen($checkArray, $maxlen);

        return $checkArray;

    }	// END function initSubstrStrLen(...){






















    function generateChangeIndexCfgSatz($cfgSatz){

        foreach ($cfgSatz as $cfgSatzIndex=>$value){

            foreach ($value as $indexValue=>$egal){
                $refArray[$cfgSatzIndex][] = $indexValue;
            }

        }

        return ($refArray);
    }








































    // Wie .csv einlesen.... nur gehe ich ueber die DB und habe feldnamen als array-index in zeilen
    function readDatensatz()
    {
         $cfgSatz = $this->readCfgSatz();

        // Erstelle query select
        $sel = '';
        foreach ($cfgSatz['S'] as $index=>$valueArray){
            $sel .= $index . ", ";
        }

        $sel = substr($sel, 0, -2);

        $zeilen = array();

        $query = "SELECT " . $sel . " FROM baseDataDimari WHERE 1 ORDER BY baseDataDimariID";

        $result = $this->gCoreDB->query($query);

        // Betroffene Zeilen, bzw. erhaltene
        $num_rows = $this->gCoreDB->num_rows($result);

        // Keine Import Datei gefunden!
        if (!$num_rows == '1'){

            // Breche Methode hier ab und liefere false - Wert zurück

            RETURN FALSE;
        }

        $indexCnt = 0;
//        while($row = $result->fetch_object()){
        while($row = $result->fetch_assoc()){

            $zeilen[$indexCnt] = $row;

            $indexCnt++;
        }

        // Gebe DB - Speicher wieder frei
        $this->gCoreDB->free_result($result);

        return $zeilen;
    }








    function readCFGSatz() {

        $cfgSatz = array();

        $query = "SELECT c.arrayIndex,
						c.indexKennung,
						c.typKennung,
						c.value,
						sourceType.shortCut

					FROM importConditions AS c

				LEFT JOIN sourceSystem 	ON c.sourceSystemID = sourceSystem.sourceSystemID
				LEFT JOIN sourceType 	ON c.sourceTypeID 	= sourceType.sourceTypeID

				WHERE c.active = 'yes'

					AND sourceType.active 			= 'yes'
					AND sourceType.sourceTypeID = '".$this->hCore->gCore['getGET']['subAction']."'

					AND sourceSystem.active 			= 'yes'
					AND sourceSystem.sourceSystemID = '".$this->hCore->gCore['getGET']['valueAction']."'

				ORDER BY c.arrayIndex";


        // Resultat
        $result = $this->gCoreDB->query($query);

        // Betroffene Zeilen, bzw. erhaltene
        $num_rows = $this->gCoreDB->num_rows($result);

        // Keine Import Datei gefunden!
        if (!$num_rows == '1'){

            // Breche Methode hier ab und liefere false - Wert zurück

            RETURN FALSE;
        }

        // Ergebnis speichern
        while($row = $result->fetch_object()){
            // Format:
            // $cfgSatz['S']['PERSONENKONTO']['PFLICHT'] 		= 'YES';
            $cfgSatz[$row->shortCut][$row->indexKennung][$row->typKennung] = $row->value;
        }

        // Gebe DB - Speicher wieder frei
        $this->gCoreDB->free_result($result);

        return $cfgSatz;

    }	// END function readCfgSatz(...) {






























//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


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
//                    AND c.Personenkonto = '10348'
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
//        $query = "SELECT * FROM bookingDataCentron WHERE KundenNummer > '10881' ORDER BY RechnungsNr, KundenNummer";
        $query = "SELECT * FROM bookingDataCentron WHERE 1 ORDER BY RechnungsNr, KundenNummer";
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
//        $lastCustomerNumber = 0;
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
//        $indexB = 0;
        $indexC =0;

//        $setCnt =0;

//        $indexA = 0;
        foreach ($hCore->gCore['BuchungsDaten'] as $bookingSet) {

//            if ($bookingSet['KundenNummer'] != '10348'){
//                continue;
//            }

            $curCustomerNumber  = $bookingSet['KundenNummer'];
            $curBookingNumber   = $bookingSet['RechnungsNr'];


            // Wenn Neue Rechnungsnummer, dann neuen Rechnungssatz erstellen
            if ($curBookingNumber != $lastBookingNumber){

                // Index C resetten
                $indexC = 0;

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

                // Rechnungsnummer:
                if ($bookingSet['Brutto'] < 0){
                    $preReNummer = 'VR';
                }
                else{
                    $preReNummer = 'AR';
                }
                $tmpReNummer = sprintf("%'.010d", $curBookingNumber);
                $curReNummer = $preReNummer . $tmpReNummer;

                // Erzeuge neuen A Satz
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Satzart'] = 'A';
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Satzart']                    = 'A';                                      // Satzart
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Personenkonto']              = $bookingSet['KundenNummer'];              // Personenkonto
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Belegnummer']                = $bookingSet['KundenNummer'];              // Belegnummer
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Rechnungsnummer']            = $curReNummer;      // Rechnungsnummer
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Buchungsperiode']            = $curBuchungsperiode;                      // Buchungsperiode
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Belegdatum']                 = $curBelegdatum;                           // Belegdatum
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Buchungsdatum']              = $curBuchungsdatum;                        // Buchungsdatum
                // PFLICHT (Wird im B - Teil behandelt und gesetzt)
//                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Bruttobetrag'] = ''; // Bruttobetrag
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Waehrung']                   = $_SESSION['customConfig']['Centron']['Waehrung'];   // Waehrung
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Skonto']                     = '';    // Skontofähiger Betrag
                // PFLICHT
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Zahlungsbedingungen']        = $curZahlungsbedingungen;    // Zahlungsbedingungen
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWZahlungsart']             = '';   // Abweichende Zahlungsart
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Faelligkeit']                = '';   // Fälligkeit
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Valuta']                     = '';   // Valuta
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['PLZ']                        = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_PLZ'];      // PLZ
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Ort']                        = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Ort'];   // Ort
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Strasse']                    = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Strasse'];   // Strasse
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Hausnummer']                 = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Hausnummer'];   // Hausnummer
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Zusatzhausnummer']           = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Zusatzhausnummer'];   // Zusatzhausnummer
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Wohnungsnummer']             = '';   // Wohnungsnummer
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWKontoInhaberName1']       = '';   // Abweichen-der-Kontoinhaber_Name1
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWKontoInhaberName2']       = '';   // Abweichen-der-Kontoinhaber_Name2
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Laendercode']                = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Laendercode'];   // Laendercode
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWBLZ']                     = '';   // BLZ abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWKontoNr']                 = '';   // Konto_Nr abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWIBAN']                    = '';   // IBAN abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWAnschriftName1']          = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Name1_abw_Kontoinhaber'];   // Anschrift - Name 1 abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWAnschriftName2']          = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Name2_abw_Kontoinhaber'];   // Anschrift - Name 2 abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWAnschriftPLZ']            = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_PLZ_abw_Kontoinhaber'];   // Anschrift - PLZ abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWAnschriftOrt']            = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Ort_abw_Kontoinhaber'];   // Anschrift - Ort abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWAnschriftStrasse']        = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Strasse_abw_Kontoinhaber'];   // Anschrift - Strasse abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWAnschriftHausNr']         = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_Hnr_abw_Kontoinhaber'];   // Anschrift - HausNr. abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWAnschriftHausNrZusatz']   = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Anschrift_zus_Hnr_abw_Kontoinhaber'];   // Anschrift - Zus. HausNr. abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Prenotifcation']             = 'j';  // Prenotification erfolgt (J)
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['MandatsRefNr']               = $hCore->gCore['CustomerData'][$bookingSet['KundenNummer']]['Mandatsreferenznummer'];   // Mandatsreferenz-nummer
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['AnkZahlungseinzgZum']        = '';   // Anküendigung des Zahlungseinzugs zum
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['AnkZahlungseinzgAm']         = '';   // Ankündigung des Zahlungseinzugs am
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['ABWAnkZahlungseinzg']        = '';   // Ankündigung des Zahlunseinzugs am für den abw. Kontoinhaber
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['BuchungszeichenAvviso']      = '';   // Buchungszeichen Avviso








                // Erzeuge neuen B Satz
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['B']['Satzart']              = 'B';                            // Satzart
                //$hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['B']['Bruttobetrag']         = $bookingSet['Brutto'];   // Bruttobetrag
                //$hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['B']['Nettobetrag']        = '';                       // Nettobetrag
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['B']['Steuerkennzeichen']    = $bookingSet['MwSt'];      // Steuerkennzeichen
                $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['B']['Geschaeftsbereich']    = $_SESSION['customConfig']['Centron']['GeschaeftsbereichNonPrivate'];    // Geschäftsbereich


            }  //  END if ($curBookingNumber != $lastBookingNumber)





            // C Satz hinzufügen
            $brutto = $bookingSet['Brutto'];
            $brutto = $this->cleanMoney($brutto);

            $mwst = $bookingSet['MwSt'];
            $mwst = $this->cleanMoney($mwst);


            $prozentBerechnung = 100 + $mwst;
//            $curCSteuerbetrag =  $brutto * ($b/100);
            $curCSteuerbetrag =  $brutto * ($mwst/$prozentBerechnung);
            $curCSteuerbetrag = $this->cleanMoney($curCSteuerbetrag);
            $curCNetto = $brutto - $curCSteuerbetrag;
            $curCNetto = $this->cleanMoney($curCNetto);

            $curBNetto = 0;
            $curBBrutto = 0;
            if (isset($hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['B']['Nettobetrag'])){
                $curBNetto  = $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['B']['Nettobetrag'];
                $curBBrutto = $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['B']['Bruttobetrag'];
            }
            $curNewNetto = $curBNetto + $curCNetto;
            $curNewNetto = $this->cleanMoney($curNewNetto);
            $curNewBBrutto = $curBBrutto + $brutto;
            $curNewBBrutto = $this->cleanMoney($curNewBBrutto);

            // B Netto / Brutto Betrag:
            $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['B']['Nettobetrag'] = $curNewNetto;
            $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['B']['Bruttobetrag'] = $curNewBBrutto;



            // A Brutto Betrag
            if (isset($hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Bruttobetrag'])){
                $curABrutto = $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Bruttobetrag'];
            }
            else{
                $curABrutto = 0;
            }
            $curNewABrutto = $curABrutto + $brutto;
            $curNewABrutto = $this->cleanMoney($curNewABrutto);
            $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['A']['Bruttobetrag'] = $curNewABrutto;




            $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['C'][$indexC]['Satzart']        = 'C';
            $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['C'][$indexC]['Erloeskonto']    = $bookingSet['Erloeskonto'];          // Konto/Erlöskonto
            $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['C'][$indexC]['Nettobetrag']    = $curCNetto;                          // Nettobetrag
            $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['C'][$indexC]['Steuerbetrag']   = $curCSteuerbetrag;                   // Steuerbetrag
            $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['C'][$indexC]['KST']            = $bookingSet['Kostenstelle'];         // KST
            $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['C'][$indexC]['KTR']            = '';                                  // KTR
            $hCore->gCore['ExportBuchungsDaten']['Rechnungen'][$curBookingNumber]['C'][$indexC]['Buchungstext']   = $bookingSet['Buchungstext'];         // Buchungstext




            // C Index erhöhen
            $indexC++;

            // Verarbeitete Rechnungsnummer speichern
            $lastBookingNumber = $curBookingNumber;
            $lastCustomerNumber = $curCustomerNumber;

        }   // END foreach ($hCore->gCore['BuchungsDaten'] as $bookingSet){


        RETURN TRUE;

    }   // END private function generateSetA()




    private function cleanMoney($arg)
    {
        $arg = str_replace(",",".", $arg);
        $arg = round($arg, 2);
        $arg = number_format($arg, 2, '.', '');

        return $arg;
    }




    // CSV - Datei Buchungssatz erstellen
    private function generateBooginCSV()
    {

        $hCore = $this->hCore;


        if (!isset($hCore->gCore['ExportBuchungsDaten']['Rechnungen'])){
            RETURN FALSE;
        }


        $tilde = '~';

        $csv = '';

        $cntA = 0;
        $cntB = 0;
        $cntC = 0;
        $cntSum = 0;
        $sumBruttoA = 0;
        $sumBruttoB = 0;
        $sumNettoC = 0;
        $sumSteuerBetragC = 0;

        foreach ($hCore->gCore['ExportBuchungsDaten']['Rechnungen'] AS $index=>$set){

            // Für Prüfsumme berechnen
            $sumBruttoA += $set['A']['Bruttobetrag'];

            // A Satz erstellen
            $csv .= $set['A']['Satzart'] . $tilde;
            $csv .= $set['A']['Personenkonto'] . $tilde;
            $csv .= $set['A']['Belegnummer'] . $tilde;
            $csv .= $set['A']['Rechnungsnummer'] . $tilde;
            $csv .= $set['A']['Buchungsperiode'] . $tilde;
            $csv .= $set['A']['Belegdatum'] . $tilde;
            $csv .= $set['A']['Buchungsdatum'] . $tilde;
            $csv .= $set['A']['Bruttobetrag'] . $tilde;
            $csv .= $set['A']['Waehrung'] . $tilde;
            $csv .= $set['A']['Skonto']. $tilde;
            $csv .= $set['A']['Zahlungsbedingungen'] . $tilde;
            $csv .= $set['A']['ABWZahlungsart'] . $tilde;
            $csv .= $set['A']['Faelligkeit'] . $tilde;
            $csv .= $set['A']['Valuta'] . $tilde;
            $csv .= $set['A']['Valuta'] . $tilde;
            $csv .= $set['A']['PLZ'] . $tilde;
            $csv .= $set['A']['Ort'] . $tilde;
            $csv .= $set['A']['Strasse'] . $tilde;
            $csv .= $set['A']['Hausnummer'] . $tilde;
            $csv .= $set['A']['Zusatzhausnummer'] . $tilde;
            $csv .= $set['A']['Wohnungsnummer'] . $tilde;
            $csv .= $set['A']['ABWKontoInhaberName1'] . $tilde;
            $csv .= $set['A']['ABWKontoInhaberName2'] . $tilde;
            $csv .= $set['A']['Laendercode'] . $tilde;
            $csv .= $set['A']['ABWBLZ'] . $tilde;
            $csv .= $set['A']['ABWKontoNr'] . $tilde;
            $csv .= $set['A']['ABWIBAN'] . $tilde;
            $csv .= $set['A']['ABWAnschriftName1'] . $tilde;
            $csv .= $set['A']['ABWAnschriftName2'] . $tilde;
            $csv .= $set['A']['ABWAnschriftPLZ'] . $tilde;
            $csv .= $set['A']['ABWAnschriftOrt'] . $tilde;
            $csv .= $set['A']['ABWAnschriftStrasse'] . $tilde;
            $csv .= $set['A']['ABWAnschriftHausNr'] . $tilde;
            $csv .= $set['A']['ABWAnschriftHausNrZusatz'] . $tilde;
            $csv .= $set['A']['Prenotifcation'] . $tilde;
            $csv .= $set['A']['MandatsRefNr'] . $tilde;
            $csv .= $set['A']['AnkZahlungseinzgZum'] . $tilde;
            $csv .= $set['A']['AnkZahlungseinzgAm'] . $tilde;
            $csv .= $set['A']['ABWAnkZahlungseinzg'] . $tilde;
            $csv .= $set['A']['BuchungszeichenAvviso'] . $tilde;
            $csv .= "\r\n";
            $cntA++;




            // Für Prüfsumme berechnen
            $sumBruttoB += $set['B']['Bruttobetrag'];

            // B Satz erstellen
            $csv .= $set['B']['Satzart'] . $tilde;
            $csv .= $set['B']['Bruttobetrag'] . $tilde;
            $csv .= $set['B']['Nettobetrag'] . $tilde;
            $csv .= $set['B']['Steuerkennzeichen'] . $tilde;
            $csv .= $set['B']['Geschaeftsbereich'] . $tilde;
            $csv .= "\r\n";
            $cntB++;






            // C Satz erstellen
            foreach ($set['C'] as $indexC=>$valueC){

                // Für Prüfsumme berechnen
                $sumNettoC += $valueC['Nettobetrag'];

                // Für Prüfsumme berechnen
                $sumSteuerBetragC += $valueC['Steuerbetrag'];

                $csv .= $valueC['Satzart'] . $tilde;
                $csv .= $valueC['Erloeskonto'] . $tilde;
                $csv .= $valueC['Nettobetrag'] . $tilde;
                $csv .= $valueC['Steuerbetrag'] . $tilde;
                $csv .= $valueC['KST'] . $tilde;
                $csv .= $valueC['KTR'] . $tilde;
                $csv .= $valueC['Buchungstext'] . $tilde;
                $csv .= "\r\n";
                $cntC++;
            }



        }   // END foreach ($hCore->gCore['ExportBuchungsDaten']['KdNr'] AS $kdNummer=>$setArray){



        // Prüfsumme
        $csv .= "P~";
        $csv .= "~";                    // Gesamtanzahl der Sätze "S" innerhalb der Datei
        $csv .= $cntA . "~";            // Gesamtanzahl der Sätze "A" innerhalb der Datei
        $csv .= $sumBruttoA. "~";       // Gesamtsumme aller Bruttobeträge der Sätze "A"
        $csv .= $cntB . "~";            // Gesamtanzahl der Sätze "B" innerhalb der Datei
        $csv .= $sumBruttoB. "~";       // Gesamtsumme aller Bruttobeträge der Sätze "B"
        $csv .= $cntC . "~";            // Gesamtanzahl der Sätze "C" innerhalb der Datei
        $csv .= $sumNettoC. "~";        // Gesamtsumme aller Nettobeträge der Sätze "C"
        $csv .= $sumSteuerBetragC . "~";  // Gesamtsumme aller Steuerbeträge der Sätze "C"
        $csv .= "\r\n";


        $hCore->gCore['BookingCSV'] = $csv;

        RETURN TRUE;

    }   // END private function generateBooginCSV()







    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////











}   // END class DBExportDimari extends Core
