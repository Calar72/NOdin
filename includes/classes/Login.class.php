<?php

/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 15:08
 *
 * Vererbungsfolge der (Basis) - Klassen:
 *  	Base									Adam/Eva
 *  	'-> SystemConfig						Child
 *  	   	'-> DefaultConfig					Child
 *  			'-> Messages					Child
 *  				'-> Debug					Child
 *  					'-> Core				Child
 * 							|-> MySQLDB			Child
 * 	===>					|-> ConcreteClass1	Core - Child - AnyCreature
 * 							|-> ...				Core - Child - AnyCreatures
 * 							|-> ConcreteClass20	Core - Child - AnyCreature
 *
 */
class Login extends Core
{

    public $gLogin = array();

    private $hCore;	            // Privates Core Objekt





    function __construct($hCore)
    {

        // Debug - Classname ausgeben?!
        $this->debugInitOnLoad('Class', $this->getClassName(false));


        // Speichere das Öffentliche hCore - Objekt zur weiteren Verwendung lokal
        $this->hCore = $hCore;


        parent::__construct();

    }    // END function __construct()





    private function getMyClassName($printOnScreen=false)
    {

        if ($printOnScreen)
            print ("<br>Ich bin Klasse: " . __CLASS__ . "<br>") ;

        return __CLASS__;

    }	// END function getMyClassName(...)





    function getClassName($printOnScreen=false)
    {

        $myClassNmae = $this->getMyClassName($printOnScreen);

        return $myClassNmae;

    }	// END function getClassName(...)





    // NULL - Funktion ... wird benötigt in der Action - Steuerung und dient als Platzhalter bzw. als Default - Aufruf
    function doNothing()
    {

        RETURN TRUE;

    }





    // Liefert die Login - ID des Users
    function loginGetLoginUserID()
    {

        if ( (isset($_SESSION['Login']['User']['userID'])) && ($_SESSION['Login']['User']['userID']) > 0)
            RETURN $_SESSION['Login']['User']['userID'];

        RETURN FALSE;

    }   // END function getLoginUserID()





    //TODO Odentliche Messages
    // INITIAL Login Prozedur
    function loginInitCallLogin()
    {

        $hCore = $this->hCore;

        $boolUsernamePasswordWrong = false;


        // Loign wurde schon durchgeführt?
        if ($this->loginGetLoginUserID() > 0) {

            // Login schon vorhanden, habe in dieser Methode nichts zu suchen!
            RETURN TRUE;

        }



        // Prüfung: Benutzerdaten (Username) angegeben?
        if (!$this->checkLenMinMax($hCore->gCore['getPOST']['getUsername'], $_SESSION['customConfig']['Login']['MinLenUsername'], $_SESSION['customConfig']['Login']['MaxLenUsername'])){

            echo "Ey kein Username<br>";

            $boolUsernamePasswordWrong = true;

        }



        // Prüfung: Logindaten (Passwort) angegeben?
        if (!$this->checkLenMinMax($hCore->gCore['getPOST']['getPassword'], $_SESSION['customConfig']['Login']['MinLenPassword'], $_SESSION['customConfig']['Login']['MaxLenPassword'])){

            echo "Ey kein Passwort<br>";

            $boolUsernamePasswordWrong = true;

        }



        // Username und/oder Passwort nicht angeben und/oder entspricht nicht den Vorgaben?
        if ($boolUsernamePasswordWrong){

            RETURN FALSE;

        }



        // Übergabe an Datenbank - Login - Abfrage
        //TODO WENN FALSE
        if ($this->loginCheckLoginOnDB()) {

            RETURN TRUE;

        }


        RETURN FALSE;

    }





    // TODO DB Magin Check
    // Führt via Datenbank den - Login - Check durch
    private function loginCheckLoginOnDB()
    {

        $hCore = $this->hCore;

        // Login & PW - DB Check

        // Erzeuge MySQL - Objekt
        $hMySQLDB = new MySQLDB($hCore);

        // Erzeuge Query - Objekt
        $hQuery = new Query($hCore);

        // Hole mir die Query zum Login
        $query = $hQuery->getQuery('UserLogin');

        // Resultat der Login - Prüfung
        $result = $hMySQLDB->query($query,true);

        // Betroffene Zeilen, bzw. erhaltene
        $num_rows = $hMySQLDB->num_rows($result);



        // Kein gültiger Login
        if ($num_rows != '1'){

            //TODO Message und Weterleitung

            RETURN FALSE;
        }



        $row = $result->fetch_object();
/*

        // Login durchführen (Loggen usw)
        // Login in DB - Schreiben

        // Session Variable setzen
        // User - Relevante Daten
        $_SESSION['Login']['User']['userID']    = $row->userID;
        $_SESSION['Login']['User']['userName']  = $row->userName;
        $_SESSION['Login']['User']['userEmail'] = $row->userEmail;

         // User Rolle
        $_SESSION['Login']['User']['roleID']    = $row->roleID;
        $_SESSION['Login']['User']['roleName']  = $row->roleName;
*/

        // Login - Vorgang in DB schreiben!
        $this->writeUserLoginToDB($hMySQLDB, $hQuery, $row->userID);

        /*
        // User Datum - Informationen
        $_SESSION['Login']['User']['dateCurLogin']  = '02.01.2016 12:34';
        $_SESSION['Login']['User']['dateLastLogin'] = '02.01.2016 12:34';
        //TODO HIER FEIERABEND
*/
        $hMySQLDB->free_result($result);


        // Login - Loggen


        RETURN TRUE;
    }





    // Speichert den Login-Vorgang eines Benutzers
    private function writeUserLoginToDB($hMySQLDB, $hQuery, $curUserID)
    {

        // Übergabe Array erstellen
        $paramArray['Login']['User']['userID'] = $curUserID;

        // Hole mir die Query
        $query = $hQuery->getQuery('WriteUserLoginToDB', $paramArray);

        // Führe Query aus
        $hMySQLDB->query($query,true);

        RETURN TRUE;

    }	// END private function writeUserLoginToDB(...)





}   // END class Action extends Core
