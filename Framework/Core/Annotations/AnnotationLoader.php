<?php 

namespace Framework\Core\Annotations;

class AnnotationLoader
{
	public static function init($instance, $annotatedClassName, $isProperty = false)
	{
		if($isProperty){
			self::initProperty($instance, $annotatedClassName);
        }
        else{
            self::initMethod($instance, $annotatedClassName);
        }
	}

    public static function scanAnnotatedRoutes()
    {
        foreach(scandir(\Framework\Config\Config::CONTROLLERS_FOLDER, 0) as $file)
        {

            preg_match("/.+(Controller\.php)/", $file, $matches);

            if(isset($matches[0]) && $matches[0] !== 'BaseController.php'){
                $controllerClass = $matches[0];
                $controllerClass = explode(".", $controllerClass);
                $controllerClass = $controllerClass[0];

                self::initMethod(\Framework\Config\Config::BASE_CONTROLLER_NAMESPACE.$controllerClass, true);
            }
        }
    }

	private static function initProperty($instance, $annotatedClassName){
		foreach($instance as $prop=>$value){
			$reader = 
                new \Framework\Core\Annotations\AnnotationReader($annotatedClassName, $prop, 'property');
            
            $all = $reader->getParameters();

            foreach($all as $anon=>$anonValue){
            	$className = AnnotationConfig::ANNOTATION_NAMESPACE.$anon;

            	if(class_exists($className)){
            		$annotation = new $className($instance, $annotatedClassName,$anonValue, $prop, $value);
            		$annotation->execute();
            	}
            	//let the app continue in case of the loader intercepting regular doc comments
            }
        }
	}

    private static function initMethod($annotatedClassName, $onlyRouteAnnotations = false){
        $methods = get_class_methods($annotatedClassName);

        foreach($methods as $method){
            $reader = 
                new \Framework\Core\Annotations\AnnotationReader($annotatedClassName, $method);
            
            $all = $reader->getParameters();

            if(!$onlyRouteAnnotations){

                $all = array_filter($all, function($k){
                   return !in_array($k, \Framework\Core\Annotations\AnnotationConfig::ROUTE_ANNOTATIONS);
                }, ARRAY_FILTER_USE_KEY);
            } else{

                $all = array_filter($all, function($k){
                   return in_array($k, \Framework\Core\Annotations\AnnotationConfig::ROUTE_ANNOTATIONS);
                }, ARRAY_FILTER_USE_KEY);
            }

            foreach($all as $anon=>$anonValue){
                $className = AnnotationConfig::ANNOTATION_NAMESPACE.$anon;
                
                if(class_exists($className)){
                    $annotation = new $className(null, $annotatedClassName, $anonValue, null, null, $method);
                    $annotation->execute();
                }
                //let the app continue in case of the loader intercepting regular doc comments
            }
        }
    }
}