<?php
namespace Framework\Core;

use \Framework\Config\Config;

class Identity
{
	private static $dbMapper;

	public static function login($username, $password){
		self::$dbMapper = new \Framework\Core\MappedObject();

		try{
			$result = self::$dbMapper->FindFirst(Config::USER_CLASS, 
				"username = '$username'");

			if($result==false){
				throw new \Exception("Username $username does not exist");
			}

			if(!password_verify($password,$result->password)){
				throw new \Exception("Invalid username or password");
			}

			$_SESSION['userId'] = $result->id;

			return true;
		}
		catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}

	public static function register(\Framework\Models\IdentityUser $userModel){
		self::$dbMapper = new \Framework\Core\MappedObject();

		//self::$dbMapper = 

		try{
			$isFree = self::$dbMapper->FindFirst(Config::USER_CLASS, 
				"username = '$userModel->username'");

			if($isFree !==false){
				throw new \Exception("Username $userModel->username is already taken");
			}

			if(!isset($userModel->userrole_id)){
				$rolesArr = Config::USER_ROLES;
				$roleText = array_pop($rolesArr);

				$resultRole = self::$dbMapper->FindFirst(Config::ROLE_CLASS, 
				"role = '$roleText'");

				$userModel->userrole_id = $resultRole->id;
			}

			$props = array();

			foreach ($userModel as $property => $value)
			{
				if(strtolower($property) == 'id'){
					continue;
				}

				if(strtolower($property) == 'password'){
					$props[$property] = password_hash($value, PASSWORD_DEFAULT);
				}
				else{
    				$props[$property] = $value;
				}
			}

			$result = self::$dbMapper->Create(Config::USER_CLASS, $props);

			$result->save();

			$_SESSION['userId'] = $result->id;
		}
		catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}

	public static function changeUserRole($username, $roleText){
		self::$dbMapper = new \Framework\Core\MappedObject();

		try{
			$user = self::$dbMapper->FindFirst(Config::USER_CLASS, 
				"username = '$username'");

			$resultRole = self::$dbMapper->FindFirst(Config::ROLE_CLASS, 
				"role = '$roleText'");

			if($user==false){
				throw new \Exception("Username $username does not exist");
			}

			if($resultRole==false){
				$resultRole = self::$dbMapper->Create(Config::ROLE_CLASS, array("role"=>$roleText));
				$resultRole->save();
			}

			$user->userrole_id = $resultRole->id;

			$final = $user->save();

			if($user->userrole_id == $resultRole->id){
				return true;
			}

			return false;
		}
		catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}


	public static function logout(){
		if(self::isUserLogged()){
			unset($_SESSION['userId']);

			return true;
		}

		throw new \Exception("There is currently no logged in user");
	}

	public static function isUserInRole($username, $roleText){
		self::$dbMapper = new \Framework\Core\MappedObject();

		try{
			$user = self::$dbMapper->FindFirst(Config::USER_CLASS, 
				"username = '$username'");

			$resultRole = self::$dbMapper->FindFirst(Config::ROLE_CLASS, 
				"role = '$roleText'");

			if($user==false){
				throw new \Exception("Username $username does not exist");
			}

			if($resultRole==false){
				throw new \Exception("Role $roleText does not exist");
			}

			if($user->userrole_id == $resultRole->id){
				return true;
			}

			return false;
		}
		catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}

	public static function getRoleId($roleText){
		self::$dbMapper = new \Framework\Core\MappedObject();

		try{

			$resultRole = self::$dbMapper->FindFirst(Config::ROLE_CLASS, 
				"role = '$roleText'");

			if($resultRole==false){
				throw new \Exception("Role $roleText does not exist");
			}

			return $resultRole->id;
		}
		catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}

	public static function getUserInformation($userId){
		self::$dbMapper = new \Framework\Core\MappedObject();
		
		$user = self::$dbMapper->FindFirst(Config::USER_CLASS, 
				"id = $userId");

		return array_filter(get_object_vars($user), function($k){
			return $k != 'password' && !is_numeric($k);
		}, ARRAY_FILTER_USE_KEY);
	}

	public static function isUserLogged(){
		$isLogged = isset($_SESSION['userId']) ? true: false;

		return $isLogged;
	}
}