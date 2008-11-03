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
	
}

?>