<?php 
session_start();
extract($_SESSION,EXTR_OVERWRITE);
 
$_SESSION = array();
if (isset($_COOKIE[session_name()])) {    setcookie(session_name(), '', time()-42000, '/');}
session_destroy ();



$html.='<html xmlns="http://www.w3.org/1999/xhtml">';
$html.='<head>';
$html.='<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';
$html.='<title>Document sans nom</title>';
$html.='</head>';
$html.='<body>';

$html.='<SCRIPT LANGUAGE="JavaScript">
 if (window !=top ) {top.location=window.location;}
</SCRIPT>';

$html.='<script language="Javascript">
location.href = "index.php"
</script>';

$html.='</body>';
$html.='</html>';

echo $html;
?>