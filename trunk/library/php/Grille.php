<?php
class Grille{
  public $id;
  public $XmlParam;
  public $trace;
  private $site;

  function __tostring() {
    return "Cette classe permet de définir et manipuler des grilles.<br/>";
    }

  function __construct($site, $id=-1, $complet=true) {
	//echo "new Site $sites, $id, $scope<br/>";
	$this->trace = TRACE;

    $this->site = $site;
    $this->id = $id;
	if($this->site->scope["FicXml"]!=-1)
		$this->XmlParam = new XmlParam($this->site->scope["FicXml"]);
	
	if($complet){
	}

	//echo "FIN new grille <br/>";
		
    }

    
    function GetTreeProb($idRub){
    	
    	$g = new Granulat($idRub,$this->site);
    	//récupère les rubriques enfants
    	$ids = str_replace(DELIM,",",$g->GetEnfantIds());
    	$ids .= "-1";
    	
		//récupère les identifiants des rubriques de la racine ayant un problème
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetListeSignalementProbleme']";
		if($this->trace)
			echo "Grille:GetTreeProb:Xpath".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-ids-", $ids, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		//if($this->trace)
			echo "Grille:GetTreeProb:".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();
		
		/*
		$xul = '<tree flex="1">
		  <treecols>
		    <treecol label="Titre" flex="1"/>
		    <treecol label="Type" flex="1"/>
		    <treecol label="Modifier" />
		    <treecol label="Supprimer" />
		  </treecols>
		  <treechildren>';
		$xul ="<richlistbox>
		   <richlistitem> <!-- 1ere ligne -->
		     <label value='Titre'/>
		     <label value='Type'/>
		     <label value='Modifier'/>
		     <label value='Supprimer'/>
		   </richlistitem>";
		*/
		$xul ='<grid flex="1">';
		//on cache la colonne de référence	
		$xul.='<columns>';	
			$xul.='<column flex="1" hidden="true"/>';	
			$xul.='<column flex="1"/>';
			$xul.='<column flex="1"/>';			
			$xul.='<column flex="1" hidden="true"/>';			
			$xul.='<column flex="1"/>';			
		$xul.='</columns>';	
		$xul.='<rows>';
		/*	
		$xul.="<row>
				<label value='Référence' />
			    <label value='Rubrique parente'/>
			    <label value='Rubrique'/>
			    <label value='Article'/>
			    <label value='N° problème'/>
			    <label value='Modifier'/>
			    <label value='Supprimer'/>
			</row>";	
		*/
		$oidRubPar=-1;
		$oidRub=-1;
		$oidArt=-1;
		while ($r =  $db->fetch_assoc($result)) {
			if($this->trace)
				echo "Grille:GetTreeProb:".$r["idRub"]." ".$r["idArt"]." ".$r["idDon"]."<br/>";
			//$xul .= $this->GetXulForm($r["idDon"], $this->site->infos["GRILLE_SIG_PROB"]); 
    		/*
			$xul .= "<listitem>
			        <listcell>".$r["titreRub"]."</listcell>
			        <listcell>".$r["titreArt"]."</listcell>
			        <listcell><image src='/images/check_no.png' /></listcell>
			        <listcell><image src='/images/check_yes.png' /></listcell>
			    </listitem>";
		$xul .="<richlistitem>
		     <label value='".$r["titreRub"]."'/>
		     <label value='".$r["titreArt"]."'/>
		     <image src='images/check_yes.png' />
		     <image src='images/check_no.png' />
		   </richlistitem>";
		
		$xul .="<treeitem>
		      <treerow>
		        <treecell label='".$r["titreRub"]."'/>
		        <treecell label='".$r["titreArt"]."'/>
		        <treecell onclick=\"alert('yes')\" src='images/check_yes.png' />
		        <treecell onclick=\"alert('no')\" src='images/check_no.png' />
		      </treerow>
		    </treeitem>";
			*/
		
		if 	(!$r["ReponsePhoto"])	$r["ReponsePhoto"] = 'Non';
				
		$idDoc = 'val'.DELIM.$this->site->infos["GRILLE_SIG_PROB"].DELIM.$r["idDon"].DELIM."Modif".DELIM.$r["idArt"];
		$xul.="<row>";
			$xul.="<vbox hidden='true' >";
				$xul.="<label id='".$idDoc."' value='".$idDoc."' />";
			$xul.="</vbox>";

			$xul.="<vbox>";
				if($r["idRubPar"]!=$oidRubPar){
					$xul.="<label value='".$r["titreRubPar"]."'/>";
					$xul.="<hbox>";
						$xul.="<label id='adminRubPar_".$r["idRubPar"]."' class='text-linkAdmin' onclick=\"OuvreLienAdmin(".$r["idRubPar"].");\" value=\"Admin\"/>";
			    		$xul.="<!--<image onclick=\"SetVal('".$idDoc."');\" src='images/check_yes.png' /> -->";
			    		$xul.="<image onclick=\"DelRubriqueParent('".$r["idRubPar"]."');\" src='images/check_no.png' />";
			    	$xul.="</hbox>";
				}
			$xul.="</vbox>";
			
			$xul.="<vbox>";
				if($r["idRub"]!=$oidRub){
					$xul.="<label value='".$r["titreRub"]."'/>";
					$xul.="<hbox>";
						$xul.="<label id='adminRub_".$r["idRub"]."' class='text-linkAdmin' onclick=\"OuvreLienAdmin(".$r["idRub"].");\" value=\"Admin\"/>";
			    		$xul.="<!--<image onclick=\"SetVal('".$idDoc."');\" src='images/check_yes.png' /> -->";
			    		$xul.="<image onclick=\"DelRubrique('".$r["idRub"]."', '".$idRub."');\" src='images/check_no.png' />";
			    	$xul.="</hbox>";
				}
			$xul.="</vbox>";

			
			$xul.="<vbox hidden='true'>";
				if($r["idArt"]!=$oidArt){
					$xul.="<label value='".$r["titreArt"]."'/>";
					$xul.="<hbox>";
						$xul.="<label id='adminArt_".$r["idArt"]."' class='text-linkAdmin' onclick=\"OuvreArticle(".$r["idArt"].");\" value=\"Admin\"/>";
						$xul.="<image onclick=\"SetVal('".$idDoc."');\" src='images/check_yes.png' />";
			    		$xul.="<image onclick=\"SetVal('".$idDoc."');\" src='images/check_no.png' />";
			    	$xul.="</hbox>";
				}
			$xul.="</vbox>";
			
			
			$xul.="<vbox>";
				if($r["idCont"]!=$oidCont){
					$xul.="<hbox>";
						$xul.="<label value=\"Problème n ° ".$r["idPbPlan"]." : ".$this->site->XmlParam->XML_entities($r["TextCont"])."\"/>";
						$xul.="<label value='(".$r["idCont"].")'/>";
		    		$xul.="</hbox>";
				}
				$xul.="<hbox>";
					$xul.="<label value='    - ".$r["RepCont"]."'/>";
					$xul.="<label value='".$r["aDate"]."'/>";
					$xul.="<label value='Photo : ".$r["ReponsePhoto"]."'/>";
					$xul.="<label id='adminDon_".$r["idDon"]."' class='text-linkAdmin' onclick=\"OuvreDonnee(".$this->site->infos["GRILLE_SIG_PROB"].",".$r["idDon"].");\" value=\"Admin\"/>";
					$xul.="<image onclick=\"SetVal('".$idDoc."');\" src='images/check_yes.png' />";
		    		$xul.="<image onclick=\"DelArticle('".$r["idDon"]."', '".$idRub."');\" src='images/check_no.png' />";
		    	$xul.="</hbox>";
			$xul.="</vbox>";
			
			/*
			$idDoc=str_replace("-champ-","Modif",$idDoc);
			$xul.="<vbox>";
				$xul.="<label id='".$idDoc."' value='".$r["titreArt"]."' hidden='true' />";
			$xul.="</vbox>";
		    
		    $idDoc=str_replace("Modif","Sup",$idDoc);
			$xul.="<vbox>";
		    	$xul.="<label id='".$idDoc."' value='".$r["titreArt"]."' hidden='true' />";
		    	$xul.="<image onclick=\"SetVal('".$idDoc."');\" src='images/check_no.png' />";
			$xul.="</vbox>";
		    */
			
		    $xul.="</row>";	
			$oidRubPar=$r["idRubPar"];
			$oidRub=$r["idRub"];
			$oidArt=$r["idArt"];
			$oidCont=$r["idCont"];
		
		}
		//$xul .= "</treechildren></tree>";    
		//$xul .="</richlistbox>";
		$xul .='</rows>';	
		$xul .='</grid>';	
		
		
	   	return $xul;
    	
    }
    
    function GetTreeObs($idRub){
    	
    	$g = new Granulat($idRub,$this->site);
    	//récupère les rubriques enfants
    	$ids = str_replace(DELIM,",",$g->GetEnfantIds());
    	$ids .= "-1";
    	
		//récupère les identifiants des rubriques de la racine ayant un problème
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetListeObservations']";
		if($this->trace)
			echo "Grille:GetTreeObs:Xpath".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-ids-", $ids, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		//if($this->trace)
			echo "Grille:GetTreeObs:".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();
		
		$xul ='<grid flex="1">';
		//on cache la colonne de référence	
		$xul.='<columns>';	
			$xul.='<column flex="1" hidden="true"/>';	
			$xul.='<column flex="1"/>';
			$xul.='<column flex="1"/>';			
			$xul.='<column flex="1" hidden="true"/>';			
			$xul.='<column flex="1"/>';			
		$xul.='</columns>';	
		$xul.='<rows>';

		$oidRubPar=-1;
		$oidRub=-1;
		$oidArt=-1;
		
		while ($r =  $db->fetch_assoc($result)) {
			if($this->trace)
				echo "Grille:GetTreeObs:".$r["idRub"]." ".$r["idArt"]." ".$r["idDon"]."<br/>";

		if 	(!$r["ReponsePhoto"])	$r["ReponsePhoto"] = 'Non';
		$idDoc = 'val'.DELIM.$this->site->infos["GRILLE_OBS"].DELIM.$r["idDon"].DELIM."Sup".DELIM.$r["idArt"];
		$xul.="<row>";
			$xul.="<vbox hidden='true' >";
				$xul.="<label id='".$idDoc."' value='".$idDoc."' />";
			$xul.="</vbox>";

			$xul.="<vbox>";
				if($r["idRubPar"]!=$oidRubPar){
					$xul.="<label value='".$r["titreRubPar"]."'/>";
					$xul.="<hbox>";
						$xul.="<label id='adminRubPar_".$r["idRubPar"]."' class='text-linkAdmin' onclick=\"OuvreLienAdmin(".$r["idRubPar"].");\" value=\"Admin\"/>";
			    		$xul.="<!--<image onclick=\"SetVal('".$idDoc."');\" src='images/check_yes.png' /> -->";
			    		$xul.="<image onclick=\"DelRubriqueParentObs('".$r["idRubPar"]."');\" src='images/check_no.png' />";
			    	$xul.="</hbox>";
				}
			$xul.="</vbox>";
			
			$xul.="<vbox>";
				if($r["idRub"]!=$oidRub){
					$xul.="<label value='".$r["titreRub"]."'/>";
					$xul.="<hbox>";
						$xul.="<label id='adminRub_".$r["idRub"]."' class='text-linkAdmin' onclick=\"OuvreLienAdmin(".$r["idRub"].");\" value=\"Admin\"/>";
			    		$xul.="<!--<image onclick=\"SetVal('".$idDoc."');\" src='images/check_yes.png' /> -->";
			    		$xul.="<image onclick=\"DelRubriqueObs('".$r["idRub"]."', '".$idRub."');\" src='images/check_no.png' />";
			    	$xul.="</hbox>";
				}
			$xul.="</vbox>";

			
			$xul.="<vbox hidden='true'>";
				if($r["idArt"]!=$oidArt){
					$xul.="<label value='".$r["titreArt"]."'/>";
					$xul.="<hbox>";
						$xul.="<label id='adminArt_".$r["idArt"]."' class='text-linkAdmin' onclick=\"OuvreArticle(".$r["idArt"].");\" value=\"Admin\"/>";
						$xul.="<image onclick=\"SetVal('".$idDoc."');\" src='images/check_yes.png' />";
			    		$xul.="<image onclick=\"SetVal('".$idDoc."');\" src='images/check_no.png' />";
			    	$xul.="</hbox>";
				}
			$xul.="</vbox>";
			
			
			$xul.="<vbox>";
				if($r["idCont"]!=$oidCont){
					$xul.="<hbox>";
						$xul.="<label value=\"Problème n ° ".$r["idPbPlan"]." : ".$this->site->XmlParam->XML_entities($r["TextCont"])."\"/>";
						$xul.="<label value='(".$r["idCont"].")'/>";
						$xul.="<!--<label value=' Commentaires : ".$r["obs"]."'/> -->";
		    		$xul.="</hbox>";
				}
				if($r["obs"]!=$oidObs) {
					$xul.="<hbox>";
						$xul.="<label value=\" Commentaires : ".$this->site->XmlParam->XML_entities($r["obs"])."\"/>";
					$xul.="</hbox>";
				}
				$xul.="<hbox>";
					$xul.="<label value='    - ".$r["RepCont"]."'/>";
					$xul.="<label value='".$r["aDate"]."'/>";
					$xul.="<label value='Photo : ".$r["ReponsePhoto"]."'/>";
					$xul.="<label id='adminDon_".$r["idDon"]."' class='text-linkAdmin' onclick=\"OuvreDonnee(".$this->site->infos["GRILLE_SIG_PROB"].",".$r["idDon"].");\" value=\"Admin\"/>";
					$xul.="<image onclick=\"SetVal('".$idDoc."');\" src='images/check_yes.png' />";
		    		$xul.="<image onclick=\"DelArticleObs('".$r["idDon"]."', '".$idRub."');\" src='images/check_no.png' />";
		    	$xul.="</hbox>";
			$xul.="</vbox>";
						
		    $xul.="</row>";	
			$oidRubPar=$r["idRubPar"];
			$oidRub=$r["idRub"];
			$oidArt=$r["idArt"];
			$oidCont=$r["idCont"];
			$oidObs=$r["obs"];
		}

		$xul .='</rows>';	
		$xul .='</grid>';	
		
		
	   	return $xul;
    	
    }
    
    function GetObjId($donId,$obj) {
		if($this->trace)
			echo "Grille:GetObjId://récupère l'identifiant de l'objet ".$obj." ".$donId."<br/>";

		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetId".$obj."']";
    	
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $donId, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		$row =  $db->fetch_assoc($rows);
		return $row["id"];
    	
    }
        
    function GereWorkflow($row, $donId) {

    	$xul="";
    	$Xpath = "/XmlParams/XmlParam/workflow[@srcId='".$row['grille'].";".$row['champ']."']";
		if($this->trace)
			echo "Grille:GereWorkflow:récupère les paramètre du workflow à exécuter ".$Xpath."<br/>";
    	$wfs = $this->site->XmlParam->GetElements($Xpath);
    	
    	if(!$wfs) return;

    	foreach($wfs as $wf)
		{
			//vérifie s'il faut récupérer l'identifiant de l'objet de destination
			if($wf['dstObj'])
				$id = $this->GetObjId($donId,$wf['dstObj']);

			switch ($wf['dstQuery']) {
				case "ShowArtGrille":
					if($this->trace)
						echo "Grille:GereWorkflow:".$wf['dstQuery']."==".$donId."<br/>";					
					//récupère le formulaire xul
					$xul = $this->GetXulForm($donId,$this->site->infos["GRILLE_SIG_PROB"]);
					break;	
				case "AddNewTab":
					$xul = $this->GetXulTabPanels($row['idRub'],$this->site->infos["GRILLE_SIG_PROB"],"SignalementProbleme");
					break;	
				case "AddNewArtGrille":
					if($this->trace)
						echo "Grille:GereWorkflow:AddNewArtGrille ".$row['valeur']."==".$wf['srcCheckVal']."<br/>";					
					if($row['valeur']==$wf['srcCheckVal']){						
						//récupération du granulat
						$gra = new Granulat($id,$this->site);
						
						if($wf['trsObjet']=="controles" ){
							$gTrs = new Granulat($wf['trsId'],$this->site);
							$id = $gra->SetNewEnfant($gTrs->titre." ".date('j/m/y - H:i:s'));
							$this->AddQuestionReponse($wf['trsId'],$id);
							if($wf['trsId']==$this->site->infos["RUB_PORTE1"] 
								|| $wf['trsId']==$this->site->infos["RUB_PORTE1"] )
									{ // Porte
								$id1 = $gra->SetNewEnfant("Face 1 ".date('j/m/y - H:i:s'));
								$this->AddQuestionReponse($this->site->infos["RUB_PORTE_FACE1"],$id1);
								$id2 = $gra->SetNewEnfant("Face 2 ".date('j/m/y - H:i:s'));
								$this->AddQuestionReponse($this->site->infos["RUB_PORTE_FACE2"],$id2);
							}
						}else{
							//gestion pour le signalement problème
							if($wf['trsId']==$this->site->infos["GRILLE_SIG_PROB"]){
								$ref = $this->GetValeur($donId,"ligne_1");
								$reponseId = $this->GetValeur($donId,"mot_1");
								$reponse = $this->GetMot($reponseId);
								
								$idArt = $gra->SetNewArticle("Problème ".$ref." ".date('j/m/y - H:i:s'));
								//ajoute une nouvelle donnee
								$idDon = $this->AddDonnee($id, $wf['trsId'], false, $idArt);
								if($this->trace)
									echo "Grille:GereWorkflow://gestion pour le signalement problème ".$ref."<br/>";
								$row=array("champ"=>"ligne_3","valeur"=>$ref);
								$this->SetChamp($row,$idDon);
								$row2=array("champ"=>"ligne_5","valeur"=>$reponse);
								$this->SetChamp($row2,$idDon);
							}else{
								if($wf['trsId']==$this->site->infos["GRILLE_OBS"]){
									$ref = $this->GetValeur($donId,"ligne_1");
									$reponseId = $this->GetValeur($donId,"mot_1");
									$reponse = $this->GetMot($reponseId);
									
									$idArt = $gra->SetNewArticle("Observations ".$ref." ".date('j/m/y - H:i:s'));
									//ajoute une nouvelle donnee
									$idDon = $this->AddDonnee($id, $wf['trsId'], false, $idArt);
									if($this->trace)
										echo "Grille:GereWorkflow://gestion pour les observations ".$ref."<br/>";
									$row=array("champ"=>"ligne_5","valeur"=>$ref);
									$this->SetChamp($row,$idDon);
									$row2=array("champ"=>"ligne_1","valeur"=>$reponse);
									$this->SetChamp($row2,$idDon);
								} else {
									$idArt = $gra->SetNewArticle($gTrs->titre." ".date('j/m/y - H:i:s'));
									//ajoute une nouvelle donnee
									$idDon = $this->AddDonnee($id, $wf['trsId'], false, $idArt);	
								}
							}
							//récupère le formulaire xul
							$xul = $this->GetXulForm($idDon,$wf['trsId']);
						}
						//renvoie le formulaire
						return $xul;
					}
					break;	
				case "AddNewMotClef":	
					if($this->trace)
						echo "Grille:GereWorkflow:AddNewMotClef ".$row['valeur']."==".$wf['srcCheckVal']."<br/>";	
					if($row['valeur']==$wf['srcCheckVal']){	
						$gra = new Granulat($id,$this->site);	
						if($wf['trsObjet']=="motclef" ){
							$gra->SetMotClef($wf['trsId'],$id);
						}	
					}
					break;	
				case "ShowDonnee":	
					if($wf['trsId']==$this->site->infos["GRILLE_SIG_PROB"] || $wf['trsId']==$this->site->infos["GRILLE_OBS"]) {
						//récupère le formulaire xul
						$xul = $this->GetXulForm($donId,$wf['trsId']);
					}
					return $xul;
					break;	
				default:								
					if($this->trace)
						echo "//workflow path query ".$wf['dstQuery']."<br/>";
					
					$Q = $this->site->XmlParam->GetElements($wf['dstQuery']);
					$where = str_replace("-id-", $id, $Q[0]->where);
					$set = str_replace("-param-", $row['valeur'], $Q[0]->set);
					$sql = $Q[0]->update.$set.$where;
					$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
					$db->connect();
					$db->query($sql);
					$db->close();
					if($this->trace)
						echo "//exécution du workflow ".$sql."<br/>";
				break;
			}								
		}
		
		if($this->trace)
			echo "Grille:GereWorflow:xul=".$xul."<br/>";
		return $xul;
		
	}	

	function GetMot($idMot) {
		
		//récupère la valeur d'un champ
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetMot']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $idMot, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		$row =  $db->fetch_assoc($rows);
		return $row['titre'];
		
	}
	
	function GetIdMot($titre) {
		//récupère la valeur d'un champ
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetIdMot']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-titre-", $titre, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		$row =  $db->fetch_assoc($rows);
		return $row['id_mot'];
		
	}
	
	function GetGrilleId($rows, $donId) {

    	$Xpath = "/XmlParams/XmlParam/majliee[@srcId='55;ligne_1']/@dstQuery";
		$donnees = $xml->GetElements($Xpath);
		if($this->trace)
			echo "//récupération des valeurs de workflow ".$donnees."<br/>";
    	
		//suppression des éventuelle champ pour la donnée
		$this->DelDonnee($donId);
		
		//création des valeurs
		while ($row =  mysql_fetch_assoc($rows)) {
			$this->SetChamp($row, $donId, false);
			//echo "--- ".$donId." nouvelle valeur ".$i;
		}
		
	}	
	
	function AddXmlDonnee($xmlSrc){
			
		if($this->trace)
			echo "Grille/AddXmlDonnee IN //récuparation de la définition des données ".$xmlSrc."<br/>";
		$xml = new XmlParam($xmlSrc);		
		
		$Xpath = "/donnees";
		$donnees = $xml->GetElements($Xpath);
		if($this->trace)
			echo "Grille/AddXmlDonnee/récupération des valeurs de donnée ".$donnees."<br/>";
		
		$idGrille = $donnees[0]->grille;
		if($this->trace)
			echo "Grille/AddXmlDonnee/récupération de l'identifiant de la grille ".$idGrille."<br/>";
		
		//récupération de la définition des champs
		$Xpath = "/donnees/champs";
		$champs = $xml->GetElements($Xpath);
		$first=true;
		foreach($donnees[0]->donnee as $donnee)
		{
			$idRub = $donnee->rub;
			if($this->trace)
				echo "Grille/AddXmlDonnee/- récupération de l'identifiant de la rubrique ".$idRub."<br/>";
			
			//récuparation du granulat
			$g = new Granulat($idRub, $this->site); 
			$idArt = $g->GetArticle();
			if($this->trace)
				echo "Grille/AddXmlDonnee/- récupération ou création du dernier article en cours de rédaction ".$idArt."<br/>";
			
			if($first){
				$this->DelGrilleArt($idGrille,$idArt);
				if($this->trace)
					echo "Grille/AddXmlDonnee/suppression des anciennes données ".$idArt."<br/>";
				$first=false;
			}
				
			$idDon = $g->GetIdDonnee($idGrille, $idArt, true);
			if($this->trace)
				echo "Grille/AddXmlDonnee/- création de la donnee ".$idDon."<br/>";

			$i=0;
			foreach($donnee->valeur as $valeur)
			{
				if($valeur!='non'){
					$valeur=utf8_decode($valeur);
					$champ = $champs[0]->champ[$i];
					if($this->trace)
						echo "Grille/AddXmlDonnee/--- gestion des champs multiples ".substr($champ,0,8)."<br/>";
					if(substr($champ,0,8)=="multiple"){
						$valeur=$champ;
						//attention il ne doit pas y avoir plus de 10 choix
						$champ=substr($champ,0,-2);
					}
					if($this->trace)
						echo "Grille/AddXmlDonnee/-- récupération du type de champ ".$champ."<br/>";
					$row = array('champ'=>$champ, 'valeur'=>$valeur);
					if($this->trace)
						echo "Grille/AddXmlDonnee/-- récupération de la valeur du champ ".$valeur."<br/>";
					$this->SetChamp($row, $idDon,false);
					if($this->trace)
						echo "Grille/AddXmlDonnee/--- création du champ <br/>";
				}
				$i++;
			}
			
		}
		if($this->trace)
			echo "Grille/AddXmlDonnee OUT //<br/>";
		
	}
    
    function AddGrilles($idRubSrc, $idRubDst, $redon=false){
			
		//récuparation des grilles des articles publiés de la rubrique
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetGrillesPublie']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idRub-", $idRubSrc, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		if($this->trace)
			echo "AddGrilles ".$idRubSrc." ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		
		$result = ""; 
		while ($row =  $db->fetch_assoc($rows)) {
			$idDon = $this->AddDonnee($idRubDst, $row["id_form"], $redon);
			$result .= $row["id_form"]." ".$row["titre"]." ".$idDon."<br/>";		
		}
		
		return $result;
	}
	
	
	function AddQuestionReponse($idRubSrc, $idRubDst){
		
		//création du granulat
		$g = new Granulat($idRubDst,$this->site);
					
		//pour les controles récupération des rubriques dans les liens de la rubrique Src 
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetRubInLiens']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idRub-", $idRubSrc, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		if($this->trace)
			echo "Grille:AddQuestionReponse:rubSrc".$sql."<br/>";
		
		//récupération du droit de la dernière donnée pour la rubrique parente de la destination
		$droit = $this->GetDroitParent($g->IdParent);
				
		while ($row =  $db->fetch_assoc($rows)) {		
			//récupération des questions publié pour un type de controle
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_AddQuestion']";
			$Q = $this->site->XmlParam->GetElements($Xpath);
			$where = str_replace("-idRubSrc-", $row['idRub'], $Q[0]->where);
			$sql = $Q[0]->select.$Q[0]->from.$where;
			$dbQ = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
			$dbQ->connect();
			$rowsQ = $dbQ->query($sql);
			$dbQ->close();
			if($this->trace)
				echo "Grille:AddQuestionReponse:Liste question".$sql."<br/>";
			$first=true;
			$rowQo = -1;
			while ($rowQ =  $dbQ->fetch_assoc($rowsQ)) {
				if($first){
					//ajoute le mot clef type de controle à la rubrique
					$g->SetMotClef($rowQ["typecon"]);
					$first=false;
				}
				//vérifie si le contrôle est cohérent par rapport au parent
				if($this->GereCoheDroit($rowQ, $droit)){
					//prise en compte des doublons suite à l'attribution de plusieurs droits
					if($rowQo != $rowQ["ref"]){
						//ajoute une nouvelle donnée réponse pour la question
						$idDon = $g->GetIdDonnee($rowQ["FormRep"],-1,true);
						if($this->trace)
							echo "Grille:AddQuestionReponse:ajoute une nouvelle donnée réponse pour la question".$idDon."<br/>";
						//ajoute la question
						$r = array("champ"=>"ligne_2","valeur"=>$rowQ["question"]);
						$this->SetChamp($r,$idDon,false);
						//ajoute la référence
						$r = array("champ"=>"ligne_1","valeur"=>$rowQ["ref"]);
						$this->SetChamp($r,$idDon,false);
						//ajoute la réponse par défaut
						$r = array("champ"=>"mot_1","valeur"=>$rowQ["valdef"]);
						$this->SetChamp($r,$idDon,false);					
						$rowQo = $rowQ["ref"];
					}
				}
			}
		}
		
	}
	
	function GereCoheDroit($rQ, $droit){

		return true;
		
		//vérifie si la question est cohérente par rapport au questionnaire parent
		//$Xpath = "/XmlParams/XmlParam/CoheDroit[@srcId='".$rQ['id_form'].";".$row['droit']."']";
		$Xpath = "/XmlParams/XmlParam/CoheDroit[@dstId='".$rQ['id_form']."' and @dstCheckVal='".$rQ['droit']."' and @srcCheckVal='".$droit."' ]";
		if($this->trace)
			echo "Grille:GereCoheDroit:récupère la cohérence ".$Xpath."<br/>";
    	$coh = $this->site->XmlParam->GetCount($Xpath);
		if($this->trace)
			echo "Grille:GereCoheDroit:coh=".$coh."<br/>";
    
    	if($coh>0)
    		$cohe=true;
    	else
    		$cohe=false;
		return $cohe;
	}

	function GetDroitParent($id){
		//récupération des droits pour la rubrique parente
		$rParDon = $this->GetLastDonne($id);

		//récupère le champ droit de la donnée du parent
		$Xpath = "/XmlParams/XmlParam/CoheDroit[@srcId='".$rParDon['id_form']."']/@srcChamp";
    	$srcChamps = $this->site->XmlParam->GetElements($Xpath);
		$srcChamp = $srcChamps[0];
		
		//récupère la valeur du champ droit
		$droit = $this->GetValeur($rParDon['id_donnee'], $srcChamp);
		
		return $droit;
	}

	function GetValeur($idDon, $champ){
		//récupère la valeur d'un champ
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetValeurChamp']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $idDon, $Q[0]->where);
		$where = str_replace("-champ-", $champ, $where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		$row =  $db->fetch_assoc($rows);
		return $row['valeur'];
	}
	
	function GetLastDonne($id){
		//récupération de la dernière donnée d'une rubriques 
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetLastDonnee']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $id, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		$row =  $db->fetch_assoc($rows);
		return $row;
	}
	
	function AddDonnee($idRub, $idGrille=-1, $redon=false, $idArt=-1){
		
		if($idGrille==-1)
			$idGrille=$this->id;
			
		//récuparation du granulat
		$g = new Granulat($idRub, $this->site);
		
		if($idArt==-1)
			//"récupération ou création du dernier article en cours de rédaction"; 
			$idArt = $g->GetArticle(" AND a.statut='prepa'");
				
		if($redon){
			//récupère les dernières données publiées
			$g = new Granulat($redon, $this->site);
			$rows = $g->GetGrille($idGrille, " AND a.statut='publie'");
			$oDonnee="";
			while ($row =  mysql_fetch_assoc($rows)) {
				//vérifie s'il on change de donnee
				if($row["id_donnee"]!=$oDonnee){
					$idDon = $g->GetIdDonnee($idGrille, $idArt, true);
					$oDonnee=$row["id_donnee"];
				}
				$this->SetChamp($row, $idDon, false);
				//echo "--- ".$donId." nouvelle valeur ".$i;
			}
		}else{
			//récupération ou création d'une nouvelle donnée
			$idDon = $g->GetIdDonnee($idGrille, $idArt);
			//récupère la définition des champs sans valeur
			$rows = $this->GetChamps($idGrille);
			//initialisation de la donnée
			$this->SetChamps($rows, $idDon);
		}

		//echo "idRub = ".$idRub." idArt = ".$idArt." idDon = ".$idDon."<br/>"; 
		return $idDon;
	
	}

	function GetChamps($idGrille=-1){
	
		if($idGrille==-1)
			$idGrille=$this->id;

		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetChamps']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idGrille-", $idGrille, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		//echo $sql."<br/>";
		
		return $result;
	
	}
	
	function SetChamps($rows, $donId) {

		//suppression des éventuelle champ pour la donnée
		$this->DelDonnee($donId);
		
		//création des valeurs
		while ($row =  mysql_fetch_assoc($rows)) {
			$this->SetChamp($row, $donId, false);
			//echo "--- ".$donId." nouvelle valeur ".$i;
		}
		
	}	
	  
	function DelDonnee($donId) {

		//Supression des valeurs de champ
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_DelDonnee']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idDon-", $donId, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		if($this->trace)
			echo "Grille:DelDonnee:".$sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		
	}	

	function DelGrilleDonnee($donId) {

		//Supression des valeurs de champ
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_DelGrilleDonnee']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idDon-", $donId, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		if($this->trace)
			echo "Grille:DelGrilleDonnee:".$sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		
	}	
	
	function DelGrilleArt($idGrille, $idArt) {

		if($this->trace)
			echo "Grille:DelGrilleArt:GetDonneeArtForm $idGrille, $idArt<br/>";
		//récupération des données pour un article et une grille
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonneeArtForm']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idArt-", $idArt, $Q[0]->where);
		$from = str_replace("-idGrille-", $idGrille, $Q[0]->from);
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		if($this->trace)
			echo "Grille:DelGrilleArt:GetDonneeArtForm=".$sql."<br/>";
		//echo $sql."<br/>";
		while ($r =  $db->fetch_assoc($result)) {
			//Supression des valeurs de champ
			$this->DelDonnee($r["id_donnee"]);
			//suppression des donnee
			$this->DelGrilleDonnee($r["id_donnee"]);
		}
		
	}	
	
	function SetChamp($row, $donId, $del=true) {

		if($del)
			//supression de la valeur
			$this->DelChamp($row, $donId); 
		
		//prise en compte des choix multiple
		if($row["valeur"]=="supprime")
			return;
		
		//création de la valeur
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_InsChamp']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$values = str_replace("-idDon-", $donId, $Q[0]->values);
		$values = str_replace("-champ-", $row["champ"], $values);
		$values = str_replace("'-val-'", $this->site->GetSQLValueString($row["valeur"],"text"), $values);
		$sql = $Q[0]->insert.$values;
		if($this->trace)
			echo $this->site->infos["SQL_DB"]." ".$sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		//echo "--- ".$donId." nouvelle valeur ".$i;
		
	}	
		
	function DelChamp($row, $donId) {

		//supression de la valeur
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_DelChamp']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idDon-", $donId, $Q[0]->where);
		$where = str_replace("-champ-", $row["champ"], $where);
		//prise en compte des choix multiples
		if(substr($row['champ'], 0, 8)=='multiple')
			$where = str_replace("-choix-", $row["valeur"], $where);
		else
			$where = str_replace("AND valeur = '-choix-'", "", $where); 
		
		$sql = $Q[0]->select.$Q[0]->from.$where;
		if($this->trace)
			echo $sql." ".substr($row['champ'], 0, 8)."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		//echo "--- ".$donId." nouvelle valeur ".$i;
		
	}	
	
	function GetXulTab($src, $id, $dst="Rub", $recur = false){

		//chaque ligne est un onglet
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetXulTabForm".$dst."']";
		if($this->trace)
			echo "GetXulTab Xpath".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $id, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "GetXulTab ".$dst." ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();

		//initialisation de la tabbox
		$tabbox = '<tabbox flex="1" id="tabbox_'.$src.'_'.$dst.'_'.$id.'">';
		$tabbox .= '<tabs>';
		$i=0;
		while ($r =  $db->fetch_assoc($result)) {
			$tabbox .= '<tab id="tab'.$r["id"].'" label="'.$r["titre"].'" />';
			//vérifie s'il faut créer un formulaire ou un sous onglet
			if($Q[0]->dst=='Form')
				$tabpanels .= $this->GetXulTabPanels($r["idArt"], $r["id"],'Form',$recur);
			else
				$tabpanels .= $this->GetXulTabPanels($src, $r["id"],$Q[0]->dst,$recur);
			$i++;
		}
		
		//prise en compte des onglets liés par le workflow
		$row = array("idRub"=>$id,"grille"=>"GetXulTabForm","champ"=>$dst);
		$WFtabpanels = $this->GereWorkflow($row,-1);
		if($WFtabpanels!=""){
			$tabbox .= '<tab id="tabWF'.$r["id"].'" label="Signalement(s) problème(s)" />';
			
		}
		
		
		if($i!=0){
			$tabbox .= '</tabs>';
			$tabbox .= '<tabpanels>';
			$tabbox .= $tabpanels;
			$tabbox .= $WFtabpanels;
			$tabbox .= '</tabpanels>';
			$tabbox .= '</tabbox>';
		}else
			$tabbox = "";
			
		return $tabbox;
		
	}


	function GetXulTabPanels($src, $id, $dst="Rub", $recur = false){

		//récupère les articles de la rubrique
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetXulTabPanels".$dst."']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $id, $Q[0]->where);
		$where = str_replace("-src-", $src, $where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		if($this->trace)
			echo "GetXulTabPanels ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$req = $db->query($sql);
		$db->close();

		//initialisation du panel
		$tabpanel = '<tabpanel flex="1" id="tabpanel_'.$src.'_'.$dst.'_'.$id.'">';	
		
		//ajoute les onglets des sous rubriques
		if($recur)
			$tabpanel .= $this->GetXulTab($src, $id, $dst, $recur);
		
		//ajoute les groupbox pour chaque article
		if($id==$this->site->infos["GRILLE_REP_CON"]){
			$tabpanel .='<grid flex="1">';
			//on cache la colonne de référence	
			$tabpanel .='<columns>';	
			$tabpanel .='<column hidden="true"/>';	
			$tabpanel .='<column flex="1"/>';
			$tabpanel .='<column />';			
			$tabpanel .='</columns>';	
			$tabpanel .='<rows>';	
			$tabpanel .='<row><label value="Référence" hidden="true" /><label value="Question"/><label value="Réponse"/></row>';	
		}
		if($id==$this->site->infos["GRILLE_SIG_PROB"]){
			$tabpanel .='<vbox flex="1">';
		}
		while($r = $db->fetch_assoc($req)) {
			//$tabpanel .= '<groupbox >';	
			//$tabpanel .= '<caption label="'.$r["titre"].'"/>';
			if($Q[0]->dst=='Form'){
				//pour le signalement d'un problème
				if($id==$this->site->infos["GRILLE_SIG_PROB"]){
					$tabpanel .='<hbox>';
					$tabpanel .='<vbox>';
					//ajoute le nom de l'article 
					$tabpanel .='<label value="'.$r["titre"].'" />';
					//ajoute la carte 
					$tabpanel .= $this->GetXulCarto(-1,$src);
					$tabpanel .='</vbox>';
					//ajoute les données de chaque article
					$tabpanel .= $this->GetXulForm($r["id"], $id);
					$tabpanel .='</hbox>';
				}else{
					//ajoute les données de chaque article
					$tabpanel .= $this->GetXulForm($r["id"], $id);
				}
			}else{
				//ajoute la tabbox de destination
				$tabpanel .= $this->GetXulTab($src, $r["id"], $Q[0]->dst, $recur);	
			}
		}
		if($id==$this->site->infos["GRILLE_REP_CON"]){
			$tabpanel .='</rows>';	
			$tabpanel .='</grid>';	
		}
		if($id==$this->site->infos["GRILLE_SIG_PROB"]){
			$tabpanel .='</vbox>';
		}
		$tabpanel .= '</tabpanel>';

		return $tabpanel;
	}

  	function GetRubDon($idDon) {
  
  
		//requête pour récupérer la rubrique de la donnée
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetRubDon']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $idDon, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		if($this->trace)
			echo "GetRubDon ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		$r = $db->fetch_assoc($req);
		
		return $r["id"];
		
		
	}
	
			
  function GetXulForm($idDon, $idGrille) {
  
  
		//requête pour récupérer les données de la grille
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $idDon, $Q[0]->where);
		$where = str_replace("-idGrille-", $idGrille, $where);
		
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		if($this->trace)
			echo "GetXulForm ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		
		//ajoute les controls pour chaque grille
		If($idGrille==$this->site->infos["GRILLE_REP_CON"]){
			$form = '<row>';	
		}else{
			$form = '<grid flex="1">';	
			$form .= '<columns flex="1">';	
			$labels = '<column flex="1" align="end">';	
			//$form .= '<caption label="Donnée : '.$idDon.'"/>';
			$controls = '<column flex="1">';
		}
		$oChamp = "";
		while($r = $db->fetch_assoc($req)) {
			
			$idDoc = 'val'.DELIM.$idGrille.DELIM.$r["id_donnee"].DELIM.$r["champ"].DELIM.$r["id_article"];
			if($this->trace)
				echo "GetXulForm/construction de l'identifiant ".$idDoc."<br/>";
			switch ($idGrille) {
				case $this->site->infos["GRILLE_REG_LEG"]:
					//construstion de la règle législative
					$labels .= '<label class="labelForm" control="first" multiligne="true" value="'.$r['titre'].'"/>';
					$controls .= $this->GetXulRegLeg($idDoc, $r);
					break;					
				default:
					if($this->trace)
						echo "GetXulForm //prise en compte des champs multiples ".$oChamp." MultiSelect=".$MultiSelect."<br/>";
					if($oChamp == $r['champ']){
						if($this->trace)
							echo "GetXulForm affiche le nouveau champ<br/>";
						$controls .= $this->GetXulControl($idDoc, $r);
						//conserve la valeur
						$MultiSelect .= "'".$r['valeur']."',";
					}else{
						$labels .= '<label class="labelForm" control="first" multiligne="true" value="'.$r['titre'].'"/>';
						if(substr($r['champ'], 0, 8)=='multiple'){
							if($this->trace)
								echo "GetXulForm début construction du multiple<br/>";
							$controls .= '<groupbox id="'.$id.'" '.$js.' >';
							$controls .= '<hbox>';
							//affiche le bouton sélecionné
							$controls .= $this->GetXulControl($idDoc, $r);
							//conserve la valeur
							$MultiSelect .=  "'".$r['valeur']."',";
						}else{
							//vérifie si la ligne précédente était multiple
							if($MultiSelect!=""){
								//récupère les multiples non sélectionné
								$controls .= $this->GetXulControl($idDoc, $r,substr($MultiSelect,0,-1));
								//fin du multiselect
								$controls .= '</hbox>';
								$controls .= '</groupbox>';
								$MultiSelect = "";
							}else{
								$controls .= $this->GetXulControl($idDoc, $r);
							}
						}
						//conserve la ligne pour la fin
						$lastRow = $r;
						$oChamp = $r['champ'];						
					}
			}
		}
		if($this->trace)
			echo "GetXulForm // FIN prise en compte des champs multiples ".$oChamp." MultiSelect=".$MultiSelect."<br/>";
		if($MultiSelect!=""){
			//récupère les multiple non sélectionné
			$controls .= $this->GetXulControl($idDoc, $lastRow, substr($MultiSelect,0,-1));
			//fin du multiselect
			$controls .= '</hbox>';
			$controls .= '</groupbox>';
		}
		If($idGrille!=$this->site->infos["GRILLE_REP_CON"]){
			$controls .= '</column>';	
			$labels .= '</column>';	
			$form .= $labels.$controls.'</columns>';
		}
		if($idGrille == $this->site->infos["GRILLE_GEO"]){
			$form .= '<groupbox >';	
			$form .= '<caption label="Cartographie"/>';
			//ajoute la carte
			$form .= $this->GetXulCarto($idDon);
			$form .= '</groupbox>';
		}
		
		If($idGrille==$this->site->infos["GRILLE_REP_CON"])
			$form .= $controls.'</row>';	
		else
			$form .= '</grid>';	

		return $form;
	
	}
	
	function GetXulCarto($idDon,$idRub=-1)
	{
		$xul="";
		if($idRub!=-1)
			$xul = "<iframe height='300px' width='350px' src='".$this->site->infos["urlCarto"]."?id=".$idRub."'  id='BrowerGlobal' />";
		else
			$xul = "<iframe height='550px' width='450px' src='".$this->site->infos["urlCarto"]."?id=".$this->GetRubDon($idDon)."'  id='BrowerGlobal' />";
		
		return	$xul;	
	
	}

	function GetXulRegLeg($id, $row)
	{
		
		/*résultat de row
		champ 	rang 	titre 	type 	obligatoire 	extra_info 	
		ligne_1 	6 	valeur étalon 	ligne 	  	  	  	  	  	 
		ligne_2 	7 	valeur étalon 2 	ligne 	  	  	  	  	  	 
		ligne_3 	4 	Nom de la valeur 	ligne 	  	  	  	  	  	 
		mot_1 	5 	opérateur 		mot 	18 	  	  	  	 
		mot_2 	8 	Unités 		mot 	19 	  	  	  	 
		select_1 	9 	règle respectée 	select radio		
		*/
		
		switch ($row['champ']) {
			case 'ligne_1':
				//récupération des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control = '<textbox '.$js.' id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row['valeur']).'" />';
				$control .= '<label id="trace'.$id.'" value=""/>';
				break;
			case 'ligne_2':
				//récupération des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control = '<textbox '.$js.' id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row['valeur']).'" />';
				$control .= '<label id="trace'.$id.'" value=""/>';
				break;
			case 'ligne_3':
				//construction du control
				$control = '<label value="'.$this->site->XmlParam->XML_entities($row["valeur"]).'"/>';			
				break;
			case 'mot_1':
				$mot = New MotClef($row["valeur"], $this->site);
				$control = '<label value="'.$this->site->XmlParam->XML_entities($mot->titre).'"/>';			
				break;
			case 'mot_2':
				$mot = New MotClef($row["valeur"], $this->site);
				$control = '<label value="'.$this->site->XmlParam->XML_entities($mot->titre).'"/>';			
				break;
			case 'select_1':
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='radio']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control .= '<groupbox>';
				$control .= '<caption label="'.$row['titre'].'"/>';
				$control .= '<radiogroup id="'.$id.'" '.$js.' >';
				$control .= '<hbox>';
				$control .= $this->GetChoixVal($row);
				$control .= '<label id="trace'.$id.'" value=""/>';
				$control .= '</hbox>';
				$control .= "</radiogroup>";
				$control .= '</groupbox>';
				break;
		}

		return $control;
	
	}
	
	function GetXulControl($id, $row, $MultiSelect="")
	{
		$control = '';
		switch ($row['type']) {
			case 'multiple':
				if($this->trace)
					echo "GetXulControl MultiSelect=".$MultiSelect."<br/>";
				$id = 'val'.DELIM.$row["grille"].DELIM.$row["id_donnee"].DELIM.$row["champ"].DELIM.$row["id_article"].DELIM.$r['choix'];
				$control .= $this->GetChoixVal($row,'multiple',$MultiSelect);
				break;
			case 'select':
				//prise en compte de l'affichage liste
				if($row['extra_info']=="liste"){
					//récupération des js
					$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='menu']";
					$js = $this->site->GetJs($Xpath, array($id));
					//construction du control
					$control .= '<menulist id="'.$id.'" '.$js.' ><menupopup >';
					$control .= $this->GetChoixVal($row,'menuitem');				
					$control .= '</menupopup></menulist>';
				}else{				
					//récupération des js
					$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='radio']";
					$js = $this->site->GetJs($Xpath, array($id));
					//construction du control
					$control .= '<groupbox>';
					//$control .= '<caption label="'.$row['titre'].'"/>';
					$control .= '<radiogroup id="'.$id.'" '.$js.' >';
					$control .= '<hbox>';
					$control .= $this->GetChoixVal($row);
					$control .= '</hbox>';
					$control .= "</radiogroup>";
					$control .= '</groupbox>';
				}
				break;
			case 'mot':
				//récupération des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='menu']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control .= '<menulist id="'.$id.'" '.$js.' ><menupopup >';
				$control .= $this->GetChoixVal($row,'menuitem');				
				$control .= '</menupopup></menulist>';
				break;
			case 'fichier':
				//récupération des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='fichier']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control .= '<button id="btn'.$id.'" label="Parcourir" '.$js.' />';
				//récupération des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				$control .= '<textbox  '.$js.' multiline="true" id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row["valeur"]).'"/>';			
				break;
			default:
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				if($row["grille"]==$this->site->infos["GRILLE_REP_CON"]){
					//on cache le textbox référence
					if($row["champ"]=="ligne_1")
						$control .= '<textbox  '.$js.' hidden="true" multiline="true" id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row["valeur"]).'"/>';			
					else
						$control .= '<textbox  '.$js.' multiline="true" id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row["valeur"]).'"/>';			
				}else{
					$control .= '<textbox '.$js.' id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row['valeur']).'" />';
				}
		}	
		
		$control .= '<label id="trace'.$id.'" hidden="true" value=""/>';

		return $control;

	}

	function GetChoixVal($row,$type='radio',$multiSelect="")
	{
		//requête pour récupérer les données de la grille
		if($multiSelect!="")
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetChoix".$row['type']."NotIn']";
		else
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetChoix".$row['type']."']";
		
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $row['grille'], $Q[0]->where);
		$where = str_replace("-champ-", $row['champ'], $where);
		$where = str_replace("-extra_info-", $row['extra_info'], $where);
		$where = str_replace("-valeur-", $row['valeur'], $where);
		$where = str_replace("-multiSelect-", $multiSelect, $where);
				
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		if($this->trace)
			echo "GetChoixVal ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->connect();
		$req = $db->query($sql);
		$db->close();

		$control = "";
		while($r = $db->fetch_assoc($req)) {
			$select = 'false';
			if($row['valeur']==$r['choix'])
				$select = 'true';
			
			if($this->trace)
				echo "extra_info ".$row['extra_info']." type ".$type." "."select ".$select." ".$row['valeur']."==".$r['choix']."<br/>";
						
			switch ($type) {
				case 'radio':
					$control .= "<radio id='".$r['choix']."' selected='".$select."' label=\"".$this->site->XmlParam->XML_entities($r["titre"])."\"/>";
					break;
				case 'menuitem':
					$control .= "<menuitem id='".$r['choix']."' value='".$r['choix']."' selected='".$select."' label=\"".$this->site->XmlParam->XML_entities($r['titre'])."\"/>";
					break;
				case 'multiple':
					if($multiSelect=="")
						$select = 'true';
					//récupération des js
					$id = 'val'.DELIM.$row["grille"].DELIM.$row["id_donnee"].DELIM.$row["champ"].DELIM.$row["id_article"].DELIM.$r['choix'];
					$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='multiple']";
					$js = $this->site->GetJs($Xpath, array($id));
					$control .= "<checkbox ".$js." id='".$id."' checked='".$select."' label=\"".$this->site->XmlParam->XML_entities($r['titre'])."\"/>";
					break;
			}
		}
		
		return $control;

	}
	
  }


?>