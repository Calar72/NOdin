function StartTicker()
{
    document.NewsTicker.Zeile.value	= "Bitte nutzen Sie einen aktuelleren Browser.";
}
// Tickermeldungen

// Es muessen alle drei Werte angegeben werden.

// Effekte fuer Start:
//   1: einrollen
//   2: blinken
// Effekte fuer Ende:
//   1: ausrollen
//   2: blinken

//var	tickernews	=
//    [
//        {meldung:"Stammdatensätze: 564", starteffekt:1, endeeffekt:1},
//        {meldung:"Buchungsdatensätze: 128", starteffekt:1, endeeffekt:1},
//        {meldung:"Und noch viel mehr Text könnte hier stehen.", starteffekt:1, endeeffekt:1}
        //{meldung:"Hier wird eingerollt und abschlie\xDFend geblinkt.", starteffekt:1, endeeffekt:2},
        //{meldung:"Nach dem eingehenden Blinken wird ausgerollt.", starteffekt:2, endeeffekt:1},
        //{meldung:"Jetzt wird am Anfang und am Ende geblinkt.", starteffekt:2, endeeffekt:2}
    //];

// Tickerparameter
var	ticker_start			= 500;	// Wartezeit bis zur ersten Meldung

var	ticker_effekt1_start_a		= 75;	// Darstellungsgeschwindigkeit [ms]
var	ticker_effekt1_start_b		= 2500;	// Darstellungszeit der Meldung [ms]

var	ticker_effekt2_start_max_blink	= 3;	// Anzahl der Blinkdarstellungen
var	ticker_effekt2_start_a		= 100;	// Blinkgeschwindigkeit [ms]
var	ticker_effekt2_start_b		= 2500;	// Darstellungszeit der Meldung [ms]

var	ticker_effekt1_ende_a		= 50;	// Scrollgeschwindigkeit [ms]
var	ticker_effekt1_ende_b		= 100;	// Wartezeit bis zur naechsten Meldung [ms]

var	ticker_effekt2_ende_max_blink	= 3;	// Anzahl der Blinkdarstellungen
var	ticker_effekt2_ende_a		= 100;	// Blinkgeschwindigkeit [ms]
var	ticker_effekt2_ende_b		= 1000;	// Wartezeit bis zur naechsten Meldung [ms]

// Interne Variablen
var	msgnr	= 0;

// Aufruf der naechsten Meldung
function nextTicker()
{
    msgnr++;
    if(msgnr >= tickernews.length)
        msgnr	= 0;
    setTimeout("showTicker(0)", 1000);
}

// Effekte fuer Meldungsende
function hideTicker(cnt)
{
    switch(tickernews[msgnr].endeeffekt)
    {
        // Effekt 2
        case 2:
            document.NewsTicker.Zeile.value	= ((cnt % 2) == 1) ? tickernews[msgnr].meldung : "";
            if(cnt > (2 * ticker_effekt2_ende_max_blink + 1))
                setTimeout("nextTicker(0)", ticker_effekt2_ende_b);
            else
                setTimeout("hideTicker(" + String(cnt + 1) + ")", ticker_effekt2_ende_a);
            break;

        // Effekt 1
        default:
            document.NewsTicker.Zeile.value	= tickernews[msgnr].meldung.substring(cnt, tickernews[msgnr].meldung.length);
            if(cnt >= tickernews[msgnr].meldung.length)
                setTimeout("nextTicker()", ticker_effekt1_ende_b);
            else
                setTimeout("hideTicker(" + String(cnt + 1) + ")", ticker_effekt1_ende_a)
    }
}

// Effekte fuer Meldungsanfang
function showTicker(cnt)
{
    switch(tickernews[msgnr].starteffekt)
    {
        // Effekt 2
        case 2:
            document.NewsTicker.Zeile.value	= ((cnt % 2) == 1) ? "" : tickernews[msgnr].meldung;
            if(cnt > (2 * ticker_effekt2_start_max_blink + 1))
                setTimeout("hideTicker(0)", ticker_effekt2_start_b);
            else
                setTimeout("showTicker(" + String(cnt + 1) + ")", ticker_effekt2_start_a);
            break;

        // Effekt 1
        default:
            document.NewsTicker.Zeile.value	= tickernews[msgnr].meldung.substring(0, cnt);
            if(cnt >= tickernews[msgnr].meldung.length)
                setTimeout("hideTicker(0)", ticker_effekt1_start_b);
            else
                setTimeout("showTicker(" + String(cnt + 1) + ")", ticker_effekt1_start_a);
    }
}

// Start des Tickers
function StartTicker()
{
    // Standardmeldung
    document.NewsTicker.Zeile.value	= "Ticker wird geladen...";

    // Ticker starten
    setTimeout("showTicker(0)", ticker_start);
}