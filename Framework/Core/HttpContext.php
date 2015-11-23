<?php
namespace Framework\Core;

class HttpContext
{
	public function request(){
		return new Request();
	}

	public function session(){
		return new Session();
	}

	public function cookie(){
		return new Cookie();
	}

	public function identity(){
		return json_decode(json_encode(\Framework\Core\Identity::getUserInformation($_SESSION['userId'])), 
			FALSE);
	}
}

class Request
{
	private $params;
	private $form;

	public function __construct(){
		$this->params = json_decode(json_encode($_GET), FALSE); 
		$this->form =json_decode(json_encode($_POST), FALSE); 
	}

	public function params(){
		return $this->params;
	}

	public function form(){
		return $this->form;
	}
}

class Session
{
	public function __get($property) {
    	if(array_key_exists($property, $_SESSION) && $property!='userId'){
    		return $_SESSION[$property];
    	}
 	 }

 	public function __set($property, $value) {
    	if($property!='userId'){
    		$_SESSION[$property] = $value;
    	}
  	}

  	public function delete($prop){
  		unset($_SESSION[$prop]);
  	}
}

class Cookie
{
	public function __get($property) {
    	if(array_key_exists($property, $_COOKIE)){
    		return $_COOKIE[$property];
    	}
 	 }

 	public function __set($property, $value) {
    	$_COOKIE[$property] = $value;
  	}

  	public function delete($prop){
  		unset($_COOKIE[$prop]);
  	}
}