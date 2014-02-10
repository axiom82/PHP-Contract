<?php
require_once('Abstract.php');

class Contract_Term_ProxyCollection extends Contract_Term_Abstract  {
	
	protected $parent = null;
	protected $returnTerms = null;
	
	public function __construct(Contract_Term $parent){
		
		$this->parent = $parent;
		$parentData = $parent->getData(Contract_Term::DATA_ORIGINAL);
		if (!is_array($parentData)) throw new Exception('Array expected for parent data.');
		
	}
	
	public function __call($method, $arguments){
		
		if (is_null($this->returnTerms)){
			
			if ($method == 'end') return $this->parent;
			
			$parentData = $this->parent->data(Contract_Term::DATA_ORIGINAL);
			$returnTerms = array();
			
			foreach ($parentData as $key => $value){
					
				$parentTerm = $this->parent->element($key);
				$returnTerms[] = call_user_func_array(array($parentTerm, $method), $arguments);
			
			}
				
			if ($method == 'element') $this->returnTerms = $returnTerms;

		}
		else {
			
			if ($method == 'end') $this->returnTerms = null;
			else foreach ($this->returnTerms as $returnTerm) call_user_func_array(array($returnTerm, $method), $arguments);
		
		}
		
		return $this;
		
	}
	
}

?>