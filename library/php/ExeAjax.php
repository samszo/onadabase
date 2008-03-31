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
		case 'Synchroniser':
			//pour tester la synchronisation en local
			// le site = $objSiteSync
			// en prod c'est $objSite
			$resultat = Synchroniser($objSiteSync);
			break;
		case 'GetFilAriane':
			$resultat = GetFilAriane(array($_GET['titre'],$_GET['typeDrc'],$_GET['typeDst']),$id);
			break;
		case 'GetTree':
			$resultat = GetTree($_GET['type'],$cols,$id,$objSite);
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
			$resultat = SetVal($_GET['idGrille'],$_GET['idDon'],$_GET['champ'],$_GET['val']);
			break;
		case 'DelVal':
			$resultat = DelVal($_GET['idGrille'],$_GET['idDon'],$_GET['champ'],$_GET['val']);
			break;
		case 'GetCurl':
			$resultat = GetCurl($_GET['url']);
			break;
		case 'AddXmlDonnee':
			$resultat = AddXmlDonnee($_GET['url']);
			break;
		case 'AddNewGrille':
			$resultat = AddNewGrille($_GET['src'], $_GET['dst'], $_GET['type']);
			break;
		case 'NewRubrique':
			//$resultat = NewRubrique($_GET['src'], $_GET['dst'], $_GET['type'], $cols);
			$resultat = NewRubrique($idRubDst);
			break;
	}

	echo  utf8_encode($resultat);	

function Synchroniser($objSite){
	return GetTree("terre",-1,-1,$objSite);
	
}
	
	function GetFilAriane($jsParam, $id){
		global $objSite;
		
		//récupère le granulat
		$xul = new Xul($objSite, $id);
		$FilAriane = $xul->GetFilAriane($jsParam);
		return $FilAriane;
	}
	
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
	
	function SetVal($idGrille,$idDon,$champ,$val){
	
		global $objSite;
		$g = new Grille($objSite,$login);

		//modifie la valeur 
		$row = array("grille"=>$idGrille,"champ"=>$champ,"valeur"=>utf8_decode($val));
		$g->SetChamp($row, $idDon);
		
		//gestion du workflow
		$xul = $g->GereWorkflow($row, $idDon);		

		return $xul;
	}

	function DelVal($idGrille,$idDon,$champ,$val){
	
		global $objSite;
		$g = new Grille($objSite);

		//modifie la valeur 
		$row = array("grille"=>$idGrille,"champ"=>$champ,"valeur"=>utf8_decode($val));
		$g->DelChamp($row, $idDon);
		
		return utf8_decode("donnée supprimée = ".$val);
	}
	
	function GetTree($type,$Cols,$id,$objSite){
		
		//récupération des colonnes
		$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/Querys/Query[@fonction='GetTreeChildren_".$type."']/col";
		$Cols = $objSite->XmlParam->GetElements($Xpath);		

		//une seule sélection possible seltype='single' onselect=\"GetTreeSelect('tree".$type."','TreeTrace',2)" seltype='multiple' single
		//	class='editableTree' 			width='100px' height='100px' 

		//récupération des js
		$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/Querys/Query[@fonction='GetTreeChildren_".$type."']/js";
		$js = $objSite->GetJs($Xpath, array($type,$id));
		
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

	function AddGrilles($idRubSrc, $idRubDst, $login, $redon){
		global $objSite;
		$g = new Grille($objSite);
		$xul = $g->AddGrilles($idRubSrc, $idRubDst, $redon);

		//header('Content-type: application/vnd.mozilla.xul+xml');
		//$xul = "<box>".$xul."</box>";

		return $xul;
		
	}
	
	function AddNewGrille($idRubSrc, $idRubDst, $trs){
		global $objSite;
		echo "ExeAjax:AddNewGrille:".$idRubSrc.", ".$idRubDst.", ".$trs."<br/>";
		$g = new Granulat($idRubDst,$objSite);
		$id = $g->SetNewEnfant($trs." Sans Nom ".date('j/m/y - H:i:s'));
		//ajoute une sous-rubrique
				
		$grille = new Grille($objSite);
		$grille->AddGrilles($idRubSrc, $id);
		//ajoute les QuestionsRéponses
		$grille->AddQuestionReponse($idRubSrc,$id);
		
		$xul = $grille->GetXulTab($trs, $id);
		
		if($trs=="EspaceInt")
			AddNewEspaceGen(66, $id, "ParamGenEspace");
		if($trs=="EspaceExt")
			AddNewEspaceGenExt(63, $id, "ParamGenEspace");
		if($trs=="ObjetInt") {
			
			/*$idArt = $g->SetNewArticle($g->titre." ".date('j/m/y - H:i:s'));
			
				echo ":GereWorkflow://ajoute une nouveau article ".$idArt."<br/>";
			//ajoute une nouvelle donnee
			$idDon = $grille->AddDonnee($id, $idRubDst, false, $idArt);*/
		
		}
		//header('Content-type: application/vnd.mozilla.xul+xml');
		//$xul = "<box>".$xul."</box>";

		return $xul;
		
	}

	function AddNewEspaceGen($idRubSrc, $idRubDst, $trs){
		global $objSite;
		
		//ajoute une sous-rubrique espace gen
		$g = new Granulat($idRubDst,$objSite);
		$grille = new Grille($objSite);
		//$idGen = $g->SetNewEnfant("Paramètres généraux espace");
		//$gGen = new Granulat($idGen,$objSite);
		
		//ajoute une sous-rubrique espace gen->éclairage
		$id = $g->SetNewEnfant("Eclairage");
		//ajoute les QuestionsRéponses
		$grille->AddQuestionReponse(71,$id);
		
		//ajoute une sous-rubrique espace gen->Equipements et dispositifs de commande
		$id = $g->SetNewEnfant("Equipements et dispositifs de commande");
		//ajoute les QuestionsRéponses
		$grille->AddQuestionReponse(70,$id);
				
		//ajoute une sous-rubrique espace gen->Pentes et ressauts
		$id = $g->SetNewEnfant("Pentes et ressauts");
		//ajoute les QuestionsRéponses
		$grille->AddQuestionReponse(69,$id);
				
		//ajoute une sous-rubrique espace gen->Signalétique
		$id = $g->SetNewEnfant("Signalétique");
		//ajoute les QuestionsRéponses
		$grille->AddQuestionReponse(72,$id);
				
		//ajoute une sous-rubrique espace gen->Sols, murs et plafonds
		$id = $g->SetNewEnfant("Sols, murs et plafonds");
		//ajoute les QuestionsRéponses
		$grille->AddQuestionReponse(68,$id);
				
		//header('Content-type: application/vnd.mozilla.xul+xml');
		//$xul = "<box>".$xul."</box>";

		return "OK";
		
	}
	
	function AddNewEspaceGenExt($idRubSrc, $idRubDst, $trs){
		global $objSite;
		
		//ajoute une sous-rubrique espace gen
		$g = new Granulat($idRubDst,$objSite);
		$grille = new Grille($objSite);
		//$idGen = $g->SetNewEnfant("Paramètres généraux espace");
		//$gGen = new Granulat($idGen,$objSite);
		
		//ajoute une sous-rubrique espace gen->Cheminement extérieur
		$id = $g->SetNewEnfant("Cheminement extérieur");
		//ajoute les QuestionsRéponses
		$grille->AddQuestionReponse(64,$id);
		
		//ajoute une sous-rubrique espace gen->Equipements et dispositifs de commande
		$id = $g->SetNewEnfant("Equipements et dispositifs de commande");
		//ajoute les QuestionsRéponses
		$grille->AddQuestionReponse(628,$id);
				
		//ajoute une sous-rubrique espace gen->Sol extérieur
		$id = $g->SetNewEnfant("Sol extérieur");
		//ajoute les QuestionsRéponses
		$grille->AddQuestionReponse(65,$id);
				
		//header('Content-type: application/vnd.mozilla.xul+xml');
		//$xul = "<box>".$xul."</box>";

		return "OK";
		
	}
	
	function NewRubrique($idRubDst) {
		global $objSite;
		
		$mot = 60; // id mot clef Ilot;
				
		if ($idRubDst==-1) {
				$idRubDst = 9; // id rubrique département
				$mot = 59; // id mot clef Commune
		}
		if ($idRubDst==9) {
				$mot = 59; // id mot clef Commune
		}
		
		// pour récupérer le parent
		$g = new Granulat($idRubDst,$objSite);
		// pour créer un nouvel enfant
		$idGen = $g->SetNewEnfant("Enfant test");
		
		$g->SetMotClef($mot,$idGen);
		
		// pour renvoyer la mise à jour du tree
		$tree = GetTree("terre",-1,-1,$objSite);
		
		return $tree;
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
