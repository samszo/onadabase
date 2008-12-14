<?php
/*
header('Content-type: text/html; charset=iso-8859-1');
header('Content-type: text/html; charset=UTF-8');


*/
require_once("../../param/ParamPage.php");


$GrilleGeo = $objSite->infos["GRILLE_GEO"];

$resultat = "";
if(isset($_GET['f'])){
	$fonction = $_GET['f'];
}
else
	$fonction = '';
if(isset($_GET['themes'])){
	$themes = $_GET['themes'];
}
else
	$themes = '';
if(isset($_GET['theme'])){
	$themes = $_GET['theme'];
}

switch ($fonction) {
	case 'get_markers':
		$resultat = $markers = get_marker($objSite, $_GET['id'], $_GET['southWestLat'], $_GET['northEastLat'],$_GET['southWestLng'], $_GET['northEastLng'], $_GET['zoom'], $_GET['MapQuery'], $themes);
		break;
	case 'get_theme_markers':
		get_theme_markers($_GET['id']);
		break;
	case 'sauve_marker':
		sauve_marker($_GET['action'],$_GET['id'],$_GET['zoommin'],$_GET['zoommax'],$_GET['lat'],$_GET['lng'],$_GET['adresse'],$_GET['type']);
		break;
	case 'get_kml':
		get_kml($_GET['BBOX']);
		break;
	case 'get_rub_kml':
		get_rub_kml($_GET['id'],$_GET['MapQuery']);
		break;
}

echo $resultat;

function sauve_marker($action,$id,$zoommin,$zoommax,$lat,$lng,$adresse,$type) {

	global $objSite, $GrilleGeo;

	// on vérifie qu'un choix est bien pass?
	switch ($action) {
		case "Modifier":
		  	//récupère l'id_donnée
			$sql = "SELECT fd.id_donnee
				FROM spip_forms_donnees fd
					INNER JOIN spip_forms_donnees_articles da ON da.id_donnee = fd.id_donnee
					INNER JOIN spip_articles a ON a.id_article = da.id_article AND a.id_rubrique = ".$id."
				WHERE fd.id_form = ".$GrilleGeo;
			//echo $sql;
			$DB = new mysql($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
			$DB->connect();
			$req = $DB->query($sql);
			$DB->close();
			
			if (mysql_num_rows($req) == 0) {
				GetRubNewGeoloc($id,$zoommin,$zoommax,$lat,$lng,$adresse,$type);
			}else{
				$row = mysql_fetch_assoc($req);
				$IdDon = $row['id_donnee'];
				//echo "suprrime les champs sauf kml<br/>";
				$DB = new mysql($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
				$sql = "DELETE FROM spip_forms_donnees_champs WHERE id_donnee = ".$IdDon." AND champ <> 'texte_1'" ;
				$DB->connect();
				$req = $DB->query($sql);
				$DB->close();
				
				//mise à jour des champs
				$sql = "INSERT INTO `spip_forms_donnees_champs` (valdec,`id_donnee`, `champ`, `valeur`, `maj`)
					VALUES (".$lat.", ".$IdDon.", 'ligne_1', ".$lat.", now())";
				$DB = new mysql($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
				$DB->connect();
				$req = $DB->query($sql);
				$DB->close();
				$sql = "INSERT INTO `spip_forms_donnees_champs` (valdec,`id_donnee`, `champ`, `valeur`, `maj`)
					VALUES (".$lng.", ".$IdDon.", 'ligne_2', ".$lng.", now())";
				$DB = new mysql($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
				$DB->connect();
				$req = $DB->query($sql);
				$DB->close();
				$sql = "INSERT INTO `spip_forms_donnees_champs` (valint,`id_donnee`, `champ`, `valeur`, `maj`)
					VALUES (".$zoommin.", ".$IdDon.", 'ligne_3', ".$zoommin.", now())";
				$DB = new mysql($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
				$DB->connect();
				$req = $DB->query($sql);
				$DB->close();
				$sql = "INSERT INTO `spip_forms_donnees_champs` (valint,`id_donnee`, `champ`, `valeur`, `maj`)
					VALUES (".$zoommax.", ".$IdDon.", 'ligne_4', ".$zoommax.", now())";
				$DB = new mysql($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
				$DB->connect();
				$req = $DB->query($sql);
				$DB->close();
				echo "ExeDonneCarto:SauveMarker:type=".$type."<br/>";
				if($type=="Mixte")
					$type = 5;
				if($type=="Satellite")
					$type = 4;
				if($type=="Plan")
					$type = 3;
				echo "ExeDonneCarto:SauveMarker:type=".$type."<br/>";
				$sql = "INSERT INTO `spip_forms_donnees_champs` (`id_donnee`, `champ`, `valeur`, `maj`, valint)
					VALUES (".$IdDon.", 'mot_1', '".$type."', now(), ".$type.")";
				echo "ExeDonneCarto:SauveMarker:".$objSite->infos["SQL_DB"]."sql=".$sql."<br/>";
				$DB = new mysql($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
				$DB->connect();
				$req = $DB->query($sql);
				$DB->close();
				$adresse=utf8_decode($adresse);
				$sql = "INSERT INTO `spip_forms_donnees_champs` (`id_donnee`, `champ`, `valeur`, `maj`)
					VALUES (".$IdDon.", 'ligne_7', \"".$adresse."\", now())";
				//echo $sql."<br/>";
				$DB = new mysql($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
				$DB->connect();
				$req = $DB->query($sql);
				$DB->close();
			}

		  	break;
		case "Supprimer":
		  $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
		  break;
  }
}

function get_marker($objSite, $id, $southWestLat, $northEastLat, $southWestLng, $northEastLng, $zoom, $query="", $themes="", $i = 0) {


	// on récupère les markers suivants les coordonnée
	$NewQuery = "idFiche";
	
	//construction de la requ�te
	$statut = " AND a.statut = 'publie' ";
	$statut = " ";
	$sql = "SELECT r.id_rubrique, r.titre, r.descriptif, r.texte
			, a.id_article idArt, da.id_donnee idDon
			, dc1.valdec lat, dc2.valdec lng, dc3.valint zoommin, dc4.valint zoommax
			, m.titre cartotype , dc7.valeur adresse
			, dc8.valeur kml
			, dArt.fichier docArtkml
			FROM spip_rubriques r
			INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique 
			INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article ".$statut."
			INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee AND fd.id_form = ".$objSite->infos["GRILLE_GEO"]."
			INNER JOIN spip_forms_donnees_champs dc1 ON dc1.id_donnee = da.id_donnee AND dc1.champ = 'ligne_1'
			INNER JOIN spip_forms_donnees_champs dc2 ON dc2.id_donnee = da.id_donnee AND dc2.champ = 'ligne_2'
			INNER JOIN spip_forms_donnees_champs dc3 ON dc3.id_donnee = da.id_donnee AND dc3.champ = 'ligne_3'
			INNER JOIN spip_forms_donnees_champs dc4 ON dc4.id_donnee = da.id_donnee AND dc4.champ = 'ligne_4'
			INNER JOIN spip_forms_donnees_champs dc5 ON dc5.id_donnee = da.id_donnee AND dc5.champ = 'mot_1'					
			INNER JOIN spip_mots m ON m.id_mot = dc5.valeur					
			INNER JOIN spip_forms_donnees_champs dc7 ON dc7.id_donnee = da.id_donnee AND dc7.champ = 'ligne_7'
			LEFT JOIN spip_forms_donnees_champs dc8 ON dc8.id_donnee = da.id_donnee AND dc8.champ = 'texte_1'
			LEFT JOIN spip_documents_articles doca ON doca.id_article = a.id_article
			LEFT JOIN spip_documents dArt ON dArt.id_document = doca.id_document AND dArt.id_type IN (".$objSite->infos["CARTE_TYPE_DOC"].")
			";
		
	switch ($query) {
		case "admin":
			//requète pour un élément
			$sql .= " WHERE r.id_rubrique =".$id." 
				ORDER BY dc1.valdec, dArt.fichier DESC
				LIMIT 0 , 1";
		  	break;
		case "adminDon":
			//requète pour un élément
			$sql .= " WHERE fd.id_donnee =".$id."  
				ORDER BY dc1.valdec, dArt.fichier DESC
				LIMIT 0 , 1";
		  	break;
		case "all":
			//requète pour un élément
			$sql .= " WHERE 1  
				ORDER BY dc1.valdec, dArt.fichier DESC
				";
		  	break;
		case "allEtatDiag":
			//requète pour un élément
			$sql .= " WHERE 1  
				ORDER BY dc1.valdec, dArt.fichier DESC
				";
			$SaveFile = true;
		  	break;
	}

	$DB = new mysql($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"]);
	$DB->connect();
	//charge les propiétés du granulat
	$req = $DB->query($sql);
	$DB->close();
	//echo $query." ".$objSite->infos["SQL_DB"]." ".$sql."<br/>";
	
	//initialisation du xml
	$xml = "<CartoDonnees idSite='".$objSite->id."' idRub='".$id."' query='".$query."' >";
	
	//$i = 0;
	while($row = mysql_fetch_assoc($req))
	{

	//echo  $i."<br/>"; 
		//echo "recupère le granulat = ".$id."<br/>";
		$g = new Granulat($row['id_rubrique'], $objSite, true);

		//construction des markers
	/*******************************modif CAI*****************************************************************/


		$xml .= "<CartoDonnee lat='".$row['lat']."'";
		
		$xml .= " lng='".$row['lng']."'";
		
		$xml .= " i='".$i."'";
		
		$xml .= " idRub='".$row['id_rubrique']."'";
		
		$xml .= " idSite='".$objSite->id."'";
		
		$xml .= " titre=\"".utf8_encode($objSite->XmlParam->XML_entities($row['titre']))."\"";
		
		/*
		$markers .= "topic_$i ".DELIM;
		//Topic
		$markers .=Root."/new/lieux.php?site=".$objSite->id."&VoirEn=Topos&Rub=".$row['id_rubrique']."&query=".$NewQuery.DELIM;//lien
		//$markers .=get_fenetre_info($row,"Topic").DELIM;//localisation
		if($row['navig'])
			$markers .=$row['navig'].DELIM;		
		else
			$markers .=" ".DELIM;
		//$markers .=$g->GetImages(68, 45).DELIM;//image
		$markers .= "".DELIM;//image
		
		$markers .=utf8_encode(tronquer($row['texte'],60)).DELIM;
		//création des onglets pour le granulats
		//$Val = $g->GetValeurForm($this->site->infos["GRILLE_Granulat"],"Titre", "", "  ", "Titre : ");
		//if(substr($row['descriptif'], -2)!="00")
		//if($Val!=" ")
			//Famillie sauf pour département et communes
		//	$markers .=get_fenetre_info($row,"Granulat").DELIM;
		//else
			$markers .="".DELIM;

		 if(substr($row['descriptif'], -4)!="0000")
			//Thematique sauf pour département
			$markers .=get_fenetre_info($row,"Thematique").DELIM;
		else
			$markers .="".DELIM;
		*/
		//zoom

		$xml .= " zoommin='".$row['zoommin']."'";
		
		$xml .= " zoommax='".$row['zoommax']."'";
		
		//adresse
		$xml .= " adresse=\"".utf8_encode($objSite->XmlParam->XML_entities($row['adresse']))."\"";
		
		//type carte
		$xml .= " cartotype='".$row['cartotype']."'";
		
		//lien vers le kml
		$kml="";
		if($row['docArtkml'])
			$kml = $objSite->infos["pathSpip"].$row['docArtkml'];
		if($kml=="")	
			$kml = $row['kml'];
		if($kml=="")
			$kml = $g->GetKml();
		$xml .= " kml='".$kml."'";
		
		//cr�ation de l'identidiant xul
		$idDoc = 'val'.DELIM.$objSite->infos["GRILLE_GEO"].DELIM.$row["idDon"].DELIM."fichier".DELIM.$row["idArt"];
		$xml .= " idDoc='".$idDoc."'";
		//finalisation des attributs de CartoDonnee
		$xml .= " >";
				
		//v�rifie s'il faut r�cup�rer le diagnostic
		if($query=="allEtatDiag"){
			
			//$xml .= $g->GetEtatDiag(true,true);

			//r�cup�re les grilles du granulat 
			$rsG = $g->GetFormIds(-1,$g->id);
			if(mysql_num_rows($rsG)>0){
				$xml .= "<grilles>";
				while($rG = mysql_fetch_assoc($rsG)) {
					$xml .= "<grille id='".$rG['id_form']."' titre='".$rG['titre']."' idArt='".$rG['id_article']."' />";
				}
				$xml .= "</grilles>";
			}
			
			//r�cup�re les mots-clef du granulat
			$rsMC = $g->GetTypeMotClef("rubrique");
			if(count($rsMC)>0){
				$xml .= "<motsclefs>";
				foreach($rsMC as $mc) {
					$xml .= "<motclef id='".$mc->id."' titre='".$mc->titre."'  />";
				}
				$xml .= "</motsclefs>";
			}
			
		}

		//finalisation du xml
		$xml .= "</CartoDonnee>";
		

	/***************************************************************fin*******************************/
		$i++;

	}
	
	//finalisation du xml
	$xml .= "</CartoDonnees>";
	
	//gestion des requêtes multisite
	if($objSite->infos["SITE_ENFANT"]!=-1 && $query!="idFiche"){
		foreach($objSite->infos["SITE_ENFANT"] as $siteenfant=>$type)
		{
				//echo $site." NextSiteEnfant:".$siteenfant."<br/>"; 
				$site = $objSite->sites[$siteenfant];
				//echo $objSite->sites."<br/>"; 
				$objSiteNew = new Site($objSite->sites, $siteenfant, $objSite->scope, false);
				echo get_marker($objSiteNew, $id, $southWestLat, $northEastLat, $southWestLng, $northEastLng, $zoom, $query, $themes, $i);
		}
	}
			
	//echo $markers;
	if($SaveFile){
		$fic = fopen(PathRoot."/bdd/carto/".$query."_".$objSite->id."_".$id.".xml", "w");
		fwrite($fic, $xml);		
    	fclose($fic);
	}else
		echo $xml;
	
}


?>
