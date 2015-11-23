<?php

namespace Framework\Config;

class Config
{
	const BASE_CONTROLLER_NAMESPACE = 'Framework\\Controllers\\';
	const BASE_AREA__NAMESPACE = 'Framework\\Areas\\';
	const CONTROLLER_SUFFIX = 'Controller';
	const CONTROLLERS_FOLDER = 'Controllers';
	const VIEW_FOLDER = 'Views';
	const VIEW_EXTENSION = '.php';
	const ACSRF_FIELD_NAME = 'acsrf-token';
	const MODEL_NAMESPACE = '\\Framework\\Models\\';
	const USER_CLASS = "IdentityUser";
	const ROLE_CLASS = "UserRole";
	
	//declare in order of importance
	const USER_ROLES = array("Admin","User");

	//list all entity models that you want to use in the application
	const APPLICATION_MODELS = array(Config::MODEL_NAMESPACE.CONFIG::ROLE_CLASS, 
		Config::MODEL_NAMESPACE.Config::USER_CLASS);
}