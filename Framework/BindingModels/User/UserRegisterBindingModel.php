<?php 

namespace Framework\BindingModels\User;

class UserRegisterBindingModel extends \Framework\Core\BindingModel
{
	/**
     * @Required true
     */
	public $username;

	/**
     * @Required true
     * @MinLength 6
     */
	public $password;

	/**
     * @Required true
     * @Matches "password"
     */
	public $confirmPassword;
}