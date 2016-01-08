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
class Action extends Core
{

    public $gAction = array();

    private $hCore;	            // Privates Core Objekt





    function __construct($hCore)
    {

        // Debug - Classname ausgeben?!
        $this->initDebugOnLoad('Class', $this->getClassName(false));


        // Speichere das Öffentliche hCore - Objekt zur weiteren Verwendung lokal
        $this->hCore = $hCore;


        parent::__construct();


        // Default Methode die bei der Erstellung eines Action-Objekts aufgerufen wird.
        // Enthalten: Eingeloggt oder nicht - Prüfung
        $this->initActionOnDefault();

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
    private function initActionOnDefault()
    {

        $hCore = $this->hCore;

        // Benutzer schon eingeloggt?
        $hLogin         = new Login($hCore);
        $getCurUserID   = $hLogin->getLoginUserID();
        if ($getCurUserID < 1){
            // Head - Datei bleibt Default!

            // Body - Datei -> Verweis zum Login - Formular
            $hCore->gCore['getLeadToBodyClass']     = 'Login';                      // Klasse die geladen werden soll
            $hCore->gCore['getLeadToBodyMethod']    = 'doNothing';                  // Methoden - Aufruf
            $hCore->gCore['getLeadToBodySite']      = 'includes/html/loginBody';    // Webseite die geladen werden soll
            $hCore->gCore['getLeadToBodyByAction']  = 'force';                      // Erzwinge das Überschreiben von Default

            // Footer - Datei bleibt Default!
        }

/*
        // Login schon vollzogen?
        $hLogin = new Login();
        $getCurUserID = $hLogin->getCurUserID();

        // Verweise zum Login - Formular
        if ($getCurUserID < 1){
            $hCore->classArg['getLeadToBodySite'] = 'html/loginBody';
            $hCore->classArg['Body']['setAction'] = 'Login';
        }
        else {
            $hCore->classArg['getLeadToBodySite'] = 'html/homeBody';
            $hCore->classArg['Body']['setAction'] = 'home';
        }


        // callAction - Abarbeiten?!
        $this->initOnCallAction();

*/

        //TODO Sonst hier zum Rechtecheck?
    }   // END function initOnDefault()





}   // END class Action extends Core
