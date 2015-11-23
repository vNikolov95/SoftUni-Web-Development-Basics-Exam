<?php

session_start();

require_once ('AppStart/Autoloader.php');

$autoloader = new \Framework\AppStart\Autoloader();
$autoloader->addNamespace('\Framework\\', __DIR__ . DIRECTORY_SEPARATOR);
$isLoaded = $autoloader->register(true);

$router = new \Framework\Core\Router();

require_once('Config/RouteConfig.php');

$uri = $_SERVER['REQUEST_URI'];
$self = $_SERVER['PHP_SELF'];
$method = $_SERVER['REQUEST_METHOD'];

$index = basename($self);
$directories = str_replace($index, '', $self);
$requestString = preg_replace("~".$directories."~", '', $uri,1);
$requestParams = explode("/", $requestString);
$controller = array_shift($requestParams);
$action = array_shift($requestParams);
$isArea = false;
$areaName = "";


//parse the entered route
 try{
	$foundRoute = $router->findRoute(strtolower($method), $requestString);

	$areaParams = array();
	$exploded = explode("\\", $foundRoute['action']);
	$areaParams = $exploded;
	$controllerAndMethod = array_pop($exploded);
	$exploded = explode("@", $controllerAndMethod);

	$action = $exploded[1];
	$controllerSplit = explode("Controller", $exploded[0]);
	$controller = $controllerSplit[0];
	$requestParams = $foundRoute['params'];
	
	if (strpos($foundRoute['action'],'Framework\\Areas\\') !== false) {
    	$isArea = true;
    	$areaName = $areaParams[3];
	}

 } 
 catch(\Framework\Core\Exceptions\RouteNotFoundException $e){

   if($router->isActionRegistered($controller,$action)){
      header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
      include("ErrorPages/404.php");
      die;
   }
 }
 
  if($controller == '' || $controller == false){
		$controller = 'home';
  }


 if($action == '' || $action == false){
		$action = 'index';
} 

\Framework\Core\Database::setInstance(
    \Framework\Config\DatabaseConfig::DB_INSTANCE,
    \Framework\Config\DatabaseConfig::DB_DRIVER,
    \Framework\Config\DatabaseConfig::DB_USER,
    \Framework\Config\DatabaseConfig::DB_PASS,
    \Framework\Config\DatabaseConfig::DB_NAME,
    \Framework\Config\DatabaseConfig::DB_HOST
);

//create tables if they dont exist

$dbMapper = new \Framework\Core\MappedObject();

foreach(\Framework\Config\Config::APPLICATION_MODELS as $model){
	$tableName = array_pop(explode("\\", $model));

  if(!$dbMapper->TableExists(strtolower($tableName))){
  	$dbMapper->MapClassToDbTable($model, $tableName);

  	if($tableName == \Framework\Config\Config::ROLE_CLASS){
  		foreach(\Framework\Config\Config::USER_ROLES as $role)
  		{
  			$strClass = \Framework\Config\Config::MODEL_NAMESPACE.\Framework\Config\Config::ROLE_CLASS;
  			$roleClass = new $strClass;
  			$res = $roleClass->Create(\Framework\Config\Config::ROLE_CLASS, 
  				array("role"=>$role));

  			$res->save();
  		}
  	}
  }
}

//testing identity system and http context
 // $user = new \Framework\Models\IdentityUser();
 // $user->username = "dubcloc";
 // $user->password = "loc123";
 // $user->userrole_id = "1";

 // $res = \Framework\Core\Identity::register($user);
  //$res = \Framework\Core\Identity::login('dubcloc', 'loc123');
 // $res = \Framework\Core\Identity::changeUserRole('dubcloc','Usero');
 // $res = \Framework\Core\Identity::isUserInRole('dubcloc','Userro');

//$context = new \Framework\Core\HttpContext();
//var_dump($context->request()->params()->userId);
//$context->cookie()->language = "english";
//$context->cookie()->delete("language");
//var_dump($context->identity()->username);
//exit;

try{
  $app = new \Framework\AppStart\Application(ucfirst(strtolower($controller)), $action, $requestParams, $isArea, $areaName);
  $app->start();
}
catch(\Exception $e){
  
  if($e->getCode() === 404){
      header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
      include("ErrorPages/404.php");
      die;
  }
}