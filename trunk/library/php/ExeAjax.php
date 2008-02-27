<?php
	$ajax = true;
	require_once ("../../param/Constantes.php");
	require_once ("../../param/ParamPage.php");
	//charge le fichier de paramètrage
	$objSite->XmlParam = new XmlParam(PathRoot."/param/SolAcc.xml");

	$resultat = "";
	if(isset($_GET['f']))
		$fonction = $_GET['f'];
	else
		$fonction = '';
	if(isset($_GET['cols']))
		$cols = $_GET['cols'];
	else
		$cols = -1;
	if(isset($_GET['id']))
		$id = $_GET['id'];
	else
		$id = -1;

	switch ($fonction) {
		case 'GetTree':
			$resultat = GetTree($_GET['type'],$cols,$id);
			break;
		case 'GetTabForm':
			$resultat = GetTabForm($_GET['type'],$id);
			break;
		case 'AddGrilles':
			$resultat = AddGrilles($_GET['src'], $_GET['dst'], false);
			break;
		case 'AddPlacemark':
			$resultat = AddPlacemark($_GET['dst'], $_GET['kml']);
			break;
		case 'SetVal':
			$resultat = SetVal($_GET['idDon'],$_GET['champ'],$_GET['val']);
			break;
		case 'GetCurl':
			$resultat = GetCurl($_GET['url']);
			break;
		case 'AddXmlDonnee':
			$resultat = AddXmlDonnee($_GET['url']);
			break;
		case 'AddEtablissement':
			$resultat = AddEtablissement($_GET['src'], $_GET['dst']);
			break;
	}

	echo  utf8_encode($resultat);	

	function AddXmlDonnee($url)
	{
		echo "ExeAjax:AddXmlDonnee:<br/>";
		global $objSite;
		$g = new Grille($objSite);
		$url = PathRoot."/param/controles.xml";
		$g->AddXmlDonnee($url);
	}
				
	function GetCurl($url)
	{
	
		$oCurl = curl_init($url);
		// set options
	   // curl_setopt($oCurl, CURLOPT_HEADER, true);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
		//echo $sCmd."<br/>";
		//$arrInfos = curl_getinfo($ch);
		//print_r($arrInfos);
		//echo "sResult=<br/>";
		//print_r($sResult);
		//echo "<br/>";
		//fin ajout samszo

		// request URL
		$sResult = curl_exec($oCurl);
		// close session
		curl_close($oCurl);

		return $sResult;
	
	}
	
	function SetVal($idDon,$champ,$val){
	
		global $objSite;
		$g = new Grille($objSite);

		//modifie la valeur 
		$row = array("champ"=>$champ,"valeur"=>utf8_decode($val));
		$g->SetChamp($row, $idDon);

		return "donnée enregistrée = ".utf8_decode($val);
	}

	function GetTree($type,$Cols,$id){
		global $objSite;
		
		//récupération des colonnes
		$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/Querys/Query[@fonction='GetTreeChildren_".$type."']/col";
		$Cols = $objSite->XmlParam->GetElements($Xpath);		

		//une seule sélection possible seltype='single' onselect=\"GetTreeSelect('tree".$type."','TreeTrace',2)" seltype='multiple' single
		//	class='editableTree' 			width='100px' height='100px' 

		//récupération des js
		$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/Querys/Query[@fonction='GetTreeChildren_".$type."']/js";
		$js = $objSite->GetJs($Xpath, array($type));
		
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
			//la première colonne est le bouton pour déplier
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
		$tree .= $objSite->GetTreeChildren($type, $Cols, $id);
		$tree .= '</tree>';
		/*
		header('Content-type: application/vnd.mozilla.xul+xml');
		$tree = $objSite->GetTreeChildren($type, $Cols, $id);
		*/
		return $tree;
		
	}

	function GetTabForm($type, $idRub){
		global $objSite;
		$g = new Grille($objSite);
		
		$xul = $g->GetXulTab($type, $idRub, $type);

		//header('Content-type: application/vnd.mozilla.xul+xml');
		//$xul = "<box>".$xul."</box>";

		return $xul;
		
	}

	function AddGrilles($idRubSrc, $idRubDst, $redon){
		global $objSite;
		$g = new Grille($objSite);
		$xul = $g->AddGrilles($idRubSrc, $idRubDst, $redon);

		//header('Content-type: application/vnd.mozilla.xul+xml');
		//$xul = "<box>".$xul."</box>";

		return $xul;
		
	}
	
	function AddEtablissement($idRubSrc, $idRubDst){
		global $objSite;
		
		$g = new Granulat($idRubDst,$objSite);
		$id = $g->SetNewEnfant("Sans Nom");
		//ajoute une sous-rubrique
				
		$grille = new Grille($objSite);
		$grille->AddGrilles($idRubSrc, $id);
		$xul = $grille->GetXulTab("Etab", $id);
		
		//header('Content-type: application/vnd.mozilla.xul+xml');
		//$xul = "<box>".$xul."</box>";

		return $xul;
		
	}
	
	function AddPlacemark($idRubDst, $kml){
		global $objSite;
		$g = new Grille($objSite);
		//création de la grille géolocalisation
		$idDon = $g->AddDonnee($idRubDst, $objSite->infos["GRILLE_GEO"], false);
		
		//ajoute la valeur du kml
		$row = array("champ"=>"texte_1","valeur"=>$kml);
		$g->SetChamp($row, $idDon);

		//header('Content-type: application/vnd.mozilla.xul+xml');
		//$xul = "<box>".$xul."</box>";

		return "donnée créé = ".$idDon;
		
	}
?>
