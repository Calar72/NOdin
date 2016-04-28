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
        $query = "SELECT STATUSID FROM baseDataDimari WHERE 1 GROUP BY STATUSID";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            $Sammelkonten[] = '';
        }
        else{
            while($row = $result->fetch_object()){
                $Sammelkonten[] = $row->STATUSID;
            }
        }
        $hCore->gCore['baseDataInfo']['Sammelkonten'] = $Sammelkonten;
        $this->gCoreDB->free_result($result);




        // Zahlungsart
        $query = "SELECT ZAHLUNGS_ART FROM baseDataDimari WHERE 1 GROUP BY ZAHLUNGS_ART";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            $Zahlungsarten[] = '';
        }
        else{
            while($row = $result->fetch_object()){
                $Zahlungsarten[] = $row->ZAHLUNGS_ART;
            }
        }
        $hCore->gCore['baseDataInfo']['Zahlungsarten'] = $Zahlungsarten;
        $this->gCoreDB->free_result($result);

        RETURN TRUE;
    }





    // INITIAL
    public function doExportsBaseDataDimari()
    {
        // Tabellen Felder lesen
        $this->fetchDBFieldnames($_SESSION['customConfig']['Dimari']['baseDataIndexAdd']);


        // Daten aus DB lesen
        $this->readDBData();


        // Daten aufbereiten
        $this->refactorCustomerSet();


        // .csv jetzt vorbereiten
        $this->OBSchnittstelleDimariKonzeptum();

        RETURN TRUE;
    }


    private function refactorCustomerSet()
    {
        $hCore = $this->hCore;

        // Setzte Default Array
        $this->setExpFormat();


        foreach ($this->hCore->gCore['customerSet'] as $customerCnt=>$customerKey){

            foreach ($this->hCore->gCore['defaultCustomerData'] as $keyname=>$egal){

                $tmp = '';

                if (isset($this->hCore->gCore['customerSet'][$customerCnt][$keyname]))
                    $tmp = $this->hCore->gCore['customerSet'][$customerCnt][$keyname];


                // Sonderfall Firma
                if ($keyname == 'KD_NAME1'){
                    if ( (isset($this->hCore->gCore['customerSet'][$customerCnt]['FIRMENNAME'])) && (strlen($this->hCore->gCore['customerSet'][$customerCnt]['FIRMENNAME']) > 0) ){

                        // Setzte Name 1 auf Firmenname
                        $tmp = $this->hCore->gCore['customerSet'][$customerCnt]['FIRMENNAME'];

                        // Setze AP_ANREDE
                        if ( (isset($this->hCore->gCore['customerSet'][$customerCnt]['KD_ANREDE'])) && (!isset($this->hCore->gCore['customerSet'][$customerCnt]['AP_ANREDE'])) )
                            $this->hCore->gCore['customerSet'][$customerCnt]['AP_ANREDE'] = $this->hCore->gCore['customerSet'][$customerCnt]['KD_ANREDE'];

                        // Setze AP_BRIEF_ANREDE
                        if ( (isset($this->hCore->gCore['customerSet'][$customerCnt]['KD_BRIEF_ANREDE'])) && (!isset($this->hCore->gCore['customerSet'][$customerCnt]['AP_BRIEF_ANREDE'])) )
                            $this->hCore->gCore['customerSet'][$customerCnt]['AP_BRIEF_ANREDE'] = $this->hCore->gCore['customerSet'][$customerCnt]['KD_BRIEF_ANREDE'];

                        // Setze Strasse
                        if (isset($this->hCore->gCore['customerSet'][$customerCnt]['STREET'])) {
                            $xtmp = $this->hCore->gCore['customerSet'][$customerCnt]['STREET'];

                            if (isset($this->hCore->gCore['customerSet'][$customerCnt]['HAUSNUMMER']))
                                $xtmp .= ' ' . $this->hCore->gCore['customerSet'][$customerCnt]['HAUSNUMMER'];

                            if (isset($this->hCore->gCore['customerSet'][$customerCnt]['HAUSNUMMER_ZUSATZ']))
                                $xtmp .= ' ' . $this->hCore->gCore['customerSet'][$customerCnt]['HAUSNUMMER_ZUSATZ'];

                            $this->hCore->gCore['customerSet'][$customerCnt]['AP_STRASSE'] = $xtmp;
                        }



                        // Setze PLZ
                        if (isset($this->hCore->gCore['customerSet'][$customerCnt]['KD_PLZ'])) {
                            $this->hCore->gCore['customerSet'][$customerCnt]['AP_PLZ'] = $this->hCore->gCore['customerSet'][$customerCnt]['KD_PLZ'];
                        }


                        // Setze Ort
                        if (isset($this->hCore->gCore['customerSet'][$customerCnt]['KD_ORT'])) {
                            $this->hCore->gCore['customerSet'][$customerCnt]['AP_ORT'] = $this->hCore->gCore['customerSet'][$customerCnt]['KD_ORT'];
                            $this->hCore->gCore['customerSet'][$customerCnt]['AP_ORT'] = $this->hCore->gCore['customerSet'][$customerCnt]['KD_ORT'];
                        }


                        // Name 2 in AP_Vorname - Feld setzen
                        if (isset($this->hCore->gCore['customerSet'][$customerCnt]['KD_NAME2']))
                            $this->hCore->gCore['customerSet'][$customerCnt]['AP_VORNAME'] = $this->hCore->gCore['customerSet'][$customerCnt]['KD_NAME2'];

                        // Name 1 in AP_Vorname - Feld setzen
                        if (isset($this->hCore->gCore['customerSet'][$customerCnt]['KD_NAME1']))
                            $this->hCore->gCore['customerSet'][$customerCnt]['AP_NACHNAME'] = $this->hCore->gCore['customerSet'][$customerCnt]['KD_NAME1'];

                        // Verhindern dass KD_NAME2 mit dem Vornamen gefüllt wird
                        $this->hCore->gCore['customerSet'][$customerCnt]['KD_NAME2'] = '';

                        // TODO hier muss auf FTTH umgestellt werden
                        $this->hCore->gCore['customerSet'][$customerCnt]['ORG_STUFE'] = $_SESSION['customConfig']['Dimari']['ORG_STUFE_FIRMA'];
                        $this->hCore->gCore['customerSet'][$customerCnt]['ORG_EINHEIT_GRUPPE_ID'] = $_SESSION['customConfig']['Dimari']['ORG_EINHEIT_GRUPPE_ID_FIRMA'];
                        $this->hCore->gCore['customerSet'][$customerCnt]['BILLINGLAUF'] = $_SESSION['customConfig']['Dimari']['BILLINGLAUF_FIRMA'];

                    }
                    else {
                        // DEFAULT Keine Firma
                        $this->hCore->gCore['customerSet'][$customerCnt]['ORG_STUFE'] = $_SESSION['customConfig']['Dimari']['ORG_STUFE_PRIVAT'];
                        $this->hCore->gCore['customerSet'][$customerCnt]['ORG_EINHEIT_GRUPPE_ID'] = $_SESSION['customConfig']['Dimari']['ORG_EINHEIT_GRUPPE_ID_PRIVAT'];
                        $this->hCore->gCore['customerSet'][$customerCnt]['BILLINGLAUF'] = $_SESSION['customConfig']['Dimari']['BILLINGLAUF_PRIVAT'];
                    }
                }




                // Sonderfall Mandant_ID
                elseif ($keyname == 'MANDANT_ID'){
                    $tmp = $_SESSION['customConfig']['Dimari']['MANDANDT_ID'];
                }


                // Sonderfall MWST
                elseif ($keyname == 'MWST'){
                    $tmp = '19';
                }



                // Sonderfall Währung
                elseif ($keyname == 'WAEHRUNG'){
                    $tmp = 'EUR';
                }


                // Sonderfall Dokument_Gruppe
                elseif ($keyname == 'DOKUMENT_GRUPPE'){

                    // Firmen-Kunde?
                    if ( (isset($this->hCore->gCore['customerSet'][$customerCnt]['FIRMENNAME'])) && (strlen($this->hCore->gCore['customerSet'][$customerCnt]['FIRMENNAME']) > 0) )
                        $tmp = $_SESSION['customConfig']['Dimari']['DOKUMENT_GRUPPE_FIRMA'];

                    else
                        $tmp = $_SESSION['customConfig']['Dimari']['DOKUMENT_GRUPPE_PRIVAT'];

                }



                // Sonderfall Vorname in Briefanschrift
                elseif ($keyname == 'KD_VORNAME'){
                    if (isset($this->hCore->gCore['customerSet'][$customerCnt]['KD_NAME2'])){
                        if ($this->hCore->gCore['customerSet'][$customerCnt]['ORG_STUFE'] == 'P') {
                            $tmp = $this->hCore->gCore['customerSet'][$customerCnt]['KD_NAME2'];
                        }
                    }
                }



                // Sonderfall Nachname in Briefanschrift
                elseif ($keyname == 'KD_NACHNAME'){
                    if (isset($this->hCore->gCore['customerSet'][$customerCnt]['KD_NAME1'])){
                        if ($this->hCore->gCore['customerSet'][$customerCnt]['ORG_STUFE'] == 'P') {
                            $tmp = $this->hCore->gCore['customerSet'][$customerCnt]['KD_NAME1'];
                        }
                    }
                }




                // Sonderfall Strasse
                elseif ($keyname == 'KD_STRASSE'){
                    $tmp = $this->hCore->gCore['customerSet'][$customerCnt]['STREET'];

                    if (isset($this->hCore->gCore['customerSet'][$customerCnt]['HAUSNUMMER']))
                        $tmp .= ' ' . $this->hCore->gCore['customerSet'][$customerCnt]['HAUSNUMMER'];

                    if (isset($this->hCore->gCore['customerSet'][$customerCnt]['HAUSNUMMER_ZUSATZ']))
                        $tmp .= ' ' . $this->hCore->gCore['customerSet'][$customerCnt]['HAUSNUMMER_ZUSATZ'];
                }




                // Sonderfall Zahlungsart
                elseif ($keyname == 'ZAHLUNGS_ART'){
                    // 0 = Überweisung
                    if ($this->hCore->gCore['customerSet'][$customerCnt]['ZAHLUNGS_ART'] == '0')
                        $tmp = 'M';

                    // 1 = Lastschrift
                    elseif ($this->hCore->gCore['customerSet'][$customerCnt]['ZAHLUNGS_ART'] == '1')
                        $tmp = 'LB';

                    // Unbekannt ... setze auf Lastschrift
                    else
                        $tmp = 'LB';

                    // Sonderfall Kunde 20011198 KUNDEN_NR
                    if ($this->hCore->gCore['customerSet'][$customerCnt]['KUNDEN_NR'] == '20011198'){
                        $this->hCore->gCore['customerSet'][$customerCnt]['ZAHLUNGS_ART'] == '0';
                        $tmp = 'M';
                    }
                }



                // Sonderfall ... Kontoinhaber leer bei Lastschrift
                elseif ($keyname == 'INHABER_KONTO'){
                    // Zahlungsart gesetzt?
                    if (isset($this->hCore->gCore['customerSet'][$customerCnt]['ZAHLUNGS_ART'])){

                        // Ist Lastschrift?
                        if ($this->hCore->gCore['customerSet'][$customerCnt]['ZAHLUNGS_ART'] == '1'){

                            $xLen = strlen($this->hCore->gCore['customerSet'][$customerCnt]['INHABER_KONTO']);

                            // Kontoinhaber leer?
                            if ($xLen < 1){

                                $newKotoInhaA = '';
                                $newKotoInhaB = '';
                                $boolGotA = false;
                                $boolGotB = false;
                                if (isset($this->hCore->gCore['customerSet'][$customerCnt]['KD_NAME2'])){
                                    $newKotoInhaB = $this->hCore->gCore['customerSet'][$customerCnt]['KD_NAME2'];
                                    $boolGotB = true;
                                }
                                if (isset($this->hCore->gCore['customerSet'][$customerCnt]['KD_NAME1'])){
                                    $newKotoInhaA = $this->hCore->gCore['customerSet'][$customerCnt]['KD_NAME1'];
                                    $boolGotA = true;
                                }

                                $tmp = $newKotoInhaB;
                                if ( ($boolGotA) && ($boolGotB) )
                                    $tmp .= ' ';

                                $tmp .= $newKotoInhaA;
                            }

                        }

                    }

                }



                // Sonderfall SEPA Unterschrift am
                elseif ($keyname == 'SEPA_UNTERCHIFT_AM'){
                    if (isset($this->hCore->gCore['customerSet'][$customerCnt]['SEPA_GUELTIG_AB']))
                        $tmp = $this->hCore->gCore['customerSet'][$customerCnt]['SEPA_GUELTIG_AB'];
                }



                // Sonderfall EGN (Einzel Gesprächs Nachweiss)
                elseif ($keyname == 'EGN'){

                    // Default auf "aus"
                    $tmp = 'J';

                    // 2016.03.04 Laut Lars immer AN ... folgender Block wird also auskommentiert!!!
//                    if (isset($this->hCore->gCore['customerSet'][$customerCnt]['EGN'])){
//                        $curEGN = $this->hCore->gCore['customerSet'][$customerCnt]['EGN'];
//
//                        if ($curEGN == '1')
//                            $tmp = 'J';
//
//                    }
                }



                // Sonderfall Varsandart
                elseif ($keyname == 'VERSANDART'){
                    if ($this->hCore->gCore['customerSet'][$customerCnt]['VERSANDART'] == 'Online')
                        $tmp = 'W';

                    elseif ($this->hCore->gCore['customerSet'][$customerCnt]['VERSANDART'] == 'Email'){
                        $tmp = 'E';

                        // Fange ab, wenn per Email die Rechnung geschickt werden soll, wir aber keien Emailadresse haben... dann Rechnung via Webportal
                        if ( (!isset($this->hCore->gCore['customerSet'][$customerCnt]['EMAIL'])) || (strlen($this->hCore->gCore['customerSet'][$customerCnt]['EMAIL'])<2) ) {
                            $tmp = 'W';
                        }
                    }

                    elseif ($this->hCore->gCore['customerSet'][$customerCnt]['VERSANDART'] == 'Papier')
                        $tmp = 'P';
                }


                // Sonderfall Telefonnummern
                elseif ($keyname == 'KD_TEL'){

                    if (isset($this->hCore->gCore['customerSet'][$customerCnt]['KD_TEL'])) {
                        $tmp = $this->hCore->gCore['customerSet'][$customerCnt]['KD_TEL'];
                        $tmp = trim($tmp);
                        $search = '/^\+49 /';
                        $replace = '0';
                        $tmp = preg_replace($search, $replace, $tmp);

                        $search = '/^02572/';
                        $replace = '02572/';
                        $tmp = preg_replace($search, $replace, $tmp);

                        $search = '/^05971/';
                        $replace = '05971/';
                        $tmp = preg_replace($search, $replace, $tmp);

                        $search = '/^05923/';
                        $replace = '05923/';
                        $tmp = preg_replace($search, $replace, $tmp);

                        $search = '/ /';
                        $replace = '/';
                        $tmp = preg_replace($search, $replace, $tmp);

                        $search = '/-/';
                        $replace = '/';
                        $tmp = preg_replace($search, $replace, $tmp);

                        $search = '/\/\//';
                        $replace = '/';
                        $tmp = preg_replace($search, $replace, $tmp);
                    }

                }



                // Sonderfall Fax
                elseif ($keyname == 'KD_FAX'){

                    if (isset($this->hCore->gCore['customerSet'][$customerCnt]['KD_FAX'])) {
                        $tmp = $this->hCore->gCore['customerSet'][$customerCnt]['KD_FAX'];
                        $tmp = trim($tmp);
                        $search = '/^\+49 /';
                        $replace = '0';
                        $tmp = preg_replace($search, $replace, $tmp);

                        $search = '/^02572/';
                        $replace = '02572/';
                        $tmp = preg_replace($search, $replace, $tmp);

                        $search = '/^05971/';
                        $replace = '05971/';
                        $tmp = preg_replace($search, $replace, $tmp);

                        $search = '/^05923/';
                        $replace = '05923/';
                        $tmp = preg_replace($search, $replace, $tmp);

                        $search = '/ /';
                        $replace = '/';
                        $tmp = preg_replace($search, $replace, $tmp);

                        $search = '/-/';
                        $replace = '/';
                        $tmp = preg_replace($search, $replace, $tmp);

                        $search = '/\/\//';
                        $replace = '/';
                        $tmp = preg_replace($search, $replace, $tmp);
                    }

                }



                // Sonderfall Mobil
                elseif ($keyname == 'KD_MOBIL'){

                    if (isset($this->hCore->gCore['customerSet'][$customerCnt]['KD_MOBIL'])) {
                        $tmp = $this->hCore->gCore['customerSet'][$customerCnt]['KD_MOBIL'];
                        $tmp = trim($tmp);
                        $preSearch = '/^\+49 (.*)+/';
                        $search = '/^\+49 /';
                        $replace = '0';
                        if (preg_match($preSearch, $tmp))
                            $tmp = preg_replace($search, $replace, $tmp);

                        $tmp = trim($tmp);
                        $search = '/^49 /';
                        $replace = '0';
                        $tmp = preg_replace($search, $replace, $tmp);


                        $search = '/^02572/';
                        $replace = '02572/';
                        $tmp = preg_replace($search, $replace, $tmp);

                        $search = '/^05971/';
                        $replace = '05971/';
                        $tmp = preg_replace($search, $replace, $tmp);

                        $search = '/^05923/';
                        $replace = '05923/';
                        $tmp = preg_replace($search, $replace, $tmp);

                        $search = '/ /';
                        $replace = '/';
                        $tmp = preg_replace($search, $replace, $tmp);

                        $search = '/-/';
                        $replace = '/';
                        $tmp = preg_replace($search, $replace, $tmp);

                        $search = '/\/\//';
                        $replace = '/';
                        $tmp = preg_replace($search, $replace, $tmp);
                    }

                }


                $this->hCore->gCore['newCustomerSet'][$customerCnt][$keyname] = utf8_encode($tmp);

            }

        }

        $emptyArray = array();
        $this->hCore->gCore['customerSet'] = $emptyArray;
    }








    private function setExpFormat()
    {
        $hCore = $this->hCore;

        $this->hCore->gCore['defaultCustomerData']['KD_NAME1']                  = '';
        $this->hCore->gCore['defaultCustomerData']['KD_NAME2']                  = '';
        $this->hCore->gCore['defaultCustomerData']['KD_NAME3']                  = '';
        $this->hCore->gCore['defaultCustomerData']['KUNDEN_NR']                 = '';

        $this->hCore->gCore['defaultCustomerData']['ORG_STUFE']                 = '';
        $this->hCore->gCore['defaultCustomerData']['KURZBEZEICHNUNG']           = '';
        $this->hCore->gCore['defaultCustomerData']['BEMERKUNG']                 = '';
        $this->hCore->gCore['defaultCustomerData']['MANDANT_ID']                = '';
        $this->hCore->gCore['defaultCustomerData']['MWST']                      = '';
        $this->hCore->gCore['defaultCustomerData']['WAEHRUNG']                  = '';
        $this->hCore->gCore['defaultCustomerData']['ORG_EINHEIT_GRUPPE_ID']     = '';
        $this->hCore->gCore['defaultCustomerData']['DOKUMENT_GRUPPE']           = '';

        $this->hCore->gCore['defaultCustomerData']['KD_ANREDE']                 = '';
        $this->hCore->gCore['defaultCustomerData']['KD_BRIEF_ANREDE']           = '';
        $this->hCore->gCore['defaultCustomerData']['KD_TITEL']                  = '';
        $this->hCore->gCore['defaultCustomerData']['KD_VORNAME']                = '';
        $this->hCore->gCore['defaultCustomerData']['KD_NACHNAME']               = '';
        $this->hCore->gCore['defaultCustomerData']['KD_STRASSE']                = '';
        $this->hCore->gCore['defaultCustomerData']['KD_PLZ']                    = '';
        $this->hCore->gCore['defaultCustomerData']['KD_ORT']                    = '';
        $this->hCore->gCore['defaultCustomerData']['KD_POSTFACH']               = '';
        $this->hCore->gCore['defaultCustomerData']['KD_PLZ_POSTFACH']           = '';
        $this->hCore->gCore['defaultCustomerData']['KD_TEL']                    = '';
        $this->hCore->gCore['defaultCustomerData']['KD_FAX']                    = '';
        $this->hCore->gCore['defaultCustomerData']['KD_MOBIL']                  = '';
        $this->hCore->gCore['defaultCustomerData']['KD_EMAIL']                  = '';
        $this->hCore->gCore['defaultCustomerData']['KD_WWW_ADRESSE']            = '';
        $this->hCore->gCore['defaultCustomerData']['KD_GEBURT_DATUM']           = '';
        $this->hCore->gCore['defaultCustomerData']['KD_ABTEILUNG_POSITION']     = '';
        $this->hCore->gCore['defaultCustomerData']['KD_ADR_BEZEICHNUNG']        = '';

        $this->hCore->gCore['defaultCustomerData']['AP_ANREDE']                 = '';
        $this->hCore->gCore['defaultCustomerData']['AP_BRIEF_ANREDE']           = '';
        $this->hCore->gCore['defaultCustomerData']['AP_TITEL']                  = '';
        $this->hCore->gCore['defaultCustomerData']['AP_VORNAME']                = '';
        $this->hCore->gCore['defaultCustomerData']['AP_NACHNAME']               = '';
        $this->hCore->gCore['defaultCustomerData']['AP_STRASSE']                = '';
        $this->hCore->gCore['defaultCustomerData']['AP_PLZ']                    = '';
        $this->hCore->gCore['defaultCustomerData']['AP_ORT']                    = '';
        $this->hCore->gCore['defaultCustomerData']['AP_POSTFACH']               = '';
        $this->hCore->gCore['defaultCustomerData']['AP_PLZ_POSTFACH']           = '';
        $this->hCore->gCore['defaultCustomerData']['AP_ABTEILUNG_POSITION']     = '';
        $this->hCore->gCore['defaultCustomerData']['AP_TEL']                    = '';
        $this->hCore->gCore['defaultCustomerData']['AP_FAX']                    = '';
        $this->hCore->gCore['defaultCustomerData']['AP_MOBIL']                  = '';
        $this->hCore->gCore['defaultCustomerData']['AP_EMAIL']                  = '';
        $this->hCore->gCore['defaultCustomerData']['AP_WWW_ADRESSE']            = '';
        $this->hCore->gCore['defaultCustomerData']['AP_GEBURT_DATUM']           = '';
        $this->hCore->gCore['defaultCustomerData']['AP_ADR_BEZEICHNUNG']        = '';

        $this->hCore->gCore['defaultCustomerData']['VETRAG_NR']                 = '';
        $this->hCore->gCore['defaultCustomerData']['KREDITINSTITUTNAME']        = '';
        $this->hCore->gCore['defaultCustomerData']['BLZ']                       = '';
        $this->hCore->gCore['defaultCustomerData']['KTONR']                     = '';
        $this->hCore->gCore['defaultCustomerData']['IBAN']                      = '';
        $this->hCore->gCore['defaultCustomerData']['BIC']                       = '';
        $this->hCore->gCore['defaultCustomerData']['ZAHLUNGS_ART']              = '';
        $this->hCore->gCore['defaultCustomerData']['INHABER_KONTO']             = '';
        $this->hCore->gCore['defaultCustomerData']['ZAHLUNGSZIEL_TAGE']         = '';

        $this->hCore->gCore['defaultCustomerData']['SEPA_MANDATSREFERENZ']      = '';
        $this->hCore->gCore['defaultCustomerData']['SEPA_UNTERCHIFT_AM']        = '';
        $this->hCore->gCore['defaultCustomerData']['SEPA_GUELTIG_AB']           = '';

        $this->hCore->gCore['defaultCustomerData']['BILLINGLAUF']               = '';
        $this->hCore->gCore['defaultCustomerData']['EGN']                       = '';
        $this->hCore->gCore['defaultCustomerData']['VERSANDART']               = '';

        $this->hCore->gCore['defaultCustomerData']['WEB_BENUTZER']               = '';
        $this->hCore->gCore['defaultCustomerData']['WEB_BENUTZER_PASSWORT']      = '';

    }









    private function readDBData()
    {
        $hCore = $this->hCore;

        $query = "SELECT * FROM `baseDataDimari` WHERE 1 ORDER BY baseDataDimariID";
        $result = $this->gCoreDB->query($query);
        $num_rows = $this->gCoreDB->num_rows($result);

        if (!$num_rows >= '1'){
            $customerSet[] = '';
        }
        else{
            $cnt = 0;
            while($row = $result->fetch_array(MYSQLI_ASSOC)){

                foreach ($this->hCore->gCore['DBFieldnames'] as $index=>$DBFielname){
                    $customerSet[$cnt][$DBFielname] = $row[$DBFielname];
                }

                $cnt++;
            }
        }

        $hCore->gCore['customerSet'] = $customerSet;

        // $hCore->gCore['baseDataInfo']['userNames'] = $userNames;
        $this->gCoreDB->free_result($result);
    }





    // Ermittelt die Feldnamen der Datenbank
    private function fetchDBFieldnames($noFirstRowsNum=0)
    {
        $hCore = $this->hCore;

        $query = "SHOW COLUMNS FROM `baseDataDimari`";

        // Resultat der Login - Prüfung
        $result = $this->gCoreDB->query($query);

        // Betroffene Zeilen, bzw. erhaltene
        $num_rows = $this->gCoreDB->num_rows($result);


        // Keine Import Datei gefunden!
        if (!$num_rows >= '1'){

            // Breche Methode hier ab und liefere false - Wert zurück

            RETURN FALSE;
        }


        // Ergebnis in $row speichern
        $curRow = 0;
        while ($row = $result->fetch_object()){
            $curRow++;
            if ($curRow > $noFirstRowsNum)
                $this->hCore->gCore['DBFieldnames'][] =  $row->Field;
        }

        $this->gCoreDB->free_result($result);

    }







    private function OBSchnittstelleDimariKonzeptum()
    {
        $oracle = '';

        // Kunden Reihen durchgehen
        foreach ($this->hCore->gCore['newCustomerSet'] as $customerCnt=>$customerSetArray){

            $leadingPipe = false;

            // Kunden einzelne Daten durchgen
            foreach ($this->hCore->gCore['newCustomerSet'][$customerCnt] as $keyFieldname=>$value){


                // Pipezeichen setzen?
                if ($leadingPipe)
                    $oracle .= '|';


                $oracle .= '"' . utf8_decode(trim($value)) . '"';


                // Ab jetzt Pipezeichen setzen!
                $leadingPipe = true;
            }

            $oracle .= "\r\n";
        }


        $downloadLink = 'DimariStammdatenExport';

        // '/var/www/html/www/uploads/';
        $exportpath = $_SESSION['customConfig']['WebLinks']['MAINUPLOADPATH'];
        $storeFile = 'uploads/' . $downloadLink . '_exp.csv';
        $storeFileCSV = 'uploads/' . $downloadLink . 'CSV_exp.csv';
        $newDownloadLink = $_SESSION['customConfig']['WebLinks']['EXTHOMESHORT'].$storeFile;
        $newDownloadLink_csv = $_SESSION['customConfig']['WebLinks']['EXTHOMESHORT'].$storeFileCSV;

        $fp = fopen($storeFile, 'w');
        fwrite($fp, $oracle);
        fclose($fp);


        // Message Ausgabe vorebeiten
        $hCore = $this->hCore;
        $hCore->gCore['Messages']['Type'][]      = 'Done';
        $hCore->gCore['Messages']['Code'][]      = 'DBExport';
        $hCore->gCore['Messages']['Headline'][]  = 'DB - Export <i class="fa fa-arrow-right"></i> A <i class="fa fa-arrow-right"></i> B';

        $info = 'DB - Export erfolgreich!<br>Die Datei kann jetzt <a href="'.$newDownloadLink.'" class="std" target=_blank>HIER</a> heruntergeladen werden!<br>';
        $info .= 'Die .csv - Datei ist <a href="'.$newDownloadLink_csv.'" class="std" target=_blank>HIER</a>';
        $hCore->gCore['Messages']['Message'][]   = $info;


        $hCore->gCore['getLeadToBodySite']          = 'includes/html/home/homeBody';    // Webseite die geladen werden soll

        $this->writeOracleToCSV();

        // Speicher freimachen
        $this->hCore->gCore['newCustomerSet'] = '';

        return $oracle;

    }   // END private function OBSchnittstelleDimariKonzeptum()









    private function writeOracleToCSV()
    {
        $csv = '';

        $leadingPipe = false;

        // Headline erzeugen
        foreach ($this->hCore->gCore['defaultCustomerData'] as $keyName=>$value){

            // Pipezeichen setzen?
            if ($leadingPipe)
                $csv .= ';';

            $csv .= '"' . utf8_encode(trim($keyName)) . '"';

            // Ab jetzt Pipezeichen setzen!
            $leadingPipe = true;
        }
        $csv .= "\r\n";




        // Kunden Reihen durchgehen
        foreach ($this->hCore->gCore['newCustomerSet'] as $customerCnt=>$customerSetArray){

            $leadingPipe = false;

            // Kunden einzelne Daten durchgen
            foreach ($this->hCore->gCore['newCustomerSet'][$customerCnt] as $keyFieldname=>$value){


                // Pipezeichen setzen?
                if ($leadingPipe)
                    $csv .= ';';


                $csv .= '"' . utf8_decode(trim($value)) . '"';


                // Ab jetzt Pipezeichen setzen!
                $leadingPipe = true;
            }

            $csv .= "\r\n";

        }

        $downloadLink = 'DimariStammdatenExportCSV';

        // '/var/www/html/www/uploads/';
        $exportpath = $_SESSION['customConfig']['WebLinks']['MAINUPLOADPATH'];
        $storeFile = 'uploads/' . $downloadLink . '_exp.csv';
        $newDownloadLink = $_SESSION['customConfig']['WebLinks']['EXTHOMESHORT'].$storeFile;

        $fp = fopen($storeFile, 'w');
        fwrite($fp, $csv);
        fclose($fp);

        return $csv;
    }








}   // END class DBExportDimari extends Core
