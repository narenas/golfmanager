var muestra = document.getElementById("muestra"); 
function showAddHoles () {
	document.getElementById("hidden").style.display = "block"; 
}

function disable_cell (celda) {
	document.getElementById(celda).disabled
}


function moveOption( fromID, toID, idx )
{    
    if (isNaN(parseInt(idx)))
    {
        var i = document.getElementById( fromID ).selectedIndex;
    }
    else
    {
        var i = idx;
    }

    var o = document.getElementById( fromID ).options[ i ];
    var theOpt = new Option( o.text, o.value, false, false );
    document.getElementById( toID ).options[document.getElementById( toID ).options.length] = theOpt;
    document.getElementById( fromID ).options[ i ] = null;
}

function moveOptions( fromID, toID )
{
    for (var x = document.getElementById( fromID ).options.length - 1; x >= 0 ; x--)
    {
        if (document.getElementById( fromID ).options[x].selected == true)
        {
            moveOption( fromID, toID, x );
        }
    }
}