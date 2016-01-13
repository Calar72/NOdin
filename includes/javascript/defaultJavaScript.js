/**
 * 
 */

/* Blendet <div> Tags aus/ein ... z.B. die Debug-Optionen! */
function show(id) {
    if(document.getElementById) {
        var mydiv = document.getElementById(id);
        mydiv.style.display = (mydiv.style.display=='block'?'none':'block');
    }
}
