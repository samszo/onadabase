<?php

require('Zend/Spreadsheet-ClientLogin.php');
require_once ('Zend/Controller/Action.php');

class IndexController extends Zend_Controller_Action {
    
	public function indexAction() {
    	 $spreadsheet= new SimpleCRUD('amelbourn@yahoo.fr','bmfamkkr');
    	 $this->view->data=$spreadsheet->promptForWorksheet();
    }
	
}

?>