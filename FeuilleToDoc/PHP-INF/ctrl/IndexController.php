<?php
session_start();
require('Zend/Spreadsheet-ClientLogin.php');
require('Zend/Docs-ClientLogin.php');
require_once ('Zend/Controller/Action.php');

class IndexController extends Zend_Controller_Action {
   
	public function indexAction() {
		
		if($_POST['login_uti']!="" && $_POST['mdp_uti']!="" ){
			$_SESSION["login"]=$_POST['login_uti'];
    	    $_SESSION["mdp"]=$_POST['mdp_uti'];
    	    $spreadsheet= new SimpleCRUD($_POST['login_uti'],$_POST['mdp_uti']);
    	 	$this->view->data=$spreadsheet->promptForSpreadsheet();
    	}
    }
    public function accueilAction(){
    	$spreadsheet= new SimpleCRUD($_SESSION["login"],$_SESSION["mdp"]);
    	$this->_helper->viewRenderer->setNoRender();
    	return $spreadsheet->promptForWorksheet($_GET['key']);
    }
	public function creatrepportAction(){
		$html=utf8_decode($_POST['html']);
		$html=str_replace("\'","'",$html); 
		$html=str_replace('\"','"',$html); 
		$html=str_replace('?','&oelig;',$html);
		$html.="<html>";
		$html.=$this->ImportWordStyle();
		$file=utf8_decode($_POST['file']);
		$this->_helper->viewRenderer->setNoRender();
		$file=str_replace(" ","_",$file); 
		if(file_exists(RAPPORT_PATH.$file)){
				unlink(RAPPORT_PATH.$file);
		}
    	$fichier = fopen(RAPPORT_PATH.$file,"w");
	    fwrite($fichier,$html);
	    fclose($fichier);
	    print "{PATH:'".RAPPORT_PATH.$file."',File:'".$file."'}";
	    $this->SendEmailToGoogleDoc($html,$file);
	}
	
	public function SendEmailToGoogleDoc($html,$file){
		
		$to = "feuilletodoc+onada-00986750759861606136-B92cp5s2@prod.writely.com";
		$subject = $file;
	    $headers  = 'MIME-Version: 1.0' . "\r\n";
    	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    	 /* et hop, à la poste */
		 if (mail($to, $subject, $html, $headers)){
				 echo 'Votre message a été envoyé avec succès!';
		}else { 
			echo 'problème lors de l\'envoi !';
		};
		exit;
		 
	}
    public function uploadtogoogledocAction(){
    	
    	$docToUpload= new GoogleDocs($_SESSION["login"],$_SESSION["mdp"]);
    	$this->_helper->viewRenderer->setNoRender();
    	$docToUpload->UploadFile(RAPPORT_URL.utf8_decode($_GET['file']),utf8_decode($_GET['file']));
    	    	
    }
	public function ImportWordStyle(){
		if (!$fp = fopen("../param/WordStyle.txt","r")) {
			echo "Echec de l'ouverture du fichier";
			exit;
		}else{
			$head="<head>";
			$head.="<style>";
			while(!feof($fp)) {
			// On récupère une ligne
			$Ligne = fgets($fp,255);
			// On stocke l'ensemble des lignes dans une variable
			$head.= $Ligne;
			}
			fclose($fp); // On ferme le fichier
		}
		$head.="/<style>";
		$head.="</head>";
		return $head;
	}
}

?>