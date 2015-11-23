<?php 

namespace Framework\Core\Drivers;

//use Hyper\Core\Drivers\MySqlDriver;

//require_once 'MySqlDriver.php';

class DriverFactory
{
	public static function load($driver, $user, $pass, $dbName, $host = null){
		if(strtolower($driver) === 'mysql' && !is_null($host)){
			$mysql = new MySqlDriver($user, $pass, $dbName, $host);

			return $mysql;
		}else{
			throw new \Exception('non-existing driver');
		}
	}
}