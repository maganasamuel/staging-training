<?php
/**
@name: Session.helper.php
@author: Gio
@desc:
	handles the session of the system
*/
class SessionHelper {
	
	/**
		@desc: initialized the file by checking t he current session
			let the page session expire after 30 minutes of inactivity.
	*/
	function __construct () {
		if (!isset($_SESSION)) session_start();
		
		if ($this->isSessionActive()) {
			if (time() - $_SESSION["activity"] > 3600) {// in seconds
				//session time out
				$this->destroySession();
			}
			else {
				//just update the activity time
				$_SESSION["activity"] = time();
			}
		}
	}
	
	/**
		@desc: creates user session upon login
	*/
	public function createSession (
		$data = "" // array of data to be stored in the _SESSION variable
	) {
		if (!is_array($data)) {
			return false;
		}
		
		$user = $data;
		
		$_SESSION["activity"] = time();
		
		//generate id
		session_regenerate_id(true);
		
		$this->saveSession($user);
		
		return true;
	}
	
	/**
		@desc: checks whether there's an existing user session running
		@return: true if yes; otherwise false;
	*/
	public function isSessionActive () {
		return (isset($_SESSION["email_address"]) && isset($_SESSION["id_user"]));
	}
	
	/**
		@desc: destroys all session and unset session key/value pair
	*/
	public function destroySession () {
		unset($_SESSION);
		session_destroy();
	}
	
	public function saveSession (
		$user // user data array to be saved
	) {
		//credentials
		$idUser = $user["id_user"];
		$emailAddress = $user["email_address"];
		$firstName = $user["first_name"];
		$lastName = $user["last_name"];
		$dateRegistered = $user["date_registered"];
		$idUserType = $user["id_user_type"];
		$userType = $user["user_type"];

		$_SESSION["id_user"] = $idUser;
		$_SESSION["email_address"] = $emailAddress;
		$_SESSION["first_name"] = $firstName;
		$_SESSION["last_name"] = $lastName;
		$_SESSION["date_registered"] = $dateRegistered;
		$_SESSION["id_user_type"] = $idUserType;
		$_SESSION["user_type"] = $userType;
	}
	
	
	
	/**
		customized functions
	*/
	
	/**
		@desc: saves a temporary session of examinees
	*/
	public function createTemporarySession (
		$data = ""
	) {
		if (!is_array($data)) {
			return false;
		}
		
		$user = $data[0];
		
		$_SESSION["activity"] = time();
		
		//generate id
		session_regenerate_id(true);
		
		$this->saveSession($user);
		
		return true;
	}
	
	/**
		@desc: retrieves specific SESSION record
	*/
	public function get (
		$key = "" // session key to be fetched
	) {
		if (isset($_SESSION[$key])) {
			return $_SESSION[$key];
		}
		return "";
	}
}

?>