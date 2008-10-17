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
  public $trace;
  
  public $site;
  

  	function __tostring() {
    return "Cette classe permet de d�finir et manipuler un granulat : .<br/>";
    }

  	function __construct($id, $site, $complet=true) {

    $this->trace = TRACE;
	if($this->trace)
	    echo "$id, $site <br/>";
	
    $this->id = $id;
    $this->site = $site;
	if($complet){
		$this->GetProps();
		$this->GetDocs();
	}
  }
  

	function GetListeEtatDiag($idDoc){
		
		if($this->trace)
	    	echo "Granulat:GetListeEtatDiag: id=$this->id idDoc=$idDoc<br/>";

		//r�cup�re les enfants
		$ids = $this->GetEnfantIds($this->id,",").$this->id;

		//construction du xml
		$grille = new Grille($this->site);
		$xul = $grille->GetEtatDiagListe($ids,$idDoc);
		
		return $xul;
		
	}


	function GetEtatDiag(){
		
		if($this->trace)
	    	echo "Granulat:GetEtatDiag: id= $this->id<br/>";

		//initialisation du xml
		$xml = "<EtatDiag idRub='".$this->id."'>";
	    	
		//r�cup�re les enfants
		$ids = $this->GetEnfantIds($this->id,",").$this->id;

		//construction du xml
		$grille = new Grille($this->site);
		$xml .= $grille->GetEtatDiagOui($ids);
		$xml .= $grille->GetEtatDiagHandi($ids,1);
		$xml .= $grille->GetEtatDiagHandi($ids,2);
		$xml .= $grille->GetEtatDiagHandi($ids,3);
		
				
		//finalisation du xml
		$xml .= "</EtatDiag>";
		
		return $xml;
		
	}
  
  
	function GetTreeChildren($type,$id=-1){

	    if($this->trace)
	    	echo "Granulat:GetTreeChildren: type = $type Cols = $Cols, id= $id<br/>";
		
		$Xpath = "/XmlParams/XmlParam[@nom='GetOntoTree']/Querys/Query[@fonction='GetTreeChildren_".$type."']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		if($id==-1){
			$id = $this->id;
		}
	
		$where = str_replace("-parent-", $id, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		
	    if($this->trace)
			echo "Granulat:GetTreeChildren:sql=".$sql."<br/>";

		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		
		if(mysql_num_rows($req)>0)
			return $req;
		else
			return false;

	}
  
 
/*
 * Parcourt r�cursivement les enfants afin de cr�er l'arborescence des rubriques et articles dans spip (correspondant � l'import)  
 */ 
  	function SetRubElements($xml, $idParent, $rubriques, $articles, $dom, $update) {
  		
  		//$rubriques = $xml->GetElements($Xpath);
  		/*if($this->trace)
			print_r($rubriques);
			print_r($articles);
		* */
  		$i = 0;
  		$g = new Granulat($idParent, $this->site); 
  		
  		foreach($articles as $article) {
  			
  			if ($g->VerifExistArticle($article['id'], $article['idRub'])==-1) { 
  				
	  			$nouvelArt = $dom->createElement("art");
				$nouvelArt->setAttribute("oldId", $article['id']);
	  			
	  			$donnees = $article->donnees;
	  			$idGrille = $donnees->grille;
	  			$idAuteur = $article->auteur;
	  			$champs = $donnees->champs;
	  			$date = $article->date;
	  			$maj = $article->maj;
	  			
	  			$idArt = $g->SetNewArticleComplet(utf8_decode($article), $date, $maj);
	  			if ($idAuteur!= "") $g->AddAuteur($idArt, $idAuteur);
	  			
	  			$nouvelArt->setAttribute("newId", $idArt);
	  			$nouvelArt->setAttribute("newRub", $g->id);
	  			
		  		$dom->lastChild->appendChild($nouvelArt);
	  			
	  			if($this->trace)
	  					print_r($donnees->donnee);
	  					
	  			foreach($donnees->donnee as $donnee){
	  				$j=0;
	  				if($this->trace)
	  					print_r($donnee->valeur);
	
	  				$idDon = $g->AddIdDonnee($idGrille, $idArt, $donnee->date, $donnee->maj);
					if($this->trace)
						echo "Granulat/AddXmlFile/- cr�ation de la donnee ".$idDon."<br/>";	
	  				
					foreach($donnee->valeur as $valeur) {
						if($valeur!='non'){
							$valeur=utf8_decode($valeur);
							$champ = $champs[0]->champ[$j];
							if($this->trace)
								echo "Granulat/AddXmlFile/--- gestion des champs multiples ".substr($champ,0,8)."<br/>";
							if(substr($champ,0,8)=="multiple"){
								$valeur=$champ;
							//attention il ne doit pas y avoir plus de 10 choix
								$champ=substr($champ,0,-2);
							}
							if($this->trace) {
								echo "Granulat/AddXmlFile/-- r�cup�ration du type de champ ".$champ."<br/>";
								echo "Granulat/AddXmlFile/-- r�cup�ration de la valeur du champ ".$valeur."<br/>";
							}
							$row = array('champ'=>$champ, 'valeur'=>$valeur);
							
							$grille = new Grille($g->site);
							if($this->trace)
								echo "Granulat/AddXmlFile/--- cr�ation du champ <br/>";
							$grille->SetChamp($row, $idDon, false);
							
						}
						$j++;
					}
	  			}
	  		if ($update) 
	  			$g->UpdateIdArt($idArt, $article['id'], $article['idRub']);
  			} 	
  		}
  		
  		foreach($rubriques as $rubrique) {
  			
  			if ($rubrique['idAdmin']!="") {
  				if ($update) {
  					$g->UpdateAdminRub($rubrique['id'], $rubrique['idAdmin']);
  				}
  			}
  			
  			if ($g->VerifExistRubrique($rubrique['id'], $rubrique['idParent'])==-1) {
	  			$nouvelleRub = $dom->createElement("rub");
				$nouvelleRub->setAttribute("oldId", $rubrique['id']);
	  			
	  			$idEnfant = $g->SetNewEnfant(utf8_decode($rubrique));
	  			$g->SetMotClef($rubrique->motclef, $idEnfant);
	  			
	  			$nouvelleRub->setAttribute("newId", $idEnfant);
	  			$nouvelleRub->setAttribute("parentId", $idParent);
		  		$dom->lastChild->appendChild($nouvelleRub);
		  		
		  		if ($update) {
		  			$g->UpdateIdRub($idEnfant, $rubrique['id'], $rubrique['idParent']);
		  		} else if ($rubrique['idAdmin']!="")	{
		  			$g->UpdateAdminRub($idEnfant, $rubrique['idAdmin']);
		  		}
  			} else $idEnfant = $rubrique['id'];
	  			
  			$g->SetRubElements($xml, $idEnfant, $rubrique->rubrique, $rubrique->article, $dom, $update);
  			//$i++;  //$rubriques[$i]->rubrique, $rubriques[$i]->article
  		}	
  	}
  	
  	/*
  	 * V�rifie l'existence d'une rubrique dans la table spip_rubriques, retourne -1 si la rubrique n'est pas trouv�e
  	 * 
  	 */
	public function VerifExistRubrique($idRub, $idParent) {
		
		$sql = "SELECT id_rubrique
				FROM spip_rubriques
				WHERE id_rubrique = ".$idRub." AND id_parent = ".$idParent;
		;//LIMIT 0 , 93";

		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
		if($data = $DB->fetch_assoc($req)) {
			return $data['id_rubrique'];
		} else return -1;
	}
	
	/*
  	 * V�rifie l'existence d'un article dans la table spip_articles, retourne -1 si l'article n'est pas trouv�
	 * 	 * 
	 */
	public function VerifExistArticle($idArt, $idRub) {
		
		$sql = "SELECT id_article
				FROM spip_articles
				WHERE id_article = ".$idArt." AND id_rubrique = ".$idRub;
		;//LIMIT 0 , 93";

		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
		if($data = $DB->fetch_assoc($req)) {
			return $data['id_article'];
		} else return -1;
	}
  	
		
	/*
	 * Met � jour les identifiants des rubriques dans les tables spip_rubriques, spip_mots_rubriques et spip_articles 
	 * 
	 */
	public function UpdateIdRub($idRubOld, $idRubNew, $idParent) {
		
		if($this->trace)
			echo "Synchro:UpdateIdRub:idRubNew ".$idRubNew;
		
		$sql = "UPDATE `spip_rubriques`
				SET id_rubrique = ".$idRubNew."
				WHERE id_rubrique = ".$idRubOld;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
		$sql = "UPDATE `spip_rubriques`
				SET id_parent = ".$idParent."
				WHERE id_rubrique = ".$idRubNew;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
		$sql = "UPDATE `spip_mots_rubriques`
				SET id_rubrique = ".$idRubNew."
				WHERE id_rubrique = ".$idRubOld;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
				
	}
	
	/*
	 * Met � jour les identifiants des articles dans les tables spip_articles, spip_forms_articles, spip_forms_donnees_articles et spip_auteurs_articles
	 * 
	 */
	public function UpdateIdArt($idArtOld, $idArtNew, $idRubNew) {
		
		if($this->trace)
			echo "Synchro:UpdateIdArt:idArtNew ".$idArtNew;
		
		$sql = "UPDATE `spip_articles`
				SET id_article = ".$idArtNew."
				WHERE id_article = ".$idArtOld;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
		$sql = "UPDATE `spip_forms_articles`
				SET id_article = ".$idArtNew."
				WHERE id_article = ".$idArtOld;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
		$sql = "UPDATE `spip_forms_donnees_articles`
				SET id_article = ".$idArtNew."
				WHERE id_article = ".$idArtOld;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
		$sql = "UPDATE `spip_auteurs_articles`
				SET id_article = ".$idArtNew."
				WHERE id_article = ".$idArtOld;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
		$sql = "UPDATE `spip_articles`
				SET id_rubrique = ".$idRubNew."
				WHERE id_article = ".$idArtNew;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
	}
	
	
	public function GetIdAdmin($idRub) {
		
		$sql = "SELECT id_auteur, id_rubrique FROM spip_auteurs_rubriques a WHERE a.id_rubrique=".$idRub;					
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		$idAuteur=-1;
		if ($r = $DB->fetch_assoc($req)){
			$idAuteur = $r['id_auteur']; 
		}
		return $idAuteur;
		
	}
	
  	function SetAuteur($newId,$objet){

	  	//pas de cr�ation d'auteur pour les rubriques
	  	if($objet=="rubrique")
	  		return;
	  		
	  	if($this->site->scope["login"]!=-1){
				//association de l'article � l'auteur
				$sql = "INSERT INTO spip_auteurs_".$objet."s (id_".$objet.",id_auteur)
					SELECT ".$newId.", id_auteur FROM spip_auteurs where login='".$this->site->scope["login"]."'";					
				$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
				$req = $DB->query($sql);
				$DB->close();
		}
  	
  	}
  
  	function AddAuteur($idArt, $idAuteur) {
  		$sql = "INSERT INTO spip_auteurs_articles (id_auteur, id_article) VALUES (".$idAuteur.", ".$idArt."	)"	;	
 		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
  	}
  	
  	function GetAuteurArticle($idArt) {
	  
		//association de l'article � l'auteur
		$sql = "SELECT id_auteur, id_article FROM spip_auteurs_articles a WHERE a.id_article=".$idArt;					
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		
		if ($r = $DB->fetch_assoc($req)){
			$idAuteur = $r['id_auteur']; 
		}
		return $idAuteur;
  	}
  
  	function SetNewEnfant($titre,$id=-1){

	if($id==-1)
		$id=$this->id;
	
	//ajoute un nouvel enfant
	$sql = "INSERT INTO spip_rubriques
		SET titre = ".$this->site->GetSQLValueString($titre, "text").", id_parent=".$id;
	
	$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
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
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$newId = mysql_insert_id();
		$DB->close();
	
		$this->SetAuteur($newId,'article');
		
		return $newId;
  
 	}

  	function SetNewArticleComplet($titre, $date, $maj, $id=-1) {
  		if($id==-1)
			$id=$this->id;
	
		//ajoute un nouvel enfant
		$sql = "INSERT INTO spip_articles
			SET titre = ".$this->site->GetSQLValueString($titre, "text")
				.", statut='prepa'
				, date ='".$date
				."', maj ='".$maj
				."', id_rubrique=".$id;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
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
	if ($id_mot != ""){
		$sql = "INSERT INTO spip_mots_rubriques
				SET id_mot = ".$id_mot.", id_rubrique=".$id;
			
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$newId = mysql_insert_id();
		$DB->close();
	} else $newId = -1;
	return $newId;
  
  }
  
  	function GetGeo($id=-1) {
		if($id==-1)
			$g = $this;
		else
			$g = new Granulat($id,$this->site);
		
			
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
			WHERE r.id_rubrique =".$g->id."
			GROUP BY r.id_rubrique
			LIMIT 0 , ".MaxMarker;
		//echo $sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
	
		$db->connect();
		$requete =  $db->query($sql);
		$db->close();
		$result['lat'] = $this->site->infos["DEF_LAT"];
		$result['lng'] = $this->site->infos["DEF_LNG"];
		$result['zoom'] = $this->site->infos["DEF_ZOOM"];
		$result['type'] = $this->site->infos["DEF_CARTE_TYPE"];
		$r =  $db->fetch_assoc($requete);
		//gestion de la localisation parente si localisation  null
		if(!$r['lat']){
			if($g->IdParent!=0)
				$result = $this->GetGeo($g->IdParent);
		}else {
			$result['lat'] = $r['lat'];
			$result['lng'] = $r['lng'];
			if($r['zoom'])
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
		
		return $result;
	}
  
	function GetArticle($extraSql=""){
		//r�cup�re pour la rubrique l'article ayant les condition de extra
		$sql = "SELECT a.id_article
			FROM spip_rubriques r
				INNER JOIN spip_articles a ON a.id_rubrique = r.id_rubrique  
			WHERE r.id_rubrique = ".$this->id." ".$extraSql;
		//echo $sql."<br/>";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		//v�rifie l'existance de l'article pour les forms
		if ($r = $DB->fetch_assoc($req)){
			$artId = $r['id_article']; 
		} else {
			//Cr�ation de l'article pour la rubrique
			$NomGrille = $this->site->GetSQLValueString($this->titre, "text");
			$sql = "INSERT INTO `spip_articles` (`titre`, id_rubrique, statut, date)
				VALUES (".$NomGrille.",".$this->id.",'prepa', now())";
			$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$req = $DB->query($sql);
			$artId = mysql_insert_id();
			$DB->close();
			$this->SetAuteur($artId,'article');
		}

		return $artId; 

	}
	
	/*
	 * Retourne un tableau contenant l'id de l'article, le titre, les dates de cr�ation et de mise � jour pour une rubrique
	 */
	function GetArticleInfo($extraSql=""){
		//r�cup�re pour la rubrique l'article ayant les condition de extra
		$sql = "SELECT a.id_article ,a.titre, a.date, a.maj, a.statut, aa.id_auteur
			FROM spip_articles a 
				LEFT JOIN spip_auteurs_articles aa ON aa.id_article = a.id_article 	
			WHERE a.id_rubrique = ".$this->id." ".$extraSql."
				";
		//echo $sql."<br/>";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();

		return $req; 
			
	}
  
	/*
	 * Retourne les id de grille pour un article
	 */
	function GetFormIds($idArticle) {
		
		$sql = "SELECT DISTINCT fd.id_form
			FROM spip_forms_donnees_articles fa
				INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fa.id_donnee
			WHERE fa.id_article = ".$idArticle;
		//echo $sql."<br/>"; spip_forms_articles
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		return $req; 	
	}
	
	/*
	 * Retourne l'ensemble des id de donn�es d'une grille donn�e pour un article 
	 */
	function GetIdDonnees($idGrille, $idArticle) {
		
		$sql = "SELECT fd.id_donnee, fd.date, fd.maj
				FROM spip_forms_donnees_articles da 
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee AND fd.id_form = ".$idGrille."
				WHERE da.id_article = ".$idArticle;
			
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		
		return $req;	
	}
	
	/*
	 * Retourne le tableau contenant l'id, le champ, la valeur et la date de mise � jour d'une donn�e
	 */
	function GetInfosDonnee($idDonnee) {
		
		$sql = "SELECT fdc.id_donnee, fdc.champ, fdc.valeur, fdc.maj
					,fc.titre
				FROM spip_forms_donnees_champs fdc
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = fdc.id_donnee
					INNER JOIN spip_forms_champs fc ON fc.champ = fdc.champ AND fc.id_form = fd.id_form
				WHERE fdc.id_donnee =".$idDonnee;
		
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		
		return $req;	
		
	}
	
	function GetIdDonnee($formId, $artId=-1, $doublon=false){

		if($artId==-1)
			$artId = $this->GetArticle();
		
		$donId = false;
		
		if(!$doublon){
			//v�rifie l'existence de la donnee
	  		$sql = "SELECT fd.id_donnee
				FROM spip_forms_donnees_articles da 
					INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee AND fd.id_form = ".$formId."
			WHERE da.id_article = ".$artId;
			//echo $sql."<br/>";
			$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$req = $DB->query($sql);
			$DB->close();
			$r = $DB->fetch_assoc($req);
			$donId= $r['id_donnee'];
		}
		
		if(!$donId){
			//attache le form � l'article
			$sql = "INSERT INTO `spip_forms_articles` (id_form, id_article)
				VALUES (".$formId.",".$artId.")";
			$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$req = $DB->query($sql);
			$DB->close();
			//cr�ation de la donn�e du formulaire
			$sql = "INSERT INTO `spip_forms_donnees` (`id_form`, `date`,`confirmation`, `statut`, `rang`)
				VALUES (".$formId.", now(), 'valide', 'prop', 1)";
			$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$req = $DB->query($sql);
			$donId = mysql_insert_id();
			$DB->close();
			//attache la donn�e � l'article
			$sql = "INSERT INTO `spip_forms_donnees_articles` (`id_donnee`, `id_article`)
				VALUES (".$donId.", ".$artId.")";
			$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$req = $DB->query($sql);
			$DB->close();
			//echo "-- cr�ation de la donn�e ".$donId." \n<br/>"; 
		}

		return $donId;

	}
  
	function AddIdDonnee($formId, $artId=-1, $date, $maj) {
		
		if($artId==-1)
			$artId = $this->GetArticle();

		//attache le form � l'article
		/*$sql = "INSERT INTO `spip_forms_articles` (id_form, id_article)
				VALUES (".$formId.",".$artId.")";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();*/
			
		//cr�ation de la donn�e du formulaire
		$sql = "INSERT INTO `spip_forms_donnees` (`id_form`, `date`, `maj`, `confirmation`, `statut`, `rang`)
				VALUES (".$formId.", '".$date."', '".$maj."', 'valide', 'prop', 1)";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$donId = mysql_insert_id();
		$DB->close();
		//attache la donn�e � l'article
		$sql = "INSERT INTO `spip_forms_donnees_articles` (`id_donnee`, `id_article`)
				VALUES (".$donId.", ".$artId.")";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		//echo "-- cr�ation de la donn�e ".$donId." \n<br/>"; 
		
		return $donId;
	}
	
	function GetLiens($rReq=false){
	
		//r�cup�re la commune du granulat
		$sql = "SELECT nom_site, url_site
			FROM `spip_syndic`
			WHERE statut = 'publie' AND id_rubrique =".$this->id;
		//echo $sql."<br/>";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
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
			
		//r�cup�re les sous th�me
		$sql = "SELECT id_rubrique, titre, r.id_parent
			FROM spip_rubriques r
			WHERE r.id_rubrique = ".$id;
	
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();

		$valeur="";
		while($r = $DB->fetch_assoc($req)) {
			$valeur .= $this->GetParentIds($r['id_parent']);
			$valeur .= $r['id_rubrique'].DELIM;
		}
		
		return $valeur;

	}

	public function GetEnfantIds($id = "", $sep="")
	{
		if($id =="")
			$id = $this->id;
		if($sep=="")
			$sep=DELIM;

		//r�cup�re les sous th�me
		$sql = "SELECT id_rubrique, titre
			FROM spip_rubriques r
			WHERE r.id_parent = ".$id;
		//echo $this->site->infos["SQL_LOGIN"]."<br/>";
	
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();

		$valeur="";
		while($r = $DB->fetch_assoc($req)) {
			$valeur .= $this->GetEnfantIds($r['id_rubrique'],$sep);
			$valeur .= $r['id_rubrique'].$sep;
		}
		
		return $valeur;

	}

	public function GetProps()
	{
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
		if($this->trace)
			echo "//charge les propi�t�s du granulat $this->id -<br/>";
		$sql = "SELECT r.titre rtitre, r.id_rubrique, r.descriptif, r.texte, r.id_parent rpid
				, rp.titre rptitre
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
	
	public function GetValeurForm($form, $champ, $valdefaut="", $sep="", $deb="", $id=-1)
	{
		if($id==-1)
			$id=$this->id;

		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
	
		//r�cup�re les sous th�me
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
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
	
		//r�cup�re les sous th�me
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
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
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
		return $this->arrDoc;
	}
	
	public function GetArtDocs($idArt)
	{
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
		//charge les documents du granulat
		$sql = "SELECT d.fichier, d.hauteur, d.largeur, d.id_document, d.id_type, d.titre dtitre
			FROM spip_documents_articles da 
				INNER JOIN spip_documents d ON d.id_document = da.id_document
			WHERE da.id_article = ".$idArt
			." ORDER by d.id_type";
		$req = $DB->query($sql);
		$DB->close();
		$i = 0;
		$arrDoc=array();
		while($data = $DB->fetch_assoc($req)) {
			$arrDoc[$i] = new Document($this->site, $data);
			$i ++;
		}
		return $arrDoc;
	}
	
	public function GetEnfants()
	{
		$sql = "SELECT id_rubrique, titre
			FROM spip_rubriques
			WHERE id_parent = ".$this->id
			." ORDER BY titre";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
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

	/*
	 * Retourne un tableau des enfants d'une rubrique contenant l'id, le titre et le descriptif des rubriques
	 */
	public function GetListeEnfants()
	{
		$sql = "SELECT id_rubrique, titre, descriptif
			FROM spip_rubriques
			WHERE id_parent = ".$this->id
			." ORDER BY titre";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
		$req = $DB->query($sql);
		$i = 0;
		$DB->close();
		$arrliste = array();
		while($data = $DB->fetch_assoc($req)) {
			$arrliste[$i] = array("id"=>$data['id_rubrique'], "titre"=>$data['titre'], "descriptif"=>$data['descriptif']);
			$i ++;
		}
	
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

	public function GetMotClef() {
		//r�cup�re lid du granulat
		$sql = "SELECT id_mot, id_rubrique
			FROM `spip_mots_rubriques`
			WHERE id_rubrique =".$this->id;
		//echo $sql."<br/>";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
				
		$valeur="";
		while($r = $DB->fetch_assoc($req)) {
			$valeur .= $r['id_mot'].DELIM;
		}
		//enl�ve le dernier d�lmiteur
		$valeur = substr($valeur,0,-1);
		return $valeur;
	}

	public function GetTypeMotClef($type,$id=-1) {
		if($id==-1)
			$id=$this->id;
		//r�cup�re lid du granulat
		$sql = "SELECT id_mot
			FROM spip_mots_".$type."s
			WHERE id_".$type." =".$id;
		//echo $sql."<br/>";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
				
		$valeur=array();
		$i=0;
		while($r = $DB->fetch_assoc($req)) {
			$m = new MotClef($r['id_mot'],$this->site);
			$valeur[$i] = $m;
		}
		return $valeur;
}
	
	public function GetTypeAuteur($type,$id=-1) {
		if($id==-1)
			$id=$this->id;
		//r�cup�re lid du granulat
		$sql = "SELECT a.id_auteur, a.nom, a.login
			FROM spip_auteurs a 
				INNER JOIN spip_auteurs_".$type."s at ON at.id_auteur = a.id_auteur
			WHERE at.id_".$type." =".$id;
		//echo $sql."<br/>";
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
				
		return $req;
}

	public function GetParent($id = "") {
		
		if($id =="")
			$id = $this->id;
			
		//r�cup�re les sous th�me
		$sql = "SELECT id_rubrique, titre, r.id_parent
			FROM spip_rubriques r
			WHERE r.id_rubrique = ".$id;
	
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		
		if($r = $DB->fetch_assoc($req)) {
			return $r['id_parent'];
		} else return -1;
	}
	
	public function CopyRub($idParent) {
		
		//$idParent = $this->GetParent($this->id);
		if($this->trace) echo "Granulat/copy/- idParent ".$idParent."<br/>";
		$arrListeEnfants = $this->GetEnfants();
		//$idArticle = $this->GetArticle();
		//if($this->trace) echo "Granulat/copy/- arrListeEnfants ".print_r($arrListeEnfants)."<br/>";
		
		$motclef = $this->GetMotClef();
		
		$arrListeInfoArticle = $this->GetArticleInfo();
		
		$g = new Granulat($idParent, $this->site);
		$idEnfant = $g->SetNewEnfant($this->titre);
		$gra = new Granulat($idEnfant, $this->site);
		$gra->descriptif = $this->descriptif;
		$gra->texte = $this->texte;
		if ($motclef!="") $gra->SetMotClef($motclef);
		
		if ($arrListeInfoArticle != null) {
			if($this->trace) echo "Granulat/copy/- arrListeInfoArticle ".print_r($arrListeInfoArticle)."<br/>";
			
			foreach($arrListeInfoArticle as $article) {
				$idArt = $gra->SetNewArticleComplet($article['titre'], $article['date'], $article['maj']);
				$idGrille = $gra->GetFormId($article['id']);
				if($this->trace) echo "Granulat/copy/- idGrille ".$idGrille."<br/>";
				$arrListeDonnees = $gra->GetIdDonneesTable($idGrille, $article['id']);
				foreach($arrListeDonnees as $donnee) {
		  			$idDon = $gra->AddIdDonnee($idGrille, $idArt, $donnee['date'], $donnee['maj']);
					if($this->trace)
						echo "Granulat/copy/- cr�ation de la donnee ".$idDon."<br/>";	
		  			
					$arrListeDonneeInfos = $gra->GetInfosDonnee($donnee['id']);
					foreach($arrListeDonneeInfos as $Donnee) {
						if($Donnee['valeur']!='non'){
							$valeur=$Donnee['valeur'];
							$champ = $Donnee['champ'];
							if($this->trace)
								echo "Granulat/copy/--- gestion des champs multiples ".substr($champ,0,8)."<br/>";
							if(substr($champ,0,8)=="multiple"){
								$valeur=$champ;
							//attention il ne doit pas y avoir plus de 10 choix
								$champ=substr($champ,0,-2);
							}
							if($this->trace) {
								echo "Granulat/copy/-- r�cup�ration du type de champ ".$champ."<br/>";
								echo "Granulat/copy/-- r�cup�ration de la valeur du champ ".$valeur."<br/>";
							}
							$row = array('champ'=>$champ, 'valeur'=>$valeur);
							
							$grille = new Grille($gra->site);
							if($this->trace)
								echo "Granulat/copy/--- cr�ation du champ <br/>";
							$grille->SetChamp($row, $idDon, false);
						}
					}
				}	
			}
		}
		
		if ($arrListeEnfants != null) {
			foreach($arrListeEnfants as $granulat) {
				$granulat->CopyRub($idEnfant);
			}
		}		
	}
	
}

?>