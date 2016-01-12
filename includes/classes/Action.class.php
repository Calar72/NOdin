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
class Action extends Core
{

    public $gAction = array();

    private $hCore;	            // Privates Core Objekt





    function __construct($hCore)
    {

        // Debug - Classname ausgeben?!
        $this->debugInitOnLoad('Class', $this->getClassName(false));

        // Speichere das Öffentliche hCore - Objekt zur weiteren Verwendung lokal
        $this->hCore = $hCore;


        parent::__construct();


        // Default Methode die bei der Erstellung eines Action-Objekts aufgerufen wird.
        // Enthalten: Eingeloggt oder nicht - Prüfung
        $this->actionInitOnDefault();

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





    // INITIAL Default Aufruf
    // Methode wird aufgerufen sobald ein Action-Objekt erzeugt wird!
    private function actionInitOnDefault()
    {

        $hCore = $this->hCore;

        // Erzeuge Login - Objekt
        $hLogin = new Login($hCore);




        // Login Aufgerufen?
        if ( (isset($hCore->gCore['getPOST']['callAction'])) && ($hCore->gCore['getPOST']['callAction'] == 'callLogin') )
        {
            // Rufe Initial - Methode für den Login auf
            // Rückgabe egal, weiter in deiser Methode werden alle Fälle abehandelt
            $hLogin->loginInitCallLogin();
        }



        // Aktuell Benutzer ID ermitteln (wenn vorhanden)
        $getCurUserID   = $hLogin->loginGetLoginUserID();



        // Login - Formular aufrufen oder Home - Webseite ausgeben?
        $this->actionGetLoginPageOrHomePage($getCurUserID);



        //////////////////////////////////// Ab hier die Action - Steuerung //////////////////////////////////

        // Logout angefordert?
        if ($this->gCore['getGET']['callAction'] == 'callLogout'){
            // Head - Datei bleibt Default!

            // Body - Datei -> Verweis zum Login - Formular
            $hCore->gCore['getLeadToBodyClass']     = 'Login';                              // Klasse die geladen werden soll
            $hCore->gCore['getLeadToBodyMethod']    = 'loginLogoutUser';                      // Methoden - Aufruf
            $hCore->gCore['getLeadToBodySite']      = 'includes/html/login/loginBody';      // Webseite die geladen werden soll
            $hCore->gCore['getLeadToBodyByAction']  = 'force';                              // Erzwinge das Überschreiben von Default

            // Footer - Datei bleibt Default!
        }





        RETURN TRUE;

    }   // END function initOnDefault()





    // Setzt Seitenaufruf auf Home (wenn güliter Login) oder auf Login - Formular (wenn kein gülitger Login (Session) vorhanden ist
    private function actionGetLoginPageOrHomePage($getCurUserID = 0)
    {
        $hCore = $this->hCore;

        // Benutzer schon eingeloggt?
        if ($getCurUserID < 1){

            // Benutzer noch nicht eingeloggt!

            // Head - Datei bleibt Default!

            // Body - Datei -> Verweis zum Login - Formular
            $hCore->gCore['getLeadToBodyClass']     = 'Login';                              // Klasse die geladen werden soll
            $hCore->gCore['getLeadToBodyMethod']    = 'doNothing';                          // Methoden - Aufruf
            $hCore->gCore['getLeadToBodySite']      = 'includes/html/login/loginBody';      // Webseite die geladen werden soll
            $hCore->gCore['getLeadToBodyByAction']  = 'force';                              // Erzwinge das Überschreiben von Default

            // Footer - Datei bleibt Default!
        }
        else {

            // Benutzer eingeloggt!

            // Head - Datei -> Verweis zu Home
            $hCore->gCore['getLeadToHeadClass']         = 'HomeHead';                       // Klasse die geladen werden soll
            $hCore->gCore['getLeadToHeadMethod']        = 'doNothing';                      // Methoden - Aufruf
            $hCore->gCore['getLeadToHeadSite']          = 'includes/html/home/homeHead';    // Webseite die geladen werden soll
            $hCore->gCore['getLeadToHeadByAction']      = 'force';                          // Erzwinge das Überschreiben von Default

            // Body - Datei -> Verweis zu Home
            $hCore->gCore['getLeadToBodyClass']         = 'HomeBody';                       // Klasse die geladen werden soll
            $hCore->gCore['getLeadToBodyMethod']        = 'doNothing';                      // Methoden - Aufruf
            $hCore->gCore['getLeadToBodySite']          = 'includes/html/home/homeBody';    // Webseite die geladen werden soll
            $hCore->gCore['getLeadToBodyByAction']      = 'force';                          // Erzwinge das Überschreiben von Default

            // Footer - Datei -> Verweis zu Home
            $hCore->gCore['getLeadToFooterClass']       = 'HomeFooter';                     // Klasse die geladen werden soll
            $hCore->gCore['getLeadToFooterMethod']      = 'doNothing';                      // Methoden - Aufruf
            $hCore->gCore['getLeadToFooterSite']        = 'includes/html/home/homeFooter';  // Webseite die geladen werden soll
            $hCore->gCore['getLeadToFooterByAction']    = 'force';                          // Erzwinge das Überschreiben von Default

        }
    }





}   // END class Action extends Core
