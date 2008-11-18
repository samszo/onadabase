<?php

Class Synchro{
	public $trace;
	public $wFic;
	private $siteSrc;
	private $siteDst;
	public $urlSrc;
	public $urlDst;
	public $nbRubrique;
	public $nbArticle;
	public $nbDonnee;
	public $dom;
	public $xul;
	public $ficXul;
	public $pathficXul;
	public $show;
	
	function __construct($siteSrc, $siteDst) {
		$this->trace = TRACE;
		$this->wFic = true;
		$this->siteSrc = $siteSrc;
		$this->siteDst = $siteDst;
		$this->urlSrc =	PathRoot."/bdd/synchro/VerifSynchro-".$siteDst->id."-".$_SESSION['IdAuteur']."-";		
		$this->urlDst =	PathRoot."/bdd/synchro/VerifSynchro-".$siteSrc->id."-".$_SESSION['IdAuteur']."-";
		$this->pathficXul =	PathRoot."/bdd/synchro/CompareSynchro-".$siteSrc->id."-".$siteDst->id."-".$_SESSION['IdAuteur']."-";
		
	}

	function DelDocumentsRubriques($idRubrique) {

		$sql = "DELETE 
				FROM spip_documents_rubriques 
				WHERE id_rubrique = ".$idRubrique;
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
	}

	function DelSyndicsRubriques($idRubrique,$ExtraSql="") {

		$sql = "DELETE 
				FROM spip_syndic 
				WHERE id_rubrique = ".$idRubrique.$ExtraSql;
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		
		//atention aux mot_syndics qui n'ont plus de rubrique
				
	}
	
	function DelMotsArticles($idArticle) {
		
		$sql = "DELETE 
				FROM spip_mots_articles 
				WHERE id_article = ".$idArticle;
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
	}	
	public function ShowSynchro($site, $auteur)
	{
		$this->show=true;
		$this->Synchronise($site,$auteur);
		return $this->xul;		
		
	}

	public function SynchroArbreSrcDst($idRub,$type,$id)
	{
		//r�cup�re le xul de comparaison
		$ficXul = $this->pathficXul.$idRub.".xul";
		$this->dom = new XmlParam($ficXul);
				
		//r�cup�re les �l�ments � ex�cuter
		//$Xpath = "//".$type."[@id='".$id."']";
		$Xpath = "//treeitem[@id='treeCompareSrcDst*".$type."*".$id."']/treechildren/treeitem/treerow";
		if($this->trace)
			echo "Synchro:SynchroArbreSrcDst//recup�re les sous elements � ex�cuter:Xpath".$Xpath."<br/>";
		$Es = $this->dom->GetElements($Xpath);
		if($Es){
			foreach($Es as $E){
				$eId= $E->treecell[0]["label"]."";
				$eVal= $E->treecell[1]["label"]."";
				$eType= $E->treecell[2]["label"]."";
				$eAction= $E->treecell[3]["label"]."";
				//on exclu certaine type
				if($eType!="id" && $eType!="id_parent"){
					$this->SynchroBrancheSrcDst($idRub,$eId,$eVal,$eType,$eAction);
				}		
			}
		}
		//supprime le xul de comparaison
		unlink($ficXul);
		//supprime l'arbre src
		unlink($this->urlSrc.$idRub.".xml");
		//supprime l'arbre dst
		unlink($this->urlDst.$idRub.".xml");
		
	}
	
	public function SynchroBrancheSrcDst($idRub,$id,$val,$type,$action)
	{

		$Xpath = "/XmlParams/XmlParam[@nom='Synchronise']/synchro[@action='".$action."' and @type='".$type."']/Query";
		if($this->trace)
			echo "Synchro:SynchroBrancheSrcDst:Xpath".$Xpath."<br/>";
		//r�cup�re les fonction � ex�cuter
		$Fs = $this->siteSrc->XmlParam->GetElements($Xpath);
		$r=0;
		if($Fs){
			foreach($Fs as $F){		
				//r�cup�re les requ�tes � ex�cuter
				$Xpath = "/XmlParams/XmlParam[@nom='Synchronise']/Querys/Query[@fonction='".$F["fonction"]."']";
				$Qs = $this->siteSrc->XmlParam->GetElements($Xpath);
				foreach($Qs as $Q){		
					$insertfrom = str_replace("-type-", $type, $Q[0]->insertfrom);
					//la source d'une requ�te est toujours le FROM 
					//la destination = INSERT INTO
					$insertfrom = str_replace("-dbSrc-", $this->siteSrc->infos["SQL_DB"], $insertfrom);
					$insertfrom = str_replace("-dbDst-", $this->siteDst->infos["SQL_DB"], $insertfrom);
					$where = str_replace("-id-", $id, $Q[0]->where);
					$where = str_replace("-type-", $type, $where);
					$sql = $insertfrom.$where;
					$db = new mysql ($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
					$db->connect();
					$result = $db->query($sql);
					$r .= mysql_affected_rows().",";			
					//if($this->trace)
						echo "Synchro:SynchroBrancheSrcDst:r=".$r." sql=".$sql."<br/>";
					$db->close();
				}
			}
		}

		return $r;		
		
	}

	public function CompareSrcDst($idRub)
	{
		$this->show=false;
		
		//r�cup�re le dom du site destination de r�f�rence
		$url = $this->urlDst.$idRub.".xml";
		//v�rifie s'il faut cr�er le dom
		if (!file_exists($url)){		
			$this->dom = new DomDocument("1.0");
			$nouveauDocument = $this->dom->createElement("documents");
			$this->dom->appendChild($nouveauDocument);	
			$parent = $this->dom->lastChild; 			
			$this->GetRubElements($this->siteDst,$idRub,$parent);
			$this->dom->save($url);	
		}
		$xmlDst = new XmlParam($url, -1);	
		
		//r�cup�re le dom du site source � v�rifier
		$url = $this->urlSrc.$idRub.".xml";
		if (!file_exists($url)){		
			$this->dom = new DomDocument("1.0");
			$nouveauDocument = $this->dom->createElement("documents");
			$this->dom->appendChild($nouveauDocument);	
			$parent = $this->dom->lastChild; 			
			$this->GetRubElements($this->siteSrc,$idRub,$parent);
			$this->dom->save($url);	
		}
		$xmlSrc = new XmlParam($url, -1);	

		if($this->wFic){
			//initialisation du fichier XUL
			//pour ne pas d�passer la limite de m�moire
			//pour g�rer les probl�me de m�moire
			//ini_set("memory_limit","128M");
			$ficXul =$this->pathficXul.$idRub.".xul";
			if (file_exists($ficXul)) {
			   include($ficXul);
			   return;
			} else {
				$this->ficXul = fopen($ficXul, "a");
			}
		}
				
		//initialisation du tree xul
		$js = "";
		$this->xul = "<tree flex=\"1\" 
			id=\"treeCompareSrcDst\"
			seltype='multiple'
			context='mnuSynchro'
			siteSrc='".$this->siteSrc->id."'
			siteDst='".$this->siteDst->id."'
			".$js."
			>".EOL;
		$this->xul .= '<treecols>'.EOL;
		$this->xul .= '<treecol  id="id" primary="true" cycler="true" flex="1" persist="width ordinal hidden"/>'.EOL;
		$this->xul .= '<splitter class="tree-splitter"/>'.EOL;
		$this->xul .= '<treecol id="treecol_id1" hidden="false" label="valeur" flex="1"  persist="width ordinal hidden" />'.EOL;
		$this->xul .= '<splitter class="tree-splitter"/>'.EOL;
		$this->xul .= '<treecol id="treecol_id2" hidden="false" label="Type" flex="1"  persist="width ordinal hidden" />'.EOL;
		$this->xul .= '<splitter class="tree-splitter"/>'.EOL;
		$this->xul .= '<treecol id="treecol_id2" hidden="false" label="Action" flex="1"  persist="width ordinal hidden" />'.EOL;
		$this->xul .= '<splitter class="tree-splitter"/>'.EOL;
		$this->xul .= '<treecol id="completed" label="Avancement" flex="1" type="progressmeter"/>'.EOL;
		$this->xul .= '</treecols>'.EOL;
		$this->xul .= '<treechildren>'.EOL;
		if($this->wFic){
			fwrite($this->ficXul, $this->xul);		
			$this->xul = EOL;
		}		
		
		//boucle sur les rubriques de r�f�rence
		$Xpath = "/documents/rubrique";
		$this->xul .= $this->CompareXml("rubrique",$Xpath, $xmlSrc, $xmlDst);
		
		$this->xul .= '</treechildren>'.EOL;
		$this->xul .= '</tree>';
		if($this->wFic){
			fwrite($this->ficXul, $this->xul);		
			$this->xul = EOL;
    		fclose($this->ficXul);
			include($ficXul);
		}else		
			return $this->xul;		
		
	}

	public function CompareXml($type, $Xpath, $xmlSrc, $xmlDst){

		if($this->trace)
			echo "Synchro:CompareXml : $type, $Xpath </br>";
		$Dsts = $xmlDst->GetElements($Xpath);
		if($Dsts!=-1 && $Dsts){
			$xulPar="";
			$i=0;
			foreach($Dsts as $Dst) {
							
				//v�rifie que l'id de la rubrique est pr�sente dans la source
				$path = "//".$type."[@id='".$Dst["id"]."']";
				$Srcs = $xmlSrc->GetElements($path);
				$xulEnf="";
				$idTree = "treeCompareSrcDst";
				if(count($Srcs)>0 && $Srcs!=-1){
					//v�rifie que les attributs du type sont les m�me 
					foreach($Dst->attributes() as $a => $b){
						//calcul l'action des attributs
						if($Srcs[0][$a].""==$b.""){
							$Action = "AUCUNE";
						}else{
							$Action = "MAJ";
							$i++;
						}
						$idXul = $idTree.DELIM.$a.DELIM.$Dst["id"].DELIM.$a;
						$xulEnf .= '<treeitem id="'.$idXul.'" container="false" empty="false"  >'.EOL;
						$xulEnf .= '<treerow '.$this->GetClassAction($Action).'>'.EOL;
						$xulEnf .= '<treecell label="'.$Dst["id"].'"/>'.EOL;
						$xulEnf .= '<treecell label="'.$this->siteSrc->XmlParam->XML_entities($b).'" />'.EOL;
						$xulEnf .= '<treecell label="'.$a.'"/>'.EOL;
						$idXul = $idTree.DELIM.$a.DELIM.$Dst["id"].DELIM.$a."pm";
						$xulEnf .= '<treecell label="'.$Action.'"/>'.EOL;
						$xulEnf .= '<treecell id="'.$idXul.'" value="100%" mode="normal"/>'.EOL;
						$xulEnf .= '</treerow>'.EOL;
						$xulEnf .= '</treeitem>'.EOL;
						
					}
					//v�rifie que la valeur de l'�l�ment est le m�me
					if($Dst==$Srcs[0])
						$i++;
					//calcul l'action de l'�l�ment
					if($i==0){
						$Action = "AUCUNE";
						$Av = 100;
					}else{
						$Action = "MAJ";
						$Av = $i;
					}
				}else{
					$Action = $this->VerifReference($type, $Dst["id"]);
					$Av = $i;				
				}
				
				//cr�ation du treeitem
				$idXul = $idTree.DELIM.$type.DELIM.$Dst["id"];
				$xulPar .= '<treeitem id="'.$idXul.'" container="true" empty="false" open="true" >'.EOL;
				$xulPar .= '<treerow '.$this->GetClassAction($Action).' >'.EOL;
				$xulPar .= '<treecell label="'.$Dst["id"].'"/>'.EOL;
				$xulPar .= '<treecell label="'.$this->siteSrc->XmlParam->XML_entities($Dst).'" />'.EOL;
				$xulPar .= '<treecell label="'.$type.'"/>'.EOL;
				$xulPar .= '<treecell label="'.$Action.'"/>'.EOL;
				$idXul = $idTree.DELIM.$type.DELIM.$Dst["id"].DELIM."pm";
				$xulPar .= '<treecell value="'.$Av.'" id="'.$idXul.'" mode="normal"/>'.EOL;
				$xulPar .= '</treerow>'.EOL;
				$xulPar .= '<treechildren>'.EOL;
				$xulPar .= $xulEnf;
				if($this->wFic){
					fwrite($this->ficXul, $xulPar);		
					$xulPar = EOL;
				}		
				
				//v�rifie si on traite les enfants
				if($type=="rubrique" && $Action=="AJOUT")
					$xulPar .= EOL;
				else{ 
					if($this->wFic){
						//cr�ation des �l�ments enfant suivant le type
						$this->GetTypeElementEnfant($type, $Dst, $Xpath, $xmlSrc, $xmlDst);
					}else{		
						$xulPar .= $this->GetTypeElementEnfant($type, $Dst, $Xpath, $xmlSrc, $xmlDst);
					}
				}
				
				$xulPar .= '</treechildren>'.EOL;
				$xulPar .= '</treeitem>'.EOL;
				if($this->wFic){
					fwrite($this->ficXul, $xulPar);		
					$xulPar = EOL;
				}		
			}
			if($this->wFic){
				fwrite($this->ficXul, $xulPar);		
				$xulPar = EOL;
			}else
				return $xulPar;
		}
	}
	
	function VerifReference($type,$id){
		$Action = "AJOUT";
		$Maj = false;
		//v�rifie si la r�f�rence est pr�sente
		switch ($type) {
			case 'mot':
				$Maj .= $this->VerifExist($this->siteSrc,$id,"mot");
				break;
			case 'grille':
				$Maj .= $this->VerifExist($this->siteSrc,$id,"form");
				break;
			case 'auteur':
				$Maj .= $this->VerifExist($this->siteSrc,$id,"auteur");
				break;
		}
		if($Maj)					
			$Action = "MAJ";
		return $Action;		
	}
	
	function GetTypeElementEnfant($type, $Dst, $Xpath, $xmlSrc, $xmlDst){
		$xulEle = "";
		$open ="true";
		$container ="false";
		switch ($type) {
			case 'rubrique':
				$xulEle .= $this->CompareXml('auteur', $Xpath."[@id=".$Dst["id"]."]/auteurs/auteur", $xmlSrc, $xmlDst);
				$xulEle .= $this->CompareXml('mot', $Xpath."[@id=".$Dst["id"]."]/mots/mot", $xmlSrc, $xmlDst);
				$xulEle .= $this->CompareXml('doc', $Xpath."[@id=".$Dst["id"]."]/docs/doc", $xmlSrc, $xmlDst);
				$xulEle .= $this->CompareXml('article', $Xpath."[@id=".$Dst["id"]."]/article", $xmlSrc, $xmlDst);
				$xulEle .= $this->CompareXml($type, $Xpath."[@id=".$Dst["id"]."]/".$type, $xmlSrc, $xmlDst);
				$open ="true";
				$container ="true";
				break;
			case 'article':
				$xulEle .= $this->CompareXml('mot', $Xpath."[@id=".$Dst["id"]."]/mots/mot", $xmlSrc, $xmlDst);
				$xulEle .= $this->CompareXml('doc', $Xpath."[@id=".$Dst["id"]."]/docs/doc", $xmlSrc, $xmlDst);
				$xulEle .= $this->CompareXml('grille', $Xpath."[@id=".$Dst["id"]."]/grilles/grille", $xmlSrc, $xmlDst);
				$container ="true";
				break;
			case 'grille':
				$xulEle .= $this->CompareXml('champ', $Xpath."[@id=".$Dst["id"]."]/champs/champ", $xmlSrc, $xmlDst);
				$xulEle .= $this->CompareXml('donnee', $Xpath."[@id=".$Dst["id"]."]/donnees/donnee", $xmlSrc, $xmlDst);
				$container ="true";
				break;
			case 'donnee':
				$xulEle .= $this->CompareXml('valeur', $Xpath."[@id=".$Dst["id"]."]/valeur", $xmlSrc, $xmlDst);
				$container ="true";
				break;
		}
		//return array("xul"=>$xulEle,"treeitemStyle"=>'container="'.$container.'" empty="false" open="'.$open.'"');
		return $xulEle;
	}
	
	
	function GetClassAction($Action){
		//d�finie la class de la ligne
		$class = "";
		switch ($Action) {
			case 'AJOUT':
				$class = "properties='RedRow'";
				break;
			case 'MAJ':
				$class = "properties='YellowRow'";
				break;
			case 'AUCUNE':
				$class = "";
				break;
		}
		return $class;		
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
			$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$db->connect();
			$db->query($sql);
			$id = mysql_insert_id();
			$db->close();
		}
		if($this->trace)
			echo "MotClef:GetNew:id=".$id."<br/>";
		return $id;
		
	}

	public function VerifExist($site, $id, $type)
	{
		$sql = "SELECT COUNT(id_".$type.") nb
				FROM spip_".$type."s
				WHERE id_".$type." = ".$id;

		$DB = new mysql($site->infos["SQL_HOST"], $site->infos["SQL_LOGIN"], $site->infos["SQL_PWD"], $site->infos["SQL_DB"]);
		$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		$nb=mysql_num_rows($req); 
		if($nb>0) {
			return true;
		} else {
			return false;
		}

	}
	
	public function Verif($idAuteur=6) {
		
		$sql = "SELECT id_rubrique, titre
		FROM spip_auteurs_rubriques
		ORDER BY titre
		WHERE id_rubrique = ".$idAuteur
		;//LIMIT 0 , 93";

		$DB = new mysql($this->siteDst->infos["SQL_HOST"], $this->siteDst->infos["SQL_LOGIN"], $this->siteDst->infos["SQL_PWD"], $this->siteDst->infos["SQL_DB"]);
		$DB->connect();
		$req = $DB->query($sql);
		$DB->close();
		
	}
	
	/*
	 * Parcourt un fichier xml afin de mettre � jour les identifiants des rubriques et articles
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
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		//$DB->connect();
		$req = $DB->query($sql);
		
		$DB->close();
		if($this->trace)
			echo "Synchro:AddHistoriqueSynchro // Termine";
	}
	
	/*
	 * G�n�re un fichier xml des rubriques administr�es, retourne le chemin vers ce fichier
	 * 
	 */
	public function Synchronise($site, $idAuteur=6) {	
    	
		//r�cup�re les rubriques de l'auteur
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetRubriquesAuteur']";
		if($this->trace)
			echo "Synchro:Synchronise:Xpath=".$Xpath."<BR/>";
		$Q = $this->siteSrc->XmlParam->GetElements($Xpath);
		$where = str_replace("-idAuteur-", $idAuteur, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($site->infos["SQL_HOST"], $site->infos["SQL_LOGIN"], $site->infos["SQL_PWD"], $site->infos["SQL_DB"]);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		if($this->trace)
			echo "Synchro:Synchronise:sql=".$sql."<BR/>";
		
		$url = PathRoot."/bdd/synchro/synchroExport-".$idAuteur.".xml";
		
		if($this->trace)
			echo "Synchro:Synchronise:url // Cr�ation Xml ".$url."<BR/>";
			
		if($this->show){
			$js = "";
			$this->xul = "<tree flex=\"1\" 
				id=\"treeSynchroExport-".$idAuteur."\"
				seltype='multiple'
				".$js."
				>";
			$this->xul .= '<treecols>';
			$this->xul .= '<treecol  id="id" primary="true" cycler="true" flex="1" persist="width ordinal hidden"/>';
			$this->xul .= '<splitter class="tree-splitter"/>';
			$this->xul .= '<treecol id="treecol_id1" hidden="false" label="" flex="1"  persist="width ordinal hidden" />';
			$this->xul .= '<splitter class="tree-splitter"/>';
			$this->xul .= '<treecol id="treecol_id2" hidden="false" label="" flex="1"  persist="width ordinal hidden" />';
			$this->xul .= '</treecols>';
			$this->xul .= '<treechildren>'.EOL;
		}
			
		$this->dom = new DomDocument("1.0");
		$nouveauDocument = $this->dom->createElement("documents");
		$this->dom->appendChild($nouveauDocument);	
		$this->dom->save($url);	
		
		$max=0;
		while ($row =  $db->fetch_assoc($rows)) {
		
			if($this->trace)
				echo "Synchro:Synchronise:id_rubrique ".$row['id_rubrique']."<BR/>";
			$parent = $this->dom->lastChild; 	
			$this->GetRubElements($site, $row['id_rubrique'],$parent);
		}
		if($this->show){
			$this->xul .= '</treechildren>'.EOL;
			$this->xul .= '</tree>';
		}
		
		$xmlSrc = $this->dom->save($url);	
		if ($this->trace) {
			echo "Synchro:Synchronise:XML Tree ".$this->dom->saveXML()."<BR/>";
		}
		
		return $url;
	}

	/*
	 * Permet d'importer dans la base rubriques et articles � partir d'un fichier xml, 
	 * g�n�re aussi de l'xml pour la mise � jour des identifiants des rubriques et articles
	 * 
	 */
  	function import($xmlSrc, $update) {
  		
  		$dom = new DOMDocument("1.0");
		$nouvelleRacine = $dom->createElement("documents");
		$dom->appendChild($nouvelleRacine);	
		$racine = $dom->lastChild;
  		
  		if($this->trace)
			echo "Synchro:import //r�cuparation de la d�finition des donn�es ".$xmlSrc."<br/>";
		$xml = new XmlParam($xmlSrc, -1);	
		
		$Xpath = "/documents/rubrique";
		
		$nodesPrincipaux = $xml->GetElements($Xpath);
		
		if ($nodesPrincipaux!=-1) {
			foreach($nodesPrincipaux as $node) {
				$idRub = $node['id']."";
				$idParent = $node['idParent']."";
				$idAdmin = $node['idAdmin']."";
			
				if($this->trace)
					echo "Synchro:import:idRub ".$idRub." idParent ".$idParent." idAdmin ".$idAdmin."<br/>";

				if ($idAdmin !="") {
					if ($update)
						$this->UpdateAdminRub($idRub, $idAdmin);
				} 
			
				$rubriques = $node->rubrique;

				$g = new Granulat($idRub, $this->siteSrc); 
			
				if ($g->VerifExistRubrique($idRub, $idParent)==-1) {
					$gra = new Granulat($idParent, $this->siteSrc); 
					$idEnfant = $gra->SetNewEnfant(utf8_decode($node));
	  				$gra->SetMotClef($node->motclef, $idEnfant);
	  				if ($update) 
	  					$gra->UpdateIdRub($idEnfant, $idRub, $idParent);
	  				else if ($idAdmin !="") $this->UpdateAdminRub($idEnfant, $idAdmin);
				}
			
				// Si un article est d�j� pr�sent pour une rubrique principale, on n'�crase pas cet article
				if ($node->article['id']) {
					if ($g->VerifExistArticle($node->article["id"], $node->article['idRub'])==-1) {
		
						$nouvelArt = $dom->createElement("art");
						$nouvelArt->setAttribute("oldId", $node->article['id']);
					
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
								echo "Synchro/import - cr�ation de la donnee ".$idDon."<br/>";	
			  				
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
										echo "Synchro/import -- r�cup�ration du type de champ ".$champ."<br/>";
										echo "Synchro/import -- r�cup�ration de la valeur du champ ".$valeur."<br/>";
									}
									$row = array('champ'=>$champ, 'valeur'=>$valeur);
									
									$grille = new Grille($this->siteSrc);
									if($this->trace)
										echo "Synchro/import --- cr�ation du champ <br/>";
									$grille->SetChamp($row, $idDon, false);
								}
								$j++;
							}
			  			}
			  		if ($update) 
			  			$g->UpdateIdArt($idArt, $node->article["id"], $node->article["idRub"]);		
					}
				}
					
				foreach($rubriques as $rubrique) {
					//r�cuparation du granulat
					
					if ($g->VerifExistRubrique($rubrique['id'], $rubrique['idParent'])==-1) {
						$nouvelleRub = $dom->createElement("rub");
						$nouvelleRub->setAttribute("oldId", $rubrique['id']);
						
						$idEnfant = $g->SetNewEnfant(utf8_decode($rubrique));
	  					$g->SetMotClef($rubrique->motclef, $idEnfant);
	  					
	  					$nouvelleRub->setAttribute("newId", $idEnfant);
	  					$nouvelleRub->setAttribute("parentId", $idRub);
	  					$racine->appendChild($nouvelleRub);
	  					
	  					if ($update) 
	  						$g->UpdateIdRub($idEnfant, $rubrique['id'], $rubrique['idParent']);
	  					
					} else $idEnfant = $rubrique['id'];
					
					$g->SetRubElements($xml, $idEnfant, $rubrique->rubrique, $rubrique->article, $dom, $update);
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
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();

		if ($DB->num_rows($req) == 0) {
			
			$sql2 = "INSERT INTO `spip_auteurs_rubriques`  (`id_rubrique`, `id_auteur`)
					VALUES (".$idRub.", ".$idAut.")";
		
			if($this->trace)
				echo $sql2."<br/>";
				
			$DB2 = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
			$req = $DB2->query($sql2);
			$DB2->close();
		}
  	}
  	
  	public function ReInitId($table, $nomChamp) {
  		
  		$sql = "SELECT max(".$nomChamp.") as valeurMax FROM ".$table;
  		
  		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();

		while($data = $DB->fetch_assoc($req)) {
			$idValeur = $data['valeurMax']+1;
		}
  		
  		$sql = "INSERT INTO ".$table." (".$nomChamp.") VALUES(".$idValeur.")";
  		
  		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();

		$sql = "DELETE FROM ".$table." WHERE ".$nomChamp." = ".$idValeur;
  	  		
  		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		
  	}
  	
	public function GetAdminRub($idAut) {
  		 		
  		$sql = "SELECT id_rubrique, id_auteur
				FROM spip_auteurs_rubriques 
				WHERE id_auteur = ".$idAut;
			//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
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
 * Parcourt r�cursivement les enfants afin de cr�er l'objet dom de l'xml (correspondant � l'export)
 */
	public function GetRubElements($site, $idRub, $parent) {

		
		$gSrc = new Granulat($idRub,$site);
		if($gSrc->IdParent.""=="")
			return;
		$xul = new Xul($site);
		
		$nouvelleRubrique = $this->dom->createElement("rubrique");

		$nomRubrique = $this->dom->createTextNode(utf8_encode($gSrc->titre));
		$nouvelleRubrique->setAttribute("id", $gSrc->id);
		$nouvelleRubrique->setAttribute("id_parent", $gSrc->IdParent);
		$nouvelleRubrique->appendChild($nomRubrique);		
		//$document = $dom->firstChild;
		$parent->appendChild($nouvelleRubrique);	
		$Rub = $parent->lastChild;
		
		if($this->show){
			$idXul = "treeSynchro_rub_".$gSrc->id;
			$this->xul .= '<treeitem id="'.$idXul.'" container="true" empty="false"  >'.EOL;
			$this->xul .= '<treerow>'.EOL;
			$this->xul .= '<treecell label="'.$gSrc->id.'"/>'.EOL;
			$this->xul .= '<treecell label="'.$gSrc->titre.'" />'.EOL;
			$this->xul .= '<treecell label="rubrique"/>'.EOL;
			$this->xul .= '</treerow>'.EOL;
		}						

		//construction des �l�ments enfants		
		if($this->show)
			$this->xul .= '<treechildren >'.EOL;
		

		//ajoute les mots clefs
		$auteurs = $this->GetAuteurs($gSrc,"rubrique");
		if($auteurs)
			$Rub->appendChild($auteurs);		
		
		//ajoute les mots clefs
		$mots = $this->GetMots($gSrc,"rubrique");
		if($mots)
			$Rub->appendChild($mots);		
			
		//ajoute les documents de l'article
		$docs = $this->GetDocs($gSrc,"rub");
		if($docs){
			$Rub->appendChild($docs);
		}
		//$arrlisteArticle = $gSrc->GetArticleInfo("AND a.statut='publie'");
		$arrlisteArticle = $gSrc->GetArticleInfo();
		
		if(mysql_num_rows($arrlisteArticle)>0){
			while($rowArt = mysql_fetch_assoc($arrlisteArticle)) {

				$Rub->appendChild($this->GetArticle($gSrc,$rowArt));
				
			}
		}
		
		//$document = $dom->firstChild;
		$parent->appendChild($Rub);
		
		$arrliste = $gSrc->GetListeEnfants();
		if($arrliste){
			for ($i = 0; $i < sizeof($arrliste); $i++) {
			//for ($i = 0; $i < 1; $i++) {
				$this->GetRubElements($site, $arrliste[$i]['id'],$Rub);
			}
		}

		if($this->show){
			$this->xul .= '</treechildren>'.EOL;
			$this->xul .= '</treeitem>'.EOL;
		}
		
	}

	public function GetAuteurs($gSrc, $type, $id=-1){

		$nx=false;
		//r�cup�re les auteurs
		$rs = $gSrc->GetTypeAuteur($type,$id);

		if(mysql_num_rows($rs)>0){			
			if($this->show){
				$idXul = "treeSynchro_".$type."_".$id."_auteurs";
				$this->xul .= '<treeitem id="'.$idXul.'" container="true" empty="false" >'.EOL;
				$this->xul .= '<treerow>'.EOL;
				$this->xul .= '<treecell label="'.'"/>'.EOL;
				$this->xul .= '<treecell label="Auteurs"/>'.EOL;
				$this->xul .= '<treecell label="'.'"/>'.EOL;
				$this->xul .= '</treerow>'.EOL;
				$this->xul .= '<treechildren >'.EOL;
			}
			
			$nx = $this->dom->createElement("auteurs");
			while($r = mysql_fetch_assoc($rs)) {
				$n = $this->dom->createElement("auteur");
				$n->setAttribute("id", $r["id_auteur"]);
				$n->setAttribute("login", $r["login"]);
				$n->appendChild($this->dom->createTextNode($r["nom"]));				
				if($this->show){
					$idXul = "treeSynchro_".$type."_".$id."_auteur_".$r["id_auteur"];
					$this->xul .= '<treeitem id="'.$idXul.'" container="false" empty="false" >'.EOL;
					$this->xul .= '<treerow>'.EOL;
					$this->xul .= '<treecell label="'.$r["id_auteur"].'"/>'.EOL;
					$this->xul .= '<treecell label="'.$r["nom"].'"/>'.EOL;
					$this->xul .= '<treecell label="'.$r["login"].'"/>'.EOL;
					$this->xul .= '</treerow>'.EOL;
					$this->xul .= '</treeitem>'.EOL;
				}
				$nx->appendChild($n);
			}

			if($this->show){
				$this->xul .= '</treechildren>'.EOL;
				$this->xul .= '</treeitem>'.EOL;
			}
			
		}
		return $nx;
	}
	
	
	public function GetMots($gSrc, $type, $id=-1){

		$nouveauxMots=false;
		//r�cup�re les motclefs
		$arrMotsClefs = $gSrc->GetTypeMotClef($type,$id);

		if(count($arrMotsClefs)>0){
			
			if($this->show){
				$idXul = "treeSynchro_".$type."_".$id."_mots";
				$this->xul .= '<treeitem id="'.$idXul.'" container="true" empty="false" >'.EOL;
				$this->xul .= '<treerow>'.EOL;
				$this->xul .= '<treecell label="'.'"/>'.EOL;
				$this->xul .= '<treecell label="MotsClefs"/>'.EOL;
				$this->xul .= '<treecell label="'.'"/>'.EOL;
				$this->xul .= '</treerow>'.EOL;
				$this->xul .= '<treechildren >'.EOL;
			}
			
			$nouveauxMots = $this->dom->createElement("mots");
			foreach($arrMotsClefs as $m) {
				$nouveauMot = $this->dom->createElement("mot");
				$nouveauMot->setAttribute("id", $m->id);
				$nouveauMot->setAttribute("idGroupe", $m->id_groupe);
				$nouveauMot->appendChild($this->dom->createTextNode(utf8_encode($m->titre)));				
				if($this->show){
					$idXul = "treeSynchro_".$type."_".$id."_mot_".$m->id;
					$this->xul .= '<treeitem id="'.$idXul.'" container="false" empty="false" >'.EOL;
					$this->xul .= '<treerow>'.EOL;
					$this->xul .= '<treecell label="'.$m->id.'"/>'.EOL;
					$this->xul .= '<treecell label="'.utf8_encode($m->titre).'"/>'.EOL;
					$this->xul .= '<treecell label="'.$m->id_groupe.'"/>'.EOL;
					$this->xul .= '</treerow>'.EOL;
					$this->xul .= '</treeitem>'.EOL;
				}
				$nouveauxMots->appendChild($nouveauMot);
			}

			if($this->show){
				$this->xul .= '</treechildren>'.EOL;
				$this->xul .= '</treeitem>'.EOL;
			}
			
		}
		return $nouveauxMots;
	}
	
	public function GetDocs($gSrc, $type, $id=-1){

		$nouveauxDocs=false;
		//r�cup�re les grilles de l'article
		if($type=="art")
			$arrDocs = $gSrc->GetArtDocs($id);
		if($type=="rub"){
			$arrDocs = $gSrc->GetDocs();
			$id = $gSrc->id;
		}
		
		if(count($arrDocs)>0){
			if($this->show){
				$idXul = "treeSynchro_".$type."_".$id."_docs";
				$this->xul .= '<treeitem id="'.$idXul.'" container="true" empty="false" >'.EOL;
				$this->xul .= '<treerow>'.EOL;
				$this->xul .= '<treecell label="'.'"/>'.EOL;
				$this->xul .= '<treecell label="Documents"/>'.EOL;
				$this->xul .= '<treecell label="'.'"/>'.EOL;
				$this->xul .= '</treerow>'.EOL;
				$this->xul .= '<treechildren >'.EOL;
			}
			
			$nouveauxDocs = $this->dom->createElement("docs");
			foreach($arrDocs as $doc) {
				$nouveauDoc = $this->dom->createElement("doc");
				$nouveauDoc->setAttribute("id", $doc->id);
				$nouveauDoc->setAttribute("type", $doc->type);
				$nouveauDoc->appendChild($this->dom->createTextNode($doc->fichier));				
				if($this->show){
					$idXul = "treeSynchro_".$type."_".$id."_doc_".$doc->id;
					$this->xul .= '<treeitem id="'.$idXul.'" container="false" empty="false" >'.EOL;
					$this->xul .= '<treerow>'.EOL;
					$this->xul .= '<treecell label="'.$doc->id.'"/>'.EOL;
					$this->xul .= '<treecell label="'.$doc->titre.'"/>'.EOL;
					$this->xul .= '<treecell label="'.$doc->fichier.'"/>'.EOL;
					$this->xul .= '</treerow>'.EOL;
					$this->xul .= '</treeitem>'.EOL;
				}
				$nouveauxDocs->appendChild($nouveauDoc);
			}

			if($this->show){
				$this->xul .= '</treechildren>'.EOL;
				$this->xul .= '</treeitem>'.EOL;
			}
			
		}
		return $nouveauxDocs;
	}

	
	public function GetArticle($gSrc, $rowArt){

		$nouvelArticle = $this->dom->createElement("article");
		$nouvelAuteur = $this->dom->createElement("auteur");
		$nouvelleDate = $this->dom->createElement("date");
		$nouvelleMaj = $this->dom->createElement("maj");
		
		$nouvelArticle->setAttribute("id", $rowArt['id_article']);
		$nouvelArticle->setAttribute("idRub", $gSrc->id);
		$nouvelArticle->setAttribute("date",utf8_encode($rowArt['date']));
		$nouvelArticle->setAttribute("maj",utf8_encode($rowArt['maj']));
		$nouvelArticle->setAttribute("idAuteur",$rowArt['id_auteur']);
		$nomArticle = $this->dom->createTextNode(utf8_encode($rowArt['titre']));		
		$nouvelArticle->appendChild($nomArticle);

		if($this->show){
			$idXul = "treeSynchro_art_".$rowArt['id_article'];
			$this->xul .= '<treeitem id="'.$idXul.'" container="true" empty="false" >'.EOL;
			$this->xul .= '<treerow>'.EOL;
			$this->xul .= '<treecell label="'.$rowArt['id_article'].'"/>'.EOL;
			$this->xul .= '<treecell label="'.$this->siteSrc->XmlParam->XML_entities($rowArt['titre']).'"/>'.EOL;
			$this->xul .= '<treecell label="article"/>'.EOL;
			$this->xul .= '</treerow>'.EOL;
			$this->xul .= '<treechildren >'.EOL;
		}
		
					
		//ajoute les mots clefs
		$mots = $this->GetMots($gSrc,"article",$rowArt['id_article']);
		if($mots)
			$nouvelArticle->appendChild($mots);		
		
		//ajoute les Grilles
		$grilles = $this->GetArtGrilles($gSrc, $rowArt['id_article']);
		if($grilles)
			$nouvelArticle->appendChild($grilles);
		
		//ajoute les documents de l'article
		$docs = $this->GetDocs($gSrc,"art" ,$rowArt['id_article']);
		if($docs)
			$nouvelArticle->appendChild($docs);
		
		if($this->show){
			$this->xul .= '</treechildren>'.EOL;
			$this->xul .= '</treeitem>'.EOL;
		}

		return $nouvelArticle;
	}

	public function GetArtGrilles($gSrc, $idArt){

		//r�cup�re les grilles de l'article
		$nouvellesGrilles = false;		
		$arrlisteGrilles = $gSrc->GetFormIds($idArt);
		if(mysql_num_rows($arrlisteGrilles)>0){
			if($this->show){
				$idXul = "treeSynchro_art_".$idArt."_grilles";
				$this->xul .= '<treeitem id="'.$idXul.'" container="true" empty="false" >'.EOL;
				$this->xul .= '<treerow>'.EOL;
				$this->xul .= '<treecell label="'.'"/>'.EOL;
				$this->xul .= '<treecell label="Grilles"/>'.EOL;
				$this->xul .= '<treecell label="'.'"/>'.EOL;
				$this->xul .= '</treerow>'.EOL;
				$this->xul .= '<treechildren >'.EOL;
			}
			
			$nouvellesGrilles = $this->dom->createElement("grilles");
			
			while($rowGrille = mysql_fetch_assoc($arrlisteGrilles)) {
				$idNumeroGrille = $rowGrille['id_form'];
				$grille = new Grille($gSrc->site,$idNumeroGrille);
				$nouvelleGrille = $this->dom->createElement("grille");
				$nouvelleGrille->setAttribute("id", $idNumeroGrille);
				$nouvelleGrille->appendChild($this->dom->createTextNode(utf8_encode($grille->titre)));	
				
				if($this->show){
					$idXul = "treeSynchro_art_".$idArt."_grille_".$idNumeroGrille;
					$this->xul .= '<treeitem id="'.$idXul.'" container="true" empty="false" >'.EOL;
					$this->xul .= '<treerow>'.EOL;
					$this->xul .= '<treecell label="'.$idNumeroGrille.'"/>'.EOL;
					$this->xul .= '<treecell label="'.$grille->titre.'"/>'.EOL;
					$this->xul .= '<treecell label="'.$idNumeroGrille.'"/>'.EOL;
					$this->xul .= '</treerow>'.EOL;
					$this->xul .= '<treechildren >'.EOL;
				}

				//r�cup�re les champs de la grille
				$rclisteChamp = $grille->GetListeChamp();
				if($this->show){
					$idXul = "treeSynchro_art_".$idArt."_grille_".$idNumeroGrille."_champs";
					$this->xul .= '<treeitem id="'.$idXul.'" container="true" empty="false" >'.EOL;
					$this->xul .= '<treerow>'.EOL;
					$this->xul .= '<treecell label="'.$idNumeroGrille.'"/>'.EOL;
					$this->xul .= '<treecell label="Champs de la grille"/>'.EOL;
					$this->xul .= '<treecell label="'.$idNumeroGrille.'"/>'.EOL;
					$this->xul .= '</treerow>'.EOL;
					$this->xul .= '<treechildren >'.EOL;
				}
				
				$nouveauxChamps = $this->dom->createElement("champs");
				while($rowChamp = mysql_fetch_assoc($rclisteChamp)) {
					
					$nouveauChamp = $this->dom->createElement("champ");
					$nomChamp = $this->dom->createTextNode(utf8_encode($rowChamp['titre']));
					$nouveauChamp->appendChild($nomChamp);	
					$nouveauChamp->setAttribute("id", $rowChamp['champ']);
					$nouveauxChamps->appendChild($nouveauChamp);
					if($this->show){
						$idXul = "treeSynchro_champ_".$idNumeroGrille;
						$this->xul .= '<treeitem id="'.$idXul.'" container="false" empty="false" >'.EOL;
						$this->xul .= '<treerow>'.EOL;
						$this->xul .= '<treecell label="'.$idNumeroGrille.'"/>'.EOL;
						$this->xul .= '<treecell label="'.$rowChamp['champ'].'"/>'.EOL;
						$this->xul .= '<treecell label="'.$rowChamp['titre'].'"/>'.EOL;
						$this->xul .= '</treerow>'.EOL;
						$this->xul .= '</treeitem>'.EOL;
					}
					
				}
				$nouvelleGrille->appendChild($nouveauxChamps);					
				if($this->show){
					$this->xul .= '</treechildren>'.EOL;
					$this->xul .= '</treeitem>'.EOL;
				}
				
				//r�cup�re les donn�es de la grille
				$arrlisteDonnee = $gSrc->GetIdDonnees($rowGrille['id_form'],$idArt);
				if(mysql_num_rows($arrlisteDonnee)>0){
							
					$nouvellesDonnees = $this->dom->createElement("donnees");
					
					//replace le curseur au d�but
					mysql_data_seek($arrlisteDonnee,0);
					$i=0;
					while($rowDon = mysql_fetch_assoc($arrlisteDonnee)) {
						$arrInfoDonnee = $gSrc->GetInfosDonnee($rowDon['id_donnee']);
						$nouvelleDonnee = $this->dom->createElement("donnee");
						$nouvelleDonnee->setAttribute("id", $rowDon['id_donnee']);
						$nouvelleDonnee->setAttribute("date", $rowDon['date']);
						$nouvelleDonnee->setAttribute("maj", $rowDon['maj']);
						$nouvelleDonnee->appendChild($this->dom->createTextNode($rowDon['id_donnee']));						
						
						if($this->show){
							$idXul = "treeSynchro_art_".$idArt."_donnee_".$rowDon['id_donnee'];
							$this->xul .= '<treeitem id="'.$idXul.'" container="true" empty="false" >'.EOL;
							$this->xul .= '<treerow>'.EOL;
							$this->xul .= '<treecell label="'.$rowDon['id_donnee'].'"/>'.EOL;
							$this->xul .= '<treecell label="Valeurs '.$i.'"/>'.EOL;
							$this->xul .= '<treecell label="'.$rowDon['id_donnee'].'"/>'.EOL;
							$this->xul .= '</treerow>'.EOL;
							$this->xul .= '<treechildren >'.EOL;
						}
						
						
						while($rowInfoDon = mysql_fetch_assoc($arrInfoDonnee)) {
							$nouvelleValeur = $this->dom->createElement("valeur");
							$nomValeur = $this->dom->createTextNode(utf8_encode($rowInfoDon['valeur']));
							$nouvelleValeur->appendChild($nomValeur);	
							$nouvelleValeur->setAttribute("id", $rowInfoDon['id_donnee']."-".$rowInfoDon['champ']);
							$nouvelleDonnee->appendChild($nouvelleValeur);						
							if($this->show){
								$idXul = "treeSynchro_don_".$rowInfoDon['id_donnee'];
								$this->xul .= '<treeitem id="'.$idXul.'" container="false" empty="false" >'.EOL;
								$this->xul .= '<treerow>'.EOL;
								$this->xul .= '<treecell label="'.$rowInfoDon['id_donnee'].'"/>'.EOL;
								$this->xul .= '<treecell label="'.$rowInfoDon['titre'].'"/>'.EOL;
								$this->xul .= '<treecell label="'.$gSrc->site->XmlParam->XML_entities($rowInfoDon['valeur']).'"/>'.EOL;
								$this->xul .= '</treerow>'.EOL;
								$this->xul .= '</treeitem>'.EOL;
							}
							
						}
						if($this->show){
							$this->xul .= '</treechildren>'.EOL;
							$this->xul .= '</treeitem>'.EOL;
						}
						$nouvellesDonnees->appendChild($nouvelleDonnee);
						$i++;
					}		
					$nouvelleGrille->appendChild($nouvellesDonnees);
					if($this->show){
						$this->xul .= '</treechildren>'.EOL;
						$this->xul .= '</treeitem>'.EOL;
					}																		
				}
				$nouvellesGrilles->appendChild($nouvelleGrille);
			}					
			if($this->show){
				$this->xul .= '</treechildren>'.EOL;
				$this->xul .= '</treeitem>'.EOL;
			}											
		}

		return $nouvellesGrilles;
		
	}
	
	public function GetXmlSrc() {
		
		$url = PathRoot."/param/synchro.xml";
		
	}

	function SupprimerArticle($idArticle) {
		
		if (TRACE) echo "<article> SupprimerArticle = ".$idArticle;
		$arrListeDonnees = $this->GetIdDonnees($idArticle) ;
			
		if($arrListeDonnees !=null) {
			foreach ($arrListeDonnees as $donnee) {
				if (TRACE) echo "<donnee>SupprimerArticle/// idDonnee = ".$donnee['id']."</donnee>";
				$this->DelFormsDonneesChamps($donnee['id']);
				$this->DelFormsDonnees($donnee['id']);
			}
		}
		$this->DelFormsDonneesArticles($idArticle);
		$this->DelFormsArticles($idArticle);
		$this->DelAuteursArticles($idArticle);
		$this->DelDocumentsArticles($idArticle);
		$this->DelMotsArticles($idArticle);
		$this->DelArticle($idArticle);
		if (TRACE) echo "</article>";
	}
	
	function SupprimerArticles($arrListArticles) {
		
		if (TRACE) echo '<suppressionArticles>';
		foreach ($arrListArticles as $article) {
			$this->SupprimerArticle($article['id']);
		}
		if (TRACE) echo '</suppressionArticles>';
	}
	
	/*
	 * Permet de nettoyer la base de donn�es des donn�es non utilis�es des articles, en pr�cisant la plage d'articles � explorer
	 * 
	 */
	function CleanArticle($deb, $fin) {
		echo 'CLEAN Article</BR>';
		for ($i=$deb; $i<=$fin; $i++) {
			$idArticleFantome = $this->GetArticleFantome($i);
			if ($idArticleFantome != -1) {
				echo "idArticleFantome = ".$idArticleFantome."</BR>";
				$this->SupprimerArticle($idArticleFantome);
				echo "Suppression idArticle = ".$idArticleFantome."</BR>";
			} 
		}
		echo 'FIN CLEAN Article</BR>';
	}
	
/*
	 * Permet de nettoyer la base de donn�es des donn�es non utilis�es des rubriques, en pr�cisant la plage d'articles � explorer
	 * 
	 */
	function CleanRubrique($deb, $fin) {
		if($this->trace)
			echo 'Synchro:CleanRubrique:$deb, $fin</BR>';
		for ($i=$deb; $i<=$fin; $i++) {
			//v�rifie si on purge les fantome ou pas
			if($deb!=$fin)
				$idRub = $this->GetRubriqueFantome($i);
			else
				$idRub = $deb;
			if ($idRub != -1) {
				$this->DelRubrique($idRub);
			} 
		}
	}

/*
	 * Permet de nettoyer une rubrique de tous ce qui la compose
	 * 
	 */
	function DelRubrique($idRub) {
		
		$gra = new Granulat($idRub,$this->siteSrc,false);
		$time_start = microtime(true);		
		$this->trace = true;
		if($this->trace)
			echo "<Synchro f='DelRubrique' idRub='".$idRub."' titre=\"".$gra->titre."\" />\n";
		
		$rsArt = $gra->GetArticleInfo();
		while($rArt = mysql_fetch_assoc($rsArt)) {
			$this->SupprimerArticle($rArt["id_article"]);
		}
			
		$this->DelMotsRubriques($idRub) ;
		$this->DelDocumentsRubriques($idRub);
		
		$RubEnfants = $gra->GetEnfants(false);
		if($RubEnfants){
			if($this->trace)
				echo "<Synchro f='DelRubrique' recursif='true' >\n";
			foreach($RubEnfants as $rub){
				$this->DelRubrique($rub->id);
			}
			if($this->trace)
				echo "<Synchro f='DelRubrique' recursif='true' >\n";
		}
		
		$sql = "DELETE 
				FROM spip_rubriques 
				WHERE id_rubrique = ".$idRub;
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		if($this->trace)
			echo "<Synchro f='DelRubrique' fin='$time' idRub='".$idRub."' titre=\"".$gra->titre."\" />\n";
			
	}

	function DelForm($idGrille) {
		
		$time_start = microtime(true);		
		$this->trace = true;
		$g = new Grille($this->siteSrc,$idForm);
		if($this->trace)
			echo "<Synchro f='DelForm' idForm='".$g->id."' titre=\"".$g->titre."\" />\n";
		
		if($this->trace)
			echo "<Synchro f='DelForm' job='supprime les donn�es de la form' >\n";
		$sql = "SELECT id_donnee
			FROM spip_forms_donnees
			WHERE id_form = ".$idGrille;
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$rs = $DB->query($sql);
		$DB->close();
		while($r = $DB->fetch_assoc($rs)) {
			$this->CleanDonnee($r["id_donnee"]);
		}
		if($this->trace)
			echo "</Synchro>\n";
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		if($this->trace)
			echo "<Synchro fin='$time' f='DelForm' idForm='".$g->id."' titre=\"".$g->titre."\" />\n";
			
	}
	
	function CleanForm() {
		
		$time_start = microtime(true);		
		$this->trace = true;
		if($this->trace)
			echo "<Synchro f='CleanForm' />\n";
		
		if($this->trace)
			echo "<Synchro f='CleanForm' job='supprime les donn�es sans articles' >\n";
		$sql = "SELECT fda.id_donnee, a.id_article, fda.id_article
			FROM `spip_forms_donnees_articles` fda
			LEFT JOIN spip_articles a ON a.id_article = fda.id_article
			WHERE a.id_article IS NULL";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$rs = $DB->query($sql);
		$DB->close();
		while($r = $DB->fetch_assoc($rs)) {
			$this->CleanDonnee($r["id_donnee"]);
		}
		if($this->trace)
			echo "</Synchro>\n";
		
		if($this->trace)
			echo "<Synchro f='CleanForm' job='supprime les valeurs de champ sans donn�ee' >\n";
		$sql = "SELECT fdc.id_donnee idDon, fd.id_donnee
			FROM `spip_forms_donnees_champs` fdc
			LEFT JOIN spip_forms_donnees fd ON fd.id_donnee = fdc.id_donnee
			WHERE fd.id_donnee IS NULL";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$rs = $DB->query($sql);
		$DB->close();
		//supprime les lignes
		while($r = $DB->fetch_assoc($rs)) {
			$this->CleanDonnee($r["idDon"]);
		}
		if($this->trace)
			echo "</Synchro>\n";
		
		if($this->trace)
			echo "<Synchro f='CleanForm' job='supprime forms_articles avec des articles perdus' >\n";
		$sql = "SELECT a.id_article, fa.id_article idArt
			FROM `spip_forms_articles` fa
			LEFT JOIN spip_articles a ON a.id_article = fa.id_article
			WHERE a.id_article IS NULL";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$rs = $DB->query($sql);
		$DB->close();
		//supprime les lignes
		while($r = $DB->fetch_assoc($rs)) {
			$this->DelFormsArticles($r["idArt"]);
		}
		if($this->trace)
			echo "</Synchro>\n";
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		if($this->trace)
			echo "<Synchro f='CleanForm' fin='$time' />\n";
			
	}

	
	
	
	function CleanDonnee($idDon) {
		
		if(!$idDon){
			return;
		}
		
		$time_start = microtime(true);
		if($this->trace)
			echo "<Synchro f='CleanDonnee' idDon='".$idDon."' />\n";
		$this->DelFormsDonnees($idDon);
		$this->DelFormsDonneesArticles(-1,$idDon);
		$this->DelFormsDonneesChamps($idDon);
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		if($this->trace)
			echo "<Synchro f='CleanDonnee' fin='$time' idDon='".$idDon."' />\n";
	}
	
	
	/*
	 * R�cup�re l'article n�cessitant la v�rification de la pr�sence de donn�es inutilis�es
	 * 
	 */
	function GetArticleFantome($idArticle, $extraSql="") {
	
		$sql = "SELECT a.id_article 
			FROM spip_articles a
			WHERE a.id_article = ".$idArticle." ".$extraSql."
				";
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		
		if($data = $DB->fetch_assoc($req)) {
			return -1;
		}

		return $idArticle; 
	}
	
	/*
	 * R�cup�re la rubrique n�cessitant la v�rification de la pr�sence de donn�es inutilis�es
	 * 
	 */
	function GetRubriqueFantome($idRubrique, $extraSql="") {
	
		$sql = "SELECT a.id_rubrique 
			FROM spip_rubriques a
			WHERE a.id_rubrique = ".$idRubrique." ".$extraSql."
				";
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		
		if($data = $DB->fetch_assoc($req)) {
			return -1;
		}

		return $idRubrique; 
	}
	
	/*
	 * Efface les donn�es d'un article pr�cis dans la table spip_forms_articles
	 * 
	 */
	function DelMotsRubriques($idRubrique) {

		$sql = "DELETE 
				FROM spip_mots_rubriques 
				WHERE id_rubrique = ".$idRubrique;
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
	}
	
	function DelArticle($idArticle) {
		
		$sql = "DELETE 
				FROM spip_articles 
				WHERE id_article = ".$idArticle;
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
	}
	
	/*
	 * Efface les donn�es d'un article pr�cis dans la table spip_forms_articles
	 * 
	 */
	function DelFormsArticles($idArticle) {

		$sql = "DELETE 
				FROM spip_forms_articles 
				WHERE id_article = ".$idArticle;
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
	}
	
	/*
	 * Efface les champs d'une donn�e pr�cise dans la table spip_forms_donnees_champs
	 * 
	 */
	function DelFormsDonneesChamps($idDonnee) {

		$sql = "DELETE 
				FROM spip_forms_donnees_champs 
				WHERE id_donnee = ".$idDonnee;
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
	}
	
	/*
	 * Efface les donn�es d'un article pr�cis dans la table spip_forms_donnees_articles
	 * 
	 */
	function DelFormsDonneesArticles($idArticle,$idDon=-1) {

		if($idDon != -1)
			$where = " WHERE id_donnee = ".$idDon;
		else
			$where = " WHERE id_article = ".$idArticle;
		$sql = "DELETE 
				FROM spip_forms_donnees_articles 
				".$where;
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
	}
	
	function DelDocumentsArticles($idArticle) {
		
		$sql = "DELETE 
				FROM spip_documents_articles 
				WHERE id_article = ".$idArticle;
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
	}
	
	function GetArticleDonnee($idDonnee, $extraSql="") {
		
		$sql = "SELECT a.id_article 
			FROM spip_forms_donnees_articles a
			WHERE a.id_donnee = ".$idDonnee." ".$extraSql."
				";
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		
		if($data = $DB->fetch_assoc($req)) {
			return $data['id_article'];
		} else return -1;		
	}
	
	function GetArticles($site, $idRub, $idGrille, $extraSql="") {
		
		$sql = "SELECT a.id_article, fd.id_donnee
				FROM spip_articles a
				INNER JOIN spip_forms_donnees_articles da ON da.id_article = a.id_article
				INNER JOIN spip_forms_donnees fd ON fd.id_donnee = da.id_donnee
				AND fd.id_form =".$idGrille." 
				WHERE a.id_rubrique = ".$idRub." ".$extraSql."
				";
		//echo $sql."<br/>";
		$DB = new mysql($site->infos["SQL_HOST"], $site->infos["SQL_LOGIN"], $site->infos["SQL_PWD"], $site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		
		$i = 0;
		while($data = $DB->fetch_assoc($req)) {
			$arrliste[$i] = array("id"=>$data['id_article'], "idDonnee"=>$data['id_donnee']);
			$i ++;
		}
		
		return $arrliste;
		
	}
	
	/*
	 * Efface une donn�e pr�cise de la table spip_forms_donnees
	 * 
	 */
	function DelFormsDonnees($idDonnee) {
	
		$sql = "DELETE 
				FROM spip_forms_donnees 
				WHERE id_donnee = ".$idDonnee;
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
	}
	
	/*
	 * Efface les donn�es d'un article pr�cis de la table spip_auteurs_articles
	 * 
	 */
	function DelAuteursArticles($idArticle) {
		
		$sql = "DELETE 
				FROM spip_auteurs_articles 
				WHERE id_article = ".$idArticle;
		//echo $sql."<br/>";
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
	}
	
	/*
	 * Renvoie un tableau des id de donn�es d'un article pr�cis
	 * 
	 */
	function GetIdDonnees($idArticle) {

		$sql = "SELECT da.id_donnee
				FROM spip_forms_donnees_articles da 
				WHERE da.id_article = ".$idArticle;
			
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
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
	
	function GetRubrique($idArticle) {
		
		$sql = "SELECT a.id_rubrique
				FROM spip_articles a 
				WHERE a.id_article = ".$idArticle;
			
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
	
		if($data = $DB->fetch_assoc($req)) {
			return $data['id_rubrique'];
		}
		else return -1;	
	}
	
	function GetHistoriqueCritere($idRubrique, $critere, $idGrille, $champ) {
		
		$sql = "SELECT sfd.id_donnee idDonnee, sa.id_article IdArt, sfdc.champ, sfdc.valeur
				FROM spip_forms_donnees sfd
				INNER JOIN spip_articles sa ON sa.id_rubrique = ".$idRubrique." 
				INNER JOIN spip_forms_donnees_articles sfda ON sfd.id_donnee = sfda.id_donnee AND sfda.id_article = sa.id_article
				INNER JOIN spip_forms_donnees_champs sfdc ON sfdc.id_donnee = sfda.id_donnee AND sfdc.champ = '".$champ."' AND sfdc.valeur = '".$critere."'
				WHERE id_form = ".$idGrille." GROUP BY IdArt DESC;";
			
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		
		$i = 0;
		while($data = $DB->fetch_assoc($req)) {
			$arrliste[$i] = array("id"=>$data['idDonnee']);
			$i ++;
		}

		return $arrliste;	
	}
	
	function AddVersion() {
		$sql = "SELECT id_article
				FROM spip_articles;";
			
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		
		$i = 0;
		while($data = $DB->fetch_assoc($req)) {
			echo ' id_article '.$data['id_article'];
			$sql2 = "SELECT id_mot, id_article
				FROM spip_mots_articles
				WHERE id_mot = 152 AND id_article = ".$data['id_article'].";";
			
			$DB2 = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
			$req2 = $DB2->query($sql2);
			$DB2->close();
			
			$donnee = $DB2->fetch_assoc($req2);
			if ($donnee->sizeof == 0) {
				$sql1 = "INSERT INTO spip_mots_articles(id_mot, id_article) VALUES (152, ".$data['id_article'].");";
				
				$DB1 = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
				$req1 = $DB1->query($sql1);
				$DB1->close();
				echo ' Ajout version '.$data['id_article'];
			}
		}
	}
	
	function ChangeAutoIncrement($table, $val){
		$sql = "ALTER TABLE `".$table."` AUTO_INCREMENT = ".$val;
			
		$DB = new mysql($this->siteSrc->infos["SQL_HOST"], $this->siteSrc->infos["SQL_LOGIN"], $this->siteSrc->infos["SQL_PWD"], $this->siteSrc->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
	}
	
}
?>