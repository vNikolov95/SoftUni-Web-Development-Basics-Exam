<?php 

namespace Framework\Core\Annotations\CustomAnnotations;

class Route extends \Framework\Core\Annotations\Annotation
{
	public function execute(){
		$routeArray = $this->annotationValue;

		$route = $routeArray[0];
		$method = 'get';

		if(isset($routeArray[1])){
			$method = $routeArray[1];
		}

		$action = $this->annotatatedFunction;
		$controller = array_pop(explode("\\",$this->annotatedClass));

		$controllerAndMethod = $controller."@".$action;

		if($GLOBALS['router']->isActionRegistered($controller, $action)){
			throw new \Exception("Controller and method $controllerAndMethod are already registered");
		}

		try{
			$found = $GLOBALS['router']->findRoute($method, $route);
		}
		catch(\Exception $e){
			$GLOBALS['router']->addRoute($method, $route, $controllerAndMethod);
		}
	}
}