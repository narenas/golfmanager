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

add_action ('plugin_loaded' , 'install_golfman');
add_action ('plugin_unloaded' , 'delete_db'); 
add_action ('admin_menu' , 'menu_page'); 

add_action('wp_print_styles', 'add_my_stylesheet');
add_action('init', 'headers_js' ) ; // plugins_url ('js/funciones.js' , __FILE__)); 

add_action('admin_head-add-tournament','add_datepicker');

function register_datepicker(){
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-datepicker');
}

add_action('calendario','calendar');

function headers_js () {
	wp_enqueue_script('admin_init', plugins_url ('js/funciones.js' , __FILE__));
}

function delete_db () {
	global $wpdb; 
	$pre = $wpdb->prefix; 
	
	$tb_members = $pre . "gm_members" ; 
	$tb_fields = $pre . "gm_fields" ; 
	$tb_holes  = $pre . "gm_holes" ; 
	$tb_seasons  = $pre . "gm_seasons" ; 
	$tb_tournaments = $pre . "gm_tournaments" ; 
	$tb_categories = $pre . "gm_categories" ; 
	$tb_scores = $pre . "gm_scores" ; 
	$tb_order = $pre . "gm_order" ; 
	$tb_type = $pre . "gm_tournament_type" ;
	$tb_registers = $pre . "gm_registers" ; 

	
	
	$wpdb->query ("drop table if exists $tb_members"); 
	$wpdb->query ("drop table if exists $tb_fields");
	$wpdb->query ("drop table if exists $tb_holes");
	$wpdb->query ("drop table if exists $tb_seasons");
	$wpdb->query ("drop table if exists $tb_tournaments");
	$wpdb->query ("drop table if exists $tb_categories");
	$wpdb->query ("drop table if exists $tb_scores");
	$wpdb->query ("drop table if exists $tb_order");
	$wpdb->query ("drop table if exists $tb_type");
	$wpdb->query ("drop table if exists $tb_registers");
	
}


function install_golfman () {
	
	// Funcion que detalla la base de datos.  
	// $wpdb es una funcion global que contiene la conexión con la base de datos. 
	
	global $wpdb; 
	$pre = $wpdb->prefix ; 
	
	//Definición de los nombres de las tablas que forman parte del plugin. 
	 
	$tb_members = $pre . "gm_members" ; 
	$tb_fields = $pre . "gm_fields" ; 
	$tb_holes  = $pre . "gm_holes" ; 
	$tb_seasons  = $pre . "gm_seasons" ; 
	$tb_tournaments = $pre . "gm_tournaments" ; 
	$tb_categories = $pre . "gm_categories" ; 
	$tb_scores = $pre . "gm_scores" ; 
	$tb_order = $pre . "gm_order" ; 
	$tb_type = $pre . "gm_tournament_type" ; 
	$tb_registers = $pre . "gm_registers" ;
	
	// Gran SQl para la creación de la Base de Datos.  
	

	
	
	
	//Sentencias SQL para la creación de las tablas en el mysql del wordpress. 
	$sql_members = "
		CREATE TABLE $tb_members (
                License VARCHAR(10) NOT NULL,
                Id_Season TINYINT NOT NULL,
                Name VARCHAR(20) NOT NULL,
                Lastname VARCHAR(20) NOT NULL,
                Photo VARCHAR (100), 
                Sex BINARY NOT NULL,
                Season_HP DECIMAL(1) NOT NULL,
                Real_HP DECIMAL(1) NOT NULL,
                PRIMARY KEY (License)
                );
	"; 
	
	$sql_fields ="
		CREATE TABLE $tb_fields (
                ID_Field SMALLINT AUTO_INCREMENT NOT NULL,
                Name VARCHAR(50) NOT NULL,
                Holes TINYINT NOT NULL,
                Address VARCHAR(50) NOT NULL,
                Yellow_Slope DECIMAL NOT NULL,
                Red_Slope DECIMAL NOT NULL,
                Yellow_value DECIMAL NOT NULL,
                Yellow_meters INT NOT NULL,
                Red_Meters VARCHAR(50) NOT NULL,
                Red_Value DECIMAL NOT NULL,
                PRIMARY KEY (ID_Field)
                );	
";
	
	$sql_holes = " 
		CREATE TABLE $tb_holes (
                ID_Hole INT AUTO_INCREMENT NOT NULL,
                ID_Field SMALLINT NOT NULL,
                Number TINYINT NOT NULL,
                Handicap TINYINT NOT NULL,
                Par TINYINT NOT NULL,
                PRIMARY KEY (ID_Hole)
                );
	";  
	$sql_seasons = "
		CREATE TABLE $tb_seasons (
                ID_Season TINYINT AUTO_INCREMENT NOT NULL,
                Current BOOLEAN DEFAULT FALSE NOT NULL, 	
                Name VARCHAR(10) NOT NULL,
                PRIMARY KEY (ID_Season)
                );
	"; 
	
	$sql_tournaments = "
		CREATE TABLE $tb_tournaments (
                ID_Tournament INT AUTO_INCREMENT NOT NULL,
                ID_Season TINYINT NOT NULL,
                Date DATE NOT NULL,
                ID_Type TINYINT NOT NULL,
                League BINARY NOT NULL,
                Merit BINARY NOT NULL,
                ID_Field SMALLINT NOT NULL,
                PRIMARY KEY (ID_Tournament)
                );
	";  
	
	$sql_categories = " 
		CREATE TABLE $tb_categories (
                ID_Category TINYINT AUTO_INCREMENT NOT NULL,
                Name VARCHAR(10) NOT NULL,
                HP_Min DECIMAL(1) NOT NULL,
                HP_Maximo DECIMAL(1) NOT NULL,
                ID_Tournament INT NOT NULL,
                PRIMARY KEY (ID_Category)
                );
	"; 
	
	$sql_scores = " 
		CREATE TABLE $tb_scores (
                License VARCHAR(10) NOT NULL,
                ID_Hole INT NOT NULL,
                ID_Tournament INT NOT NULL,
                Shots TINYINT NOT NULL,
                Fayways BINARY NOT NULL,
                Putts TINYINT NOT NULL,
                PRIMARY KEY (License, ID_Hole, ID_Tournament)
                );	
    ";
	
	$sql_order = "
		CREATE TABLE $tb_order (
                id_merit INT AUTO_INCREMENT NOT NULL,
                ID_Season TINYINT NOT NULL,
                Result SMALLINT NOT NULL,
                Points SMALLINT NOT NULL,
                PRIMARY KEY (id_merit)
                );
	";
	$sql_tournament_type = "
	CREATE TABLE $tb_type (
                ID_Type TINYINT NOT NULL,
                Type VARCHAR(30) NOT NULL,
                PRIMARY KEY (ID_Type)
                );
	"; 
	
	$sql_registers = "
	CREATE TABLE $tb_registers (
				License VARCHAR(10) NOT NULL,
				ID_Tournament INT NOT NULL, 
				PRIMARY KEY (License, ID_Tournament)); 
	"; 	
	
	
	$sql_misc = "
ALTER TABLE wp_gm_tournaments ADD CONSTRAINT wp_gm_field_wp_gm_tournaments_fk
FOREIGN KEY (ID_Field)
REFERENCES wp_gm_field (ID_Field)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE wp_gm_holes ADD CONSTRAINT wp_gm_field_wp_gm_holes_fk
FOREIGN KEY (ID_Field)
REFERENCES wp_gm_field (ID_Field)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE wp_gm_scores ADD CONSTRAINT wp_gm_holes_vp_gm_scores_fk
FOREIGN KEY (ID_Hole)
REFERENCES wp_gm_holes (ID_Hole)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE wp_gm_tournaments ADD CONSTRAINT wp_gm_tournament_type_wp_gm_tournaments_fk
FOREIGN KEY (ID_Type)
REFERENCES wp_gm_tournament_type (ID_Type)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE wp_gm_scores ADD CONSTRAINT wp_gm_members_vp_gm_scores_fk
FOREIGN KEY (LIcense)
REFERENCES wp_gm_members (LIcense)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE wp_gm_registers ADD CONSTRAINT wp_gm_members_wp_gm_registers_fk
FOREIGN KEY (LIcense)
REFERENCES wp_gm_members (LIcense)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE wp_gm_tournaments ADD CONSTRAINT wp_gm_seasons_wp_gm_tournaments_fk
FOREIGN KEY (ID_Season)
REFERENCES wp_gm_seasons (ID_Season)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE wp_gm_order ADD CONSTRAINT wp_gm_seasons_merit_order_fk
FOREIGN KEY (ID_Season)
REFERENCES wp_gm_seasons (ID_Season)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE wp_gm_scores ADD CONSTRAINT wp_gm_tournaments_vp_gm_scores_fk
FOREIGN KEY (ID_Tournament)
REFERENCES wp_gm_tournaments (ID_Tournament)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE wp_gm_categories ADD CONSTRAINT wp_gm_tournaments_wp_gp_categories_fk
FOREIGN KEY (ID_Tournament)
REFERENCES wp_gm_tournaments (ID_Tournament)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE wp_gm_registers ADD CONSTRAINT wp_gm_tournaments_wp_gm_registers_fk
FOREIGN KEY (ID_Tournament)
REFERENCES wp_gm_tournaments (ID_Tournament)
ON DELETE NO ACTION
ON UPDATE NO ACTION;	"; 
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	dbDelta($sql_tournaments);
	dbDelta($sql_fields);
	dbDelta($sql_holes);
	dbDelta($sql_members);
	dbDelta($sql_tournament_type);
	dbDelta($sql_order);
	dbDelta($sql_scores);
	dbDelta($sql_seasons);
	dbDelta ($sql_categories); 
	dbDelta($sql_registers); 
	dbDelta($sql_misc); 
	
	// add_option('golfman_db_version', $golf_db_version);
	
	
}


function menu_page() {
	add_menu_page ("Liga de Golf de Arganda" , "LGA" , "manage_options" , "golf-arganda" , "principal_admin_page_html" , 3 ); 
	
	//Paginas de campos
	add_submenu_page('golf-arganda', 'Add Course','Add Golf Course', 'manage_options', 'add-course' ,'add_course_page'); 
	//add_submenu_page('golf-arganda', 'Course Management', 'Course Management', 'manage_options', 'manage-course', 'manage_golf_field_page');
	
	// Temporadas
	add_submenu_page('golf-arganda', 'New Season', 'New Season','manage_options' ,'add-season', 'add_season_page') ; 
	
	//Jugadores 
	add_submenu_page('golf-arganda', 'Add player', 'Add Player','manage_options' ,'add-player', 'add_player_page') ;
	
	//add_submenu_page('golf_arganda', 'Add Player', 'Add player','manage-options' ,'add_player_page') ; 
	
	//Torneos
	add_submenu_page('golf-arganda','New Tournament', 'New Tournament', 'manage_options' , 'add-tournament' , 'add_tournament_page') ; 
	add_submenu_page('golf-arganda','Register Players in tournaments','Register Players in tournaments', 'manage_options','registers','register_players_page'); 
	
	
}

function principal_admin_page_html (){
	echo '<p>Welcome to the Golf Tracker plugin. I hope this helps you track your progress in golf. This was originally written for my blog at <a href="http://tothepga.com/">http://tothepga.com</a>. I decided if it helps me then maybe it can help others. So enjoy.</p>';
	echo '<p>This plugin still has a lot of work to go, but hopefully this can get you started with something';
	echo '<h2>How to Use</h2>';
	echo '<div>';
	echo '<strong>Add Score to a Post:</strong><br />';
	echo '[score date="year-mo-da"]';
 	echo '</div>';
 	echo '<div>';
 	echo '<strong>List all your rounds of golf:</strong><br />';
 	echo '[allscores]';
	echo '</div>';
	echo '<h2>Known Issues</h2>';
	echo '<ul>';
	echo '<li>Odd display issue when editing course, but it works</li>';
	echo '<li>Can\'t edit a score. I recommend removing and readding scores.';
	echo '<li>';
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
	echo '<input type="button" name="show_holes" value="Mostrar Hoyos"  onclick="showAddHoles();" />' ;
	
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
	$sql_get_tour_types = "SELECT ID_Type,Type FROM " . $wpdb->prefix . "gm_tournament_type" ; 
	
	$temporadas = $wpdb->get_results($sql_get_current_season); 
	$campos = $wpdb->get_results($sql_get_fields); 
	$tipos = $wpdb->get_results($sql_get_tour_types); 
	
	
	
 
	echo '<div class="wrap">';
	echo '<div id="icon-tools" class="icon32"><br /></div>';
	echo '<h2>Add Tournament</h2>'; 
	echo '<form method="post" action="' . $_SERVER["REQUEST_URI"] . '">';
	
	echo '<h3> Temporada </h3>'; 
	echo '<div id=temporadas class=golf-select>'; 
	echo '<select name=season>'; 
	 
	foreach ( $temporadas as $temporada) {
		$selected = 0;
		if ($temporada->Current == 1 ) {
			$selected=1; 
		}
		echo '<option value="'.$temporada->ID_Season .'" selected="'. $selected. '"> ' . $temporada->name . ' </option>' ; 
	}
	echo '</select>'; 
	echo '</div>';
	
	
	echo '<h3> Campo </h3>';
	echo '<div id=campos class=golf-radio>' ;
	foreach ($campos as $campo) {
		echo $campo->Name . ' <input type=radio  name=course value="' . $campo->ID_Field . '" >' ; 
	}
	echo '</div>' ;
	
	echo '<h3> Tipo </h3>';
	echo '<div id=type class=golf-radio>' ;
	foreach ($tipos as $tipo) {
		echo $tipo->Type . ' <input type=radio  name=type value="' . $tipo->ID_Type . '" >' ; 
	}
	echo '</div>' ;
	
	echo '<h3> Liga </h3>';
	echo '<div>Liga <input type="checkbox" name="league" value="1"> </div>'; 
	
	echo '<h3> Orden de Mérito </h3>';
	echo '<div>Merito <input type="checkbox" name="merit"  value="1"></div>'; 
	
	echo '<h3> Fecha</h3>';
	echo '<input type=date name=date id=datepicker />'; 
	echo '<script type="text/javascript">'; 
    echo  'jQuery(document).ready(function(){'; 
    echo  'jQuery(".tournament_date").datepicker({ ';
    echo  	"dateFormat : 'D, m/d/yy'"; 
    echo     '})';
    echo ' });' ;
    echo '</script>';
    echo '<br />'; 
    echo '<input type=submit name="submit_add_tournament" value="Add Tournament">';
	echo '</form>';
	
}

function select_players_registered ($tournament_id) {
	
	global $wpdb; 
	$sql_registered_players = "SELECT License FROM wp_gm_registers WHERE ID_Tournament = %d" ;
	$sql = $wpdb->prepare($sql_registered_players,$tournament_id) ; 
	$result = $wpdb->get_results($sql,ARRAY_A); 
	return $result;
} 

function select_players_not_registered ($tournament_id) {	
	global $wpdb; 
	$sql_not_registered_players = "SELECT License FROM wp_gm_members WHERE License not in (SELECT License FROM wp_gm_registers WHERE ID_Tournament = %d;)" ;
	$sql = $wpdb->prepare($sql_not_registered_players,$tournament_id) ; 
	$result = $wpdb->get_results($sql,ARRAY_A); 
	return $result;
}

function select_all_players () {	
	global $wpdb; 
	$sql_all_players = "SELECT License,name,lastname FROM wp_gm_members" ;
	$result = $wpdb->get_results($sql_all_players); 
	return $result;
}  


function register_players_page () {
	global $wpdb; 
	$sql_sel_tournaments = "SELECT tnmts.ID_Tournament as ID, tnmts.Date as Date, field.name as Name FROM wp_gm_tournaments tnmts inner join   wp_gm_fields field on field.ID_Field = tnmts.ID_FIeld;"; 
	$all_players = select_all_players (); 
	$tournaments = $wpdb->get_results($sql_sel_tournaments);
	
		
	echo '<div id="icon-tools" class="icon32"><br /></div>';
	echo '<h2>Register Players</h2>'; 
	echo '<form method="post" action="' . $_SERVER["REQUEST_URI"] . '">';	
	echo '<div id=tournaments class=golf-select>' ; 
	echo '<select name=tournament onchange=refresh_select()>'; 
	foreach ($tournaments as $tournament) {
		echo '<option value="' . $tournament->ID . '" >' . $tournament->Name . '  ' . $tournament->Date . '</option>' ;    
	}
	echo '</select>'; 
	echo '</div>';
	
	echo '<div id=boxes>'; 
	//echo '<div id=not_registered_players class=golf-select>'; 
	echo '<select id="LEFT_MENU2"  name="no_registered" multiple size=15>' ;
	 
	foreach ($all_players as $player) {
		echo '<option value="' . $player->License . '" >' . $player->name . ' ' . $player->lastname . ' </option>'; 
 
	} 
	echo '</select>'; 
	//echo '</div>' ;
	
	
		echo '<input type="button" id="moveRight2" value="&gt;" onclick="moveOptions(\'LEFT_MENU2\',\'RIGHT_MENU2\')">' ;
		echo '<input type="button" id="moveLeft2" value="&lt;" onclick="moveOptions(\'RIGHT_MENU2\',\'LEFT_MENU2\')">' ; 
	
	
	//echo '<div id=registered_players class=golf-select>'; 
	echo '<select id="RIGHT_MENU2" name=registered_players multiple size=15>';
	echo '</select>'; 
	//echo '</div>'; 
	echo '</div>';
	echo '</form>'; 
	
	
}


function add_the_season () {
	
	$file = "/var/tmp/debug.log"; 
	$fh = fopen($file, 'w'); 
	fwrite($fh,'Entro en la funcion'); 	
	if (isset($_POST['submit_add_season'])){
		global $wpdb; 
		$wpdb->insert($wpdb->prefix."gm_seasons",array('Name' => $_POST['temporada']));  
		fwrite($fh, 'Se supone que he hecho algo'); 
		fwrite($fh, "Se ha introducido el id:". $wpdb->insert_id ."\n"); 
	}
	
	else {
		fwrite($fh, 'No he hecho nada'); 
	}
	fclose($fh); 
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
																'ID_Type' => $_POST['type'],
																'Date' => $_POST['date'],
																'League' => $_POST['league'],
																'Merit' => $_POST['merit'],
																'ID_Field' => $_POST['course']
																)); 
	}
	fclose($fh);
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


?>
