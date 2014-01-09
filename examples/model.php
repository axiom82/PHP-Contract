<?php

class User_Model {
	
	public function createUser($userId, $userData){
		
		/* Define Contract Requirements for Model Data */
		$contract = new Site_Contract();
		$contract->term('userId')->id();
		$contract->term('userData')->arraylist()
		                           ->element('type')->in(array('member','administrator'))->end()
		                           ->element('username')->alphaNumeric()->length(8, 16)->end()
		                           ->element('name')->required()->end()
		                           ->element('address')->required()->end()
		                           ->element('city')->required()->end()
		                           ->element('state')->length(2)->end()
		                           ->element('zip')->length(5,10)->end()
		                           ->element('country')->required()->end()
		                           ->element('email')->email()->end()
		                           ->element('phone')->phone()->end()
		                           ->element('fax')->optional()->phone()->end()
		                           ->element('photo')->optional()->file()->end()
		                           ->element('website')->optional()->url()->end()
		                           ->element('registered')->datetime()->end()
		                           ->element('active')->boolean()->end();
		$contract->metOrThrow();
  		
		/* Follow w/ Basic MySQL Query */
		$rows = array();
  	
		/* $select = "SELECT * FROM user WHERE id = {$userId}";
		while($row = mysql_query($select)) $rows[] = $row; */
		
		return $rows;
  
	}
	
?>
