<?php
/*
Plugin Name: 	Golf Manager
Description: 	Golf Manager
Version:		0.1
Author: 		Nicolas Arenas
: 		GPL3
*/

 
 


register_activation_hook( __FILE__ , 'install_golfman');
register_deactivation_hook(__FILE__,'delete_db');

global $golf_db_version;
$golf_db_version = "0.01";

require_once(dirname(__FILE__).'/includes/sql.php');

add_action ('plugin_loaded' , 'install_golfman');
add_action ('plugin_unloaded' , 'delete_db');
add_action ('admin_menu' , 'menu_page');
add_action ('template_redirect','register_plugins'); 
//add_action ('init','register_lga_member_post');



function register_plugins () {
	$datatable_url = plugins_url('js/jquery.datatables.min.js' , __FILE__);
	wp_enqueue_script('datatables' , $datatable_url,array('jquery')) ; 
	wp_enqueue_script('table_to_datatables', plugins_url ('js/table_to_datatable.js' , __FILE__),array('jquery','datatables'));

}

function register_table_javascript () {
	wp_enqueue_script('table_to_datatables', plugins_url ('js/table_to_datatable.js' , __FILE__),array('jquery','datatables'));
}


function add_my_stylesheet(){
	$myStyleUrl = plugins_url ('css/gm_sample_style.css' , __FILE__);
	wp_register_style('golfManagerStyle',$myStyleUrl,array(),'0.1',''); 
	wp_enqueue_style('golfManagerStyle');
}



function register_datepicker(){
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-datepicker');
}


function headers_js () {
	wp_enqueue_script('admin_init', plugins_url ('js/funciones.js' , __FILE__),array('jquery','datatables'));
	
}


add_action('init', 'add_my_stylesheet');
add_action('init', 'headers_js' ) ; // plugins_url ('js/funciones.js' , __FILE__));
add_action('init', 'register_plugins'); 
//add_action('admin_head-add-tournament','add_datepicker');







function delete_db () {
	drop_database () ;
}

function install_golfman () {
	create_database () ;
}


function menu_page() {
	add_menu_page ("Liga de Golf de Arganda" , "LGA" , "manage_options" , "golf-arganda" , "principal_admin_page_html");// , 3 );

	//Paginas de campos
	add_submenu_page('golf-arganda', 'Add Course','Add Golf Course', 'manage_options', 'add-course' ,'add_course_page');
	// Temporadas
	add_submenu_page('golf-arganda', 'New Season', 'New Season','manage_options' ,'add-season', 'add_season_page') ;
	//Jugadores
	add_submenu_page('golf-arganda','Jugadores','Gestion de jugadores','manage_options','jugadores','add_player_page');
	add_submenu_page('jugadores', 'Add player', 'Add Player','manage_options' ,'add-player', 'add_player_page') ;


	//Torneos
	add_submenu_page('golf-arganda','New Tournament', 'New Tournament', 'manage_options' , 'add-tournament' , 'add_tournament_page') ;
	add_submenu_page('golf-arganda','Register Players in tournaments','Register Players in tournaments', 'manage_options','registers','register_players_page');
	add_submenu_page('golf-arganda','Add Scorecard for tournament','Add Scorecard for tournament', 
'manage_options','add_scorecard','add_scorecard_page');
	
	add_submenu_page('golf-arganda','Import CSV','Import CSV', 'manage_options','import-csv','import_csv_page');

}

function principal_admin_page_html (){
	?>
	<p>This plugin still has a lot of work to go, but hopefully this can get you started with something
	<h2>How to Use</h2>
	<div>
	<strong>Add Score to a Post:</strong><br />
	[score date="year-mo-da"]
	</div>
	<div>
	<strong>List all your rounds of golf:</strong><br />
	[allscores]
	</div>
	<h2>Known Issues</h2>
	<ul>
	<li>Odd display issue when editing course, but it works</li>
	<li>Can\'t edit a score. I recommend removing and readding scores.
	<li>
<?php 
}


function golf_input_field($label, $field, $value = '') {

	$final = '<tr>';
	$final .= '<th><label for="'. $label .'">'. $label .'</label></th>';
	$final .= '<td><input type="text" id="' . $label . '" name="' . $field . '" value="'.$value.'" class="regular-text" /></td>';
	$final .= '</tr>';
	return $final;

}

function golf_input_field_div($label,$field,$value = '') {
	$final = '<div id="' . $label . '"> <label for=' . $label . '>' . $label . '</label> <input type="text" id="' . $label . '" name="' . $field . '" value="'.$value.'" class="regular-text" />' . ' </div>' ;
	return $final;
}

function fill_select_from_sql ($data) {
	foreach ($datas as $data) {
		echo '<option value="' . $$data->ID . '" >' . $tournament->Name . '  ' . $tournament->Date . '</option>' ;
	}

}

function draw_select_tournaments ($funcion) {

	global $wpdb;
	$sql_sel_tournaments = "SELECT ID_Tournament as ID, Name as Name FROM " . $wpdb->prefix . "gm_tournaments" ; 
	
	//$all_players = select_all_players ();
	$tournaments = $wpdb->get_results($sql_sel_tournaments);
	echo '<h3>Evento</h3>'; 
	echo '<select id="sel_tnm" name=tournament onchange=' . $funcion . '>';

	foreach ($tournaments as $tournament) {
		echo '<option value="' . $tournament->ID . '" >' . $tournament->Name .  '</option>' ;
	}
	echo '</select>';
	echo '</div>';
}


function add_season_page() {
	echo '<div class="wrap">';
	echo '<div id="icon-tools" class="icon32"><br /></div>';
	echo '<h2>Nueva Temporada </h2>' ;
	echo '<form method="post" action="' . $_SERVER["REQUEST_URI"] . '">';
	echo '<table class="form-table">';
	echo golf_input_field('Temporada', 'temporada');
	echo '</table>';
	echo '<input type="submit" name="submit_add_season" value="Add Season" />';
	echo '</form>';
	echo '</div>' ;
}


function add_course_page() {

	echo '<div class="wrap">';
	echo '<div id="icon-tools" class="icon32"><br /></div>';
	echo '<h2>Add Course</h2>';
	echo '<form method="post" action="' . $_SERVER["REQUEST_URI"] . '">';
	echo '<table class="form-table">';
	echo golf_input_field('Nombre', 'name');
	echo '<br />';
	echo golf_input_field('Hoyos', 'holes');
	echo '<br />';
	echo golf_input_field('Direccion', 'address');
	echo '<br />';
	echo golf_input_field('Slope Amarillas', 'yellow_slope');
	echo '<br />';
	echo golf_input_field('Slope Rojas', 'red_slope');
	echo '<br />';
	echo golf_input_field('Valor Amarillas', 'yellow_value');
	echo '<br />';
	echo golf_input_field('Valor Rojas', 'red_value');
	echo '<br />';
	echo golf_input_field('Metros Amarillas', 'yellow_meters');
	echo '<br />';
	echo golf_input_field('Metros Rojas', 'red_meters');
	echo '<br />';
	echo '</table>';
	echo '<br />';
	echo '<input type="button" name="show_holes" value="Mostrar Hoyos"  onclick=showAddHoles("hidden"); />' ;

	echo '</div>' ;
	echo '<br />' ;


	//Tabla con los hoyos, par y handicap de los mismos.
	echo '<div id=hidden style="display: none">';
	echo '<table border="1" width="500">';
	echo '<tr>';
	echo '<th>Hoyo</th>';
	for ($i=1; $i <=18 ; $i++) {
		echo '<th>'.$i.'</th>';
	}
	echo '</tr>';
	echo '<br />';
	echo '<tr>';
	echo '<th> Par </th>';
	for ($i =1 ; $i<= 18 ; $i++){
		echo '<td><input size="3" type=text name="par' .$i. '"' . '/' . '></td>';
	}
	echo '</tr>';
	echo '<tr>';
	echo '<th> HP </th>';
	for ($i =1 ; $i<= 18 ; $i++){
		echo '<td><input size="3" type=text name="hp'.$i. '"' . '/' . '/></td>';
	}
	echo '</tr>';
	echo '</table>';
	echo '<input type=submit name="submit_add_course" value="Registrar Campo"/>';
	echo '</div>';
	echo '</form>' ;
	echo '<br />' ;

	//Boton que registra el campo
	//echo '<input type=submit name="submit_add_course" value="Registrar Campo"/>';

}


function add_player_page () {
	echo '<div class="wrap">';
	echo '<div id="icon-tools" class="icon32"><br /></div>';
	echo '<h2>Add Player</h2>';
	echo '<form method="post" enctype="multipart/form-data" action="' . $_SERVER["REQUEST_URI"] . '">';

	echo golf_input_field_div("Licencia","license") ;
	echo golf_input_field_div("Nombre","name") ;
	echo golf_input_field_div("Apellidos","lastname");

	echo '<div id=sex> Sexo <br />';
	echo '<input type="radio" name="sex" value="0" cheked/> Hombre<br />';
	echo '<input type="radio" name="sex" value="1"> Mujer<br />';
	echo '</div>' ;
	echo 'Foto <input type=file name="fotofile" />';

	echo '<input type="submit" name="submit_add_member" value="Add Member">';

	echo '</form>' ;
}


function add_tournament_page () {

	global $wpdb;

	$sql_get_current_season = "SELECT ID_Season,name,Current FROM " .  $wpdb->prefix . "gm_seasons" ;
	$sql_get_fields = "SELECT ID_Field,Name FROM " .  $wpdb->prefix . "gm_fields" ;
	$sql_get_round_types = "SELECT ID_Type,Type FROM " . $wpdb->prefix . "gm_round_type" ;
	$sql_get_tnmts = "SELECT ID_Tournament,Name FROM " . $wpdb->prefix . "gm_tournaments" ; 

	$temporadas = $wpdb->get_results($sql_get_current_season);
	$campos = $wpdb->get_results($sql_get_fields);
	$tipos = $wpdb->get_results($sql_get_round_types);
	$torneos = $wpdb->get_results($sql_get_tnmts); 

	echo '<div class="wrap">';
	echo '<div id="icon-tools" class="icon32"><br /></div>';
	echo '<h2>Add Tournament</h2>';

	// Formulario con la temporada y los datos de creación del torneo una vez que se  mete un torneo se despliega otro
	// formulario que permite registrar jornadas para los torneos.

	echo '<form method="post" action="' . $_SERVER["REQUEST_URI"] . '">';
	echo '<h3> Temporada </h3>';
	echo '<div id=temporadas class=golf-select>';
	echo '<select name=season class=gm_select>';
	foreach ($temporadas as $temporada) {
		$selected = 0;
		if ($temporada->Current == 1 ) {
			$selected=1;
		}
		echo '<option value="'.$temporada->ID_Season .'" selected="'. $selected. '"> ' . $temporada->name . ' </option>' ;
	}

	echo '</select>';
	echo '</div>';

	echo golf_input_field_div('Nombre','tnmt_name');
	
	echo '<input type=date label="Fecha de comienzo" name=begin_date id="tnmt_begin_date" onclick=calendar_gm("tnmt_begin_date") />';
	echo '<div>Merito <input type="checkbox" name="merit"  value="1"></div>';
	echo '<input type=submit name="submit_add_tournament" value="Add Tournament" onclick=showAddHoles("hidden_add_round")>';

	echo '</form>';


	//Tenemos un div oculto que se muestra cuando se añade un torneo.

	echo '<form method="post" action="' . $_SERVER["REQUEST_URI"] . '">';
	
	echo '<div id="hidden_add_round class="gm_hidden_table">';
	echo '<table>' ;
	echo '<tr>' ;
	echo '<td id="label_add_round" class="gm_row_header">' ;
	echo '<h3> Torneo </h3>';
	echo '</td>';
	echo '<td id="label_add_round" class="gm_row_header">' ;
	echo '<h3> Campo </h3>';
	echo '</td>';
	echo '<td id="label_add_round" class="gm_row_header">' ;
	echo '<h3> Tipo </h3>';
	echo '</td>';
	echo '<td id="label_add_round" class="gm_row_header">' ;
	echo '<h3> Fecha </h3>';
	echo '</td>';

	echo '</tr>' ;
	echo '<tr>';
	echo '<td id="select_tnmt_add_round" class="gm_row_content"> ';
	echo '<select id="gm_select_tnmt_add_round" name="select_tnmt_add_round" class="gm_select" onchange=update_rounds_table("gm_add_round_table","gm_select_tnmt_add_round","'. plugins_url("/golfmanager/tmp/rounds_by_tnmt.txt"). '")>' ; 
	foreach ($torneos as $torneo) {
		echo '<option value='.$torneo->ID_Tournament. '>' . $torneo->Name. '</option>' ;
	}
	echo '</select>'; 
	echo '<td id="select_field_add_round" class="gm_row_content"> ';
	echo '<select id="gm_select_field_add_round" name="select_field_add_round" class="gm_select"> ' ;
	foreach ($campos as $campo) {
		echo '<option value='.$campo->ID_Field. '>' . $campo->Name. '</option>' ;
	}
	echo '</select>';
	echo '</td>';
	echo '<td id="select_type_add_round" class="gm_row_content"> ';
	echo '<select id="gm_select_type_add_round" name="select_type_add_round" class="gm_select"> ' ;
	foreach ($tipos as $tipo) {
		echo '<option value='.$tipo->ID_Type.'>' . $tipo->Type . '</option>';  ;
	}
	echo '</select>';
	echo '</td>';
	echo '<td id="select_date_add_round" class="gm_row_content"> ';
	echo '<input type=date name="round_date" id="gm_round_date" onclick=calendar_gm("gm_round_date") />';
	echo '</td>';
	echo '</table>';

	echo '<input type=submit name="submit_add_round" value="Nueva Ronda" onclick=update_rounds_table("gm_add_round_table","gm_select_tnmt_add_round","' . plugins_url("/golfmanager/tmp/rounds_by_tnmt.txt"). '".)>';
	echo '<div id="tnmt_rounds class=gm_table>';
	echo '<table id="add_rounds_table">'; 
	echo '<table>';
	echo '</form>';
	echo '</div>' ;

?>
	<table id="gm_add_round_table">
		<thead>
			<tr>
				<th><h4>Ronda</h4></th>
				<th><h4>Torneo</h4></th>
				<th><h4>Campo</h4></th>
				<th><h4>Tipo</h4></th>
				<th><h4>Fecha</h4></th>
			</tr>
		</thead>
		<tbody>
        </tbody>
	</table>

<?php
}






function register_players_page () {

	global $wpdb;
	$funcion_onchange = 'refresh_select_for_tnmt("sel_tnm","SEL_NOT_REGISTERED","SEL_REGISTERED")'; 

	echo '<div id="icon-tools" class="icon32"><br /></div>';
	echo '<h2>Register Players</h2>';
	echo '<form method="post" id="register_form" value= action="' . $_SERVER["REQUEST_URI"] . '">';

	draw_select_tournaments ($funcion_onchange);
	
	echo '<div id=boxes>';
	echo '<div id=not_registered_players class=golf-select>';


	echo '<select id="SEL_NOT_REGISTERED"  name="no_registered[]" multiple size=15>' ;
	echo '<div id="not_registered_players"></div>';
	echo '</select>' ;
	echo '</div>' ;


	echo '<input type="button" id="moveRight2" value="&gt;" onclick="moveOptions(\'SEL_NOT_REGISTERED\',\'SEL_REGISTERED\')">' ;
	echo '<input type="button" id="moveLeft2" value="&lt;" onclick="moveOptions(\'SEL_REGISTERED\',\'SEL_NOT_REGISTERED\')">' ;


	echo '<div id=registered_players class=golf-select>';
	echo '<select id="SEL_REGISTERED" name=registered_players[] multiple size=15>';
	echo '</select>';
	echo '</div>';
	echo '</div>';

	echo '<input type="button" id="submit_button" name="button_register_players" value="Actualizar inscripciones" onclick=sel_all_options("SEL_REGISTERED","");sel_all_options("SEL_NOT_REGISTERED");submit_form("submit_registers")  />' ;
	echo '<input type=submit id="submit_registers" name="submit_register_players" value="Actualiza"/>' ;

	echo '</form>';
}



function add_scorecard_page() {
	$funcion_onchange = 'update_rnd_for_tnmt("sel_tnm","sel_rnd")' ; 
	
	echo '<form  method="post" id="register_form" value= action="' . $_SERVER["REQUEST_URI"] . '">';
	
	echo '<div>'; 
	draw_select_tournaments ($funcion_onchange);
	
	?> 
	<select id=sel_rnd name="ronda" class=gm_select onchange=fill_select_registered_players_tnmt("sel_tnm","reg_players")  >
	</select>
	
	<select id=reg_players name="licencia" class=gm_select onchange=fill_scorecard("sel_rnd","reg_players","")>
	</select>
	
	</div> 
	<?php 
	echo "No participa";
	echo '<input type=checkbox label="No participa" name="dnp">' ;
	echo golf_input_field_div("Handicap de Juego","hdp",'');
	echo '<div id=add_scorecard>';
	echo '<table class=gm_scorecard_table>';
	echo '<tr>';
	echo '<th>Hoyo</th>';
	for ($i=1; $i <=18 ; $i++) {
		echo '<th>'.$i.'</th>';
	}
	echo '</tr>';
	echo '<br />';
	echo '<tr>';
	echo '<th> Golpes </th>';
	for ($i =1 ; $i<= 18 ; $i++){
		echo '<td><input size="3" type=text id=idGolpes' .$i .' name="golpes' .$i. '"' . '/' . '></td>';
	}
	echo '</tr>';
	echo '<tr>';
	echo '<th> Putts </th>';
	for ($i =1 ; $i<= 18 ; $i++){
		echo '<td><input size="3" type=text id=idPutts' .$i .' name="putts'.$i. '"' . '/' . '/></td>';
	}
	echo '</tr>';
	echo '<tr>';
	echo '<th> Calles </th>';
	for ($i =1 ; $i<= 18 ; $i++){
		echo '<td><input size="3" type=checkbox id=idCalles' .$i .' name="calles'.$i. '"' . '/' . '/></td>';
	}
	echo '</tr>';
	echo '</table>';
	
	echo '<input type=submit name=add_player_scorecad value="Añadir tarjeta" />'; 
	
	echo '</form>' ; 
	
	
}


function import_csv_page () {
	
	$tournaments = get_tournaments () ; 
	

	echo '<div class="wrap">';
	echo '<div id="icon-tools" class="icon32"><br /></div>';
	echo '<h2>Importar resultados desde CSV</h2>';
	echo '<form method="post" enctype="multipart/form-data" action="' . $_SERVER["REQUEST_URI"] . '">';


	echo '<select id="tnmt_id" name="tnmt" class=gm_select onchange=update_rnd_for_tnmt("tnmt_id","rnd_id")>' ; 
	echo '<option>Elija un torneo</option>'; 
	foreach ($tournaments as $tournament) {
		echo '<option value=' . $tournament->ID_Tournament . ' >' . $tournament->Name . ' </option>' ;
	}	
	echo '</select>'; 
	
	echo '<select id="rnd_id" name="rnd">' ; 
	echo '<option>Elija un torneo</option>'; 
	echo '</select>' ; 
	echo '<br>' ; 
	echo '<input type=file name=csv>'; 

	echo '<input type=submit name="import_csv" value="Añadir tarjeta" />'; 
	echo '<br>' ;
	echo '</form>' ; 
		
}


function get_json_not_registered_players () {
	global $wpdb ;
	$tnm_id = intval( $_POST['identificador'] );
	$players = select_players_not_registered ($tnm_id) ;
	$output = json_encode($players) ;
	print_r($output);
	die () ;
}

function get_json_rounds_tnmt() {
	$tnmt_id = $_POST['tnmt_id'];
	$rounds = select_rounds_for_tnmt($tnmt_id) ; 
	$file =dirname(__FILE__).'/tmp/rounds_by_tnmt.txt'; 
	$fh = fopen($file, 'w'); 
	$cadena = print_r($rounds,true);
	fwrite($fh, '{ "aaData":'); 
	fwrite($fh,json_encode($rounds));
	fwrite($fh,'}');
	fclose($fh);  
	echo json_encode($rounds) ; 
	die(); 	
}


function get_json_registered_players () {
	global $wpdb ;
	$tnm_id = intval( $_POST['identificador'] );
	$players = select_players_registered ($tnm_id) ;
	$output = json_encode($players) ;
	print_r($output);
	die () ;
}


function get_json_socrecard () {
	global $wpdb ; 
	$rnd_id  = $_POST['tnmt']; 
	$license = $_POST['license']; 
		$scorecard = select_player_scorecard($rnd_id,$license);
	$output = json_encode($scorecard); 
	print_r($output);
	
	die(); 
}

function get_json_player_rounds () {

	global $wpdb ; 
	$tnmt_id = $_POST['tnmt_id'] ; 
	$license = $_POST['license'] ; 
		
	$player_scorecards_for_tournamet = select_players_scorecard($tnmt_id,$license);
	
	$holes_hp = array(); 
	$holes_result = array(); 	
	
	
	
	foreach ($player_scorecards_for_tournamet as $result) {
		 
		//Calculamos el handicap de juego para cada ronda de cada jugador.
		$field_details = get_field_details($result->ID_Round);
		
		
		$sex = get_sex($license); 
		if ($sex == 1) {
			$slope = $field_details->Red_Slope ;
			$valor_campo = $field_details->Red_value ; 
		}
		else {
			$slope = $field_details->Yellow_Slope ; 
			$valor_campo = $field_details->Yellow_value ; 
		}
	
		
		$par_campo = 72 ; 
		
		//Reduccion de handicap, meter en las tablas del torneo que sea definible
		
		if (($sex == 0) && ($result->hp_play > 26.4)) {
			$holes_result[$result->Date][$result->Number]["hp_ex"] = 26.4;
			$hdp_juego = round(26.4*$slope/113+$valor_campo-$par_campo) ;	
		}
		else {
			$holes_result[$result->Date][$result->Number]["hp_ex"] = $result->hp_play ;
			$hdp_juego = round($result->hp_play*$slope/113+$valor_campo-$par_campo) ;	
		}
	
		// Sacamos si el torneo se ha jugado 
		
		$played = get_if_played ($result->ID_Round);
	
		$holes_result[$result->Date][$result->Number]["Shots"] = $result->Shots ; 
		$holes_result[$result->Date][$result->Number]["Hp"] = $result->Handicap ; 
		$holes_result[$result->Date][$result->Number]["Par"] = $result->Par ; 
		$holes_result[$result->Date][$result->Number]["hp_play"] = $hdp_juego ;
		$holes_result[$result->Date][$result->Number]["Played"] = $played;
		$holes_result[$result->Date][$result->Number]["Value"] = $valor_campo ; 
		$holes_result[$result->Date][$result->Number]["Slope"] = $slope; 
		
	}	
		
	$prueba = json_encode($holes_result) ; 
	print_r($prueba); 
	die() ; 
		
}


add_action('wp_ajax_get_json_not_registered_players','get_json_not_registered_players');
add_action('wp_ajax_get_json_registered_players','get_json_registered_players');
add_action('wp_ajax_get_json_rounds_tnmt','get_json_rounds_tnmt'); 
add_action('wp_ajax_get_json_socrecard','get_json_socrecard'); 
add_action('wp_ajax_nopriv_get_json_player_rounds','get_json_player_rounds');
add_action('wp_ajax_get_json_player_rounds','get_json_player_rounds');




function add_the_season () {

	if (isset($_POST['submit_add_season'])){
		global $wpdb;
		$wpdb->insert($wpdb->prefix."gm_seasons",array('Name' => $_POST['temporada']));
	}
}

function add_the_course () {
	$file = "/var/tmp/debug_add_course.log";
	$fh = fopen($file, 'w');
	$number_of_holes = $_POST['holes'];
	fwrite($fh,"Entro en la funcion\n");

	foreach ($_POST as $key => $value){
		fwrite($fh, "$key es $value\n");
	}

	if (isset($_POST['submit_add_course'])){
		global $wpdb;
		$wpdb->insert($wpdb->prefix."gm_fields",array(
				'Name' => $_POST['name'],
				'Holes' => $_POST['holes'],
				'Address' => $_POST['address'],
				'Yellow_Slope' => $_POST['yellow_slope'],
				'Red_Slope' => $_POST['red_slope'],
				'Yellow_value' => $_POST['yellow_value'],
				'Red_Value' => $_POST['red_value'],
				'Red_Meters' => $_POST['red_meters'],
				'Yellow_meters' => $_POST['yellow_meters']
			));

		fwrite($fh, 'Se supone que he hecho algo');
		fwrite($fh, "Se ha introducido el id:". $wpdb->insert_id ."\n");
		$id_field = $wpdb->insert_id ;
		for ($i=1; $i <= $number_of_holes; $i++){
			$wpdb->insert($wpdb->prefix."gm_holes",array(
					'ID_Field' => $id_field,
					'Number' => $i,
					'Handicap' => $_POST['hp'.$i],
					'Par' => $_POST['par'.$i]
				));
		}
	}
	else {
		fwrite($fh, 'No he hecho nada');
	}
	fclose($fh);
}

function import_csv () {
	
	global $wpdb ;
	
	$fh  = fopen(__DIR__.'/tmp/parse_card.log', 'w') or die("No puedo abrir fichero"); 
	fwrite($fh, "Parseo de tarjetas mediante csv\n");
	$csvdir = __DIR__ . '/storage/csv' ;
	$tb_fields = $wpdb->prefix . 'gm_fields';
	$tb_hdp = $wpdb->prefix . 'gm_play_handicap'; 
	$tb_scores = $wpdb->prefix . "gm_scores" ; 
	
	$id_ronda = $_POST['rnd']; 
	

	if ( ! file_exists($csvdir) ) {
		mkdir($csvdir);
	}
	
	if (isset($_POST['import_csv'])) {
		$uploadfile = "$csvdir/" . $id_ronda. ".csv" ; 
		move_uploaded_file( $_FILES[ 'csv' ][ 'tmp_name' ] , $uploadfile);
		
		
		$registrados = select_players_registered($_POST['tnmt']); 
		$field_details = get_field_details($id_ronda);
		$field_id = $field_details->ID_Field; 
	
		$file = file_get_contents($uploadfile) ; 
		$lines = explode("\n", $file) ; 
		$id_ronda= $_POST['rnd'];
		$licencias =array(); 
		foreach ($registrados as $licencia){
			array_push($licencias, $licencia->License); 
		}
		fwrite($fh, "El campo es $field_id  y la ronda es $id_ronda \n") ; 
		foreach ($licencias as $a) {
			fwrite($fh, "Registrada $a\n");
		}
			
		fwrite($fh, "Empiezo a evaluar el archivo del campo $field_id\n");
		
		if ($field_id == 1) {
		
			foreach ($lines as $line) {
			
				fwrite($fh, "Parseo" . $line . "\n");
				$datos = explode(";", $line) ; 
				$licencia = str_replace('"','', $datos[0]);
				
				if ($licencia == 'Licencia' || $licencia == ''){
					fwrite($fh, "Linea no valida, siguiente\n");
					continue;
				}				
				$hdp=$datos[2];  
				$sex=$datos[5];
				fwrite($fh, "La licencia es $licencia el hdp es: $hdp el sexo es: $sex\n") ; 
				if (in_array($licencia, $licencias)) {
					fwrite($fh, "La licencia $licencia juega!!!!\n"); 
					$hoyo = array(); 
					for ($i=9; $i <=26; $i++) {
						//$hoyo[$i-9+1] = $datos[$i] ;  
						$number = $i-9+1;
						
						$wpdb->insert($tb_scores,array(
							'Number'=>$number, 
							'ID_Round'=>$id_ronda,
							'ID_Field'=>$field_id,
							'License'=>$licencia,
							'Shots'=>$datos[$i]
						),
						array(
							'%d',
							'%d',
							'%d',
							'%s',
							'%d'
						));
						
						fwrite($fh,$wpdb->last_query . "\n");
						$wpdb->print_error;
					}
					
					$wpdb->insert($tb_hdp,array(
							'ID_Round'=>$id_ronda,
							'License'=>$licencia, 
							'hp_play_round'=>$hdp),
							array(
							'%d',
							'%s',
							'%f'
							));
					$wpdb->print_error;
					fwrite($fh,$wpdb->last_query);
				}
				else {
					fwrite($fh, "La licencia $licencia no juega\n");
					for ($i=1 ; $i++ ; $i <= 18){
						$wpdb->insert($tb_scores,array(
							'Number'=>$i, 
							'ID_Round'=>$id_ronda,
							'ID_Field'=>$field_id,
							'License'=>$licencia,
							'Shots'=>0
						),
						array(
							'%d',
							'%d',
							'%d',
							'%s',
							'%d'
						));
					}
					$wpdb->insert($tb_hdp,array(
							'ID_Round'=>$id_ronda,
							'License'=>$licencia, 
							'hp_play_round'=>$hdp),
							array(
							'%d',
							'%s',
							'%f'
					));
					$wpdb->print_error;
				}
			}
		}
		elseif ($field_id == 2) {
			fwrite($fh,  "El campo es $field_id\n");
			foreach ($lines as $line){
				if (preg_match("/^\s/", $line)) {
					$mychars = str_split($line); 
					$license ='';
					$hp_ex = ''; 
					$hp_jue = ''; 
					$hoyos = array(); 
					for ($i=13 ; $i <= 22 ; $i++){
						$license .= $mychars[$i]; 	
					}
					for ($i=27 ; $i <= 30 ; $i++){
						$hp_ex .= $mychars[$i]; 	
					}
					for ($i=31 ; $i <= 32 ; $i++){
						$hp_jue .= $mychars[$i]; 	
					}
					fwrite($fh, "Empiezo a evaluar el archivo del Encin $license $hp_ex $hp_jue");
					for ($j=1 ; $j <= 18 ; $j++){
						$hoyos[$j] .= $mychars[$i];
						$i++; 
						$hoyos[$j] .= $mychars[$i];
						$i++;
					}
				}
				if (in_array($license, $licencias)) {
					fwrite($fh, "La licencia $license juega!!!!\n"); 
					$hoyo = array(); 
					for ($i=1; $i <=18; $i++) {					
						$wpdb->insert($tb_scores,array(
							'Number'=>$i,
							'ID_Round'=>$id_ronda,
							'ID_Field'=>$field_id,
							'License'=>$license,
							'Shots'=>$hoyos[$i]
						),
						array(
							'%d',
							'%d',
							'%d',
							'%s',
							'%d'
						));
					}
					$wpdb->insert($tb_hdp,array(
							'ID_Round'=>$id_ronda,
							'License'=>$license, 
							'hp_play_round'=>$hp_ex),
							array(
							'%d',
							'%s',
							'%f'
							));
				}
				else {
					fwrite($fh, "La licencia $license no juega, relleno la tarjeta a 0.\n");
					for ($i=1 ; $i++ ; $i <= 18){
						$wpdb->insert($tb_scores,array(
							'Number'=>$i, 
							'ID_Round'=>$id_ronda,
							'ID_Field'=>$field_id,
							'License'=>$licencia,
							'Shots'=>0
						),
						array(
							'%d',
							'%d',
							'%d',
							'%s',
							'%d'
						));
					}
					$wpdb->insert($tb_hdp,array(
							'ID_Round'=>$id_ronda,
							'License'=>$licencia, 
							'hp_play_round'=>$hdp),
							array(
							'%d',
							'%s',
							'%f'
					));
				}
			}		
		}
	}
	fclose($fh);
}
function add_the_member () {
	global $wpdb;

	$file = "/var/tmp/debug_add_member.log";
	$fh = fopen($file, 'w');

	$picturesdir = __DIR__ . '/images/pictures_member' ;

	if ( ! file_exists($picturesdir) ) {
		fwrite($fh, "Creando $picturesdir") ;
		mkdir($picturesdir);
	}
	if (isset($_POST['submit_add_member'])){
		fwrite($fh, "Adding new member\n");
		$licencia = $_POST['license'];
		$uploadfile = "$picturesdir/$licencia";

		move_uploaded_file( $_FILES[ 'fotofile' ][ 'tmp_name' ] , $uploadfile);

		$wpdb->insert($wpdb->prefix."gm_members", array ('License' => $licencia ,
				'Name' => $_POST['name'],
				'Lastname' => $_POST['lastname'],
				'Photo' => $uploadfile,
				'Sex' => $_POST['sex']
			)) ;
	}
	fclose($fh);
	$the_query = new WP_Query( $args );
	add_post_meta($post_id, $meta_key, $meta_value, false);
}

function add_the_tournament () {
	global $wpdb;
	$file = "/var/tmp/debug_add_tournament.log";
	$fh = fopen($file, 'w');
	fwrite($fh,"Entro en la funcion\n");

	foreach ($_POST as $key => $value){
		fwrite($fh, "$key es $value\n");
	}


	if (isset($_POST['submit_add_tournament'])){
		$wpdb->insert($wpdb->prefix . "gm_tournaments", array(
				'ID_Season' => $_POST['season'],
				'Name' => $_POST['tnmt_name'],
				'Merit' => $_POST['merit'],
				'Begin_Date' => $_POST['begin_date']
			));
	}
	fclose($fh);
}


function add_the_scorecard () {
	global $wpdb ; 
	$file = "/var/tmp/debug_add_scorecard.log";
	$fh = fopen($file, 'w');
	fwrite($fh,"Entro en la funcion\n");

	foreach ($_POST as $key => $value){
		fwrite($fh, "$key es $value\n");
	}
	
	for ($i = 1; $i <= 18 ; $i++){
		if (! $_POST["dnp"]) {
			$shots = $_POST["golpes$i"];
			$putts = $_POST["putts$i"];
			if ( $_POST["calles$i"] == "on" ) {
				$fairways = 1 ; 
			}
			else 	{
				$fairways = 0; 
			}
		}
		else {
			$shots = 10; 
			$putts = "";  
			$fairways = ""; 
		}
		$wpdb->insert($wpdb->prefix . "gm_scores", array(
				'Number' => $i , 
				'Shots' => $shots , 
				'Fairways' => $fairways, 
				'Putts' => $putts, 
				'ID_Round' => $_POST['ronda'], 
				'License' => $_POST['licencia']
		)); 
		$wpdb->insert($wpdb->prefix . "gm_play_handicap" , array (
				'hp_play_round' => $_POST['hdp'],
				'ID_Round' => $_POST['ronda'],
				'License' => $_POST['licencia']
		));
	}				
}

function add_the_round () {
	global $wpdb;
	$wpdb->insert($wpdb->prefix . "gm_rounds",array(
												'Date'=>$_POST['round_date'] ,
												'ID_Type' => $_POST['select_type_add_round'],
												'ID_Field' => $_POST['select_field_add_round'],
												'ID_Tournament' => $_POST['select_tnmt_add_round']
	)); 
}

function register_the_players () {

	global $wpdb;
	//ID del Torneo
	$tournament = $_POST['tournament'] ;
	// Lista de registrados
	$licencias = $_POST['registered_players'];
	//Lista de no registrados.
	$licencias_noreg = $_POST['no_registered'];

	$file = "/var/tmp/debug_registers.log";
	$fh = fopen($file, 'w');
	fwrite($fh,"Entro en la funcion\n");

	//Registrados sin actualizar
	$registrados = select_players_registered($tournament) ;
	//No registrados sin actualizar

	$no_registrados = select_players_not_registered ($tournament);
	//fwrite($fh, $registrados);


	// Creo un array con las licencias previamente registradas
	foreach ($registrados as $registrado) {
		$lic_registrados[] = $registrado->License ;
	}


	//Creo un array con las licencias previamente NO registradas
	foreach ($no_registrados as $no_registrado) {
		$lic_no_registrados[] = $no_registrado->License ;
	}


	//Chequeo por jugadores no registrados que se tengan que registrar.
	// Recorro las nuevas licencias
	foreach ($licencias as $licencia){
		// Si la licencia esta en las antiguas no hago nada
		if ( in_array($licencia, $lic_registrados)){
			fwrite($fh, "$licencia ya estaba registrada\n");
		}
		// Si no estaba la añado a la base de datos.
		else {
			fwrite($fh,"$licencia no registrada, registrando\n");
			//Registrar aqui en BBDD.
			$wpdb->insert($wpdb->prefix . 'gm_registers', array ('License' => $licencia , 'ID_Tournament' => $tournament)) ;
		}
	}

	// Chequeo Jugadores no registrados anteriormente que
	foreach ($licencias_noreg as $licencia_noreg){
		if ( in_array($licencia_noreg,$lic_no_registrados)) {
			fwrite($fh,"$licencia_noreg Sigue sin cambiar el estado.\n");
		}
		else {
			$sql_delete = $wpdb->prepare("DELETE FROM wp_gm_registers WHERE License = %s AND ID_Tournament = %d" , $licencia_noreg, $tournament);
			$wpdb->query ($sql_delete);
		}
	}
}



// main Loop
if (isset($_POST['submit_add_season'])) {
	add_the_season ();
}
if (isset($_POST['submit_add_course'])){
	add_the_course ();
}
if (isset($_POST['submit_add_member'])){
	add_the_member ();
}
if (isset($_POST['submit_add_tournament'])){
	add_the_tournament  ();
}
if (isset($_POST['submit_register_players'])) {
	register_the_players () ;
}
if (isset($_POST['submit_add_round'])){
	add_the_round (); 
}
if (isset($_POST['add_player_scorecad'])){
	add_the_scorecard () ; 
}
if (isset($_POST['update_player_scorecard'])){
	update_the_scorecard() ; 
}
if (isset($_POST['import_csv'])){
	import_csv ();
}
// Add ajaxurl as global 

add_action('wp_head','pluginname_ajaxurl');

function pluginname_ajaxurl() {
?>
<script type="text/javascript">
var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
</script>
<?php
}


// Shortcodes para las rondas

add_shortcode ('tnmt_table','show_tnmt_table') ; 
add_shortcode ('galeria','show_gallery');

function wp_exist_post_by_title($title_str) {
	global $wpdb;
	return $wpdb->get_row("SELECT ID FROM golf_posts WHERE post_title = '" . $title_str . "' && post_type = 'jugadores'", 'ARRAY_N');

}

function show_gallery ($atts) { 
 	
	$jugadores = get_players() ;
	echo '<div class="gm_table_div">';
	foreach ($jugadores as $jugador){
		$Photo = $jugador->Photo;
		$cambio = '';
		$patron = '/\/var\/www\/golf\//';		
		$foto=preg_replace($patron,$cambio,$Photo);
		$foto_url = site_url($foto);
		
		$post_content = "Nombre " . $jugador->Name . " " . $jugador->Lastname . " Licencia: " . $jugador->License ; 
		$post_content .= "<img src=$foto_url>";
		
		$post_content .= "Esto es una prueba de mierda";
		$title  = 'Ficha del jugador: ' .$jugador->Name . ' ' .$jugador->Lastname ;
		
		$cpt_jugador = wp_exist_post_by_title($title) ;
	
		if (! $cpt_jugador)	{
			$new_post = array(
    			'post_title' => $title ,
    			'post_content' => $post_content,
    			'post_status' => 'publish',
    			'post_date' => date('Y-m-d H:i:s'),
    			'post_author' => $user_ID,
    			'post_type' => 'jugadores',
    			'post_category' => array(0)
    		);
    		$post_id = wp_insert_post($new_post);
    		add_post_meta($post_id, 'Nombre', $jugador->Name, true);
    		add_post_meta($post_id, 'Apellidos', $jugador->Lastname, true);
    		add_post_meta($post_id, 'Licencia', $jugador->License, true);
    	}
    	else {
    		$post_id = $cpt_jugador[0]; 
    	}
		
		//$player_url = plugins_url('includes/player.php' , __FILE__);
		//echo '<div class="gm_member_div">';
		//echo '<a href=' . get_permalink($post_id) . '><img src="' . $foto_url. '" alt="Smiley face" height="190" width="150"></a>';
		//echo '<div class="gm_gal_caption_member">' . $jugador->Name . ' ' . $jugador->Lastname . '</div>' ;
		//echo '</div>';
		
	}
	echo '</div>';
}

function  show_tnmt_table ($atts) {

	//add_action ('template_redirect','register_table_javascript') ;

	$file = __DIR__ . "/tmp/handicap.txt";
	$fh = fopen($file, 'w');	
	extract( shortcode_atts( array(
		'tnmt_id' => '1',
		'division' => '',
		'merito' => ''
	), $atts ) );
		
	$dates  = select_tnmt_dates ($tnmt_id) ;
	$nmb_of_rouns = get_number_of_rounds($tnmt_id);
	$season = get_tnmt_season($tnmt_id);
	
	$max_result = get_max_order ($season); 
	$min_result = get_min_order ($season); 
	$max_points = get_order_max($season); 
					
	
	
	if ($division == ''){	
		$resultados = select_results_for_tournament ($tnmt_id) ; 
		$jugadores = select_players_registered ($tnmt_id) ;
		$totales_torneo = select_player_totals_tnmt ($tnmt_id) ; 
	}
	else {
		$resultados = select_results_for_tournament ($tnmt_id,$division) ; 
		$jugadores = select_players_registered ($tnmt_id) ;
		$totales_torneo = select_player_totals_tnmt ($tnmt_id,$division) ; 
	}
	
	echo '<label style="display:none;" id="tnmt_id">'  . $tnmt_id . '</label>' ; 
	echo '<label style="display:none;" id="tnmt_rounds">'  . $nmb_of_rouns . '</label>' ;
	echo '<label style="display:none;" id="table_type">' . $merito . '</label>' ;
	echo '<table id="tableTournament" class="resultsDatatable">'; 
		echo '<thead>' ;
		echo '<tr>';
			echo '<th style="display:none;"><h4>Licencia</h4></th>' ;
			echo '<th><h4>Jugador</h4></th>' ; 
			foreach ($dates as $fecha){
				echo '<th><h4>'  . $fecha->Date . '</h4></th>' ; 		
			}	 
			echo '<th><h4>Total</h4></th>';
			echo '</tr>'; 
		echo '</thead>';
		echo '<tbody>'; 
			$index=0; 
			$linea = array();
			
			foreach ($totales_torneo as $jugador) {
				echo '<tr>' ; 
				echo '<td style="display:none;">' . $jugador->License . '</td>' ;
				echo '<td>' . $jugador->Name . " " . $jugador->Lastname . '</td>' ;
				$nombre = $jugador->Name . " " . $jugador->Lastname ; 
				$linea[$index]=array(); 
				array_push($linea[$index], $nombre); 
				$golpes_totales = 0 ; 
				$puntos_merito_totales = 0; 
				foreach ($dates as $fecha) {
					$ronda = get_round_result($fecha->ID_Round,$jugador->License); 
					$hdp = get_hdp($fecha->ID_Round,$jugador->License); 
					
					$winner = round_winner($fecha->ID_Round); 
					
					fwrite($fh, "El ganador ha sido $winner\n");
					
					$sex_player = get_sex($jugador->License);
					$field_details = get_field_details ($fecha->ID_Round); 
										
					if ($sex_player == 1) {
						$slope = $field_details->Red_Slope ;
						$valor_campo = $field_details->Red_value ; 
					}
					else {
						$slope = $field_details->Yellow_Slope ; 
						$valor_campo = $field_details->Yellow_value ; 
					}
					
					$par_campo = 72;  
					
					
					fwrite($fh, "Jugado: $played sexo: $sex_player licencia: ".$jugador->License. " handicap $hdp slope: $slope vc: $valor_campo par: $par_campo\n");
					
					
					
					//Reduzco el handicap a los caballeros, mejorar
					if (($hdp > 26.4) && ($sex_player == 0 ) ){
						$hdp = 26.4;
					}
					$hdp_juego = round($hdp*$slope/113+$valor_campo-$par_campo) ; 
					$golpes = $ronda[0]->Golpes;
					$played = get_if_played ($fecha->ID_Round);
					
					

					//Calculo si el jugador ha jugado y si no ha jugado se le pone el resultado a 180 o 0 segun proceda.  
					
					if (($golpes == 0) && ($played == 1)) {
						$golpes = 180 ; 
					}
					elseif ($golpes == 0){
						$golpes = 0; 
					}
					else {
						$golpes_brutos = $golpes ; 
						$golpes = $golpes - $hdp_juego ;  
					}
					
					
					if ($merito == 1){
						if ($golpes < $min_result && $golpes != 0 ){ 
							$puntos_merito = $max_points; 
							if ($jugador->License == $winner){
								$puntos_merito += 5; 
							}
						}
						elseif ($golpes > $max_result || $golpes == 0 ){
							$puntos_merito = 0 ; 
						}
						else {
							$puntos_merito = get_merit_points($season,$golpes);
							if ($jugador->License == $winner){
								$puntos_merito += 5; 
							}
						}
						$puntos_merito_totales += $puntos_merito ; 
						echo '<td>' . $puntos_merito . '</td>' ;
						array_push($linea[$index], $puntos_merito);
					}
					
					else{
						echo '<td>' . $golpes . '</td>' ;
						array_push($linea[$index], $golpes);  
						$golpes_totales += $golpes ; 
					}
				}	
				
				
				
				if ($merito == 1){
					echo '<td>' . $puntos_merito_totales . '</td>' ;
					array_push($linea[$index], $puntos_merito_totales);
				}
				
				else{
					echo '<td>' . $golpes_totales . '</td>' ;
					array_push($linea[$index],$golpes_totales);

					
				}
				echo '</tr>'; 
				$index++; 
			}
			
		echo '</tbody>' ; 
	echo '</table>'; 	
	fclose($fh); 		
}

function round_winner($id_round) {
	
	$roundResults =  get_results_rnd($id_round); 
	$arrayResultados = array(); 
	$fh = fopen(__DIR__ . "/tmp/result.".$id_round, 'w'); 
	foreach ($roundResults as $roundResult) {
		$sex = get_sex($roundResult->License); 
		$ys = $roundResult->Yellow_Slope; 
		$yv = $roundResult->Yellow_Value; 
		$rs = $roundResult->Red_Slope; 
		$rv = $roundResult->Red_Value; 
		$golpes = $roundResult->golpes; 
		$hp_ex = $roundResult->hp_play_round; 
		$par_campo = 72 ; 
		if ($sex == 0) {
			if ($hp_ex > 26.4){
				$hp_ex = 26.4; 
			}
			$hdp = round($hp_ex*$ys/113+$yv-$par_campo); 
		} 
		else {
			$hdp = round($hp_ex*$rs/113+$rv-$par_campo);
		}
		$arrayResultados[$roundResult->License] = 	round($golpes - $hdp) ; 
		fwrite($fh, $roundResult->License . " $golpes $hp_ex $hdp \n" ); 
	}
	asort($arrayResultados); 
	
	$winner = key($arrayResultados);
	foreach ($arrayResultados as $key => $val) {
		fwrite($fh, "$key = $val\n" ); 
		
	}
	
	fwrite($fh, "El ganador es $winner\n") ;
	
	fclose($fh); 
	return $winner; 
}



?>
