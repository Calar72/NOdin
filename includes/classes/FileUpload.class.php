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
class FileUpload extends Core
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





    // Führe den Datei - Upload durch
    public function fileUploadPerformUpload()
    {
        $hCore = $this->hCore;

        $uploaddir = $_SESSION['customConfig']['WebLinks']['MAINUPLOADPATH'];


        $filePrefix = 't';		// Timestamp
        $filePrefix .= time();

        $filePrefix .= 'IDt';	// ID Type 1=Stammdaten, 2=Buchungssatz ... more info in DB 'sourceType'
        $filePrefix .= $hCore->gCore['getGET']['subAction'];

        $filePrefix .= 'IDs';	// ID Source 1 = Dimari, 2 = Centron ... more info in DB 'source'
        $filePrefix .= $hCore->gCore['getGET']['valueAction'];

        $filePrefix .= 'IDu';	// ID User
        $filePrefix .= $_SESSION['Login']['User']['userID'];

        $filePrefix .= '_';


        $uploaddir = $this->checkUploadPath();


        $uploadfile = $uploaddir . $filePrefix . basename($_FILES['fileToUpload']['name']);


        // Wurde Datei zum Upload uebergeben?
        if ( (isset($_FILES['fileToUpload']['tmp_name'])) && ($_FILES['fileToUpload']['error'] == 0) ){

            // Informationen aufbereiten
            $typeIndex = array_search($hCore->gCore['getGET']['subAction'], $hCore->gCore['LNav']['ConvertTypeID']);
            $typeInfo = $hCore->gCore['LNav']['ConvertType'][$typeIndex];

            $systemIndex = array_search($hCore->gCore['getGET']['valueAction'], $hCore->gCore['LNav']['ConvertSystemID']);
            $systemInfo = $hCore->gCore['LNav']['ConvertSystem'][$systemIndex];

            $fileOrigName = basename($_FILES['fileToUpload']['name']);

            if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $uploadfile)) {

                // Message Ausgabe vorebeiten
                $hCore->gCore['Messages']['Type'][]      = 'Done';
                $hCore->gCore['Messages']['Code'][]      = 'Upload';
                $hCore->gCore['Messages']['Headline'][]  = 'Datei - Upload <i class="fa fa-arrow-right"></i> '.$typeInfo.' <i class="fa fa-arrow-right"></i> '.$systemInfo;
                $hCore->gCore['Messages']['Message'][]   = 'Datei - Upload erfolgreich!<br>Die Datei kann jetzt über "DB - Import" in die Datenbank importiert werden!';

                //DB Eintrag erstellen!
                $this->logFileBaseData($uploaddir, $uploadfile, $hCore->gCore['getGET']['valueAction'], $hCore->gCore['getGET']['subAction']);

                $hCore->gCore['getLeadToBodySite']          = 'includes/html/home/homeBody';    // Webseite die geladen werden soll
            }
            else {
                // Message Ausgabe vorebeiten
                $hCore->gCore['Messages']['Type'][]      = 'Error';
                $hCore->gCore['Messages']['Code'][]      = 'Upload';
                $hCore->gCore['Messages']['Headline'][]  = 'Fehler bei: Datei - Upload <i class="fa fa-arrow-right"></i> '.$typeInfo.' <i class="fa fa-arrow-right"></i> '.$systemInfo . '<br>PHP Datei - Fehler - Code:' . $_FILES['fileToUpload']['error'];
            }
        }
        else {
            // Message Ausgabe vorebeiten
            $hCore->gCore['Messages']['Type'][]      = 'Error';
            $hCore->gCore['Messages']['Code'][]      = 'Upload';
            $hCore->gCore['Messages']['Headline'][]  = 'Fehler bei: Datei - Upload <i class="fa fa-arrow-right"></i> '.$typeInfo.' <i class="fa fa-arrow-right"></i> '.$systemInfo . '<br>PHP Datei - Fehler - Code:' . $_FILES['fileToUpload']['error'];
        }

        RETURN TRUE;
    }   // END public function fileUploadPerformUpload()





    // Erstelle ggf. den aktuellen Ordner für den Upload
    private function checkUploadPath(){
        $return = false;

        $datetime = getdate();

        $thisYear 	= $datetime['year'];
        $thisMonth 	= $datetime['mon'];
        $thisDay 	= $datetime['mday'];

        $yearPath	= $_SESSION['customConfig']['WebLinks']['MAINUPLOADPATH'] . $thisYear;

        $monthPath 	= $yearPath . '/' . $thisMonth;
        $dayPath 	= $monthPath . '/' . $thisDay;

        // Jahr vorhanden?
        if (!is_dir($yearPath)){
            // Nein ... erstellen
            mkdir($yearPath);
        }
        // Ja ... Monat vorhanden?
        if (!is_dir($monthPath)){
            // Nein ... erstellen
            mkdir($monthPath);
        }
        // Ja ... Tag vorhanden?
        if (!is_dir($dayPath)){
            // Nein ... erstellen
            mkdir($dayPath);
        }

        $return = $dayPath . '/';

        return $return;
    }   // END  private function checkUploadPath(){





    // File - Upload in DB schreiben
    private function logFileBaseData($uploaddir, $uploadfile, $IDs, $IDt){

        $hCore = $this->hCore;

        $targetFileName = basename($uploadfile);

        $varSet = $uploadfile;
        $searchMatch = '/(.*?)(uploads\/)(.*)/';

        preg_match_all($searchMatch, $varSet, $match);

        $downloadLink = 'uploads/' . $match[3][0];

        $query = "INSERT INTO fileUpload (
									sourceSystemID,
									sourceTypeID,
									userID,
									uploadDateTime,
									fileOriginName,
									fileTmpName,
									fileTargetName,
									fileTargetPath,
									fileTargetFullPath,
									fileSize,
									downloadLink
								  ) VALUES (
									'".$IDs."',
									'".$IDt."',
								  	'".$_SESSION['Login']['User']['userID']."',
								  	now(),
								  	'".$_FILES['fileToUpload']['name']."',
								  	'".$_FILES['fileToUpload']['tmp_name']."',
								  	'".$targetFileName."',
								  	'".$uploaddir."',
								  	'".$uploadfile."',
								  	'".$_FILES['fileToUpload']['size']."',
								  	'".$downloadLink."')";

        // Resultat der Konvertierungs - Typen
        $this->gCoreDB->query($query);

        return true;

    }   // END private function logFileBaseData(...){





}   // END class FileUpload extends Core
