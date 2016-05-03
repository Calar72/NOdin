<?php

/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 15:08
 *
 * Vererbungsfolge der (Basis) - Klassen:
 *    Base                                                Adam/Eva
 *    '-> SystemConfig                                    Child
 *        '-> DefaultConfig                                Child
 *            '-> Messages                                Child
 *                '-> Debug                                Child
 *                        '-> MySQLDB                            Child
 *                        '-> Query                        Child
 *                            '-> Core                    Child
 * ===>                                |-> ConcreteClass1        Core - Child - AnyCreature
 *                                    |-> ...                    Core - Child - AnyCreatures
 *                                    |-> ConcreteClass20        Core - Child - AnyCreature
 *
 */
class DBImportCentron extends Core
{

	public $gDBImportCentron = array();

	private $hCore;                // Privates Core Objekt










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

		RETURN true;

	}










	// INITIAL Daten (Stammdaten) aufbereiten und in DB speichern
	public function importBaseDataFileToDB()
	{

		// OBSchnittstelle klassenspezifisch aufrufen
		$this->OBSchnittstelleBaseDataCentron();

		RETURN true;

	}   // END public function importFileToDB()










	// INITIAL Daten (Buchungssatz) aufbereiten und in DB speichern
	public function importBookingDataFileToDB()
	{

		// OBSchnittstelle klassenspezifisch aufrufen
		$this->OBSchnittstelleBookingDataCentron();

		RETURN true;

	}   // END public function importBookingDataFileToDB()










	// csv Daten aufbereiten
	private function OBSchnittstelleBaseDataCentron()
	{

		$hCore = $this->hCore;


		// Lastschriftmandate einlesen
		$query = "SELECT * FROM centron_mand_ref WHERE activeStatus = 'yes' ORDER BY personenkonto";
		$result = $this->gCoreDB->query($query);
		$num_rows = $this->gCoreDB->num_rows($result);

		if ($num_rows >= '1') {
			$mandRefArray = array();
			while ($row = $result->fetch_object()) {

				$mandRefArray[$row->personenkonto] = $row->mandatsnummer;
			}
		}
		$this->gCoreDB->free_result($result);

//

		$hDB = '';
		$hMessage = '';
		$zeilen = $hCore->gCore['csvValue'];
		$downloadLink = $hCore->gCore['curDownloadLink'];
		$IDt = $hCore->gCore['curSourceTypeID'];
		$IDs = $hCore->gCore['curSourceSystemID'];

		$csv = "";

		$cnt_kunden = 0;

		// Die erste Reihe in der .csv - Datei ist eine "Ueberschrift"?
		$skipHeadline = false;

		$errorArray = array();

		// Setting in welcher Spalte steht was?
		$setRowKDNummer = 0;
		$setRowName1 = 1;
		$setRowStrasseHnr = 2;
		$setRowPLZ = 3;
		$setRowOrt = 4;
		$setRowTelefon = 5;
		$setRowEmail = 6;
		$setRowZahlungstyp = 7;
		$setIBAN = 8;
		$setSWIFT = 9;
		$setLandCode = 10;
		$setLandName = 11;
		$setBKLZ = 12;
		$setBankNr = 13;


		// Tabelle leeren!
		$query = "TRUNCATE TABLE `baseDataCentron`";

		// Tabelle leeren!
		$this->gCoreDB->query($query);


		////////////////////////////////////////////////////////////////////
		foreach($zeilen as $kunde) {
			$daten['errorArray']['Kd.-Nr.'] = array();

			// Headline in Rohdatei? Wenn ja, überspringe ich die erste Zeile
			if (($skipHeadline) && ($cnt_kunden == 0)) {
				$skipHeadline = false;
				continue;
			}


			//TODO WORKAROUND für 3 kundennumern... keine Anschriftdaten!
			$tmpKdNr = trim($kunde[$setRowKDNummer]);
			if (($tmpKdNr == '10148') || ($tmpKdNr == '10371') || ($tmpKdNr == '10131')) {

				continue;
			}

			if (trim($kunde[$setRowKDNummer]) == "") {
				continue;
			}



			// Strassenstring auseinandernehmen
			if (!isset($kunde[$setRowStrasseHnr])) {
				$strassenname = '';
				$hausnummer = '';
				$hausnummerzusatz = '';
				$daten['errorArray']['Kd.-Nr.'] = $kunde[$setRowKDNummer];
			}
			else {
				$strassenname = trim($kunde[$setRowStrasseHnr]);
				$hausnummer = "";
				$hausnummerzusatz = "";
				$search = '/([^\d]+)(\d+)?([^\d]+)?/i';
				if (preg_match_all($search, $strassenname, $result)) {
					$strassenname = trim($result[1][0]);
					$hausnummer = trim($result[2][0]);
					$hausnummerzusatz = trim($result[3][0]);
				}
			}

			// Mandatsreferenznummer
			$curMandRef = '';
			if (isset($mandRefArray[$tmpKdNr])) {
				$curMandRef = $mandRefArray[$tmpKdNr];
			}


			// TODO ELEGANTER Datensatz bei Doppel abfangen
			if (strlen($strassenname) < 2) {
				continue;
			}

			// Escapen für DB - Insert z.B. bei: Up'n Nien Esch
			$strassenname = addslashes($strassenname);


			$cnt_kunden++;


			if (!isset($kunde[$setRowPLZ])) {
				$PLZ = '';
				$daten['errorArray']['Kd.-Nr.'] = $kunde[$setRowKDNummer];
			}
			else {
				$PLZ = trim($kunde[$setRowPLZ]);
			}


			if (!isset($kunde[$setRowOrt])) {
				$Ort = '';
				$daten['errorArray']['Kd.-Nr.'] = $kunde[$setRowKDNummer];
			}
			else {
				$Ort = trim($kunde[$setRowOrt]);
			}


			if (!isset($kunde[$setRowTelefon])) {
				$Telefon = '';
				$daten['errorArray']['Kd.-Nr.'] = $kunde[$setRowKDNummer];
			}
			else {
				$Telefon = trim($kunde[$setRowTelefon]);
			}


			if (!isset($kunde[$setRowEmail])) {
				$Email = '';
				$daten['errorArray']['Kd.-Nr.'] = $kunde[$setRowKDNummer];
			}
			else {
				$Email = trim($kunde[$setRowEmail]);
			}

			// Calar neu

			if (!isset($kunde[$setIBAN]))
				$IBAN = '';
			else
				$IBAN = trim($kunde[$setIBAN]);


			if (!isset($kunde[$setLandCode]))
				$Laendercode = '';
			else
				$Laendercode = trim($kunde[$setLandCode]);


			if (!isset($kunde[$setBankNr]))
				$Kontonummer = '';
			else
				$Kontonummer = trim($kunde[$setBankNr]);


			if (!isset($kunde[$setBKLZ]))
				$BLZ = '';
			else
				$BLZ = trim($kunde[$setBKLZ]);


			if (!isset($kunde[$setSWIFT]))
				$BIC = '';
			else
				$BIC = trim($kunde[$setSWIFT]);



			// Anschriftsname
			$anschrifts_name1 = trim($kunde[$setRowName1]);
			$anschrifts_name2 = "";
			if (strlen($anschrifts_name1) > 35) {
				$anschrifts_name2 = substr($anschrifts_name1, 35);
				$anschrifts_name1 = substr($anschrifts_name1, 0, 35);
			}


			$name1 = trim($kunde[$setRowName1]);
			$name2 = "";
			if (strlen($name1) > 30) {
				$name2 = substr($name1, 30);
				$name1 = substr($name1, 0, 30);
			}

			$search = '/,$/i';
			if (preg_match_all($search, $name1, $result)) {
				$newValue = '';
				$name1 = preg_replace($search, $newValue, $name1);
			}

			$search = '/,$/i';
			if (preg_match_all($search, $name2, $result)) {
				$newValue = '';
				$name2 = preg_replace($search, $newValue, $name2);
			}


			if (count($daten['errorArray']['Kd.-Nr.']) > 0) {
				$errorArray[] = $daten['errorArray'];
			}


			$personenkonto = trim($kunde[$setRowKDNummer]);    // Personenkonto sprich Kundennummer


			// Welches System soll genutzt werden (das Alte nur mit SZ oder das Neue mit SZ und BL als Zahlart)
			if ($_SESSION['customConfig']['Centron']['ZahlungsartOldNew'] == 'new'){

					// Zahlungsart Lastschrift oder Überweisung?
				if (strlen($BIC)>0)
					$zahlungsart = $_SESSION['customConfig']['Centron']['ZahlungsartBL'];
				else
					$zahlungsart = $_SESSION['customConfig']['Centron']['Zahlungsart'];

			}
			else{
				$zahlungsart = $_SESSION['customConfig']['Centron']['Zahlungsart'];
			}




			$dynInsertQuery = "(
                                `userID`,
                                `Personenkonto`,
                                `Name1`,
                                `Name2`,
                                `Sammelkonto`,
                                `Laendercode`,
                                `BLZ`,
                                `BIC`,
                                `Kontonummer`,
                                `IBAN`,
                                `Zahlungsart`,
                                `Mandatsreferenznummer`,
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
                                '" . $_SESSION['Login']['User']['userID'] . "',
                                '" . $personenkonto . "',
                                '" . $name1 . "',
                                '" . $name2 . "',
                                '" . $_SESSION['customConfig']['Centron']['Sammelkonto'] . "',
                                '" . $Laendercode . "',
                                '" . $BLZ . "',
                                '" . $BIC . "',
                                '" . $Kontonummer . "',
                                '" . $IBAN . "',
                                '" . $zahlungsart . "',
                                '" . $curMandRef . "',
                                '" . $anschrifts_name1 . "',
                                '" . $anschrifts_name2 . "',
                                '" . $PLZ . "',
                                '" . $Ort . "',
                                '" . $strassenname . "',
                                '" . $hausnummer . "',
                                '" . $hausnummerzusatz . "',
                                '" . $Telefon . "',
                                '" . $Email . "'
                                )
                                ";

			$dynUpdateQuery = "`userID`                 = '" . $_SESSION['Login']['User']['userID'] . "',
                               `Name1`                  = '" . $name1 . "',
                               `Name2`                  = '" . $name2 . "',
                               `Sammelkonto`            = '" . $_SESSION['customConfig']['Centron']['Sammelkonto'] . "',
                               `Laendercode`            = '" . $Laendercode . "',
                               `BLZ`                    = '" . $BLZ . "',
                               `BIC`                    = '" . $BIC . "',
                               `Kontonummer`            = '" . $Kontonummer . "',
                               `IBAN`                   = '" . $IBAN . "',
                               `Zahlungsart`            = '" . $zahlungsart . "',
                               `Anschrift_Name1`        = '" . $anschrifts_name1 . "',
                               `Anschrift_Name2`        = '" . $anschrifts_name2 . "',
                               `Anschrift_PLZ`          = '" . $PLZ . "',
                               `Anschrift_Ort`          = '" . $Ort . "',
                               `Anschrift_Strasse`      = '" . $strassenname . "',
                               `Anschrift_Hausnummer`   = '" . $hausnummer . "',
                               `Zusatzhausnummer`       = '" . $hausnummerzusatz . "',
                               `Telefon`                = '" . $Telefon . "',
                               `Email`                  = '" . $Email . "'
            ";


			//TODO Eintrag nur wenn kein Fehler passiert ist... das fange ich hier nicht ab!
			// DB Eintrag erstellen oder Updaten (Query erstellen)!
			$query = "INSERT INTO baseDataCentron " . $dynInsertQuery . " ON DUPLICATE KEY UPDATE " . $dynUpdateQuery;

			// DB Eintrag erstellen oder Updaten!
			$this->gCoreDB->query($query);

		}   // END foreach ($zeilen as $kunde){


		// Informationen aufbereiten
		$typeIndex = array_search($hCore->gCore['curSourceTypeID'], $hCore->gCore['LNav']['ConvertTypeID']);
		$typeInfo = $hCore->gCore['LNav']['ConvertType'][$typeIndex];

		$systemIndex = array_search($hCore->gCore['curSourceSystemID'], $hCore->gCore['LNav']['ConvertSystemID']);
		$systemInfo = $hCore->gCore['LNav']['ConvertSystem'][$systemIndex];



		// Fehler aufgetreten?
		if (count($errorArray) > 0) {

			//TODO .... prüfen ob das eine gute Idee ist den Import-Counter wirklich hochzusetzen
			// Import Counter aktuallisieren
			$query = "UPDATE fileUpload SET importCounter = importCounter+1 WHERE fileUploadID = '" . $hCore->gCore['getPOST']['sel_fileUploadID'] . "' LIMIT 1";
			$this->gCoreDB->query($query);

			$infoOut = '';
			foreach($errorArray as $key) {

				foreach($key as $varname => $info)
					$infoOut .= "<br>" . $varname . ": " . $info;

			}

			// Message Ausgabe vorebeiten
			$hCore->gCore['Messages']['Type'][] = 'Error';
			$hCore->gCore['Messages']['Code'][] = 'DBImport';
			$hCore->gCore['Messages']['Headline'][] = 'Fehler bei: DB - Import <i class="fa fa-arrow-right"></i> ' . $typeInfo . ' <i class="fa fa-arrow-right"></i> ' . $systemInfo;
			$hCore->gCore['Messages']['Message'][] = 'Fehler bei: DB - Import!<br>Export-Datei nicht erstellt! Fehler bei folgenden Kundennummer(n):<br>' . $infoOut;

			$hCore->gCore['getLeadToBodySite'] = 'includes/html/home/homeBody';    // Webseite die geladen werden soll
		}
		else {

			// Import Counter aktuallisieren
			$query = "UPDATE fileUpload SET importCounter = importCounter+1 WHERE fileUploadID = '" . $hCore->gCore['getPOST']['sel_fileUploadID'] . "' LIMIT 1";
			$this->gCoreDB->query($query);

			// Message Ausgabe vorebeiten
			$hCore->gCore['Messages']['Type'][] = 'Done';
			$hCore->gCore['Messages']['Code'][] = 'DBImport';
			$hCore->gCore['Messages']['Headline'][] = 'DB - Import <i class="fa fa-arrow-right"></i> ' . $typeInfo . ' <i class="fa fa-arrow-right"></i> ' . $systemInfo;
			$hCore->gCore['Messages']['Message'][] = 'DB - Import erfolgreich!<br>Die Datei kann jetzt über "DB - Export" exportiert werden!';

			$hCore->gCore['getLeadToBodySite'] = 'includes/html/home/homeBody';    // Webseite die geladen werden soll
		}


		RETURN true;

	}   // END private function OBSchnittstelleCentron()



	/////////////////////////////////// Buchungssatz //////////////////////////////////



	// Importiert .txt Buchungsdatei in DB
	private function OBSchnittstelleBookingDataCentron()
	{

		$hCore = $this->hCore;

		$zeilen = $hCore->gCore['csvValue'];

		// Tabelle leeren!
		$query = "TRUNCATE TABLE `bookingDataCentron`";

		// Tabelle leeren!
		$this->gCoreDB->query($query);

		foreach($zeilen as $bookingSet) {

			preg_match_all("/(\d+)\.(\d+)\.(\d+)/i", trim($bookingSet[0]), $splitDate);

			$Datum = '20' . $splitDate[3][0] . '-' . $splitDate[2][0] . '-' . $splitDate[1][0];
			$RechnungsNr = trim($bookingSet[1]);
			$Buchungstext = trim($bookingSet[2]);
			$Erloeskonto = trim($bookingSet[3]);
			$KundenNummer = trim($bookingSet[4]);
			$Brutto = trim($bookingSet[5]);
			$MwSt = trim($bookingSet[6]);
			$Kostenstelle = trim($bookingSet[7]);

			if (strlen($RechnungsNr) < 1) {
				continue;
			}

			// NULL - Werte abfangen
			if (strlen($Datum) < 1) {
				$Datum = '0000-00-00';
			}
			if (strlen($Buchungstext) < 1) {
				$Buchungstext = '0';
			}
			if (strlen($Erloeskonto) < 1) {
				$Erloeskonto = '0';
			}
			if (strlen($KundenNummer) < 1) {
				$KundenNummer = '0';
			}
			if (strlen($Brutto) < 1) {
				$Brutto = '0';
			}
			if (strlen($MwSt) < 1) {
				$MwSt = '0';
			}
			if (strlen($Kostenstelle) < 1) {
				$Kostenstelle = '0';
			}

			// Brutto Komma in Punkt umwandeln
			$Brutto = str_replace(",", ".", $Brutto);
			$Brutto = round($Brutto, 2);
			$Brutto = number_format($Brutto, 2, '.', '');

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
                                                      '" . $curTime . "',
                                                      '" . $_SESSION['Login']['User']['userID'] . "',
                                                      '" . $Datum . "',
                                                      '" . $RechnungsNr . "',
                                                      '" . $Buchungstext . "',
                                                      '" . $Erloeskonto . "',
                                                      '" . $KundenNummer . "',
                                                      '" . $Brutto . "',
                                                      '" . $MwSt . "',
                                                      '" . $Kostenstelle . "'
                                                      )";

			// DB Eintrag erstellen!
			$this->gCoreDB->query($query);


		}   // END foreach ($zeilen as $bookingSet){


		// Import Counter aktuallisieren
		$query = "UPDATE fileUpload SET importCounter = importCounter+1 WHERE fileUploadID = '" . $hCore->gCore['getPOST']['sel_fileUploadID'] . "' LIMIT 1";
		$this->gCoreDB->query($query);


		// Informationen aufbereiten
		$typeIndex = array_search($hCore->gCore['curSourceTypeID'], $hCore->gCore['LNav']['ConvertTypeID']);
		$typeInfo = $hCore->gCore['LNav']['ConvertType'][$typeIndex];

		$systemIndex = array_search($hCore->gCore['curSourceSystemID'], $hCore->gCore['LNav']['ConvertSystemID']);
		$systemInfo = $hCore->gCore['LNav']['ConvertSystem'][$systemIndex];

		// Message Ausgabe vorebeiten
		$hCore->gCore['Messages']['Type'][] = 'Done';
		$hCore->gCore['Messages']['Code'][] = 'DBImport';
		$hCore->gCore['Messages']['Headline'][] = 'DB - Import <i class="fa fa-arrow-right"></i> ' . $typeInfo . ' <i class="fa fa-arrow-right"></i> ' . $systemInfo;
		$hCore->gCore['Messages']['Message'][] = 'DB - Import erfolgreich!<br>Die Datei kann jetzt über "DB - Export" exportiert werden!';

		$hCore->gCore['getLeadToBodySite'] = 'includes/html/home/homeBody';    // Webseite die geladen werden soll

		RETURN true;

	}   // END private function OBSchnittstelleBookingDataCentron()


}   // END class DBImportCentron extends Core
