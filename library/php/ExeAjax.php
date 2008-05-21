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
		/*case 'Synchroniser':
			//pour tester la synchronisation en local
			// le site = $objSiteSync
			// en prod c'est $objSite
			$resultat = Synchroniser($objSiteSync);
			break;*/
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
			$resultat = AddNewGrille($_GET['src'], $_GET['dst'], $_GET['type'], $_GET['login']);
			break;
		case 'NewRubrique':
			$resultat = NewRubrique($_GET['idRubSrc'], $_GET['idRubDst'], $_GET['idAuteur']);
			break;
		case 'Synchronise':
			//$resultat = NewRubrique($_GET['src'], $_GET['dst'], $_GET['type'], $cols);
			$resultat = Synchronise($siteSrc, $siteDst=-1, $_GET['idAuteur']);
			break;
		case 'SynchroImport':
			$resultat = SynchroImport($_GET['idAuteur']);
			break;
		case 'CleanArticle':
			$resultat = CleanArticle($_GET['deb'], $_GET['fin']);
			break;
		case 'CleanRubrique':
			$resultat = CleanRubrique($_GET['deb'], $_GET['fin']);
			break;
		case 'AddDocToArt':
			//$resultat = AddDocToArt($_GET['path'], $_GET['idArt'], $_GET['doc']);
			$resultat = AddDocToArt($_GET['idDoc']);
			break;
		case 'GetAdminRub':
			//$resultat = AddDocToArt($_GET['path'], $_GET['idArt'], $_GET['doc']);
			$resultat = AddDocToArt($_GET['idAuteur']);
			break;
		default:
			//$resultat = AddDocToArt();
	}

	echo  utf8_encode($resultat);	
	
	/*
		ajoute un document à un article 
	*/
	function AddDocToArt($idDoc){
		
		global $objSite;
		
		$arrIdDoc = split("[".DELIM."]",$idDoc);

		if(TRACE){
			echo "ExeAjax:AddDocToArt:POST<br/>";
			print_r($_POST);
			echo "ExeAjax:AddDocToArt:FILES<br/>";
			print_r($_FILES);
			echo "ExeAjax:AddDocToArt:idDoc".$idDoc."<br/>";
			print_r($arrIdDoc);
		}	

		//construction du fichier de destination
		$FicDst = $arrIdDoc[4]."_".$arrIdDoc[2]."_".date('j-m-y_H-i-s').substr($_FILES['filename']['name'],-4,4);
		
		//déplace le fichier dans le répertoire spip
		move_uploaded_file($_FILES['filename']['tmp_name'], $objSite->infos["pathUpload"].$FicDst);
		if(TRACE)
			echo "ExeAjax:AddDocToArt:FicDst".$FicDst."<br/>";

		//construction du nouveau document en fonction du type
		$doc = new Document($objSite);
		switch (substr($_FILES['filename']['name'],-3,3)) {
			case 'kml':
				$row = array(
					'titre'=>$_FILES['filename']['name']
					,'type'=>75
					,'desc'=>''
					,'fichier'=>'IMG/kml/'.$FicDst
					,'taille'=>$_FILES['filename']['size']
					,'largeur'=>0
					,'hauteur'=>0
					,'idArt'=>$arrIdDoc[4]
					); 
			break;
		}
		$doc->AddNew($row);
		
	}
	
	
	/*
	 * Synchronise le local avec le serveur,
	 * récupère les nouvelles données du serveur,
	 * actualise les id rubriques et articles
	 * et import les nouvelles rubriques et articles du serveur
	 */
	function Synchronise($siteSrc, $siteDst=-1, $idAuteur){
    	
		global $objSite;
		global $objSiteSync; //Mundi
		    	
		if(TRACE)
			echo "ExeAjax:Synchronise:idAuteur $idAuteur<br/>";

		$urlAdmin = $objSiteSync->infos["urlExeAjax"]."?f=GetAdminRub&idAuteur=".$idAuteur;
		
		if(TRACE)
			echo "ExeAjax:Synchronise:urlAdmin=$urlAdmin<br/>";
		
		$pageDebut = GetCurl($urlAdmin);
		$arrliste = unserialize($pageDebut);
		
		if(TRACE)
			echo "ExeAjax:Synchronise:liste=$arrliste<br/>";
		
		/*$synchro = new Synchro($objSite, $objSite);
    	$xmlUrl = $synchro->synchronise($objSiteSync, $objSite, $idAuteur);
    	$url = $objSiteSync->infos["urlExeAjax"]."?f=SynchroImport&idAuteur=".$idAuteur;
		if(TRACE)
			echo "ExeAjax:Synchronise:url=$url<br/>";

		$page = UploadCurl($xmlUrl, $url);
		
		$posDeb = strrpos($page, "<?xml version=\"1.0\"?>");
		$posFin = strrpos($page, "</documents>");
		if(TRACE){
			//echo "ExeAjax:Synchronise:PAGE ::: ".$page;
			echo "ExeAjax:Synchronise:posDeb ".$posDeb;
			echo "ExeAjax:Synchronise:posFin ".$posFin;
		}
		
		if ($posFin === false) {
			$posFin = strrpos($page, "<documents/>");
			if(TRACE){
				echo "ExeAjax:Synchronise:posFin ".$posFin;
			}
		}
		
		if ($posDeb !== false) {
			if ($posFin !== false) {
				$xmlString = substr($page, $posDeb, $posFin);
				if(TRACE)
					echo "ExeAjax:Synchronise:xmlString=".$xmlString." FIN ExeAjax:Synchronise:xmlString";
					$path = $synchro->Actualise($xmlString);
					$synchro->import($path);
			}
		}*/
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
		if(TRACE)
			echo "ExeAjax:AddXmlDonnee:<br/>";
		global $objSite;
		$g = new Grille($objSite);
		$url = PathRoot."/param/controles.xml";
		$g->AddXmlDonnee($url);
	}

	/*
	 * Récupère le fichier uploadé, 
	 * réalise l'import sur le serveur 
	 * et synchronise les nouvelles rubriques et articles du serveur
	 * 
	 */
	function SynchroImport($idAuteur) {	
		global $objSite;
		global $objSiteSync;
		
		if(TRACE){
			echo "ExeAjax:SynchroImport:idAuteur=".$idAuteur."<br/>";
			print_r($_POST);
			print_r($_FILES);
		}	
		
		if ((isset($_FILES['file']['name'])&&($_FILES['nom_du_fichier']['error'] == UPLOAD_ERR_OK))) {
			if(TRACE){
				echo "ExeAjax:SynchroImport:PATH = ".PathRoot."/param/";
			}
			$chemin_destination = PathRoot."/param/";
			move_uploaded_file($_FILES['file']['tmp_name'], $chemin_destination.$_FILES['file']['name']);
			
			$src = $chemin_destination.$_FILES['file']['name'];
			if(TRACE){
				echo "ExeAjax:SynchroImport:urlSRC = ".$src;
			}
			
			$sync = new Synchro($objSite,-1);
						
			$reponseSynch = $sync->import($src);
			$sync->AddHistoriqueSynchro($src, $idAuteur);
			
			$doc = new DOMDocument();
			$doc->loadXML($reponseSynch);
			
			$doc2 = new DOMDocument();
			$xmlUrl = $sync->synchronise($objSiteSync, $objSite, $idAuteur);
			$doc2->load($xmlUrl);

			//$node = $doc->importNode($doc2->firstChild, true);
			//$doc2->getElementsByTagName("documents")->item(0)
			//$nodePrincipaux = $node->childNodes;
			$nodePrincipaux = $doc2->firstChild->childNodes;
			foreach ($nodePrincipaux as $enfant) {
				$nodeEnfant = $doc->importNode($enfant, true);
				$doc->firstChild->appendChild($nodeEnfant);
			}	
			
			echo "ExeAjax:SynchroImport:SOURCE = ".$doc->saveXML(); // Ne pas mettre dans les traces
			
			//echo $reponseSynch;
		}
	}
	
	function GetAdminRub($idAuteur) {
		global $objSite;
		
		echo 'ICI';
		$sync = new Synchro($objSite,-1);
		$arrliste = $sync->GetAdminRub($idAuteur);
		print_r($arrliste);
		echo serialize($arrliste);
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
		
		if(TRACE){
			$arrInfos = curl_getinfo($oCurl);
			echo "ExeAjax:GetCurl:getinfo=".print_r($arrInfos)."<br/>";
			echo "ExeAjax:GetCurl:page=".$sResult."<br/>";
		}
		
		// close session
		curl_close($oCurl);

		return $sResult;
	
	}
	
	function PostCurl($url,$data)
	{
		
		if(TRACE)
			echo "ExeAjax:PostCurl:url=$url<br/>";
		$curl = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$page = curl_exec($curl);
		if(TRACE){
			$arrInfos = curl_getinfo($curl);
			echo "ExeAjax:PostCurl:getinfo=".print_r($arrInfos)."<br/>";
			echo "ExeAjax:PostCurl:page=".$page."<br/>";
		}
		curl_close($curl);
		return $page; 
	}
	
	/*
	 * Upload un fichier
	 * 
	 */
	function UploadCurl($urlLocal, $url) {
		
		$ch = curl_init(); 
		$data = array('name' => 'Test', 'file' => '@'.$urlLocal); 
		//print_r($data);
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_POST, 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$page = curl_exec($ch);
		
		if(TRACE){
			$arrInfos = curl_getinfo($ch);
			echo "ExeAjax:UploadCurl:getinfo=".print_r($arrInfos)."<br/>";
			echo "ExeAjax:UploadCurl:page=".$page."<br/>";
		}
		curl_close($ch);
		return $page;
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
	
	function AddNewGrille($idRubSrc, $idRubDst, $trs)		{

		global $objSite;
		echo "ExeAjax:AddNewGrille:".$idRubSrc.", ".$idRubDst.", ".$trs."<br/>";
		$g = new Granulat($idRubDst,$objSite);
		$id = $g->SetNewEnfant($trs." Sans Nom ".date('j/m/y - H:i:s'));
		//ajoute une sous-rubrique
		//alert("AddNewGrille  IN Src "+$idRubSrc+" Dst "+$idRubDst+" trs "+$trs+" n");	
		
		$grille = new Grille($objSite);
		
		/*if($trs=="CabineAscenseur") {
			//AddNewObjetIntBat(597, $id, "ObjetIntBat");
			//alert("AddNewGrille  IN "+type+"\n");
			$idArt = $g->SetNewArticle($g->titre." Controle ".date('j/m/y - H:i:s'));
			$idDon = $grille->AddDonnee($id, $grille, false, $idArt);
		}*/
		
		$grille->AddGrilles($idRubSrc, $id);
		//ajoute les QuestionsRéponses
		$grille->AddQuestionReponse($idRubSrc,$id);
		
		$xul = $grille->GetXulTab($trs, $id);
		
		if($trs=="EspaceInt")
			AddNewEspaceGen(66, $id, "ParamGenEspace");
		if($trs=="EspaceExt")
			AddNewEspaceGenExt(63, $id, "ParamGenEspace");
		if($trs=="ObjetIntBat") {
			
			/*$idArt = $g->SetNewArticle($g->titre." ".date('j/m/y - H:i:s'));
			
				echo ":GereWorkflow://ajoute une nouveau article ".$idArt."<br/>";
			//ajoute une nouvelle donnee
			$idDon = $grille->AddDonnee($id, $idRubDst, false, $idArt);*/
		
		}
		
		if($trs=="ObjetInt") {
			//AddNewObjetInt(1167, $id, "ParamObjetInt");
			//$g->SetMotClef($mot,$id);
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
		$id = $g->SetNewEnfant("Commandes");
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
		$id = $g->SetNewEnfant("Cheminement");
		//ajoute les QuestionsRéponses
		$grille->AddQuestionReponse(64,$id);
						
		//ajoute une sous-rubrique espace gen->Sol extérieur
		$id = $g->SetNewEnfant("Sol extérieur");
		//ajoute les QuestionsRéponses
		$grille->AddQuestionReponse(65,$id);
		
		//header('Content-type: application/vnd.mozilla.xul+xml');
		//$xul = "<box>".$xul."</box>";

		return "OK";
		
	}
	
	function AddNewObjetIntBat($idRubSrc, $idRubDst, $trs){
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
	
	function AddNewObjetInt($idRubSrc, $idRubDst, $trs){
		global $objSite;
		
		//ajoute une sous-rubrique espace gen
		$g = new Granulat($idRubDst,$objSite);
		$grille = new Grille($objSite);
		//$idGen = $g->SetNewEnfant("Paramètres généraux espace");
		//$gGen = new Granulat($idGen,$objSite);
		
		//ajoute une sous-rubrique espace gen->Cheminement extérieur
		$id = $g->SetNewEnfant("Cabine d'ascenseur");
		//ajoute les QuestionsRéponses
		$grille->AddQuestionReponse(613,$id);
						
		//header('Content-type: application/vnd.mozilla.xul+xml');
		//$xul = "<box>".$xul."</box>";

		return "OK";
		
	}
	
	function NewRubrique($idRubSrc, $idRubDst, $idAuteur) {
		global $objSite;

		if(TRACE) {
			echo 'ExeAjax:NewRubrique:idRubSrc '.$idRubSrc;
			echo 'ExeAjax:NewRubrique:idRubDst '.$idRubDst;
		}
		// pour récupérer le parent
		$g = new Granulat($idRubDst,$objSite);
		
		// pour créer un nouvel enfant
		$idGen = $g->SetNewEnfant("Territoire Sans Nom ".date('j/m/y - H:i:s'));
		
		$grille = new Grille($objSite);
		
		$grille->AddGrilles($idRubSrc, $idGen);
		
		$gra = new Granulat($idGen, $objSite);
		
		$idArticle = $gra->GetArticle();
		$gra->AddAuteur($idArticle, $idAuteur);
		
		$xul = $grille->GetXulTab('Terre', $idGen);
		
		//if ($mot != -1) $g->SetMotClef($mot,$idGen);
		
		// pour renvoyer la mise à jour du tree
		$tree = GetTree('terre',-1,-1,$objSite);
		
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

	/*
	 * Nettoye les données des articles inutilisées
	 * 
	 */
	function CleanArticle($deb, $fin) {
		global $objSite;
		
		$synchro = new Synchro($objSite, -1);
		$synchro->CleanArticle($deb, $fin);
	}
	
	/*
	 * Nettoye les données des rubriques inutilisées
	 * 
	 */
	function CleanRubrique($deb, $fin) {
		global $objSite;
		
		$synchro = new Synchro($objSite, -1);
		$synchro->CleanRubrique($deb, $fin);
	}
	
?>
