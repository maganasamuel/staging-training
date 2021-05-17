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
        $query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

        $query = "SELECT
                ta_user.id_user,
                ta_user.email_address,
                ta_user.password,
                ta_user.first_name,
                ta_user.last_name,
                ta_user.date_registered,
                ta_user.id_user_type,
                ta_user_type.user_type
            FROM
                ta_user
                LEFT JOIN ta_user_type
                    ON ta_user.id_user_type = ta_user_type.id_user_type
            WHERE
                ta_user.email_address = ? AND
                ta_user.password = ?
            ";
        $statement = $this->prepare($query);
        $statement->bind_param('ss', $emailAddress, $password);
        $dataset = $this->execute($statement);

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