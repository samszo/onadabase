<?php
	$ajax = true;
	require_once ("../../../param/Constantes.php");
	require_once (PathRoot."/param/ParamPage.php");
	//charge le fichier de param�trage
	$objSite->XmlParam = new XmlParam(PathRoot."/param/SolAcc.xml");

	$resultat = "";
	if(isset($_GET['f']))
		$fonction = $_GET['f'];
	else
		$fonction = '';
		
	switch ($fonction) {
		case 'SetVal':
			$resultat = SetVal($_GET['idGrille'],$_GET['idDon'],$_GET['champ'],$_GET['val']);
			break;
	}
	echo "fonction:".$fonction;
	echo $resultat;
/*
	$xml = $_GET['reponse'];
		echo ' URL '.$xml;
		echo ' CONTENU '.$xml->saveXML();
		$dom = new DOMDocument();
		$dom->loadXML($_GET['reponse']);
		echo $dom->saveXML(); 
*/		
		
		
	?> 
