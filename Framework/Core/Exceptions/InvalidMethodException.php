<?php
namespace Framework\Core\Exceptions;

class InvalidMethodException extends \Exception{
    public function __construct($message = "")
    {
        parent::__construct($message, 0, null);
    }
}