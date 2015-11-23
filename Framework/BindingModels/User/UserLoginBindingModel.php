<?php 

namespace Framework\BindingModels\User;

class UserLoginBindingModel extends \Framework\Core\BindingModel
{
	/**
     * @Required true
     */
	public $username;

	/**
     * @Required true
     */
	public $password;
}