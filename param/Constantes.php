<?php

  //
  // Fichier contenant les definitions de constantes
  //define ("PathRoot",$_SERVER["DOCUMENT_ROOT"]."/sites/n/naoss.fr/mundigo"); 
  //define ("PathRoot",$_SERVER["DOCUMENT_ROOT"]."/onadabase"); 
  define ("PathRoot","C:/wamp/www/"."onadabase"); 
  
	// *** chemin de toutes les bases et les spip en service ***
  define("TT_CLASS_BASE", PathRoot."/library/php/");
	// Include the class files.
	require_once(TT_CLASS_BASE."AllClass.php");

  define ("TRACE", false);

  define ("DEFSITE", "local1");
  //define ("DEFSITE", "mundi"); 
  define ("SYNCSITE", "local1");
  //define ("SYNCSITE", "mundi");
 
  
  $DB_OPTIONS = array (
		'ERROR_DISPLAY' => true
		);
  
  define ("MaxMarker", 300);
  define ("DELIM",'*');
  define ("jsPathRoot",PathRoot."/library/js/");

  define ("XmlParam",PathRoot."/param/SolAcc.xml");
  define ("XmlScena",PathRoot."/param/scenarisation.xml");
  //define ("XmlParam","http://www.naoss.fr:81/mundigo/param/SolAcc.xml");
  
  define('EOL', "\r\n");

$SiteLocal1 = array(
	"AUTEUR_SYNCHRO" => 8, 
	"SQL_LOGIN" => "root", 
	"SQL_PWD" => "", 
	"SQL_HOST" => "localhost",
	"SQL_DB" => "onadadev",
	"NOM" => "onadabase DEV",//je sais pas
	"SITE_PARENT" => -1,//je sais pas
	"SITE_ENFANT" => -1,
	"GRILLE_GEO" => 1,
	"GRILLE_REG_LEG" => 52,
	"GRILLE_REP_CON" => 59,
	"GRILLE_SIG_PROB" => 60,
	"GRILLE_CONTROL_V1" => 54,
	"GRILLE_CONTROL_V2" => 70,
	"GRILLE_OBS" => 67,
	"GRILLE_LIGNE_TRANS" => 72,
	"GRILLE_CHAINE_DEPLA" => 71,
	"GRILLE_ETAB" => 55,
	"GRILLE_VOIRIE" => 62,
	"CHAMPS_CONTROL_DIAG" => array("moteur"=>"ligne_2","audio"=>"ligne_3","visu"=>"ligne_4","cog"=>"ligne_5"),
	"CHAMPS_CONTROL_DEFFICIENCE" => array("champ"=>"multiple_3","valeur"=>array("moteur"=>"multiple_3_1","audio"=>"multiple_3_2","visu"=>"multiple_3_3","cog"=>"multiple_3_4")),
	"MOT_CLEF_OBS" => 151,
	"MOT_CLEF_DEF_TYPE_CARTE" => 4,
	"MOT_CLEF_PANG" => 64,
	"MOT_CLEF_GARE" => 62,
	"MOT_CLEF_LIGNE_TRANS" => 167,
	"MOT_CLEF_CHAINE_DEPLA" => 168,
	"RUB_TERRE" => 5479,
	"RUB_PORTE1" => -50,
	"RUB_PORTE2" => -74,
	"RUB_PORTE_FACE1" => -1342,
	"RUB_PORTE_FACE2" => -1341,
	"DEF_ID" => 5479,
	"DEF_LAT" => 45,
	"DEF_LNG" => 1,
	"DEF_ZOOM" => 4,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"CARTE_TYPE_DOC" => "75,76",
	"gmKey" => "ABQIAAAATs5RWdW8iTPuaiLrkvnqjRTRqFFvaBKBgdVodt96I8idUV-28RTOELCsfx_7hD62HnKwk7Lm8Cg_lQ",
	"lienAdminSpip" => "http://localhost/onadabase/spipsync/ecrire",
	"urlExeAjax" => "http://localhost/onadabase/library/php/ExeAjax.php",
	"jsXulParam" => "http://localhost/onadabase/xul/chrome/content/param/onadabase.xml",	
	"MenuContexte" => "PopupMenuSet.xul",
	"urlCarto" => "http://localhost/onadabase/design/BlocCarte.php",
	"urlVideo" => "http://localhost/onadabase/design/BlocVideo.php",
	"urlLibPhp" => "http://localhost/onadabase/library/php/",
	"urlLibJs" => "http://localhost/onadabase/library/js/",
	"urlLibSwf" => "http://localhost/onadabase/library/swf/",
	"pathUpload" => PathRoot."/spipsync/IMG/",
	"pathXulJs" => "http://localhost/onadabase/xul/chrome/content/js/",	
	"pathSpip" => "http://localhost/onadabase/spipsync/"
	); 
$SiteLocal2 = array(
	"AUTEUR_SYNCHRO" => 8, 
	"SQL_LOGIN" => "root", 
	"SQL_PWD" => "", 
	"SQL_HOST" => "localhost",
	"SQL_DB" => "onadabase",
	"NOM" => "onadabasecentre",//je sais pas
	"SITE_PARENT" => -1,//je sais pas
	"SITE_ENFANT" => -1,
	"GRILLE_GEO" => 1,
	"GRILLE_REG_LEG" => 52,
	"GRILLE_REP_CON" => 59,
	"GRILLE_SIG_PROB" => 60,
	"GRILLE_CONTROL_V1" => 54,
	"GRILLE_CONTROL_V2" => 70,
	"GRILLE_OBS" => 67,
	"GRILLE_LIGNE_TRANS" => 72,
	"CHAMPS_CONTROL_DIAG" => array("moteur"=>"ligne_2","audio"=>"ligne_3","visu"=>"ligne_4","cog"=>"ligne_5"),
	"CHAMPS_CONTROL_DEFFICIENCE" => array("champ"=>"multiple_3","valeur"=>array("moteur"=>"multiple_3_1","audio"=>"multiple_3_2","visu"=>"multiple_3_3","cog"=>"multiple_3_4")),
	"MOT_CLEF_OBS" => 151,
	"MOT_CLEF_DEF_TYPE_CARTE" => 4,
	"MOT_CLEF_PANG" => 64,
	"MOT_CLEF_GARE" => 62,
	"MOT_CLEF_LIGNE_TRANS" => 167,
	"RUB_TERRE" => 1942,
	"RUB_PORTE1" => 50,
	"RUB_PORTE2" => 74,
	"RUB_PORTE_FACE1" => 1342,
	"RUB_PORTE_FACE2" => 1341,
	"DEF_ID" => 2152,
	"DEF_LAT" => 50.63705,
	"DEF_LNG" => 3.06994,
	"DEF_ZOOM" => 14,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"CARTE_TYPE_DOC" => "75,76",
	"gmKey" => "ABQIAAAAU9-q_ELxIQ-YboalQWRCjRR8S4b2oMC5BlotEgOIFwvu2Zfg4BRD2eEaRYw3NNB3VDcldikKtbZtsw",
	"lienAdminSpip" => "http://localhost/onadabase/spip/ecrire",
	"urlExeAjax" => "http://localhost/onadabase/library/php/ExeAjax.php",
	"jsXulParam" => "http://localhost/onadabase/xul/chrome/content/param/onadabase.xml",
	"MenuContexte" => "PopupMenuSet.xul",
	"urlSite" => "http://localhost/onadabase/library/php/Site.php",	
	"urlCarto" => "http://localhost/onadabase/design/BlocCarte.php",
	"urlVideo" => "http://localhost/onadabase/design/BlocVideo.php",
	"urlLibPhp" => "http://localhost/onadabase/library/php/",
	"urlLibJs" => "http://localhost/onadabase/library/js/",
	"urlLibSwf" => "http://localhost/onadabase/library/swf/",
	"pathUpload" => PathRoot."/spip/IMG/",
	"pathXulJs" => "http://localhost/onadabase/xul/chrome/content/js/",	
	"pathSpip" => "http://localhost/onadabase/spip/"
	); 

	$SiteMundi = array(
	"AUTEUR_SYNCHRO" => 8, 
	"SQL_LOGIN" => "mundilogcai", 
	"SQL_PWD" => "CnVjzGxb", 
	"SQL_HOST" => "mysql5-5",
	"SQL_DB" => "mundilogcai",
	"NOM" => "mundilogiweb onadabase",//je sais pas
	"SITE_PARENT" => -1,//je sais pas
	"SITE_ENFANT" => -1,
	"GRILLE_GEO" => 1,
	"GRILLE_REG_LEG" => 52,
	"GRILLE_REP_CON" => 59,
	"GRILLE_SIG_PROB" => 60,
	"GRILLE_OBS" => 67,
	"GRILLE_CONTROL_V1" => 54,
	"GRILLE_CONTROL_V2" => 70,
	"GRILLE_LIGNE_TRANS" => 72,
	"CHAMPS_CONTROL_DIAG" => array("moteur"=>"ligne_2","audio"=>"ligne_3","visu"=>"ligne_4","cog"=>"ligne_5"),
	"CHAMPS_CONTROL_DEFFICIENCE" => array("champ"=>"multiple_3","valeur"=>array("moteur"=>"multiple_3_1","audio"=>"multiple_3_2","visu"=>"multiple_3_3","cog"=>"multiple_3_4")),
	"MOT_CLEF_OBS" => 151,
	"MOT_CLEF_DEF_TYPE_CARTE" => 4,
	"MOT_CLEF_PANG" => 64,
	"MOT_CLEF_GARE" => 62,
	"MOT_CLEF_LIGNE_TRANS" => 167,
	"RUB_TERRE" => 1942,
	"RUB_PORTE1" => 50,
	"RUB_PORTE2" => 74,
	"RUB_PORTE_FACE1" => 1342,
	"RUB_PORTE_FACE2" => 1341,
	"DEF_ID" => 2152,
	"DEF_LAT" => 50.63705,
	"DEF_LNG" => 3.06994,
	"DEF_ZOOM" => 4,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"CARTE_TYPE_DOC" => "75,76",
	"gmKey" => "ABQIAAAAU9-q_ELxIQ-YboalQWRCjRRWuADP5IHMsclz4StI_c8lb9zkohTyT7mGzubP9DAdxQrlDe2AW8dGTw",
	"lienAdminSpip" => "http://www.mundilogiweb.com/onadabase/spip/ecrire",
	"urlExeAjax" => "http://www.mundilogiweb.com/onadabase/library/php/ExeAjax.php",
	"jsXulParam" => "http://www.mundilogiweb.com/onadabase/xul/chrome/content/param/onadabase.xml",	
	"MenuContexte" => "menu_contextuel_Picardie.xul",
	"urlSite" => "http://www.mundilogiweb.com/onadabase/xul/chrome/library/php/Site.php",	
	"urlCarto" => "http://www.mundilogiweb.com/onadabase/design/BlocCarte.php",
	"urlVideo" => "http://www.mundilogiweb.com/onadabase/design/BlocVideo.php",
	"urlLibPhp" => "http://www.mundilogiweb.com/onadabase/library/php/",
	"urlLibJs" => "http://www.mundilogiweb.com/onadabase/library/js/",
	"urlLibSwf" => "http://www.mundilogiweb.com/onadabase/library/swf/",
	"pathXulJs" => "http://www.mundilogiweb.com/onadabase/xul/chrome/content/js/",	
	"pathUpload" => PathRoot."/spip/IMG/",
	"pathSpip" => "http://www.mundilogiweb.com/onadabase/spip/"	
	); 
	
$SITES = array(
	"local1" => $SiteLocal1
	,"local2" => $SiteLocal2
	,"mundi" => $SiteMundi
	);

?>