<?php

session_start();

extract($_SESSION,EXTR_OVERWRITE);

class Grille{
  public $id;
  public $XmlParam;
  public $XmlScena;
  public $trace;
  public $titre;
  private $site;

  function __tostring() {
    return "Cette classe permet de d�finir et manipuler des grilles.<br/>";
    }

  function __construct($site, $id=-1, $complet=true) {
	//echo "new Site $sites, $id, $scope<br/>";
	$this->trace = TRACE;

    $this->site = $site;
    $this->id = $id;
	if($this->site->scope["FicXml"]!=-1)
		$this->XmlParam = new XmlParam($this->site->scope["FicXml"]);
	$this->XmlScena = new XmlParam(XmlScena);
		
	if($complet){
		$this->GetProps();
	}

	//echo "FIN new grille <br/>";
		
    }

	public function GetEtatDiagListe($ids, $idDoc)
	{
		//r�cup�re les info de l'id xul
		$arrDoc = split("_",$idDoc);
		
		if($arrDoc[0]==0){
			//r�cup�re les crit�re suivant leur validation
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetEtatDiagOuiListe']";
			$champ = $this->site->infos["CHAMPS_CONTROL_DEFFICIENCE"]["champ"];
			$valeur = $this->site->infos["CHAMPS_CONTROL_DEFFICIENCE"]["valeur"][$arrDoc[1]];
		}else{
			//r�cup�re les crit�re suivant leur validation
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetEtatDiagHandiListe']";
			$champ = $this->site->infos["CHAMPS_CONTROL_DIAG"][$arrDoc[1]];
			$valeur = $arrDoc[0];
		}

		if($this->trace)
			echo "Grille:GetEtatDiagListe:Xpath".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-ids-", $ids, $Q[0]->where);
		$from = str_replace("-idFormRep-", $this->site->infos["GRILLE_REP_CON"], $Q[0]->from);
		$from = str_replace("-idFormCont-", $this->site->infos["GRILLE_CONTROL_".$_SESSION['version']], $from);
		$from = str_replace("-valeur-", $valeur, $from);
		$from = str_replace("-champ-", $champ, $from);
		
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "GetEtatDiagListe".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();
			
		//construction du xul
		$xul = "<vbox flex='1'>";
		while ($r =  $db->fetch_assoc($result)) {
				$xul .= '<textbox  multiline="true" id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($r['affirm']).'"/>';			
				$xul .= $this->GetXulLegendeControle($r['idDonCont'],$this->site->infos["GRILLE_CONTROL_".$_SESSION['version']]);
		}
		$xul .= "</vbox>";
		
		return $xul;
	}
    
    
    public function GetEtatDiagOui($ids)
	{
		//r�cup�re le nombre de crit�res valid�s
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetEtatDiagOui']";
		if($this->trace)
			echo "Grille:GetEtatDiagOui:Xpath".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-ids-", $ids, $Q[0]->where);
		$from = str_replace("-idFormRep-", $this->site->infos["GRILLE_REP_CON"], $Q[0]->from);
		$from = str_replace("-idFormCont-", $this->site->infos["GRILLE_CONTROL_".$_SESSION['version']], $from);
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "Grille:GetEtatDiagOui".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();
			
		//construction du xml
		$r =  $db->fetch_assoc($result);
		$xml = "<CritsValides id='0_' moteur='".$r['moteur']."' audio='".$r['audio']."' visu='".$r['visu']."' cog='".$r['cog']."' ></CritsValides>";
		
		return $xml;
	}

	public function GetEtatDiagHandi($ids,$handi)
	{
		//r�cup�re le nombre de crit�res valid�s
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetEtatDiagHandi']";
		if($this->trace)
			echo "Grille:GetEtatDiagHandi:Xpath".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-ids-", $ids, $Q[0]->where);
		$from = str_replace("-idFormRep-", $this->site->infos["GRILLE_REP_CON"], $Q[0]->from);
		$from = str_replace("-idFormCont-", $this->site->infos["GRILLE_CONTROL_".$_SESSION['version']], $from);
		$from = str_replace("-handi-", $handi, $from);
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "Grille:GetEtatDiagHandi".$this->site->infos["SQL_DB"]." ".$sql."<br/>";
		$db->close();
			
		//construction du xml
		$r =  $db->fetch_assoc($result);
		$xml = "<Obstacles id='".$handi."_' moteur='".$r['moteur']."' audio='".$r['audio']."' visu='".$r['visu']."' cog='".$r['cog']."' ></Obstacles>";
		if($this->trace)
			echo "Grille:GetEtatDiagHandi:r=".print_r($r)."<br/>";
		
		return $xml;
	}
	
    
	public function GetProps()
	{
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$DB->connect();
		if($this->trace)
			echo "//charge les propi�t�s de la grille $this->id -<br/>";
		$sql = "SELECT titre
			FROM spip_forms 
			WHERE id_form = ".$this->id;
		//echo $sql."<br/>";
		$req = $DB->query($sql);
		$DB->close();
		$data = $DB->fetch_assoc($req);
		$this->titre = $data['titre'];

	}
    
    function GetTreeProb($idRub){
    	
    	$g = new Granulat($idRub,$this->site);
    	//r�cup�re les rubriques enfants
    	$ids = str_replace(DELIM,",",$g->GetEnfantIds());
    	$ids .= "-1";
    	
		//r�cup�re les identifiants des rubriques de la racine ayant un probl�me
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetListeSignalementProbleme']";
		if($this->trace)
			echo "Grille:GetTreeProb:Xpath".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-ids-", $ids, $Q[0]->where);
		$from = str_replace("-idFormRep-", $this->site->infos["GRILLE_REP_CON"], $Q[0]->from);
		$from = str_replace("-idFormCont-", $this->site->infos["GRILLE_CONTROL_".$_SESSION['version']], $from);
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
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
		//on cache la colonne de r�f�rence	
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
				<label value='R�f�rence' />
			    <label value='Rubrique parente'/>
			    <label value='Rubrique'/>
			    <label value='Article'/>
			    <label value='N� probl�me'/>
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
					$xul.="<label value=\"".$this->site->XmlParam->XML_entities($r["titreRubPar"])."\"/>";
					$xul.="<hbox>";
						$xul.="<label id='adminRubPar_".$r["idRubPar"]."' class='text-linkAdmin' onclick=\"OuvreLienAdmin(".$r["idRubPar"].");\" value=\"Admin\"/>";
			    		$xul.="<!--<image onclick=\"SetVal('".$idDoc."');\" src='images/check_yes.png' /> -->";
			    		$xul.="<image onclick=\"DelRubriqueParent('".$r["idRubPar"]."');\" src='images/check_no.png' />";
			    	$xul.="</hbox>";
				}
			$xul.="</vbox>";
			
			$xul.="<vbox>";
				if($r["idRub"]!=$oidRub){
					$xul.="<label value=\"".$this->site->XmlParam->XML_entities($r["titreRub"])."\"/>";
					$xul.="<hbox>";
						$xul.="<label id='adminRub_".$r["idRub"]."' class='text-linkAdmin' onclick=\"OuvreLienAdmin(".$r["idRub"].");\" value=\"Admin\"/>";
			    		$xul.="<!--<image onclick=\"SetVal('".$idDoc."');\" src='images/check_yes.png' /> -->";
			    		$xul.="<image onclick=\"DelRubrique('".$r["idRub"]."', '".$idRub."');\" src='images/check_no.png' />";
			    	$xul.="</hbox>";
				}
			$xul.="</vbox>";

			
			$xul.="<vbox hidden='true'>";
				if($r["idArt"]!=$oidArt){
					$xul.="<label value=\"".$this->site->XmlParam->XML_entities($r["titreArt"])."\"/>";
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
						$xul.="<label value=\"Probl�me n � ".$r["idPbPlan"]." : ".$this->site->XmlParam->XML_entities($r["TextCont"])."\"/>";
						$xul.="<label class='text-linkAdmin' onclick=\"OuvreControle(".$r["idDonneCont"].");\" value='(".$r["idCont"].")'/>";
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
    
    function GetTableauBord($idRub){
    	
    	$g = new Granulat($idRub,$this->site);
    	//r�cup�re les rubriques enfants
    	$ids = str_replace(DELIM,",",$g->GetEnfantIds());
    	$ids .= "-1";
    	
		//r�cup�re les identifiants des rubriques de la racine ayant un probl�me
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetListeSignalementProbleme']";
		if($this->trace)
			echo "Grille:GetTableauBord:Xpath".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-ids-", $ids, $Q[0]->where);
		$from = str_replace("-idFormRep-", $this->site->infos["GRILLE_REP_CON"], $Q[0]->from);
		$from = str_replace("-idFormCont-", $this->site->infos["GRILLE_CONTROL_".$_SESSION['version']], $from);
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "Grille:GetTableauBord:".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();
		
		$xul ='<grid flex="1">';
		//on cache la colonne de r�f�rence	
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
				echo "Grille:GetTableauBord:".$r["idRub"]." ".$r["idArt"]." ".$r["idDon"]."<br/>";

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
			    		$xul.="<image onclick=\"DelRubriqueParentObs('".$r["idRubPar"]."');\" src='images/check_no.png' />";
			    	$xul.="</hbox>";
				}
			$xul.="</vbox>";
			
			$xul.="<vbox>";
				if($r["idRub"]!=$oidRub){
					$xul.="<label value='".$r["titreRub"]."'/>";
					$xul.="<hbox>";
						$xul.="<label id='adminRub_".$r["idRub"]."' class='text-linkAdmin' onclick=\"OuvreLienAdmin(".$r["idRub"].");\" value=\"Admin\"/>";
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
						$xul.="<label value=\"Probl�me n � ".$r["idPbPlan"]." : ".$this->site->XmlParam->XML_entities($r["TextCont"])."\"/>";
						$xul.="<label   value='(".$r["idCont"].")'/>";
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
    

    function GetListeChamp($idGrille=-1){
    	
    	if($idGrille==-1)
    		$idGrille=$this->id;
    	
		$sql = "SELECT fc.titre, fc.champ
				FROM spip_forms_champs fc 
				WHERE fc.id_form = ".$idGrille;
			
		$DB = new mysql($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$req = $DB->query($sql);
		$DB->close();
		
		return $req;	
    	
    }
    
    function GetTreeObs($idRub){
    	
    	$g = new Granulat($idRub,$this->site);
    	//r�cup�re les rubriques enfants
    	$ids = str_replace(DELIM,",",$g->GetEnfantIds());
    	$ids .= "-1";
    	
		//r�cup�re les identifiants des rubriques de la racine ayant un probl�me
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetListeObservations']";
		if($this->trace)
			echo "Grille:GetTreeObs:Xpath".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-ids-", $ids, $Q[0]->where);
		$from = str_replace("-idFormRep-", $this->site->infos["GRILLE_REP_CON"], $Q[0]->from);
		$from = str_replace("-idFormCont-", $this->site->infos["GRILLE_CONTROL_".$_SESSION['version']], $from);
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "Grille:GetTreeObs:".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();
		
		$xul ='<grid flex="1">';
		//on cache la colonne de r�f�rence	
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
						$xul.="<label value=\"Probl�me n � ".$r["idPbPlan"]." : ".$this->site->XmlParam->XML_entities($r["TextCont"])."\"/>";
						$xul.="<label   value='(".$r["idCont"].")'/>";
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
    
	function GetTreeCsv($idRub){
    	
    	$g = new Granulat($idRub,$this->site);
    	//r�cup�re les rubriques enfants
    	$ids = str_replace(DELIM,",",$g->GetEnfantIds());
    	$ids .= "-1";
    	
		//r�cup�re les identifiants des rubriques de la racine ayant un probl�me
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetListeSignalementProbleme']";
		if($this->trace)
			echo "Grille:GetTreeCsv:Xpath".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-ids-", $ids, $Q[0]->where);
		$from = str_replace("-idFormRep-", $this->site->infos["GRILLE_REP_CON"], $Q[0]->from);
		$from = str_replace("-idFormCont-", $this->site->infos["GRILLE_CONTROL_".$_SESSION['version']], $from);
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "Grille:GetTreeCsv:".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();
		
		header("Content-Type: application/csv-tab-delimited-table"); // text/csv
		header("Content-disposition: attachment; filename=SignalementPb.csv"); 
		header('Expires: 0');
		header('Pragma: no-cache'); 
		
		echo 'Rubrique Parent;Rubrique;Id Pb;Questions Probl�me;Crit�re r�glementaire;Date;Observations';
		echo "\n";
		
		while ($r =  $db->fetch_assoc($result)) {
			if($this->trace)
				echo "Grille:GetTreeCsv:".$r["idRub"]." ".$r["idArt"]." ".$r["idDon"]."<br/>";

			echo $r["titreRubPar"].";";
			echo $r["titreRub"].';';
			echo $r["idPbPlan"].';';
			$text = html_entity_decode($this->site->XmlParam->XML_entities($r["TextCont"]));
			echo str_replace(';', ',', $text).';';
			//echo $r["idCont"].';';
			//crit�re r�glementaire
			if($r["regle"])
				echo 'oui;';
			else
				echo 'non;';
			echo $r["aDate"].';';
			$textObs = html_entity_decode($r["obs"]);
			echo str_replace(';', ',', $textObs).';';
			echo "\n" ;	
		}    	
    }
    
    function GetObjId($donId,$obj) {
		if($this->trace)
			echo "Grille:GetObjId://r�cup�re l'identifiant de l'objet ".$obj." ".$donId."<br/>";

		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetId".$obj."']";
    	
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $donId, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		$row =  $db->fetch_assoc($rows);
		return $row["id"];
    	
    }

    function GereScenarisation($row, $donId) {

    	$xul="";
		$critere = $this->GetValeur($donId,'ligne_1'); 
    	$Xpath = "//question[@id='".$critere."']";
    	if($this->trace)
			echo "Grille:GereScenarisation:r�cup�re les param�tre � ex�cuter ".$Xpath."<br/>";
    	$scena = $this->XmlScena->GetElements($Xpath);
    	
    	if(!$scena) return;
    	
		$idArt = $this->GetObjId($donId,'Article');
    	
    	foreach($scena as $qi)
		{
			//r�cup�re l'id li�e au crit�re
			if($qi["reponse"]==$row["valeur"]){
		    	foreach($qi as $q)
				{
					$critere = $q["id"];
					$idDon = $this->GetDonneeCritere($idArt,$critere);
					$xul .= $this->GetXulForm($idDon,$row["grille"]);
				}
			}
		}
    	return $xul;
    }
    
    function GereWorkflow($row, $donId) {

    	$xul="";
    	$Xpath = "/XmlParams/XmlParam/workflow[@srcId='".$row['grille'].";".$row['champ']."']";
		if($this->trace)
			echo "Grille:GereWorkflow:r�cup�re les param�tre du workflow � ex�cuter ".$Xpath."<br/>";
    	$wfs = $this->site->XmlParam->GetElements($Xpath);
    	
    	if(!$wfs) return;

    	foreach($wfs as $wf)
		{
			//v�rifie s'il faut r�cup�rer l'identifiant de l'objet de destination
			if($wf['dstObj'])
				$id = $this->GetObjId($donId,$wf['dstObj']);

			switch ($wf['dstQuery']) {
				case "ShowArtGrille":
					if($this->trace)
						echo "Grille:GereWorkflow:".$wf['dstQuery']."==".$donId."<br/>";					
					//r�cup�re le formulaire xul
					$xul = $this->GetXulForm($donId,$this->site->infos["GRILLE_SIG_PROB"]);
					break;	
				case "AddNewTab":
					$xul = $this->GetXulTabPanels($row['idRub'],$this->site->infos["GRILLE_SIG_PROB"],"SignalementProbleme");
					break;	
				case "AddNewArtGrille":
					if($this->trace)
						echo "Grille:GereWorkflow:AddNewArtGrille ".$row['valeur']."==".$wf['srcCheckVal']."<br/>";					
					if($row['valeur']==$wf['srcCheckVal']){						
						//r�cup�ration du granulat
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
							//gestion pour le signalement probl�me
							if($wf['trsId']==$this->site->infos["GRILLE_SIG_PROB"]){
								$ref = $this->GetValeur($donId,"ligne_1");
								$reponseId = $this->GetValeur($donId,"mot_1");
								$reponse = $this->GetMot($reponseId);
								
								$idArt = $gra->SetNewArticle("Probl�me ".$ref." ".date('j/m/y - H:i:s'));
								//ajoute une nouvelle donnee
								$idDon = $this->AddDonnee($id, $wf['trsId'], false, $idArt);
								if($this->trace)
									echo "Grille:GereWorkflow://gestion pour le signalement probl�me ".$ref."<br/>";
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
							//r�cup�re le formulaire xul
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
						//r�cup�re le formulaire xul
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
					$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
					$db->connect();
					$db->query($sql);
					$db->close();
					if($this->trace)
						echo "//ex�cution du workflow ".$sql."<br/>";
				break;
			}								
		}
		
		if($this->trace)
			echo "Grille:GereWorflow:xul=".$xul."<br/>";
		return $xul;
		
	}	

	function GetMot($idMot) {
		
		//r�cup�re la valeur d'un champ
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetMot']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $idMot, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		$row =  $db->fetch_assoc($rows);
		return $row['titre'];
		
	}
	
	function GetIdMot($titre) {
		//r�cup�re la valeur d'un champ
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetIdMot']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-titre-", $titre, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
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
			echo "//r�cup�ration des valeurs de workflow ".$donnees."<br/>";
    	
		//suppression des �ventuelle champ pour la donn�e
		$this->DelDonnee($donId);
		
		//cr�ation des valeurs
		while ($row =  mysql_fetch_assoc($rows)) {
			$this->SetChamp($row, $donId, false);
			//echo "--- ".$donId." nouvelle valeur ".$i;
		}
		
	}	
	
	function AddXmlDonnee($xmlSrc){
			
		if($this->trace)
			echo "Grille/AddXmlDonnee IN //r�cuparation de la d�finition des donn�es ".$xmlSrc."<br/>";
		$xml = new XmlParam($xmlSrc);		
		
		$Xpath = "/donnees";
		$donnees = $xml->GetElements($Xpath);
		if($this->trace)
			echo "Grille/AddXmlDonnee/r�cup�ration des valeurs de donn�e ".$donnees."<br/>";
		
		$idGrille = $donnees[0]->grille;
		if($this->trace)
			echo "Grille/AddXmlDonnee/r�cup�ration de l'identifiant de la grille ".$idGrille."<br/>";
		
		//r�cup�ration de la d�finition des champs
		$Xpath = "/donnees/champs";
		$champs = $xml->GetElements($Xpath);
		$first=true;
		foreach($donnees[0]->donnee as $donnee)
		{
			$idRub = $donnee->rub;
			if($this->trace)
				echo "Grille/AddXmlDonnee/- r�cup�ration de l'identifiant de la rubrique ".$idRub."<br/>";
			
			//r�cuparation du granulat
			$g = new Granulat($idRub, $this->site); 
			$idArt = $g->GetArticle();
			if($this->trace)
				echo "Grille/AddXmlDonnee/- r�cup�ration ou cr�ation du dernier article en cours de r�daction ".$idArt."<br/>";
			
			if($first){
				$this->DelGrilleArt($idGrille,$idArt);
				if($this->trace)
					echo "Grille/AddXmlDonnee/suppression des anciennes donn�es ".$idArt."<br/>";
				$first=false;
			}
				
			$idDon = $g->GetIdDonnee($idGrille, $idArt, true);
			if($this->trace)
				echo "Grille/AddXmlDonnee/- cr�ation de la donnee ".$idDon."<br/>";

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
						echo "Grille/AddXmlDonnee/-- r�cup�ration du type de champ ".$champ."<br/>";
					$row = array('champ'=>$champ, 'valeur'=>$valeur);
					if($this->trace)
						echo "Grille/AddXmlDonnee/-- r�cup�ration de la valeur du champ ".$valeur."<br/>";
					$this->SetChamp($row, $idDon,false);
					if($this->trace)
						echo "Grille/AddXmlDonnee/--- cr�ation du champ <br/>";
				}
				$i++;
			}
			
		}
		if($this->trace)
			echo "Grille/AddXmlDonnee OUT //<br/>";
		
	}
    
    function AddGrilles($idRubSrc, $idRubDst, $redon=false){
			
		//r�cuparation des grilles des articles publi�s de la rubrique
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetGrillesPublie']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idRub-", $idRubSrc, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
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
		
		//cr�ation du granulat
		$g = new Granulat($idRubDst,$this->site);
					
		//pour les controles r�cup�ration des rubriques dans les liens de la rubrique Src 
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetRubInLiens']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idRub-", $idRubSrc, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		if($this->trace)
			echo "Grille:AddQuestionReponse:rubSrc".$sql."<br/>";
		
		//r�cup�ration du droit de la derni�re donn�e pour la rubrique parente de la destination
		$droit = $this->GetDroitParent($g->IdParent);
				
		while ($row =  $db->fetch_assoc($rows)) {		
			//r�cup�ration des questions publi� pour un type de controle
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_AddQuestion']";
			$Q = $this->site->XmlParam->GetElements($Xpath);
			$where = str_replace("-idRubSrc-", $row['idRub'], $Q[0]->where);
			//ajoute les crit�re de version
			$from = str_replace("-idForm-", $this->site->infos["GRILLE_CONTROL_".$_SESSION['version']], $Q[0]->from);			
			$sql = $Q[0]->select.$from.$where;
			$dbQ = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			$dbQ->connect();
			$rowsQ = $dbQ->query($sql);
			$dbQ->close();
			if($this->trace)
				echo "Grille:AddQuestionReponse:Liste question".$sql."<br/>";
			$first=true;
			$rowQo = -1;
			while ($rowQ =  $dbQ->fetch_assoc($rowsQ)) {
				if($first){
					//ajoute le mot clef type de controle � la rubrique
					$g->SetMotClef($rowQ["typecon"]);
					$first=false;
				}
				//v�rifie si le contr�le est coh�rent par rapport au parent
				if($this->GereCoheDroit($rowQ, $droit)){
					//prise en compte des doublons suite � l'attribution de plusieurs droits
					if($rowQo != $rowQ["ref"]){
						//ajoute une nouvelle donn�e r�ponse pour la question
						$idDon = $g->GetIdDonnee($rowQ["FormRep"],-1,true);
						if($this->trace)
							echo "Grille:AddQuestionReponse:ajoute une nouvelle donn�e r�ponse pour la question".$idDon."<br/>";
						//ajoute la question
						$r = array("champ"=>"ligne_2","valeur"=>$rowQ["question"]);
						$this->SetChamp($r,$idDon,false);
						//ajoute la r�f�rence
						$r = array("champ"=>"ligne_1","valeur"=>$rowQ["ref"]);
						$this->SetChamp($r,$idDon,false);
						//ajoute la r�ponse par d�faut
						$r = array("champ"=>"mot_1","valeur"=>$rowQ["valdef"]);
						$this->SetChamp($r,$idDon,false);
						//ajoute la donn�e r�f�rente
						$r = array("champ"=>"ligne_3","valeur"=>$rowQ["id_donnee"]);
						$this->SetChamp($r,$idDon,false);
								
						$rowQo = $rowQ["ref"];
					}
				}
			}
		}
		
	}
	
	function GereCoheDroit($rQ, $droit){

		return true;
		
		//v�rifie si la question est coh�rente par rapport au questionnaire parent
		//$Xpath = "/XmlParams/XmlParam/CoheDroit[@srcId='".$rQ['id_form'].";".$row['droit']."']";
		$Xpath = "/XmlParams/XmlParam/CoheDroit[@dstId='".$rQ['id_form']."' and @dstCheckVal='".$rQ['droit']."' and @srcCheckVal='".$droit."' ]";
		if($this->trace)
			echo "Grille:GereCoheDroit:r�cup�re la coh�rence ".$Xpath."<br/>";
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
		//r�cup�ration des droits pour la rubrique parente
		$rParDon = $this->GetLastDonne($id);

		//r�cup�re le champ droit de la donn�e du parent
		$Xpath = "/XmlParams/XmlParam/CoheDroit[@srcId='".$rParDon['id_form']."']/@srcChamp";
    	$srcChamps = $this->site->XmlParam->GetElements($Xpath);
		$srcChamp = $srcChamps[0];
		
		//r�cup�re la valeur du champ droit
		$droit = $this->GetValeur($rParDon['id_donnee'], $srcChamp);
		
		return $droit;
	}

	function GetValeur($idDon, $champ){
		//r�cup�re la valeur d'un champ
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetValeurChamp']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $idDon, $Q[0]->where);
		$where = str_replace("-champ-", $champ, $where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		$row =  $db->fetch_assoc($rows);
		return $row['valeur'];
	}
	
	function GetLastDonne($id){
		//r�cup�ration de la derni�re donn�e d'une rubriques 
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetLastDonnee']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $id, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		$row =  $db->fetch_assoc($rows);
		return $row;
	}

	function GetDonneeCritere($idArt,$critere){
		//pour la sc�narisarisation
		//r�cup�ration de la donn�e d'un article correspondant au crit�re 
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetDonneeCritere']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idArt-", $idArt, $Q[0]->where);
		$from = str_replace("-critere-", $critere, $Q[0]->from);
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		$row =  $db->fetch_assoc($rows);
		return $row['id_donnee'];
	}
	
	
	function AddDonnee($idRub, $idGrille=-1, $redon=false, $idArt=-1){
		
		if($idGrille==-1)
			$idGrille=$this->id;
			
		//r�cuparation du granulat
		$g = new Granulat($idRub, $this->site);
		
		if($idArt==-1)
			//"r�cup�ration ou cr�ation du dernier article en cours de r�daction"; 
			$idArt = $g->GetArticle(" AND a.statut='prepa'");
				
		if($redon){
			//r�cup�re les derni�res donn�es publi�es
			$g = new Granulat($redon, $this->site);
			$rows = $g->GetGrille($idGrille, " AND a.statut='publie'");
			$oDonnee="";
			while ($row =  mysql_fetch_assoc($rows)) {
				//v�rifie s'il on change de donnee
				if($row["id_donnee"]!=$oDonnee){
					$idDon = $g->GetIdDonnee($idGrille, $idArt, true);
					$oDonnee=$row["id_donnee"];
				}
				$this->SetChamp($row, $idDon, false);
				//echo "--- ".$donId." nouvelle valeur ".$i;
			}
		}else{
			//r�cup�ration ou cr�ation d'une nouvelle donn�e
			$idDon = $g->GetIdDonnee($idGrille, $idArt);
			//r�cup�re la d�finition des champs sans valeur
			$rows = $this->GetChamps($idGrille);
			//initialisation de la donn�e
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
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		//echo $sql."<br/>";
		
		return $result;
	
	}
	
	function SetChamps($rows, $donId) {

		//suppression des �ventuelle champ pour la donn�e
		$this->DelDonnee($donId);
		
		//cr�ation des valeurs
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
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
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
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		
	}	
	
	function DelGrilleArt($idGrille, $idArt) {

		if($this->trace)
			echo "Grille:DelGrilleArt:GetDonneeArtForm $idGrille, $idArt<br/>";
		//r�cup�ration des donn�es pour un article et une grille
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonneeArtForm']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idArt-", $idArt, $Q[0]->where);
		$from = str_replace("-idGrille-", $idGrille, $Q[0]->from);
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
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
		
		//cr�ation de la valeur
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_InsChamp']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$values = str_replace("-idDon-", $donId, $Q[0]->values);
		$values = str_replace("-champ-", $row["champ"], $values);
		$values = str_replace("'-val-'", $this->site->GetSQLValueString($row["valeur"],"text"), $values);
		$sql = $Q[0]->insert.$values;
		if($this->trace)
			echo $this->site->infos["SQL_DB"]." ".$sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
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
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
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
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		$db->connect();
		$result = $db->query($sql);
		if($this->trace)
			echo "GetXulTab ".$dst." ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->close();

		//initialisation de la tabbox
		$tabbox = '<tabbox flex="1" id="tabbox_'.$src.'_'.$dst.'_'.$id.'">';
		$tabbox .= '<tabs>';
		$i=0;
		$tabpanels ="";
		while ($r =  $db->fetch_assoc($result)) {
			//on exclu les grille g�o
			if($r["id"]!=$this->site->infos["GRILLE_GEO"]){
				$tabbox .= '<tab id="tab'.$r["id"].'" label="'.$r["titre"].'" />';
				//v�rifie s'il faut cr�er un formulaire ou un sous onglet
				if($Q[0]->dst=='Form' )
					$tabpanels .= $this->GetXulTabPanels($r["idArt"], $r["id"],'Form',$recur);
				else
					$tabpanels .= $this->GetXulTabPanels($src, $r["id"],$Q[0]->dst,$recur);
			}
			$i++;
		}
		
		//prise en compte des onglets li�s par le workflow
		$row = array("idRub"=>$id,"grille"=>"GetXulTabForm","champ"=>$dst);
		$WFtabpanels = $this->GereWorkflow($row,-1);
		if($WFtabpanels!=""){
			$tabbox .= '<tab id="tabWF'.$r["id"].'" label="Signalement(s) probl�me(s)" />';
			
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

		//on n'affiche pas les grille g�olo
		if($id == $this->site->infos["GRILLE_GEO"])
			return;
			
		//r�cup�re les articles de la rubrique
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetXulTabPanels".$dst."']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $id, $Q[0]->where);
		$where = str_replace("-src-", $src, $where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
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
			//on cache la colonne de r�f�rence	
			$tabpanel .='<columns>';	
			$tabpanel .='<column hidden="true"/>';	
			$tabpanel .='<column flex="1"/>';
			$tabpanel .='<column />';			
			$tabpanel .='<column />';			
			$tabpanel .='</columns>';	
			$tabpanel .='<rows>';	
			$tabpanel .='<row><label value="R�f�rence" hidden="true" /><label value="Question"/><label value="R�ponse"/><label value="Observations"/></row>';	
		}
		if($id==$this->site->infos["GRILLE_SIG_PROB"]){
			$tabpanel .='<vbox flex="1">';
		}
		while($r = $db->fetch_assoc($req)) {
			//$tabpanel .= '<groupbox >';	
			//$tabpanel .= '<caption label="'.$r["titre"].'"/>';
			if($Q[0]->dst=='Form'){
				//ex�cution suivant les type de grille
				switch ($id) {
					case $this->site->infos["GRILLE_GEO"]:
						$tabpanel .= "";
						break;
					case $this->site->infos["GRILLE_SIG_PROB"]:
						$tabpanel .='<hbox>';
						$tabpanel .='<vbox>';
						//ajoute le nom de l'article 
						$tabpanel .='<label value="'.$r["titre"].'" />';
						//ajoute la carte 
						$tabpanel .= $this->GetXulCarto(-1,$src);
						$tabpanel .='</vbox>';
						//ajoute les donn�es de chaque article
						$tabpanel .= $this->GetXulForm($r["id"], $id);
						$tabpanel .='</hbox>';
						break;
					case $this->site->infos["GRILLE_REP_CON"]:
						$verif = $this->VerifChoixDiagnostic($r["id"], $_SESSION['type_controle'], $_SESSION['type_contexte']); 
						if ($verif) {							
							$tabpanel .= $this->GetXulForm($r["id"], $id);
						}
						break;
					default:
						//v�rifie s'il faut afficher une carte
						$idDon = $this->VerifDonneeLienGrille($r["id"],$this->site->infos["GRILLE_GEO"]);
						if($idDon){
							$tabpanel .= $this->GetXulForm($r["id"], $id);
							$tabpanel .= $this->GetXulForm($idDon, $this->site->infos["GRILLE_GEO"]);
						}else
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


	function VerifDonneeLienGrille($idDon,$idGrille){
		
		//v�rifie si une grille est dans l'article de la donnee
		//dans le cas o� la donnee est d'une autre grille que celle recherchh�e
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_VerifDonneeLienGrille']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idDon-", $idDon, $Q[0]->where);
		$from = str_replace("-idGrille-", $idGrille, $Q[0]->from);
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		if($this->trace)
			echo "VerifDonneeLienGrille ".$this->site->infos["SQL_DB"]." ".$sql."<br/>";
		$req = $db->query($sql);
		$r = $db->fetch_assoc($req);
		$db->close();
		
		if($r['idDonV'])
			return $r['idDonV'];
		else
			return false;
	}
	
	function VerifQuestionIntermediaire($critere){
		
		$Xpath = "/questionnaire/grille/question[@id='".$critere."']";
		$Q = $this->XmlScena->GetElements($Xpath);
		if($Q)
			return true;
		else
			return false;
	}
		
	function VerifChoixDiagnostic ($id, $typeCritere, $typeContexte){
		
		// On r�cupere le critere corespondant � la donn�e (grille 59 Diagnostic)
		$critere = $this->GetValeur($id,'ligne_1'); 

		//v�rifie s'il faut traiter les questions interm�diaires pour V2
		if($_SESSION['version']=="V2"){
			if(!$this->VerifQuestionIntermediaire($critere))
				return false;
		}
		//si aucun contexte ou crit�re n'est saisi on renvoie toute les questions
		if(!$typeContexte || !$typeCritere)
			return true;
		if(!$typeContexte[0] 
			&& !$typeContexte[1]
			&& !$typeContexte[2]
			&& !$typeContexte[3]
			&& !$typeCritere[0]
			&& !$typeCritere[1]
			)
			return true;	
				
		if($this->trace)
			echo "Grille:VerifChoixDiagnostic:On r�cupere la donn�e corespondant au critere (grille ".$this->site->infos["GRILLE_CONTROL_".$_SESSION['version']]." Controle)<br/>";
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonneeCritere']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$from = str_replace("-critere-", $critere, $Q[0]->from);
		$where = str_replace("-idForm-", $this->site->infos["GRILLE_CONTROL_".$_SESSION['version']], $Q[0]->where);				
		$sql = $Q[0]->select.$from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		if($this->trace)
			echo "VerifChoixDiagnostic ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		
		if ($r = $db->fetch_assoc($req)) {
			if($this->trace)
				echo "Grille:VerifChoixDiagnostic: On recupere la valeur du type de critere propre � la donn�e (multiple_1 reglementaire ou souhaitable)<br/>";
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonneeChoix']";
			$Q = $this->site->XmlParam->GetElements($Xpath);
			$where = str_replace("-id-", $r['idDonnee'], $Q[0]->where);
					
			$sql = $Q[0]->select.$Q[0]->from.$where.$Q[0]->and_multiple1;
			$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
			if($this->trace)
				echo "VerifChoixDiagnostic ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
			$db->connect();
			$req = $db->query($sql);
			$db->close();
			
			if ($r = $db->fetch_assoc($req)) {
				if($this->trace)
					echo "Grille:VerifChoixDiagnostic:typeCritere[0]=".$typeCritere[0]." typeCritere[1]=".$typeCritere[1]." valeur=".$r['valeur']."<br/>";
				
				//v�rifie les crit�res r�gl�mentaires souhaitables
				if(($typeCritere[0]== $r['valeur'] || $typeCritere[1]== $r['valeur']) ) 
					$ok = $r['valeur'];
				else 
					return false;
				
				
				//v�rifie le contexte r�gl�mentaire uniquement dans le cas des crit�res r�gl�mentaires 
				if ($ok =='multiple_1_1') {
					// On recupere la valeur du type de droit r�gelementaire (multiple_2)
					$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonneeChoix']";
					$Q = $this->site->XmlParam->GetElements($Xpath);
					$where = str_replace("-id-", $r['idDonnee'], $Q[0]->where);
							
					$sql = $Q[0]->select.$Q[0]->from.$where.$Q[0]->and_multiple2;
					$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
					if($this->trace)
						echo "VerifChoixDiagnostic ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
					$db->connect();
					$req = $db->query($sql);
					$db->close();
					
					while ($r = $db->fetch_assoc($req)) {
						if($typeContexte[0]== $r['valeur'] 
							|| $typeContexte[1]== $r['valeur'] 
							|| $typeContexte[2]== $r['valeur'] 
							|| $typeContexte[3]== $r['valeur']) 
							return true;
					}
					return false;
				}
				if($this->trace)
					echo "Grille:VerifChoixDiagnostic:ok=".$ok."<br/>";
				return true;
			} 	
		}
		if($this->trace)
			echo "Grille:VerifChoixDiagnostic:END<br/>";
		return false;
	}
	
  	function GetRubDon($idDon) {
  
  
		//requ�te pour r�cup�rer la rubrique de la donn�e
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetRubDon']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $idDon, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		if($this->trace)
			echo "GetRubDon ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		$r = $db->fetch_assoc($req);
		
		return $r["id"];
		
		
	}
	
			
  function GetXulForm($idDon, $idGrille,$qi="") {
  
  
		//requ�te pour r�cup�rer les donn�es de la grille
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $idDon, $Q[0]->where);
		$where = str_replace("-idGrille-", $idGrille, $where);
		
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		if($this->trace)
			echo "GetXulForm ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		
		$labels ="";
		$controls="";
		//ajoute les controls pour chaque grille
		if($idGrille==$this->site->infos["GRILLE_REP_CON"]){
			$form = '<row id="row_'.$idGrille.'_'.$idDon.'" >';	
		}else{
			$form = '<grid flex="1">';	
			$form .= '<columns flex="1">';	
			$labels = '<column flex="1" align="end">';	
			//$form .= '<caption label="Donn�e : '.$idDon.'"/>';
			$controls = '<column flex="1">';
		}
		$oChamp = "";
		$MultiSelect = "";
		while($r = $db->fetch_assoc($req)) {
						
			$idDoc = 'val'.DELIM.$idGrille.DELIM.$r["id_donnee"].DELIM.$r["champ"].DELIM.$r["id_article"];
			if($this->trace)
				echo "GetXulForm/construction de l'identifiant ".$idDoc."<br/>";
			switch ($idGrille) {
				case $this->site->infos["GRILLE_REG_LEG"]:
					//construstion de la r�gle l�gislative
					$labels .= '<label class="labelForm" control="first" multiligne="true" value="'.$r['titre'].'"/>';
					$controls .= $this->GetXulRegLeg($idDoc, $r);
					break;					
				case $this->site->infos["GRILLE_GEO"]:
					//on ne construit pas la grille GEO
					$labels .= '';
					$controls .= '';
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
								echo "GetXulForm d�but construction du multiple<br/>";
							$controls .= '<groupbox id="'.$id.'" '.$js.' >';
							$controls .= '<hbox>';
							//affiche le bouton s�lecionn�
							$controls .= $this->GetXulControl($idDoc, $r);
							//conserve la valeur
							$MultiSelect .=  "'".$r['valeur']."',";
						}else{
							//v�rifie si la ligne pr�c�dente �tait multiple
							if($MultiSelect!=""){
								//r�cup�re les multiples non s�lectionn�
								$controls .= $this->GetXulControl($idDoc, $r,substr($MultiSelect,0,-1));
								//fin du multiselect
								$controls .= '</hbox>';
								$controls .= '</groupbox>';
								$MultiSelect = "";
							}else{
								//v�rifie s'il faut ajouter la l�gende de la donn�e dans la liste des r�ponses
								if($idGrille== $this->site->infos["GRILLE_REP_CON"]
									&& $r['champ']=="ligne_3")
									$controls .= $this->GetXulLegendeControle($r['valeur'],$this->site->infos["GRILLE_CONTROL_".$_SESSION['version']]);
								else
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
			//r�cup�re les multiple non s�lectionn�
			$controls .= $this->GetXulControl($idDoc, $lastRow, substr($MultiSelect,0,-1));
			//fin du multiselect
			$controls .= '</hbox>';
			$controls .= '</groupbox>';
		}
		if($idGrille!=$this->site->infos["GRILLE_REP_CON"]){
			$controls .= '</column>';	
			$labels .= '</column>';
			$form .= $labels.$controls.'</columns>';
		}
		
		if($idGrille==$this->site->infos["GRILLE_REP_CON"]){
			//ajout un bouton observation
			$controls.="<button image='images/IconeEcrire.gif' oncommand=\"AddObservation('".$idDoc."',".$this->site->infos["MOT_CLEF_OBS"].");\"/>";
			$form .= $controls.'</row>';
			//ajout d'une ligne pour les questions interm�diaires
			$form .= '<row id="row_'.$idGrille.'_'.$idDon.'_qi" />';	
		}else
			$form .= '</grid>';	
		
		if($idGrille == $this->site->infos["GRILLE_GEO"]){
			$carto = true;
			//$form .= '<groupbox >';	
			//$form .= '<caption label="Cartographie"/>';
			//ajoute la carte
			$form = $this->GetXulCarto($idDon);
			//$form .= '</groupbox>';
		}
		//v�rifie s'il faut ajouter le bouton de cr�ation de placemark
		if(!$this->VerifDonneeLienGrille($idDon,$this->site->infos["GRILLE_GEO"])){
			$form .="<button label='Ajouter une g�olocalisation' oncommand=\"AddPlacemark();\"/>";
		}
			
		return $form;
	
	}

	function GetXulLegendeControle($idDon, $idGrille){
		//requ�te pour r�cup�rer les donn�es de la grille
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetLegendeControle']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $idDon, $Q[0]->where);
		$where = str_replace("-idGrille-", $idGrille, $where);
		
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
		if($this->trace)
			echo "GetXulLegendeControle ".$this->site->infos["SQL_LOGIN"]." ".$sql."<br/>";
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		
		$labels = "";
		$ico1 = "";
		$ico2 = "";
		$ico3 = "";
		$ico4 = "";
		while($r = $db->fetch_assoc($req)) {
			switch ($r["champ"]) {
				case "multiple_1":
					//construstion r�glementaire
					if($r['valeur']=="multiple_1_1")
						$labels .= '<label value="R�glementaire"/>';
					//construstion souhaitable
					if($r['valeur']=="multiple_1_2")
						$labels .= '<label value="Souhaitable"/>';
					break;
				case "multiple_2":
					//construstion r�glementaire
					if($r['valeur']=="multiple_2_1")
						$labels .= '<label value="Travail"/>';
					//construstion souhaitable
					if($r['valeur']=="multiple_2_2")
						$labels .= '<label value="EPR_IOP"/>';
					break;
				case "multiple_3":
					//construstion des icones
					if($r['valeur']=="multiple_3_1")
						$ico1 = '<image src="images/moteur.jpg"/>';
					if($r['valeur']=="multiple_3_2")
						$ico2 = '<image src="images/audio.jpg"/>';
					if($r['valeur']=="multiple_3_3")
						$ico3 = '<image src="images/visu.jpg"/>';
					if($r['valeur']=="multiple_3_4")
						$ico4 = '<image src="images/cog.jpg"/>';
					break;
				//construstion des handicateurs
				case "ligne_2":
					if($r['valeur']!="0")
						$ico1 .= '<label value="'.$r['valeur'].'"/>';
					break;
				case "ligne_3":
					if($r['valeur']!="0")
						$ico2 .= '<label value="'.$r['valeur'].'"/>';
					break;
				case "ligne_4":
					if($r['valeur']!="0")
						$ico3 .= '<label value="'.$r['valeur'].'"/>';
					break;
				case "ligne_5":
					if($r['valeur']!="0")
						$ico4 .= '<label value="'.$r['valeur'].'"/>';
					break;
			}					
		}
			
		$xul = "<vbox><hbox>".$labels."</hbox><hbox>".$ico1.$ico2.$ico3.$ico4."</hbox></vbox>";
		
		return $xul;
	}
	
	
	function GetXulCarto($idDon,$idRub=-1)
	{
		$xul="";
		if($idRub!=-1){
			$xul = "<iframe height='450px' width='500px' src='".$this->site->infos["urlCarto"]."?id=".$idRub."'  id='BrowerGlobal' />";
			//$xul = "<iframe height='450px' width='500px' src='http://www.mundilogiweb.com/onadabase/kml/garedelille.kmz'  id='BrowerGlobal' />";			
		}else{
			$xul = "<iframe height='450px' width='500px' src='".$this->site->infos["urlCarto"]."?id=".$this->GetRubDon($idDon)."'  id='BrowerGlobal' />";
			//$xul = "<iframe height='450px' width='500px' src='http://maps.google.fr/maps?f=q&hl=fr&geocode=&q=http:%2F%2Fwww.mundilogiweb.com%2Fonadabase%2Fkml%2Fgaredelille.kmz&ie=UTF8&t=h&z=16'  id='BrowerGlobal' />";
			//$xul = "<iframe height='450px' width='500px' src='http://www.mundilogiweb.com/onadabase/kml/garedelille.kmz'  id='BrowerGlobal' />";			
		}		
		return	$xul;	
	
	}

	function GetXulRegLeg($id, $row)
	{
		
		/*r�sultat de row
		champ 	rang 	titre 	type 	obligatoire 	extra_info 	
		ligne_1 	6 	valeur �talon 	ligne 	  	  	  	  	  	 
		ligne_2 	7 	valeur �talon 2 	ligne 	  	  	  	  	  	 
		ligne_3 	4 	Nom de la valeur 	ligne 	  	  	  	  	  	 
		mot_1 	5 	op�rateur 		mot 	18 	  	  	  	 
		mot_2 	8 	Unit�s 		mot 	19 	  	  	  	 
		select_1 	9 	r�gle respect�e 	select radio		
		*/
		
		switch ($row['champ']) {
			case 'ligne_1':
				//r�cup�ration des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control = '<textbox '.$js.' id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row['valeur']).'" />';
				$control .= '<label id="trace'.$id.'" value=""/>';
				break;
			case 'ligne_2':
				//r�cup�ration des js
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
					//r�cup�ration des js
					$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='menu']";
					$js = $this->site->GetJs($Xpath, array($id));
					//construction du control
					$control .= '<menulist id="'.$id.'" '.$js.' ><menupopup >';
					$control .= $this->GetChoixVal($row,'menuitem');				
					$control .= '</menupopup></menulist>';
				}else{				
					//r�cup�ration des js
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
				//r�cup�ration des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='menu']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control .= '<menulist id="'.$id.'" '.$js.' ><menupopup >';
				$control .= $this->GetChoixVal($row,'menuitem');				
				$control .= '</menupopup></menulist>';
				break;
			case 'fichier':
				//r�cup�ration des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='fichier']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control .= '<button id="btn'.$id.'" label="Parcourir" '.$js.' />';
				//r�cup�ration des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				$control .= '<textbox  '.$js.' multiline="true" id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row["valeur"]).'"/>';			
				break;
			default:
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				if($row["grille"]==$this->site->infos["GRILLE_REP_CON"]){
					//on cache le textbox r�f�rence
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
		//requ�te pour r�cup�rer les donn�es de la grille
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
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"]);
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
					//r�cup�ration des js
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