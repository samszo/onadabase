<?php
session_start();
require('Zend/Spreadsheet-ClientLogin.php');
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
		//$file=utf8_decode($_POST['file']);
		$file=$_POST['file'];
		$this->_helper->viewRenderer->setNoRender();
		$file=str_replace(" ","_",$file); 
		if(file_exists(PATH.$file)){
				unlink(PATH.$file);
		}
    	$fichier = fopen(PATH.$file,"w");
	    fwrite($fichier,$html);
	    fclose($fichier);
	    print "{PATH:'".PATH.$file."',File:'".$file."'}";
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
	
}

?>