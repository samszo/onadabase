<?php
class Xul{
  public $id;
  public $trace;
  private $site;
 
    function __tostring() {
    return "Cette classe permet la cr�ation dynamique d'objet XUL.<br/>";
    }

    function __construct($site, $id=-1, $complet=true) {
	//echo "new Site $sites, $id, $scope<br/>";
  	$this->trace = TRACE;

    $this->site = $site;
    $this->id = $id;
	
	
	if($complet){
	}

	//echo "FIN new grille <br/>";
		
    }

    function GetMenuPopUp($idRub,$typeSrc,$niv=0){
    	$gra = new Granulat($idRub,$this->site);
    	$menu ='';
    	$Xpath = "/XmlParams/XmlParam[@nom='MenuNavig']/menuSrc[@code='".$typeSrc."']/menuDst";
		$menusDst = $this->site->XmlParam->GetElements($Xpath);
    	
		if($menusDst){			
	    	foreach($menusDst as $mDst)
			{    	
		    	$rows = $gra->GetTreeChildren($mDst["codeTree"]);
		    	if($rows){
		    		$lib = utf8_decode($mDst["lib"]);
		    		$mnuLabel = " - ".$lib."";
		    		$menu .='<menu id="MenuPopUp_'.$typeSrc.'_'.$mDst["codeTree"].'_'.$idRub.'" label="'.$mnuLabel.'" ><menupopup >';
			    	while($r = mysql_fetch_assoc($rows))
					{
						//r�cup�ration du js
						$Xpath = "/XmlParams/XmlParam[@nom='MenuNavig']/menuSrc[@code='".$typeSrc."']/menuDst[@codeTree='".$mDst["codeTree"]."']/js";
						$js = $this->site->GetJs($Xpath, array($idRub,$lib,$mDst["codeTree"],$mDst["codeSaisi"],$r["id"]));
						//cr�ation de l'item
		    			$mnuLabel = $this->site->XmlParam->XML_entities($r["titre"]);
						$menu .= '<menuitem '.$js.' label="'.$mnuLabel.'"/>';
						//v�rifie la cr�ation d'un sous menu
						$sousmenu = $this->GetMenuPopUp($r["id"],$mDst["codeSaisi"],$niv+1);
						if($sousmenu!=""){
							$menu .= $sousmenu;
							$menu .= '<menuseparator/>';			
						}					
					}
					$menu .= '</menupopup></menu>';
		    	}
			}			
		}
			
		return $menu;

    }
    
    function GetPopUp($xul,$titre, $login){

		header('Content-type: application/vnd.mozilla.xul+xml');
		echo '<' . '?xml version="1.0" encoding="iso-8859-15" ?' . '>';
		echo '<' . '?xml-stylesheet href="chrome://global/skin/" type="text/css"?' . '>' . "\n";
		echo '<' . '?xml-stylesheet href="'.$this->site->infos["pathXulJs"].'../popup.css" type="text/css"?' . '>' . "\n";
		
    	echo "<window  
    	    persist='screenX screenY width height'
		    orient='horizontal'
    		title='".$titre."'
		    xmlns='http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul' >";
		
		echo "<script type='application/x-javascript' src='".$this->site->infos["pathXulJs"]."interface.js' />";
		
		echo '<script type="application/x-javascript" src="'.$this->site->infos["pathXulJs"].'ajax.js"/>';
		echo '<script type="application/x-javascript" src="'.$this->site->infos["pathXulJs"].'tree.js"/>';
		echo '<script>
			var lienAdminSpip = "'.$this->site->infos["lienAdminSpip"].'";
			var urlExeAjax = "'.$this->site->infos["urlExeAjax"].'";
     	</script>';
		echo '<vbox  flex="1" style="overflow:auto">
				<hbox class="menubar">
					<label id="login" value="'.$login.'"/>
					<progressmeter id="progressMeter" value="0" mode="determined" style="margin: 4px;"/>
				</hbox>	
			</vbox>
				';
		echo '<script>
			document.getElementById("login").style.visibility="hidden";
			document.getElementById("progressMeter").style.visibility="hidden";
		</script>';
		echo $xul;
		echo "</window>";

    }
    

    
    function GetFilAriane($jsParam, $id=-1, $niv=0){

		if($id==-1)
			$id=$this->id;
		//echo "$id, $niv<br/>";
			
		//cr�ation du granulat
		$g = new Granulat($id, $this->site);
		
		//if($niv==0)
		//	$FilAriane = "<toolbar id='fa-toolbar' class='toolbar'>";	
		
		if($g->IdParent!=0)
			$FilAriane.=$this->GetFilAriane($jsParam, $g->IdParent, ($niv+1));
		
		$FilAriane.="<label value='|' />";
		//$url = $this->site->infos['lienAdminSpip']."".urlencode ("/?exec=naviguer&id_rubrique=".$g->id);
		//$FilAriane.="<label id='fa_".$id."' class='titreLiens' href='' onclick=\"RefreshEcran(document.getElementById('idRub').value,'".$jsParam[0]."','".$jsParam[1]."','".$jsParam[2]."');\" value='".$g->titre."'/>";
		$FilAriane.="<label id='fa_".$id."' class='text-link2' onclick=\"OuvreLienAdmin(".$g->id.");\" value=\"".$g->titre."\"/>";
		//$FilAriane.="<toolbarbutton id='fa_".$id."' class='toolbarbutton' label='".$g->titre."'/>";
		
		//if($niv==0)
		//	$FilAriane .= "</toolbar>";
		
		
		return $FilAriane;
		
	}

	function GetTab($src, $id, $dst="Rub", $recur = false){


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
		$tabbox = '<tabbox  id="tabbox_'.$src.'_'.$dst.'_'.$id.'">';
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


	function GetTabPanels($src, $id, $dst="Rub", $recur = false){

		//r�cup�re les articles de la rubrique
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
		$tabpanel = '<tabpanel id="tabpanel_'.$src.'_'.$dst.'_'.$id.'">';	
		
		//ajoute les onglets des sous rubriques
		if($recur)
			$tabpanel .= $this->GetXulTab($src, $id, $dst, $recur);
		
		//ajoute les groupbox pour chaque article
		while($r = $db->fetch_assoc($req)) {
			//$tabpanel .= '<groupbox >';	
			//$tabpanel .= '<caption label="'.$r["titre"].'"/>';
			if($Q[0]->dst=='Form')
				//ajoute les donn�es de chaque article
				$tabpanel .= $this->GetXulForm($r["id"], $id);
			else
				//ajoute la tabbox de destination
				$tabpanel .= $this->GetXulTab($src, $r["id"], $Q[0]->dst, $recur);	
		}
		$tabpanel .= '</tabpanel>';

		return $tabpanel;
	}

			
    function GetForm($idDon, $idGrille) {
  
  
		//requ�te pour r�cup�rer les donn�es de la grille
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
		$form = '<hbox flex="1">';	
		$form .= '<groupbox >';	
		$form .= '<caption label="Donn�e : '.$idDon.'"/>';
		while($r = $db->fetch_assoc($req)) {
			$idDoc = 'val'.DELIM.$r["id_donnee"].DELIM.$r["champ"];
			switch ($idGrille) {
				case $this->site->infos["GRILLE_REG_LEG"]:
					//construstion de la r�gle l�gislative
					$form .= $this->GetXulRegLeg($idDoc, $r);
					break;
				default:
					$form .= $this->GetXulControl($idDoc, $r);
			}
		}
		$form .= '</groupbox>';
		if($idGrille == $this->site->infos["GRILLE_GEO"]){
			$form .= '<groupbox >';	
			$form .= '<caption label="Cartographie"/>';
			//ajoute la carte
			$form .= $this->GetCarto($idDon);
			$form .= '</groupbox>';
		}
		
		$form .= '</hbox>';	

		return $form;
	
	}
	
	function GetCarto($idDon)
	{
	
		return	"<iframe height='550px' width='450px' src='".$this->site->infos["urlCarto"]."?id=".$this->GetRubDon($idDon)."'  id='BrowerGlobal' />";
	
	
	}

	function GetRegLeg($id, $row)
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
				$control .= '<label id="trace'.$id.'" value="" width="200"/>';
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
	
	function GetControl($id, $row)
	{
		$control = '';
		switch ($row['type']) {
			case 'select':
				//r�cup�ration des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='radio']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control .= '<groupbox>';
				$control .= '<caption label="'.$row['titre'].'"/>';
				$control .= '<radiogroup id="'.$id.'" '.$js.' >';
				$control .= '<hbox>';
				$control .= $this->GetChoixVal($row);
				$control .= '</hbox>';
				$control .= "</radiogroup>";
				$control .= '</groupbox>';
				break;
			case 'mot':
				//r�cup�ration des js
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='radio']";
				$js = $this->site->GetJs($Xpath, array($id));
				//construction du control
				$control .= '<groupbox>';
				$control .= '<caption label="'.$row['titre'].'"/>';
				$control .= '<radiogroup id="'.$id.'" '.$js.' >';
				$control .= '<hbox>';
				$control .= $this->GetChoixVal($row);
				$control .= '</hbox>';
				$control .= "</radiogroup>";
				$control .= '</groupbox>';
				break;
			default:
				$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='Grille_GetDonnee']/js[@type='textbox']";
				$js = $this->site->GetJs($Xpath, array($id));
				$control .= '<label value="'.$this->site->XmlParam->XML_entities($row["titre"]).'"/>';			
				$control .= '<textbox '.$js.' id="'.$id.'" value="'.$this->site->XmlParam->XML_entities($row['valeur']).'" />';
				
		}
		
		$control .= '<label id="trace'.$id.'" value=""/>';

		return $control;

	}

	function GetChoixVal($row)
	{
		//requ�te pour r�cup�rer les donn�es de la grille
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
			$control .= "<radio id='".$r['choix']."' selected='".$select."' label='".$this->site->XmlParam->XML_entities($r["titre"])."'/>";
		}
		
		return $control;

	}
	
	
	function GetTree($type, $Cols, $js, $id){
		
		$tree = "<tree flex=\"1\" 
			id=\"tree".$type."\"
			seltype='multiple'
			".$js."
			>";
		$tree .= '<treecols>';
		$tree .= '<treecol  id="id" primary="true" cycler="true" flex="1" persist="width ordinal hidden"/>';
		$tree .= '<splitter class="tree-splitter"/>';

		$i=0;
		foreach($Cols as $Col)
		{
			//la premi�re colonne est le bouton pour d�plier
			if($i!=0){
				if($Col["hidden"])
					$visible = $Col["hidden"];
				else
					$visible = "false";
				if($Col["type"]=="checkbox"){
					$tree .= '<treecol id="treecol_'.$Col["tag"].'" label="'.$Col["tag"].'" type="checkbox" editable="true" persist="width ordinal hidden" />';
				}else{
					$tree .= '<treecol id="treecol_'.$Col["tag"].'" hidden="'.$visible.'" label="'.$Col["tag"].'" flex="1"  persist="width ordinal hidden" />';
					$tree .= '<splitter class="tree-splitter"/>';
				}
			}
			
			$i++;
		}
		$tree .= '</treecols>';
		$tree .= $this->GetTreeChildren($type, $Cols, $id);
		$tree .= '</tree>';
		
		return $tree;
		
	}
	
	function GetTreeChildren($type, $Cols=-1, $id=-1){

		if($this->trace)
			echo "//GetTreeChildren($type, $Cols, $id <br/>";
		
		if($Cols==-1){
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetTreeChildren_".$type."']/col";
			$Cols = $this->site->XmlParam->GetElements($Xpath);
			//print_r($Cols);
		}
		
		$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetTreeChildren_".$type."']";
		$Q = $this->site->XmlParam->GetElements($Xpath);
		//print_r($Q);
		if($id==-1){
			//r�cup�re la valeur par defaut
			$Xpath = "/XmlParams/XmlParam/Querys/Query[@fonction='GetTreeChildren_".$type."']/from";
			$attrs =$this->site->XmlParam->GetElements($Xpath);
			//print_r( $attrs[0]["def"]);
			
			if($attrs[0]["niv"])
				$id = $attrs[0]["niv"];
			//echo $id." def<br/>";
		}
		
		$where = str_replace("-parent-", $id, $Q[0]->where);
		//ECHO $FROM;
		$sql = $Q[0]->select.$Q[0]->from.$where;
		if($this->trace)
			echo "Xul:GetTreeChildren:".$this->site->infos["SQL_LOGIN"]." sql=".$sql."<br/>";
		
		$db = new mysql ($this->site->infos["SQL_HOST"], $this->site->infos["SQL_LOGIN"], $this->site->infos["SQL_PWD"], $this->site->infos["SQL_DB"], $dbOptions);
		$db->connect();
		$req = $db->query($sql);
		$db->close();
		$nb = mysql_num_rows($req);

		$hierEnfant = "";
		$tree = '<treechildren >'.EOL;
		while($r = mysql_fetch_row($req))
		{
			switch ($type) {
				case "SitePage":
					$idXul = "treeitem_".$type."_".$id."_".$r[0];
					break;
				case 'page':
					$idXul = "treeitem_".$type."_".$r[4]."_".$id."_".$r[0];
					break;
				default:	
					$idXul = "treeitem_".$type."_".$r[0];
			}
					
			$tree .= '<treeitem id="'.$idXul.'" container="true" empty="false" >'.EOL;
			$tree .= '<treerow>'.EOL;
			$i= 0;
			//colonne de l'identifiant
			//$tree .= '<treecell label="'.$r[$i].'"/>'.EOL;
			foreach($Cols as $Col)
			{
				if($i==0)
					//construction de l'id
					$val = $idXul;
				else
					$val = $this->site->XmlParam->XML_entities($r[$i]);
				
				$tree .= '<treecell label="'.$val.'"/>'.EOL;
				$i ++;
			}
			$tree .= '</treerow>'.EOL;
			if($this->trace)
				echo "//v�rifie s'il faut afficher une hi�rarchie ".$type." <br/>";
			
			if($type=="site")
				$tree .= $this->GetTreeChildren("SitePage", -1, $r[0]);
			if($type=="SitePage")
				$tree .= $this->GetTreeChildren("page", -1, $r[0]);
			if($type=="page")
				$tree .= $this->GetTreeChildren("page", $Cols, $r[0]);
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