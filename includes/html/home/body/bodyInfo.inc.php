<?php
/**
 * Copyright (c) 2016 by Markus Melching (TKRZ)
 */

// Debug - Dateinamen ausgeben?!
$hCore->debugInitOnLoad('File',__FILE__);



?>



<br>
<table border=0 class="standard" align="center" style="width:80%">
    <tr>
        <th>Ausgangssystem</th>
        <th><i class="fa fa-arrow-right"></i></th>
        <th><i class="fa fa-upload"></i>&nbsp;&nbsp;Datei - Upload</th>
        <th><i class="fa fa-arrow-right"></i></th>
        <th><i class="fa fa-database"></i>&nbsp;&nbsp;DB - Import</th>
        <th><i class="fa fa-arrow-right"></i></th>
        <th><i class="fa fa-database"></i>&nbsp;&nbsp;DB - Export</th>
        <th><i class="fa fa-arrow-right"></i></th>
        <th>kVASy - System</th>
    </tr>

    <tr>
        <td colspan="9">&nbsp;</td>
    </tr>

    <tr>
        <td align="center">Stammdaten</td>
        <td align="center"><i class="fa fa-arrow-right"></i></td>
        <td align="center">1.) Stammdaten</td>
        <td align="center"><i class="fa fa-arrow-right"></i></td>
        <td align="center">2.) Stammdaten</td>
        <td align="center"><i class="fa fa-arrow-right"></i></td>
        <td align="center">3.) Stammdaten</td>
        <td align="center"><i class="fa fa-arrow-right"></i></td>
        <td align="center">kVASy - System</td>
    </tr>

    <tr>
        <td colspan="9">&nbsp;</td>
    </tr>

    <tr>
        <td align="center">Buchungsdaten</td>
        <td align="center"><i class="fa fa-arrow-right"></i></td>
        <td align="center">4.) Buchungsdaten</td>
        <td align="center"><i class="fa fa-arrow-right"></i></td>
        <td align="center">5.) Buchungsdaten</td>
        <td align="center"><i class="fa fa-arrow-right"></i></td>
        <td align="center">6.) Buchungsdaten</td>
        <td align="center"><i class="fa fa-arrow-right"></i></td>
        <td align="center">kVASy - System</td>
    </tr>
</table>

<br><br><br>


<table border=0 class="standard" align="center" style="width:80%">
    <tr>
        <th><a class="std" href="" onclick="javascript:show('divHelpInfo'); return false"><i class="fa fa-info"></i>&nbsp;&nbsp;Vorgang Hilfe ein/ausblenden</a></th>
    </tr>
</table>




<div style="display: none" id="divHelpInfo" class="divHelpInfo">




<table border=0 class="standard" align="center" style="width:80%">
    <tr>
        <td>
            <b>Wichtige Anmerkung:</b><br>
            Die Reihenfolge: "Stammdaten behandeln" <i class="fa fa-arrow-right"></i> "Buchungsdaten behandeln" ist für den "<i>DB - Export</i>" unbedingt einzuhalten!<br><br>
            Begründung:<br>
            Für die Berechnung und für den Export der Buchungsdaten werden Teile aus den Stammdaten benötigt (IBAN, BLZ usw.).<br>
            Würde eine Rechnung auflaufen dessen Stammdaten noch nicht in der Konvertierungs-Datenbank vorliegen, kommt es zu einem Fehlerhaften - Datensatz.
        </td>
    </tr>
</table>



<br><br>



<table border=0 class="standard" align="center" style="width:80%">
    <tr>
        <td valign="top" colspan="2"><b>Ablauf</b></td>
    </tr>
    <tr>
        <td valign="top">1.0)</td>
        <td>
            <i class="fa fa-upload"></i>&nbsp;&nbsp;Datei - Upload<br>
            Ausgangsdatei auf/in das Konvertierungssystem laden!<br><br>
            Zuänchst müssen die Ausgangsdatein in das Konvertierungssystem "<i>hochgeladen</i>" werden.<br>
            Menü: <i class="fa fa-upload"></i>&nbsp;&nbsp;Datei - Upload <i class="fa fa-arrow-right"></i> {Typ} <i class="fa fa-arrow-right"></i> {Ausgangssystem}<br>
            Beispiel: <i class="fa fa-upload"></i>&nbsp;&nbsp;Datei - Upload  <i class="fa fa-arrow-right"></i> Stammdaten <i class="fa fa-arrow-right"></i> Centron<br>
            Die Reihenfolge für Stammdaten oder Buchungsdaten sowie der Ausgangssysteme spielt hier keine Rolle!
        </td>
    </tr>

    <tr>
        <td colspan="9">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="9">&nbsp;</td>
    </tr>

    <tr>
        <td valign="top">2.0)</td>
        <td>
            <i class="fa fa-database"></i>&nbsp;&nbsp;DB - Import<br>
            Import - System und Typ wählen!<br><br>
            Nach dem Upload, müssen die Ausgangsdatein (jetzt auf dem Server liegend) in die Datenbank des Konvertierungssystemes importiert werden.<br>
            Menü: <i class="fa fa-database"></i>&nbsp;&nbsp;DB - Import <i class="fa fa-arrow-right"></i> {Typ} <i class="fa fa-arrow-right"></i> {Ausgangssystem}<br>
            Beispiel: <i class="fa fa-database"></i>&nbsp;&nbsp;DB - Import <i class="fa fa-arrow-right"></i> Stammdaten <i class="fa fa-arrow-right"></i> Centron<br><br>
        </td>
    </tr>
    <tr>
        <td valign="top">2.1)</td>
        <td>
            <i class="fa fa-database"></i>&nbsp;&nbsp;DB - Import<br>
            Ausgangsdatei wählen!<br><br>
            Ausgegeben und angezeigt werden die zur Verfügung stehenden Ausgangsdatein. (Siehe Punkt 1.0)<br>
            Je nach Typ (Stammdaten/Buchungsdaten) werden erweiterte Informationen ausgegeben.<br><br>

            Zu beachtetn ist die Spalte "<i>&sum; Imports</i>" in der leicht zu erkennen ist, ob der Datensatz schon einmal importiert wurde.<br><br>

            Bei einem erneuten Import der Datensätze für Buchungsdaten werden diese als aktuell gültig (sprich allein vorhanden) behandelt!<br>
            ALTE STAMMDATENSÄTZE WERDE IMMER GEUPDATET ... DER DATENSTAMM WIRD ERWEITERT!<br>
            ALTE BUCHUNGSDATENSÄTZE WERDE IMMER GELÖSCHT ... DER DATENSTAMM FÜR DIE BUCHUNGSSÄTZE WIRD NEU GESCHRIEBEN!<br><br>

            Ist nicht sicher, was genau in den Datein enthalten ist, so können die Datein via Klick auf "<i>Download</i>" heruntergeladen werden.
        </td>
    </tr>

    <tr>
        <td colspan="9">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="9">&nbsp;</td>
    </tr>

    <tr>
        <td valign="top">3.0)</td>
        <td>
            <i class="fa fa-database"></i>&nbsp;&nbsp;DB - Export<br>
            Export - System und Typ wählen!<br><br>
            Nach dem Import, können die Datensätze exportiet und damit an kVASy übergeben werden.<br>
            Menü: <i class="fa fa-database"></i>&nbsp;&nbsp;DB - Export <i class="fa fa-arrow-right"></i> {Typ} <i class="fa fa-arrow-right"></i> {Ausgangssystem}<br>
            Beispiel: <i class="fa fa-database"></i>&nbsp;&nbsp;DB - Export <i class="fa fa-arrow-right"></i> Stammdaten <i class="fa fa-arrow-right"></i> Centron<br><br>
        </td>
    </tr>
    <tr>
        <td valign="top">3.1)</td>
        <td>
            <i class="fa fa-database"></i>&nbsp;&nbsp;DB - Export<br>
            Export bestätigen!<br><br>
            Ausgegeben und angezeigt werden die zur Verfügung stehenden Export-Daten. (Siehe Punkt 2.0)<br>
            Je nach Typ (Stammdaten/Buchungsdaten) werden erweiterte Informationen ausgegeben.<br><br>

            Nach dem Klick auf den Button "<i>Senden</i>" werden die Daten final bearbeitet und sind per Download verfügbar.<br>
            Der Link wird im "<i>Informationsfenster</i>" (erscheint im oberen Bereich des Monitors) ausgegeben!
        </td>
    </tr>
</table>




</div>
