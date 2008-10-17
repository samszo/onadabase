<?php
define("PATH","../rapports/");
if(isset($_GET['f']))
		$fonction = $_GET['f'];
	else
		$fonction = '';
switch ($fonction) {
	case'CreatRepport':
			$resultat =CreatRepport($_GET['html'],$_GET['file']);
}
echo $resultat;
function CreatRepport($html,$file){
		if(file_exists(PATH.$file)){
				unlink(PATH.$file);
		}
    	$fichier = fopen(PATH.$file,"w");
	    fwrite($fichier,$html);
	    fclose($fichier);
	    return PATH.$file."*".$file;
}
?>