<?php
/**
@name: Login.controller.php
@author: Gio
@desc:
	Handles the login request
*/

include_once("class/DB.class.php");

class LoginController extends DB {	
    /**
        @desc: Init class
    */
    public function __construct () {
        // init API
        parent::__construct();
    }
	
    /**
        @desc: handles the login post request
    */
	public function userLogin (
		$emailAddress, //email address of the user
		$password // password
	) {
		//variables
		$emailAddress = $this->clean($emailAddress);
		$password = $this->clean($password);
		
		//prepare/execute
        $query = "call ad_user_login(?, ?);";
        $statement = $this->prepare($query);
        $statement->bind_param("ss", 
					$emailAddress, $password);
        $dataset = $this->execute($statement);
		//error_log("call ad_user_login('{$emailAddress}','{$password}')", 0);
		
		$data["message"] = "Invalid email address or password";
		
        if ($dataset->num_rows > 0) {
			unset($data);
            while ($row = $dataset->fetch_assoc()) {
				$data[] = $row;
            }
        }
		return $data;
	}
}
?>