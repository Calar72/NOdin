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



        // Übergabe an Datenbank - Login - Abfrage und Login ok?
        if ($this->loginCheckLoginOnDB()) {

            RETURN TRUE;

        }
        else {

            echo "Ey dich kenne ich nicht!<br>";

            RETURN FALSE;
        }

    }





    // TODO DB Magin Check
    // Führt via Datenbank den - Login - Check durch
    private function loginCheckLoginOnDB()
    {

        // Hole mir die Query zum Login
        $query = $this->gCoreQuery->getQuery('loginCheckLoginOnDB');

        // Resultat der Login - Prüfung
        $result = $this->gCoreDB->query($query);

        // Betroffene Zeilen, bzw. erhaltene
        $num_rows = $this->gCoreDB->num_rows($result);


        // Kein gültiger Login!
        if ($num_rows != '1'){

            // Breche Methode hier ab und liefere false - Wert zurück

            RETURN FALSE;
        }


        // Login durchführen (Loggen usw)

        $row = $result->fetch_object();

        // Session Variable setzen
        // User - Relevante Daten
        $_SESSION['Login']['User']['userID']    = $row->userID;
        $_SESSION['Login']['User']['userName']  = $row->userName;
        $_SESSION['Login']['User']['userEmail'] = $row->userEmail;

         // User Rolle
        $_SESSION['Login']['User']['roleID']    = $row->roleID;
        $_SESSION['Login']['User']['roleName']  = $row->roleName;

        // Login - Vorgang in DB schreiben!
        $this->loginWriteUserLoginToDB($row->userID);

        // User Datum - Informationen der letzten Logins ermitteln
        $this->loginGetUserLastLogin($row->userID);
        /*
            $_SESSION['Login']['User']['dateCurLogin']  = '02.01.2016 12:34';
            $_SESSION['Login']['User']['dateLastLogin'] = '02.01.2016 12:34';
        */

        $this->gCoreDB->free_result($result);

        RETURN TRUE;

    }   // END private function loginCheckLoginOnDB()





    // Speichert den Login-Vorgang eines Benutzers
    private function loginWriteUserLoginToDB($curUserID)
    {

        // Übergabe Array erstellen
        $paramArray['Login']['User']['userID'] = $curUserID;

        // Hole mir die Query
        $query = $this->gCoreQuery->getQuery('loginWriteUserLoginToDB', $paramArray);

        // Führe Query aus
        $this->gCoreDB->query($query);

        RETURN TRUE;

    }	// END private function loginWriteUserLoginToDB(...)





    // Liest letzte Login-Informationen eines Betnutzers
    private function loginGetUserLastLogin($curUserID)
    {

        // Übergabe Array erstellen
        $paramArray['Login']['User']['userID'] = $curUserID;

        // Hole mir die Query
        $query = $this->gCoreQuery->getQuery('loginGetUserLastLogin', $paramArray);

        // Führe Query aus
        $result = $this->gCoreDB->query($query);

        if ($this->gCoreDB->num_rows($result) >= 1){

            $bGotLast = false;

            while($row = $result->fetch_object()){

                if (!$bGotLast){
                    // Speichere beide Info-Variable auf dateLastLogin
                    // Im zweiten Durchlauf der Schleife ist dann alles richtig.
                    // So fange ich ab, wenn der User sich zum ersten mal einlogt

                    $_SESSION['Login']['User']['dateCurLogin']  = $row->lastLogin;
                    $_SESSION['Login']['User']['dateLastLogin'] = $row->lastLogin;

                    $bGotLast = true;
                }
                else {
                    $_SESSION['Login']['User']['dateLastLogin'] = $row->lastLogin;
                }

            }

        }
        else {
            $this->gCoreDB->free_result($result);

            RETURN FALSE;
        }

        $this->gCoreDB->free_result($result);

        RETURN TRUE;

    }	// END private function loginGetUserLastLogin(...)




    // Logge User aus
    public function loginLogoutUser()
    {

        // Soll gleich wohin leiten?
        $redirectTo = $_SESSION['customConfig']['WebLinks']['EXTHOME'];

        // Session - Save initial mit Array
        $_SESSION = array();

        // Session - loeschen
        session_destroy();

        // Header Redirect
        header('Location: '.$redirectTo.'');

        exit;
    }
}   // END class Action extends Core
