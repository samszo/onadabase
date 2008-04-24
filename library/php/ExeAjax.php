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
			$resultat = NewRubrique($_GET['idRubSrc'], $_GET['idRubDst']);
			break;
		case 'Synchronise':
			//$resultat = NewRubrique($_GET['src'], $_GET['dst'], $_GET['type'], $cols);
			$resultat = Synchronise($siteSrc, $siteDst=-1, $idAuteur=6, $_GET['type']);
			break;
		case 'AddXmlFile':
			$resultat = AddXmlFile($_GET['src']);
			break;
	}

	echo  utf8_encode($resultat);	

	/*function Synchroniser($objSite){
		return GetTree("terre",-1,-1,$objSite);
	
}*/
	
	function  Synchronise($siteSrc, $siteDst=-1, $idAuteur=6, $type){
    	
		global $objSite;
		global $objSiteSync; //Mundi
		    	
		if(TRACE)
			echo $type;
    	$synchro = new Synchro($objSiteSync, $objSite);
    	//$xmlSrc = $synchro->synchronise($objSiteSync, $objSite, $idAuteur, $type);
    	//$synchro->synchronise($objSiteSync, $objSite, $idAuteur, $type);
    	$xmlSrc="http://www.mundilogiweb.com/onadabase/library/php/ExeAjax.php?f=AddXmlFile&src=<?xml version=\"1.0\"?><documents><rubrique id=\"1058\" idParent=\"114\">Troncon nord<motclef></motclef><article id=\"1416\">Troncon nord<date>2008-04-23 10:09:46</date><maj>2008-04-23 10:09:46</maj><auteur>5</auteur><donnees><grille>62</grille><champs><champ>ligne_2</champ><champ>ligne_3</champ><champ>ligne_4</champ><champ>ligne_5</champ><champ>ligne_1</champ><champ>select_1</champ></champs><donnee><date>2008-04-15 16:42:02</date><maj>2008-04-15 16:42:02</maj><valeur></valeur><valeur></valeur><valeur></valeur><valeur></valeur><valeur>Troncon nord</valeur><valeur>select_1_7</valeur></donnee></donnees></article><rubrique id=\"2032\" idParent=\"1058\">Cheminement sur la voirie 21/04/08 - 13:44:58<motclef>130</motclef><article id=\"1644\">Cheminement sur la voirie 21/04/08 - 13:44:58<date>2008-04-21 13:44:58</date><maj>2008-04-22 14:47:26</maj><auteur>5</auteur><donnees><grille>59</grille><champs><champ>ligne_2</champ><champ>ligne_1</champ><champ>mot_1</champ></champs><donnee><date>2008-04-21 13:44:58</date><maj>2008-04-21 13:44:58</maj><valeur>Le sol n'est pas meuble</valeur><valeur>cr_voirie_chem_01</valeur><valeur>2</valeur></donnee><donnee><date>2008-04-21 13:44:58</date><maj>2008-04-21 13:44:58</maj><valeur>Le revetement n'est pas glissant</valeur><valeur>cr_voirie_chem_02</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:44:59</date><maj>2008-04-21 13:44:59</maj><valeur>Le cheminement ne comporte pas d'obstacles au sol</valeur><valeur>cr_voirie_chem_03</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:44:59</date><maj>2008-04-21 13:44:59</maj><valeur>S'il existe sur le cheminement des obstacles en porte--faux alors ils laissent un passage libre = 220 cm de haut</valeur><valeur>cr_voirie_chem_04</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:44:59</date><maj>2008-04-21 13:44:59</maj><valeur>S'il existe sur le cheminement des obstacles en saillie latrale de plus de 15 cm laissant un passage libre inf 220 cm de hauteur, alors ils sont rappels par un ment bas installe au max 40 cm du sol ou par une surpaisseur au sol d'au moins 3 cm</valeur><valeur>cr_voirie_chem_05</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:44:59</date><maj>2008-04-21 13:44:59</maj><valeur>Prsence d'un guide tactilement et visuellement constrat le long du cheminement</valeur><valeur>cr_voirie_chem_06</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:44:59</date><maj>2008-04-21 13:44:59</maj><valeur>La largeur du cheminement est = 140 cm sans obstacle</valeur><valeur>cr_voirie_chem_07</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:44:59</date><maj>2008-04-21 13:44:59</maj><valeur>Si absence de murs et d'obstacles de part et d'autre du cheminement alors sa largeur est =  120 cm</valeur><valeur>cr_voirie_chem_08</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:44:59</date><maj>2008-04-21 13:44:59</maj><valeur>Le devers est infrieur ou gal2%</valeur><valeur>cr_voirie_chem_09</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:44:59</date><maj>2008-04-21 13:44:59</maj><valeur>Pente infrieure  5%</valeur><valeur>cr_voirie_chem_10</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:44:59</date><maj>2008-04-21 13:44:59</maj><valeur>Si impossibilit technique alors pente infrieure  8% sur une distance infrieure ougale 2 m</valeur><valeur>cr_voirie_chem_11</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:44:59</date><maj>2008-04-21 13:44:59</maj><valeur>Si impossibilit technique alors pente infrieure  12% sur une distance infrieure ougale  50 cm</valeur><valeur>cr_voirie_chem_12</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:44:59</date><maj>2008-04-21 13:44:59</maj><valeur>Si pente suprieure 4% alors palier de repos en bas</valeur><valeur>cr_voirie_chem_13</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:44:59</date><maj>2008-04-21 13:44:59</maj><valeur>Si pente suprieure  4% alors palier de repos en haut</valeur><valeur>cr_voirie_chem_14</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:44:59</date><maj>2008-04-21 13:44:59</maj><valeur>Si cheminement pentu continu alors palier de repos tous les 10 m</valeur><valeur>cr_voirie_chem_15</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:44:59</date><maj>2008-04-21 13:44:59</maj><valeur>Si cheminement pentu continu alors prsence d'un main courante ergonomique et constrate</valeur><valeur>cr_voirie_chem_16</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:45:00</date><maj>2008-04-21 13:45:00</maj><valeur>Le palier est horizontal</valeur><valeur>cr_voirie_chem_17</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:45:00</date><maj>2008-04-21 13:45:00</maj><valeur>Le palier mesure 120 x 140 cm sans obstacle</valeur><valeur>cr_voirie_chem_18</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:45:00</date><maj>2008-04-21 13:45:00</maj><valeur>Un palier de repos prsent chaque bifurcation du cheminement</valeur><valeur>cr_voirie_chem_19</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:45:00</date><maj>2008-04-21 13:45:00</maj><valeur>Un palier de repos prsent chaque bifurcation du cheminement possde un espace de manoeuvre de 200 cm de diamtre</valeur><valeur>cr_voirie_chem_20</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:45:00</date><maj>2008-04-21 13:45:00</maj><valeur>Si rupture de niveau de plus de 40 cm alors garde-corps le long de la rupture de niveau</valeur><valeur>cr_voirie_chem_21</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:45:00</date><maj>2008-04-21 13:45:00</maj><valeur>Le garde au corps est contrast par rapport l'environnement, plein et continu jusqu'au sol</valeur><valeur>cr_voirie_chem_22</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:45:00</date><maj>2008-04-21 13:45:00</maj><valeur>Un ressaut est infrieure ou gal  2 cm</valeur><valeur>cr_voirie_chem_23</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:45:00</date><maj>2008-04-21 13:45:00</maj><valeur>Un ressaut est infrieur ou gal 4 cm si il est en chanfrein1/3</valeur><valeur>cr_voirie_chem_24</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:45:00</date><maj>2008-04-21 13:45:00</maj><valeur>La distance entre 2 ressauts est infrieure ou gale  250 cm</valeur><valeur>cr_voirie_chem_25</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-21 13:45:00</date><maj>2008-04-21 13:45:00</maj><valeur>Absence de pente 'pas d'ne' </valeur><valeur>cr_voirie_chem_26</valeur><valeur>124</valeur></donnee></donnees></article></rubrique><rubrique id=\"2039\" idParent=\"1058\">Passage pitons 22/04/08 - 14:54:52<motclef>133</motclef><article id=\"1655\">Passage pitons 22/04/08 - 14:54:52<date>2008-04-22 14:54:52</date><maj>2008-04-22 14:54:52</maj><auteur>5</auteur><donnees><grille>59</grille><champs><champ>ligne_2</champ><champ>ligne_1</champ><champ>mot_1</champ></champs><donnee><date>2008-04-22 14:54:52</date><maj>2008-04-22 14:54:52</maj><valeur>Prsence d'un 'bateau' au droit de chaque traverse</valeur><valeur>cr_voirie_piton_01</valeur><valeur>2</valeur></donnee><donnee><date>2008-04-22 14:54:52</date><maj>2008-04-22 14:54:52</maj><valeur>Le 'bateau' a une largeur=  120 cm</valeur><valeur>cr_voirie_piton_02</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-22 14:54:52</date><maj>2008-04-22 14:54:52</maj><valeur>Le 'bateau' a une largeur =  140 cm</valeur><valeur>cr_voirie_piton_03</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-22 14:54:52</date><maj>2008-04-22 14:54:52</maj><valeur>Le 'bateau' respecte les rgles des pentes et des ressauts</valeur><valeur>cr_voirie_piton_04</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-22 14:54:52</date><maj>2008-04-22 14:54:52</maj><valeur>Si la largeur du trottoir le permet, un passage horizontal d'au moins 80 cm est rserv au droit des traverses pour pitons entre la pente du plan inclin vers la chausse et le cadre bti ou tout autre obstacle</valeur><valeur>cr_voirie_piton_05</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-22 14:54:52</date><maj>2008-04-22 14:54:52</maj><valeur>Si la largeur du trottoir le permet, un espace de manoeuvre de 200 cm de diamtre est rserv au droit des traverses pour pitons entre la pente du plan inclin vers la chausse et le cadre ti ou tout autre obstacle</valeur><valeur>cr_voirie_piton_06</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-22 14:54:52</date><maj>2008-04-22 14:54:52</maj><valeur>Prence d'une bande d'veil de vigilance au droit des traverses</valeur><valeur>cr_voirie_piton_07</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-22 14:54:52</date><maj>2008-04-22 14:54:52</maj><valeur>La bande d'veil de vigilance a une largeur de 42 cm</valeur><valeur>cr_voirie_piton_08</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-22 14:54:52</date><maj>2008-04-22 14:54:52</maj><valeur>La bande d'veil de vigilance est situe  50 cm du bord du trottoir</valeur><valeur>cr_voirie_piton_09</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-22 14:54:53</date><maj>2008-04-22 14:54:53</maj><valeur>Les passages pour pitons possdent un marquage rglementaire (bandes contrastes de 50 cm)</valeur><valeur>cr_voirie_piton_10</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-22 14:54:53</date><maj>2008-04-22 14:54:53</maj><valeur>Le passage pour pitons est visuellement constrat (permet d'en dtecter les limites)</valeur><valeur>cr_voirie_pton_11</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-22 14:54:53</date><maj>2008-04-22 14:54:53</maj><valeur>Le passage pour pitons est tactilement constrat (permet d'en dtecter les limites)</valeur><valeur>cr_voirie_piton_12</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-22 14:54:53</date><maj>2008-04-22 14:54:53</maj><valeur>Les signaux pour pitons sont complts par des dispositifs permettant aux dficients visuels de connatre les priodes durant lesquelles il est possible de traverser</valeur><valeur>cr_voirie_piton_13</valeur><valeur>124</valeur></donnee><donnee><date>2008-04-22 14:54:53</date><maj>2008-04-22 14:54:53</maj><valeur>Si un cheminement pour pitons comporte une chicane, sans alternative, alors il permet le passage d'un fauteuil roulant d'un gabarit de 80 x 130 cm</valeur><valeur>cr_voirie_piton_14</valeur><valeur>124</valeur></donnee></donnees></article><article id=\"1656\">cr_voirie_piton_01 22/04/08 - 14:56:07<date>2008-04-22 14:56:07</date><maj>2008-04-22 14:56:07</maj><auteur>5</auteur><donnees><grille>60</grille><champs><champ>ligne_3</champ><champ>ligne_1</champ><champ>ligne_2</champ><champ>mot_1</champ><champ>texte_1</champ><champ>ligne_4</champ><champ>texte_2</champ><champ>mot_2</champ></champs><donnee><date>2008-04-22 14:56:07</date><maj>2008-04-22 14:56:07</maj><valeur>cr_voirie_piton_01</valeur><valeur>hth</valeur><valeur>hhh</valeur><valeur>141</valeur><valeur>ttt</valeur><valeur>ttjjj</valeur><valeur>ooll</valeur><valeur>2</valeur></donnee></donnees></article></rubrique><rubrique id=\"2036\" idParent=\"1058\">Test<motclef></motclef><rubrique id=\"2038\" idParent=\"2036\">Sous test<motclef></motclef></rubrique></rubrique></rubrique></documents>";
    	$url = $objSiteSync->infos["urlExeAjax"]."?f=AddXmlFile&src=".$xmlSrc;
    	echo $url;
    	echo GetCurl($url);
    	
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

	function AddXmlFile($src) {
		
		echo "ExeAjax:AddXmlFile:<br/>";
		global $objSite;
		$gra = new Granulat(-1,$objSite);
		//$url = PathRoot."/param/synchroExport.xml";
		$gra->AddXmlFile($src);
		
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
	
	function NewRubrique($idRubSrc, $idRubDst) {
		global $objSite;

		
		echo '$idRubSrc '.$idRubSrc;
		echo '$idRubDst '.$idRubDst;
		// pour récupérer le parent
		$g = new Granulat($idRubDst,$objSite);
		
		// pour créer un nouvel enfant
		$idGen = $g->SetNewEnfant("Territoire Sans Nom ".date('j/m/y - H:i:s'));
		
		$grille = new Grille($objSite);
		
		$grille->AddGrilles($idRubSrc, $idGen);
		
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
?>
