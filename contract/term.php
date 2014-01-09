<?php
require_once('exception.php');

class Contract_Term {
	
	protected $name = null;
	
	protected $data = array();
	protected $dataAllowed = array();
	
	protected $parent = null;
	protected $children = array();
	
	protected $meetAllowed = false;
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
	protected $meetGreaterThan = false;
	protected $meetId = false;
	protected $meetIn = false;
	protected $meetInteger = false;
	protected $meetIp = false;
	protected $meetLater = false;
	protected $meetLength = false;
	protected $meetLessThan = false;
	protected $meetNatural = false;
	protected $meetNaturalPositive = false;
	protected $meetNone = false;
	protected $meetNot = false;
	protected $meetNull = false;
	protected $meetNumeric = false;
	protected $meetOptional = false;
	protected $meetPhone = false;
	protected $meetRequired = false;
	protected $meetString = false;
	protected $meetUrl = false;
	protected $meetWithData = false;
	
	protected $debugging = false;
	
	public function __construct($name, $data, $parent = null){ $this->name = $name; $this->data = $data; if (!is_null($parent) && ($parent instanceof Contract || $parent instanceof Contract_Term)) $this->parent = $parent; }
	
	public function allowed(array $keys){ $this->meetAllowed = $keys; return $this; }
	public function alone(array $exceptions = null){ $this->meetAlone = (!is_null($exceptions)) ? $exceptions : true; return $this; }
	public function alpha(){ $this->meetAlpha = true; return $this; }
	public function alphaNumeric(){ $this->meetAlphaNumeric = true; return $this; }
	public function alphaDash(){ $this->meetAlphaDash = true; return $this; }
	public function alphaUnderscore(){ $this->meetAlphaUnderscore = true; return $this; }
	public function arraylist(){ $this->meetArraylist = true; return $this; }
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
	public function natural(){ $this->meetNatural = true; return $this; }
	public function naturalPositive(){ $this->meetNaturalPositive = true; return $this; }
	public function none(){ $this->meetNone = true; return $this; }
	public function not($value){ $this->meetNot = $value; return $this; }
	public function null(){ $this->meetNull = true; return $this; }
	public function numeric(){ $this->meetNumeric = true; return $this; }
	public function object($type = null){ $this->meetObject = (!is_null($type)) ? $type : true; return $this; }
	public function optional($optional = true){ $this->meetOptional = $optional; return $this; }
	public function phone($strict = false){ $this->meetPhone = ($strict) ? 'strict' : 'loose'; return $this; }
	public function string(){ $this->meetString = true; return $this; }
	public function time(){ $this->meetTime = true; return $this; }
	public function url(){ $this->meetUrl = true; return $this; }
	public function required($required = true){ $this->meetRequired = $required; return $this; }
	public function withData(){ $this->meetWithData = true; return $this; }
	
	public function data(){ if (is_array($this->meetAllowed)) return array_intersect_key($this->data, array_flip($this->meetAllowed)); return $this->data; }

	public function element($name){
		
		$term = null;
		
		if (isset($this->children[$name])){
			
			$term = $this->children[$name];
			
		}
		else {
			
			$data = (isset($this->data[$name])) ? $this->data[$name] : null;
			$term = $this->children[$name] = new Contract_Term($name, $data, $this);
			
			if (!is_array($this->meetAllowed)) $this->meetAllowed = array($name);
			else if (!in_array($name, $this->meetAllowed)) $this->meetAllowed[] = $name;
			
		}
		
		return $term;
		
	}
	
	public function end(){ return $this->parent; }

	public function debug($return = false){ 
	
		$this->debugging = true;
		
		$debug = array();
		
		if (empty($this->data) && $this->meetOptional == true) return $debug;
		
		$properties = get_object_vars($this);
		
		foreach ($properties as $propertyName => $propertyValue){
			
			if (substr($propertyName, 0, 4) == 'meet' && $propertyValue !== false){
				
				$condition = substr($propertyName, 4);
				$method = 'met' . $condition;
				$met = $this->$method();
				if ($met == false) $debug[strtolower($condition)] = $this->data;
				
			}
			
		}
		
		foreach ($this->children as $child){
			
			$childDebug = $child->debug($return);
			$childName = $child->getName();
			if (!empty($childDebug)) $debug['elements'][$childName] = $childDebug;
			
		}	
		
		$this->debugging = false;
		
		if (!empty($debug)){
		
			if ($return) return $debug;	
			else print_r($debug);
			
		}
		
	}
	
	public function met(){ 
		
		$met = true;
		
		if (empty($this->data) && $this->meetOptional == true) return $met;
		
		$properties = get_object_vars($this);
		
		foreach ($properties as $propertyName => $propertyValue){
			
			if (substr($propertyName, 0, 4) == 'meet' && $propertyValue !== false){
				
				$condition = substr($propertyName, 4);
				$method = 'met' . $condition;
				$met = $this->$method();
				if ($met == false) break;
				
			}
			
		}
		
		foreach ($this->children as $child){
			
			$met = $child->met();
			if ($met == false) break;
			
		}
		
		return $met;		
		
	}
	
	public function metOrThrow(){
		
		$met = $this->met();
		if ($met == false) throw new Contract_Exception('Contract term `' . $this->name . '` did not meet its requirements.', $this->name);
		return $met;
		
	}
	
	protected function metAllowed(){ return true; }
	protected function metAlone(){ return $this->predicate('Alone'); }	
	protected function metAlpha(){ return $this->predicate('Alpha'); }	
	protected function metAlphaNumeric(){ return $this->predicate('AlphaNumeric'); }
	protected function metAlphaDash(){ return $this->predicate('AlphaDash'); }
	protected function metAlphaUnderscore(){ return $this->predicate('AlphaUnderscore'); }
	protected function metArraylist(){ return is_array($this->data); }
	protected function metBoolean(){ return $this->predicate('Boolean'); }
	protected function metBase64(){ return $this->predicate('Base64'); }
	protected function metCount(){ return $this->predicate('Count'); }
	protected function metDate(){ return $this->predicate('Date'); }
	protected function metDatetime(){ return $this->predicate('Datetime'); }
	protected function metDecimal(){ return $this->predicate('Decimal'); }
	protected function metEarlier(){ return $this->predicate('Earlier'); }
	protected function metEmail(){ return $this->predicate('Email'); }
	protected function metEquals(){ return $this->predicate('Equals'); }
	protected function metFile(){ return $this->predicate('File'); }
	protected function metGreaterThan(){ return $this->predicate('GreaterThan'); }
	protected function metId(){ return $this->predicate('Id'); }
	protected function metIn(){ return $this->predicate('In'); }
	protected function metInteger(){ return $this->predicate('Integer'); }
	protected function metIp(){ return $this->predicate('Ip'); }
	protected function metLater(){ return $this->predicate('Later'); }
	protected function metLength(){ return $this->predicate('Length'); }
	protected function metLessThan(){ return $this->predicate('LessThan'); }
	protected function metNatural(){ return $this->predicate('Natural'); }
	protected function metNaturalPositive(){ return $this->predicate('NaturalPositive'); }
	protected function metNone(){ return empty($this->data); }
	protected function metNot(){ return $this->predicate('Not'); }
	protected function metNull(){ return $this->predicate('Null'); }
	protected function metNumeric(){ return $this->predicate('Numeric'); }
	protected function metObject(){ return $this->predicate('Object'); }
	protected function metOptional(){ return true; }
	protected function metPhone(){ return $this->predicate('Phone'); }
	protected function metString(){ return $this->predicate('String'); }
	protected function metTime(){ return $this->predicate('Time'); }
	protected function metUrl(){ return $this->predicate('Url'); }
	protected function metWithData(){ $data = $this->data(); return !empty($data); }
	protected function metRequired(){
		
		$met = true;
		
		if (is_array($this->data)){
			
			if (is_array($this->meetRequired)){
				
				foreach ($this->meetRequired as $required){
					
					if (empty($this->data[$required])){
						
						$met = false;
						break;
						
					}
					
				}
				
			}
			else {
				
				$met = !empty($this->data);
				
			}
			
		}
		else {
			
			$met = (trim($this->data) == '') ? false : true;
			
		}
		
		return $met;
		
	}
	
	protected function predicate($predicate){
		
		$met = true;
		$predicate = 'predicate' . $predicate;
		
		if (!is_array($this->data)){
			
			$value = $this->data;
			$met = call_user_func_array(array($this, $predicate), array($value));
			
		}
		else {
			
			foreach ($this->data as $value){
				
				$met = call_user_func_array(array($this, $predicate), array($value));;
				if ($met == false) break;
				
			}
			
		}
		
		return $met;
		
	}
	
	protected function predicateAlone($value){ if (is_null($this->parent) || !($this->parent instanceof self)) return true; $parentData = $this->parent->data(); if (!is_array($parentData)) return true; if ($this->meetAlone === true) return (count($parentData) == 1 && array_key_exists($this->name, $parentData)); $parentDataAlone = array_diff_key($parentData, array_flip($this->meetAlone)); return (count($parentDataAlone) == 1 && array_key_exists($this->name, $parentDataAlone)); }
	protected function predicateAlpha($value){ return (bool) preg_match('/[a-zA-Z]+/', $value); }
	protected function predicateAlphaNumeric($value){ return (bool) preg_match('/[a-zA-Z0-9]+/', $value); }
	protected function predicateAlphaDash($value){ return (bool) preg_match('/[a-zA-Z0-9-]+/', $value); }
	protected function predicateAlphaUnderscore($value){ return (bool) preg_match('/[a-z0-9_]+/', $value); }
	protected function predicateBase64($value){ return (bool) !preg_match('/[^a-zA-Z0-9\/\+=]/', $value); }
	protected function predicateBoolean($value){ return ($this->meetBoolean == 'strict') ? is_bool($value) : is_bool($value) || $value === 0 || $value === 1 || $value === '0' || $value === '1'; }
	protected function predicateCount($value){ $count = count($value); if (!is_null($this->meetCount[0]) && !is_null($this->meetCount[1])){ if ($this->meetCount[0] !== false && $this->meetCount[1] !== false) return ($count >= $this->meetCount[0] && $count <= $this->meetCount[1]); if ($this->meetCount[0] !== false) return $count >= $this->meetCount[0]; else return $count <= $this->meetCount[1]; } else if (!is_null($this->meetCount[0])) return $count == $this->meetCount[0]; return false; }
	protected function predicateDate($value){ return (bool) preg_match('/^(((\d{4})(-)(0[13578]|10|12)(-)(0[1-9]|[12][0-9]|3[01]))|((\d{4})(-)(0[469]|1??1)(-)([0][1-9]|[12][0-9]|30))|((\d{4})(-)(02)(-)(0[1-9]|1[0-9]|2[0-8]))|(([02468]??[048]00)(-)(02)(-)(29))|(([13579][26]00)(-)(02)(-)(29))|(([0-9][0-9][0][48])(-)(0??2)(-)(29))|(([0-9][0-9][2468][048])(-)(02)(-)(29))|(([0-9][0-9][13579][26])(-)(02??)(-)(29)))$/', $value); }
	protected function predicateDatetime($value){ return (bool) preg_match('/^(((\d{4})(-)(0[13578]|10|12)(-)(0[1-9]|[12][0-9]|3[01]))|((\d{4})(-)(0[469]|1??1)(-)([0][1-9]|[12][0-9]|30))|((\d{4})(-)(02)(-)(0[1-9]|1[0-9]|2[0-8]))|(([02468]??[048]00)(-)(02)(-)(29))|(([13579][26]00)(-)(02)(-)(29))|(([0-9][0-9][0][48])(-)(0??2)(-)(29))|(([0-9][0-9][2468][048])(-)(02)(-)(29))|(([0-9][0-9][13579][26])(-)(02??)(-)(29)))(\s([0-1][0-9]|2[0-4]):([0-5][0-9]):([0-5][0-9]))$/', $value); }
	protected function predicateDecimal($value){ return filter_var($value, FILTER_VALIDATE_FLOAT); }
	protected function predicateEarlier($value){ return strtotime($value) < $this->meetEarlier; }
	protected function predicateEmail($value){ return filter_var($value, FILTER_VALIDATE_EMAIL); }
	protected function predicateEquals($value){ return $value == $this->meetEquals; }
	protected function predicateFile($value){ return is_file($value); }
	protected function predicateGreaterThan($value){ return $value > $this->meetGreaterThan; }
	protected function predicateId($value){ return (bool) preg_match('/^[0-9]+$/', $value) && $value > 0;  }
	protected function predicateIn($value){ return in_array($value, $this->meetIn); }
	protected function predicateInteger($value){ return filter_var($value, FILTER_VALIDATE_INT); }
	protected function predicateIp($value){ return filter_var($value, FILTER_VALIDATE_IP); }
	protected function predicateLater($value){ return strtotime($value) > $this->meetLater; }
	protected function predicateLength($value){ $length = strlen($value); if (!is_null($this->meetLength[0]) && !is_null($this->meetLength[1])){ if ($this->meetLength[0] !== false && $this->meetLength[1] !== false) return ($length >= $this->meetLength[0] && $length <= $this->meetLength[1]); if ($this->meetLength[0] !== false) return $length >= $this->meetLength[0]; return $length <= $this->meetLength[1]; } else if (!is_null($this->meetLength[0])) return $length == $this->meetLength[0]; return false; }
	protected function predicateLessThan($value){ return $value < $this->meetLessThan; }
	protected function predicateNatural($value){ return (bool) preg_match('/^[0-9]+$/', $value); }
	protected function predicateNaturalPositive($value){ return (bool) preg_match('/^[0-9]+$/', $value) && $value > 0; }
	protected function predicateNot($value){ return (is_array($this->meetNot)) ? !in_array($value, $this->meetNot) : $value != $this->meetNot; }
	protected function predicateNull($value){ return is_null($value); }
	protected function predicateNumeric($value){ return is_numeric($value); }
	protected function predicateObject($value){ return ($this->meetObject === true) ? is_object($value) : is_object($value) && $value instanceof $this->meetObject; }
	protected function predicatePhone($value){ if ($this->meetPhone == 'strict') return (bool) preg_match('/^\([0-9]{3}\)\s?[0-9]{3}-[0-9]{4}$/', $value); else return (strlen(preg_replace('/[^0-9]/', '', $value)) >= 10); }
	protected function predicateString($value){ return is_string($value); }
	protected function predicateTime($value){ return (bool) preg_match('/^(\s([0-1][0-9]|2[0-4]):([0-5][0-9]):([0-5][0-9]))$/', $value);  }
	protected function predicateURL($value){ return filter_var($value, FILTER_VALIDATE_URL); }	
	
	public function getName(){
		
		return $this->name;	
		
	}
	
	public function shareData(Contract_Term $child){
		
		return (in_array($child, $this->children)) ? $this->data : null;
		
	}
	
}

?>
