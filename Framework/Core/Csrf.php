<?php

namespace Framework\Core;

use \Framework\Config\Config;

class Csrf
{
    private static $httpContext;

    public static function generate()
    {
        self::$httpContext = new \Framework\Core\HttpContext();
        self::$httpContext->session()->{Config::ACSRF_FIELD_NAME} = md5(uniqid(rand(), TRUE));
    }

    public static function getToken()
    {
        return self::$httpContext->session()->{Config::ACSRF_FIELD_NAME};
    }

    public static function validateToken()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            self::$httpContext = new \Framework\Core\HttpContext();
            $token = self::$httpContext->request()->form()->{Config::ACSRF_FIELD_NAME};
            if(isset($token)){
                if($token === self::getToken()){
                    return true;
                }
                return false;
            }
            return false;
        }
    }
}