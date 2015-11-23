<?php 

namespace Framework\ViewModels;

abstract class BaseViewModel
{
	public $error = false;
	public $errorsList = array();
    public $success = false;
    public $successList = array();

    public function __call($name, $arguments)
    {
        if(strtolower($name === 'type'))
        {
            if($arguments[0] !== get_class($this))
            {
                throw new \Exception("View model must be of type $arguments[0]");
            }
        }
    }

    public function ListErrors()
    {
    	$errorBox = '<div class="errors-box alert alert-danger" role="alert">';
    	$uniqueArray = array_unique($this->errorsList);
        $span = "<span class='glyphicon glyphicon-exclamation-sign'></span>";
    	
    	foreach($uniqueArray as $error){
    		$errorBox = $errorBox.'<div class="error">'.$span.$error.'</div>';
    	}

    	$errorBox = $errorBox."</div>";

    	echo $errorBox;
    }

    public function ListSuccessMessages()
    {
        $successBox = '<div class="errors-box alert alert-success alert-dismissable" role="alert">';
        $uniqueArray = array_unique($this->successList);
        
        $span = "<button type='button' class='close' data-dismiss='alert' aria-label='Close'>"
        ."<span aria-hidden='true'>&times;</span></button>";
        
        foreach($uniqueArray as $error){
            $successBox = $successBox.'<div class="error">'.$span.$error.'</div>';
        }

        $successrBox = $successBox."</div>";

        echo $successBox;
    }
}