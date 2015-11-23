<?php
namespace Framework\Controllers;

use Framework\Core\View as View;
use Framework\BindingModels\User\UserRegisterBindingModel;
use Framework\BindingModels\User\UserLoginBindingModel;

class UserController extends BaseController
{
	/**
	* @Route ["/login"]
	*/
	public function getLogin(){
		return new View(new \Framework\ViewModels\User\GetLoginViewModel());
	}

	/**
	* @Route ["/login", "post"]
	*/
	public function postLogin(UserLoginBindingModel $userModel){
		try{
			 if(!$userModel->isValid()){

			 	$viewModel = new \Framework\ViewModels\User\GetLoginViewModel();
			 	$viewModel->errorsList = $userModel->getErrorsList();
			 	$viewModel->error = true;
				
				return new View('\\User\\getLogin', $viewModel);
			}

			$result = \Framework\Core\Identity::login($userModel->username, $userModel->password);

			$viewModel = new \Framework\ViewModels\User\GetLoginViewModel();
			$viewModel->success = true;
			$viewModel->successList[] = "You have successfully logged in";

			return new View('\\User\\getLogin', $viewModel);
		}
		catch(\Exception $e){

			$viewModel = new \Framework\ViewModels\User\GetLoginViewModel();
			$viewModel->errorsList = $userModel->getErrorsList();
			$viewModel->errorsList[] = $e->getMessage();
			$viewModel->error = true;

			return new View('\\User\\getLogin', $viewModel);
		}
	}

	public function getRegister(){
		return new View(new \Framework\ViewModels\User\GetRegisterViewModel());
	}

	public function postRegister(UserRegisterBindingModel $userModel){
		try{
			 if(!$userModel->isValid()){

			 	$viewModel = new \Framework\ViewModels\User\GetRegisterViewModel();
			 	$viewModel->errorsList = $userModel->getErrorsList();
			 	$viewModel->error = true;
				
				return new View('\\User\\getRegister', $viewModel);
			}

			$userClass = \Framework\Config\Config::MODEL_NAMESPACE.\Framework\Config\Config::USER_CLASS;
			$userIdentityModel = new $userClass();

			foreach($userModel as $prop=>$value){
				if(property_exists($userClass, $prop)){
					$userIdentityModel->$prop = $userModel->$prop;
				}
			}

			\Framework\Core\Identity::register($userIdentityModel);

			$viewModel = new \Framework\ViewModels\User\GetRegisterViewModel();
			$viewModel->success = true;
			$viewModel->successList[] = "You have successfully registered";

			return new View('\\User\\getRegister', $viewModel);
		}
		catch(\Exception $e){

			$viewModel = new \Framework\ViewModels\User\GetRegisterViewModel();
			$viewModel->errorsList = $userModel->getErrorsList();
			$viewModel->errorsList[] = $e->getMessage();
			$viewModel->error = true;

			return new View('\\User\\getRegister', $viewModel);
		}
	}

	/**
	* @Route ["/profile", "get"]
	*/
	public function getProfile(){
		$viewModel = new \Framework\ViewModels\User\GetProfileViewModel();
		$viewModel->username = $this->httpContext->identity()->username;

		return new View($viewModel);
	}

	/**
	* @Route ["/logout", "get"]
	*/
	public function logout(){
		\Framework\Core\Identity::logout();

		$this->redirect("login");
	}
}