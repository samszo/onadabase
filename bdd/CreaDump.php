<?php
// Database configuration
require_once ("../param/Constantes.php");
if(isset($_GET['site'])){
	$idSite = $_GET['site'];
}else{
	$idSite = DEFSITE;
}
$idSite = 'valdemarne';
$db_server   = $SITES[$idSite]["SQL_HOST"];
$db_name     = $SITES[$idSite]["SQL_DB"];
$db_username = $SITES[$idSite]["SQL_LOGIN"];
$db_password = $SITES[$idSite]["SQL_PWD"];


echo "Votre base est en cours de sauvegarde.......

";
system("mysqldump --host=$db_server --user=$db_name --password=$db_password $db_username > $db_name.sql");
echo "C'est fini. Vous pouvez récupérer la base par FTP";

?>