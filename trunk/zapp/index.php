<?php
//http://www.kitpages.fr/zf_quickIntroduction.html
// mettre vos constantes ici...
define("WEBAPP_DIR","C:/wamp/www/onadabase/zapp");
define("MODEL_DIR",WEBAPP_DIR."/PHP-INF");
define("ROOT_URL","http://localhost/onadabase/zapp");
define("BASE_URL","/zapp/");
define("ZEND_FRAMEWORK_DIR","C:/wamp/www/onadabase/library/php/ZendGdata/library");
 
set_include_path(
  ".".PATH_SEPARATOR.
  MODEL_DIR.PATH_SEPARATOR.
  ZEND_FRAMEWORK_DIR.PATH_SEPARATOR.
  get_include_path()
);
 
require_once 'Zend/Loader.php';
 
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