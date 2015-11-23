<?php

namespace Framework\Models;

class UserRole extends \Framework\Core\MappedObject{
	
	/**
	* @int primary auto_increment
	*/
	public $id;

	/**
	* @varchar(250) not null
	*/
	public $role;
}