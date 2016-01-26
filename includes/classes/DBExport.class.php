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




























    // INITIAL Daten Importieren
    public function OLD_getExports()
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


/*


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
        $Data = array_map('str_getcsv', file($filepath));



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
            }

            // Centron - System?
            elseif ($this->hCore->gCore['curSourceSystemID'] == '2'){
                //TODO Centron Stammdaten - Import
                $hDBImport = new DBImportCentron($hCore);
                $hDBImport->importBaseDataFileToDB();
            }

            // Webfakt - System?
            elseif ($this->hCore->gCore['curSourceSystemID'] == '2'){
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
            elseif ($this->hCore->gCore['curSourceSystemID'] == '2'){
                //TODO Centron Buchungssatz - Import
            }
        }

        RETURN TRUE;

    }   // END private function OBSchnittstellenController()

*/




}   // END class DBExport extends Core
