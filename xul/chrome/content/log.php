<?php
session_start();


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>ONADABASE</title>
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
	width:200px;
	margin: 5px;
	margin-top:60px;
    }
</style>
</head>

<body bgcolor="#ffffff">
	<div id='globalPass'>
		<div class='BlocTextePass'>	
			<p align="center">Vous allez entrer dans une zone sécurisée.</p>		
			<form name="formulaire" method="post" action="diagnostic.php">
			<p align="center">Login :<br /> 
			<input name="login_uti" type="text" id="login_uti">
			</p>
			<p align="center">Mot de passe : 
			<input name="mdp_uti" type="password" id="mdp_uti">
			</p>
			<p align="center">
			<input type="submit" name="Submit" value="Envoyer">
			</p>
			</form>
		</div>
	</div><!--Fin div globalPass-->
</body>
</html>
