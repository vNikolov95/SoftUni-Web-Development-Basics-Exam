<?php
namespace Framework\Controllers;

abstract class BaseController
{
    protected $db;
    protected $httpContext;

    public function __construct()
    {
        $this->db = \Framework\Core\Database::getInstance(\Framework\Config\DatabaseConfig::DB_INSTANCE);
        $this->httpContext = new \Framework\Core\HttpContext();
    }

    protected function redirect($uri)
    {
        header("Location: $uri");
    }
}