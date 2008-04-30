<?php

Class Synchro{
	public $trace;
	private $siteSrc;
	private $siteDst;
	
	function __construct($siteSrc, $siteDst) {
		$this->trace = TRACE;
		$this->siteSrc = $siteSrc;
		$this->siteDst = $siteDst;
		
	}
	
	public function GetRub($idPar){
		if($this->trace)
			echo 'Synchro:GetRub:idPar='.$idPar.'<br/>';
		$g = new Granulat($idPar,$this->siteSrc);
		$arrEnfs = $g->GetEnfantIds().split(DELIM);
		foreach($arrEnfs as $Enf)
		{
			
			//echo $siteparent."=>".$type."<br/>";
			$valeur .=" ".$this->sites[$siteparent]["NOM"]." ";
				
		}
		
		return $this->xml->xpath($Xpath);
	}
	
	public function GetNew($titre,$idGroupe)
	{
		$id = $this->VerifExist($titre);
		if($id==-1){
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetNewMC']";
			if($this->trace)
				echo "MotClef:GetNew:Xpath=".$Xpath."<br/>";
			$Q = $this->site->XmlParam->GetElements($Xpath);
			$set = str_replace("-titre-", $this->site->GetSQLValueString($titre, "text"), $Q[0]->set);
			$set = str_replace("-idGroupe-", $idGroupe, $set);
			$sql = $Q[0]->insert.$set;
			if($this->trace)
				echo "MotClef:GetNew:sql=".$sql."<br/>";
			$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
			$db->connect();
			$db->query($sql);
			$id = mysql_insert_id();
			$db->close();
		}
		if($this->trace)
			echo "MotClef:GetNew:id=".$id."<br/>";
		return $id;
		
	}

	public function VerifExist($titre)
	{
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='VerifExistMC']";
		if($this->trace)
			echo "MotClef:VerifExist:Xpath=".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-titre-", $this->site->GetSQLValueString($titre, "text"), $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		if($this->trace)
			echo "MotClef:VerifExist:sql=".$sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		if ($r = $db->fetch_assoc($req)){
			$id = $r['id_mot']; 
		} else {
			$id=-1;
		}
		
		return $id;
		
	}
	
	public function Verif($idAuteur=6) {
		
		$sql = "SELECT id_rubrique, titre
		FROM spip_auteurs_rubriques
		ORDER BY titre
		WHERE id_rubrique = ".$idAuteur
		;//LIMIT 0 , 93";

		$DB = new mysql($siteDst->infos["SQL_HOST"], $siteDst->infos["SQL_LOGIN"], $siteDst->infos["SQL_PWD"], $siteDst->infos["SQL_DB"], $DB_OPTIONS);
		$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
	}
	
	public function Synchronise($siteSrc, $siteDst, $idAuteur=6, $type) {
		
		global $objSite;
		//global $objSiteSync; //Mundi
		
    	/*if($siteDst==-1)
			$siteDst=$objSite;*/
    	
		//récupère les rubriques de l'auteur
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetRubriquesAuteur']";
		if($this->trace)
			echo "Site:Synchronise2:Xpath=".$Xpath."<br/>";
		$Q = $siteDst->XmlParam->GetElements($Xpath);
		$where = str_replace("-idAuteur-", $idAuteur, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($siteDst->infos["SQL_HOST"], $siteDst->infos["SQL_LOGIN"], $siteDst->infos["SQL_PWD"], $siteDst->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		if($this->trace)
			echo "Site:Synchronise2:sql=".$sql."<br/>";
		
		// Création Xml
		if ($type == "export") $url = PathRoot."/param/synchroExport.xml";
		else $url = PathRoot."/param/synchroImport.xml";
		if($this->trace)
			echo $url;
			
		$dom = new DomDocument("1.0");
		$nouveauDocument = $dom->createElement("documents");
		$dom->appendChild($nouveauDocument);	
		
		while ($row =  $db->fetch_assoc($rows)) {
			if($this->trace)
				echo $row['id_rubrique'];
				
			$document = $dom->lastChild; //firstChild
						
			$this->GetChildren($row['id_rubrique'], $dom, $document);
				
			if ($this->trace) {
				//echo 'type : '.$type;
				echo $dom->saveXML();
			}
			$xmlSrc = $dom->save($url);
			
			//$xmlSrc = $dom->saveXML();
			//return $url;
		}
		return $url;
	}

	
  	function import($xmlSrc) {
  		
  		if($this->trace)
			echo "Synchro:ImportSynchro //récuparation de la définition des données ".$xmlSrc."<br/>";
		$xml = new XmlParam($xmlSrc, -1);	
		
		//$xml = simplexml_load_string($xmlSrc);
		
		$Xpath = "/documents/rubrique";
		
		$nodesPrincipaux = $xml->GetElements($Xpath);
		//$k = 0;
		foreach($nodesPrincipaux as $node) {
			$idRub = $node['id'];
			if($this->trace)
				echo "Synchro:ImportSynchro:idRub ".$idRub."<br/>";
				
			//$Xpath = $Xpath."/rubrique";
			//$rubriques = $xml->GetElements($Xpath);
			$rubriques = $node->rubrique;

			$g = new Granulat($idRub, $this->siteSrc); 
			
			// Si un article est déjà présent pour une rubrique principale, on n'écrase pas cet article
			if (!$node->article) {

				$article = $node->article;
				$donnees = $article->donnees;
	  			$idGrille = $donnees->grille;
	  			
	  			$idAuteur = $article->auteur;
	  			$champs = $donnees->champs;
	  			$date = $article->date;
	  			$maj = $article->maj;
	  			
	  			$idArt = $g->SetNewArticleComplet(utf8_decode($article), $date, $maj);
	  			$g->AddAuteur($idArt, $idAuteur);	
	  			
	  			if($this->trace)
	  					print_r($donnees->donnee);
	  					
	  			foreach($donnees->donnee as $donnee){
	  				$j=0;
	  				if($this->trace)
	  					print_r($donnee->valeur);
	
	  				$idDon = $g->AddIdDonnee($idGrille, $idArt, $donnee->date, $donnee->maj);
					if($this->trace)
						echo "Granulat/AddXmlFile/- création de la donnee ".$idDon."<br/>";	
	  				
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
								echo "Granulat/AddXmlFile/-- récupération du type de champ ".$champ."<br/>";
								echo "Granulat/AddXmlFile/-- récupération de la valeur du champ ".$valeur."<br/>";
							}
							$row = array('champ'=>$champ, 'valeur'=>$valeur);
							
							$grille = new Grille($g->site);
							if($this->trace)
								echo "Granulat/AddXmlFile/--- création du champ <br/>";
							$grille->SetChamp($row, $idDon, false);
							
						}
						$j++;
					}
	  			}	
				
			}
			
			$i = 0;
			foreach($rubriques as $rubrique)
			{
				/**/
				//récuparation du granulat
				
				$idEnfant = $g->SetNewEnfant(utf8_decode($rubrique));
	  			$g->SetMotClef($rubrique->motclef, $idEnfant);
	  			   			
				$g->GetChildren($xml, $idEnfant, $rubriques[$i]->rubrique, $rubriques[$i]->article);
				$i++;
			}
		}
  	}

/*
 * Parcourt récursivement les enfants afin de créer l'objet dom de l'xml (correspondant à l'export)
 */
	public function GetChildren($idRub, $dom, $parent) {
		
		global $objSite;
		
		$gSrc = new Granulat($idRub,$objSite);
		//echo " ".$gSrc->GetMotClef()." ";
		
		$nouvelleRubrique = $dom->createElement("rubrique");
		$nouveauMotClef = $dom->createElement("motclef");

		$nomRubrique = $dom->createTextNode(utf8_encode($gSrc->titre));
		$nouvelleRubrique->setAttribute("id", $gSrc->id);
		$nouvelleRubrique->setAttribute("idParent", $gSrc->IdParent);
			
		$idMotClef = $dom->createTextNode($gSrc->GetMotClef());
			
		$nouvelleRubrique->appendChild($nomRubrique);
		$nouveauMotClef->appendChild($idMotClef);
		
		//$document = $dom->firstChild;
		$parent->appendChild($nouvelleRubrique);
	
			//echo $dom->saveXML();
			//$listeRubrique = $dom->getElementsByTagName('rubrique');
			//$Rub = $listeRubrique->item($index);
		$Rub = $parent->lastChild;
		$Rub->appendChild($nouveauMotClef);		
		
		$arrlisteArticle = $gSrc->GetArticleInfo("AND a.statut='prepa'");
		
		for ($k=0; $k<sizeof($arrlisteArticle); $k++) {
		
			$nouvellesDonnees = $dom->createElement("donnees");
			$nouvelleGrille = $dom->createElement("grille");
			$nouveauxChamps = $dom->createElement("champs");
			$nouvelArticle = $dom->createElement("article");
			$nouvelAuteur = $dom->createElement("auteur");
			$nouvelleDate = $dom->createElement("date");
			$nouvelleMaj = $dom->createElement("maj");
			
			$nouvelArticle->setAttribute("id", $arrlisteArticle[$k]['id']);
			$nomArticle = $dom->createTextNode(utf8_encode($arrlisteArticle[$k]['titre']));
			$dateArticle = $dom->createTextNode(utf8_encode($arrlisteArticle[$k]['date']));
			$majArticle = $dom->createTextNode(utf8_encode($arrlisteArticle[$k]['maj']));
			
			//echo ' ID FORM '.$gSrc->GetFormId($arrlisteArticle[0]['id']); 
			$idNumeroGrille = $gSrc->GetFormId($arrlisteArticle[$k]['id']);
			//echo ' ARTICLE '.$arrlisteArticle[$k]['id'];
			//echo ' GRILLE '.$idNumeroGrille;
			
			$idGrille = $dom->createTextNode($idNumeroGrille);
			
			$nouvelleGrille->appendChild($idGrille);
			$nouvelArticle->appendChild($nomArticle);
			
			$nouvelleDate->appendChild($dateArticle);
			$nouvelleMaj->appendChild($majArticle);
			
			$nomAuteur = $dom->createTextNode($gSrc->GetAuteurArticle($arrlisteArticle[$k]['id']));
			$nouvelAuteur->appendChild($nomAuteur);
			
			$nouvellesDonnees->appendChild($nouvelleGrille);
			$arrlisteGrilles = $gSrc->GetIdDonneesTable($idNumeroGrille, $arrlisteArticle[$k]['id']);
			
			$arrlisteDonnee = $gSrc->GetInfosDonnee($arrlisteGrilles[0]['id']);
			for ($j=0; $j<sizeof($arrlisteDonnee); $j++) {
				$nouveauChamp = $dom->createElement("champ");
				$nomChamp = $dom->createTextNode($arrlisteDonnee[$j]['champ']);
				$nouveauChamp->appendChild($nomChamp);	
				$nouveauxChamps->appendChild($nouveauChamp);
				$nouvellesDonnees->appendChild($nouveauxChamps);					
			}
						
			for ($i=0; $i<sizeof($arrlisteGrilles); $i++) {
				$arrlisteDonnee = $gSrc->GetInfosDonnee($arrlisteGrilles[$i]['id']);
				$nouvelleDonnee = $dom->createElement("donnee");
						/*for ($j=0; $j<sizeof($arrlisteDonnee); $j++) {
							echo 'Id champ '.$arrlisteDonnee[$j]['champ'];
						}*/
				$nouvelleDateDonnee = $dom->createElement("date");
				$nouvelleMajDonnee =$dom->createElement("maj");
				$dateDonnee =  $dom->createTextNode($arrlisteGrilles[$i]['date']);
				$majDonnee =  $dom->createTextNode($arrlisteGrilles[$i]['maj']);
				$nouvelleDateDonnee->appendChild($dateDonnee);
				$nouvelleMajDonnee->appendChild($majDonnee);
				
				$nouvelleDonnee->appendChild($nouvelleDateDonnee);
				$nouvelleDonnee->appendChild($nouvelleMajDonnee);
				
				for ($j=0; $j<sizeof($arrlisteDonnee); $j++) {
					$nouvelleValeur = $dom->createElement("valeur");
					$nomValeur = $dom->createTextNode(utf8_encode($arrlisteDonnee[$j]['valeur']));
					$nouvelleValeur->appendChild($nomValeur);	
					$nouvelleDonnee->appendChild($nouvelleValeur);	
										
				}
				$nouvellesDonnees->appendChild($nouvelleDonnee);		
			}		
			
			$nouvelArticle->appendChild($nouvelleDate);
			$nouvelArticle->appendChild($nouvelleMaj);
			$nouvelArticle->appendChild($nouvelAuteur);
			$nouvelArticle->appendChild($nouvellesDonnees);
			
			$Rub->appendChild($nouvelArticle);
		
		}
		
		//$document = $dom->firstChild;
		$parent->appendChild($Rub);
		
		$arrliste = $gSrc->GetListeEnfants();
		for ($i = 0; $i < sizeof($arrliste); $i++) {
			//echo " GG ".$arrliste[$i]['id'];
			$this->GetChildren($arrliste[$i]['id'], $dom, $Rub);
		}
	}
	
	public function GetXmlSrc() {
		
		$url = PathRoot."/param/synchro.xml";
		
	}
			
}
?>