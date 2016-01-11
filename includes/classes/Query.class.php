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
 * ===> 					|-> ConcreteClass1	Core - Child - AnyCreature
 * 							|-> ...				Core - Child - AnyCreatures
 * 							|-> ConcreteClass20	Core - Child - AnyCreature
 *
 */
class Query extends Core
{
    public $gQuery = array();

    private $hCore;	            // Privates Core Objekt



    function __construct($hCore)
    {

        // Debug - Classname ausgeben?!
        $this->debugInitOnLoad('Class', $this->getClassName(false));


        // Speichere das Öffentliche hCore - Objekt zur weiteren Verwendung lokal
        $this->hCore = $hCore;

        parent::__construct();

    }	// END function __construct()





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





    public function getQuery($queryName,$paramArray = array())
    {

        $hCore = $this->hCore;

        $getQuery = '';

        switch ($queryName){
            case 'UserLogin':
                // Login - Abfrage

                $getQuery = "SELECT u.*
                                    ,r.roleID
                                    ,r.roleName
                              FROM user u
                                LEFT JOIN role r ON (u.userSetRoleID = r.roleID)
                              WHERE u.userName      = '".$hCore->gCore['getPOST']['getUsername']."'
                                AND u.userPassword  = md5('".$hCore->gCore['getPOST']['getPassword']."')
                                AND u.activeStatus  = 'yes'
                                AND r.activeStatus  = 'yes'
                                LIMIT 1";
                break;



            case 'WriteUserLoginToDB':
                // Login - Vorgang in DB schreiben

                $getQuery = "INSERT INTO log_user (userID,
									REMOTE_ADDR,
									HTTP_USER_AGENT,
									HTTP_REFERER,
									HTTP_COOKIE,
									REQUEST_URI,
									SCRIPT_NAME,
									PHP_SELF
								  ) VALUES ('".$paramArray['Login']['User']['userID']."',
								  	'".$_SERVER['REMOTE_ADDR']."',
								  	'".$_SERVER['HTTP_USER_AGENT']."',
								  	'".$_SERVER['HTTP_REFERER']."',
								  	'".$_SERVER['HTTP_COOKIE']."',
								  	'".$_SERVER['REQUEST_URI']."',
								  	'".$_SERVER['SCRIPT_NAME']."',
								  	'".$_SERVER['PHP_SELF']."')";
                break;



            default:
                echo "default";
                break;
        }

        return $getQuery;
    }

}   // END class Query extends Coreg
