PHP-Contract
============

A PHP library designed to ensure the integrity of values passed to class method parameters. 


Example of Chaining Contract Terms and Rules
----------------------------------------------------------

<pre>
class Model {

	public function getFoos($barId, $includeBaz = false, $limit = 0, $offset = 0){
	
		$contract = new Contract();
		$contract->term('barId')->id()->end()
				 ->term('includeBaz')->boolean()->end()
				 ->term('limit')->natural()->end()
				 ->term('offset')->natural()->end()
				 ->metOrThrow();
			 
		/* Continue with peace of mind ... */

	}
	
}
</pre>


Documentation of PHP-Contract Functionality
-------------------------------------------
(For detailed usage examples, see https://github.com/axiom82/PHP-Contract/tree/master/Examples)


<pre>

class MyClass {

	public function myMethod($arg){

		$contract = new Contract();
		
		/* Defining Terms For Method Arguments and Local Variables in Method Scope ...
		   Note: arguments are already created as terms for you, local variables must be created manually (see below) */
		
		$contract->term('arg')->allowed($array); /* The term may be an array containing the specified fields (other fields filtered out, see data();) */
		$contract->term('arg')->alone(); /* The term must be alone, having no siblings */
		$contract->term('arg')->alpha(); /* The term must be an alphabetical string */
		$contract->term('arg')->alphaNumeric(); /* The term must be an alplanumeric string */
		$contract->term('arg')->alphaDash(); /* The term must be an alphanumeric allowing dashes */
		$contract->term('arg')->alphaUnderscore(); /* The term must be an alphanumeric allowing unscores */
		$contract->term('arg')->arraylist(); /* The term must be an array */
		$contract->term('arg')->base64(); /* The term must be a base64 string */
		$contract->term('arg')->boolean(); /* The term must be a boolean */
		$contract->term('arg')->count($value); /* The term must be the count of the value (for arrays) */
		$contract->term('arg')->decimal(); /* The term must be a decimal */
		$contract->term('arg')->earlier(); /* The term must be earlier than the value */
		$contract->term('arg')->email(); /* The term must be an email address */
		$contract->term('arg')->equals($value); /* The term must match the value */
		$contract->term('arg')->greaterThan($value); /* The term must be greater than the value */
		$contract->term('arg')->id(); /* The term must be an id (a natural positive number) */
		$contract->term('arg')->in($value); /* The term must be in the values of the array */
		$contract->term('arg')->integer(); /* The term must be an integer */
		$contract->term('arg')->ip(); /* The term must be an ip address */
		$contract->term('arg')->later(); /* The term must be later than the value */
		$contract->term('arg')->length($value); /* The term must be the length of the value */
		$contract->term('arg')->lessThan($value); /* The term must be less than the value */
		$contract->term('arg')->many(); /* The term must be an array with more than one element */
		$contract->term('arg')->natural(); /* The term must be a natural number */
		$contract->term('arg')->naturalPositive(); /* The term must be a natural positive number */
		$contract->term('arg')->none($value); /* The term must be an empty value or values */
		$contract->term('arg')->not($value); /* The term must not be equal to the value or values */
		$contract->term('arg')->null(); /* The term must be null */
		$contract->term('arg')->numeric(); /* The term must be numeric */
		$contract->term('arg')->object($value); /* The term must be an object that is an instance of the value */
		$contract->term('arg')->one(); /* The term must be an array with one and only one element */
		$contract->term('arg')->optional(); /* The term is not required */
		$contract->term('arg')->phone(); /* The term must be a phone number */
		$contract->term('arg')->required(); /* The term must be non-empty */
		$contract->term('arg')->required($array); /* The term must be an array with the specific fields */
		$contract->term('arg')->string(); /* The term must be a string */
		$contract->term('arg')->url(); /* The term must be URL */
		$contract->term('arg')->withData(); /* The term, after the contract filters out invalid data,
		                                       must have one or more valid values */
		
		/* Defining Terms for Local Variables in Method Scope */
		$contract->term('id', $idVar)->id();
		$contract->term('email', $emailVar)->email();
		$contract->term('password', $passVar)->alphaNumeric()->length(8, 16);

		/* Validation */
		$met = $contract->term('arg')->met(); /* The contract term has a met() method that checks to see if
		                                         the term met its own rules, it does so and then returns a 
		                                         boolean for success or failure */
		if (!$met) return false; /* You may choose to return false when the term has not been met */
		
		$met = $contract->met(); /* The contract checks all of its child terms through its met() method,
		                            which calls each contract term's met() method,
		                            and collects the results */
		$contract->metOrThrow(); /* Most useful I think.  Equivalent to met(), however, throws an exception
		                            halting the program unless caught. Terms also have their own individual
		                            metOrThrow() method, in case you want to test line by line per term. */
		
		/* Post Validation, Obtaining Filtered Data */
		$argData = $contract->term('arg')->data(); /* Returns the term's value(s) as per the contract.
		                                              Indeed, the contract presents through its data() method
		                                              only the data that meets the contract term rules. If
		                                              allowed() is used (see above), data() will return the
		                                              allowed value(s) from the original value(s) in the
		                                              argument */
							      
		$argData = $contract->data('arg'); /* This is equivalent in functionality to the line above, however,
		                                      this method is cleaner in appearance.  The contract proxies to
		                                      the term and gets the data via the term's data() method. */

		/* Debugging: "Which term(s) did not meet the contract?" */
		$contract->debug();
		
		/* Or, return the array into your own variable */
		$debug = $contract->debug(true);
		
		/* Print and review the full definition of the contract */
		echo $contract; /* Prints a clean and readable text describing the contract and its terms */
		
	}
	
}
</pre>
