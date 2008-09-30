<?php
session_start();
require_once ("../../../param/ParamPage.php");

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
	width:420px;
	height:616px;
	margin-top: -308px; /* moitié de la hauteur */
	margin-left: -210px; /* moitié de la largeur */
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
	width:410px;
	margin: 10px;
	margin-top:140px;
    }
</style>
</head>

<body bgcolor="#ffffff">
	<div id='globalPass'>
		<div class='BlocTextePass'>	
			<h3 align="center">Vous allez entrer dans une zone sécurisée</h3>		
			<form name="formulaire" method="post" action="index.php">
			<p align="center">Login : 
			<input name="login_uti" type="text" id="login_uti" />
			</p>
			<p align="center">Mot de passe : 
			<input name="mdp_uti" type="password" id="mdp_uti" />
			</p>
			<h3 align="center">Veuillez choisir la version de votre diagnostic</h3>		
			<p align="center">Version : <SELECT name="version">
				<OPTION VALUE="V1" [SELECTED] >V1</OPTION>
				<OPTION VALUE="V2">V2</OPTION>
			</SELECT>
			</p>
			<h3 align="center">Veuillez décocher les caractéristiques inutiles</h3>		
			<H4 align="center">Type de critère : </h4>
			<p align="center">
			Souhaitable<input align="center" name="type_controle1" checked="true" type="checkbox" value="multiple_1_2"/>
			Réglementaire<INPUT align="center" type=checkbox checked="true" name="type_controle2" value="multiple_1_1"/> 
			</p>
			<h4 align="center">Contexte réglementaire : </h4>
			<p align="center">
			Travail<input name="type_contexte1" checked="true" type="checkbox" value="multiple_2_1"/>
			ERP/IOP<INPUT type=checkbox name="type_contexte2" checked="true" value="multiple_2_2"/> 
			Voirie<INPUT type=checkbox name="type_contexte3" checked="true" value="multiple_2_4"/>
			Logement<INPUT type=checkbox name="type_contexte4" checked="true" value="multiple_2_3"/>
			</p>
			<p align="center">
			<input type="submit" name="Submit" value="Envoyer"/>
			</p>
			</form>
			
		</div>
	</div><!--Fin div globalPass-->
</body>
</html>

