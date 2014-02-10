<?php require_once('../Contract.php');

$filter = new User_Filter();
$filter->test();

class User_Filter {
	
	public function test(){
		
		$usersData = self::getUsersData();
		
		$usersDataFilteredByStateCT = $this->filterByState($usersData, 'CT');
		print_r($usersDataFilteredByStateCT);
		
		$usersDataFilteredByHasMultipleOptions = $this->filterByHasMultipleOptions($usersData);
		print_r($usersDataFilteredByHasMultipleOptions);
		
		$usersDataFilteredByValidUser = $this->filterByValidUser($usersData);
		print_r($usersDataFilteredByValidUser);
		
	}

	public function filterByHasMultipleOptions($usersData){
	
		/* Before filtering the data, establish the agreement for the method parameters. */
		$contract = new Contract();
		$contract->term('usersData')->arraylist();
		$contract->metOrThrow();
		
		/* Users data must be an array of elements, each element an array itself, with an options array that has two or more elements. */
		$contract->term('usersData')->elements()
						->arraylist()
						->allowed('*')
						->element('options')->arraylist()->count(2, '*');
		
		$filteredData = $contract->term('usersData')->data();
		
		return $filteredData;
		
	}
	
	public function filterByState($usersData, $state){
	
		/* Before filtering the data, establish the agreement for the method parameters. */
		$contract = new Contract();
		$contract->term('usersData')->arraylist();
		$contract->term('state')->length(2)->in(self::getStates(true));
		$contract->metOrThrow();
		
		/* Users data must be an array of elements, each element an array itself, with a state element equaling $state.
		   Allowed fields to be returned are name, address, city, state, and zip. */
		$contract->term('usersData')->elements()
						->arraylist()
							->allowed(array('name', 'address', 'city', 'state', 'zip'))
							->element('state')->equals($state)->end();
		
		$filteredData = $contract->term('usersData')->data();
		
		return $filteredData;
		
	}
	
	public function filterByValidUser($usersData){
	
		/* Before filtering the data, establish the agreement for the method parameters. */
		$contract = new Contract();
		$contract->term('usersData')->arraylist();
		$contract->metOrThrow();
		
		/* Users data must be an array of elements, each element an array itself, with an id, name, registered date, and active boolean true.
		   Allowed fields to be returned are the defined elements: id, name, registered, active. */
		$contract->term('usersData')->elements()
						->arraylist()
							->element('id')->id()->end()
							->element('name')->optional()->alpha()->end()
							->element('registered')->datetime()->end()
							->element('active')->boolean()->equals(true)->end();
		
		$filteredData = $contract->term('usersData')->data();
		
		return $filteredData;
		
	}
		
	public static function getStates($abbreviationIsKey = false){
		
		$states = array(
			'AL' => 'Alabama',  
			'AK' => 'Alaska',  
			'AZ' => 'Arizona',  
			'AR' => 'Arkansas',  
			'CA' => 'California',  
			'CO' => 'Colorado',  
			'CT' => 'Connecticut',  
			'DE' => 'Delaware',  
			'DC' => 'District Of Columbia',  
			'FL' => 'Florida',  
			'GA' => 'Georgia',  
			'HI' => 'Hawaii',  
			'ID' => 'Idaho',  
			'IL' => 'Illinois',  
			'IN' => 'Indiana',  
			'IA' => 'Iowa',  
			'KS' => 'Kansas',  
			'KY' => 'Kentucky',  
			'LA' => 'Louisiana',  
			'ME' => 'Maine',  
			'MD' => 'Maryland',  
			'MA' => 'Massachusetts',  
			'MI' => 'Michigan',  
			'MN' => 'Minnesota',  
			'MS' => 'Mississippi',  
			'MO' => 'Missouri',  
			'MT' => 'Montana',
			'NE' => 'Nebraska',
			'NV' => 'Nevada',
			'NH' => 'New Hampshire',
			'NJ' => 'New Jersey',
			'NM' => 'New Mexico',
			'NY' => 'New York',
			'NC' => 'North Carolina',
			'ND' => 'North Dakota',
			'OH' => 'Ohio',  
			'OK' => 'Oklahoma',  
			'OR' => 'Oregon',  
			'PA' => 'Pennsylvania',  
			'RI' => 'Rhode Island',  
			'SC' => 'South Carolina',  
			'SD' => 'South Dakota',
			'TN' => 'Tennessee',  
			'TX' => 'Texas',  
			'UT' => 'Utah',  
			'VT' => 'Vermont',  
			'VA' => 'Virginia',  
			'WA' => 'Washington',  
			'WV' => 'West Virginia',  
			'WI' => 'Wisconsin',  
			'WY' => 'Wyoming'
		);
		
		if ($abbreviationIsKey) $states = array_flip($states);
		
		return $states;
		
	}
	
	public static function getUsersData(){
		
		$usersData = array(
			array(
				'id'			=> 1,
				'name'			=> 'John Smith',
				'address'		=> '123 Main Street',
				'city'			=> 'Hartford',
				'state'			=> 'CT',
				'zip'			=> '34678',
				'options'		=> array(
					'remember'		=> true,
					'duration_length'	=> 2,
					'duration_unit'		=> 'week'
				),
				'logged'		=> '2014-02-06 12:00:00',
				'registered'		=> '2012-01-01 12:00:00',
				'active'		=> true
			),
			array(
				'id'			=> 2,
				'name'			=> 'Jane Smith',
				'address'		=> '345 Main Street',
				'city'			=> 'Boston',
				'state'			=> 'MA',
				'zip'			=> '01243',
				'options'		=> array(
					'remember'		=> false
				),
				'logged'		=> '2014-03-01 12:00:00',
				'registered'		=> '2012-07-01 12:00:00',
				'active'		=> true
			),
			array(
				'id'			=> null,
				'name'			=> 'Paul Smith',
				'address'		=> '768 First Street',
				'city'			=> 'Providence',
				'state'			=> 'RI',
				'zip'			=> '03654',
				'options'		=> array(
					'remember'		=> null
				),
				'logged'		=> null,
				'registered'		=> '2012-05-01 12:00:00',
				'active'		=> false
			)
		);
		
		return $usersData;
		
	}

}

?>
