<?php
require_once('Contract/Term.php');

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
		
	public function data($term){
		
		return $this->term($term)->data();
		
	}
	
	public function debug($print = true){
		
		$debug = array();
		
		foreach ($this->terms as $term){
			
			$termName = $term->getName();
			$debug[$termName] = $term->debug(false);
		
		}
		
		if ($print) print_r($debug);
		
		return $debug;
		
	}
	
	public function getMets(){
		
		$mets = array();
		
		foreach ($this->terms as $term){
			
			$termMets = $term->getMets();
			$mets = array_merge($mets, $termMets);
			
		}
		
		return $mets;
		
	}
	
	public function met(){
		
		$mets = $this->getMets();
		
		foreach ($mets as $met) if ($met['met'] !== true){
				
			if ($met['predicate'] != 'Allowed') return false;
			
		}
		
		return true;
		
	}
	
	public function metOrThrow(){
		
		$mets = $this->getMets();
		
		foreach ($mets as $met) if ($met['met'] !== true){
				
			if ($met['predicate'] != 'Allowed'){
				
				require_once('Contract/Exception.php');
				throw new Contract_Exception('Contract term `' . $met['term'] . '` did not meet its requirements.', $met['name']);
			
			}
			
		}
		
		return true;
		
	}
	
	public function term($key, $data = null){
		
		$term = null;
		
		if (isset($this->terms[$key])) $term = $this->terms[$key];
		else $term = $this->terms[$key] = new Contract_Term($key, $data, $this);
		
		return $term;
		
	}
	
	public function __toString(){
		
		$string = '[contract:' . $this->reflection['class']->name . '/' . $this->reflection['method']->name . "]\n";
		foreach ($this->terms as $term) $string .= $term;
		return $string;
		
	}
	
}

?>
