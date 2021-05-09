<?php

/**
@name: User.controller.php
@author: Gio
@desc:
	Handles the manipulation of User data
 */

require_once 'package/vendor/autoload.php';

include_once("class/DB.class.php");

class UserController extends DB
{
	/**
        @desc: Init class
	 */
	public function __construct()
	{
		// init API
		parent::__construct();
	}

	/**
		@desc: Update the ta_password a.k.a referral code
	 */
	public function updatePassword(
		$newPassword, // new password to save
		$idPassword // the id of the password to replace
	) {
		//prepare/execute
		$query = "call ad_update_referral_code (?, ?)";
		$statement = $this->prepare($query);
		$statement->bind_param(
			"si",
			$newPassword,
			$idPassword
		);
		$dataset = $this->execute($statement);

		return $dataset;
	}

	/**
		@desc: get the referral code/password of the specified user type
	 */
	public function getReferralCode(
		$idUserType = 0 // type of user requesting the password
	) {
		$query = "call ad_referral_code (?)";
		$statement = $this->prepare($query);
		$statement->bind_param("i", $idUserType);
		$dataset = $this->execute($statement);

		return $dataset;
	}

	/**
		@desc: Fetch all trainer record
	 */
	public function getTrainerAll(
		$keyword = "" // used for searching for a specific trainer
	) {
		$keyword = $keyword . "%";
		$query = "call ad_trainer_all (?)";
		$statement = $this->prepare($query);
		$statement->bind_param("s", $keyword);
		$dataset = $this->execute($statement);

		return $dataset;
	}

	/**
		@desc: Fetch specific trainer record
	 */
	public function getTrainerSpecific(
		$idUser = 0 // id of the trainer that will be fetched
	) {
		$query = "call ad_trainer_specific (?)";
		$statement = $this->prepare($query);
		$statement->bind_param("i", $idUser);
		$dataset = $this->execute($statement);

		return $dataset;
	}

	/**
		@desc: delete a specific trainer
	 */
	public function deleteTrainer(
		$idUser = 0 // id of the trainer that will be deleted
	) {
		if ($idUser == 1) {
			return array();
		}
		$query = "call ad_trainer_delete (?)";
		$statement = $this->prepare($query);
		$statement->bind_param("i", $idUser);
		$dataset = $this->execute($statement);

		return $dataset;
	}

	/**
		@desc: add/register new trainer record
	 */
	public function addTrainer(
		$firstName = "", // trainer's first name
		$lastName = "", // last name
		$emailAddress = "", // email address
		$password = "", // password
		$accessibleTestSets = ""
	) {
		$firstName = $this->clean($firstName);
		$lastName = $this->clean($lastName);
		$emailAddress = $this->clean($emailAddress);
		$password = $this->clean($password);

		// make sure that the user type is 3. Please refer to the database
		// and select * from ta_user_type for the complete list of user type
		$idUserType = 3;

		//Check if existing email address 
		$query = "SELECT 1=1 FROM ta_user
		WHERE ta_user.email_address = '$emailAddress' AND
			ta_user.first_name = '$firstName' AND
			ta_user.last_name = '$lastName' AND
			ta_user.id_user_type = $idUserType";
			
		$statement = $this->prepare($query);
		$dataset = $this->execute($statement);

		if ($dataset->num_rows > 0) {
			$query = "SELECT 
			ta_user.id_user,
            ta_user.email_address,
			ta_user.first_name,
            ta_user.last_name,
			ta_user.id_user_type
		FROM ta_user
		WHERE ta_user.email_address ='$emailAddress' AND
			ta_user.first_name = '$firstName' AND
			ta_user.last_name = '$lastName' AND
			ta_user.id_user_type = $idUserType";
			$statement = $this->prepare($query);
			$dataset = $this->execute($statement);
		}
		else{
			$query = "
			INSERT INTO ta_user (
				email_address,
				password,
				first_name,
				last_name,
				id_user_type
			)
			VALUES (
				'$emailAddress',
				'$password',
				'$firstName',
				'$lastName',
				$idUserType
			);";
			$statement = $this->prepare($query);
			$dataset = $this->execute($statement);
			$insert_id = $this->mysqli->insert_id;
			
			foreach($accessibleTestSets as $testSet){
				$this->registerTrainerTestSetAccess($insert_id, $testSet);
			}

			$dataset = $this->getTrainerSpecific($insert_id);
		}

		return $dataset;
	}
	
	/**
		@desc: update trainer record
	 */
	public function updateTrainer(
		$idUser = 0, // id of the trainer to be updated
		$firstName = "", // new/current first name
		$lastName = "", // new/current last name
		$emailAddress = "", // new/current email address
		$password = "", //  new/current password
		$accessibleTestSets = ""
	) {
		$query = "call ad_user_update (?, ?, ?, ?, ?)";
		$statement = $this->prepare($query);
		$statement->bind_param(
			"issss",
			$idUser,
			$emailAddress,
			$password,
			$firstName,
			$lastName
		);
		$dataset = $this->execute($statement);

		$this->revokeAllTrainerTestSetAccess($idUser);
		
		foreach($accessibleTestSets as $testSet){
			$this->registerTrainerTestSetAccess($idUser, $testSet);
		}

		$dataset = $this->getTrainerSpecific($idUser);

		return $dataset;
	}
	
	/**
		@desc: update trainer record
	 */
	public function registerTrainerTestSetAccess(
		$idUser = 0, // id of the trainer to be give access to
		$idSet = 0 // id of the test set to be allowed access to
	) {
		$query = "INSERT INTO ta_trainer_test_set_access (user_id, set_id) VALUES ($idUser, $idSet)";
		$statement = $this->prepare($query);
		$dataset = $this->execute($statement);

		return $dataset;
	}

	/**
		@desc: Fetch specific trainer record
	*/
	public function getTrainerTestSetAccess (
		$idUser = 0 // id of the trainer that will be fetched
	) {
        $query = "SELECT s.id_set, s.set_name FROM ta_user u LEFT JOIN ta_trainer_test_set_access a ON u.id_user = a.user_id LEFT JOIN ta_set s ON s.id_set = a.set_id WHERE u.id_user = $idUser order by s.set_name";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

		return collect($dataset)->all();
	}

	
	/**
		@desc: update trainer record
	 */
	public function revokeAllTrainerTestSetAccess(
		$idUser = 0 // id of the trainer to be remove access from
	) {
		$query = "DELETE FROM ta_trainer_test_set_access WHERE user_id = $idUser";
		$statement = $this->prepare($query);
		$dataset = $this->execute($statement);

		return $dataset;
	}
}
