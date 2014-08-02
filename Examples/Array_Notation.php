<?php require_once('../Contract.php');

$notation = new Array_Notation();
$notation->test();

class Array_Notation {
	
	public function test(){
	
		$this->testA('2053', 'John Smith');
		$this->testB();
		
	}
	
	/* Validating Class Method Parameters In Array Notation */
	public function testA($userId, $userName){
	
		$contract = new Contract(array(
			'userId' => 'id',
			'userName' => array('optional', 'alphaNumeric', 'length' => array(8,12))
		));
		$contract->metOrThrow();
		
	}
	
	
	/* Validating Local Variables In Array Notation */
	public function testB(){
	
		$testValue1 = 100;
		$testValue2 = 'red';
		$testValue3 = array('name' => 'John Smith', 'age' => 30);
		
		$contract = new Contract(array(
			'test1' => array('data' => $testValue1, 'definition' => 'integer'),
			'test2' => array('data' => $testValue2, 'definition' => array('length' => 3, 'in' => array('red', 'green', 'blue'))),
			'test3' => array('data' => $testValue3, 'definition' => array('arraylist', 'element' => array('age' => array('integer', 'lessThan' => 29), 'name' => array('alpha', 'in' => array('John Doe', 'Jane Smith')))))
		));
		$contract->metOrThrow();
		
	}

}
