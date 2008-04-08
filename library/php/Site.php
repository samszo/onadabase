<?php
class Site{
  public $id;
  public $idParent;
  public $scope;
  public $NbsTopics;
  public $XmlParam;
  private $sites;
  private $trace;
  
  function __tostring() {
    return "Cette classe permet de définir et manipuler un site.<br/>";
    }

  function __construct($sites, $id, $scope, $complet=true) {

  	//echo "new Site $sites, $id, $scope<br/>";
    $this->trace = true;
  	
    $this->sites = $sites;
    $this->id = $id;
    $this->infos = $this->sites[$this->id];
	$this->scope = $scope;
	if($this->scope["FicXml"]!=-1)
		$this->XmlParam = new XmlParam($this->scope["FicXml"]);
	
	if($this->infos["SITE_PARENT"]!=-1){
		$Parent = array_keys($this->infos["SITE_PARENT"]);
		$this->idParent = $Parent[0];
	}else{
		$this->idParent = -1;
	}
	if($complet){
		if($this->scope["VoirEn"] == "Mot")
			$Liens = array("page"=>"themes.php?"
				,"pageAjax"=>"design/BlocMilieuMot.php?"
				,"VoirEn"=>"Mot"
				);
		else
			$Liens = array("page"=>"lieux.php?"
				,"pageAjax"=>"design/BlocMilieuTopos.php?"
				,"VoirEn"=>"Topos"
				);
		$this->menu = $this->MenuSite($this->id,0,$Liens);
	}

	//echo "FIN new Site <br/>";
		
    }
    
    public function Synchronise($siteSrc, $siteDst=-1){
		if($siteDst==-1)
			$siteDst=$this->id;
    	
		//récupère les mots clefs de la source
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetMotsClef']";
		if($this->trace)
			echo "Site:Synchronise:Xpath=".$Xpath."<br/>";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$sql = $Q[0]->select.$Q[0]->from;
		$db = new mysql ($siteSrc->infos["SQL_HOST"], $siteSrc->infos["SQL_LOGIN"], $siteSrc->infos["SQL_PWD"], $siteSrc->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		if($this->trace)
			echo "Site:Synchronise:sql=".$sql."<br/>";
		while ($row =  $db->fetch_assoc($rows)) {
			//vérifie l'existence dans la destination
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='VerifMotsClef']";
			$Q = $this->site->XmlParam->GetElements($Xpath);
			$where = str_replace("-idGroupe-", $row['id_groupe'], $Q[0]->where);
			$where = str_replace("-titre-", $row['titre'], $where);
			$sql = $Q[0]->select.$Q[0]->from.$where;
			$db = new mysql ($siteSrc->infos["SQL_HOST"], $siteSrc->infos["SQL_LOGIN"], $siteSrc->infos["SQL_PWD"], $siteSrc->infos["SQL_DB"], $dbOptions);
			$db->connect();
			$rowsVerif = $db->query($sql);
			$db->close();
			$rowVerif =  $db->fetch_assoc($rowsVerif);
			if($rowVerif['nb']==0){
				//ajoute le mot clef
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='AjoutMotsClef']";
				$Q = $this->site->XmlParam->GetElements($Xpath);
				$values = str_replace("-idGroupe-", $row['id_groupe'], $Q[0]->values);
				$values = str_replace("-titre-", $row['titre'], $values);
				$sql = $Q[0]->insert.$values;
				$db = new mysql ($siteSrc->infos["SQL_HOST"], $siteSrc->infos["SQL_LOGIN"], $siteSrc->infos["SQL_PWD"], $siteSrc->infos["SQL_DB"], $dbOptions);
				$db->connect();
				$db->query($sql);
				$db->close();
			}
			
		}
			
    	
    }
    
    public function EstParent($id)
	{
		$arrParent = split("[".DELIM."]", $this->GetParentIds());
		//print_r($arrParent); 
		//echo $id."<br/>";	
		return in_array($id, $arrParent);	
	}

	public function GetParentIds($id = "")
	{
		if($id =="")
			$id = $this->id;
		//echo "GetParentIds = ".$id."<br/>";
			
		if($this->sites[$id]["SITE_PARENT"]!=-1){
			$Parent = array_keys($this->sites[$id]["SITE_PARENT"]);
			$idParent = $Parent[0];
			$valeur .= $this->GetParentIds($idParent);
			$valeur .= $id.DELIM;
		}
		//echo $valeur."<br/>";	
		return $valeur;

	}
	
	public function GetNomSiteParent($id_site=-1)
	{
		if($id_site==-1)
			$id_site=$this->id;
			
		$valeur="";
		//print_r($this->sites[$id_site]["SITE_PARENT"]);
		if(is_array($this->sites[$id_site]["SITE_PARENT"])){
			foreach($this->sites[$id_site]["SITE_PARENT"] as $siteparent=>$type)
			{
				//echo $siteparent."=>".$type."<br/>";
				$valeur .=" ".$this->sites[$siteparent]["NOM"]." ";
				
			}
		}
		return $valeur;	
	}

	public function NextSiteEnfant($id_site)
	{
		$valeur=-1;
		if($this->infos["SITE_ENFANT"]!=-1){		
			$next=false;
			foreach($this->infos["SITE_ENFANT"] as $siteenfant=>$type)
			{
				//echo $this->id." NextSiteEnfant:".$siteenfant."=".$id_site." ".$next."<br/>"; 
				if($next){
					$valeur = $siteenfant;
					break;
				}
				if($siteenfant==$id_site)
					$next=true;				
			}
		}
		return $valeur;
	}

	public function GetSiteEnfant($id_site=-1)
	{
		if($id_site==-1)
			$id_site=$this->id;
			
		$valeur="";
		foreach($this->sites[$id_site]["SITE_ENFANT"] as $siteenfant)
		{
			print_r($siteenfant);
			//$valeur .= $this->GetSiteEnfant($siteenfant=>id);
			//$valeur .= $r['id_rubrique'].DELIM;
			
		}	
	}

	public function GetFilAriane($id=-1)
	{
		if($id==-1)
			$id=$this->id;
			
		$valeur="";
		//echo $this->id." SiteParent=".$this->sites[$id_site]["SITE_PARENT"].'<br/>';
		if($this->sites[$id]["SITE_PARENT"]!=-1){		
			foreach($this->sites[$id]["SITE_PARENT"] as $SiteParent=>$titre)
			{
				$valeur .= $this->GetFilAriane($SiteParent);
			}
		}
		$lien =  "themes.php?site=".$id;
		$valeur .= "<a href='".$lien."'>".$this->sites[$id]["NOM"]."</a> | "."\n";

		return $valeur;		

	}


	public function MenuSite($id_site, $niv=0,$Liens)
	{

		$valeur = "";
		$valon = "";
		$valselect = "";
		$menu =""; 
		//création d'un bloc  pour calculer le nombre de topic
		$g = new Bloc($this,"vide",$this->scope);
		
		//echo $this->id." SiteEnfant=".$this->sites[$id_site]["SITE_ENFANT"].'<br>';
		//echo "création du menu du site et des enfants<br/>";
		if($this->sites[$id_site]["SITE_ENFANT"]!=-1){		
			foreach($this->sites[$id_site]["SITE_ENFANT"] as $siteenfant=>$rptitre)
			{
				//echo $rptitre.' : '.$siteenfant.'<br>';
				$EstParent = $this->EstParent($siteenfant);
				//echo "vérifie la sélection d'un site enfant : ".$this->id." - ".$siteenfant." - EstParent=".$EstParent."<br/>";
				if($siteenfant==$this->id || $EstParent){
					$valon = "<div class='MenuToposOn'></div>";
					$valselect = "<div class='MenuToposLabel'>".$this->sites[$siteenfant]["NOM"]."</div>";
	
					//calcul les enfants
					if($this->sites[$siteenfant]["SITE_ENFANT"]!=-1){
						//echo "calcul le menu des parents : ".$siteenfant."<br/>";
						$menuenfant = $this->MenuSite($siteenfant,$niv-1,$Liens);
					}
				}else{
					//création du lien
					$lien =  $this->GetLien($Liens["page"]
						, array("site","VoirEn","Rub")
						, array($siteenfant,$Liens["VoirEn"],$this->sites[$siteenfant]["RUB_TopicTopos"])
						, array("PageCourante","Rub","RubSelect","SiteSelect","PasCourant")
						);
					//echo "MenuSite - calcul le nombre de topic pour le site : ".$siteenfant."<br/>";
					//if($niv<0)
					$nbmot = $g->GetSiteNbTopic($siteenfant,-1,-1,0);
					if($nbmot>0){
						//$valeur .= "<a href='".$lien."'>".$this->sites[$siteenfant]["NOM"]." (".$nbmot.")</a><br/>";
						$valeur .= "<a href='".$lien."'>".$this->sites[$siteenfant]["NOM"]."</a><br/>";
					}else
						$valeur .= $this->sites[$siteenfant]["NOM"]."<br/>";
				}
				
			}
	
			//calcul du lien
			/*
			if($id_site=="france")
				$lien =  "topictopos.php?site=".$id_site;
			else{
				$lien =  $this->GetLien($Liens["pageAjax"]
					, array("site","VoirEn")
					, array($id_site,"Topos")
					,array("PageCourante")
					);
				$jsFunctions = "onclick=\"AjaxRequest('".$lien."', 'SetBlocMilieuTopos');fcthtmlExpand(".$niv.",'site')\"";
				$lien =  $this->GetLien($Liens["page"]
					, array("site","VoirEn")
					, array($id_site,$Liens["VoirEn"])
					,array("PageCourante","Rub","RubSelect","SiteSelect","PasCourant")
					);
			}
			*/
			$jsFunctions = "onclick=\"fcthtmlExpand(".$niv.",'site')\"";
			
			//création de l'entête
			$menu .= "<script language='JavaScript'>maxHtmlExpand++;</script>";			
			$menu .= "<div class='MenuTopos' >";
			//vérifie si un élément est sélectionné
			if($valon!=""){
				$menu .= $valon;			
				$menu .= "<div class='MenuToposTitre'>".$rptitre."</div>";
				$menu .= "</div>";			
				$menu .= $valselect;
				//$menu .= "<div class='MenuToposLienTous' style='cursor: pointer; cursor: hand;' ".$jsFunctions." > <a href='".$lien."'> >Toutes les ".$rptitre." </a></div>";
				//$menu .= "<div class='MenuToposLienTous' style='cursor: pointer; cursor: hand;' ".$jsFunctions." > >Tout afficher </div>";
				$menu .= "<div class='MenuToposLienTous' style='display:bloc;' id='siteExpand".$niv."' >";
			}else{
				$menu .= $valon;			
				$menu .= "<div class='MenuToposTitre'>".$rptitre."</div>";
				$menu .= "</div>";			
				//$menu .= "<div class='MenuToposLabel'>Tout afficher </div>";
				$menu .= "<div class='MenuToposLienTous' style='display:bloc;' id='siteExpand".$niv."' >";			
			}
			$menu .= $valeur;			
			$menu .= "</div>";
			$menu .= $menuenfant;
		}			
	
		return $menu;
	}
	
	function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
	{
	  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;
	  // evite le double caractere \'' 
	  if (get_magic_quotes_gpc()) $theValue = str_replace("'","''",$theValue);
	  $theValue = str_replace("\"","''",$theValue);
	  //$theValue = htmlentities($theValue);
	  //echo $theValue."<br/>";

	  switch ($theType) {
	    case "text":
	      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "''";
	      break;    
	    case "long":
	    case "int":
	      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
	      break;
	    case "double":
	      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
	      break;
	    case "date":
	      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
	      break;
	    case "defined":
	      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
	      break;
	  }
	  return $theValue;
	}


	public function GetLien($url, $type_select, $new_val, $arrSup=false)
	{
		if($this->scope!=-1){		
			foreach($this->scope as $param=>$val)
			{
				//prise en compte du tableau des valeurs de paramètre à modifier
				if(is_array($type_select)){
					$i = 0;
					$change = false;
					foreach($type_select as $type)
					{
						if($type==$param){
							$url .= $param."=".$new_val[$i]."&";
							$change = true;
						}
						$i ++;
					}
					if(!$change){
						if($arrSup){
							if (in_array($param, $arrSup))
								$url .= "";
							else
								$url .= $param."=".$val."&";
						}else
							$url .= $param."=".$val."&";
					}
				}else{			
					if($type_select==$param)
						$url .= $param."=".$new_val."&";
					else{
						if($arrSup){
							if (in_array($param, $arrSup))
								$url .= "";
							else
								$url .= $param."=".$val."&";
						}else
							$url .= $param."=".$val."&";
					}
				}
			}
		}
		//enlève la dernière virgule
		$url = substr($url, 0, -1);
		
		return $url;
	}

	function GetSiteResult($site){
	
		$DBSearch = new DatabaseSearch(
			$site->infos["SQL_HOST"]
			,$site->infos["SQL_DB"]
			,$site->infos["SQL_LOGIN"]
			,$site->infos["SQL_PWD"]
			, false
			);
		//echo "DBSearch->needle=".$DBSearch->needle."<br/>";

		$recherche = $DBSearch->needle;

		//Search in table news, return data from column id, search in column tresc
		//It will use value from form (if defined) as needle.
		//$search_result = $DBSearch->DoSearch("spip_rubriques","id_rubrique",array("titre","texte"),"","AND");
		$search_result = $DBSearch->DoSearch("spip_rubriques","id_rubrique",array("texte"),"","AND");
		//print_r($search_result);
		if($search_result){
			$rstRub = array("nb"=>count($search_result),"ids"=>implode(",", $search_result));
			/*	
			$search_result = $DBSearch->DoSearch("spip_mots m INNER JOIN spip_mots_rubriques mr ON mr.id_mot = m.id_mot","id_rubrique",array("titre"),"","AND");
			$rstMot = array("nb"=>count($search_result),"ids"=>implode(",", $search_result));
			*/
			return array("site"=>$site->id,"recherche"=>$recherche,"rstRub"=>$rstRub);
		}
		
	}

	function GetAllResult($site=-1)
	{
		if($site==-1)
			$site = $this;
		
		$SitesEnfants = $site->infos["SITE_ENFANT"];
		//echo "vérifie le calcul des sites enfants ".$SitesEnfants."<br/>";
		$NbT = 0;
		if(is_array($SitesEnfants)){
			//boucle sur les enfants
			$i = 0;
			foreach($SitesEnfants as  $SiteEnfant=>$type)
			{
				//echo "boucle sur les enfants ".$type." : ".$SiteEnfant." ".$this->site->sites[$SiteEnfant]."<br/>";
				$siteEnf = new Site($site->sites, $SiteEnfant, $site->scope, false);
				$R = $this->GetSiteResult($siteEnf);
				if($R){
					$Result[$i] = $R;
					//enregistre le résultat
					$site->NbsTopics[$SiteEnfant]=$Result[$i]["rstRub"]["nb"];
					//additionne le nombre de topic du site enfant
					//$NbT += $site->NbsTopics[$SiteEnfant];
					$i ++;
				}else
					$site->NbsTopics[$SiteEnfant]=0;

			}	
		}
		// enregistre le résultat
		//ajoute le nb de TOPIC du scope
		//$NbT += $site->NbsTopics[$site->id];
		$R = $this->GetSiteResult($site);
		if($R){
			$Result[$i] = $R;
			$site->NbsTopics[$site->id]=$Result[$i]["rstRub"]["nb"];
		}
		//print_r($site->NbsTopics);

		return $Result;
		
	}	
	
	function GetJs($Xpath, $arrParam)
	{
		$nodesJs = $this->XmlParam->GetElements($Xpath);		
		$js = "";
		$i = 0;
		foreach($nodesJs as $nodeJs)
		{
			if($arrParam[$i])
				$Param = $arrParam[$i];
			$function = str_replace("-param".$i."-", $Param, $nodeJs["function"]);
			$js .= " ".$nodeJs["evt"]."=\"".$function."\"";
			$i ++;
		}
		return $js;
	}
	
	function GetTreeChildren($type, $Cols=-1, $id=-1){

	    if($this->trace)
	    	echo ":GetTreeChildren: type = $type Cols = $Cols, id= $id<br/>";
		
	    if($Cols==-1){
			$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/Querys/Query[@fonction='GetTreeChildren_".$type."']/col";
			$Cols = $this->XmlParam->GetElements($Xpath);	
		}
		
		$Xpath = "/XmlParams/XmlParam[@nom='".$this->scope['ParamNom']."']/Querys/Query[@fonction='GetTreeChildren_".$type."']";
		$Q = $this->XmlParam->GetElements($Xpath);
		if($id==-1){
			//récupère la valeur par defaut
			$attrs = $Q[0]->where[0]->attributes();
			if($attrs["def"])
				$id = $attrs["def"];
			//echo $id." def<br/>";
		}
	
		$where = str_replace("-parent-", $id, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		
	    if($this->trace)
			echo $sql."<br/>";

		$db = new mysql ($this->infos["SQL_HOST"], $this->infos["SQL_LOGIN"], $this->infos["SQL_PWD"], $this->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		$nb = mysql_num_rows($req);

		$hierEnfant = "";
		$tree = '<treechildren >'.EOL;
		while($r = mysql_fetch_row($req))
		{
			$tree .= '<treeitem id="'.$type.'_'.$r[0].'" container="true" empty="false" >'.EOL;
			$tree .= '<treerow>'.EOL;
			$i= 0;
			//colonne de l'identifiant
			//$tree .= '<treecell label="'.$r[$i].'"/>'.EOL;
			foreach($Cols as $Col)
			{
				$tree .= '<treecell label="'.$r[$i].'"/>'.EOL;
				$i ++;
			}
			$tree .= '</treerow>'.EOL;
			$tree .= $this->GetTreeChildren($type, $Cols, $r[0]);
			$tree .= '</treeitem>'.EOL;
		}

		if($nb>0)
			$tree .= '</treechildren>'.EOL;
		else
			$tree = '';
		
		return $tree;

	}

	
	
  }


?>