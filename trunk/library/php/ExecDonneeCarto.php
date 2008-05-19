<?php
/*
header('Content-type: text/html; charset=iso-8859-1');
header('Content-type: text/html; charset=UTF-8');


*/
require_once($_SERVER["DOCUMENT_ROOT"]."/onadabase/param/ParamPage.php");


$site = $objSite->infos;

$GrilleGeo = $site["GRILLE_GEO"];

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
		$resultat = $markers = get_marker($site, $objSite, $_GET['id'], $_GET['southWestLat'], $_GET['northEastLat'],$_GET['southWestLng'], $_GET['northEastLng'], $_GET['zoom'], $_GET['MapQuery'], $themes);
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

function tronquer($tocut , $max_caracteres , $space='1' , $points='1') {
	if (strlen($tocut)>$max_caracteres){
		if ($space=='1'){
			$max_caracteres=strrpos(substr($tocut, 0, $max_caracteres), " ");
		}
		$tocut = substr($tocut, 0, $max_caracteres);
		if ($points=='1'){
			$tocut.=' ...';
		}
	}
	return $tocut;
}


function get_marker($site, $objSite, $id, $southWestLat, $northEastLat, $southWestLng, $northEastLng, $zoom, $query="", $themes="", $i = 0) {

	//$site = $_SESSION["Site"];

	// on récupère les markers suivants les coordonnée
	$NewQuery = "idFiche";
	switch ($query) {
		case "idFiche":
			//requète pour un élément
			$sql = "SELECT a.id_rubrique, a.id_article, a.titre, a.texte
					,fichier kml
				FROM spip_articles a 
					INNER JOIN spip_documents_articles da ON da.id_article = a.id_article
					INNER JOIN spip_documents d ON da.id_document = d.id_document
				WHERE a.id_article =".$id."  
				LIMIT 0 , ".MaxMarker;
		  	break;
	}

	$DB = new mysql($site["SQL_HOST"], $site["SQL_LOGIN"], $site["SQL_PWD"], $site["SQL_DB"], $DB_OPTIONS);
	$DB->connect();
	//charge les propiétés du granulat
	$req = $DB->query($sql);
	$DB->close();
	//echo $site["SQL_LOGIN"]." ".$sql."<br/>";
	//if($objSite->id==DEFSITE)
	
	//$i = 0;
	while($row = mysql_fetch_assoc($req))
	{

	//echo  $i."<br/>"; 
		//echo "recupère le granulat = ".$id."<br/>";
		$g = new Granulat($row['id_rubrique'], $objSite, true);

		//construction des markers
	/*******************************modif CAI*****************************************************************/


		$markers .= $site["DEF_LAT"].DELIM;//point

		$markers .= $site["DEF_LNG"].DELIM;//point

		$markers .= $i.DELIM;
		$markers .= $row['id_article'].DELIM;

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
		
		$markers .=utf8_encode($row['titre']).DELIM;
		$markers .=utf8_encode(tronquer($row['texte'],60)).DELIM;
		//création des onglets pour le granulats
		//$Val = $g->GetValeurForm($this->site->infos["GRILLE_Granulat"],"Titre", "", "  ", "Titre : ");
		//if(substr($row['descriptif'], -2)!="00")
		//if($Val!=" ")
			//Famillie sauf pour département et communes
		//	$markers .=get_fenetre_info($row,"Granulat").DELIM;
		//else
			$markers .="".DELIM;

		/*if(substr($row['descriptif'], -4)!="0000")
			//Thematique sauf pour département
			$markers .=get_fenetre_info($row,"Thematique").DELIM;
		else
		*/
			$markers .="".DELIM;
		//zoom
		$markers .=$site['DEF_ZOOM'].DELIM;
		$markers .='17'.DELIM;
		//adresse
		$markers .=' '.DELIM;
		//type carte
		$markers .='Mixte'.DELIM;
		//lien vers le kml
		$markers .=$site["pathSpip"].$row['kml'].DELIM;


	/***************************************************************fin*******************************/
		$i++;

	}

	//gestion des requêtes multisite
	if($objSite->infos["SITE_ENFANT"]!=-1 && $query!="idFiche"){
		foreach($objSite->infos["SITE_ENFANT"] as $siteenfant=>$type)
		{
				//echo $site." NextSiteEnfant:".$siteenfant."<br/>"; 
				$site = $objSite->sites[$siteenfant];
				//echo $objSite->sites."<br/>"; 
				$objSiteNew = new Site($objSite->sites, $siteenfant, $objSite->scope, false);
				echo get_marker($site, $objSiteNew, $id, $southWestLat, $northEastLat, $southWestLng, $northEastLng, $zoom, $query, $themes, $i);
		}
	}
			
	echo $markers;

}


?>
