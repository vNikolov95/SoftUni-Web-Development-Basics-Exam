<?php 

namespace Framework\Core\Drivers;

//use Framework\Core\Drivers\DriverAbstract;

//require_once 'DriverAbstract.php';

class MySqlDriver extends DriverAbstract
{
	public function getDsn(){
		$dsn = "mysql:host=" . $this->host . ";dbname=". $this->dbName;

		return $dsn;
	}
}