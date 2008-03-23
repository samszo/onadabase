<?php

class Granulat
{
  public $id;
  public $titre;
  public $descriptif;
  public $texte;
  public $moyen;
  public $cause;
  public $consequence;
  public $localisation;
  public $arrDoc;
  public $TitreParent;
  public $IdParent;
  
  private $site;
  

  function __tostring() {
    return "Cette classe permet de définir et manipuler un granulat : .<br/>";
    }

  function __construct($id, $site, $complet=true) {

    //echo "$id, $site login=".$site["SQL_LOGIN"]."<br/>";
	$this->id = $id;
    $this->site = $site;
	if($complet){
		$this->GetProps();
		$this->GetDocs();
	}
  }

  function SetAuteur($newId,$objet){

  	if($this->site->scope["login"]!=-1){
			//association de l'article à l'auteur
			$sql = "INSERT INTO spip_auteurs_".$objet."s (id_".$objet.",id_auteur)
				SELECT ".$newId.", id_auteur FROM spip_auteurs where login='".$this->site->scope["login"]."'";					
			$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
			$req = $DB->query($sql);
			$DB->close();
	}
	echo "sql=".$sql."<br/>";
  	
  }
  
  function SetNewEnfant($titre,$id=-1){

	if($id==-1)
		$id=$this->id;
	
	//ajoute un nouvel enfant
	$sql = "INSERT INTO spip_rubriques
		SET titre = ".$this->site->GetSQLValueString($titre, "text").", id_parent=".$id;
	
	$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
	$req = $DB->query($sql);
	$newId = mysql_insert_id();
	$DB->close();
			
	return $newId;
  
  }
  
  function SetNewArticle($titre,$id=-1){

	if($id==-1)
		$id=$this->id;
	
	//ajoute un nouvel enfant
	$sql = "INSERT INTO spip_articles
		SET titre = ".$this->site->GetSQLValueString($titre, "text")
			.", statut='prepa'
			, date = now()"
			.", id_rubrique=".$id;
	
	$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
	$req = $DB->query($sql);
	$newId = mysql_insert_id();
	$DB->close();

	$this->SetAuteur($newId,'article');
	
	return $newId;
  
  }

  function SetMotClef($id_mot,$id=-1){

	if($id==-1)
		$id=$this->id;
	
	//ajoute un nouveau mot clef
	$sql = "INSERT INTO spip_mots_rubriques
		SET id_mot = ".$id_mot.", id_rubrique=".$id;
	
	$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
	$req = $DB->query($sql);
	$newId = mysql_insert_id();
	$DB->close();
	
	return $newId;
  
  }
  
  
  function GetGeo($id=-1) {
		
		if($id==-1)
			$id = $this->id;
		
			
		$sql = "SELECT r.id_rubrique, r.titre, r.descriptif, r.id_parent, da.id_donnee
				,dc1.valeur lat, dc2.valeur lng, dc3.valeur zoom, dc4.valeur type
			FROM spip_rubriques r
				INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique
				INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
				INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee AND fd.id_form = ".$this->site->infos["GRILLE_GEO"]."
				LEFT JOIN spip_forms_donnees_champs dc1 ON dc1.id_donnee = da.id_donnee AND dc1.champ = 'ligne_1'
				LEFT JOIN spip_forms_donnees_champs dc2 ON dc2.id_donnee = da.id_donnee AND dc2.champ = 'ligne_2'
				LEFT JOIN spip_forms_donnees_champs dc3 ON dc3.id_donnee = da.id_donnee AND dc3.champ = 'ligne_3'
				LEFT JOIN spip_forms_donnees_champs dc4 ON dc4.id_donnee = da.id_donnee AND dc4.champ = 'ligne_5'
			WHERE r.id_rubrique =".$id."
			GROUP BY r.id_rubrique
			LIMIT 0 , ".MaxMarker;
		//echo $sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
	
		$db->connect();
		$requete =  $db->query($sql);
		$db->close();
		$result = false;
		while ($r =  $db->fetch_assoc($requete)) {
			//gestion de la localisation parente si localisation  null
			if($r['lat']==""){
				if($r['id_parent']!=0){
					$result = $this->GetGeo($r['id_parent']);
				}else{
					$result['lat'] = $this->site->infos["DEF_LAT"];
					$result['lng'] = $this->site->infos["DEF_LNG"];
					$result['zoom'] = $this->site->infos["DEF_ZOOM"];
					$result['type'] = $this->site->infos["DEF_CARTE_TYPE"];
				}
			}else {
				$result['lat'] = $r['lat'];
				$result['lng'] = $r['lng'];
				$result['zoom'] = $r['zoom'];
				$GmapType = "G_SATELLITE_MAP";
				if($r['type']=="Plan")
					$GmapType = "G_NORMAL_MAP";
				if($r['type']=="Mixte")
					$GmapType = "G_HYBRID_MAP";
				if($r['type']=="Satellite")
					$GmapType = "G_SATELLITE_MAP";				
				$result['type'] = $GmapType;
			}
		}
		if(!$result){
			$result['lat'] = $this->site->infos["DEF_LAT"];
			$result['lng'] = $this->site->infos["DEF_LNG"];
			$result['zoom'] = $this->site->infos["DEF_ZOOM"];
			$result['type'] = $this->site->infos["DEF_CARTE_TYPE"];		
		}
		
		
		return $result;

		}
  
  
  function GetArticle($extraSql=""){
		//récupère pour la rubrique l'article ayant les condition de extra
		$sql = "SELECT a.id_article
			FROM spip_rubriques r
				INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique  
			WHERE r.id_rubrique = ".$this->id." ".$extraSql;
		//echo $sql."<br/>";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
		$req = $DB->query($sql);
		$DB->close();
		//vérifie l'existance de l'article pour les forms
		if ($r = $DB->fetch_assoc($req)){
			$artId = $r['id_article']; 
		} else {
			//Création de l'article pour la rubrique
			$NomGrille = $this->site->GetSQLValueString($this->titre, "text");
			$sql = "INSERT INTO `spip_articles` (`titre`, id_rubrique, statut, date)
				VALUES (".$NomGrille.",".$this->id.",'prepa', now())";
			$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
			$req = $DB->query($sql);
			$artId = mysql_insert_id();
			$DB->close();
			$this->SetAuteur($artId,'article');
		}

		return $artId; 

	}
  
  function GetIdDonnee($formId, $artId=-1, $doublon=false){

		if($artId==-1)
			$artId = $this->GetArticle();
		
		$donId = false;
		
		if(!$doublon){
			//vérifie l'existence de la donnee
	  		$sql = "SELECT fd.id_donnee
				FROM spip_forms_donnees_articles da 
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee AND fd.id_form = ".$formId."
			WHERE da.id_article = ".$artId;
			//echo $sql."<br/>";
			$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
			$req = $DB->query($sql);
			$DB->close();
			$r = $DB->fetch_assoc($req);
			$donId= $r['id_donnee'];
		}
		
		if(!$donId){
			//attache le form à l'article
			$sql = "INSERT INTO `spip_forms_articles` (id_form, id_article)
				VALUES (".$formId.",".$artId.")";
			$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
			$req = $DB->query($sql);
			$DB->close();
			//création de la donnée du formulaire
			$sql = "INSERT INTO `spip_forms_donnees` (`id_form`, `date`,`confirmation`, `statut`, `rang`)
				VALUES (".$formId.", now(), 'valide', 'prop', 1)";
			$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
			$req = $DB->query($sql);
			$donId = mysql_insert_id();
			$DB->close();
			//attache la donnée à l'article
			$sql = "INSERT INTO `spip_forms_donnees_articles` (`id_donnee`, `id_article`)
				VALUES (".$donId.", ".$artId.")";
			$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
			$req = $DB->query($sql);
			$DB->close();
			//echo "-- création de la donnée ".$donId." \n<br/>"; 
		}

		return $donId;

	}
  
  function GetLiens($rReq=false){
	
		//récupère la commune du granulat
		$sql = "SELECT nom_site, url_site
			FROM `spip_syndic`
			WHERE statut = 'publie' AND id_rubrique =".$this->id;
		//echo $sql."<br/>";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
		$req = $DB->query($sql);
		$DB->close();
		
		if($rReq)
			return $req;
		
		$valeur="";
		while($r = $DB->fetch_assoc($req)) {
			$valeur .= "<a href='".$r['url_site']."' style='cursor: pointer; cursor: hand;' target='_new' >".$r['nom_site']."</a><br/>";
		}
		
		return $valeur;
	}

  function GetCommuneId($id=-1){

	if($id==-1)
		$id=$this->id;
	
	//récupère le granulat parent
	$sql = "SELECT r.descriptif, rp.descriptif rpdesc, rp.id_rubrique
		FROM spip_rubriques r
			INNER JOIN spip_rubriques rp ON rp.id_rubrique = r.id_parent
		WHERE r.id_rubrique = ".$id;
		//echo "GetCommuneId ".$sql."<br/>";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
		$req = $DB->query($sql);
		$DB->close();
		while ($r = $DB->fetch_assoc($requete))
		{
			if(strstr($r["descriptif"], 'Code INSEE :')!="")
				return $id;
			if(strstr($r["rpdesc"], 'Code INSEE :')!="")
				return $r["id_rubrique"];
			//une rubrique commune doit avoir une grille commune
			$cTitre = $this->GetValeurForm($this->site->infos["GRILLE_Commune"], 'Titre',"","","",$r["id_rubrique"]);
			//echo "GetCommuneId ".$cTitre."<br/>";
			if($cTitre!="")
				return $r["id_rubrique"];
			else
				$this->GetCommuneId($r["id_rubrique"]);
		}
  
  }

	
  function GetCommune($champ="titre",$complet=false){
	
		//récupère la commune du granulat
		$sql = "SELECT rCom.titre, rCom.id_rubrique
			FROM spip_rubriques r, spip_rubriques rCom
			WHERE r.id_rubrique = ".$this->id."
				AND rCom.descriptif LIKE '%".substr($this->descriptif, 0, 5)."%'";
		//echo $sql."<br/>";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
		$req = $DB->query($sql);
		$DB->close();
		$r = $DB->fetch_assoc($req);
		
		if($complet)
			return $r;
		else
			return $r[$champ];
		
	}

	public function TronqueTexte($max_caracteres , $space='1' , $points='1',$tocut="")
	{
		if($tocut=="")
			$tocut = $this->texte;
		

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
		
	public function GetScope($id=-1)
	{
		if($id==-1)
			$id = $this->id;

		$Scope = $this->GetEnfantIds();
		$Scope = str_replace(DELIM,",",$Scope);
		$Scope .= $id;
		
		return $Scope;
		
	}


	public function EstParent($id)
	{
		$arrParent = split("[".DELIM."]", $this->GetParentIds());
		//echo "<br/>EstParent ".$id."<br/>";
		//print_r($arrParent);		
		return in_array($id, $arrParent);
	}

	public function GetParentIds($id = "")
	{
		if($id =="")
			$id = $this->id;
			
		//récupère les sous thème
		$sql = "SELECT id_rubrique, titre, r.id_parent
			FROM spip_rubriques r
			WHERE r.id_rubrique = ".$id;
	
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
		$req = $DB->query($sql);
		$DB->close();

		$valeur="";
		while($r = $DB->fetch_assoc($req)) {
			$valeur .= $this->GetParentIds($r['id_parent']);
			$valeur .= $r['id_rubrique'].DELIM;
		}
		
		return $valeur;

	}

	public function GetEnfantIds($id = "")
	{
		if($id =="")
			$id = $this->id;

		//récupère les sous thème
		$sql = "SELECT id_rubrique, titre
			FROM spip_rubriques r
			WHERE r.id_parent = ".$id;
		//echo $this->site->infos["SQL_LOGIN"]."<br/>";
	
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
		$req = $DB->query($sql);
		$DB->close();

		$valeur="";
		while($r = $DB->fetch_assoc($req)) {
			$valeur .= $this->GetEnfantIds($r['id_rubrique']);
			$valeur .= $r['id_rubrique'].DELIM;
		}
		
		return $valeur;

	}

	public function GetProps()
	{
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
		$DB->connect();
		//charge les propiétés du granulat
		$sql = "SELECT r.titre rtitre, r.id_rubrique, r.descriptif, r.texte
				, rp.titre rptitre, rp.id_rubrique rpid
				, a.texte atexte, a.chapo , a.descriptif adesc, a.ps, a.extra, a.date
			FROM spip_rubriques r
				LEFT JOIN spip_articles a ON a.id_rubrique = r.id_rubrique AND a.statut = 'publie'
				LEFT JOIN spip_rubriques rp ON rp.id_rubrique = r.id_parent
			WHERE r.id_rubrique = ".$this->id
			." ORDER BY a.date DESC";
		//echo $sql."<br/>";
		$req = $DB->query($sql);
		$DB->close();
		$data = $DB->fetch_assoc($req);
		$this->titre = $data['rtitre'];
		$this->descriptif = $data['descriptif'];
		$this->texte = $data['texte'];
		$this->localisation = "";
		$this->TitreParent = $data['rptitre'];
		$this->IdParent = $data['rpid'];
		$this->adesc= $data['adesc'];
		$this->adate= $data['date'];
		$this->atexte= $data['atexte'];
		$this->achapo= $data['chapo'];
		$this->ps= $data['ps'];

	}
	
	function GetOaiSet(){

		$sql = "SELECT CONCAT( '".$this->site->id."', 'site', ':', r.id_secteur, 'sect', ':', r.id_parent, 'rubpar', ':', r.id_rubrique, 'rub' ) setSpec
			FROM spip_rubriques r
			WHERE id_rubrique = ".$this->id;
		//echo $sql;
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
		$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		$r = $DB->fetch_assoc($req);
		$val = $r['setSpec'];
		
		return $val;
	
	}

	public function GetValeurForm($form, $champ, $valdefaut="", $sep="", $deb="", $id=-1)
	{
		if($id==-1)
			$id=$this->id;

		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
		$DB->connect();
	
		//récupère les sous thème
		$sql = "SELECT dc.valeur
			FROM spip_articles a
				INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
				INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee
				INNER JOIN spip_forms_donnees_champs dc ON dc.id_donnee = da.id_donnee
				INNER JOIN spip_forms_champs fc ON fc.champ = dc.champ
			WHERE a.id_rubrique =".$id."
				AND fd.id_form =".$form."
				AND fc.titre ='".$champ."'";
		//echo $this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";

		$req = $DB->query($sql);
		$DB->close();

		$valeur=$valdefaut;
		while($r = $DB->fetch_assoc($req)) {
			if($r['valeur']!="")
				$valeur=$deb.$r['valeur'].$sep;
		}
		
		return $valeur;
	}

	public function GetGrille($IdGrille, $ExtraSql="")
	{
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
		$DB->connect();
	
		//récupère les sous thème
		$sql = "SELECT dc.valeur, dc.champ, da.id_donnee, fc.titre
			FROM spip_articles a
				INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
				INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee AND fd.id_form =".$IdGrille."
				INNER JOIN spip_forms_donnees_champs dc ON dc.id_donnee = da.id_donnee
				INNER JOIN spip_forms_champs fc ON fc.champ = dc.champ AND fc.id_form =".$IdGrille."
			WHERE a.id_rubrique =".$this->id.$ExtraSql."
			ORDER BY da.id_donnee, dc.champ";
		//echo $this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
	
		$req = $DB->query($sql);
		$DB->close();

		return $req;
	}
	

	public function GetDocs()
	{
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
		$DB->connect();
		//charge les documents du granulat
		$sql = "SELECT r.titre rtitre, r.id_rubrique, r.descriptif
				, d.fichier, d.hauteur, d.largeur, d.id_document, d.id_type, d.titre dtitre
			FROM spip_rubriques r
				INNER JOIN spip_documents_rubriques dr ON dr.id_rubrique = r.id_rubrique
				INNER JOIN spip_documents d ON d.id_document = dr.id_document
			WHERE r.id_rubrique = ".$this->id
			." ORDER by d.id_type";
		$req = $DB->query($sql);
		$DB->close();
		$i = 0;
		while($data = $DB->fetch_assoc($req)) {
			$this->arrDoc[$i] = new Document($this->site, $data);
			$i ++;
		}
	}

	public function GetEnfants()
	{
		$sql = "SELECT id_rubrique, titre
			FROM spip_rubriques
			WHERE id_parent = ".$this->id
			." ORDER BY titre";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
		$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		$i = 0;
		while($data = $DB->fetch_assoc($req)) {
			$arrliste[$i] = new Granulat($data['id_rubrique'], $this->site);
			$i ++;
		}
		return $arrliste;
	}

	public function GetListeEnfants()
	{
		$sql = "SELECT id_rubrique, titre, descriptif
			FROM spip_rubriques
			WHERE id_parent = ".$this->id
			." ORDER BY titre";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $DB_OPTIONS);
		$DB->connect();
		$req = $DB->query($sql);
		$i = 0;
		while($data = $DB->fetch_assoc($req)) {
			$arrliste[$i] = array("id"=>$data['id_rubrique'], "titre"=>$data['titre'], "descriptif"=>$data['descriptif']);
			$i ++;
		}
		$DB->close();
	
		return $arrliste;
	}

	public function GetImages($Largeur, $Hauteur, $vignette=false)
	{
		$GranulatTofs = "";
		$FicLastTof = "";
		if($this->arrDoc){ 
			foreach ($this->arrDoc as $Doc) {
				//print_r($Doc);
				if($Doc->type == 10 && !$vignette){
					//if($FicLastTof=="")
					//	$FicLastTof="http://91.121.20.191/new/img/LogoCRMorbihan.jpg";
					$GranulatTofs = "<object type='application/x-shockwave-flash' 
							data='admin/includes/player_flv.swf' 
							width='".$Largeur."' height='".$Hauteur."' >
						<param name='movie' value='".$_SERVER["DOCUMENT_ROOT"]."/new/admin/includes/player_flv.swf'>
						<param name='FlashVars' value='flv=".$Doc->fichier."&amp;width=".$Largeur."&amp;height=".$Hauteur."&amp;bgcolor1=ffffff&amp;bgcolor2=ffffff&amp;buttoncolor=999999&amp;buttonovercolor=0&amp;slidercolor1=cccccc&amp;slidercolor2=999999&amp;sliderovercolor=666666&amp;textcolor=0&amp;showstop=1&amp;title=&amp;startimage=".$FicLastTof."'>
						<param name='wmode' value='opaque'>
						<span><a href='".$Doc->fichier."' rel='enclosure'>".$Doc->fichier."</a></span>
					</object>";
				}else{
					if($vignette)
						$GranulatTofs .= $Doc->GetVignette($Largeur, $Hauteur);
					else
						$GranulatTofs .= $Doc->DimensionImage($Largeur, $Hauteur);
					$FicLastTof = $Doc->fichier;
				}
			}
		}
		return $GranulatTofs;
	}

	public function GetLogo()
	{
		return 'GetLogo<br/>';
	}


}


?>