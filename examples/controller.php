<?php

class User_Controller {

	public function login(){
	
		if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		
			/* Hypothetical Data */
			$loginUserEmail = $_REQUEST['email'];
			$loginUserPass = $_REQUEST['pass'];
			$loginUserIp = $_SERVER['REMOTE_ADDR'];
			$loginDateTime = date('Y-m-d H:i:s');
			
			try {
			
				$contract = new Contract();
				$contract->term('userEmail', $loginUserEmail)->email()->metOrThrow();
				$contract->term('userPass', $loginUserPass)->alphaNumeric()->length(8,16)->metOrThrow();
				$contract->term('userIp', $loginUserPass)->ip()->metOrThrow();
				$contract->term('dateTime', $loginDateTime)->datetime()->metOrThrow();
				
				/* Get User For Login */
				$user = $userModel->getUser($userEmail, $userPass);
				$contract->term('user', $user)->arraylist()
							      ->element('id')->id()->end()
							      ->element('active')->equals(1)->end()
							      ->metOrThrow();
				$loginUserId = $user['id'];
				
				/* Proceed Safely to Model for Storage of User Login */
				$logged = $userModel->login($loginUserId, $loginUserIp, $loginDateTime);
				$contract->term('userLogged', $logged)->boolean()->equals(TRUE)->metOrThrow();
				
			}
			catch (Contract_Exception $e){
			
				/* Collect error messages from contract exception */
				$messages = array();
				switch ($e->term){
				
					case 'userEmail':  $messages[] = 'Please enter an email address.'; break;
					case 'userPass':   $messages[] = 'Please enter a password.'; break;
					case 'userIp':     $messages[] = 'Please enter a valid ip address.'; break;
					case 'dateTime':   $messages[] = 'Please enter a valid date time.'; break;
					case 'user':       $messages[] = 'Please enter a valid user.'; break;
					case 'userLogged': $messages[] = 'Sorry. You could not be logged in.'; break;
					default:           $messages[] = 'We do not get it either!'; break;
				
				}
			
			}
		
		}
	
	}

}

?>
