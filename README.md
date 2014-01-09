PHP-Contract
============

A PHP library designed to ensure the integrity of values passed to class method parameters. 



Documentation of PHP-Contract Functionality (For detailed usage examples, see PHP-Contract/examples)
===========================================

<?php

class MyClass {

    public function myMethod($arg){

        $contract = new Contract();
		
		/* Defining Terms (e.g. Method Arguments, Local Variables in Method ) ... arguments are already terms, local variables are not */
        $contract->term('arg')->allowed($array); /* The term may be an array containing the specified fields (other fields filtered out, see data();) */
	    $contract->term('arg')->alpha(); /* The term must be an alphabetical string */
	    $contract->term('arg')->alphaNumeric(); /* The term must be an alplanumeric string */
	    $contract->term('arg')->alphaDash(); /* The term must be an alphanumeric with dash */
	    $contract->term('arg')->array(); /* The term must be an array */
	    $contract->term('arg')->base64(); /* The term must be a base64 string */
	    $contract->term('arg')->boolean(); /* The term must be a boolean */
	    $contract->term('arg')->decimal(); /* The term must be a decimal */
	    $contract->term('arg')->integer(); /* The term must be an integer */
	    $contract->term('arg')->email(); /* The term must be an email address */
	    $contract->term('arg')->equals($value); /* The term must match the value */
	    $contract->term('arg')->greaterThan($value); /* The term must be greater than the value */
	    $contract->term('arg')->id(); /* The term must be an id (a natural positive number) */
	    $contract->term('arg')->ip(); /* The term must be an ip address */
	    $contract->term('arg')->length($value); /* The term must be the length of the value */
	    $contract->term('arg')->lessThan($value); /* The term must be less than the value */
	    $contract->term('arg')->natural(); /* The term must be a natural number */
	    $contract->term('arg')->naturalPositive(); /* The term must be a natural positive number */
	    $contract->term('arg')->numeric(); /* The term must be numeric */
	    $contract->term('arg')->required(); /* The term must be non-empty */
	    $contract->term('arg')->required($array); /* The term must be an array with the specific fields */
	    $contract->term('arg')->url(); /* The term must be URL */
	    $contract->term('arg')->withData(); /* The term must be an array with at least one item, used for editing records  */
		
		/* Defining Terms for Local Variables in Method */
		$contract->term('id', 1)->id();
		$contract->term('email', 'test@mail.com')->email();
		$contract->term('user', 'username123')->alphaNumeric()->length(8, 16);

        /* Validation */
		$met = $contract->term('arg')->met(); /* The contract checks all terms and returns a boolean for success or failure to meet the contract */
		if (!$met) return false; /* The method returns false when the contract for the method has not been met */
		
		$contract->metOrThrow(); /* Equivalent to met(), however, throws an exception halting the program */
		
		/* Post Validation, Obtaining Filtered Data */
		$argData = $contract->term('arg')->data(); /* Returns the term's value(s) as per the contract.  If allowed() is used (see above), data() will return only the allowed value(s) from the original value(s) of the argument */
		
	}
	
}

?>
