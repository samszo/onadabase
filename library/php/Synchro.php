<?php

Class Synchro{
	public $trace;
	private $siteSrc;
	private $siteDst;
	
	function __construct($siteSrc, $siteDst) {
		$this->trace = true;
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
		while ($row =  $db->fetch_assoc($rows)) {
				echo $row['id_rubrique'];
				
				/*$g = new Granulat($row['id_rubrique'],$siteDst);
				echo $g->titre;
				echo " ".$g->GetMotClef()." ";*/
				
				// Site local
				$gSrc = new Granulat($row['id_rubrique'],$objSite);
				
				// Création Xml		
				$dom = new DomDocument("1.0");
				
				$nouveauDocument = $dom->createElement("documents");
				$dom->appendChild($nouveauDocument);
				$document = $dom->firstChild;
				
				$nouvelleRubrique = $dom->createElement("rubrique");
				$nouveauMotClef = $dom->createElement("motclef");				
				
				$nomRubrique = $dom->createTextNode($gSrc->titre);
				$nouvelleRubrique->setAttribute("id", $gSrc->id);
				$nouvelleRubrique->setAttribute("IdParent", $gSrc->IdParent);
					
				$idMotClef = $dom->createTextNode($gSrc->GetMotClef());
					
				$nouvelleRubrique->appendChild($nomRubrique);
				$nouveauMotClef->appendChild($idMotClef);
				
				$document->appendChild($nouvelleRubrique);
					
					$Rub = $document->lastChild;	
					$Rub->appendChild($nouveauMotClef);
					
				
				$arrlisteArticle = $gSrc->GetArticleInfo("AND a.statut='prepa'");
				//echo ' id arr '.$arrlisteArticle[0]['id'];
				//echo ' titre arr '.$arrlisteArticle[0]['titre'];
				
				for ($k=0; $k<sizeof($arrlisteArticle); $k++) {
					
					$nouvellesDonnees = $dom->createElement("donnees");
					$nouvelleGrille = $dom->createElement("grille");
					$nouveauxChamps = $dom->createElement("champs");
					$nouvelArticle = $dom->createElement("article");
					$nouvelArticle->setAttribute("id", $arrlisteArticle[$k]['id']);
					$nomArticle = $dom->createTextNode($arrlisteArticle[$k]['titre']);
					
					$idNumeroGrille = $gSrc->GetFormId($arrlisteArticle[$k]['id']);
					$idGrille = $dom->createTextNode($idNumeroGrille);			
	

					$nouvelArticle->appendChild($nomArticle);
					$nouvelleGrille->appendChild($idGrille);
					
					$nouvellesDonnees->appendChild($nouvelleGrille);
					
					$arrlisteGrilles = $gSrc->GetIdDonneesTable($idNumeroGrille, $arrlisteArticle[$k]['id']);
					
					//echo '$arrlisteGrilles->lenght '.$arrlisteGrilles->lenght." ";
					
					/*for ($i=0; $i<sizeof($arrlisteGrilles); $i++) {
						echo 'Id donnee '.$arrlisteGrilles[$i]['id'];
					}*/
					
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
						
						for ($j=0; $j<sizeof($arrlisteDonnee); $j++) {
							$nouvelleValeur = $dom->createElement("valeur");
							$nomValeur = $dom->createTextNode($arrlisteDonnee[$j]['valeur']);
							$nouvelleValeur->appendChild($nomValeur);	
							$nouvelleDonnee->appendChild($nouvelleValeur);
						}
						$nouvellesDonnees->appendChild($nouvelleDonnee);					
					}
								
					
					
					$nouvelArticle->appendChild($nouvellesDonnees);
					
					$Rub->appendChild($nouvelArticle);
					
				}
				
	
				
				$document->appendChild($Rub);
				
				$arrliste = $gSrc->GetListeEnfants();
				for ($i = 0; $i < sizeof($arrliste); $i++){
					$this->GetChildren($arrliste[$i]['id'], $dom);
				}
				
			    //$url = PathRoot."/param/synchro.xml";
			     
			    if ($type == "export") $url = PathRoot."/param/synchroExport.xml";
			    else $url = PathRoot."/param/synchroImport.xml";
				//$url = "C:\wamp\www\onadabase\param\synchro.xml";	
					
				if ($this->trace) {
					//echo 'type : '.$type;
					echo $dom->saveXML();
					echo $url;
				}
				$dom->save($url);
				
				/*
				 * Récupère le titre de la rubrique
				 */
				/*
				$Xpath2 = "/XmlParams/XmlParam/Querys/Query[@fonction='GetRubriquesTitre']";
				echo "Site:Synchronise2:Xpath=".$Xpath2."<br/>";
				$Q2 = $siteDst->XmlParam->GetElements($Xpath2);
				$where2 = str_replace("-idRubrique-", $row['id_rubrique'], $Q2[0]->where);
				$sql2 = $Q2[0]->select.$Q2[0]->from.$where2;
				$db2 = new mysql ($siteDst->infos["SQL_HOST"], $siteDst->infos["SQL_LOGIN"], $siteDst->infos["SQL_PWD"], $siteDst->infos["SQL_DB"], $dbOptions);
				$db2->connect();
				$rows2 = $db2->query($sql2);
				$db2->close();
				//if($this->trace)
				echo "Site:Synchronise2:sql=".$sql2."<br/>";
				while ($row2 =  $db2->fetch_assoc($rows2)) {
					echo $row2['titre'];
				}
				*/
				/*
				 * Crée une nouvelle rubrique
				 * 
				  $sql = "INSERT INTO spip_rubriques
				SET titre = ".$objSite->GetSQLValueString($titre, "text");
				
				$DB = new mysql($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"], $objSite->infos["SQL_DB"], $DB_OPTIONS);
				$req = $DB->query($sql);
				$newId = mysql_insert_id();
				$DB->close();*/
		}
	}
	
	public function GetChildren($idRub, $dom) {
		
		global $objSite;
		
		$gSrc = new Granulat($idRub,$objSite);
		//echo " ".$gSrc->GetMotClef()." ";
		
		$nouvelleRubrique = $dom->createElement("rubrique");
		$nouveauMotClef = $dom->createElement("motclef");

		$nomRubrique = $dom->createTextNode($gSrc->titre);
		$nouvelleRubrique->setAttribute("id", $gSrc->id);
		$nouvelleRubrique->setAttribute("IdParent", $gSrc->IdParent);
			
		$idMotClef = $dom->createTextNode($gSrc->GetMotClef());
			
		$nouvelleRubrique->appendChild($nomRubrique);
		$nouveauMotClef->appendChild($idMotClef);
		
		$document = $dom->firstChild;
		$document->appendChild($nouvelleRubrique);
	
			//echo $dom->saveXML();
			//$listeRubrique = $dom->getElementsByTagName('rubrique');
			//$Rub = $listeRubrique->item($index);
		$Rub = $document->lastChild;
		$Rub->appendChild($nouveauMotClef);		
		
		$arrlisteArticle = $gSrc->GetArticleInfo("AND a.statut='prepa'");
		
		for ($k=0; $k<sizeof($arrlisteArticle); $k++) {
		
			$nouvellesDonnees = $dom->createElement("donnees");
			$nouvelleGrille = $dom->createElement("grille");
			$nouveauxChamps = $dom->createElement("champs");
			$nouvelArticle = $dom->createElement("article");
			$nouvelArticle->setAttribute("id", $arrlisteArticle[$k]['id']);
			$nomArticle = $dom->createTextNode($arrlisteArticle[$k]['titre']);
			
			//echo ' ID FORM '.$gSrc->GetFormId($arrlisteArticle[0]['id']); 
			$idNumeroGrille = $gSrc->GetFormId($arrlisteArticle[$k]['id']);
			$idGrille = $dom->createTextNode($idNumeroGrille);
			

			$nouvelleGrille->appendChild($idGrille);
			$nouvelArticle->appendChild($nomArticle);
	
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
					
				for ($j=0; $j<sizeof($arrlisteDonnee); $j++) {
					$nouvelleValeur = $dom->createElement("valeur");
					$nomValeur = $dom->createTextNode($arrlisteDonnee[$j]['valeur']);
					$nouvelleValeur->appendChild($nomValeur);	
					$nouvelleDonnee->appendChild($nouvelleValeur);	
										
				}
				$nouvellesDonnees->appendChild($nouvelleDonnee);		
			}		
			

			
			
			$nouvelArticle->appendChild($nouvellesDonnees);
			
			$Rub->appendChild($nouvelArticle);
		
		}
		
		//$document = $dom->firstChild;
		$document->appendChild($Rub);
		
		$arrliste = $gSrc->GetListeEnfants();
		for ($i = 0; $i < sizeof($arrliste); $i++) {
			//echo " GG ".$arrliste[$i]['id'];
			$this->GetChildren($arrliste[$i]['id'], $dom);
		}
	}
	
	public function GetXmlSrc() {
		
		$url = PathRoot."/param/synchro.xml";
		
	}
			
}
?>