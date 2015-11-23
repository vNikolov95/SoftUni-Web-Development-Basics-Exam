<?php 

namespace Framework\Core\Annotations\CustomAnnotations;

class MinLength extends \Framework\Core\Annotations\Annotation
{
	public function execute(){
		$minLength = $this->annotationValue;

		if(strLen($this->annotatedPropertyValue) < $minLength){
                throw new \Exception("$this->annotatedProperty must be atleast $minLength character(s) long", 400);
		}
	}
}