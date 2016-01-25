<?php

/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 06.01.2016
 * Time: 15:08
 *
 * Vererbungsfolge der (Basis) - Klassen:
 *  	Base							    		        Adam/Eva
 *  	'-> SystemConfig			    			        Child
 *  	   	'-> DefaultConfig	    				        Child
 *  			'-> Messages					            Child
 *  				'-> Debug					            Child
 * 					    '-> MySQLDB			                Child
 * ===>					    '-> Query		                Child
 *       					    '-> Core    			    Child
 * 		    					    |-> ConcreteClass1	    Core - Child - AnyCreature
 * 			    				    |-> ...				    Core - Child - AnyCreatures
 * 				    			    |-> ConcreteClass20	    Core - Child - AnyCreature
 *
 */
class Query extends MySQLDB
{
    public $gQuery = array();




    function __construct()
    {

        // Debug - Classname ausgeben?!
        $this->debugInitOnLoad('Class', $this->getClassName(false));



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
        $getQuery = '';

        switch ($queryName){
            case 'loginCheckLoginOnDB':
                // Login - Abfrage

                $getQuery = "SELECT u.*
                                    ,r.roleID
                                    ,r.roleName
                              FROM user u
                                LEFT JOIN role r ON (u.userSetRoleID = r.roleID)
                              WHERE u.userName      = '".$this->gCore['getPOST']['getUsername']."'
                                AND u.userPassword  = md5('".$this->gCore['getPOST']['getPassword']."')
                                AND u.activeStatus  = 'yes'
                                AND r.activeStatus  = 'yes'
                                LIMIT 1";
                break;



            case 'loginWriteUserLoginToDB':
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



            case 'loginGetUserLastLogin':
                // letzte Login-Informationen eines Betnutzers ermitteln

                $getQuery = "SELECT `lastLogin`
					          FROM log_user
					          WHERE `userID` LIKE '".$paramArray['Login']['User']['userID']."'
					          ORDER BY log_userID DESC
					          LIMIT 0,2";
            break;



            case 'leftNavigationGetConvertTypes':
                // KonvertierungsTypen einlesen

                $getQuery = "SELECT `sourceTypeName`,
                                    `shortCut`,
                                    `sourceTypeID`
					          FROM sourceType
					          WHERE `active` LIKE 'yes'
					          ORDER BY sourceTypeName ASC";
            break;



            case 'leftNavigationGetConvertSystems':
                // KonvertierungsSysteme einlesen

                $getQuery = "SELECT `sourceSystemName`,
                                    `sourceSystemID`
					          FROM sourceSystem
					          WHERE `active` LIKE 'yes'
					          ORDER BY sourceSystemName ASC";
                break;



            default:
                echo "default";
                break;
        }

        return $getQuery;
    }

}   // END class Query extends MySQLDB
