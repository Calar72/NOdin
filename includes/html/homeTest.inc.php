<?php
/**
 * Copyright (c) 2016 by Markus Melching (TKRZ)
 */

/**
 * Created by PhpStorm.
 * User: MMelching
 * Date: 12.01.2016
 * Time: 17:50
 */
?>


<?php
// Deklariere Div-Tag Navigation
?>
<div style="display: block" id="divLeftNavigationOuter" class="divLeftNavigationOuter">
    <?php include 'includes/html/home/body/bodyLeftNavigation.inc.php'; ?>
</div>


<?php
class baseDataSet {

    private static $baseDataSettings = array(
                                            'Vorname'=>array('min'=>'1',
                                                                'max'=>'30'
                                                            ),

                                            'Nachname'=>array('min'=>'1',
                                                                'max'=>'30'
                                                            ),

                                            'Email'=>array('min'=>'1',
                                                            'max'=>'60'
                                                            ),

                                            'Telefon'=>array('min'=>'0',
                                                                'max'=>'10'
                                                            ),

                                            'Brutto'=>array('min'=>'0',
                                                                'max'=>'10',
                                                                'callFunc'=>'ref_formatMyMoney',
                                                                'argFunc'=>'yes'
                                                            ),

                                            );


    function __construct(){

        RETURN TRUE;

    }




    // INITIAL Methode ... Durchläuft Feldname und Dateninhalt entsprechend aller Notwendigkeiten
    public function ref_checkDataSet(& $getFieldname, & $getValue)
    {
        // Datensatz ... "säubern" (Leerzeichen usw)
        $this->ref_getClearData($getFieldname);
        $this->ref_getClearData($getValue);



        // Datensatz ... soll weitere Methode aufgerufen werden?
        $this->ref_checkDynFunctionCall($getFieldname, $getValue);



        // Datensatz ... Min - Max - Länge prüfen
        if (!$this->checkMinMaxLen($getFieldname, $getValue))
            RETURN FALSE;


        RETURN TRUE;

    }





    // Dynamisch pro Datensatz eine Funktion/Methode aufrufen?
    private function ref_checkDynFunctionCall(& $getFieldname, & $getValue)
    {

        // Dynamisch eine Methode aufrufen?
        if (isset(self::$baseDataSettings[$getFieldname]['callFunc'])){
            $callMethod = self::$baseDataSettings[$getFieldname]['callFunc'];

            if ( (isset(self::$baseDataSettings[$getFieldname]['argFunc'])) && (self::$baseDataSettings[$getFieldname]['argFunc'] == 'yes') ){
                $this->$callMethod($getValue);
            }
            else{
                $this->$callMethod();
            }
        }

    }





    // Formatiert Geldbetrag
    private function ref_formatMyMoney(& $arg)
    {

        $arg = str_replace(",",".", $arg);
        $arg = round($arg, 2);
        $arg = number_format($arg, 2, '.', '');

    }





    // Datensatz ... "säubern" (Leerzeichen usw)
    private function ref_getClearData(& $arg){

        $arg = trim($arg);

    }





    // Min - Max - Länge prüfen und TRUE oder FALSE zurück geben
    private function checkMinMaxLen($getFieldname, $getValue){

        // Min - Max - Länge ermitteln
        $getRetArray = $this->getMinMaxLen($getFieldname);

        $min = $getRetArray[$getFieldname]['min'];
        $max = $getRetArray[$getFieldname]['max'];

        if (strlen($getValue) < $min)
            RETURN FALSE;

        if (strlen($getValue) > $max)
            RETURN FALSE;

        RETURN TRUE;

    }





    // Liefert die benötigte min und max Länge eines Datensatzes
    private function getMinMaxLen($getFieldname)
    {

        $return = array();
        $return[$getFieldname]['min'] = 0;
        $return[$getFieldname]['max'] = 0;

        if (key_exists($getFieldname, self::$baseDataSettings)){
            $return[$getFieldname]['min'] = self::$baseDataSettings[$getFieldname]['min'];
            $return[$getFieldname]['max'] = self::$baseDataSettings[$getFieldname]['max'];
        }

        RETURN $return;

    }

}

?>






<?php
// Deklariere Div-Tag Content
?>

    <div style="display: block" id="divContentOuter" class="divContentOuter">
        <table border=0 class="standard" style="width:100%">
            <tr>
                <td>
                    Code Testing <i class="fa fa-arrow-right"></i> Startseite
                </td>
            </tr>
        </table>


        <?php
        /*



            echo "<hr>";



            $obj = new baseDataSet();


            $datensatz = '12';
            $fieldName = 'Brutto';


            echo "<pre>";
            print_r($fieldName); echo "<br>";
            print_r($datensatz); echo "<br>";
            echo "</pre>";

            echo "<hr>";

            if (!$obj->ref_checkDataSet($fieldName, $datensatz)) {
                echo "Datensatz geht nicht<br>";
            }
            else{
                echo "Datensatz ok<br>";
            }


            echo "<pre>";
            print_r($fieldName); echo "<br>";
            print_r($datensatz); echo "<br>";
            echo "</pre>";

            echo "<hr>";



        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



            $pattern = 'Marienhospital Münsterland,';
            $search = '/,$/i';

            if ( preg_match_all($search, $pattern, $result)) {
                // $strassenname       = trim($result[1][0]);
            }
            $newValue = '';
            $pattern = preg_replace($search, $newValue, $pattern);


            echo "$pattern<br>";
            echo "<pre>";
            print_r($result);
            echo "</pre>";
            echo "$pattern<br>";
        */



        $p = 'Paul';

        $a = (isset($p)) ? $p : 'Markus';


        echo "$a<br>";

        ?>



<?php

echo "<hr>";

$tmp = '+49 2572 953262';
$tmp = '02572 8004422';
$tmp = '+49 2572952755';
$tmp = '0176 44563868';
//$tmp = '49 177 5474207';


$tmp = trim($tmp);
$preSearch = '/^\+49 (.*)+/';
$search = '/^\+49 /';
$replace = '0';
if (preg_match($preSearch, $tmp))
    $tmp = preg_replace($search, $replace, $tmp);

echo "$tmp<br>";
$tmp = trim($tmp);
$search = '/^49 /';
$replace = '0';
$tmp = preg_replace($search, $replace, $tmp);


$search = '/^02572/';
$replace = '02572/';
$tmp = preg_replace($search, $replace, $tmp);

$search = '/ /';
$replace = '/';
$tmp = preg_replace($search, $replace, $tmp);

$search = '/-/';
$replace = '/';
$tmp = preg_replace($search, $replace, $tmp);

$search = '/\/\//';
$replace = '/';
$tmp = preg_replace($search, $replace, $tmp);

print ($tmp);
echo "<br>";

$curBuchungsdatumReadable = '2016-01-12';
$myDate = new DateTime($curBuchungsdatumReadable. ' 08:00:00');

$curZahlungsbedingungen = '10';
$myDate->add(new DateInterval('P'.$curZahlungsbedingungen.'D'));
$ankZahlungseinzugZum = $myDate->format('Ymd');

//$ankZahlungseinzugZum = $curBuchungsdatumReadable + $curZahlungsbedingungen;

echo "<hr>--->";
echo $ankZahlungseinzugZum;
echo "<br><hr>";


?>


    </div>


<div style="display: block" id="footer_ticker_container" class="footer_ticker_container">
    <FORM NAME="NewsTicker">
        <INPUT TYPE="TEXT" READONLY id="inputFooterTicker" class="inputFooterTicker" NAME="Zeile" SIZE=50 MAXLENGTH=60">
    </FORM>
    <SCRIPT>StartTicker();</SCRIPT>
</div>
