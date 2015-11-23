<?php

namespace Framework\Models;

class IdentityUser extends \Framework\Core\MappedObject{

	/**
	* @int primary auto_increment
	*/
	public $id;

	/**
	* @varchar(250) not null
	*/
	public $username;

	/**
	* @varchar(250) not null
	*/
	public $password;

	/**
	* @int foreign 
	*/
	public $userrole_id;
}