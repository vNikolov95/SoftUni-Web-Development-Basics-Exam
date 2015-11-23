<?php

namespace Framework\Core;

use Framework\Core\Drivers\DriverFactory;

class Database
{
	private static $inst = array();

    /**
     * @var \PDO
     */
    private $db = null;

    private function __construct(\PDO $dbInstance){
         $this->db = $dbInstance;
    }

    public static function setInstance(
        $instanceName,
        $driver,
        $user,
        $pass,
        $dbName,
        $host = null
    ){
        $driver = DriverFactory::load($driver, $user, $pass, $dbName, $host);
        $pdo = new \PDO(
            $driver->getDsn(),
            $user,
            $pass
        );
        self::$inst[$instanceName] = new self($pdo);
    }

    public static function getInstance($instanceName = \Framework\Config\DatabaseConfig::DB_INSTANCE){
        if (self::$inst[$instanceName] == null){
            throw new \Exception('Invalid instance');
        }

        return self::$inst[$instanceName];
    }

    /**
     * @param string $statement
     * @param array $driverOptions
     * @return Statement
     */
    public function prepare($statement, array $driverOptions = array()){
        $statement = $this->db->prepare($statement, $driverOptions);
		
		//var_dump($statement);exit;
        return new Statement($statement);
    }

    public function query($query){
        $this->db->query($query);
    }

    public function lastId($name = null){
        return $this->db->lastInsertId($name);
    }

    public function quote($val){
        return $this->db->quote($val);
    }
}

class Statement
{
    /**
     * @var \PDOStatement
     */
    private $stmt = null;

    public function __construct(\PDOStatement $stmt){
        $this->stmt = $stmt;
    }

    public function fetch($fetchStyle = \PDO::FETCH_ASSOC){
        return $this->stmt->fetch($fetchStyle);
    }

    public function fetchAll($fetchStyle = \PDO::FETCH_ASSOC){
        return $this->stmt->fetchAll($fetchStyle);
    }

    public function bindParam($parameter, $variable, $dataType = \PDO::PARAM_STR, $length = null, $driverOptions = null){
        return $this->stmt->bindParam($parameter, $variable, $dataType, $length, $driverOptions);
    }

    public function rowCount(){
        return $this->stmt->rowCount();
    }
    
    public function execute($params = array()){
        //var_dump($params);exit;
		$this->stmt->execute($params);
    }
	
	public function showErrors(){
		print_r($this->stmt->errorInfo());
	}
}