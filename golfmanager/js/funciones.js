function showAddHoles (id) {
	document.getElementById(id).style.display = "block"; 
}

function disable_cell (celda) {
	document.getElementById(celda).disabled
}

function showTournamentTable (idTable) {
	jQuery(document).ready(function(){
		jQuery('#' + idTable).dataTable();
	});
}

function test (message) {
	alert (message);
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

function fill_select_registered_players_tnmt (select_id,multiselect_id_reg) {
	var tnm_id = document.getElementById(select_id); 
	var tnm_value = tnm_id.options[tnm_id.selectedIndex].value;

	var data_registered = {
		action: 'get_json_registered_players' ,
		identificador: tnm_value 
	}; 
	
	jQuery(document).ready(function($) { 
		jQuery.post(ajaxurl,data_registered, function (response_a) {
				//alert('Got This from the server: ' + response_a ) ;
				//alert (select_id); 
				var sel = $("#" + multiselect_id_reg)  ; 
				sel.empty (); 
				var json = JSON.parse(response_a) ; 
				//alert ('El numero de claves es: ' + json.length) ; 	
				for (var i=0 ; i<json.length ; i++ ) {
					sel.append('<option value="'  + json[i].License + '">' + json[i].Name + ' ' + json[i].Lastname + '</option>');
				}	
		});
	});	
}


function fill_scorecard (tournament_id,player_id) {
	var tnmt_sel = document.getElementById(tournament_id);
	var player_sel = document.getElementById(player_id) ; 
	
	var tnmt =  tnmt_sel.options[tnmt_sel.selectedIndex].value;
	var player = player_sel.options[player_sel.selectedIndex].value; 
	
	var data = {
		action: 'get_json_socrecard', 
		tnmt: tnmt,
		license: player
	}; 
	
	jQuery(document).ready(function($) {
		jQuery.post(ajaxurl,data, function (response) {	
			alert(response);		
			var json = JSON.parse(response); 
			if (json.length == 0) {
				alert ("No hay resultados previos");
				alert (json); 
				for (var i=1 ; i <= 18 ; i++ ) {
					document.getElementById("idGolpes" + i ).value = '0'; 	
					document.getElementById("idPutts" + i ).value = '0'; 
					document.getElementById("idCalles" + i ).checked=true;
					}
			}  
			else {
				if (json.length > 18 || json.length < 18)	{
					alert ("Ronda con no 18 hoyos, por favor, chequear")
				}	
				else {
					for (var i = 0 ; i <= 18 ; i++) {
						var hole_number = json[i].Number ; 
						document.getElementById("idGolpes" + hole_number).value = json[i].Shots ; 
						document.getElementById("idPutts" + hole_number).value = json[i].putts ;
						if (json[i].Fairways == 1 ) {
							document.getElementById("idCalles" + hole_number ).checked=true;	
						} 
						else {
							document.getElementById("idCalles" + hole_number ).checked=false;	
						}
					}
				}
			}
		});
		
	}); 
}

function refresh_select_for_tnmt (select_id,multiselect_id_not_reg,multiselect_id_reg) {
	
	var tnm_id = document.getElementById(select_id); 
	var tnm_value = tnm_id.options[tnm_id.selectedIndex].value;
	//var tnm_value = 1; 
	var data_not_registered = {
		action: 'get_json_not_registered_players' ,
		identificador: tnm_value 
	}
	var data_registered = {
		action: 'get_json_registered_players' ,
		identificador: tnm_value 
	}

	jQuery(document).ready(function($) { 
		jQuery.post(ajaxurl,data_not_registered, function (response_a) {
				//alert('Got This from the server: ' + response_a ) ;
				//alert (select_id); 
				var sel = $("#" + multiselect_id_not_reg)  ; 
				sel.empty (); 
				var json = JSON.parse(response_a) ; 
				//alert ('El numero de claves es: ' + json.length) ; 	
				for (var i=0 ; i<json.length ; i++ ) {
					sel.append('<option value="'  + json[i].License + '">' + json[i].Name + ' ' + json[i].Lastname + '</option>');
				}	
		});
		jQuery.post(ajaxurl,data_registered, function (response_b) {
				//alert('Got This from the server: ' + response_b ) ;
				//alert (select_id); 
				var sel = $("#" + multiselect_id_reg); 
				sel.empty (); 
				var json = JSON.parse(response_b) ; 
				//alert ('El numero de claves es: ' + json.length) ; 	
				for (var i=0 ; i<json.length ; i++ ) {
					sel.append('<option value="'  + json[i].License + '">' + json[i].Name + ' ' + json[i].Lastname + '</option>');
					}	
				}); 
		});
}

function submit_form (submit_id) {
	hidden_submit_id = document.getElementById(submit_id);
    hidden_submit_id.click(); 
} 

function sel_all_options (select_id,submit_id) {
	// have we been passed an ID 
    if (typeof select_id == "string") { 
        select_id = document.getElementById(select_id);
    } 
    // is the select box a multiple select box? 
    for (var i = 0; i < select_id.options.length; i++) { 
    	select_id.options[i].selected = true; 
    } 
}

function calendar_gm (id) { 
	jQuery(document).ready(function(){ 
    	jQuery(id).datepicker
    });
}

function update_rounds_table (table_id,select_id,url) {
	var tnm_obj =  document.getElementById(select_id); 
	var tnmt = tnm_obj.options[tnm_obj.selectedIndex].value;	
	var table_obj = document.getElementById(table_id) 
	
	var data  = {
		action: 'get_json_rounds_tnmt',
		tnmt_id: tnmt
	}; 
	
	jQuery(document).ready(function($) {
		jQuery.post(ajaxurl,data, function(response) {
			var json = JSON.stringify(response); 
			jQuery("#" + table_id).dataTable( {			
			"sAjaxSource": url,
			"bDestroy": true
			});
			 
		});
	});	
}



function update_rnd_for_tnmt (select_id_tnmt,select_id_rnd) {
	var tnm_obj =  document.getElementById(select_id_tnmt); 
	var tnmt = tnm_obj.options[tnm_obj.selectedIndex].value;	
	var rnd_obj = document.getElementById(select_id_rnd) ; 
	
	var data = {
		action: 'get_json_rounds_tnmt',
		tnmt_id: tnmt
	}; 

	jQuery(document).ready(function($){
		var sel = $("#" + select_id_rnd) ;   
		sel.empty(); 
		jQuery.post(ajaxurl,data, function (response) {
			var json_rnd = JSON.parse(response) ; 
			alert(response);
			sel.append('<option value=""> Seleccione una ronda </option>') ; 
			for (var i=0 ; i<json_rnd.length ; i++ ) {
					sel.append('<option value="'  + json_rnd[i][0]  + '">' + json_rnd[i][3] + ' ' + json_rnd[i][4] + '</option>');
			}			
		}); 
	});
}