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
class DBImportDimari extends Core
{

    public $gDBImportDimari = array();

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

        $hCore = $this->hCore;

        // 2. Datei via csv-Import oeffnen
        //$param['KundenDaten'] = $this->readCSVFile($param);
        $param['KundenDaten'] = $this->hCore->gCore['csvValue'];


        // 3. Refferenz Array erstellen
        $getReturn 	= $this->getMapIndex($param);
        $param['cfgSatz'] 	= $getReturn['cfgSatz'];
        $param['refArray']  = $getReturn['refArray'];


        // Zeilen Index von Nummer auf Wort - Index aendern
        $param['KundenDaten'] = $this->mapIndex($param);


        // Uebregabe in DB Eintraaege erstellen
        $this->writeToDB($param);


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

    }   // END public function importFileToDB()





    // Datensatz in DB schreien
    function writeToDB($param)
    {
        $return = array();

        $myInsertTable 				= 'baseDataDimari';
        $curUserID 					= $_SESSION['Login']['User']['userID'];

        // Flag fuer erste Reihe koennte Headline sein - Pruefung
        $DoneFirstRowCheck = false;
        $tmpCnt = 0;
        foreach ($param['KundenDaten'] as $kundenCounter=>$Datensatz){

            $isHeadline = false;

            $initQuery = "INSERT INTO `".$myInsertTable."` ";

            $middleQueryA = " (`userID`, ";
            $middleQueryB = ") VALUES ('".$curUserID."', ";

            $dynUpdateQuery = '';

            foreach ($Datensatz as $curFieldname=>$value){

                // Erste Reihe koennte Headline sein - Pruefung=
                if (!$DoneFirstRowCheck){
                    $DoneFirstRowCheck = true;
                    if (!preg_match('/^\d+/', $value)){
                        $isHeadline = true;	// Durchlauf ueberspringen... erster Eintrag muss eine Headline sein... wir haben keine KD-Nr.
                    }
                }

                $middleQueryA .= "`" . $curFieldname . "`, ";
                $middleQueryB .= "'" . $value . "', ";

                $dynUpdateQuery .= "`" . $curFieldname . "` = '".$value."', ";

            }

            $endQuery = ')';

            // Letzte 2 Zeichen bei den Mittleeren Query - Variable entfernen
            $middleQueryA = substr($middleQueryA, 0, -2);
            $middleQueryB = substr($middleQueryB, 0, -2);
            $dynUpdateQuery = substr($dynUpdateQuery, 0, -2);

            $query = $initQuery . $middleQueryA. $middleQueryB . $endQuery;

            $query .= " ON DUPLICATE KEY UPDATE ".$dynUpdateQuery;

            if (!$isHeadline){
                // Gültiges Personenkonto?
                if ($Datensatz['PERSONENKONTO'] > 0){
                    $this->gCoreDB->query($query);
                    $tmpCnt++;
                }
            }

        }

        $return['SumImport'] = $tmpCnt;

        return $return;
    }





    // Erstellt "Wort-Index-Array" Anstelle von Nummer-Index
    function mapIndex($param)
    {
        $return = array();
        $newZeilen = array();

        // Kunden Counter
        $myCntCustomer = 0;

        // Oberster Durchlauf ... Kunden
        foreach ($param['KundenDaten'] as $kundenCounter){

            // Innerer Durchlauf ... Dateneintrag im Datensatz des Kunden
            foreach ($kundenCounter as $oldIndex=>$value){

                // ShortCut fuer CFG - Satzart
                $myType = key($param['cfgSatz']);

                // Ermittle den neuen Index
                $newIndex = $param['refArray'][$myType][$oldIndex];

                // Setze neue-Zeilen-Array mit dem Wert aus dem Import
                $newZeilen[$myCntCustomer][$newIndex] = $value;

            }

            // Kunden Counter
            $myCntCustomer++;
        }

        // Aufbereitete DatenArray zurueckgeben
        $return = $newZeilen;

        return $return;
    }





    function getMapIndex($param)
    {
        $return = array();

        // cfgSatz einlesen
        $cfgSatz = $this->readCFGSatz();

        // Refferenz Array fuer Change-Index erstellen
        $refArray = $this->generateChangeIndexCFGSatz($cfgSatz);

        $return['cfgSatz']  = $cfgSatz;
        $return['refArray'] = $refArray;

        return $return;
    }





    function generateChangeIndexCFGSatz($cfgSatz){

        foreach ($cfgSatz as $cfgSatzIndex=>$value){

            foreach ($value as $indexValue=>$egal){
                $refArray[$cfgSatzIndex][] = $indexValue;
            }

        }

        return ($refArray);
    }





    function readCFGSatz() {

        $cfgSatz = array();

        $query = "SELECT c.arrayIndex,
						c.indexKennung,
						c.typKennung,
						c.value,
						sourceType.shortCut

					FROM importConditionsDimari AS c

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





}   // END class DBImportDimari extends Core
