<?php
	session_start();
	//pour le d�bugage
	if(!isset($_SESSION['version'])) {
		$_SESSION['version']="V2";
		$_SESSION['type_controle'] = array ($_POST['type_controle1'], $_POST['type_controle2']);
		$_SESSION['type_contexte'] = array ($_POST['type_contexte1'], $_POST['type_contexte2'], $_POST['type_contexte3'], $_POST['type_contexte4']);
		$_SESSION['IdAuteur']=1;
	}
	
	$ajax = true;
	require_once ("../../param/ParamPage.php");
	//charge le fichier de param�trage
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
	if(isset($_GET['ppp']))
		$ppp = $_GET['ppp'];
	else
		$ppp = -1;
		
	switch ($fonction) {
		/*case 'Synchroniser':
			//pour tester la synchronisation en local
			// le site = $objSiteSync
			// en prod c'est $objSite
			$resultat = Synchroniser($objSiteSync);
			break;*/		
		case 'ShowPopUp':
			$resultat = ShowPopUp($_GET['idGrille'],$_GET['idDon'],$_GET['login']);
			break;
		case 'GetListeEtatDiag':
			$resultat = $g->GetListeEtatDiag($_GET['idDoc']);
			break;
		case 'GetEtatDiag':
			$resultat = $g->GetEtatDiag();
			break;
		case 'GetTreeProb':
			$resultat = GetTreeProb($_GET['id']);
			break;
		case 'GetTreeObs':
			$resultat = GetTreeObs($_GET['id']);
			break;
		case 'GetTreeCsv':
			$resultat = GetTreeCsv($_GET['id']);
			break;
		case 'GetFilAriane':
			$resultat = GetFilAriane(array($_GET['titre'],$_GET['typeDrc'],$_GET['typeDst']),$id);
			break;
		case 'GetMenuPopUp':
			$resultat = GetMenuPopUp($_GET['id'],$objSite,$_GET['type']);
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
			$resultat = AddPlacemark($_GET['dst'], $_GET['kml'],$_GET['BBOX']);
			break;
		case 'SetVal':
			$resultat = SetVal($_GET['idGrille'],$_GET['idDon'],$_GET['champ'],$_GET['val'], $_GET['login']);
			break;
		case 'DelVal':
			$resultat = DelVal($_GET['idGrille'],$_GET['idDon'],$_GET['champ'],$_GET['val']);
			break;
		case 'GetCurl':
			$resultat = GetCurl($_GET['url']);
			break;
		case 'AddXmlDonnee':
			$resultat = AddXmlDonnee($_GET['url'], $objSite);
			break;
		case 'AddNewGrille':
			$resultat = AddNewGrille($_GET['src'], $_GET['dst'], $_GET['type'], $objSite);
			break;
		case 'NewRubrique':
			$resultat = NewRubrique($_GET['idRubSrc'], $_GET['idRubDst'], $_GET['idAuteur']);
			break;
		case 'Synchronise':
			//$resultat = NewRubrique($_GET['src'], $_GET['dst'], $_GET['type'], $cols);
			$resultat = Synchronise($objSite,$objSiteSync, $_GET['idAuteur']);
			break;
		case 'ShowSynchro':
			$sync = new Synchro($objSite,$objSiteSync);
			$resultat = $sync->ShowSynchro($objSite,$objSite->infos["AUTEUR_SYNCHRO"]);
			break;
		case 'CompareLocalServeur':
			$sync = new Synchro($objSite,$objSiteSync);
			$resultat = $sync->CompareSrcDst($_GET['idRub']);
			break;
		case 'CompareServeurLocal':
			$sync = new Synchro($objSiteSync,$objSite);
			$resultat = $sync->CompareSrcDst($_GET['idRub']);
			break;
		case 'SynchroSrcDst':
			if($_GET['siteSrc']==$objSite->id)
				$sync = new Synchro($objSite,$objSiteSync);
			else
				$sync = new Synchro($objSiteSync,$objSite);
			if($_GET['scope']=="arbre")
				$resultat = $sync->SynchroArbreSrcDst($_GET['idRub'],$_GET['type'],$_GET['id']);
			else	
				$resultat = $_GET['scope'];//$sync->SynchroBrancheSrcDst($_GET['idRub'],$_GET['id'],$_GET['val'],$_GET['type'],$_GET['action']);
			break;
		case 'SynchroImport':
			$resultat = SynchroImport($objSiteSync, $_GET['idAuteur']);
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
			$resultat = GetAdminRub($_GET['idAuteur']);
			break;
		case 'ClearArticle':
			$resultat = ClearArticle($_GET['idDonnee'], $_GET['idRub']);
			break;
		case 'ClearArticleObs':
			$resultat = ClearArticleObs($_GET['idDonnee'], $_GET['idRub']);
			break;
		case 'ClearRubrique':
			$resultat = ClearRubrique($_GET['idRub'], $_GET['idRubParent']) ;
			break;
		case 'ClearRubriqueParent':
			$resultat = ClearRubriqueParent($_GET['idRubParent']) ;
			break;
		case 'ClearRubriqueObs':
			$resultat = ClearRubriqueObs($_GET['idRub'], $_GET['idRubParent']) ;
			break;
		case 'ClearRubriqueParentObs':
			$resultat = ClearRubriqueParentObs($_GET['idRubParent']) ;
			break;
		case 'CopyRub':
			$resultat = CopyRub($_GET['idDst']) ;
			break;
		case 'AddVersion':
			$resultat = AddVersion() ;
			break;
		case 'ChangeAutoIncrement':
			$resultat = ChangeAutoIncrement($_GET['table'], $_GET['val']) ;
			break;
		case 'SetSessionValues':
			$resultat = SetSessionValues($_GET['site'], $_GET['type_controle1'], $_GET['type_controle2'],$_GET['type_contexte1'], $_GET['type_contexte2'], $_GET['type_contexte3'], $_GET['type_contexte4'],$_GET['version']) ;
			break;
		case 'explorerDir':
			$resultat=explorerDir($_GET['dir'],$objSite);
		default:
			//$resultat = AddDocToArt();
	}

	echo  utf8_encode($resultat);	
	
	function SetSessionValues($site, $type_controle1, $type_controle2,$type_contexte1, $type_contexte2, $type_contexte3, $type_contexte4, $version){
		$_SESSION['type_controle'] = array ($type_controle1, $type_controle2);
		$_SESSION['type_contexte'] = array ($type_contexte1, $type_contexte2, $type_contexte3, $type_contexte4);
		$_SESSION['version']= $version;
		$_SESSION['site']= $site;
	}
	
	function ShowPopUp($idGrille,$idDon,$login){
		global $objSite;
		$g = new Grille($objSite,$idGrille);
		$xul = $g->GetXulForm($idDon,$idGrille);
		
		$oXul = new Xul($objSite,$idGrille);
		return $oXul->GetPopUp($xul,$g->titre, $login, $idDon);
		
	}
	
	/*
		ajoute un document � un article 
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
		$tabDecomp = explode('.', $_FILES['filename']['name']);
		$extention = strtolower($tabDecomp[sizeof($tabDecomp)-1]);
		
		$FicDst = $arrIdDoc[4]."_".$arrIdDoc[2]."_".date('j-m-y_H-i-s').'.'.$extention;
		
		if(TRACE){
			echo "ExeAjax:AddDocToArt:tabDecomp <br/>";
			print_r($tabDecomp);
			echo "ExeAjax:AddDocToArt:extension <br/>".$extention;
		}
		
		//substr($_FILES['filename']['name'],-4,4)
		//d�place le fichier dans le r�pertoire spip
		move_uploaded_file($_FILES['filename']['tmp_name'], $objSite->infos["pathUpload"].$extention.'/'.$FicDst);
		if(TRACE) {
			echo "ExeAjax:AddDocToArt:FicDst".$FicDst."<br/>";
		}
		//construction du nouveau document en fonction du type
		$doc = new Document($objSite);
		// substr($_FILES['filename']['name'],-3,3)
		// pathinfo($_FILES['filename']['tmp_name'],PATHINFO_EXTENSION)
		
		$add = true;
		
		$repExist = is_dir($objSite->infos["pathUpload"].$extention);
		
		if (!is_dir($objSite->infos["pathUpload"].$extention)) mkdir($objSite->infos["pathUpload"].$extention);
		
		switch ($extention) {
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
			case 'jpg':
				$imageInfo = getimagesize($objSite->infos["pathUpload"].$extention.'/'.$FicDst);
				$row = array(
					'titre'=>$_FILES['filename']['name']
					,'type'=>1
					,'desc'=>''
					,'fichier'=>'IMG/jpg/'.$FicDst
					,'taille'=>$_FILES['filename']['size']
					,'largeur'=>$imageInfo[0]
					,'hauteur'=>$imageInfo[1]
					,'idArt'=>$arrIdDoc[4]
					); 
			break;
			case 'mpg':
				$row = array(
					'titre'=>$_FILES['filename']['name']
					,'type'=>15
					,'desc'=>''
					,'fichier'=>'IMG/mpg/'.$FicDst
					,'taille'=>$_FILES['filename']['size']
					,'largeur'=>0
					,'hauteur'=>0
					,'idArt'=>$arrIdDoc[4]
					); 
			break;
			case 'mov':
				$row = array(
					'titre'=>$_FILES['filename']['name']
					,'type'=>13
					,'desc'=>''
					,'fichier'=>'IMG/mov/'.$FicDst
					,'taille'=>$_FILES['filename']['size']
					,'largeur'=>0
					,'hauteur'=>0
					,'idArt'=>$arrIdDoc[4]
					); 
			break;
			case 'flv':
				$row = array(
					'titre'=>$_FILES['filename']['name']
					,'type'=>10
					,'desc'=>''
					,'fichier'=>'IMG/flv/'.$FicDst
					,'taille'=>$_FILES['filename']['size']
					,'largeur'=>0
					,'hauteur'=>0
					,'idArt'=>$arrIdDoc[4]
					); 
			break;
			case 'png':
				$imageInfo = getimagesize($objSite->infos["pathUpload"].$extention.'/'.$FicDst);
				$row = array(
					'titre'=>$_FILES['filename']['name']
					,'type'=>2
					,'desc'=>''
					,'fichier'=>'IMG/png/'.$FicDst
					,'taille'=>$_FILES['filename']['size']
					,'largeur'=>$imageInfo[0]
					,'hauteur'=>$imageInfo[1]
					,'idArt'=>$arrIdDoc[4]
					); 
			break;
			case 'gif':
				$imageInfo = getimagesize($objSite->infos["pathUpload"].$extention.'/'.$FicDst);
				$row = array(
					'titre'=>$_FILES['filename']['name']
					,'type'=>3
					,'desc'=>''
					,'fichier'=>'IMG/gif/'.$FicDst
					,'taille'=>$_FILES['filename']['size']
					,'largeur'=>$imageInfo[0]
					,'hauteur'=>$imageInfo[1]
					,'idArt'=>$arrIdDoc[4]
					); 
			break;
			default:
				$add = false;
		}
		
		if($add) $doc->AddNew($row);
		
	}
	
	
	/*
	 * Synchronise le local avec le serveur,
	 * r�cup�re les nouvelles donn�es du serveur,
	 * actualise les id rubriques et articles
	 * et import les nouvelles rubriques et articles du serveur
	 */
	function Synchronise($siteSrc, $siteDst, $idAuteur){
    			    	
		if(TRACE)
			echo "ExeAjax:Synchronise:idAuteur $idAuteur<br/>";

		/*$urlAdmin = $objSiteSync->infos["urlExeAjax"]."?f=GetAdminRub&idAuteur=".$idAuteur;
		
		if(TRACE)
			echo "ExeAjax:Synchronise:urlAdmin=$urlAdmin<br/>";
		
		$pageDebut = GetCurl($urlAdmin);
		$arrliste = unserialize($pageDebut);
		
		if(TRACE)
			echo "ExeAjax:Synchronise:liste=";
		
		foreach ($arrliste as $row) {
			echo $row['id_rubrique']." ".$row['id_auteur'];
		}*/
			
		$synchro = new Synchro($siteSrc, $siteDst);
    	$xmlUrl = $synchro->synchronise($idAuteur);
    	$url = $siteDst->infos["urlExeAjax"]."?f=SynchroImport&idAuteur=".$idAuteur;
		if(TRACE)
			echo "ExeAjax:Synchronise:url=$url<br/>";

		$page = UploadCurl($xmlUrl, $url);
		
		$posDeb = strrpos($page, "<?xml version=\"1.0\"?>");
		$posFin = strrpos($page, "</documents>");
		if(TRACE){
			//echo "ExeAjax:Synchronise:PAGE ::: ".$page;
			echo " ExeAjax:Synchronise:posDeb ".$posDeb;
			echo " ExeAjax:Synchronise:posFin ".$posFin;
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
					$synchro->import($path, true);
					$synchro->ReInitId("spip_rubriques", "id_rubrique");
					$synchro->ReInitId("spip_articles", "id_article");
			}
		}
    }

	function GetFilAriane($jsParam, $id){
		global $objSite;
		
		//r�cup�re le granulat
		$xul = new Xul($objSite, $id);
		$FilAriane = $xul->GetFilAriane($jsParam);
		return $FilAriane;
	}
	
	function AddXmlDonnee($url,$objSite)
	{
		//if(TRACE)
			echo "ExeAjax:AddXmlDonnee:d�but importation $url ".date(DATE_W3C)."<br/>";
		$g = new Grille($objSite);
		$g->AddXmlDonnee($url);
		//if(TRACE)
			echo "--- ExeAjax:AddXmlDonnee:fin importation ".date(DATE_W3C)."<br/>";
	}

	/*
	 * R�cup�re le fichier upload�, 
	 * r�alise l'import sur le serveur 
	 * et synchronise les nouvelles rubriques et articles du serveur
	 * 
	 */
	function SynchroImport($objSite,$idAuteur) {	
		
		$debug = true;
		
		if(TRACE){
			echo "ExeAjax:SynchroImport:idAuteur=".$idAuteur."<br/>";
			print_r($_POST);
			print_r($_FILES);
		}
		
		//if ((isset($_FILES['file']['name'])&&($_FILES['nom_du_fichier']['error'] == UPLOAD_ERR_OK))) {
			if(TRACE){
				echo "ExeAjax:SynchroImport:PATH = ".PathRoot."/param/";
			}
			if($debug){
				$src = PathRoot."/param/synchroExport-8.xml";
			}else{
				$chemin_destination = PathRoot."/param/";
				move_uploaded_file($_FILES['file']['tmp_name'], $chemin_destination.$_FILES['file']['name']);			
				$src = $chemin_destination.$_FILES['file']['name'];
			}
			if(TRACE){
				echo "ExeAjax:SynchroImport:urlSRC = ".$src;
			}
			
			$sync = new Synchro($objSite,-1);
						
			$reponseSynch = $sync->import($src, false);
			$sync->AddHistoriqueSynchro($src, $idAuteur);
			
			$doc = new DOMDocument();
			$doc->loadXML($reponseSynch);
			
			$doc2 = new DOMDocument();
			$xmlUrl = $sync->synchronise($idAuteur);
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
		//}
	}
	
	function GetAdminRub($idAuteur) {
		global $objSite;
		
		//echo 'ICI';
		$sync = new Synchro($objSite,-1);
		$arrliste = $sync->GetAdminRub($idAuteur);
		//print_r($arrliste);
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
	
	function SetVal($idGrille,$idDon,$champ,$val, $login){
	
		global $objSite, $ppp;
		$g = new Grille($objSite,$idGrille);

		//modifie la valeur 
		$row = array("grille"=>$idGrille,"champ"=>$champ,"valeur"=>utf8_decode($val));
		if(TRACE)
			echo "ExeAjax:SetVal:row=".print_r($row)."<br/>";
		
		if($champ!="Modif" && $champ!="Sup" && $val!=$objSite->infos["MOT_CLEF_OBS"]) //151 mot clef observations
			$g->SetChamp($row, $idDon);

		//gestion du workflow
		$xul = $g->GereWorkflow($row, $idDon);		
		
		//gestion de la sc�narisation
		if($idGrille==59 && $_SESSION['version']=="V2" && $ppp==-1)
			$xul = $g->GereScenarisation($row, $idDon);		
		
		if(TRACE)
			echo "ExeAjax:SetVal:ppp=".$ppp."<br/>";
		if ($ppp==1){
			$pppxul = new Xul($objSite);
			return $pppxul->GetPopUp($xul,"Signalement probl�me ".$g->GetValeur($idDon,"ligne_1"), $login);
		} 
		if ($ppp==2){
			$pppxul = new Xul($objSite);
			return $pppxul->GetPopUp($xul,"Observations ".$g->GetValeur($idDon,"ligne_1"), $login);
		} 
		
		
		return $xul;

	}

	
	function DelVal($idGrille,$idDon,$champ,$val){
	
		global $objSite;
		$g = new Grille($objSite);

		//modifie la valeur 
		$row = array("grille"=>$idGrille,"champ"=>$champ,"valeur"=>utf8_decode($val));
		$g->DelChamp($row, $idDon);
		
		return utf8_decode("donn�e supprim�e = ".$val);
	}
	
	function GetTree($type,$Cols,$id,$objSite){
		
		
		//r�cup�ration des colonnes
		$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/Querys/Query[@fonction='GetTreeChildren_".$type."']/col";
		$Cols = $objSite->XmlParam->GetElements($Xpath);		

		
		//une seule s�lection possible seltype='single' onselect=\"GetTreeSelect('tree".$type."','TreeTrace',2)" seltype='multiple' single
		//	class='editableTree' 			width='100px' height='100px' 

		//r�cup�ration des js
		$Xpath = "/XmlParams/XmlParam[@nom='".$objSite->scope['ParamNom']."']/Querys/Query[@fonction='GetTreeChildren_".$type."']/js";
		$js = $objSite->GetJs($Xpath, array($type,$id));
		$objXul = new Xul($objSite);
		//$tree = $objXul->GetTree($type,$Cols,$js,$id); 
		
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

	function GetTreeProb($idRub){
		global $objSite;
		$g = new Grille($objSite);
		
		$xul = $g->GetTreeProb($idRub);

		//header('Content-type: application/vnd.mozilla.xul+xml');
		//$xul = "<box>".$xul."</box>";

		return $xul;
		
	}
	
	function GetTreeObs($idRub){
		global $objSite;
		$g = new Grille($objSite);
		
		$xul = $g->GetTreeObs($idRub);

		//header('Content-type: application/vnd.mozilla.xul+xml');
		//$xul = "<box>".$xul."</box>";

		return $xul;
		
	}
	
	function GetMenuPopUp($idRub,$objSite,$type){

		$xul = new Xul($objSite);
			
		return $xul->GetMenuPopUp($idRub,$type);
	
	}
	
	function GetTreeCsv($idRub){
		global $objSite;
		$g = new Grille($objSite);
		
		return $g->GetTreeCsv($idRub);

		//header('Content-type: application/vnd.mozilla.xul+xml');
		//$xul = "<box>".$xul."</box>";

		//return $xul;
		
	}
	
	function AddGrilles($idRubSrc, $idRubDst, $login, $redon){
		global $objSite;
		$g = new Grille($objSite);
		$xul = $g->AddGrilles($idRubSrc, $idRubDst, $redon);

		//header('Content-type: application/vnd.mozilla.xul+xml');
		//$xul = "<box>".$xul."</box>";

		return $xul;
		
	}
	
	function AddNewGrille($idRubSrc, $idRubDst, $trs, $objSite)		{

		if(TRACE)
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
		//ajoute les QuestionsR�ponses
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
		//$idGen = $g->SetNewEnfant("Param�tres g�n�raux espace");
		//$gGen = new Granulat($idGen,$objSite);
		
		//ajoute une sous-rubrique espace gen->�clairage
		$id = $g->SetNewEnfant("Eclairage");
		//ajoute les QuestionsR�ponses
		$grille->AddQuestionReponse(71,$id);

		//ajoute une sous-rubrique espace gen->Equipements et dispositifs de commande
		$id = $g->SetNewEnfant("Commandes");
		//ajoute les QuestionsR�ponses
		$grille->AddQuestionReponse(70,$id);
		
		//ajoute une sous-rubrique espace gen->Pentes et ressauts
		$id = $g->SetNewEnfant("Pentes et ressauts");
		//ajoute les QuestionsR�ponses
		$grille->AddQuestionReponse(69,$id);
				
		//ajoute une sous-rubrique espace gen->Signal�tique
		$id = $g->SetNewEnfant("Signal�tique");
		//ajoute les QuestionsR�ponses
		$grille->AddQuestionReponse(72,$id);
				
		//ajoute une sous-rubrique espace gen->Sols, murs et plafonds
		$id = $g->SetNewEnfant("Sols, murs et plafonds");
		//ajoute les QuestionsR�ponses
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
		//$idGen = $g->SetNewEnfant("Param�tres g�n�raux espace");
		//$gGen = new Granulat($idGen,$objSite);
		
		//ajoute une sous-rubrique espace gen->Cheminement ext�rieur
		$id = $g->SetNewEnfant("Cheminement");
		//ajoute les QuestionsR�ponses
		$grille->AddQuestionReponse(64,$id);
						
		//ajoute une sous-rubrique espace gen->Sol ext�rieur
		$id = $g->SetNewEnfant("Sol ext�rieur");
		//ajoute les QuestionsR�ponses
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
		//$idGen = $g->SetNewEnfant("Param�tres g�n�raux espace");
		//$gGen = new Granulat($idGen,$objSite);
		
		//ajoute une sous-rubrique espace gen->Cheminement ext�rieur
		$id = $g->SetNewEnfant("Cheminement ext�rieur");
		//ajoute les QuestionsR�ponses
		$grille->AddQuestionReponse(64,$id);
		
		//ajoute une sous-rubrique espace gen->Equipements et dispositifs de commande
		$id = $g->SetNewEnfant("Equipements et dispositifs de commande");
		//ajoute les QuestionsR�ponses
		$grille->AddQuestionReponse(628,$id);
				
		//ajoute une sous-rubrique espace gen->Sol ext�rieur
		$id = $g->SetNewEnfant("Sol ext�rieur");
		//ajoute les QuestionsR�ponses
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
		//$idGen = $g->SetNewEnfant("Param�tres g�n�raux espace");
		//$gGen = new Granulat($idGen,$objSite);
		
		//ajoute une sous-rubrique espace gen->Cheminement ext�rieur
		$id = $g->SetNewEnfant("Cabine d'ascenseur");
		//ajoute les QuestionsR�ponses
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
		// pour r�cup�rer le parent
		$g = new Granulat($idRubDst,$objSite);
		
		// pour cr�er un nouvel enfant
		$idGen = $g->SetNewEnfant("Territoire Sans Nom ".date('j/m/y - H:i:s'));
		
		$grille = new Grille($objSite);
		
		$grille->AddGrilles($idRubSrc, $idGen);
		
		$gra = new Granulat($idGen, $objSite);
		
		$idArticle = $gra->GetArticle();
		$gra->AddAuteur($idArticle, $idAuteur);
		
		$xul = $grille->GetXulTab('Terre', $idGen);
		
		//if ($mot != -1) $g->SetMotClef($mot,$idGen);
		
		// pour renvoyer la mise � jour du tree
		$tree = GetTree('terre',-1,-1,$objSite);
		
		return $xul;
	}
		
	function AddPlacemark($idRubDst, $kml, $bbox){
		global $objSite;
		$g = new Grille($objSite);
		//cr�ation de la grille g�olocalisation
		$idDon = $g->AddDonnee($idRubDst, $objSite->infos["GRILLE_GEO"], false);
		
		//ajoute la valeur du kml
		$row = array("champ"=>"texte_1","valeur"=>$kml);
		$g->SetChamp($row, $idDon);
	
		if($bbox!=-1){
			$values = explode(",", $bbox);
			foreach($values as $k=>$val){
				$row = array("champ"=>$k,"valeur"=>$val);
				$g->SetChamp($row, $idDon);			
			}
		}else{
			$row = array("champ"=>'ligne_1',"valeur"=>$objSite->infos["DEF_LAT"]);
			$g->SetChamp($row, $idDon);			
			$row = array("champ"=>'ligne_2',"valeur"=>$objSite->infos["DEF_LNG"]);
			$g->SetChamp($row, $idDon);			
			$row = array("champ"=>'ligne_3',"valeur"=>$objSite->infos["DEF_ZOOM"]);
			$g->SetChamp($row, $idDon);			
			$row = array("champ"=>'ligne_4',"valeur"=>$objSite->infos["DEF_ZOOM"]+4);
			$g->SetChamp($row, $idDon);			
			$row = array("champ"=>'ligne_7',"valeur"=>"inconnue");
			$g->SetChamp($row, $idDon);			
			$row = array("champ"=>'mot_1',"valeur"=>$objSite->infos["MOT_CLEF_DEF_TYPE_CARTE"]);
			$g->SetChamp($row, $idDon);			
			
		}
		
		//header('Content-type: application/vnd.mozilla.xul+xml');
		//$xul = "<box>".$xul."</box>";

		return "donn�e cr�� = ".$idDon;
		
	}

	function ClearArticle($idDonnee, $idRub) {
		
		global $objSite;
		
		$g = new Grille($objSite);
		$synchro = new Synchro($objSite, -1);
		$idArticle = $synchro->GetArticleDonnee($idDonnee);
		
		if (TRACE) echo '+++ ExeAjax:ClearArticle:idArticle:'.$idArticle;
		if ($idArticle !=-1) {	
			$critere = $g->GetValeur($idDonnee, 'ligne_3');
			if (TRACE) echo '+++ ExeAjax:ClearArticle:critere:'.$critere;
			$idRubrique = $g->GetRubDon($idDonnee);
			if (TRACE) echo '+++ ExeAjax:ClearArticle:idRubrique:'.$idRubrique;
			$synchro->SupprimerArticle($idArticle);
			//$idRub = $synchro->GetRubrique($idArticle);
			if ($idRubrique!=-1) {
				$arrListeDonnees = $synchro->GetHistoriqueCritere($idRubrique, $critere, 60, 'ligne_3');
				//if (sizeof($arrListeDonnees)!=0) {
				if ($arrListeDonnees!=null) {
					if (TRACE) echo '+++ ExeAjax:ClearArticle:arrListeDonnees:'.$arrListeDonnees[0]['id'];
					$reponse = $g->GetValeur($arrListeDonnees[0]['id'], 'ligne_5');
					if (TRACE) echo '+++ ExeAjax:ClearArticle:reponse:'.$reponse;
					$idMot = $g->GetIdMot($reponse);
					if (TRACE) echo '+++ ExeAjax:ClearArticle:idMot:'.$idMot;
					$arrListeDonnee = $synchro->GetHistoriqueCritere($idRubrique, $critere, 59, 'ligne_1');
					//if (sizeof($arrListeDonnee)!=0) {
					if ($arrListeDonnee!=null) {
						if (TRACE) echo '+++ ExeAjax:ClearArticle:arrListeDonnee[0]:'.$arrListeDonnee[0]['id'];
						$row = array("grille"=>59,"champ"=>'mot_1',"valeur"=>$idMot);
						$g->SetChamp($row, $arrListeDonnee[0]['id']);
					}
				} else {
					$arrListeDonnee = $synchro->GetHistoriqueCritere($idRubrique, $critere, 59, 'ligne_1');
					//if (sizeof($arrListeDonnee)!=0) {
					if ($arrListeDonnee!=null) {
						if (TRACE) echo '+++ ExeAjax:ClearArticle:arrListeDonnee[0]:'.$arrListeDonnee[0]['id'];
						$row = array("grille"=>59,"champ"=>'mot_1',"valeur"=>124); // 124 : N. A.
						$g->SetChamp($row, $arrListeDonnee[0]['id']); 
					}
				}
			}
		}
		if (TRACE) echo '+++ ExeAjax:ClearArticle:idRub:'.$idRub;
		$xul = $g->GetTreeProb($idRub);

		return $xul;
	}
	
	function ClearArticleObs($idDonnee, $idRub) {
		
		global $objSite;
		
		$synchro = new Synchro($objSite, -1);
		
		if (TRACE) echo '+++ ExeAjax:ClearArticleObs:idDonnee:'.$idDonnee;
		$idArticle = $synchro->GetArticleDonnee($idDonnee);
	
		if ($idArticle !=-1) {	
			$synchro->SupprimerArticle($idArticle);
		}
		
		$g = new Grille($objSite);
		if (TRACE) echo '+++ ExeAjax:ClearArticleObs:idRub:'.$idRub;
		$xul = $g->GetTreeObs($idRub);

		return $xul;
	}
	
	function ClearRubriqueParent($idRub) {
		
		global $objSite;
		
		$gra = new Granulat($idRub, $objSite);
		$arrListeRub = $gra->GetListeEnfants();
		
		$synchro = new Synchro($objSite, -1);
		$g = new Grille($objSite);
		
		foreach($arrListeRub as $rubrique) {
			if (TRACE) echo '+++ ExeAjax:ClearRubriqueParent:rubrique:'.$rubrique['id'];
			$arrListArticles = $synchro->GetArticles($rubrique['id'], $objSite->infos["GRILLE_SIG_PROB"]);
			if (sizeof($arrListArticles) !=0) {
				foreach ($arrListArticles as $article){
					if (TRACE) echo '+++ ExeAjax:ClearRubriqueParent:idDonnee:'.$article['idDonnee'];
					$critere = $g->GetValeur($article['idDonnee'], 'ligne_3');
					if (TRACE) echo '+++ ExeAjax:ClearRubriqueParent:critere:'.$critere;
					if ($rubrique['id']!=-1) {
						$arrListeDonnee = $synchro->GetHistoriqueCritere($rubrique['id'], $critere, $objSite->infos["GRILLE_REP_CON"], 'ligne_1');
						if ($arrListeDonnee!=null) {
							if (TRACE) echo '+++ ExeAjax:ClearRubriqueParent:arrListeDonnee[0]:'.$arrListeDonnee[0]['id'];
							$row = array("grille"=>$objSite->infos["GRILLE_REP_CON"],"champ"=>'mot_1',"valeur"=>124); // 124 : N. A.
							$g->SetChamp($row, $arrListeDonnee[0]['id']); 
						}
					}
				}
				$synchro->SupprimerArticles($arrListArticles);
			}
		}
		
		$xul = $g->GetTreeProb($idRub);
		return $xul;
	}
	
	function ClearRubriqueParentObs($idRub) {
		
		global $objSite;
		
		$gra = new Granulat($idRub, $objSite);
		$arrListeRub = $gra->GetListeEnfants();
		
		$synchro = new Synchro($objSite, -1);
		$g = new Grille($objSite);
		
		foreach($arrListeRub as $rubrique) {
			if (TRACE) echo '+++ ExeAjax:ClearRubriqueParentObs:rubrique:'.$rubrique['id'];
			$arrListArticles = $synchro->GetArticles($rubrique['id'], $objSite->infos["GRILLE_OBS"]);
			if (sizeof($arrListArticles) !=0) 
				$synchro->SupprimerArticles($arrListArticles);
		}
	
		$xul = $g->GetTreeObs($idRub);
		return $xul;
	}
		
	function ClearRubrique($idRub, $idParentRub) {
		
		global $objSite;
		
		$synchro = new Synchro($objSite, -1);
		$g = new Grille($objSite);
		
		$arrListArticles = $synchro->GetArticles($idRub, $objSite->infos["GRILLE_SIG_PROB"]);
		
		if ($arrListArticles !=null) {
			foreach ($arrListArticles as $article){
				if (TRACE) echo '+++ ExeAjax:ClearRubrique:id_donnee:'.$article['idDonnee'];
				$critere = $g->GetValeur($article['idDonnee'], 'ligne_3');
				if (TRACE) echo '+++ ExeAjax:ClearRubrique:critere:'.$critere;
				if ($idRub!=-1) {
					$arrListeDonnee = $synchro->GetHistoriqueCritere($idRub, $critere, 59, 'ligne_1');
					if ($arrListeDonnee!=null) {
						if (TRACE) echo '+++ ExeAjax:ClearRubrique:arrListeDonnee[0]:'.$arrListeDonnee[0]['id'];
						$row = array("grille"=>59,"champ"=>'mot_1',"valeur"=>124); // 124 : N. A.
						$g->SetChamp($row, $arrListeDonnee[0]['id']); 
					}
				}
			}
			$synchro->SupprimerArticles($arrListArticles);
		}
		
		$xul = $g->GetTreeProb($idParentRub);

		return $xul;
	}
	
	function ClearRubriqueObs($idRub, $idParentRub) {
		
		global $objSite;
		
		$synchro = new Synchro($objSite, -1);
		$g = new Grille($objSite);
		
		$arrListArticles = $synchro->GetArticles($idRub, $objSite->infos["GRILLE_OBS"]);
		
		if ($arrListArticles !=null) {
			$synchro->SupprimerArticles($arrListArticles);
		}
	
		$xul = $g->GetTreeObs($idParentRub);

		return $xul;
	}
	
	/*
	 * Nettoye les donn�es des articles inutilis�es
	 * 
	 */
	function CleanArticle($deb, $fin) {
		global $objSite;
		
		$synchro = new Synchro($objSite, -1);
		$synchro->CleanArticle($deb, $fin);
	}
	
	
	/*
	 * Nettoye les donn�es des rubriques inutilis�es
	 * 
	 */
	function CleanRubrique($deb, $fin) {
		global $objSite;
		
		$synchro = new Synchro($objSite, -1);
		$synchro->CleanRubrique($deb, $fin);
	}
		
	function CopyRub($idRub) {
		global $objSite;
		
		if (TRACE) echo '+++ ExeAjax:CopyRub:idRub:'.$idRub;
		
		$g = new Granulat($idRub, $objSite);
		$idParent = $g->GetParent($g->id);
		$g->CopyRub($idParent);
	}
	
	function AddVersion() {
		global $objSite;
		$synchro = new Synchro($objSite, -1);
		$synchro->AddVersion();
	}
	
	function ChangeAutoIncrement($table, $val) {
		global $objSite;
		$synchro = new Synchro($objSite, -1);
		$synchro->ChangeAutoIncrement($table, $val);
	}
	function explorerDir($dir,$objSite){
		$dir = PathRoot."/param/".$dir;
		$dossier = opendir($dir);
		while($entree = readdir($dossier)){
			if ($entree != "." && $entree != ".." && substr($entree,0,1) != ".") {
			    AddXmlDonnee($dir."/".$entree,$objSite);
				//echo $entree."</br>";
			}
		}
   
	}
	
?>
