<?php 

namespace Framework\Core\Annotations\CustomAnnotations;

class Matches extends \Framework\Core\Annotations\Annotation
{
	public function execute(){
		$prop = $this->annotationValue;
		$mustMatchVal = $this->annotatedClassInstance->$prop;

		if($this->annotatedPropertyValue != $mustMatchVal){
                throw new \Exception("$this->annotatedProperty does not match $this->annotationValue", 400);
		}
	}
}