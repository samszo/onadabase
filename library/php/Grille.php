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
	$this->trace = true;

    $this->site = $site;
    $this->id = $id;
	if($this->site->scope["FicXml"]!=-1)
		$this->XmlParam = new XmlParam($this->site->scope["FicXml"]);
	
	if($complet){
	}

	//echo "FIN new grille <br/>";
		
    }

    function GetObjId($donId,$obj) {
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
    
    function GereWorkflow($rows, $donId) {

    	
    	$Xpath = "/XmlParams/XmlParam/workflow[@srcId='".$rows['grille'].";".$rows['champ']."']";
		if($this->trace)
			echo "Grille:GereWorkflow:récupère les paramètre du workflow à exécuter ".$Xpath."<br/>";
    	$wfs = $this->site->XmlParam->GetElements($Xpath);
		foreach($wfs as $wf)
		{
			
			$id = $this->GetObjId($donId,$wf['dstObj']);
			if($this->trace)
				echo "//récupère l'identifiant de l'objet ".$wf['dstObj']." ".$id."<br/>";
			
			if($this->trace)
				echo "//workflow path query ".$wf['dstQuery']."<br/>";
			
			$Q = $this->site->XmlParam->GetElements($wf['dstQuery']);
			$where = str_replace("-id-", $id, $Q[0]->where);
			$set = str_replace("-param-", $rows['valeur'], $Q[0]->set);
			$sql = $Q[0]->update.$set.$where;
			$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
			$db->connect();
			$db->query($sql);
			$db->close();
			if($this->trace)
				echo "//exécution du workflow ".$sql."<br/>";
				
		}
		
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
			echo "AddXmlDonnee IN //récuparation de la définition des données ".$xmlSrc."<br/>";
		$xml = new XmlParam($xmlSrc);		
		
		$Xpath = "/donnees";
		$donnees = $xml->GetElements($Xpath);
		if($this->trace)
			echo "//récupération des valeurs de donnée ".$donnees."<br/>";
		
		$idGrille = $donnees[0]->grille;
		if($this->trace)
			echo "//récupération de l'identifiant de la grille ".$idGrille."<br/>";
		
		//récupération de la définition des champs
		$Xpath = "/donnees/champs";
		$champs = $xml->GetElements($Xpath);
		
		foreach($donnees[0]->donnee as $donnee)
		{
			$idRub = $donnee->rub;
			if($this->trace)
				echo "//- récupération de l'identifiant de la rubrique ".$idRub."<br/>";
			
			//récuparation du granulat
			$g = new Granulat($idRub, $this->site); 
			$idArt = $g->GetArticle();
			if($this->trace)
				echo "//- récupération ou création du dernier article en cours de rédaction ".$idArt."<br/>";
			
			$idDon = $g->GetIdDonnee($idGrille, $idArt, true);
			if($this->trace)
				echo "//- création de la donnee ".$idDon."<br/>";

			$i=0;
			foreach($donnee->valeur as $valeur)
			{
				if($valeur!='false'){
					$valeur=utf8_decode($valeur);
					$champ = $champs[0]->champ[$i];
					if($this->trace)
						echo "//--- gestion des champs multiples ".substr($champ,0,8)."<br/>";
					if(substr($champ,0,8)=="multiple"){
						$valeur=$champ;
						//attention il ne doit pas y avoir plus de 10 choix
						$champ=substr($champ,0,-2);
					}
					if($this->trace)
						echo "//-- récupération du type de champ ".$champ."<br/>";
					$row = array('champ'=>$champ, 'valeur'=>$valeur);
					if($this->trace)
						echo "//-- récupération de la valeur du champ ".$valeur."<br/>";
					$this->SetChamp($row, $idDon,false);
					if($this->trace)
						echo "//--- création du champ <br/>";
				}
				$i++;
			}
			
		}
		if($this->trace)
			echo "AddXmlDonnee OUT //<br/>";
		
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
		
		//récupération des rubriques dans les liens de la rubrique Src 
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetRubInLiens']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-idRub-", $idRubSrc, $Q[0]->where);
		$sql = $Q[0]->select.$Q[0]->from.$where;
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$rows = $db->query($sql);
		$db->close();
		if($this->trace)
			echo "Grille:AddGrilles:".$sql."<br/>";
		while ($row =  $db->fetch_assoc($rows)) {
			$result .= $this->AddGrilles($row["idRub"], $idRubDst, $row["idRub"]);
		}
		
		return $result;
	}
	


	function AddDonnee($idRub, $idGrille=-1, $redon=false){
		
		if($idGrille==-1)
			$idGrille=$this->id;
			
		//récuparation du granulat
		$g = new Granulat($idRub, $this->site);
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
		//echo $sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		
	}	

	function SetChamp($row, $donId, $del=true) {

		if($del)
			//supression de la valeur
			$this->DelChamp($row, $donId); 
		
		//création de la valeur
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_InsChamp']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$values = str_replace("-idDon-", $donId, $Q[0]->values);
		$values = str_replace("-champ-", $row["champ"], $values);
		$values = str_replace("-val-", $row["valeur"], $values);
		$sql = $Q[0]->insert.$values;
		if($this->trace)
			echo $sql."<br/>";
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
		$sql = $Q[0]->select.$Q[0]->from.$where;
		if($this->trace)
			echo $sql."<br/>";
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$result = $db->query($sql);
		$db->close();
		//echo "--- ".$donId." nouvelle valeur ".$i;
		
	}	
	
	function GetXulTab($src, $id, $dst="Rub", $recur = false){


		//chaque ligne est un onglet
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetXulTabForm".$dst."']";
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
			if($Q[0]->dst=='Form')
				$tabpanels .= $this->GetXulTabPanels($r["idArt"], $r["id"],'Form',$recur);
			else
				$tabpanels .= $this->GetXulTabPanels($src, $r["id"],$Q[0]->dst,$recur);
			$i++;
		}
		
		if($i!=0){
			$tabbox .= '</tabs>';
			$tabbox .= '<tabpanels>';
			$tabbox .= $tabpanels;
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
		while($r = $db->fetch_assoc($req)) {
			//$tabpanel .= '<groupbox >';	
			//$tabpanel .= '<caption label="'.$r["titre"].'"/>';
			if($Q[0]->dst=='Form')
				//ajoute les données de chaque article
				$tabpanel .= $this->GetXulForm($r["id"], $id);
			else
				//ajoute la tabbox de destination
				$tabpanel .= $this->GetXulTab($src, $r["id"], $Q[0]->dst, $recur);	
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
		$form = '<grid flex="1">';	
		$form .= '<columns flex="1">';	
		$labels = '<column flex="1" align="end">';	
		//$form .= '<caption label="Donnée : '.$idDon.'"/>';
		$controls = '<column flex="1">';
		while($r = $db->fetch_assoc($req)) {
			$idDoc = 'val'.DELIM.$idGrille.DELIM.$r["id_donnee"].DELIM.$r["champ"].DELIM.$r["id_article"];
			$labels .= '<label control="first" multiligne="true" value="'.$r['titre'].'"/>';
			switch ($idGrille) {
				case $this->site->infos["GRILLE_REG_LEG"]:
					//construstion de la règle législative
					$controls .= $this->GetXulRegLeg($idDoc, $r);
					break;
				default:
					$controls .= $this->GetXulControl($idDoc, $r);
			}
		}
		$controls .= '</column>';	
		$labels .= '</column>';	
		$form .= $labels.$controls.'</columns>';
		if($idGrille == $this->site->infos["GRILLE_GEO"]){
			$form .= '<groupbox >';	
			$form .= '<caption label="Cartographie"/>';
			//ajoute la carte
			$form .= $this->GetXulCarto($idDon);
			$form .= '</groupbox>';
		}
		
		$form .= '</grid>';	

		return $form;
	
	}
	
	function GetXulCarto($idDon)
	{
	
		return	"<iframe height='550px' width='450px' src='http://www.mundilogiweb.com/onadabase/design/BlocCarte.php?id=".$this->GetRubDon($idDon)."'  id='BrowerGlobal' />";
	
	
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
	
	function GetXulControl($id, $row)
	{
		$control = '';
		switch ($row['type']) {
			case 'select':
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
				break;
			case 'mot':
				//récupération des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='menu']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				/*
				$control .= '<groupbox>';
				//$control .= '<caption label="'.$row['titre'].'"/>';
				$control .= '<radiogroup id="'.$id.'" '.$js.' >';
				$control .= '<hbox>';
				$control .= $this->GetChoixVal($row);
				$control .= '</hbox>';
				$control .= "</radiogroup>";
				$control .= '</groupbox>';
				*/
				$control .= '<menulist id="'.$id.'" '.$js.' ><menupopup >';
				$control .= $this->GetChoixVal($row,'menuitem');				
				$control .= '</menupopup></menulist>';
				break;
			default:
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				//$control .= '<label value="'.$this->site->XmlParam->XML_entities($row["titre"]).'"/>';			
				$control .= '<textbox '.$js.' id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row['valeur']).'" />';
				
		}
		
		$control .= '<label id="trace'.$id.'" hidden="true" value=""/>';

		return $control;

	}

	function GetChoixVal($row,$type='radio')
	{
		//requête pour récupérer les données de la grille
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetChoix".$row['type']."']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		$where = str_replace("-id-", $row['grille'], $Q[0]->where);
		$where = str_replace("-champ-", $row['champ'], $where);
		$where = str_replace("-extra_info-", $row['extra_info'], $where);
		
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
				echo "select ".$select." ".$row['valeur']."==".$r['choix']."<br/>";
			if($type=='radio')
				$control .= "<radio id='".$r['choix']."' selected='".$select."' label='".$this->site->XmlParam->XML_entities($r["titre"])."'/>";
			else
				$control .= "<menuitem id='".$r['choix']."' selected='".$select."' label='".$this->site->XmlParam->XML_entities($r['titre'])."'/>";
		}
		
		return $control;

	}
	
  }


?>