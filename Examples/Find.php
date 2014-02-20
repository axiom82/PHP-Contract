<?php require_once('../Contract.php');

class Find {
	
	public function __construct(){
		
		$data = array(
			'outer' => array(
				'inner' => array(
					'number' => 1,
					'letter' => 'A'
				),
				'inner2' => array(
					'email' => 'user@email.com'
				),
			)
		);
		
		/* Run the tests */
		$this->children($data);
		$this->parents($data);
		
	}
	
	/* Direct access for defining child terms found anywhere in multi-dimensional arrays */
	public function children($data){

		$contract = new Contract();
		$contract->term('data/outer/inner')->arraylist()
										   ->element('number')->integer()->end()
										   ->element('letter')->alpha()->end();
		$contract->metOrThrow();
		
	}	
	
	/* Direct access for defining parent terms from anywhere in multi-dimensional arrays */
	public function parents($data){

		$contract = new Contract();
		$contract->term('data')->arraylist()
							   ->element('outer')->arraylist()
							   					 ->element('inner')->arraylist()
																   ->element('number')->integer()->parent()
																   ->element('letter')->alpha()->parent('outer')	/* Direct access to 'outer' element */
							   					 ->element('inner2')->arraylist()
												 					->element('email')->email()->parent(TRUE) 		/* Direct access to the contract super object */	
				 ->metOrThrow();

	}
	
}

?>