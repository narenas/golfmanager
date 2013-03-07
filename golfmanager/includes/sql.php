<?php

global $wpdb ; 

$pre = $wpdb->prefix ; 

$tb_members = $pre . "gm_members" ; 
$tb_fields = $pre . "gm_fields" ; 
$tb_holes  = $pre . "gm_holes" ; 
$tb_seasons  = $pre . "gm_seasons" ; 
$tb_tournaments = $pre . "gm_tournaments" ; 
$tb_categories = $pre . "gm_categories" ; 
$tb_scores = $pre . "gm_scores" ; 
$tb_rounds = $pre . "gm_rounds" ; 
$tb_order = $pre . "gm_order" ; 
$tb_type = $pre . "gm_round_type" ;
$tb_registers = $pre . "gm_registers" ; 
$tb_hp_play = $pre . "gm_play_handicap"; 



function get_players(){
	global $wpdb; 
	$pre = $wpdb->prefix ; 
	$tb_members = $pre . "gm_members" ; 

	$sql_str = "SELECT License,Name,Lastname,Photo FROM $tb_members";

	$sql = $wpdb->prepare($sql_str) ; 
	$result =  $wpdb->get_results($sql);
	
	return $result;
}



function get_results_rnd ($id_round) {
	global $wpdb; 
	global $tb_scores; 
	global $tb_hp_play; 
	global $tb_fields; 
	global $tb_rounds; 
	
	$sql_str = "
	Select SUM(Shots) as golpes, scores.License, scores.ID_Round ,hp_tb.hp_play_round, Red_Slope, Red_Value, Yellow_Slope, 	Yellow_Value 
	FROM $tb_scores as scores INNER JOIN $tb_hp_play as hp_tb ON scores.License = hp_tb.License
	INNER JOIN $tb_rounds as rounds ON rounds.ID_Round = scores.ID_Round AND hp_tb.ID_Round = rounds.ID_Round 
	INNER JOIN $tb_fields as fields ON rounds.ID_Field = fields.ID_Field
	WHERE rounds.ID_Round = %d 
	GROUP BY scores.License,hp_tb.hp_play_round
	HAVING golpes != 0 
	ORDER BY 1
	"; 
	
	$sql = $wpdb->prepare($sql_str,$id_round) ; 
	$result =  $wpdb->get_results($sql);  
	return $result ; 
}

function get_merit_points($season,$golpes_totales) {
	global $wpdb; 
	global $tb_order;
	
	$sql_str = "SELECT Points FROM $tb_order WHERE ID_Season = %d and Result = %d";
	$sql = $wpdb->prepare($sql_str,$season,$golpes_totales);
	$result = $wpdb->get_var($sql); 
	return $result; 
}

function get_max_order ($season) {
	global $wpdb;
	global $tb_order;
	
	$sql_str = "SELECT MAX(Result) FROM $tb_order WHERE ID_Season = %d";
	$sql = $wpdb->prepare($sql_str,$season) ;
	$result = $wpdb->get_var($sql);
	return $result;
	
}

function get_min_order ($season) {
	global $wpdb;
	global $tb_order;
	
	$sql_str = "SELECT MIN(Result) FROM $tb_order WHERE ID_Season = %d";
	$sql = $wpdb->prepare($sql_str,$season) ;
	$result = $wpdb->get_var($sql);
	return $result;
	
}

function get_order_max ($season){
	global $wpdb;
	global $tb_order;
	
	$sql_str = "SELECT MAX(Points) FROM $tb_order WHERE ID_Season = %d";
	$sql = $wpdb->prepare($sql_str,$season) ;
	$result = $wpdb->get_var($sql);
	return $result;

}

function get_order_min ($season){
	global $wpdb;
	global $tb_order;
	
	$sql_str = "SELECT MIN(Points) FROM $tb_order WHERE ID_Season = %d";
	$sql = $wpdb->prepare($sql_str,$season) ;
	$result = $wpdb->get_var($sql);
	return $result;

}


function get_tnmt_season($tnmt_id){
	global $wpdb; 
	global $tb_tournaments;
	
	$sql_str = "SELECT ID_Season FROM $tb_tournaments WHERE ID_Tournament= %d";
	$sql = 	 $wpdb->prepare($sql_str,$tnmt_id);
	$result = $wpdb->get_var($sql); 
	return $result; 
} 

function get_hdp($id_round,$license) {
	global $wpdb; 
	global $tb_hp_play; 
	$sql_str = "SELECT hp_play_round as hdp FROM  $tb_hp_play WHERE License = %s AND ID_Round = %d"; 
	$sql = $wpdb->prepare($sql_str,$license,$id_round); 
	$result = $wpdb->get_var($sql); 
	return $result; 
}

function get_number_of_rounds($tnmt_id) {
	global $wpdb; 
	global $tb_rounds;
	
	$sql_str = "SELECT COUNT(*) FROM $tb_rounds WHERE ID_Tournament = %d"; 
	$sql = 	 $wpdb->prepare($sql_str,$tnmt_id);
	$result = $wpdb->get_var($sql); 
	return $result; 
}


function get_field_details ($id_round) {
	global $wpdb ; 
	global $tb_rounds ; 
	global $tb_fields ; 
	$sql_string = "SELECT Yellow_Slope,Yellow_value,Red_Slope,Red_value,Campos.ID_Field FROM $tb_fields as Campos INNER JOIN $tb_rounds as Rondas ON Campos.ID_Field = Rondas.ID_Field WHERE Rondas.ID_Round = %s";
	$sql = $wpdb->prepare($sql_string,$id_round); 
	$result = $wpdb->get_row($sql); 
	return $result ; 
}

function get_sex($License) {
	global $wpdb; 
	global $tb_members; 
	$file = __DIR__ . "/tmp/sexo.txt";
	//$fh = fopen($file, 'a');	

	$sql_str = "SELECT sex FROM $tb_members WHERE License = %s" ; 
	//fwrite ($fh,$sql_str) ;
	$sql = $wpdb->prepare($sql_str,$License); 
	$result = $wpdb->get_var($sql); 
	//fwrite($fh, $License);
	//fclose($fh);
	return $result ; 
	
}

function select_player_totals_tnmt ($tnmt_id,$division = null) {
	global $wpdb ; 
	global $tb_rounds; 
	global $tb_scores ; 
	global $tb_members;
	global $tb_categories; 
	global $tb_registers; 
	
	if ( ! isset($division)) {
		$sql_str = "SELECT ID_Tournament,SUM(Shots) as Golpes,".$tb_scores.".License,Name,Lastname FROM " . $tb_scores .  " INNER JOIN " . $tb_rounds .  " ON " .$tb_scores .  ".ID_Round = " .$tb_rounds.  ".ID_Round INNER JOIN ". $tb_members ." ON ". $tb_members.".License = ".$tb_scores.".License WHERE ID_Tournament = %d GROUP BY License ORDER BY 2; " ; 
		$sql = $wpdb->prepare($sql_str,$tnmt_id); 
	}
	else {
		$sql_str = "
	SELECT $tb_rounds.ID_Tournament,SUM(Shots) as Golpes,$tb_scores.License,$tb_members.Name,$tb_members.Lastname 	FROM 
	$tb_scores INNER JOIN $tb_rounds  ON $tb_scores.ID_Round = $tb_rounds.ID_Round 
	INNER JOIN $tb_members ON $tb_members.License = $tb_scores.License  
	INNER JOIN $tb_categories ON $tb_rounds.ID_Tournament = $tb_categories.ID_Tournament  
	WHERE $tb_rounds.ID_Tournament = %d AND $tb_members.License IN 
	( SELECT License 
	FROM $tb_registers INNER JOIN $tb_categories ON $tb_registers.ID_Tournament = $tb_categories.ID_Tournament 		
	WHERE Handicap_tnmt <= HP_Maximo AND Handicap_tnmt >= HP_Min AND ID_Category = %d) 
	GROUP BY License ORDER BY 2; " ;
		
		
		
		$sql = $wpdb->prepare($sql_str,$tnmt_id,$division); 
	}
	
	$result = $wpdb->get_results($sql); 
	return $result; 

}


function get_if_played ($round_id){
	global $wpdb; 
	global $tb_rounds;
	$sql_str = "SELECT Closed FROM $tb_rounds WHERE ID_Round = %d";
	$sql = $wpdb->prepare($sql_str,$round_id);
	
	$result = $wpdb->get_var($sql);	
	return $result;
	
}


function get_round_result($id_round,$License) {
	global $wpdb; 
	global $tb_scores; 
	global $tb_rounds; 
	$sql_str="SELECT SUM(Shots) as Golpes, License,Date FROM $tb_scores INNER JOIN $tb_rounds ON " . $tb_scores . ".ID_Round =" . $tb_rounds .".ID_Round WHERE License=%s AND " . $tb_rounds . ".ID_Round=%d ORDER BY " . $tb_rounds . ".Date" ; 
	
	$sql = $wpdb->prepare($sql_str,$License,$id_round); 
	$result = $wpdb->get_results($sql); 
	return $result; 

}

function select_results_for_tournament ($tnmt_id) {
	global $wpdb; 
	global $tb_scores; 
	$sql_str="SELECT ID_Tournament,golf_gm_scores.ID_Round,SUM(Shots) as Golpes,License,Date FROM golf_gm_scores INNER JOIN golf_gm_rounds ON golf_gm_scores.ID_Round = golf_gm_rounds.ID_Round GROUP BY ID_Round,License HAVING ID_Tournament = %d ORDER BY ID_Round,Date" ; 
	$sql = $wpdb->prepare($sql_str,$tnmt_id); 
	$result = $wpdb->get_results($sql); 
	return $result; 

}

function select_tnmt_dates ($tnmt_id) {
	
	global $wpdb;
	global $tb_rounds; 
	
	$sql_str = "SELECT DISTINCT Date,ID_Round FROM ".$tb_rounds." WHERE ID_Tournament = %d ORDER BY 1"; 
	$sql = $wpdb->prepare($sql_str,$tnmt_id); 
	$result = $wpdb->get_results($sql); 
	return $result; 
	
}


function get_tournaments () {
	global $wpdb; 
	global $tb_tournaments ; 
	
	$sql_str =  "SELECT ID_Tournament,Name FROM $tb_tournaments" ;
	$sql = $wpdb->prepare($sql_str) ; 
	$result = $wpdb->get_results($sql); 
	return $result ; 	
}

function select_rounds_for_tnmt ($tnmt_id) {
	global $wpdb;
	
	global $tb_tournaments; 
	global $tb_rounds; 
	global $tb_fields; 
	global $tb_type; 
	
	$sql_str = "SELECT rounds.ID_Round as round, tnmts.name  as tournament, types.type as type_name, fields.name as field_name, rounds.date as date FROM $tb_rounds as rounds INNER JOIN $tb_tournaments as tnmts ON rounds.ID_Tournament=tnmts.ID_Tournament INNER JOIN $tb_fields as fields ON rounds.ID_Field=fields.ID_Field INNER JOIN $tb_type as types ON rounds.ID_Type=types.ID_Type WHERE rounds.ID_Tournament = %d"; 
	$sql = $wpdb->prepare($sql_str,$tnmt_id); 
	$result = $wpdb->get_results($sql,ARRAY_N); 
	return $result; 
}

function count_number_of_rounds_for_tnmt($tnmt_id) {
	global $wpdb;
	global $tb_rounds; 
	$sql_str = "SELECT count(rounds.ID_Round) FROM $tb_rounds as rounds WHERE rounds.ID_Tournament = %d"; 
	$sql = $wpdb->prepare($sql_str,$tnmt_id); 
	$result = $wpdb->get_var($sql); 
	return $result; 
}

function select_players_registered ($tournament_id) {
	global $wpdb; 
	global $tb_registers; 
	global $tb_members; 
	
	$sql_str = "SELECT members.License as License ,members.Name as Name ,members.Lastname as Lastname FROM $tb_members as members  inner join $tb_registers registers on  registers.License = members.License WHERE ID_Tournament = %d" ;	
	$sql = 	$wpdb->prepare($sql_str,$tournament_id) ; 
	$result = $wpdb->get_results($sql); 
	return $result;
} 


function select_players_not_registered ($tournament_id) {	
	global $wpdb; 
	global $tb_members ; 
	global $tb_registers; 
	$sql_not_registered_players = "SELECT License,Name,Lastname FROM $tb_members WHERE License not in (SELECT License FROM $tb_registers WHERE ID_Tournament = %d);" ;
	$sql = $wpdb->prepare($sql_not_registered_players,$tournament_id) ; 
	$result = $wpdb->get_results($sql) ;  
	return $result;
}

function select_all_players () {	
	global $wpdb;
	global $tb_members;  
	$sql_all_players = "SELECT License,name,lastname FROM $tb_members" ;
	$result = $wpdb->get_results($sql_all_players); 
	return $result;
}  

function create_database (){
	
	global $wpdb; 
	$pre = $wpdb->prefix; 
	
	$tb_members = $pre . "gm_members" ; 
	$tb_fields = $pre . "gm_fields" ; 
	$tb_holes  = $pre . "gm_holes" ; 
	$tb_seasons  = $pre . "gm_seasons" ; 
	$tb_tournaments = $pre . "gm_tournaments" ; 
	$tb_categories = $pre . "gm_categories" ; 
	$tb_scores = $pre . "gm_scores" ; 
	$tb_rounds = $pre . "gm_rounds" ; 
	$tb_order = $pre . "gm_order" ; 
	$tb_type = $pre . "gm_round_type" ;
	$tb_registers = $pre . "gm_registers" ; 
	$tb_hp_play = $pre . "gm_play_handicap"; 


	
	
$big_sql = "

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


CREATE TABLE $tb_holes (
                ID_Field SMALLINT NOT NULL,
                Number TINYINT NOT NULL,
                Handicap TINYINT NOT NULL,
                Par TINYINT NOT NULL,
                PRIMARY KEY (ID_Field, Number)
);


CREATE TABLE $tb_type (
                ID_Type TINYINT NOT NULL,
                Type VARCHAR(30) NOT NULL,
                PRIMARY KEY (ID_Type)
);


CREATE TABLE $tb_members (
                LIcense VARCHAR(10) NOT NULL,
                Name VARCHAR(20) NOT NULL,
                Lastname VARCHAR(20) NOT NULL,
                Photo VARCHAR(100),
                Sex BINARY NOT NULL,
                Season_HP DECIMAL(1) NOT NULL,
                Real_HP DECIMAL(1) NOT NULL,
                PRIMARY KEY (LIcense)
);


CREATE TABLE $tb_seasons (
                ID_Season TINYINT NOT NULL,
                Current BOOLEAN DEFAULT FALSE NOT NULL,
                Name VARCHAR(10) NOT NULL,
                PRIMARY KEY (ID_Season)
);


CREATE TABLE $tb_tournaments (
                ID_Tournament INT AUTO_INCREMENT NOT NULL,
                ID_Season TINYINT NOT NULL,
                Name VARCHAR(30) NOT NULL,
                Merit BOOLEAN NOT NULL,
                Begin_Date DATE NOT NULL,
                PRIMARY KEY (ID_Tournament)
);


CREATE TABLE $tb_registers (
                ID_Tournament INT NOT NULL,
                LIcense VARCHAR(10) NOT NULL,
                Handicap_tnmt DECIMAL NOT NULL,
                PRIMARY KEY (ID_Tournament, LIcense)
);


CREATE TABLE $tb_categories (
                ID_Category TINYINT AUTO_INCREMENT NOT NULL,
                Name VARCHAR(10) NOT NULL,
                HP_Min DECIMAL(1) NOT NULL,
                HP_Maximo DECIMAL(1) NOT NULL,
                ID_Tournament INT NOT NULL,
                PRIMARY KEY (ID_Category)
);


CREATE TABLE $tb_rounds (
                ID_Round INT AUTO_INCREMENT NOT NULL,
                Date DATE NOT NULL,
                ID_Type TINYINT NOT NULL,
                ID_Field SMALLINT NOT NULL,
                ID_Tournament INT NOT NULL,
                PRIMARY KEY (ID_Round)
);


CREATE TABLE $tb_hp_play (
                ID_Round INT NOT NULL,
                LIcense VARCHAR(10) NOT NULL,
                hp_play_round INT NOT NULL,
                PRIMARY KEY (ID_Round, LIcense)
);


CREATE TABLE $tb_scores (
                ID_Field SMALLINT NOT NULL,
                Number TINYINT NOT NULL,
                LIcense VARCHAR(10) NOT NULL,
                ID_Round INT NOT NULL,
                Shots TINYINT NOT NULL,
                Fairways BINARY NOT NULL,
                Putts TINYINT NOT NULL,
                PRIMARY KEY (ID_Field, Number, LIcense, ID_Round)
);


CREATE TABLE $tb_order (
                id_merit INT NOT NULL,
                ID_Season TINYINT NOT NULL,
                Result SMALLINT NOT NULL,
                Points SMALLINT NOT NULL,
                PRIMARY KEY (id_merit)
);


ALTER TABLE $tb_rounds ADD CONSTRAINT wp_gm_field_wp_gm_tournaments_fk
FOREIGN KEY (ID_Field)
REFERENCES $tb_fields (ID_Field)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE $tb_holes ADD CONSTRAINT wp_gm_field_wp_gm_holes_fk
FOREIGN KEY (ID_Field)
REFERENCES $tb_fields (ID_Field)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE $tb_scores ADD CONSTRAINT wp_gm_holes_vp_gm_scores_fk
FOREIGN KEY (ID_Field, Number)
REFERENCES $tb_holes (ID_Field, Number)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE $tb_rounds ADD CONSTRAINT wp_gm_tournament_type_wp_gm_tournaments_fk
FOREIGN KEY (ID_Type)
REFERENCES $tb_type (ID_Type)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE $tb_scores ADD CONSTRAINT wp_gm_members_vp_gm_scores_fk
FOREIGN KEY (LIcense)
REFERENCES $tb_members (LIcense)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE $tb_registers ADD CONSTRAINT wp_gm_members_wp_gm_registers_fk
FOREIGN KEY (LIcense)
REFERENCES $tb_members (LIcense)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE $tb_hp_play ADD CONSTRAINT wp_gm_members_handicap_juego_fk
FOREIGN KEY (LIcense)
REFERENCES $tb_members (LIcense)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE $tb_order ADD CONSTRAINT wp_gm_seasons_merit_order_fk
FOREIGN KEY (ID_Season)
REFERENCES $tb_seasons (ID_Season)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE $tb_tournaments ADD CONSTRAINT wp_gm_seasons_wp_gm_tournaments_fk1
FOREIGN KEY (ID_Season)
REFERENCES $tb_seasons (ID_Season)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE $tb_rounds ADD CONSTRAINT wp_gm_tournaments_wp_gm_rounds_fk
FOREIGN KEY (ID_Tournament)
REFERENCES $tb_tournaments (ID_Tournament)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE $tb_categories ADD CONSTRAINT wp_gm_tournaments_wp_gm_categories_fk
FOREIGN KEY (ID_Tournament)
REFERENCES $tb_tournaments (ID_Tournament)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE $tb_registers ADD CONSTRAINT wp_gm_tournaments_wp_gm_registers_fk1
FOREIGN KEY (ID_Tournament)
REFERENCES $tb_tournaments (ID_Tournament)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE $tb_scores ADD CONSTRAINT wp_gm_tournaments_vp_gm_scores_fk
FOREIGN KEY (ID_Round)
REFERENCES $tb_rounds (ID_Round)
ON DELETE NO ACTION
ON UPDATE NO ACTION;

ALTER TABLE $tb_hp_play ADD CONSTRAINT wp_gm_rounds_handicap_juego_fk
FOREIGN KEY (ID_Round)
REFERENCES $tb_rounds (ID_Round)
ON DELETE NO ACTION
ON UPDATE NO ACTION;
"; 

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	

	dbDelta($big_sql); 

}

function drop_database () {
	
	global $wpdb; 
	$pre = $wpdb->prefix; 
	
	$tb_members = $pre . "gm_members" ; 
	$tb_fields = $pre . "gm_fields" ; 
	$tb_holes  = $pre . "gm_holes" ; 
	$tb_seasons  = $pre . "gm_seasons" ; 
	$tb_tournaments = $pre . "gm_tournaments" ; 
	$tb_categories = $pre . "gm_categories" ; 
	$tb_scores = $pre . "gm_scores" ; 
	$tb_rounds = $pre . "gm_rounds" ; 
	$tb_order = $pre . "gm_order" ; 
	$tb_type = $pre . "gm_round_type" ;
	$tb_registers = $pre . "gm_registers" ; 
	$tb_hp_play = $pre . "wp_gm_play_handicap"; 
	
	$wpdb->query ("drop table if exists $tb_members"); 
	$wpdb->query ("drop table if exists $tb_fields");
	$wpdb->query ("drop table if exists $tb_holes");
	$wpdb->query ("drop table if exists $tb_seasons");
	$wpdb->query ("drop table if exists $tb_tournaments");
	$wpdb->query ("drop table if exists $tb_categories");
	$wpdb->query ("drop table if exists $tb_scores");
	$wpdb->query ("drop table if exists $tb_rounds");
	$wpdb->query ("drop table if exists $tb_order");
	$wpdb->query ("drop table if exists $tb_type");
	$wpdb->query ("drop table if exists $tb_registers");
	$wpdb->query ("drop table if exists $tb_hp_play");

	
}


function select_player_scorecard ($rnd_id,$player_id) {
	global $wpdb; 
	global $tb_scores ; 
	
	$sql_string = "SELECT Number, Shots, Fairways, putts FROM $tb_scores WHERE ID_Round = %d AND License = %s"; 
	$sql = 	$wpdb->prepare($sql_string,$rnd_id,$player_id) ; 
	
	$file = "/var/tmp/sql_debug.log";
	$fh = fopen($file, 'w');
	fwrite($fh, "$sql_string $player_id $rnd_id"); 
	fclose($fh); 	
	$result = $wpdb->get_results($sql); 
	return $result;	
}

function select_players_scorecard($tnmt_id,$license) {
	global $wpdb;
	global $tb_holes; 
	global $tb_scores; 
	global $tb_rounds; 
	global $tb_fields; 	
	global $tb_hp_play; 
	$fh = fopen(__DIR__ . "/../tmp/log-sql.txt" ,'w') ; 
	$sql_string = "
	Select $tb_hp_play.License , hoyos.Handicap , Par , Shots , scores.ID_Round , hoyos.Number ,$tb_rounds.Date,$tb_hp_play.hp_play_round as hp_play
	FROM $tb_holes as hoyos INNER JOIN $tb_scores as scores  ON 
	hoyos.Number = scores.Number AND hoyos.ID_Field = scores.ID_Field
	INNER JOIN $tb_rounds ON $tb_rounds.ID_Round = scores.ID_Round
	INNER JOIN $tb_hp_play ON $tb_rounds.ID_Round = $tb_hp_play.ID_Round
	WHERE ID_Tournament = %d AND 
	$tb_hp_play.License  = %s AND 
	$tb_hp_play.License = scores.License
	ORDER BY ID_Round,$tb_hp_play.License,Number
	"; 
	fwrite($fh, $sql_string); 
	$sql = 	$wpdb->prepare($sql_string,$tnmt_id,$license) ;
	$result = $wpdb->get_results($sql); 
	fclose($fh); 
	return $result;	
	
}

?>