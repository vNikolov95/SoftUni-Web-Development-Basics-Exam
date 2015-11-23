<?php

namespace Framework\Core;

class BindingModel
{
    private $errorsList = array();
    private $isValid = true;

    public function __construct()
    {
        $this->populateWithPost($this);
        $this->validate($this);

        return $this;
    }

    public function __call($method, $args)
    {
        $property = strtolower(preg_replace('/get/', '', $method));

        if(property_exists($this, $property)){
            return $this->{$property};
        }

        $this->addError("$property is not defined");
        $this->isValid = false;

        //throw new \Exception("Property $property is not defined", 400);
    }

    public function getErrorsList()
    {
        return $this->errorsList;
    }

    public function isValid(){
        return $this->isValid;
    }

    public function addError($error)
    {
        $this->errorsList[] = $error;
    }

    // public function getacsrftoken()
    //  {
    //     return $this->acsrf;
    //  }

    private function populateWithPost ($obj)
    {
        $post = $_POST;

        foreach ($post as $var => $value) {
            $var = strip_tags($var);
            $value = strip_tags($value);

            if($var == \Framework\Config\Config::ACSRF_FIELD_NAME){
                continue;
            }

            if(true) {
                if(is_array($value)){
                    $obj->{$var} = array_map('trim',$value);
                }else {
                    $obj->{$var} = trim($value);
                }
            }else{
                //throw new \Exception("Unexpected value for $var from input", 400);
                $this->addError("$var is invalid");
                $this->isValid = false;
            }

            if(!(\Framework\Core\Csrf::validateToken())){
                //throw new \Exception("Anti-Forgery token does not match", 400);
                $this->addError("Anti-Forgery token does not match");
                $this->isValid = false;
            }
        }
    }

    private function validate($obj)
    {
        try{
            \Framework\Core\Annotations\AnnotationLoader::init($obj, get_called_class(), true);
        }
        catch(\Exception $e){
            $this->addError($e->getMessage());
            $this->isValid = false;
        }
    }
}