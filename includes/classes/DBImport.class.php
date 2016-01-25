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
class DBImport extends Core
{

    public $gLeftNavigation = array();

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





    // INITIAL Daten Importieren
    public function getImports()
    {
        $hCore = $this->hCore;

        // Typ bekannt!
        $req_sourceTypeID   = $hCore->gCore['getGET']['subAction'];

        // System bekannt!
        $req_sourceSystemID = $hCore->gCore['getGET']['valueAction'];;


        // Daten einlesen

        $query = "SELECT *
                      FROM `fileUpload`
                        LEFT JOIN `sourceSystem`  ON sourceSystem.sourceSystemID 	= fileUpload.sourceSystemID
                        LEFT JOIN `sourceType` 	  ON sourceType.sourceTypeID 	    = fileUpload.sourceTypeID
                        LEFT JOIN `user`          ON user.userID                    = fileUpload.userID
                      WHERE fileUpload.sourceTypeID     = '".$req_sourceTypeID."'
                        AND fileUpload.sourceSystemID   = '".$req_sourceSystemID."'
                        AND sourceType.active           = 'yes'
                        AND sourceSystem.active         = 'yes'
                      ORDER BY fileUpload.uploadDateTime
                        ";


        // Resultat der Login - Prüfung
        $result = $this->gCoreDB->query($query);

        // Betroffene Zeilen, bzw. erhaltene
        $num_rows = $this->gCoreDB->num_rows($result);


        // Keine Import Datein gefunden!
        if (!$num_rows >= '1'){

            // Breche Methode hier ab und liefere false - Wert zurück

            RETURN FALSE;
        }


        $indexCnt = 0;
        while ($row = $result->fetch_object()){

           $this->hCore->gCore['DBImportFiles'][$indexCnt]['fileUploadID'] = $row->fileUploadID;
           $this->hCore->gCore['DBImportFiles'][$indexCnt]['fileOriginName'] = $row->fileOriginName;
           $this->hCore->gCore['DBImportFiles'][$indexCnt]['uploadDateTime'] = $row->uploadDateTime;
           $this->hCore->gCore['DBImportFiles'][$indexCnt]['fileSize'] = $row->fileSize;
           $this->hCore->gCore['DBImportFiles'][$indexCnt]['userName'] = $row->userName;
           $this->hCore->gCore['DBImportFiles'][$indexCnt]['importCounter'] = $row->importCounter;
           $this->hCore->gCore['DBImportFiles'][$indexCnt]['downloadLink'] = $row->downloadLink;

            $indexCnt++;
        }

        $this->gCoreDB->free_result($result);


        RETURN TRUE;

    }   // END public function getImports()






}   // END class DBImport extends Core
