<?php
require_once('Term/Abstract.php');

class Contract_Term extends Contract_Term_Abstract {
	
	const DATA_ALLOWABLE = 'allowable';
	const DATA_MET = 'met';
	const DATA_ORIGINAL = 'original';
	
	protected $name = null;
	protected $fullName = null;
	
	protected $data = array();
	protected $dataAllowable = array();
	protected $dataMet = array();
	protected $scanned = false;
	
	protected $parent = null;
	protected $children = array();
	
	protected $meetAllowed = true;
	protected $meetAlone = false;
	protected $meetAlpha = false;
	protected $meetAlphaNumeric = false;
	protected $meetAlphaDash = false;
	protected $meetAlphaUnderscore = false;
	protected $meetArraylist = false;
	protected $meetBase64 = false;
	protected $meetBoolean = false;
	protected $meetCount = false;
	protected $meetDate = false;
	protected $meetDatetime = false;
	protected $meetDecimal = false;
	protected $meetEarlier = false;
	protected $meetEmail = false;
	protected $meetEquals = false;
	protected $meetFile = false;
	protected $meetGreaterThan = false;
	protected $meetId = false;
	protected $meetIn = false;
	protected $meetInteger = false;
	protected $meetIp = false;
	protected $meetLater = false;
	protected $meetLength = false;
	protected $meetLessThan = false;
	protected $meetMany = false;
	protected $meetNatural = false;
	protected $meetNaturalPositive = false;
	protected $meetNone = false;
	protected $meetNot = false;
	protected $meetNull = false;
	protected $meetNumeric = false;
	protected $meetOne = false;
	protected $meetOptional = false;
	protected $meetPhone = false;
	protected $meetRequired = false;
	protected $meetRow = false;
	protected $meetString = false;
	protected $meetTime = false;
	protected $meetUrl = false;
	protected $meetWithData = false;
	
	public function __construct($name, $data, $parent = null){
		
		$this->setName($name);
		$this->setData($data);
		$this->setParent($parent);
		$this->setChildren();
		
	}
	
	public function allowed($keys, $propagate = false){ if ($keys == '*'){ foreach ($this->children as $key => $term){ $this->dataAllowable[] = $key; if ($propagate) $term->allowed('*', $propagate); } } else { if (is_array($keys)) $this->dataAllowable = $keys; else $this->dataAllowable[] = $keys; } return $this; }
	public function alone(array $exceptions = null){ $this->meetAlone = (!is_null($exceptions)) ? $exceptions : true; return $this; }
	public function alpha(){ $this->meetAlpha = true; return $this; }
	public function alphaNumeric(){ $this->meetAlphaNumeric = true; return $this; }
	public function alphaDash(){ $this->meetAlphaDash = true; return $this; }
	public function alphaUnderscore(){ $this->meetAlphaUnderscore = true; return $this; }
	public function arraylist($all = false){ $this->meetArraylist = ($all) ? 'all' : 'one'; return $this; }
	public function base64(){ $this->meetBase64 = true; return $this; }
	public function boolean($strict = true){ $this->meetBoolean = ($strict) ? 'strict' : 'loose'; return $this; }
	public function count($value = null, $value2 = null){ $this->meetCount = (!is_null($value) || !is_null($value2)) ? array($value, $value2) : false; return $this; }
	public function date(){ $this->meetDate = true; return $this; }
	public function datetime(){ $this->meetDatetime = true; return $this; }
	public function decimal(){ $this->meetDecimal = true; return $this; }
	public function earlier($timestamp = null){ $this->meetEarlier = (!is_null($timestamp)) ? strtotime($timestamp) : time(); return $this; }
	public function email(){ $this->meetEmail = true; return $this; }
	public function equals($value){ $this->meetEquals = $value; return $this; }
	public function file(){ $this->meetFile = true; return $this; }
	public function greaterThan($value){ $this->meetGreaterThan = $value; return $this; }
	public function id(){ $this->meetId = true; return $this; }
	public function in(array $values){ $this->meetIn = $values; return $this; }
	public function integer(){ $this->meetInteger = true; return $this; }
	public function ip(){ $this->meetIp = true; return $this; }
	public function later($timestamp = null){ $this->meetLater = (!is_null($timestamp)) ? strtotime($timestamp) : time(); return $this; }
	public function length($value = null, $value2 = null){ $this->meetLength = (!is_null($value) || !is_null($value2)) ? array($value, $value2) : false; return $this; }
	public function lessThan($value){ $this->meetLessThan = $value; return $this; }
	public function many(){ $this->meetMany = true; return $this; }
	public function natural(){ $this->meetNatural = true; return $this; }
	public function naturalPositive(){ $this->meetNaturalPositive = true; return $this; }
	public function none(){ $this->meetNone = true; return $this; }
	public function not($value){ $this->meetNot = $value; return $this; }
	public function null(){ $this->meetNull = true; return $this; }
	public function numeric(){ $this->meetNumeric = true; return $this; }
	public function object($type = null){ $this->meetObject = (!is_null($type)) ? $type : true; return $this; }
	public function one(){ $this->meetOne = true; return $this; }
	public function optional($optional = true){ $this->meetOptional = $optional; return $this; }
	public function phone($strict = false){ $this->meetPhone = ($strict) ? 'strict' : 'loose'; return $this; }
	public function required($required = true){ $this->meetRequired = $required; return $this; }
	public function row($all = false){ $this->meetRow = ($all) ? 'all' : 'one'; return $this; }
	public function string(){ $this->meetString = true; return $this; }
	public function time(){ $this->meetTime = true; return $this; }
	public function url(){ $this->meetUrl = true; return $this; }
	public function withData(){ $this->meetWithData = true; return $this; }
	
	public function data($type = null){
		
		return $this->getData($type);
		
	}

	public function debug($print = true){
		
		$mets = $this->getMets();
		
		foreach ($mets as $m => $met) if ($met['met'] === true) unset($mets[$m]);
		
		if ($print) print_r($mets);
		
		return $mets;
		
	}
	
	public function element($name){
		
		return $this->getTerm($name);
		
	}
	
	public function elements(){
		
		require_once('Term/ProxyCollection.php');
		return new Contract_Term_ProxyCollection($this);
		
	}
	
	public function end($recursion = false){
		
		return $this->parent();
		
	}
	
	public function find($termName){
		
		$terms = explode('/', $termName);
		$current = $this;
		
		foreach ($terms as $term){
			
			$term = $current->getTerm($term, false);
			if ($term instanceof Contract_Term_Abstract) $current = $term;
			else {
			
				require_once('Exception.php');	
				throw new Contract_Exception('Could not find: ' . $termName);
				
			}
			
		}
		
		return $current;
		
	}	
	
	public function getData($type = null){
		
		switch ($type){
			case self::DATA_ALLOWABLE: return $this->dataAllowable;
			case self::DATA_MET: return $this->getMetData(false);
			case self::DATA_ORIGINAL: return $this->data;
			default: return $this->getMetData();
		}
		
	}
	
	public function getFullName(){
		
		if (is_null($this->fullName)){
			
			$fullName = $this->getName();
			if ($this->parent && $this->parent instanceof Contract_Term) $fullName = $this->parent->getFullName() . '/' . $fullName;
			$this->fullName = $fullName;
		
		}
		
		return $this->fullName;
		
	}

	public function getMetData($rescan = false){
		
		if ($this->scanned && !$rescan) return $this->dataMet;
		
		$dataMet = $this->getData(self::DATA_ORIGINAL);
		$mets = $this->getMets();
		
		foreach ($dataMet as $key => $value){
			
			$keyFullName = $this->element($key)->getFullName();
			
			foreach ($mets as $metFullName => $met){
			
				$metFullName = implode('/', array_slice(explode('/', $metFullName), 0, -1));
				if (strpos($metFullName, $keyFullName) === 0){
					
					if ($met['met'] === false){
						
						if ($met['predicate'] == 'Allowed') eval('unset($dataMet[' . implode('][', array_slice(explode('/', $metFullName), 1)) . ']);');
						else unset($dataMet[$key]);
							 
					}
				
				}
				
			}
			
		}
		
		$this->dataMet = $dataMet;
		$this->scanned = true;
		
		return $this->dataMet;
		
	}
	
	public function getMets(){ 
	
		$mets = array();
		
		if ($this->meetOptional == true && empty($this->data)) return $mets;
		
		$properties = get_object_vars($this);
		foreach ($properties as $propertyName => $propertyValue) if (substr($propertyName, 0, 4) == 'meet' && $propertyValue !== false){
				
			$condition = substr($propertyName, 4);
			$method = 'met' . $condition;
			$methodMets = $this->$method();
			$mets = array_merge($mets, $methodMets);
			
		}
					 
		foreach ($this->children as $childName => $childTerm){
			
			$childMets = $childTerm->getMets();
			$mets = array_merge($mets, $childMets);
		
		}
		
		return $mets;	
		
	}
	
	public function getName(){
		
		return $this->name;
		
	}
		
	protected function getParent($recursion = false){
	
		if ($recursion && $this->parent instanceof Contract_Term){
			
			if (is_string($recursion) && $recursion == $this->parent->getName()) return $this->parent;
			if (is_int($recursion)) $recursion--;
			$parent = $this->parent->getParent($recursion);
			
		}
		else $parent = $this->parent;
		
		return $parent;
	
	}
	
	protected function getPredicateFullName($predicateName){
		
		$fullName = $this->getFullName();
		$predicateFullName = $fullName . '/' . $predicateName;
		return $predicateFullName;
		
	}
		
	public function getTerm($name, $find = true){
		
		if ($find) $term = $this->find($name);
		else $term = $this->children[$name];
		return $term;
		
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
			
				require_once('Exception.php');
				throw new Contract_Exception('Contract term `' . $met['term'] . '` did not meet its requirement for ' . $met['predicate'] . '.', $met['name']);	
				
			}
			
		}
		
		return true;
		
	}
	
	protected function metAllowed(){ return $this->scanOne('Allowed'); }
	protected function metAlone(){ return $this->scanAll('Alone'); }	
	protected function metAlpha(){ return $this->scanAll('Alpha'); }	
	protected function metAlphaNumeric(){ return $this->scanAll('AlphaNumeric'); }
	protected function metAlphaDash(){ return $this->scanAll('AlphaDash'); }
	protected function metAlphaUnderscore(){ return $this->scanAll('AlphaUnderscore'); }
	protected function metArraylist(){ switch($this->meetArraylist){ case 'all': return $this->scanAll('Arraylist'); break; case 'one': return $this->scanOne('Arraylist'); break; } }
	protected function metBoolean(){ return $this->scanAll('Boolean'); }
	protected function metBase64(){ return $this->scanAll('Base64'); }
	protected function metCount(){ return $this->scanOne('Count'); }
	protected function metDate(){ return $this->scanAll('Date'); }
	protected function metDatetime(){ return $this->scanAll('Datetime'); }
	protected function metDecimal(){ return $this->scanAll('Decimal'); }
	protected function metEarlier(){ return $this->scanAll('Earlier'); }
	protected function metEmail(){ return $this->scanAll('Email'); }
	protected function metEquals(){ return $this->scanAll('Equals'); }
	protected function metFile(){ return $this->scanAll('File'); }
	protected function metGreaterThan(){ return $this->scanAll('GreaterThan'); }
	protected function metId(){ return $this->scanAll('Id'); }
	protected function metIn(){ return $this->scanAll('In'); }
	protected function metInteger(){ return $this->scanAll('Integer'); }
	protected function metIp(){ return $this->scanAll('Ip'); }
	protected function metLater(){ return $this->scanAll('Later'); }
	protected function metLength(){ return $this->scanAll('Length'); }
	protected function metLessThan(){ return $this->scanAll('LessThan'); }
	protected function metMany(){ return $this->scanOne('Many'); }
	protected function metNatural(){ return $this->scanAll('Natural'); }
	protected function metNaturalPositive(){ return $this->scanAll('NaturalPositive'); }
	protected function metNone(){ return $this->scanOne('None'); }
	protected function metNot(){ return $this->scanAll('Not'); }
	protected function metNull(){ return $this->scanAll('Null'); }
	protected function metNumeric(){ return $this->scanAll('Numeric'); }
	protected function metObject(){ return $this->scanAll('Object'); }
	protected function metOne(){ return $this->scanOne('One'); }
	protected function metOptional(){ return $this->scanOne('Optional'); }
	protected function metPhone(){ return $this->scanAll('Phone'); }
	protected function metRequired(){ return $this->scanOne('Required'); }
	protected function metRow(){ switch($this->meetRow){ case 'all': return $this->scanAll('Row'); break; case 'one': return $this->scanOne('Row'); break; } }
	protected function metString(){ return $this->scanAll('String'); }
	protected function metTime(){ return $this->scanAll('Time'); }
	protected function metUrl(){ return $this->scanAll('Url'); }
	protected function metWithData(){ return $this->scanOne('WithData'); }
	
	public function parent($recursion = false){
		
		return $this->getParent($recursion);
		
	}
	
	protected function predicateAllowed($value, $key){ if (is_null($this->parent) || !($this->parent instanceof self)) return false; return in_array($this->getName(), $this->parent->data(self::DATA_ALLOWABLE)); }
	protected function predicateAlone($value){ if (is_null($this->parent) || !($this->parent instanceof self)) return true; $parentData = $this->parent->getData(self::DATA_ORIGINAL); if (!is_array($parentData)) return true; if ($this->meetAlone === true) return (count($parentData) == 1 && array_key_exists($this->getName(), $parentData)); $parentDataAlone = array_diff_key($parentData, array_flip($this->meetAlone)); return (count($parentDataAlone) == 1 && array_key_exists($this->getName(), $parentDataAlone)); }
	protected function predicateAlpha($value){ return (bool) preg_match('/[a-zA-Z]+/', $value); }
	protected function predicateAlphaNumeric($value){ return (bool) preg_match('/[a-zA-Z0-9]+/', $value); }
	protected function predicateAlphaDash($value){ return (bool) preg_match('/[a-zA-Z0-9-]+/', $value); }
	protected function predicateAlphaUnderscore($value){ return (bool) preg_match('/[a-z0-9_]+/', $value); }
	protected function predicateArraylist($value){ return is_array($value); }
	protected function predicateBase64($value){ return (bool) !preg_match('/[^a-zA-Z0-9\/\+=]/', $value); }
	protected function predicateBoolean($value){ return ($this->meetBoolean == 'strict') ? is_bool($value) : is_bool($value) || $value === 0 || $value === 1 || $value === '0' || $value === '1'; }
	protected function predicateCount($value){ $count = count($value); if (!is_null($this->meetCount[0]) && !is_null($this->meetCount[1])){ if (is_numeric($this->meetCount[0]) && is_numeric($this->meetCount[1])) return ($count >= $this->meetCount[0] && $count <= $this->meetCount[1]); if (is_numeric($this->meetCount[0])) return $count >= $this->meetCount[0]; else return $count <= $this->meetCount[1]; } else if (!is_null($this->meetCount[0])) return $count == $this->meetCount[0]; return false; }
	protected function predicateDate($value){ return (bool) preg_match('/^(((\d{4})(-)(0[13578]|10|12)(-)(0[1-9]|[12][0-9]|3[01]))|((\d{4})(-)(0[469]|1??1)(-)([0][1-9]|[12][0-9]|30))|((\d{4})(-)(02)(-)(0[1-9]|1[0-9]|2[0-8]))|(([02468]??[048]00)(-)(02)(-)(29))|(([13579][26]00)(-)(02)(-)(29))|(([0-9][0-9][0][48])(-)(0??2)(-)(29))|(([0-9][0-9][2468][048])(-)(02)(-)(29))|(([0-9][0-9][13579][26])(-)(02??)(-)(29)))$/', $value); }
	protected function predicateDatetime($value){ return (bool) preg_match('/^(((\d{4})(-)(0[13578]|10|12)(-)(0[1-9]|[12][0-9]|3[01]))|((\d{4})(-)(0[469]|1??1)(-)([0][1-9]|[12][0-9]|30))|((\d{4})(-)(02)(-)(0[1-9]|1[0-9]|2[0-8]))|(([02468]??[048]00)(-)(02)(-)(29))|(([13579][26]00)(-)(02)(-)(29))|(([0-9][0-9][0][48])(-)(0??2)(-)(29))|(([0-9][0-9][2468][048])(-)(02)(-)(29))|(([0-9][0-9][13579][26])(-)(02??)(-)(29)))(\s([0-1][0-9]|2[0-4]):([0-5][0-9]):([0-5][0-9]))$/', $value); }
	protected function predicateDecimal($value){ return filter_var($value, FILTER_VALIDATE_FLOAT) !== false; }
	protected function predicateEarlier($value){ return strtotime($value) < $this->meetEarlier; }
	protected function predicateEmail($value){ return filter_var($value, FILTER_VALIDATE_EMAIL) !== false; }
	protected function predicateEquals($value){ return $value == $this->meetEquals; }
	protected function predicateFile($value){ return is_file($value); }
	protected function predicateGreaterThan($value){ return $value > $this->meetGreaterThan; }
	protected function predicateId($value){ return (bool) preg_match('/^[0-9]+$/', $value) && $value > 0;  }
	protected function predicateIn($value){ return in_array($value, $this->meetIn); }
	protected function predicateInteger($value){ return filter_var($value, FILTER_VALIDATE_INT) !== false; }
	protected function predicateIp($value){ return filter_var($value, FILTER_VALIDATE_IP) !== false; }
	protected function predicateLater($value){ return strtotime($value) > $this->meetLater; }
	protected function predicateLength($value){ $length = strlen($value); if (!is_null($this->meetLength[0]) && !is_null($this->meetLength[1])){ if ($this->meetLength[0] !== false && $this->meetLength[1] !== false) return ($length >= $this->meetLength[0] && $length <= $this->meetLength[1]); if ($this->meetLength[0] !== false) return $length >= $this->meetLength[0]; return $length <= $this->meetLength[1]; } else if (!is_null($this->meetLength[0])) return $length == $this->meetLength[0]; return false; }
	protected function predicateLessThan($value){ return $value < $this->meetLessThan; }
	protected function predicateMany($value){ return count($value) > 1; }
	protected function predicateNatural($value){ return (bool) preg_match('/^[0-9]+$/', $value); }
	protected function predicateNaturalPositive($value){ return (bool) preg_match('/^[0-9]+$/', $value) && $value > 0; }
	protected function predicateNone($value){ return empty($this->data); }
	protected function predicateNot($value){ return (is_array($this->meetNot)) ? !in_array($value, $this->meetNot) : $value != $this->meetNot; }
	protected function predicateNull($value){ return is_null($value); }
	protected function predicateNumeric($value){ return is_numeric($value); }
	protected function predicateObject($value){ return ($this->meetObject === true) ? is_object($value) : is_object($value) && $value instanceof $this->meetObject; }
	protected function predicateOne($value){ return count($value) == 1; }
	protected function predicateOptional($value){ return true; }
	protected function predicatePhone($value){ if ($this->meetPhone == 'strict') return (bool) preg_match('/^\([0-9]{3}\)\s?[0-9]{3}-[0-9]{4}$/', $value); else return (strlen(preg_replace('/[^0-9]/', '', $value)) >= 10); }
	protected function predicateRequired($value){ if (is_array($this->data)){ if (is_array($this->meetRequired)) foreach ($this->meetRequired as $required) if (empty($this->data[$required])) return false; return !empty($this->data); } return (trim($this->data) != ''); }
	protected function predicateRow($value){ return (isset($value['id']) && preg_match('/^[0-9]+$/', $value['id']) && $value['id'] > 0); }
	protected function predicateString($value){ return is_string($value); }
	protected function predicateTime($value){ return (bool) preg_match('/^(([0-1][0-9]|2[0-4]):([0-5][0-9]):([0-5][0-9]))$/', $value);  }
	protected function predicateURL($value){ return filter_var($value, FILTER_VALIDATE_URL) !== false; }
	protected function predicateWithData($value){ $data = $this->data(self::DATA_ORIGINAL); return !empty($data); }
		
	protected function scanAll($predicate){
		
		$mets = array();
		$predicateMethod = 'predicate' . $predicate;
		$predicateFullName = $this->getPredicateFullName($predicate);
		
		if (!is_array($this->data)){
			
			$value = $this->data;
			$met = call_user_func_array(array($this, $predicateMethod), array($value, $this->getName()));
			$mets[$predicateFullName] = array('term' => $this->getFullName(), 'name' => $this->getName(), 'value' => $value, 'predicate' => $predicate, 'met' => $met);

		}
		else foreach ($this->data as $key => $value){
			
			$met = call_user_func_array(array($this, $predicateMethod), array($value, $key));
			$mets[$predicateFullName] = array('term' => $this->getFullName(), 'name' => $key, 'value' => $value, 'predicate' => $predicate, 'met' => $met);
			
		}
		
		return $mets;
		
	}
	
	protected function scanOne($predicate){
		
		$mets = array();
		$predicateMethod = 'predicate' . $predicate;
		$predicateFullName = $this->getPredicateFullName($predicate);
		
		$value = $this->data;
		$met = call_user_func_array(array($this, $predicateMethod), array($value, $this->getName())); 
		$mets[$predicateFullName] = array('term' => $this->getFullName(), 'name' => $this->getName(), 'value' => $value, 'predicate' => $predicate, 'met' => $met);

		return $mets;
		
	}
	
	protected function setChildren(){
		
		if (is_array($this->data)) foreach ($this->data as $name => $data) $this->children[$name] = new Contract_Term($name, $data, $this);
		
	}
	
	protected function setData($data = null){
		
		$this->data = $data;
		
	}
	
	protected function setName($name){
		
		if (is_null($this->name)) $this->name = $name;
		
	}
	
	protected function setParent($parent){
		
		if ($parent instanceof Contract || $parent instanceof Contract_Term) $this->parent = $parent;
		
	}

	
	public function __toString(){
		
		$string = '[term:' . $this->getFullName() . "]\n";
		$vars = get_object_vars($this);
		foreach ($vars as $key => $value) if (substr($key, 0, 4) == 'meet' && $value !== false) $string .= "\t" . $key . "\n";
		foreach ($this->children as $term) $string .= $term;
		return $string;
		
	}
	
}

?>
