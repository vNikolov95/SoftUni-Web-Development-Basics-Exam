<?php

namespace Framework\Core;

class View
{
    const PARAMS_COUNT_MODEL_AND_VIEW = 2;
    const PARAMS_COUNT_MODEL_ONLY = 1;
    const VIEW_FOLDER = \Framework\Config\Config::VIEW_FOLDER;
    const VIEW_EXTENSION = \Framework\Config\Config::VIEW_EXTENSION;

    public static $controllerName;
    public static $actionName;

    public function __construct(){
        $params = func_get_args();
        if (count($params) == self::PARAMS_COUNT_MODEL_AND_VIEW){
            $view = $params[0];
            $model = $params[1];
            $this->initModelView($view, $model);
        } else {
            $model = isset($params[0]) ? $params[0] : null;
            $this->initModelOnly($model);
        }
    }

    private function initModelOnly($model){
        require self::VIEW_FOLDER
            . DIRECTORY_SEPARATOR
            . self::$controllerName
            . DIRECTORY_SEPARATOR
            . self::$actionName
            . self::VIEW_EXTENSION;
    }

    private function initModelView($view, $model){
        require self::VIEW_FOLDER
            . DIRECTORY_SEPARATOR
            . $view
            . self::VIEW_EXTENSION;
    }

    public function url(){
    }
}