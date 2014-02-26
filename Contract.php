<?php
require_once('Contract/Term.php');

class Contract {
	
	protected $name = '';
	protected $reflection = array();
	protected $terms = array();
	
	public function __construct(){
		
		$trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
		
		$object = $trace[1];
		unset($trace);
		
		if (isset($object)){
			
			$this->reflection['class'] = new ReflectionClass($object['class']);
			$this->reflection['method'] = $this->reflection['class']->getMethod($object['function']);
			$this->reflection['parameters'] = $this->reflection['method']->getParameters();
			$this->name = $this->reflection['class']->name . '/' . $this->reflection['method']->name;
			
			foreach ($this->reflection['parameters'] as $p => $parameter){
				
				$termName = $parameter->name;
				$termData = (isset($object['args'][$p])) ? $object['args'][$p] : (($parameter->isDefaultValueAvailable()) ? $parameter->getDefaultValue() : null);
				
				$this->terms[$termName] = new Contract_Term($termName, $termData, $this);
				
			}
		
		}
		
		return $this;
		
	}
		
	public function data($term){
		
		return $this->getData($term);
		
	}
	
	public function debug($print = true){
		
		$debug = array();
		
		$terms = $this->getTerms();
		foreach ($terms as $term){
			
			$termName = $term->getName();
			$debug[$termName] = $term->debug(false);
		
		}
		
		if ($print) print_r($debug);
		
		return $debug;
		
	}
	
	public function find($termName){
		
		$terms = explode('/', $termName);
		$current = $this;
		
		foreach ($terms as $term){
			
			$term = $current->getTerm($term, false);
			if ($term instanceof Contract_Term_Abstract) $current = $term;
			else {
			
				require_once('Contract/Exception.php');	
				throw new Contract_Exception('Could not find: ' . $termName);
				
			}
			
		}
		
		return $current;
		
	}
	
	public function getData($term){
		
		return $this->getTerm($term)->data();	
		
	}
	
	public function getMets(){
		
		$mets = array();
		
		$terms = $this->getTerms();
		foreach ($terms as $term){
			
			$termMets = $term->getMets();
			$mets = array_merge($mets, $termMets);
			
		}
		
		return $mets;
		
	}
	
	public function getName(){
		
		return $this->name;	
		
	}
	
	public function getTerm($name, $find = true, $data = null){
		
		if ($find && is_null($data)) $term = $this->find($name);
		else $term = $this->terms[$name];
		
		if (!$term instanceof Contract_Term_Abstract) $term = $this->terms[$name] = new Contract_Term($name, $data, $this);

		return $term;
		
	}
	
	public function getTerms(){
		
		return $this->terms;	
		
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
				throw new Contract_Exception('Contract term `' . $met['term'] . '` did not meet its requirement for ' . $met['predicate'] . '.', $met['name']);
			
			}
			
		}
		
		return true;
		
	}
	
	public function term($name, $data = null){
		
		$term = $this->getTerm($name, true, $data);
		return $term;
		
	}
	
	public function __toString(){
		
		$string = '[contract' . (!empty($this->name) ? ':' . $this->name : '') . "]\n";
		
		$terms = $this->getTerms();
		foreach ($terms as $term) $string .= $term;
		
		return $string;
		
	}
	
}

?>
