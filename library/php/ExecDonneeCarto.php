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


function get_rub_kml($id,$query) {

	global $site, $objSite,  $GrilleGeo;

	//récupèration de la commune
	if($query=="commune"){
		$g = new Granulat($id, $objSite, false);
		$id= $g->GetCommuneId();
	}

	$sql = "SELECT dc.valeur
				FROM spip_rubriques r
					INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique
					INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee
					INNER JOIN spip_forms_donnees_champs dc ON dc.id_donnee = da.id_donnee AND dc.champ = 'texte_1'
				WHERE fd.id_form = ".$GrilleGeo."
					AND r.id_rubrique = ".$id;
	//echo $site." - ".$sql;

	$DB = new mysql($site["SQL_HOST"], $site["SQL_LOGIN"], $site["SQL_PWD"], $site["SQL_DB"], $DB_OPTIONS);
	$DB->connect();
	//charge les propiétés du granulat
	$req = $DB->query($sql);
	$DB->close();
	//echo $site["SQL_LOGIN"]." ".$sql."<br/>";
	
	
	$BaseKml =""; 
	while($row = mysql_fetch_assoc($req))
	{
		$BaseKml .= $row['valeur'];
	}

	$kml = $BaseKml;
	if(strstr($BaseKml,"<kml")!="") {
		//on renvoieun flux xml
		header('Content-Type: application/vnd.google-earth.kml+xml');
		header("Content-Disposition: attachment; filename=\"".$site["NOM"].".kml\"");
	}
	if(strrpos($BaseKml, "http://")>0){
		//on construit un kml à partir de plusieurs placemarks
		$kml = "<?xml version='1.0' encoding='UTF-8'?>";
		$kml .= "<kml xmlns='http://earth.google.com/kml/2.0'>";
		$kml .= "<Folder>
			<Style id=\"trb441\">
				<LineStyle>
					<color>FFFFFFFF</color>
					<width>1</width>
				</LineStyle>
				<PolyStyle>
					<color>FFFFFFFF</color>
					<fill>0</fill>
				</PolyStyle>
			</Style>
			<name>topic-topos-".$query."-".$id."</name>
			<open>1</open>";
		$kml .= $BaseKml;
		$kml .=  "</Folder>
			</kml>";
		//on renvoieun flux xml
		header('Content-Type: application/vnd.google-earth.kml+xml');
		header("Content-Disposition: attachment; filename=\"onadabase.kml\"");
	}
	echo $kml;
}


function get_kml($bbox) {

	global $SiteInfo, $GrilleGeo;

	$site = $_SESSION["Site"];

	$values = explode(",", $bbox);


	$sql = "SELECT r.id_rubrique, r.titre, r.descriptif, r.texte
					, fichier
					, da.id_donnee
					, dc1.valdec lat, dc2.valdec lng
				FROM spip_rubriques r
					INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique
					INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee
					INNER JOIN spip_forms_donnees_champs dc1 ON dc1.id_donnee = da.id_donnee AND dc1.champ = 'ligne_1'
						AND dc1.valdec BETWEEN ".$values[1]." AND ".$values[3]."
					INNER JOIN spip_forms_donnees_champs dc2 ON dc2.id_donnee = da.id_donnee AND dc2.champ = 'ligne_2'
						AND dc2.valdec BETWEEN ".$values[0]." AND ".$values[2]."
					LEFT JOIN spip_documents_rubriques dr ON r.id_rubrique = dr.id_rubrique
					LEFT JOIN spip_documents d ON dr.id_document = d.id_document
				WHERE fd.id_form = ".$GrilleGeo."
				GROUP BY r.id_rubrique
				LIMIT 0 , ".MaxMarker;
	//echo $site." - ".$sql;
	$connexion = connexion($SiteInfo[SiteDbNom][$site], $SiteInfo[SiteDbPasse][$site], $SiteInfo[SiteDbBase][$site], $SiteInfo[SiteDbServeur][$site]);
	$req = ExecRequete($sql, $connexion);

	header('Content-Type: application/vnd.google-earth.kml+xml');
	header("Content-Disposition: attachment; filename=\"topic-topos.kml\"");

	echo "<?xml version='1.0' encoding='UTF-8'?>";
	echo "<kml xmlns='http://earth.google.com/kml/2.0'>";
	echo "<Folder>
  			<name>Topic-Topos : ".$SiteInfo[SiteGeo][$site]."</name>
  			<open>1</open>
  			<description>
			  Le patrimoine des communes de France
  			</description>
			<Style id='ttStyle'>
      			<IconStyle>
         			<scale>1.1</scale>
         			<Icon>
            			<href>".IconMarker."</href>
         			</Icon>
		      </IconStyle>
		      <LabelStyle>
		         <color>#FC7303</color>
		         <scale>1.5</scale>
		      </LabelStyle>
			  <BalloonStyle>
				  <text><![CDATA[
				  <b><font color='#FC7303' size='+3'>$[name]</font></b>
				  <font face='Courier'>$[description]</font>
				  <img src='".LogoPti."' border='0'>
				  <br/><br/>
				  <!-- insert the to/from hyperlinks -->
				  $[geDirections]
				  ]]></text>
				</BalloonStyle>
		    </Style>";
	while($row = mysql_fetch_assoc($req))
	{
		//lien
		$lien =SiteRoot."topicFiche.php?site=".$_SESSION["Site"]."&granulat=".$row['id_rubrique'];
		//image
		$ima = get_vignette_granulat($row['fichier'],HMVL,LMVL);
		echo "<Placemark>
				<name>".$row['titre']."</name>
				<styleUrl>ttStyle</styleUrl>
				<description><![CDATA[
					<div class='BlocGranulatGM'>
						<div class='BlocGranulatImg'>".$ima."</div>
						<div class='BlocGranulatTopic'><a href='".$lien."'>".$row['titre']."</a></div>
						<div class='BlocGranulatNotice'>".tronquer($row['texte'],60)."</div>
					</div>]]></description>
				<LookAt>
					<longitude>".$row['lng']."</longitude>
					<latitude>".$row['lat']."</latitude>
					<tilt>0</tilt>
					<heading>0</heading>
				</LookAt>
				<Snippet>".tronquer($row['texte'],30)."</Snippet>
				<Point>
					<coordinates>".$row['lng'].",".$row['lat']."</coordinates>
				</Point>
			</Placemark>";
	}
 	echo "</Folder>";
 	echo "</kml>";
}

function get_theme_markers($id) {

	global $SiteInfo;

	$site = $_SESSION["Site"];

	$sql = "SELECT id_groupe, titre
		FROM spip_groupes_mots
		WHERE id_parent = ".$id;
	//echo $site." - ".$sql;
	$connexion = connexion($SiteInfo[SiteDbNom][$site], $SiteInfo[SiteDbPasse][$site], $SiteInfo[SiteDbBase][$site], $SiteInfo[SiteDbServeur][$site]);
	$req = ExecRequete($sql, $connexion);

	$form = "<form id='themesTopos' name='themesTopos' method='get' action='#'>";
	while($row = mysql_fetch_assoc($req))
	{
		$js = " onclick=\"GetMarkers(".$row['id_groupe'].",'themes');\" ";
 		$form .= "<div class='BlocOutilsBox'><input ".$js." type='checkbox' name='cb".$row['id_groupe']."' checked='checked' value='".$row['id_groupe']."' /> ".$row['titre']."</div>";
	}
	$form .= "</form>";
 	echo $form;
}


function get_marker_rub($Id) {

	global $SiteInfo, $GrilleGeo;

	$site = $_SESSION["Site"];

	$sql = "SELECT r.id_rubrique, r.titre, r.descriptif, da.id_donnee
			,dc1.valeur lat, dc2.valeur lng, dc3.valeur zoom, dc4.valeur type
		FROM spip_rubriques r
			INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique
			INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
			INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee
			INNER JOIN spip_forms_donnees_champs dc1 ON dc1.id_donnee = da.id_donnee AND dc1.champ = 'ligne_1'
			INNER JOIN spip_forms_donnees_champs dc2 ON dc2.id_donnee = da.id_donnee AND dc2.champ = 'ligne_2'
			INNER JOIN spip_forms_donnees_champs dc3 ON dc3.id_donnee = da.id_donnee AND dc3.champ = 'ligne_3'
			INNER JOIN spip_forms_donnees_champs dc4 ON dc4.id_donnee = da.id_donnee AND dc4.champ = 'ligne_5'
		WHERE r.id_rubrique =".$Id."
			AND fd.id_form = ".$GrilleGeo."
		GROUP BY r.id_rubrique
		LIMIT 0 , ".MaxMarker;
	//echo $sql;
	$connexion = connexion($SiteInfo[SiteDbNom][$site], $SiteInfo[SiteDbPasse][$site], $SiteInfo[SiteDbBase][$site], $SiteInfo[SiteDbServeur][$site]);
	$req = ExecRequete($sql, $connexion);

	$result[0]="vide";
	while($row = mysql_fetch_assoc($req))
	{
 		$result[0] = $row['lat'];
 		$result[1] = $row['lng'];
 		$result[2] = $row['zoom'];
 		$result[3] = $row['type'];
	}
 	return $result;
}


function sauve_marker($action,$id,$zoommin,$zoommax,$lat,$lng,$adresse,$type) {

	global $site, $GrilleGeo;

	// on vérifie qu'un choix est bien pass?
	switch ($action) {
		case "Ajouter":
			//vérifie s'ilfaut créer un marker
			//echo "id_marker".$id_marker;
			if($id_marker == -1) {
				// on crée un nouveau marker
				$sql = "INSERT INTO `carto_markers` (titre, coor, lat, lng, date, zoom)
				VALUES ('".$titleInput."', '".$marker_coor."', ".$lat.",".$lng.",now(),".$zoom.")";
				mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
				// on récupère l'identifiant
				//echo $sql;
				$new_marker = mysql_insert_id();
			} else
				$new_marker = $id_marker;

			// on crée un nouveau DOCUMENT POUR LE marker
			$sql = "INSERT INTO `carto_markers_documents` (id_document, id_marker)
			VALUES (".$id_document.", ".$new_marker.")";
			mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
		  	break;
		case "Modifier":
		  	//récupère l'id_donnée
			$sql = "SELECT fd.id_donnee
				FROM spip_forms_donnees fd
					INNER JOIN spip_forms_donnees_articles da ON da.id_donnee = fd.id_donnee
					INNER JOIN spip_articles a ON a.id_article = da.id_article AND a.id_rubrique = ".$id."
				WHERE fd.id_form = ".$GrilleGeo;
			//echo $sql;
			$DB = new mysql($site["SQL_HOST"], $site["SQL_LOGIN"], $site["SQL_PWD"], $site["SQL_DB"], $DB_OPTIONS);
			$DB->connect();
			//charge les propiétés du granulat
			$req = $DB->query($sql);
			$DB->close();
			if (mysql_num_rows($req) == 0) {
				GetRubNewGeoloc($id,$zoommin,$zoommax,$lat,$lng,$adresse,$type);
			}else{
				$row = mysql_fetch_assoc($req);
				$IdDon = $row['id_donnee'];
				//echo "suprrime les champs sauf kml<br/>";
				$sql = "DELETE FROM spip_forms_donnees_champs WHERE id_donnee = ".$IdDon." AND champ <> 'texte_1'" ;
			$DB = new mysql($site["SQL_HOST"], $site["SQL_LOGIN"], $site["SQL_PWD"], $site["SQL_DB"], $DB_OPTIONS);
			$DB->connect();
			//charge les propiétés du granulat
			$req = $DB->query($sql);
			$DB->close();
				//mise à jour des champs
				$sql = "INSERT INTO `spip_forms_donnees_champs` (valdec,`id_donnee`, `champ`, `valeur`, `maj`)
					VALUES (".$lat.", ".$IdDon.", 'ligne_1', ".$lat.", now())";
			$DB = new mysql($site["SQL_HOST"], $site["SQL_LOGIN"], $site["SQL_PWD"], $site["SQL_DB"], $DB_OPTIONS);
			$DB->connect();
			//charge les propiétés du granulat
			$req = $DB->query($sql);
			$DB->close();
				$sql = "INSERT INTO `spip_forms_donnees_champs` (valdec,`id_donnee`, `champ`, `valeur`, `maj`)
					VALUES (".$lng.", ".$IdDon.", 'ligne_2', ".$lng.", now())";
			$DB = new mysql($site["SQL_HOST"], $site["SQL_LOGIN"], $site["SQL_PWD"], $site["SQL_DB"], $DB_OPTIONS);
			$DB->connect();
			//charge les propiétés du granulat
			$req = $DB->query($sql);
			$DB->close();
				$sql = "INSERT INTO `spip_forms_donnees_champs` (valint,`id_donnee`, `champ`, `valeur`, `maj`)
					VALUES (".$zoommin.", ".$IdDon.", 'ligne_3', ".$zoommin.", now())";
			$DB = new mysql($site["SQL_HOST"], $site["SQL_LOGIN"], $site["SQL_PWD"], $site["SQL_DB"], $DB_OPTIONS);
			$DB->connect();
			//charge les propiétés du granulat
			$req = $DB->query($sql);
			$DB->close();
				$sql = "INSERT INTO `spip_forms_donnees_champs` (valint,`id_donnee`, `champ`, `valeur`, `maj`)
					VALUES (".$zoommax.", ".$IdDon.", 'ligne_4', ".$zoommax.", now())";
			$DB = new mysql($site["SQL_HOST"], $site["SQL_LOGIN"], $site["SQL_PWD"], $site["SQL_DB"], $DB_OPTIONS);
			$DB->connect();
			//charge les propiétés du granulat
			$req = $DB->query($sql);
			$DB->close();
				$sql = "INSERT INTO `spip_forms_donnees_champs` (`id_donnee`, `champ`, `valeur`, `maj`)
					VALUES (".$IdDon.", 'ligne_5', '".$type."', now())";
				//echo $sql;
			$DB = new mysql($site["SQL_HOST"], $site["SQL_LOGIN"], $site["SQL_PWD"], $site["SQL_DB"], $DB_OPTIONS);
			$DB->connect();
			//charge les propiétés du granulat
			$req = $DB->query($sql);
			$DB->close();
				$adresse=utf8_decode($adresse);
				$sql = "INSERT INTO `spip_forms_donnees_champs` (`id_donnee`, `champ`, `valeur`, `maj`)
					VALUES (".$IdDon.", 'ligne_7', '".$adresse."', now())";
				//echo $sql."<br/>";
			$DB = new mysql($site["SQL_HOST"], $site["SQL_LOGIN"], $site["SQL_PWD"], $site["SQL_DB"], $DB_OPTIONS);
			$DB->connect();
			//charge les propiétés du granulat
			$req = $DB->query($sql);
			$DB->close();
				$sql = "INSERT INTO `spip_forms_donnees_champs` (`id_donnee`, `champ`, `valeur`, `maj`)
					VALUES (".$IdDon.", 'ligne_8', '', now())";
			$DB = new mysql($site["SQL_HOST"], $site["SQL_LOGIN"], $site["SQL_PWD"], $site["SQL_DB"], $DB_OPTIONS);
			$DB->connect();
			//charge les propiétés du granulat
			$req = $DB->query($sql);
			$DB->close();
			}

		  	break;
		case "Supprimer":
		  $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
		  break;
  }
}

function GetRubNewGeoloc($id,$zoommin,$zoommax,$lat,$lng,$adresse,$type) {

	global $site, $GrilleGeo;

	//récupère la grille géoloc pour la rubrique
	$sql = "SELECT a.id_article
		FROM spip_articles a
			INNER JOIN spip_forms_articles fa ON fa.id_article = a.id_article
		WHERE a.id_rubrique = ".$id;
	$DB = new mysql($site["SQL_HOST"], $site["SQL_LOGIN"], $site["SQL_PWD"], $site["SQL_DB"], $DB_OPTIONS);
	$DB->connect();
	//charge les propiétés du granulat
	$req = $DB->query($sql);
	$DB->close();
	$row = mysql_fetch_row($req);
	$IdA = $row[0];

	if(!$IdA) {
		//attache le form à l'article
		$sql = "INSERT INTO `spip_articles` (id_rubrique, titre)
			VALUES (".$id.",'Grille ".$id."')";
		ExecRequete($sql, $connexion);
		$IdA =  mysql_insert_id();
	}

		//attache le form à l'article
		$sql = "INSERT INTO `spip_forms_articles` (id_form, id_article)
			VALUES (".$GrilleGeo.",".$IdA.")";
		ExecRequete($sql, $connexion);
		//création de la donnée du formulaire
		$sql = "INSERT INTO `spip_forms_donnees` (`id_form`, `date`,`confirmation`, `statut`, `rang`)
			VALUES (".$GrilleGeo.", now(), 'valide', 'prop', 1)";
		ExecRequete($sql, $connexion);
		$donId = mysql_insert_id();
		//echo "création de la donnée à l'article<br/>";
		$sql = "INSERT INTO `spip_forms_donnees_articles` (`id_donnee`, `id_article`)
			VALUES (".$donId.", ".$IdA.")";
		ExecRequete($sql, $connexion);
	
		//création des valeurs					//problème CAST to Decimal et du int
		$sql = "INSERT INTO `spip_forms_donnees_champs` (valdec, `id_donnee`, `champ`, `valeur`, `maj`)
			VALUES (".$lat.", ".$donId.", 'ligne_1', ".$lat.", now())";
		ExecRequete($sql, $connexion);
		$sql = "INSERT INTO `spip_forms_donnees_champs` (valdec,`id_donnee`, `champ`, `valeur`, `maj`)
			VALUES (".$lng.", ".$donId.", 'ligne_2', ".$lng.", now())";
		ExecRequete($sql, $connexion);
		$sql = "INSERT INTO `spip_forms_donnees_champs` (valint, `id_donnee`, `champ`, `valeur`, `maj`)
			VALUES (".$zoommin.", ".$donId.", 'ligne_3', ".$zoommin.", now())";
		ExecRequete($sql, $connexion);
		$sql = "INSERT INTO `spip_forms_donnees_champs` (valint, `id_donnee`, `champ`, `valeur`, `maj`)
			VALUES (".$zoommax.", ".$donId.", 'ligne_4', ".$zoommax.", now())";
		ExecRequete($sql, $connexion);
		$sql = "INSERT INTO `spip_forms_donnees_champs` (`id_donnee`, `champ`, `valeur`, `maj`)
			VALUES (".$donId.", 'ligne_5', '".$type."', now())";
		ExecRequete($sql, $connexion);
		$adresse=utf8_decode($adresse);
		$sql = "INSERT INTO `spip_forms_donnees_champs` (`id_donnee`, `champ`, `valeur`, `maj`)
			VALUES (".$donId.", 'ligne_7', '".$adresse."', now())";
		//echo $sql."<br/>";
		ExecRequete($sql, $connexion);

}

function get_marker($site, $objSite, $id, $southWestLat, $northEastLat, $southWestLng, $northEastLng, $zoom, $query="", $themes="", $i = 0) {

	//$site = $_SESSION["Site"];

	// on récupère les markers suivants les coordonnée
	$NewQuery = "idFiche";
	switch ($query) {
		case "dep":
			$sql = "SELECT r.id_rubrique, r.titre, r.descriptif, r.texte
					, fichier
					, da.id_donnee
					, dc1.valdec lat, dc2.valdec lng, dc3.valint zoommin, dc4.valint zoommax
					, dc5.valeur cartotype
					, dc7.valeur adresse
					, dc8.valeur kml
				FROM spip_rubriques r
					INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique
					INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee
					INNER JOIN spip_forms_donnees_champs dc1 ON dc1.id_donnee = da.id_donnee AND dc1.champ = 'ligne_1'
						AND dc1.valdec BETWEEN ".$southWestLat." AND ".$northEastLat."
					INNER JOIN spip_forms_donnees_champs dc2 ON dc2.id_donnee = da.id_donnee AND dc2.champ = 'ligne_2'
						AND dc2.valdec BETWEEN ".$southWestLng." AND ".$northEastLng."
					INNER JOIN spip_forms_donnees_champs dc3 ON dc3.id_donnee = da.id_donnee AND dc3.champ = 'ligne_3'
						AND dc3.valint BETWEEN 1 AND 17 
					INNER JOIN spip_forms_donnees_champs dc4 ON dc4.id_donnee = da.id_donnee AND dc4.champ = 'ligne_4'
					INNER JOIN spip_forms_donnees_champs dc5 ON dc5.id_donnee = da.id_donnee AND dc5.champ = 'ligne_5'
					INNER JOIN spip_forms_donnees_champs dc7 ON dc7.id_donnee = da.id_donnee AND dc7.champ = 'ligne_7'
					LEFT JOIN spip_forms_donnees_champs dc8 ON dc8.id_donnee = da.id_donnee AND dc8.champ = 'texte_1'
					LEFT JOIN spip_documents_rubriques dr ON r.id_rubrique = dr.id_rubrique
					LEFT JOIN spip_documents d ON dr.id_document = d.id_document
				WHERE fd.id_form = ".$site["GRILLE_GEO"]."
				GROUP BY r.id_rubrique
				ORDER BY dc3.valeur
				LIMIT 0 , ".MaxMarker;
		  	break;
		case "id":
			//requète pour un élément
			$sql = "SELECT r.id_rubrique, r.titre, r.descriptif, r.texte
					, fichier
					, da.id_donnee
					, dc1.valdec lat, dc2.valdec lng, dc3.valint zoommin, dc4.valint zoommax
					, dc5.valeur cartotype
					, dc7.valeur adresse
				FROM spip_rubriques r
					INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique
					INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee
					INNER JOIN spip_forms_donnees_champs dc1 ON dc1.id_donnee = da.id_donnee AND dc1.champ = 'ligne_1'
						AND dc1.valdec BETWEEN ".$southWestLat." AND ".$northEastLat."
					INNER JOIN spip_forms_donnees_champs dc2 ON dc2.id_donnee = da.id_donnee AND dc2.champ = 'ligne_2'
						AND dc2.valdec BETWEEN ".$southWestLng." AND ".$northEastLng."
					INNER JOIN spip_forms_donnees_champs dc3 ON dc3.id_donnee = da.id_donnee AND dc3.champ = 'ligne_3'
					INNER JOIN spip_forms_donnees_champs dc4 ON dc4.id_donnee = da.id_donnee AND dc4.champ = 'ligne_4'
					INNER JOIN spip_forms_donnees_champs dc5 ON dc5.id_donnee = da.id_donnee AND dc5.champ = 'ligne_5'
					INNER JOIN spip_forms_donnees_champs dc7 ON dc7.id_donnee = da.id_donnee AND dc7.champ = 'ligne_7'
					LEFT JOIN spip_documents_rubriques dr ON r.id_rubrique = dr.id_rubrique
					LEFT JOIN spip_documents d ON dr.id_document = d.id_document
				WHERE fd.id_form = 53
				ORDER BY dc3.valeur
				LIMIT 0 , ".MaxMarker;
		  	break;
		case "idFiche":
			//requète pour un élément
			$sql = "SELECT r.id_rubrique, r.titre, r.descriptif, r.texte
					, fichier, da.id_donnee
					, dc1.valdec lat, dc2.valdec lng, dc3.valint zoommin, dc4.valint zoommax
					, dc5.valeur cartotype , dc7.valeur adresse
					, dc8.valeur kml
					, dc9.valeur navig
				FROM spip_rubriques r
					INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique
					INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
					LEFT JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee
					LEFT JOIN spip_forms_donnees_champs dc1 ON dc1.id_donnee = da.id_donnee AND dc1.champ = 'ligne_1'
					LEFT JOIN spip_forms_donnees_champs dc2 ON dc2.id_donnee = da.id_donnee AND dc2.champ = 'ligne_2'
					LEFT JOIN spip_forms_donnees_champs dc3 ON dc3.id_donnee = da.id_donnee AND dc3.champ = 'ligne_3'
					LEFT JOIN spip_forms_donnees_champs dc4 ON dc4.id_donnee = da.id_donnee AND dc4.champ = 'ligne_4'
					LEFT JOIN spip_forms_donnees_champs dc5 ON dc5.id_donnee = da.id_donnee AND dc5.champ = 'ligne_5'
					LEFT JOIN spip_forms_donnees_champs dc7 ON dc7.id_donnee = da.id_donnee AND dc7.champ = 'ligne_7'
					LEFT JOIN spip_forms_donnees_champs dc8 ON dc8.id_donnee = da.id_donnee AND dc8.champ = 'texte_1'
					LEFT JOIN spip_forms_donnees_champs dc9 ON dc9.id_donnee = da.id_donnee AND dc9.champ = 'ligne_8'
					LEFT JOIN spip_documents_rubriques dr ON r.id_rubrique = dr.id_rubrique
					LEFT JOIN spip_documents d ON dr.id_document = d.id_document
				WHERE r.id_rubrique =".$id." AND fd.id_form =".$site["GRILLE_GEO"];
		  	break;
		case "idFicheRN":
			//requète pour un élément
			$sql = "SELECT r.id_rubrique, r.titre, r.descriptif, r.texte
					, fichier, da.id_donnee
					, dc1.valdec lat, dc2.valdec lng, dc3.valint zoommin, dc4.valint zoommax
					, dc5.valeur cartotype , dc7.valeur adresse
					, dc8.valeur kml
					, dc9.valeur navig
				FROM spip_rubriques r
					INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique
					INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
					LEFT JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee
					LEFT JOIN spip_forms_donnees_champs dc1 ON dc1.id_donnee = da.id_donnee AND dc1.champ = 'ligne_1'
					LEFT JOIN spip_forms_donnees_champs dc2 ON dc2.id_donnee = da.id_donnee AND dc2.champ = 'ligne_2'
					LEFT JOIN spip_forms_donnees_champs dc3 ON dc3.id_donnee = da.id_donnee AND dc3.champ = 'ligne_3'
					LEFT JOIN spip_forms_donnees_champs dc4 ON dc4.id_donnee = da.id_donnee AND dc4.champ = 'ligne_4'
					LEFT JOIN spip_forms_donnees_champs dc5 ON dc5.id_donnee = da.id_donnee AND dc5.champ = 'ligne_5'
					LEFT JOIN spip_forms_donnees_champs dc7 ON dc7.id_donnee = da.id_donnee AND dc7.champ = 'ligne_7'
					LEFT JOIN spip_forms_donnees_champs dc8 ON dc8.id_donnee = da.id_donnee AND dc8.champ = 'texte_1'
					LEFT JOIN spip_forms_donnees_champs dc9 ON dc9.id_donnee = da.id_donnee AND dc9.champ = 'ligne_8'
					LEFT JOIN spip_documents_rubriques dr ON r.id_rubrique = dr.id_rubrique
					LEFT JOIN spip_documents d ON dr.id_document = d.id_document
				WHERE r.id_rubrique =".$id." AND fd.id_form =".$site["GRILLE_GEO"];
		  	break;
		case "idFicheEnfant":
			//requète pour un élément
			$sql = "SELECT r.id_rubrique, r.titre, r.descriptif, r.texte
					, fichier, da.id_donnee
					, dc1.valdec lat, dc2.valdec lng, dc3.valint zoommin, dc4.valint zoommax
					, dc5.valeur cartotype , dc7.valeur adresse
					, dc8.valeur kml
					, dc9.valeur navig
				FROM spip_rubriques r
					INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique
					INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
					LEFT JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee
					LEFT JOIN spip_forms_donnees_champs dc1 ON dc1.id_donnee = da.id_donnee AND dc1.champ = 'ligne_1'
					LEFT JOIN spip_forms_donnees_champs dc2 ON dc2.id_donnee = da.id_donnee AND dc2.champ = 'ligne_2'
					LEFT JOIN spip_forms_donnees_champs dc3 ON dc3.id_donnee = da.id_donnee AND dc3.champ = 'ligne_3'
					LEFT JOIN spip_forms_donnees_champs dc4 ON dc4.id_donnee = da.id_donnee AND dc4.champ = 'ligne_4'
					LEFT JOIN spip_forms_donnees_champs dc5 ON dc5.id_donnee = da.id_donnee AND dc5.champ = 'ligne_5'
					LEFT JOIN spip_forms_donnees_champs dc7 ON dc7.id_donnee = da.id_donnee AND dc7.champ = 'ligne_7'
					LEFT JOIN spip_forms_donnees_champs dc8 ON dc8.id_donnee = da.id_donnee AND dc8.champ = 'texte_1'
					LEFT JOIN spip_forms_donnees_champs dc9 ON dc9.id_donnee = da.id_donnee AND dc9.champ = 'ligne_8'
					LEFT JOIN spip_documents_rubriques dr ON r.id_rubrique = dr.id_rubrique
					LEFT JOIN spip_documents d ON dr.id_document = d.id_document
				WHERE r.id_parent =".$id." AND fd.id_form =".$site["GRILLE_GEO"];
			if($objSite->scope["Alpha"]!=-1)		
				$sql .= " AND r.titre LIKE '".$objSite->scope["Alpha"]."%' ";
		  	$NewQuery = "idFicheEnfant";
			break;
		case "admin":
			//requète pour un élément
			$sql = "SELECT r.id_rubrique, r.titre, r.descriptif, r.texte
					, fichier, da.id_donnee
					, dc1.valdec lat, dc2.valdec lng, dc3.valint zoommin, dc4.valint zoommax
					, dc5.valeur cartotype , dc7.valeur adresse
					, dc8.valeur kml
				FROM spip_rubriques r
					INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique
					INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee AND fd.id_form = ".$site["GRILLE_GEO"]."
					LEFT JOIN spip_forms_donnees_champs dc1 ON dc1.id_donnee = da.id_donnee AND dc1.champ = 'ligne_1'
					LEFT JOIN spip_forms_donnees_champs dc2 ON dc2.id_donnee = da.id_donnee AND dc2.champ = 'ligne_2'
					LEFT JOIN spip_forms_donnees_champs dc3 ON dc3.id_donnee = da.id_donnee AND dc3.champ = 'ligne_3'
					LEFT JOIN spip_forms_donnees_champs dc4 ON dc4.id_donnee = da.id_donnee AND dc4.champ = 'ligne_4'
					LEFT JOIN spip_forms_donnees_champs dc5 ON dc5.id_donnee = da.id_donnee AND dc5.champ = 'ligne_5'
					LEFT JOIN spip_forms_donnees_champs dc7 ON dc7.id_donnee = da.id_donnee AND dc7.champ = 'ligne_7'
					LEFT JOIN spip_forms_donnees_champs dc8 ON dc8.id_donnee = da.id_donnee AND dc8.champ = 'texte_1'
					LEFT JOIN spip_documents_rubriques dr ON r.id_rubrique = dr.id_rubrique
					LEFT JOIN spip_documents d ON dr.id_document = d.id_document
				WHERE r.id_rubrique =".$id."  
				ORDER BY dc1.valdec DESC
				LIMIT 0 , ".MaxMarker;
		  	break;
		case "commune":
			//echo "recupère la liste des rubriques enfants = ".$id."<br/>";
			$g = new Granulat($id, $objSite, false);
			$IdsEnfant = str_replace(DELIM, ",", $g->GetEnfantIds());
			$IdsEnfant .= $id;
			//requète pour les communes
			$sql = "SELECT r.id_rubrique, r.titre, r.descriptif, r.texte
					, fichier
					, da.id_donnee
					, dc1.valdec lat, dc2.valdec lng, dc3.valint zoommin, dc4.valint zoommax
					, dc5.valeur cartotype
					, dc7.valeur adresse
				FROM spip_rubriques r
					INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique
					INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee
					INNER JOIN spip_forms_donnees_champs dc1 ON dc1.id_donnee = da.id_donnee AND dc1.champ = 'ligne_1'
					INNER JOIN spip_forms_donnees_champs dc2 ON dc2.id_donnee = da.id_donnee AND dc2.champ = 'ligne_2'
					INNER JOIN spip_forms_donnees_champs dc3 ON dc3.id_donnee = da.id_donnee AND dc3.champ = 'ligne_3'
					INNER JOIN spip_forms_donnees_champs dc4 ON dc4.id_donnee = da.id_donnee AND dc4.champ = 'ligne_4'
					INNER JOIN spip_forms_donnees_champs dc5 ON dc5.id_donnee = da.id_donnee AND dc5.champ = 'ligne_5'
					INNER JOIN spip_forms_donnees_champs dc7 ON dc7.id_donnee = da.id_donnee AND dc7.champ = 'ligne_7'
					LEFT JOIN spip_documents_rubriques dr ON r.id_rubrique = dr.id_rubrique
					LEFT JOIN spip_documents d ON dr.id_document = d.id_document
				WHERE fd.id_form = ".$site["GRILLE_GEO"]."
					AND r.id_rubrique IN (".$IdsEnfant.")
				GROUP BY r.id_rubrique
				ORDER BY dc3.valeur
				LIMIT 0 , ".MaxMarker;
		  	break;
		case "theme":
			//recupère la liste des rubrique enfant
			//$IdsMot = get_mots_menu($id);
			//$IdsMot .= -1;
			//$themes = id_mot
			$IdsMot = $themes;
			//requète pour les communes
			$sql = "SELECT r.id_rubrique, r.titre, r.descriptif, r.texte
					, fichier
					, da.id_donnee
					, dc1.valdec lat, dc2.valdec lng, dc3.valint zoommin, dc4.valint zoommax
					, dc5.valeur cartotype
					, dc7.valeur adresse
				FROM spip_rubriques r
					INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique
					INNER JOIN  spip_mots_rubriques mr ON mr.id_rubrique = r.id_rubrique
					INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee
					INNER JOIN spip_forms_donnees_champs dc1 ON dc1.id_donnee = da.id_donnee AND dc1.champ = 'ligne_1'
						AND dc1.valdec BETWEEN ".$southWestLat." AND ".$northEastLat."
					INNER JOIN spip_forms_donnees_champs dc2 ON dc2.id_donnee = da.id_donnee AND dc2.champ = 'ligne_2'
						AND dc2.valdec BETWEEN ".$southWestLng." AND ".$northEastLng."
					INNER JOIN spip_forms_donnees_champs dc3 ON dc3.id_donnee = da.id_donnee AND dc3.champ = 'ligne_3'
					INNER JOIN spip_forms_donnees_champs dc4 ON dc4.id_donnee = da.id_donnee AND dc4.champ = 'ligne_4'
					INNER JOIN spip_forms_donnees_champs dc5 ON dc5.id_donnee = da.id_donnee AND dc5.champ = 'ligne_5'
					INNER JOIN spip_forms_donnees_champs dc7 ON dc7.id_donnee = da.id_donnee AND dc7.champ = 'ligne_7'
					LEFT JOIN spip_documents_rubriques dr ON r.id_rubrique = dr.id_rubrique
					LEFT JOIN spip_documents d ON dr.id_document = d.id_document
					LEFT JOIN spip_forms_donnees_champs dc8 ON dc8.id_donnee = da.id_donnee AND dc8.champ = 'texte_1'
				WHERE fd.id_form = 53
					AND mr.id_mot IN (".$IdsMot.")
				GROUP BY r.id_rubrique
				ORDER BY dc3.valeur
				LIMIT 0 , ".MaxMarker;
		  	break;
		case "themes":
			//requète pour les communes
			$sql = "SELECT r.id_rubrique, r.titre, r.descriptif, r.texte
					, fichier
					, da.id_donnee
					, dc1.valdec lat, dc2.valdec lng, dc3.valint zoommin, dc4.valint zoommax
					, dc5.valeur cartotype
					, dc7.valeur adresse
				FROM spip_rubriques r
					INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique
					INNER JOIN spip_mots_rubriques mr ON mr.id_rubrique = r.id_rubrique
					INNER JOIN spip_mots m ON m.id_mot = mr.id_mot
					INNER JOIN spip_groupes_mots gm ON gm.id_groupe = m.id_groupe AND gm.id_parent IN (".$themes.")
					INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee
					INNER JOIN spip_forms_donnees_champs dc1 ON dc1.id_donnee = da.id_donnee AND dc1.champ = 'ligne_1'
						AND dc1.valdec BETWEEN ".$southWestLat." AND ".$northEastLat."
					INNER JOIN spip_forms_donnees_champs dc2 ON dc2.id_donnee = da.id_donnee AND dc2.champ = 'ligne_2'
						AND dc2.valdec BETWEEN ".$southWestLng." AND ".$northEastLng."
					INNER JOIN spip_forms_donnees_champs dc3 ON dc3.id_donnee = da.id_donnee AND dc3.champ = 'ligne_3'
					INNER JOIN spip_forms_donnees_champs dc4 ON dc4.id_donnee = da.id_donnee AND dc4.champ = 'ligne_4'
					INNER JOIN spip_forms_donnees_champs dc5 ON dc5.id_donnee = da.id_donnee AND dc5.champ = 'ligne_5'
					INNER JOIN spip_forms_donnees_champs dc7 ON dc7.id_donnee = da.id_donnee AND dc7.champ = 'ligne_7'
					LEFT JOIN spip_documents_rubriques dr ON r.id_rubrique = dr.id_rubrique
					LEFT JOIN spip_documents d ON dr.id_document = d.id_document
				WHERE fd.id_form = 53
				GROUP BY r.id_rubrique
				ORDER BY dc3.valeur
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


		if($row['lat']!="0.00000000")
			$markers .= $row['lat'].DELIM;//point
		else
			$markers .= $site["DEF_LAT"].DELIM;//point

		if($row['lng']!="0.00000000")
			$markers .= $row['lng'].DELIM;//point
		else
			$markers .= $site["DEF_LNG"].DELIM;//point

		$markers .= $i.DELIM;
		$markers .= $row['id_rubrique'].DELIM;

		$markers .= "topic_$i ".DELIM;
		//Topic
		$markers .=Root."/new/lieux.php?site=".$objSite->id."&VoirEn=Topos&Rub=".$row['id_rubrique']."&query=".$NewQuery.DELIM;//lien
		//$markers .=get_fenetre_info($row,"Topic").DELIM;//localisation
		if($row['navig'])
			$markers .=$row['navig'].DELIM;		
		else
			$markers .=" ".DELIM;
		$markers .=$g->GetImages(68, 45).DELIM;//image
		//$markers .= "".DELIM;//image
		
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
		$markers .=$row['zoommin'].DELIM;
		$markers .=$row['zoommax'].DELIM;
		//adresse
		$markers .=utf8_encode($row['adresse']).DELIM;
		$markers .=$row['cartotype'].DELIM;
		//lien vers le kml
		if(strrpos($row['kml'], "http://")==0)
			//sous forme de fichier
			$markers .=$row['kml'].DELIM;
		else
			//sous forme de FLUX
			//$markers .= "http://www.mundilogiweb.com/onadabase/spip/IMG/kml/Gare_Lille_Flandre_a_Rue_Negrier_59800_Lille.kml";
			$markers .= 'http://www.mundilogiweb.com/onadabase/library/php/ExecDonneeCarto.php?f=get_rub_kml&site='.$objSite->id.'&id='.$row['id_rubrique'].'&query='.$query.DELIM;
		//$markers .= "\n<br>";


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


function get_fenetre_info($row, $type) {

	$htmlStr = "";
	switch ($type) {
		case 'Granulat':
			// récupère la localisation et enlève le dernier séparateur
			$htmlStr = $row['titre'];
			$htmlStr .=Root."/new/lieux.php?site=".$objSite->id."&VoirEn=Topos&Rub=".$row['id_rubrique'];//lien
		/*******************modif CAI ****************************/
		case 'Topic':
			// récupère la localisation et enlève le dernier séparateur
			$localisation = substr(get_navig_loca_granulat($row['id_rubrique']), 0, -3);

			$htmlStr .=$localisation.DELIM;


		/***************************fin *****************************/
		case 'Famille':
			//$htmlStr .= get_famille_topic($row['id_rubrique'],"frere");
			$htmlStr = get_famille_topic($row['id_rubrique'],"enfant");
			break;
		case 'Thematique':
			//$htmlStr .= get_theme_cloud($row['id_rubrique'],3);
			//$htmlStr .= get_tag_cloud($row['id_rubrique'],3);
			//$htmlStr .= "<div class='FicheGranulat'>";
			/*
			$htmlStr .= "<div class='BlocSujet'>
								<div class='BlocSujetTitre'>Les thémes du Topic</div>
								<div class='BlocSujetLiens'>".get_theme_cloud($row['id_rubrique'],3)."</div>
							</div>";
			*/
			//$htmlStr .= "<div class='BlocSujet'>";
			//$htmlStr .= "<div class='BlocSujetTitre'>Les mots clés du Topic</div>";
			//$htmlStr .= "<div class='BlocSujetLiens'>";
			//$htmlStr .= "<div class='BlocSujetTagCloud'>".get_tag_cloud($row['id_rubrique'],3)."</div>";
			//$htmlStr .= "</div>";
			//$htmlStr .= "</div>";
			//$htmlStr .= "</div>";

			break;
	}

		return $htmlStr;
}

?>
