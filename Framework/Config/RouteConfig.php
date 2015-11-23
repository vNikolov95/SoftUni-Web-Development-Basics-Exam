<?php

namespace Framework\Config;

\Framework\Core\Annotations\AnnotationLoader::scanAnnotatedRoutes();

//as well as annotations, custom routes can be added here
//using the pattern $GLOBALS['router']->addRoute("{method}", "{route}", "{Controller@Action}");

$GLOBALS['router']->addRoute("get", "/register", "UserController@getRegister");
$GLOBALS['router']->addRoute("post", "/register", "UserController@postRegister");
