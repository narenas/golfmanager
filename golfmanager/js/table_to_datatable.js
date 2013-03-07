jQuery(document).ready(function(){
var oDataTable = jQuery('.resultsDatatable').dataTable({
	"iDisplayLength": 50,
	"bJQueryUI": true,
	"sPaginationType": "full_numbers",
	"aoColumnsDefs": [
		{ "bVisible": false, "aTargets": [0]}
	],
	});
});


jQuery(document).ready(function(){
	
	var oTable;  
	jQuery('.resultsDatatable tbody tr').click(function () {	
		if ( oTable.fnIsOpen(this) ) {
			oTable.fnClose( this );
		} 
		else {	
			var table = "<div class=Leaderboard>" ; 
			table += '<table id="idLeaderBoardTable" class="LeaderBoardTable">' ; 
			table += '<thead>' ; 
			table += '<tr>';  
			table += '<td>Ronda</td>' ;
			for (var i = 1 ; i <=18 ; i++) {
				if (i == 10 ) {
				table += '<td>' + "Out" + '</td>' ;
	
				}
				table += '<td>' + i + '</td>' ; 
			}
			table += '<td>' + "In" + '</td>' ;
			table += '<td>' + "Total" + '</td>' ;
			table += '</tr>'; 
			table += '</thead>';
			var license = oTable.fnGetData(this,0); 
			var tnmt_id = document.getElementById('tnmt_id').innerText ;
			
			var json_request = {
				action: 'get_json_player_rounds',
				tnmt_id: tnmt_id,
				license: license
			};
			table = "<div class=Leaderboard>" ; 
			table += '<table id="idLeaderBoardTable'+license+'" class="LeaderBoardTable">' ; 

			jQuery.post(ajaxurl,json_request,function (response) {
				json_rnd = JSON.parse(response) ;
				var socoreClass ; 
				for (var i in json_rnd) {
					var cabecera = ''; 
					var shotsrow = '';
					var parrow = '' ; 
					var hprow = '' ; 
					var numrow = ''; 
					var emptyrow  = '<tr></tr>' ; 
					var golpesInOut = 0; 
					var Total = 0; 
					var Neto = 0; 
					var hp_play = json_rnd[i][1]['hp_play']; 
					var hp_ex = json_rnd[i][1]['hp_ex'];
					var played = json_rnd[i][1]['Played']; 
					shotsrow += '<tr><td></td><td>Golpes</td>' ;
					parrow += '<tr><td></td><td>Par</td>' ;
					hprow += '<tr><td></td><td>Handicap</td>' ;
					numrow += '<tr><td></td><td>Hoyo</td>' ;
					cabecera += '<tr><td colspan="10">Fecha:' +i + ' Hp Juego:' + hp_play + ' HP Exacto:' + hp_ex + '</td></tr>'; 
					for (var j=1 ; j<=18 ; j++){
						if ( j == 10 ) {
							shotsrow += '<td>' + golpesInOut + '</td>' ;
							numrow += '<td>Out</td>' ;
							parrow += '<td></td>' ; 
							hprow += '<td></td>' ; 
							golpesInOut=0; 
						}
						
						switch (parseInt(json_rnd[i][j]['Shots']) - parseInt(json_rnd[i][j]['Par'])) {
							case 0: 
								scoreClass = "par"; 
								break ; 
							case -1: 
								scoreClass = "birdie"; 
								break ; 
							case -2: 
								scoreClass = "better"; 
								break ;
							case -3:
								scoreClass = "better" ; 
								break ;
							case 1: 
								scoreClass = "bogey" ; 
								break ;
							case 2:
								scoreClass = "worst" ; 
								break ;
							case 3:
								scoreClass = "worst" ; 
								break ;
							case 4:
								scoreClass = "worst" ; 
								break ;
							case 5:
								scoreClass = "worst" ; 
								break ;
							case 6:
								scoreClass = "worst" ; 
								break ;
							case 7:
								scoreClass = "worst" ; 
								break ;
							
						}
				
						shotsrow += '<td class="' + scoreClass + '">' + json_rnd[i][j]['Shots'] + '</td>' ;
						golpesInOut +=  parseInt(json_rnd[i][j]['Shots'])  ;
						Total += parseInt(json_rnd[i][j]['Shots'])  ;
						parrow += '<td>' +json_rnd[i][j]['Par'] + '</td>' ;
						hprow += '<td>' +json_rnd[i][j]['Hp'] + '</td>' ;	
						numrow += '<td>' + j + '</td>' ; 			
					}					
					if ((played == 1) && (Total == 0)) {
						Neto = 180;
					}
					else if (Total == 0){
						Neto = 0;
					}
					else {
						Neto = Total - hp_play ; 
					}
					
					shotsrow += '<td>' + golpesInOut + '</td><td>' + Total + '</td><td>' + Neto + '</td></tr>'; 
					parrow += '</tr>';
					numrow += '<td>In</td><td>Total</td><td>Neto</td></tr>';
					jQuery('#idLeaderBoardTable'+license).append(cabecera);
					jQuery('#idLeaderBoardTable'+license).append(numrow);
					jQuery('#idLeaderBoardTable'+license).append(hprow);
					jQuery('#idLeaderBoardTable'+license).append(parrow);
					jQuery('#idLeaderBoardTable'+license).append(shotsrow);
					jQuery('#idLeaderBoardTable'+license).append(emptyrow);
					
				}
			});
			
			table += '</table>';
			table += '</div>'; 

			oTable.fnOpen( this, table , "info_row" );
		}
	});
	
	
	var table_type = document.getElementById('table_type') ;
	var tnmt_rounds = document.getElementById('tnmt_rounds') ;
	if (table_type != null && tnmt_rounds != null ){
		if (table_type.innerText == '1'){
			var tipo=1;
		}
		else {
			var tipo = 0;
		}
		if (tnmt_rounds != null ){
			var rondas = tnmt_rounds.innerText;
			rondas =  parseInt(rondas) + 2 ;
		}
		oTable = jQuery('.resultsDatatable').dataTable() ; 
	//oTable.fnSort( [ [tnmt_rounds,'asc'] ] );
		if (tipo == '1' ) {
			oTable.fnSort( [ [rondas,'desc'] ] );
		}
		else {
			oTable.fnSort( [ [rondas,'asc'] ] );
		}
	}
}); 




