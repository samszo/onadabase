<?php
require_once ("../../../param/ParamPage.php");
session_start();

extract($_SESSION,EXTR_OVERWRITE);
extract($_POST,EXTR_OVERWRITE);

if(isset($_SESSION['loginSess']))
{
	$login=$_SESSION['loginSess'];
	$mdp=$_SESSION['mdpSess'];
	$idAuteur=$_SESSION['IdAuteur'];
	
}
else
{
	$login=$_POST['login_uti'];
	$mdp=$_POST['mdp_uti'];
	//$_SESSION['type_controle'] = array ($_POST['type_controle1'], $_POST['type_controle2']);
	//$_SESSION['type_contexte'] = array ($_POST['type_contexte1'], $_POST['type_contexte2'], $_POST['type_contexte3'], $_POST['type_contexte4']);
}

function ChercheAbo ($login, $mdp, $objSite)
	{
		// connexion serveur
		$link = mysql_connect($objSite->infos["SQL_HOST"], $objSite->infos["SQL_LOGIN"], $objSite->infos["SQL_PWD"]) or die("Impossible de se connecter : " . mysql_error());	
		// Sélection de la base de données
		//mysql_select_db("solacc", $link);	
		mysql_select_db($objSite->infos["SQL_DB"], $link);	
		
		$sql = "SELECT id_auteur, nom, login, email  FROM spip_auteurs WHERE login = '".$login."' AND pass = md5( CONCAT(alea_actuel,'$mdp'))";
		//echo $sql;
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
			//echo $nbResultat."<br/>";
		mysql_close($link);
	  	$nbre_lignes = mysql_num_rows($req);
	  	//echo $nbre_lignes;
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
			include("log.php");
			exit;
		}
	}

	function SetVal($idGrille,$idDon,$champ,$val, $login){
	
		global $objSite, $ppp;
		$g = new Grille($objSite,$login);

		//modifie la valeur 
		$row = array("grille"=>$idGrille,"champ"=>$champ,"valeur"=>utf8_decode($val));
		if(TRACE)
			echo "ExeAjax:SetVal:row=".print_r($row)."<br/>";
		
		if($champ!="Modif" && $champ!="Sup" && $val!=151) //151 mot clef observations
			$g->SetChamp($row, $idDon);

		//gestion du workflow
		$xul = $g->GereWorkflow($row, $idDon);		

		if(TRACE)
			echo "ExeAjax:SetVal:ppp=".$ppp."<br/>";
		if ($ppp==1){
			$pppxul = new Xul($objSite);
			return $pppxul->GetPopUp($xul,"Signalement problème ".$g->GetValeur($idDon,"ligne_1"), $login);
		} 
		if ($ppp==2){
			$pppxul = new Xul($objSite);
			return $pppxul->GetPopUp($xul,"Observations ".$g->GetValeur($idDon,"ligne_1"), $login);
		} 
		
		
		return $xul;

	}
/*function Test ($syncSite)
	{
		// connexion serveur
		$link = mysql_connect($syncSite->infos["SQL_HOST"], $syncSite->infos["SQL_LOGIN"], $syncSite->infos["SQL_PWD"]) or die("Impossible de se connecter : " . mysql_error());	
		// Sélection de la base de données
		//mysql_select_db("solacc", $link);	
		mysql_select_db($syncSite->infos["SQL_DB"], $link) or die("Impossible de se connecter a la base : " . mysql_error());	
	}

Test (	$objSiteSync);*/
ChercheAbo ($login, $mdp, $objSite);

//SetVal(68, 19848, null, null, $login);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Paramètres de diagnostic</title>
<SCRIPT LANGUAGE="JavaScript">

    <!--
    if (window !=top ) {top.location=window.location;}
   //-->

</SCRIPT>
<style type="text/css">
#globalPass
	{
	position:absolute;
	left:50%; 
	top:50%;
	width:210px;
	height:416px;
	margin-top: -208px; /* moitié de la hauteur */
	margin-left: -105px; /* moitié de la largeur */
	border: 1px solid #FFFFFF;
	background-image:url(images/FondLog.jpg);
	background-repeat:no-repeat;
	background-color:#FFFFFF;
	font-family:Helvetica, sans-serif;
	font-size:15px;
	color:#000000;
    }

.BlocTextePass
	{
	width:230px;
	margin: 5px;
	margin-top:60px;
    }
</style>
</head>

<body bgcolor="#ffffff">
	<div id='globalPass'>
		<div class='BlocTextePass'>	
			<p align="center">Veuillez entrer les caractéristiques de votre diagnostic</p>		
			<form name="formulaire" method="post" action="index.php">
			<p align="center">Version : <SELECT name="version">
				<OPTION VALUE="v1" [SELECTED] >V1</OPTION>
				<OPTION VALUE="v2">V2</OPTION>
			</SELECT>
			</p>
			<p align="center">Type de critère : 
			<BR/>Souhaitable<input name="type_controle1" type="checkbox" value="multiple_1_2"/>
			 Réglementaire<INPUT type=checkbox name="type_controle2" value="multiple_1_1"/> 
			</p>
			<p align="center">Contexte réglementaire : 
			<BR/>Travail<input name="type_contexte1" type="checkbox" value="multiple_2_1"/>
			 ERP/IOP<INPUT type=checkbox name="type_contexte2" value="multiple_2_2"/> 
			<BR/>Voirie<INPUT type=checkbox name="type_contexte3" value="multiple_2_4"/>
			 Logement<INPUT type=checkbox name="type_contexte4" value="multiple_2_3"/>
			</p>
			<INPUT type=hidden name="login_uti" value="<?php echo $login; ?>"/>
			<INPUT type=hidden name="mdp_uti" value="<?php echo $mdp; ?>"/>
			<p align="center">
			<input type="submit" name="Submit" value="Envoyer"/>
			</p>
			</form>
			
		</div>
	</div><!--Fin div globalPass-->
</body>
</html>

