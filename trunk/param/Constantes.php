<?php

  //
  // Fichier contenant les definitions de constantes
  //define ("PathRoot",$_SERVER["DOCUMENT_ROOT"]."/sites/n/naoss.fr/mundigo"); 
  define ("PathRoot","C:/wamp/www/"."onadabase"); 
  
	// *** chemin de toutes les bases et les spip en service ***
  define("TT_CLASS_BASE", PathRoot."/library/php/");
	// Include the class files.
	require_once(TT_CLASS_BASE."AllClass.php");

  define ("TRACE", false);

  //define ("DEFSITE", "localPicardieCentre");
  define ("DEFSITE", "local1");
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

  
$localPicardieCentre = array(
	"AUTEUR_SYNCHRO" => 8, 
	"SQL_LOGIN" => "root", 
	"SQL_PWD" => "", 
	"SQL_HOST" => "localhost",
	"SQL_DB" => "onada1",
	"NOM" => "onadabase Centre - Val de Marne ",//je sais pas
	"SITE_PARENT" => -1,//je sais pas
	"SITE_ENFANT" => -1,
	"GRILLE_GEO" => 1,
	"GRILLE_REG_LEG" => 52,
	"GRILLE_REP_CON" => 59,
	"GRILLE_SIG_PROB" => 60,
	"GRILLE_CONTROL_V1" => 54,
	"GRILLE_CONTROL_V2" => 70,
	"GRILLE_OBS" => 67,
	"CHAMPS_CONTROL_DIAG" => array("moteur"=>"ligne_2","audio"=>"ligne_3","visu"=>"ligne_4","cog"=>"ligne_5"),
	"MOT_CLEF_OBS" => 151,
	"RUB_TERRE" => 1942,
	"RUB_PORTE1" => 50,
	"RUB_PORTE2" => 74,
	"RUB_PORTE_FACE1" => 1342,
	"RUB_PORTE_FACE2" => 1341,
	"DEF_LAT" => 45,
	"DEF_LNG" => 1,
	"DEF_ZOOM" => 4,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"gmKey" => "ABQIAAAATs5RWdW8iTPuaiLrkvnqjRTRqFFvaBKBgdVodt96I8idUV-28RTOELCsfx_7hD62HnKwk7Lm8Cg_lQ",
	"lienAdminSpip" => "http://localhost/onadabase/spip/ecrire",
	"urlExeAjax" => "http://localhost/onadabase/library/php/ExeAjax.php",
	"jsXulParam" => "http://localhost/onadabase/xul/chrome/content/param/onadabase.xml",	
	"urlCarto" => "http://localhost/onadabase/design/BlocCarte.php",
	"urlLibPhp" => "http://localhost/onadabase/library/php/",
	"pathUpload" => PathRoot."/spip/IMG/",
	"pathXulJs" => "http://localhost/onadabase/xul/chrome/content/js/",	
	"pathSpip" => "http://localhost/onadabase/spip/"
	); 

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
	"CHAMPS_CONTROL_DIAG" => array("moteur"=>"ligne_2","audio"=>"ligne_3","visu"=>"ligne_4","cog"=>"ligne_5"),
	"MOT_CLEF_OBS" => 151,
	"RUB_TERRE" => 1942,
	"RUB_PORTE1" => 50,
	"RUB_PORTE2" => 74,
	"RUB_PORTE_FACE1" => 1342,
	"RUB_PORTE_FACE2" => 1341,
	"DEF_LAT" => 45,
	"DEF_LNG" => 1,
	"DEF_ZOOM" => 4,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"gmKey" => "ABQIAAAATs5RWdW8iTPuaiLrkvnqjRTRqFFvaBKBgdVodt96I8idUV-28RTOELCsfx_7hD62HnKwk7Lm8Cg_lQ",
	"lienAdminSpip" => "http://localhost/onadabase/spipsync/ecrire",
	"urlExeAjax" => "http://localhost/onadabase/library/php/ExeAjax.php",
	"jsXulParam" => "http://localhost/onadabase/xul/chrome/content/param/onadabase.xml",	
	"urlCarto" => "http://localhost/onadabase/design/BlocCarte.php",
	"urlLibPhp" => "http://localhost/onadabase/library/php/",
	"pathUpload" => PathRoot."/spipsync/IMG/",
	"pathXulJs" => "http://localhost/onadabase/xul/chrome/content/js/",	
	"pathSpip" => "http://localhost/onadabase/spipsync/"
	); 
$SiteLocal2 = array(
	"AUTEUR_SYNCHRO" => 8, 
	"SQL_LOGIN" => "root", 
	"SQL_PWD" => "", 
	"SQL_HOST" => "localhost",
	"SQL_DB" => "onadalocal",
	"NOM" => "onada local",//je sais pas
	"SITE_PARENT" => -1,//je sais pas
	"SITE_ENFANT" => -1,
	"GRILLE_GEO" => 1,
	"GRILLE_REG_LEG" => 52,
	"GRILLE_REP_CON" => 59,
	"GRILLE_SIG_PROB" => 60,
	"GRILLE_CONTROL_V1" => 54,
	"GRILLE_CONTROL_V2" => 70,
	"GRILLE_OBS" => 67,
	"CHAMPS_CONTROL_DIAG" => array("moteur"=>"ligne_2","audio"=>"ligne_3","visu"=>"ligne_4","cog"=>"ligne_5"),
	"MOT_CLEF_OBS" => 151,
	"RUB_TERRE" => 1942,
	"RUB_PORTE1" => 50,
	"RUB_PORTE2" => 74,
	"RUB_PORTE_FACE1" => 1342,
	"RUB_PORTE_FACE2" => 1341,
	"DEF_LAT" => 50.63705,
	"DEF_LNG" => 3.06994,
	"DEF_ZOOM" => 14,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"gmKey" => "ABQIAAAAU9-q_ELxIQ-YboalQWRCjRR8S4b2oMC5BlotEgOIFwvu2Zfg4BRD2eEaRYw3NNB3VDcldikKtbZtsw",
	"lienAdminSpip" => "http://localhost/onadabase/spip/ecrire",
	"urlExeAjax" => "http://localhost/onadabase/library/php/ExeAjax.php",
	"jsXulParam" => "http://localhost/onadabase/xul/chrome/content/param/onadabase.xml",
	"urlSite" => "http://localhost/onadabase/library/php/Site.php",	
	"urlCarto" => "http://localhost/onadabase/design/BlocCarte.php",
	"urlLibPhp" => "http://localhost/onadabase/library/php/",
	"pathUpload" => PathRoot."/spip/IMG/",
	"pathXulJs" => "http://localhost/onadabase/xul/chrome/content/js/",	
	"pathSpip" => "http://localhost/onadabase/spip/"
	); 
$SiteAmel = array(
	"AUTEUR_SYNCHRO" => 8, 
	"SQL_LOGIN" => "root", 
	"SQL_PWD" => "", 
	"SQL_HOST" => "localhost",
	"SQL_DB" => "onadaAmel",
	"NOM" => "onada Amel",//je sais pas
	"SITE_PARENT" => -1,//je sais pas
	"SITE_ENFANT" => -1,
	"GRILLE_GEO" => 1,
	"GRILLE_REG_LEG" => 52,
	"GRILLE_REP_CON" => 59,
	"GRILLE_SIG_PROB" => 60,
	"GRILLE_CONTROL_V1" => 54,
	"GRILLE_CONTROL_V2" => 70,
	"GRILLE_OBS" => 67,
	"CHAMPS_CONTROL_DIAG" => array("moteur"=>"ligne_2","audio"=>"ligne_3","visu"=>"ligne_4","cog"=>"ligne_5"),
	"MOT_CLEF_OBS" => 151,
	"RUB_TERRE" => 1942,
	"RUB_PORTE1" => 50,
	"RUB_PORTE2" => 74,
	"RUB_PORTE_FACE1" => 1342,
	"RUB_PORTE_FACE2" => 1341,
	"DEF_LAT" => 50.63705,
	"DEF_LNG" => 3.06994,
	"DEF_ZOOM" => 14,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"gmKey" => "ABQIAAAAU9-q_ELxIQ-YboalQWRCjRR8S4b2oMC5BlotEgOIFwvu2Zfg4BRD2eEaRYw3NNB3VDcldikKtbZtsw",
	"lienAdminSpip" => "http://localhost/onadabase/spip/ecrire",
	"urlExeAjax" => "http://localhost/onadabase/library/php/ExeAjax.php",
	"jsXulParam" => "http://localhost/onadabase/xul/chrome/content/param/onadabase.xml",
	"urlSite" => "http://localhost/onadabase/library/php/Site.php",	
	"urlCarto" => "http://localhost/onadabase/design/BlocCarte.php",
	"urlLibPhp" => "http://localhost/onadabase/library/php/",
	"pathUpload" => PathRoot."/spip/IMG/",
	"pathXulJs" => "http://localhost/onadabase/xul/chrome/content/js/",	
	"pathSpip" => "http://localhost/onadabase/spip/"
	); 
$SiteLocalGilles = array(
	"AUTEUR_SYNCHRO" => 8, 
	"SQL_LOGIN" => "root", 
	"SQL_PWD" => "", 
	"SQL_HOST" => "localhost",
	"SQL_DB" => "onadagilles",
	"NOM" => "onada gilles clean",//je sais pas
	"SITE_PARENT" => -1,//je sais pas
	"SITE_ENFANT" => -1,
	"GRILLE_GEO" => 1,
	"GRILLE_REG_LEG" => 52,
	"GRILLE_REP_CON" => 59,
	"GRILLE_SIG_PROB" => 60,
	"GRILLE_CONTROL_V1" => 54,
	"GRILLE_CONTROL_V2" => 70,
	"CHAMPS_CONTROL_DIAG" => array("moteur"=>"ligne_2","audio"=>"ligne_3","visu"=>"ligne_4","cog"=>"ligne_5"),
	"GRILLE_OBS" => 67,
	"MOT_CLEF_OBS" => 151,
	"RUB_TERRE" => 1942,
	"RUB_PORTE1" => 50,
	"RUB_PORTE2" => 74,
	"RUB_PORTE_FACE1" => 1342,
	"RUB_PORTE_FACE2" => 1341,
	"DEF_LAT" => 50.63705,
	"DEF_LNG" => 3.06994,
	"DEF_ZOOM" => 14,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"gmKey" => "ABQIAAAAU9-q_ELxIQ-YboalQWRCjRR8S4b2oMC5BlotEgOIFwvu2Zfg4BRD2eEaRYw3NNB3VDcldikKtbZtsw",
	"lienAdminSpip" => "http://localhost/onadabase/spip/ecrire",
	"urlExeAjax" => "http://localhost/onadabase/library/php/ExeAjax.php",
	"jsXulParam" => "http://localhost/onadabase/xul/chrome/content/param/onadabase.xml",
	"urlSite" => "http://localhost/onadabase/library/php/Site.php",	
	"urlCarto" => "http://localhost/onadabase/design/BlocCarte.php",
	"urlLibPhp" => "http://localhost/onadabase/library/php/",
	"pathUpload" => PathRoot."/spip/IMG/",
	"pathXulJs" => "http://localhost/onadabase/xul/chrome/content/js/",	
	"pathSpip" => "http://localhost/onadabase/spip/"
	); 
$SiteNaos = array(
	"AUTEUR_SYNCHRO" => 8, 
	"SQL_LOGIN" => "naossfr3", 
	"SQL_PWD" => "Eg5PyukqDj", 
	"SQL_HOST" => "sql3",
	"SQL_DB" => "naossfr3",
	"NOM" => "naos onadabase",//je sais pas
	"SITE_PARENT" => -1,//je sais pas
	"SITE_ENFANT" => -1,
	"GRILLE_GEO" => 1,
	"GRILLE_REG_LEG" => 52,
	"GRILLE_REP_CON" => 59,
	"GRILLE_SIG_PROB" => 60,
	"GRILLE_CONTROL_V1" => 54,
	"GRILLE_CONTROL_V2" => 70,
	"GRILLE_OBS" => 67,
	"CHAMPS_CONTROL_DIAG" => array("moteur"=>"ligne_2","audio"=>"ligne_3","visu"=>"ligne_4","cog"=>"ligne_5"),
	"MOT_CLEF_OBS" => 151,
	"RUB_TERRE" => 1942,
	"RUB_PORTE1" => 50,
	"RUB_PORTE2" => 74,
	"RUB_PORTE_FACE1" => 1342,
	"RUB_PORTE_FACE2" => 1341,
	"DEF_LAT" => 45,
	"DEF_LNG" => 1,
	"DEF_ZOOM" => 4,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"gmKey" => "� g�n�rer",
	"lienAdminSpip" => "http://www.naoss.fr:81/mundigo/spip/ecrire",
	"urlExeAjax" => "http://www.naoss.fr:81/mundigo/library/php/ExeAjax.php",
	"jsXulParam" => "http://www.naoss.fr:81/mundigo/xul/chrome/content/param/onadabase.xml",	
	"urlCarto" => "http://www.naoss.fr:81/mundigo/design/BlocCarte.php",
	"urlLibPhp" => "http://www.naoss.fr:81/mundigo/library/php/",
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
	"CHAMPS_CONTROL_DIAG" => array("moteur"=>"ligne_2","audio"=>"ligne_3","visu"=>"ligne_4","cog"=>"ligne_5"),
	"MOT_CLEF_OBS" => 151,
	"RUB_TERRE" => 1942,
	"RUB_PORTE1" => 50,
	"RUB_PORTE2" => 74,
	"RUB_PORTE_FACE1" => 1342,
	"RUB_PORTE_FACE2" => 1341,
	"DEF_LAT" => 50.63705,
	"DEF_LNG" => 3.06994,
	"DEF_ZOOM" => 4,
	"DEF_CARTE_TYPE" => "G_HYBRID_MAP",
	"gmKey" => "ABQIAAAAU9-q_ELxIQ-YboalQWRCjRRWuADP5IHMsclz4StI_c8lb9zkohTyT7mGzubP9DAdxQrlDe2AW8dGTw",
	"lienAdminSpip" => "http://www.mundilogiweb.com/onadabase/spip/ecrire",
	"urlExeAjax" => "http://www.mundilogiweb.com/onadabase/library/php/ExeAjax.php",
	"jsXulParam" => "http://www.mundilogiweb.com/onadabase/xul/chrome/content/param/onadabase.xml",	
	"urlSite" => "http://www.mundilogiweb.com/onadabase/xul/chrome/library/php/Site.php",	
	"urlCarto" => "http://www.mundilogiweb.com/onadabase/design/BlocCarte.php",
	"urlLibPhp" => "http://www.mundilogiweb.com/onadabase/library/php/",
	"pathXulJs" => "http://www.mundilogiweb.com/onadabase/xul/chrome/content/js/",	
	"pathUpload" => PathRoot."/spip/IMG/",
	"pathSpip" => "http://localhost/onadabase/spip/"	
	); 
	
$SITES = array(
	"local1" => $SiteLocal1
	,"local2" => $SiteLocal2
	,"OnadaAmel" => $SiteAmel
	,"localGilles" => $SiteLocalGilles
	,"naos" => $SiteNaos
	,"mundi" => $SiteMundi
	,"localPicardieCentre" =>$localPicardieCentre
	);

?>