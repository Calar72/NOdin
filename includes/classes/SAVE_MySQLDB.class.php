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
    private $DBHOST			= '';	// Datenbank Host
    private $DBNAME			= '';	// (Default) Datenbank Name
    private $DBUSER			= '';	// Datenbank Benutzer
    private $DBPASSWORD		= '';	// Datenbank Passwort

    private $last_injection	= '';
    private $conn_id		= NULL;





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
        if ($this->conn_id == NULL){
            $this->pconnect();
        }

        return($this->conn_id);

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
    private function pconnect()
    {
        $this->conn_id = @mysql_pconnect($this->DBHOST, $this->DBUSER, $this->DBPASSWORD);

        // DB Verbindung fehlgeschlagen?
        if($this->conn_id === false){

            print ("<pre>");
            $message = "FEHLER -KRITISCH FÜHRT ZU EXIT-<br>";
            $message .= "Versuch Aufbau Datenbankverbindung fehlgeschlagen!<br>";
            $message .= "MySQL-Fehlermeldung: <br>";
            $message .= mysql_error();
            trigger_error($message);
            print ("</pre>");
            exit;

        }
        else {
            $this->select_db();
        }

    }	// END private function pconnect()





    // Selektiert Databenk
    private function select_db()
    {

        $select = @mysql_select_db($this->DBNAME, $this->conn_id);

        // Fehler bei Selektierung?
        if($select === false){

            print ("<pre>");
            $message = "FEHLER -KRITISCH FÜHRT ZU EXIT-<br>";
            $message .= "Die angegebene Datenbank '".$this->DBNAME."' existiert nicht.<br>";
            $message .= "MySQL-Fehlermeldung: <br>";
            $message .= mysql_error();
            trigger_error($message);
            print ("</pre>");
            exit;

        }

    }	// END private function select_db()





    // Gibt SQL - Speicher wieder frei
    public function free_result($result = NULL)
    {

        if($result === NULL)
            $inc = $this->last_injection;
        else
            $inc = $result;

        @mysql_free_result($inc);

    }	// END public function free_result(...)





    // MySQL Query
    public function query($query, $debug=false)
    {

        if ($debug)
            $this->simpleout($query);


        $this->last_injection = @mysql_query($query);


        if($this->last_injection === false){

            print ("<pre>");
            $message = "FEHLER -REGULÄR-<br>";
            $message .= "Fehler bei dem Ausf&uuml;hren eines Mysql-codes!<br>";
            $message .= "Mysql-Code: " . htmlspecialchars($query, ENT_QUOTES) . "<br>";
            $message .= "MySQL-Fehlermeldung:<br>";
            $message .= mysql_error();
            trigger_error($message);
            print ("</pre>");

        }

        return($this->last_injection);

    }	// END public function query(...)





    // MySQL fetch via assoc
    public function fetch_assoc($result = NULL, &$row = '')
    {

        if($result === NULL)
            $inc = $this->last_injection;
        else
            $inc = $result;

        $row = mysql_fetch_assoc($inc);

        RETURN($row);

    }	// END public function fetch_assoc(...)





    // MySQL fetch via object
    public function fetch_object($result = NULL, &$row = '')
    {

        if($result === NULL)
            $inc = $this->last_injection;
        else
            $inc = $result;

        $row = mysql_fetch_object($inc);

        RETURN($row);

    }	// END public function fetch_assoc(...)





    // MySQL Funktion mysql_num_rows
    public function num_rows($result = NULL)
    {

        if($result === NULL)
            $inc = $this->last_injection;
        else
            $inc = $result;

        $num = mysql_num_rows($inc);

        return($num);

    }	// END public function num_result(...)





}   // END class MySQLDB extends Core
