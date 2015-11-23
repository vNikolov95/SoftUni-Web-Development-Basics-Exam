<?php

namespace Framework\AppStart;

use Framework\Core\View;

class Application
{
    private $controllersNamespace = \Framework\Config\Config::BASE_CONTROLLER_NAMESPACE;
    private $controllersSuffix = \Framework\Config\Config::CONTROLLER_SUFFIX;

    private $controllerName;
    private $actionName;
    private $requestParams = array();
    private $controller;

    public function __construct($controllerName, $actionName, $requestParams = array(), $isArea = false, $areaName = ""){

        if($isArea){
            $this->controllersNamespace = 'Framework\\Areas\\'. ucfirst($areaName) . '\\Controllers\\';
        }

        $this->controllerName = $controllerName;
        $this->actionName = $actionName;
        $this->requestParams = $requestParams;
    }

    public function start(){
        $this->initController();

        View::$controllerName = $this->controllerName;
        View::$actionName = $this->actionName;

        try{
            $method = new \ReflectionMethod($this->controller, $this->actionName);
            foreach ($method->getParameters() as $param) {

                $param = $param->getType()->__toString();

                if(class_exists($param)) {
                    $this->requestParams[] = new $param();
                }
            }
        
            call_user_func_array(
                array(
                    $this->controller,
                    $this->actionName
                ),
                $this->requestParams
            );
        }
        catch(\Exception $e){
            throw new \Exception($e->getMessage(), 404);
        }
    }

    private function initController(){
        try{
            $controllerName = $this->controllersNamespace
            . $this->controllerName
            . $this->controllersSuffix;
            $this->controller = new $controllerName();
        }
        catch(\Exception $e){
            throw new \Exception($e->getMessage(), 404);
        }
    }

    private function getClassName(\ReflectionParameter $param) {
        preg_match('/\[\s\<\w+?>\s([\w]+)/s', $param->__toString(), $matches);
        return isset($matches[1]) ? trim($matches[1]) : null;
    }
}