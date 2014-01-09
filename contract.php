<?php
require_once('./contract/term.php');
require_once('./contract/exception.php');

class Contract {
	
	protected $reflection = array();
	public $terms = array();
	
	public function __construct(){
		
		$trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
		
		$object = $trace[1];
		unset($trace);
		
		$this->reflection['class'] = new ReflectionClass($object['class']);
		$this->reflection['method'] = $this->reflection['class']->getMethod($object['function']);
		$this->reflection['parameters'] = $this->reflection['method']->getParameters();
		
		foreach ($this->reflection['parameters'] as $p => $parameter){
			
			$termKey = $parameter->name;
			$termData = (isset($object['args'][$p])) ? $object['args'][$p] : (($parameter->isDefaultValueAvailable()) ? $parameter->getDefaultValue() : null);
			
			$this->terms[$termKey] = new Contract_Term($termKey, $termData, $this);
			
		}
		
	}
	
	public function debug($return = false){
		
		$debug = array();
		
		foreach ($this->terms as $term){
			
			$termName = $term->getName();
			$debug[$termName] = $term->debug(true);
		
		}
		
		if ($return) return $debug;
		else print_r($debug);
		
	}
	
	public function met(){
		
		$met = true;
		
		foreach ($this->terms as $term){
			
			$met = $term->met();
			if ($met == false) break;
			
		}
		
		return $met;
		
	}
	
	public function metOrThrow(){
		
		$met = $this->met();
		if ($met == false) throw new Contract_Exception('Contract terms did not meet their requirements in ' . $this->reflection['class']->getName() . '::' . $this->reflection['method']->getName() . '.', 'contract');
		return $met;
		
	}
	
	public function term($key, $data = null){
		
		$term = null;
		
		if (isset($this->terms[$key])) $term = $this->terms[$key];
		else $term = $this->terms[$key] = new Contract_Term($key, $data, $this);
		
		return $term;
		
	}
	
}

?>
