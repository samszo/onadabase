<?php
define("PATH","../rapports/");
if(isset($_POST['f']))
		$fonction = $_POST['f'];
	else
		$fonction = '';
switch ($fonction) {
	case'CreatRepport':
			$resultat =CreatRepport($_POST['html'],$_POST['file']);
			break;
	case'VerifExistRepport':
		$resultat = VerifExistRepport($_POST['file'],$_POST['i']);
		break;
}
echo $resultat;
function CreatRepport($html,$file){
	    $file=str_replace(" ","_",$file); 
		if(file_exists(PATH.$file)){
				unlink(PATH.$file);
		}
    	$fichier = fopen(PATH.$file,"w");
	    fwrite($fichier,$html);
	    fclose($fichier);
	    return "{PATH:'".PATH.$file."',File:'".$file."'}";
}
function VerifExistRepport($file,$i){
	$file=str_replace(" ","_",$file);
	if(file_exists(PATH.$file)){
				return "{PATH:'".PATH.$file."',File:".$file.",Ligne:".$i.",Existe:true}";
	}else
		 return "{Existe:false}";
}
?>