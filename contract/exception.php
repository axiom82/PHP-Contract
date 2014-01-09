<?php

class Contract_Exception extends Exception {
	
	protected $term;
	
	public function __construct($message, $term = null, $code = 0){

		parent::__construct($message, $code); 
		if (!is_null($term)) $this->term = $term;

	}
	
	public function __get($name){
		
		$property = null;
		
		if ($name == 'term') $property = $this->term;
		
		return $property;
		
	}
	
}

?>
