<?php 

namespace Framework\Core\Annotations;

class Annotation
{
	protected $annotatedClassInstance;
	protected $annotatedClass;
	protected $annotatedProperty;
	protected $annotatatedFunction;
	protected $annotatedPropertyValue;
	protected $annotationValue;

	public function __construct($annotatedClassInstance = null,
		$annotatedClass,
		$annotationValue,
		$annotatedProperty = null,
		$annotatedPropertyValue = null,
		$annotatedFunction = null){

		$this->annotatedClassInstance = $annotatedClassInstance;
		$this->annotatedClass = $annotatedClass;
		$this->annotatatedFunction = $annotatedFunction;
		$this->annotatedProperty = $annotatedProperty;
		$this->annotatedPropertyValue = $annotatedPropertyValue;
		$this->annotationValue = $annotationValue;
	}

	public function execute(){

	}
}