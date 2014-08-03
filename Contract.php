<?php
require_once('Contract/Exception.php');
require_once('Contract/Term.php');

class Contract {
	
	const FROM_FACTORY = true;
	const MET = 'met';
	const MET_OR_THROW = 'metOrThrow';
	
	protected $name = '';
	protected $reflection = array();
	protected $terms = array();
	
	public function __construct(array $termsData = null, $metAction = null, $fromFactory = null){
		
		$trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
		$traceIndex = 1;
		
		if ($fromFactory === Contract::FROM_FACTORY) $traceIndex++;
		
		$object = $trace[$traceIndex];
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
		
		if (!is_null($termsData)) $this->terms($termsData);
		if (!is_null($metAction)) switch ($metAction){
			
			case self::MET_OR_THROW: $this->metOrThrow(); break;
			default: $this->metOrThrow(); break;
			
		}
		
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
	
	public static function factory(array $termsData = null, $metAction = null){
		
		$contract = new self($termsData, $metAction, Contract::FROM_FACTORY);
		return $contract;
		
	}
	
	public function find($termName){
		
		$terms = explode('/', $termName);
		$current = $this;
		
		foreach ($terms as $term){
			
			$term = $current->getTerm($term, false);
			if ($term instanceof Contract_Term_Abstract) $current = $term;
			else {
			
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
				
				throw new Contract_Exception('Contract term `' . $met['term'] . '` did not meet its requirement for ' . $met['predicate'] . '.', $met['name']);
			
			}
			
		}
		
		return true;
		
	}
	
	public function term($name, $data = null){
		
		$term = $this->getTerm($name, true, $data);
		return $term;
		
	}
	
	public function terms(array $termsData){
		
		foreach ($termsData as $termName => $termConfig){
			
			$name = $termName;
			$data = null;
			$definition = $termConfig;
			
			if (is_array($termConfig)){
				
				if (array_key_exists('data', $termConfig)) $data = $termConfig['data'];
				if (array_key_exists('definition', $termConfig)) $definition = $termConfig['definition'];
			
			}
			
			$term = $this->getTerm($name, true, $data);
			
			if (is_array($definition)){
				
				foreach ($definition as $definitionName => $definitionValue){
					
					if (method_exists($term, $definitionName)){
						
						switch ($definitionName){
							
							case 'element':
							
								if (is_array($definitionValue)){
									
									foreach ($definitionValue as $elementName => $elementConfig){
										
										$element = $term->element($elementName);
										
										if (is_array($elementConfig)){
													 
											foreach ($elementConfig as $elementDefinitionName => $elementDefinitionValue){
												
												if (method_exists($element, $elementDefinitionName)){
																										
													if (!is_array($elementDefinitionValue) || in_array($elementDefinitionName, array('allowed', 'alone', 'in'))) $elementDefinitionValue = array($elementDefinitionValue);
													call_user_func_array(array($element, $elementDefinitionName), $elementDefinitionValue);

													
												}
												else if (method_exists($element, $elementDefinitionValue)){
													
													call_user_func_array(array($element, $elementDefinitionValue), array());
													
												}
												
											}
													 
										}
										else {
											
											if (method_exists($element, $elementConfig)){
												
												call_user_func_array(array($element, $elementConfig), array());
													
											}
											
										}
										
									}
									
								}
							
							break;
							
							case 'elements':
							
								throw new Exception('Contract_Term::elements() is not supported when creating terms via array notation.');
								
							break;
							
							default:
							
								if (!is_array($definitionValue) || in_array($definitionName, array('allowed', 'alone', 'in'))) $definitionValue = array($definitionValue);
								call_user_func_array(array($term, $definitionName), $definitionValue);
							
							break;
							
						}
									  
					}
					else if (method_exists($term, $definitionValue)){
						
						call_user_func_array(array($term, $definitionValue), array());
						
					}
					
				}
				
			}
			else {
				
				if (method_exists($term, $definition)){
					
					call_user_func_array(array($term, $definition), array());
						
				}
				
			}
			
		}
		
		return $this;
		
	}
	
	public function __toString(){
		
		$string = '[contract' . (!empty($this->name) ? ':' . $this->name : '') . "]\n";
		
		$terms = $this->getTerms();
		foreach ($terms as $term) $string .= $term;
		
		return $string;
		
	}
	
}

?>