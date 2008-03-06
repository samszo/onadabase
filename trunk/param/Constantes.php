<?php

  //
  // Fichier contenant les definitions de constantes
  //define ("PathRoot",$_SERVER["DOCUMENT_ROOT"]."/sites/n/naoss.fr/mundigo"); 
  define ("PathRoot",$_SERVER["DOCUMENT_ROOT"]."/onadabase"); 
  
	// *** chemin de toutes les bases et les spip en service ***
  define("TT_CLASS_BASE", PathRoot."/library/php/");
	// Include the class files.
	require_once(TT_CLASS_BASE."AllClass.php");

  define ("DEFSITE", "mundi");

  $DB_OPTIONS = array (
		'ERROR_DISPLAY' => true
		);
  
  define ("MaxMarker", 300);
  define ("DELIM",'*');
  define ("jsPathRoot",PathRoot."/library/js/");

  define ("XmlParam",PathRoot."/param/SolAcc.xml");

  define('EOL', "\r\n");


$Site = array(
	"SQL_LOGIN" => "root", 
	"SQL_PWD" => "", 
	"SQL_HOST" => "localhost",
	"SQL_DB" => "solacc",
	"NOM" => "SolAcc",//je sais pas
	"SITE_PARENT" => -1,//je sais pas
	"SITE_ENFANT" => -1,
	"GRILLE_GEO" => 1,
	"GRILLE_REG_LEG" => 52,
	"GRILLE_REP_CON" => 59,
	"DEF_LAT" => 45,
	"DEF_LNG" => 1,
	"DEF_ZOOM" => 4,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"gmKey" => "ABQIAAAATs5RWdW8iTPuaiLrkvnqjRTRqFFvaBKBgdVodt96I8idUV-28RTOELCsfx_7hD62HnKwk7Lm8Cg_lQ"
	); 
$SiteNaos = array(
	"SQL_LOGIN" => "naossfr3", 
	"SQL_PWD" => "Eg5PyukqDj", 
	"SQL_HOST" => "sql3",
	"SQL_DB" => "naossfr3",
	"NOM" => "onadabase",//je sais pas
	"SITE_PARENT" => -1,//je sais pas
	"SITE_ENFANT" => -1,
	"GRILLE_GEO" => 1,
	"GRILLE_REG_LEG" => 52,
	"GRILLE_REP_CON" => 59,
	"DEF_LAT" => 45,
	"DEF_LNG" => 1,
	"DEF_ZOOM" => 4,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"gmKey" => gmKey
	); 

	$SiteMundi = array(
	"SQL_LOGIN" => "mundilogcai", 
	"SQL_PWD" => "CnVjzGxb", 
	"SQL_HOST" => "mysql5-5",
	"SQL_DB" => "mundilogcai",
	"NOM" => "onadabase",//je sais pas
	"SITE_PARENT" => -1,//je sais pas
	"SITE_ENFANT" => -1,
	"GRILLE_GEO" => 1,
	"GRILLE_REG_LEG" => 52,
	"GRILLE_REP_CON" => 59,
	"DEF_LAT" => 45,
	"DEF_LNG" => 1,
	"DEF_ZOOM" => 4,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"gmKey" => "ABQIAAAAU9-q_ELxIQ-YboalQWRCjRRWuADP5IHMsclz4StI_c8lb9zkohTyT7mGzubP9DAdxQrlDe2AW8dGTw"
	); 

$SITES = array(
	"local" => $Site
	,"naos" => $SiteNaos
	,"mundi" => $SiteMundi
	);

?>