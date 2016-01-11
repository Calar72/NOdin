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
 * ===>						|-> MySQLDB			Child
 *  						|-> ConcreteClass1	Core - Child - AnyCreature
 * 							|-> ...				Core - Child - AnyCreatures
 * 							|-> ConcreteClass20	Core - Child - AnyCreature
 *
 */
class MySQLDB extends Core
{

    public $gMySQLDB = array();

    private $hCore;	            // Privates Core Objekt


    // Defniere Initial - Variable!
    // Achtung! Die tatsächlichen Werte werden in der 'includes/configs/customConfig.inc.php' gesetzt!
    private $DBHOST			= '';	// Datenbank Host
    private $DBNAME			= '';	// (Default) Datenbank Name
    private $DBUSER			= '';	// Datenbank Benutzer
    private $DBPASSWORD		= '';	// Datenbank Passwort


    // MySQLi neu in PHP 5.5
    private $mysqli         = NULL; // MySQLi Verbindungs - Objekt
    private $lastResult	    = '';   // MySQLi Letztes Resultat
    private $last_insert_id = 0;    // Letzte eingefügte ID in einer Tabelle




    function __construct($hCore)
    {

        // Debug - Classname ausgeben?!
        $this->debugInitOnLoad('Class', $this->getClassName(false));



        // Speichere das Öffentliche hCore - Objekt zur weiteren Verwendung lokal
        $this->hCore = $hCore;



        parent::__construct();



        // Setze Initial-Variable!
        $this->DBHOST		= $_SESSION['customConfig']['DBSettings']['DBHOST'];
        $this->DBNAME		= $_SESSION['customConfig']['DBSettings']['DBNAME'];
        $this->DBUSER		= $_SESSION['customConfig']['DBSettings']['DBUSER'];
        $this->DBPASSWORD	= $_SESSION['customConfig']['DBSettings']['DBPASSWORD'];



        // Erzeuge Datenbankverbindung
        if ($this->mysqli == NULL){
            $this->mysqliConnect();
        }

        return($this->mysqli);

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




    // Erzeugt MySQL permanente Verbindung
    private function mysqliConnect()
    {

        // Permanente Verbindung?
        if ($_SESSION['systemConfig']['Setting']['DBConnectionType'] == 'pconnect')
            $mysqli = new mysqli('p:'.$this->DBHOST, $this->DBUSER, $this->DBPASSWORD, $this->DBNAME);
        else
            $mysqli = new mysqli($this->DBHOST, $this->DBUSER, $this->DBPASSWORD, $this->DBNAME);


        // DB Verbindung fehlgeschlagen?
        if ($mysqli->connect_errno) {

            print ("<pre>");
            $message = "FEHLER -KRITISCH FÜHRT ZU EXIT-<br>";
            $message .= "Versuch Aufbau Datenbankverbindung fehlgeschlagen!<br>";
            $message .= "MySQL-Fehlermeldung: <br>";
            $message .= "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            print($message);
            print ("</pre>");
            exit;

        }

        // Speichere Verbindungs-Objekt
        $this->mysqli = $mysqli;

        RETURN TRUE;

    }	// END private function pconnect()





    // MySQLi Query
    public function query($query, $debug=false)
    {

        $mysqli = $this->mysqli;

        // Debug - Ausgabe der Query gewünscht?
        if ($debug)
            $this->simpleout($query);


        // Query ausführen und in Resultat speichern
        $result = $mysqli->query($query);



        // Wurde Autoincrement hochgezählt?
        // Wenn ja, letzte ID speichern
        if ($mysqli->insert_id > 0){
            $this->last_insert_id = $mysqli->insert_id;
        }


        // Resultat in Klassen - Var speichern
        $this->lastResult = $result;


        RETURN $result;

    }	// END public function query(...)





    // MySQLi Funktion mysql_num_rows
    public function num_rows($result = NULL)
    {

        if($result === NULL)
            $inc = $this->lastResult;
        else
            $inc = $result;

        $num_rows = $inc->num_rows;

        return($num_rows);

    }	// END public function num_result(...)





    // MySQL Speicher wieder freigeben
    public function free_result($result = NULL)
    {
        if($result === NULL)
            $result = $this->lastResult;

        mysqli_free_result($result);

        return TRUE;
    }





}   // END class MySQLDB extends Core
