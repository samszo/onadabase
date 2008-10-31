<?php
// declartion des constantes
//define("WEBAPP_DIR","http://localhost/onadabase/FeuilleToDoc");
define("WEBAPP_DIR","http://www.mundilogiweb.com/onadabase/FeuilleToDoc");

define("MODEL_DIR",WEBAPP_DIR."PHP-INF");

//define("ROOT_URL","http://localhost/onadabase/FeuilleToDoc");
define("ROOT_URL",WEBAPP_DIR);

define("BASE_URL","/onadabase/FeuilleToDoc/");

//define("ZEND_FRAMEWORK_DIR","c:/wamp/www/onadabase/Zend/library");
define("ZEND_FRAMEWORK_DIR",$_SERVER["DOCUMENT_ROOT"]."/onadabase/Zend/library");




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
 
// Logger init
Zend_Loader::loadClass('Zend_Log');
Zend_Loader::loadClass('Zend_Log_Writer_Stream');
$logger = new Zend_Log();
$logger->addWriter(new Zend_Log_Writer_Stream(LOG_FILE));
Zend_Registry::set("logger",$logger);
Zend_Registry::get("logger")
    ->debug("** URI=".$_SERVER["REQUEST_URI"]);
 
// Controller init
Zend_Loader::loadClass('Zend_Controller_Front');
Zend_Loader::loadClass('Zend_Controller_Router_Rewrite');
$controller = Zend_Controller_Front::getInstance();
 
$router = new Zend_Controller_Router_Rewrite();
 
$cmtRoute = new Zend_Controller_Router_Route(
    "spreadsheet/:action/:spreadsheet",
    array(  "spreadsheet"=>null,
            "controller"=>"spreadsheet",
            "action"=>"getspreadsheet"
    )
);
$router->addRoute("spreadsheet",$cmtRoute);
$controller->setBaseUrl(BASE_URL);
 
$controller->setRouter($router);
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