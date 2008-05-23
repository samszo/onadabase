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
	
	/*
	 * Parcourt un fichier xml afin de mettre à jour les identifiants des rubriques et articles
	 * 
	 */
	public function Actualise($xmlString) {
		
		$doc = new DOMDocument();
		$doc->loadXML($xmlString);
		
		//$xml = new XmlParam(-1, $xmlString);	
				
		$XpathRub = "rub";
		$XpathArt = "art";
		
		$nodesPrincipaux = $doc->getElementsByTagName($XpathRub);
		
		$g = new Granulat(0, $this->siteSrc);
		
		foreach($nodesPrincipaux as $node) {
			$idRubOld = $node->getAttribute('oldId');
			$idRubNew = $node->getAttribute('newId');
			$idRubParent = $node->getAttribute('parentId');
			if($this->trace) {
				echo "Synchro:Actualise:idRubOld ".$idRubOld."<br/>";
				echo "Synchro:Actualise:idRubNew ".$idRubNew."<br/>";
				echo "Synchro:Actualise:idRubParent ".$idRubParent."<br/>";
			}	
			$g->UpdateIdRub($idRubOld, $idRubNew, $idRubParent);
		}
		
		$nodesPrincipaux = $doc->getElementsByTagName($XpathArt);
		
		foreach($nodesPrincipaux as $node) {
			$idArtOld = $node->getAttribute('oldId');
			$idArtNew = $node->getAttribute('newId');
			$idArtRub = $node->getAttribute('newRub');
			if($this->trace) {
				echo "Synchro:Actualise:idArtOld ".$idArtOld."<br/>";
				echo "Synchro:Actualise:idArtNew ".$idArtNew."<br/>";
				echo "Synchro:Actualise:idArtRub ".$idArtRub."<br/>";
			}	
			$g->UpdateIdArt($idArtOld, $idArtNew, $idArtRub);
		}
		$path = PathRoot."/param/synchroImport.xml";
		$xmlScr = $doc->save($path);
		//$this->import($path);
		return $path;
		
	}
	
	/*
	 * Enregistre le contenu de fichier xml de l'import dans la table spip_synchro_historique
	 * 
	 */
	public function AddHistoriqueSynchro($xmlSrc, $idAuteur) {
		
		$doc = new DOMDocument();
		$doc->load($xmlSrc);
		//echo $doc->saveXML();
		
		$src = $doc->saveXML();
		$sql = "INSERT INTO `spip_synchro_historique` (`id_auteur`, `synchro_xml`)
				VALUES (".$idAuteur.", ".$this->siteSrc->GetSQLValueString($src, "text").")";
		//print_r("siteSrc ".$this->siteSrc);
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"], $DB_OPTIONS);
		//$DB->connect();
		$req = $DB->query($sql);
		
		$DB->close();
		if($this->trace)
			echo "Synchro:AddHistoriqueSynchro // Termine";
	}
	
	/*
	 * Génére un fichier xml des rubriques administrées, retourne le chemin vers ce fichier
	 * 
	 */
	public function Synchronise($siteSrc, $siteDst, $idAuteur=6) {	
		global $objSite;
		//global $objSiteSync; //Mundi
    	
		//récupère les rubriques de l'auteur
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetRubriquesAuteur']";
		if($this->trace)
			echo "Synchro:Synchronise:Xpath=".$Xpath."<BR/>";
		$Q = $siteDst->XmlParam->GetElements($Xpath);
		$where = str_replace("-idAuteur-", $idAuteur, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($siteDst->infos["SQL_HOST"], $siteDst->infos["SQL_LOGIN"], $siteDst->infos["SQL_PWD"], $siteDst->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		if($this->trace)
			echo "Synchro:Synchronise:sql=".$sql."<BR/>";
		
		$url = PathRoot."/param/synchroExport.xml";
		
		if($this->trace)
			echo "Synchro:Synchronise:url // Création Xml ".$url."<BR/>";
			
		$dom = new DomDocument("1.0");
		$nouveauDocument = $dom->createElement("documents");
		$dom->appendChild($nouveauDocument);	
		$dom->save($url);	
		
		while ($row =  $db->fetch_assoc($rows)) {
			if($this->trace)
				echo "Synchro:Synchronise:id_rubrique ".$row['id_rubrique']."<BR/>";
				
			$document = $dom->lastChild; //firstChild
			
			$this->GetChildren($row['id_rubrique'], $dom, $document);
		}
		$xmlSrc = $dom->save($url);	
		if ($this->trace) {
			echo "Synchro:Synchronise:XML Tree ".$dom->saveXML()."<BR/>";
		}
		
		return $url;
	}

	/*
	 * Permet d'importer dans la base rubriques et articles à partir d'un fichier xml, 
	 * génére aussi de l'xml pour la mise à jour des identifiants des rubriques et articles
	 * 
	 */
  	function import($xmlSrc) {
  		
  		$dom = new DOMDocument("1.0");
		$nouvelleRacine = $dom->createElement("documents");
		$dom->appendChild($nouvelleRacine);	
		$racine = $dom->lastChild;
  		
  		if($this->trace)
			echo "Synchro:import //récuparation de la définition des données ".$xmlSrc."<br/>";
		$xml = new XmlParam($xmlSrc, -1);	
		
		$Xpath = "/documents/rubrique";
		
		$nodesPrincipaux = $xml->GetElements($Xpath);
		
		if ($nodesPrincipaux!=false) {
			foreach($nodesPrincipaux as $node) {
				$idRub = $node['id'];
				$idParent = $node['idParent'];
				$idAdmin = $node['idAdmin'];
			
				if($this->trace)
					echo "Synchro:import:idRub ".$idRub." idParent ".$idParent." idAdmin ".$idAdmin."<br/>";

				if ($idAdmin !="") $this->UpdateAdminRub($idRub, $idAdmin);
			
				$rubriques = $node->rubrique;

				$g = new Granulat($idRub, $this->siteSrc); 
			
				if ($g->VerifExistRubrique($idRub, $idParent)==-1) {
					$gra = new Granulat($idParent, $this->siteSrc); 
					$idEnfant = $gra->SetNewEnfant(utf8_decode($node));
	  				$gra->SetMotClef($node->motclef, $idEnfant);
	  				$gra->UpdateIdRub($idEnfant, $idRub, $idParent);
				}
			
				// Si un article est déjà présent pour une rubrique principale, on n'écrase pas cet article
				if ($node->article) {
					if ($g->VerifExistArticle($node->article["id"], $node->$article['idRub'])==-1) {
		
						$nouvelArt = $dom->createElement("art");
						$nouvelArt->setAttribute("oldId", $article['id']);
					
						$article = $node->article;
						$donnees = $article->donnees;
			  			$idGrille = $donnees->grille;
			  			
			  			$idAuteur = $article->auteur;
			  			$champs = $donnees->champs;
			  			$date = $article->date;
			  			$maj = $article->maj;
			  			
			  			$idArt = $g->SetNewArticleComplet(utf8_decode($article), $date, $maj);
			  			if($idAuteur!="") $g->AddAuteur($idArt, $idAuteur);	
			  			
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
								echo "Synchro/import - création de la donnee ".$idDon."<br/>";	
			  				
							foreach($donnee->valeur as $valeur) {
								if($valeur!='non'){
									$valeur=utf8_decode($valeur);
									$champ = $champs[0]->champ[$j];
									if($this->trace)
										echo "Synchro/import --- gestion des champs multiples ".substr($champ,0,8)."<br/>";
									if(substr($champ,0,8)=="multiple"){
										$valeur=$champ;
									//attention il ne doit pas y avoir plus de 10 choix
										$champ=substr($champ,0,-2);
									}
									if($this->trace) {
										echo "Synchro/import -- récupération du type de champ ".$champ."<br/>";
										echo "Synchro/import -- récupération de la valeur du champ ".$valeur."<br/>";
									}
									$row = array('champ'=>$champ, 'valeur'=>$valeur);
									
									$grille = new Grille($this->siteSrc);
									if($this->trace)
										echo "Synchro/import --- création du champ <br/>";
									$grille->SetChamp($row, $idDon, false);
								}
								$j++;
							}
			  			}
			  		$g->UpdateIdArt($idArt, $node->article["id"], $node->article["idRub"]);		
					}
				}
					
				foreach($rubriques as $rubrique) {
					//récuparation du granulat
					
					if ($g->VerifExistRubrique($rubrique['id'], $rubrique['idParent'])==-1) {
						$nouvelleRub = $dom->createElement("rub");
						$nouvelleRub->setAttribute("oldId", $rubrique['id']);
						
						$idEnfant = $g->SetNewEnfant(utf8_decode($rubrique));
	  					$g->SetMotClef($rubrique->motclef, $idEnfant);
	  					
	  					$nouvelleRub->setAttribute("newId", $idEnfant);
	  					$nouvelleRub->setAttribute("parentId", $idRub);
	  					$racine->appendChild($nouvelleRub);
	  					
	  					$g->UpdateIdRub($idEnfant, $rubrique['id'], $rubrique['idParent']);
	  					
					} else $idEnfant = $rubrique['id'];
					
					$g->GetChildren($xml, $idEnfant, $rubrique->rubrique, $rubrique->article, $dom);
				}
			}
		}
		
		return $dom->saveXML();
  	}

  	public function UpdateAdminRub($idRub, $idAut) {
  		 		
  		$sql = "SELECT id_rubrique, id_auteur
				FROM spip_auteurs_rubriques 
				WHERE id_rubrique = ".$idRub." AND id_auteur = ".$idAut;
			//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"], $DB_OPTIONS);
		$req = $DB->query($sql);
		$DB->close();

		if ($DB->num_rows($req) == 0) {
			
			$sql2 = "INSERT INTO `spip_auteurs_rubriques`  (`id_rubrique`, `id_auteur`)
					VALUES (".$idRub.", ".$idAut.")";
		
			if($this->trace)
				echo $sql2."<br/>";
				
			$DB2 = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"], $DB_OPTIONS);
			$req = $DB2->query($sql2);
			$DB2->close();
		}
  	}
  	
	public function GetAdminRub($idAut) {
  		 		
  		$sql = "SELECT id_rubrique, id_auteur
				FROM spip_auteurs_rubriques 
				WHERE id_auteur = ".$idAut;
			//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"], $DB_OPTIONS);
		$req = $DB->query($sql);
		$DB->close();

		$i = 0;
		while($data = $DB->fetch_assoc($req)) {
			$arrliste[$i] = array("id_rubrique"=>$data['id_rubrique'], "id_auteur"=>$data['id_auteur']);
			//echo "Liste article : ".$arrliste2[$i]['id']." ".$arrliste2[$i]['titre'];
			$i ++;
		}

		return $arrliste;
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
		$nouvelleRubrique->setAttribute("idAdmin", $gSrc->GetIdAdmin($gSrc->id));
		
		$idMotClef = $dom->createTextNode($gSrc->GetMotClef());
			
		$nouvelleRubrique->appendChild($nomRubrique);
		$nouveauMotClef->appendChild($idMotClef);
		
		//$document = $dom->firstChild;
		$parent->appendChild($nouvelleRubrique);
	
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
			$nouvelArticle->setAttribute("idRub", $gSrc->id);
			$nomArticle = $dom->createTextNode(utf8_encode($arrlisteArticle[$k]['titre']));
			$dateArticle = $dom->createTextNode(utf8_encode($arrlisteArticle[$k]['date']));
			$majArticle = $dom->createTextNode(utf8_encode($arrlisteArticle[$k]['maj']));
			
			$idNumeroGrille = $gSrc->GetFormId($arrlisteArticle[$k]['id']);
			
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
			$this->GetChildren($arrliste[$i]['id'], $dom, $Rub);
		}
	}
	
	public function GetXmlSrc() {
		
		$url = PathRoot."/param/synchro.xml";
		
	}

	/*
	 * Permet de nettoyer la base de données des données non utilisées des articles, en précisant la plage d'articles à explorer
	 * 
	 */
	function CleanArticle($deb, $fin) {
		echo 'CLEAN Article</BR>';
		for ($i=$deb; $i<=$fin; $i++) {
			$idArticleFantome = $this->GetArticleFantome($i);
			if ($idArticleFantome != -1) {
				echo "idArticleFantome = ".$idArticleFantome."</BR>";
				$arrListeDonnees = $this->GetIdDonnees($idArticleFantome) ;
				
				if($arrListeDonnees !=null) {
					foreach ($arrListeDonnees as $donnee) {
						echo "/// idDonnee = ".$donnee['id']."</BR>";
						$this->DelFormsDonneesChamps($donnee['id']);
						echo "/// +++ Suppression champ idDonnee = ".$donnee['id']."</BR>";
						$this->DelFormsDonnees($donnee['id']);
						echo "/// +++ Suppression idDonnee = ".$donnee['id']."</BR>";
					}
				}
				$this->DelFormsDonneesArticles($idArticleFantome);
				$this->DelFormsArticles($idArticleFantome);
				$this->DelAuteursArticles($idArticleFantome);
				echo "Suppression idArticle = ".$idArticleFantome."</BR>";
			} 
		}
		echo 'FIN CLEAN Article</BR>';
	}
	
/*
	 * Permet de nettoyer la base de données des données non utilisées des rubriques, en précisant la plage d'articles à explorer
	 * 
	 */
	function CleanRubrique($deb, $fin) {
		echo 'CLEAN Rubrique</BR>';
		for ($i=$deb; $i<=$fin; $i++) {
			$idRubriqueFantome = $this->GetRubriqueFantome($i);
			if ($idRubriqueFantome != -1) {
				echo "idRubriqueFantome = ".$idRubriqueFantome."</BR>";
				$this->DelMotsRubriques($idRubriqueFantome) ;
				echo "Suppression idRubrique = ".$idRubriqueFantome."</BR>";
			} 
		}
		echo 'FIN CLEAN Rubrique</BR>';
	}
	
	/*
	 * Récupére l'article nécessitant la vérification de la présence de données inutilisées
	 * 
	 */
	function GetArticleFantome($idArticle, $extraSql="") {
	
		$sql = "SELECT a.id_article 
			FROM spip_articles a
			WHERE a.id_article = ".$idArticle." ".$extraSql."
				";
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"], $DB_OPTIONS);
		$req = $DB->query($sql);
		$DB->close();
		
		if($data = $DB->fetch_assoc($req)) {
			return -1;
		}

		return $idArticle; 
	}
	
	/*
	 * Récupére la rubrique nécessitant la vérification de la présence de données inutilisées
	 * 
	 */
	function GetRubriqueFantome($idRubrique, $extraSql="") {
	
		$sql = "SELECT a.id_rubrique 
			FROM spip_rubriques a
			WHERE a.id_rubrique = ".$idRubrique." ".$extraSql."
				";
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"], $DB_OPTIONS);
		$req = $DB->query($sql);
		$DB->close();
		
		if($data = $DB->fetch_assoc($req)) {
			return -1;
		}

		return $idRubrique; 
	}
	
	/*
	 * Efface les données d'un article précis dans la table spip_forms_articles
	 * 
	 */
	function DelMotsRubriques($idRubrique) {

		$sql = "DELETE 
				FROM spip_mots_rubriques 
				WHERE id_rubrique = ".$idRubrique;
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"], $DB_OPTIONS);
		$req = $DB->query($sql);
		$DB->close();
	}
	
	/*
	 * Efface les données d'un article précis dans la table spip_forms_articles
	 * 
	 */
	function DelFormsArticles($idArticle) {

		$sql = "DELETE 
				FROM spip_forms_articles 
				WHERE id_article = ".$idArticle;
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"], $DB_OPTIONS);
		$req = $DB->query($sql);
		$DB->close();
	}
	
	/*
	 * Efface les champs d'une donnée précise dans la table spip_forms_donnees_champs
	 * 
	 */
	function DelFormsDonneesChamps($idDonnee) {

		$sql = "DELETE 
				FROM spip_forms_donnees_champs 
				WHERE id_donnee = ".$idDonnee;
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"], $DB_OPTIONS);
		$req = $DB->query($sql);
		$DB->close();
	}
	
	/*
	 * Efface les données d'un article précis dans la table spip_forms_donnees_articles
	 * 
	 */
	function DelFormsDonneesArticles($idArticle) {

		$sql = "DELETE 
				FROM spip_forms_donnees_articles 
				WHERE id_article = ".$idArticle;
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"], $DB_OPTIONS);
		$req = $DB->query($sql);
		$DB->close();
	}
	
	/*
	 * Efface une donnée précise de la table spip_forms_donnees
	 * 
	 */
	function DelFormsDonnees($idDonnee) {
	
		$sql = "DELETE 
				FROM spip_forms_donnees 
				WHERE id_donnee = ".$idDonnee;
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"], $DB_OPTIONS);
		$req = $DB->query($sql);
		$DB->close();
	}
	
	/*
	 * Efface les données d'un article précis de la table spip_auteurs_articles
	 * 
	 */
	function DelAuteursArticles($idArticle) {
		
		$sql = "DELETE 
				FROM spip_auteurs_articles 
				WHERE id_article = ".$idArticle;
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"], $DB_OPTIONS);
		$req = $DB->query($sql);
		$DB->close();
	}
	
	/*
	 * Renvoie un tableau des id de données d'un article précis
	 * 
	 */
	function GetIdDonnees($idArticle) {

		$sql = "SELECT da.id_donnee
				FROM spip_forms_donnees_articles da 
				WHERE da.id_article = ".$idArticle;
			
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"], $DB_OPTIONS);
		$req = $DB->query($sql);
		$DB->close();
		
		$i = 0;
		while($data = $DB->fetch_assoc($req)) {
			$arrliste[$i] = array("id"=>$data['id_donnee']);
			//echo "Liste article : ".$arrliste2[$i]['id']." ".$arrliste2[$i]['titre'];
			$i ++;
		}

		return $arrliste;		
	}
	
}
?>