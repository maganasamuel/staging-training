<?php
/**
@name: Test.helper.php
@author: Gio
@desc:
	Serves as the API of the test form and test checker page
	This page handles all asynchronous javascript request from the above mentioned page
@returnType:
	JSON
*/

if($_SERVER['SERVER_NAME'] == 'onlineinsure.co.nz'){
	$autoloadPath = $_SERVER['DOCUMENT_ROOT'] . '/staging/staging-training/package/vendor/autoload.php';
}else{
	$autoloadPath = $_SERVER['DOCUMENT_ROOT'] . '/staging-training/package/vendor/autoload.php';
};

require_once $autoloadPath;

include_once("class/DB.class.php");

class TrainingController extends DB {	
    /**
        @desc: Init class
    */
    public function __construct () {
        // init API
        parent::__construct();
    }

    public function addTraining ($trainer_id="",$training_topic = "", 
		$training_attendee,$training_date = "",$training_venue = "",$attendee_id = "",$trainer_signature=""){

    	$date = date('Y-m-d');

    	$query = "INSERT INTO ta_training (
					trainer_id,
					training_topic,
					training_attendee,
					training_date,
					attendee_signiture,
					trainer_signiture,
					training_venue,
					attendee_id
				)
				VALUES (
					'$trainer_id',
					'$training_topic',
					'$training_attendee',
					'$training_date',
					'',
					'$trainer_signature',
					'$training_venue',
					'$attendee_id'
				)";

			$statement = $this->prepare($query);
			$dataset = $this->execute($statement);
			$insert_id = $this->mysqli->insert_id;
			

    	return $insert_id;

    }

    public function getTraining ($id,$idUserType) {
		//prepare/execute
		$query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

		$query = "SELECT 
					ta_user_training.id_user,
					ta_training.training_id,
					ta_training.training_topic,
					ta_training.training_attendee,
					ta_user_training.full_name as fullname,
					ta_training.training_date
				FROM
					ta_training
					LEFT JOIN ta_user_training
					ON ta_user_training.id_user = ta_training.trainer_id
					";

		if($idUserType != "admin"){
			$query .= "WHERE ta_training.trainer_id = '$id' OR 
					ta_training.training_attendee LIKE '%$id%'";
		}

        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

		return $dataset;
	}

	public function getAdviser(){
		$query = " SELECT 
					*
				FROM
					ta_user_training";

        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

		return $dataset;
	}

	public function getTrainingDetail($id){


		$query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

		$query = "SELECT *
		FROM ta_training
		WHERE training_id = $id";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

		return $dataset;
	}
	public function getAttendee($id){

		$query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

		$query = "SELECT *
		FROM ta_user
		WHERE id_user = $id";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

		return $dataset;

	}
	public function addUserTraining($full_name="",
						$email_address="",
						$password="",
						$ssf_number=0,$user_type=""
					){

    	$query = "INSERT INTO ta_user_training (
					email_address,
					full_name,
					password,
					id_user_type,
					ssf_number
				)
				VALUES (
					'$email_address',
					'$full_name',
					'$password',
					'$user_type',
					'$ssf_number'
				)";

			$statement = $this->prepare($query);
			$dataset = $this->execute($statement);
			$insert_id = $this->mysqli->insert_id;
			

    	return $insert_id;	
	}
	public function trainingLogin($email_address,$password){
		$query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

		$query = "SELECT *
		FROM ta_user_training
		WHERE email_address = '$email_address' and password = '$password'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

		return $dataset;
	}
	public function adminLogin($email_address){
		$query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

		$query = "SELECT *
		FROM ta_user
		WHERE email_address = '$email_address'";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

		return $dataset;
	}
}	

?>