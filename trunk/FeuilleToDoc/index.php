<?php
// declartion des constantes
//define("WEBAPP_DIR","http://localhost/onadabase/FeuilleToDoc");
define("WEBAPP_DIR","http://www.mundilogiweb.com/onadabase/FeuilleToDoc/");
define("MODEL_DIR",WEBAPP_DIR."PHP-INF");
define("ROOT_URL",WEBAPP_DIR);
define("BASE_URL","/onadabase/FeuilleToDoc/");
//define("IMG_URL","http://localhost/onadabase/rapports/");
define("IMG_URL","http://www.mundilogiweb.com/onadabase/rapports/");
//define("ZEND_FRAMEWORK_DIR","c:/wamp/www/onadabase/Zend/library");
define("ZEND_FRAMEWORK_DIR",$_SERVER["DOCUMENT_ROOT"]."/onadabase/Zend/library");
define("PATH","../rapports/");

 
set_include_path(
  ".".PATH_SEPARATOR.
  MODEL_DIR.PATH_SEPARATOR.
  ZEND_FRAMEWORK_DIR.PATH_SEPARATOR.
  get_include_path()
);
 
require_once 'Zend/Loader.php';

Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');
Zend_Loader::loadClass('Zend_Gdata_App_AuthException');
Zend_Loader::loadClass('Zend_Http_Client');

// Registry init
Zend_Loader::loadClass("Zend_Registry");
 
// Controller init
Zend_Loader::loadClass('Zend_Controller_Front');
Zend_Loader::loadClass('Zend_Controller_Router_Rewrite');
$controller = Zend_Controller_Front::getInstance();
 
$controller->setBaseUrl(BASE_URL);
$controller->setControllerDirectory('PHP-INF/ctrl');
$controller->throwExceptions(true);
 
// init viewRenderer
Zend_Loader::loadClass("Zend_View");
$view = new Zend_View();
$viewRenderer = Zend_Controller_Action_HelperBroker::
    getStaticHelper('viewRenderer');
$viewRenderer->setView($view)
             ->setViewSuffix('phtml');
 
// call dispatcher
$controller->dispatch();
?>