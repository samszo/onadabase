<?php
require_once ("Constantes.php");

// vérification du site en cours
if(isset($_GET['site'])){
	$site = $_GET['site'];
}else{
	$site = DEFSITE;
}

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
	$id = -1;

if(isset($_GET['login']))
	$login = $_GET['login'];
else
	$login = -1;
	

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
	
	
	

?>