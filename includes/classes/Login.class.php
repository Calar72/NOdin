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
        if ($this->loginCheckLoginOnDB()) {

            RETURN TRUE;

        }


        RETURN FALSE;

    }





    // TODO DB Magin Check
    // Führt via Datenbank den - Login - Check durch
    private function loginCheckLoginOnDB()
    {

        $boolLoginOk = false;

        // Login & PW - DB Check
        $boolLoginOk = true;


        // Login durchführen (Loggen usw)
        if ($boolLoginOk){
            // Login in DB - Schreiben

            // Session Variable setzen
            // User - Relevante Daten
            $_SESSION['Login']['User']['userID']    = '1';
            $_SESSION['Login']['User']['userName']  = 'Calar';
            $_SESSION['Login']['User']['userEmail'] = 'markus.melching@tkrz.de';

            // User Rolle
            $_SESSION['Login']['User']['roleID']    = '1';
            $_SESSION['Login']['User']['roleName']  = 'Entwickler';

            // User Datum - Informationen
            $_SESSION['Login']['User']['dateCurLogin']  = '02.01.2016 13:34';
            $_SESSION['Login']['User']['dateLastLogin'] = '02.01.2016 12:34';
        }

        RETURN TRUE;
    }





}   // END class Action extends Core
