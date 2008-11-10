<?php

require_once ("Constantes.php");

if(isset($_POST['login_uti'])) {
	$login=$_POST['login_uti'];
	$mdp=$_POST['mdp_uti'];
	if(TRACE)
		echo "ParamPage:post:$login, $mdp<br/>";
} else {
	$login=$_SESSION['loginSess'];
	$mdp=$_SESSION['mdpSess'];
	$idAuteur=$_SESSION['IdAuteur'];
	if(TRACE)
		echo "ParamPage:session:$login, $mdp, $idAuteur<br/>";
}

// vérification du site en cours
if(isset($_GET['site']))
	$site = $_GET['site'];
if(isset($_POST['site']))
	$site = $_POST['site'];
if(!$site)
	$site = DEFSITE;
if(isset($_SESSION['site']))
	$site=$_SESSION['site'];
$_SESSION['site']=$site;

if(!isset($_SESSION['ShowLegendeControle']))
	$_SESSION['ShowLegendeControle']=true;
if(!isset($_SESSION['ShowCarte']))
	$_SESSION['ShowCarte']=false;
if(!isset($_SESSION['ShowDocs']))
	$_SESSION['ShowDocs']=false;
if(!isset($_SESSION['ContEditAll']))
	$_SESSION['ContEditAll']=true;
if(!isset($_SESSION['ContEditPublie']))
	$_SESSION['ContEditPublie']=false;
	
	
	
if(TRACE)
	echo "ParamPage:session".print_r($_SESSION)."<br/>";

	
if(isset($_GET['type']))
	$type = $_GET['type'];
else
	$type = 'voirie';
	
if(isset($_GET['FicXml']))
	$FicXml = $_GET['FicXml'];
else
	$FicXml = XmlParam;

if(isset($_GET['ParamNom']))
	$ParamNom = $_GET['ParamNom'];
else
	$ParamNom = "GetOntoTree";

if(isset($_GET['box']))
	$box = $_GET['box'];
else
	$box = "singlebox";

if(isset($_GET['UrlNom']))
	$UrlNom = $_GET['UrlNom'];
else
	$UrlNom = "Traduction";
	
if(isset($_GET['So']))
	$So = $_GET['So'];
else
	$So = "Traduction";

if(isset($_GET['id']))
	$id = $_GET['id'];
else
	$id = $SITES[$site]["DEF_ID"];

if(isset($_GET['idDon']))
	$idDon = $_GET['idDon'];
else
	$idDon = -1;
	
$scope = array(
		"site" => $site
		,"type" => $type
		,"FicXml" => $FicXml
		,"ParamNom" => $ParamNom
		,"box" => $box
		,"UrlNom" => $UrlNom
		,"So" => $So
		,"id" => $id
		,"login" => $login
		);	
//print_r($scope);

$objSite = new Site($SITES, $site, $scope, false);
$objSiteSync = new Site($SITES, SYNCSITE, $scope, false);

if($id!=-1)
	$g = new Granulat($id,$objSite);
	
	
function ChercheAbo ($login, $mdp, $objSite)
	{
		// connexion serveur
		$link = mysql_connect($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"]) or die("Impossible de se connecter : " . mysql_error());	
		// Sélection de la base de données
		//mysql_select_db("solacc", $link);	
		mysql_select_db($objSite->infos["SQL_DB"], $link);	
		
		$sql = "SELECT id_auteur, nom, login, email  FROM spip_auteurs WHERE login = '".$login."' AND pass = md5( CONCAT(alea_actuel,'$mdp'))";
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	  	if(TRACE)
	  		echo "ParamPage:ChercheAbo:sql=".$sql."<br/>";
			
		mysql_close($link);
	  	$nbre_lignes = mysql_num_rows($req);
	  	if(TRACE)
	  		echo "ParamPage:ChercheAbo:nbre_lignes=".$nbre_lignes."<br/>";
		if ($nbre_lignes == 1)
		{
			while($resultat = mysql_fetch_assoc($req))
				{	
					$_SESSION['IdAuteur'] = $resultat['id_auteur'];
					$_SESSION['NomSess'] = $resultat['nom'];
					$_SESSION['EmailSess'] = $resultat['email'];
					$_SESSION['loginSess'] = $resultat['login'];	
					$_SESSION['IpSess'] = $_SERVER['REMOTE_ADDR'];
					$_SESSION['mdpSess'] = $mdp;
				}
			
		}
		else
		{
			include("diagnostic.php");
			exit;
		}
		if(TRACE)
		  	echo "ParamPage:ChercheAbo:session=".print_r($_SESSION)."<br/>";
	}
	
	

?>