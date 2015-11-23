<?php 

namespace Framework\Core\Annotations\CustomAnnotations;

class Required extends \Framework\Core\Annotations\Annotation
{
	public function execute(){
		if($this->annotationValue == true){
			if( (!isset($this->annotatedPropertyValue) )
				|| empty($this->annotatedPropertyValue) 
				|| $this->annotatedPropertyValue == false){
                    throw new \Exception("$this->annotatedProperty is required", 400);
			}
		}
	}
}