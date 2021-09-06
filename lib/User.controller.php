<?php

/**
 * @name: User.controller.php
 * @author: Gio
 * @desc:
 * Handles the manipulation of User data
 */

require_once realpath(__DIR__ . '/../package/vendor/autoload.php');

include_once('class/DB.class.php');

class UserController extends DB
{
    /**
     * @desc: Init class
     */
    public function __construct()
    {
        // init API
        parent::__construct();
    }

    /**
     * @desc: Update the ta_password a.k.a referral code
     * @param mixed $newPassword
     * @param mixed $idPassword
     */
    public function updatePassword(
        $newPassword, // new password to save
        $idPassword // the id of the password to replace
    ) {
        //prepare/execute
        $query = 'UPDATE ta_password
			SET ta_password.password = ?
		WHERE ta_password.id_password = ?';
        $statement = $this->prepare($query);
        $statement->bind_param(
            'si',
            $newPassword,
            $idPassword
        );
        $dataset = $this->execute($statement);

        $query = "SELECT 'New password saved.' message";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    /**
     * @desc: get the referral code/password of the specified user type
     * @param mixed $idUserType
     */
    public function getReferralCode(
        $idUserType = 0 // type of user requesting the password
    ) {
        $query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

        $query = 'SELECT
			ta_password.id_password,
			ta_password.password,
			ta_password.id_user_type,
			ta_user_type.user_type
		FROM
			ta_password
			LEFT JOIN ta_user_type ON ta_password.id_user_type = ta_user_type.id_user_type
		WHERE (
				? > 0 AND
				ta_password.id_user_type = ?
			) OR (
				? < 0 AND
				ta_password.id_user_type > 0
			)';
        $statement = $this->prepare($query);
        $statement->bind_param('iii', $idUserType, $idUserType, $idUserType);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    /**
     * @desc: Fetch all trainer record
     * @param mixed $keyword
     */
    public function getTrainerAll(
        $keyword = '' // used for searching for a specific trainer
    ) {
        $keyword = $keyword . '%';
        $query = "SELECT
			ta_user.id_user,
			ta_user.email_address,
			ta_user.password,
			ta_user.first_name,
			ta_user.last_name,
			ta_user.id_user_type,
			DATE_FORMAT(DATE(ta_user.date_registered), '%d/%b/%Y') date_registered,
			ta_user_type.user_type
		FROM
			ta_user
			LEFT JOIN ta_user_type
				ON ta_user.id_user_type = ta_user_type.id_user_type
		WHERE
			ta_user.id_user_type = 3 AND (
				ta_user.first_name LIKE ? OR
				ta_user.last_name LIKE ? OR
				ta_user.email_address LIKE ?
			)
		ORDER BY ta_user.id_user DESC";

        $statement = $this->prepare($query);
        $statement->bind_param('sss', $keyword, $keyword, $keyword);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    /**
     * @desc: Fetch specific trainer record
     * @param mixed $idUser
     */
    public function getTrainerSpecific(
        $idUser = 0 // id of the trainer that will be fetched
    ) {
        $query = "SELECT
			ta_user.id_user,
			ta_user.email_address,
			ta_user.password,
			ta_user.first_name,
			ta_user.last_name,
			ta_user.id_user_type,
			DATE_FORMAT(DATE(ta_user.date_registered), '%d/%b/%Y') date_registered,
			ta_user_type.user_type
		FROM
			ta_user
			LEFT JOIN ta_user_type
				ON ta_user.id_user_type = ta_user_type.id_user_type
					
		WHERE
			ta_user.id_user_type = 3 AND 
			ta_user.id_user = ?
		ORDER BY ta_user.id_user DESC";

        $statement = $this->prepare($query);
        $statement->bind_param('i', $idUser);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    /**
     * @desc: delete a specific trainer
     * @param mixed $idUser
     */
    public function deleteTrainer(
        $idUser = 0 // id of the trainer that will be deleted
    ) {
        if (1 == $idUser) {
            return [];
        }
        $query = 'DELETE FROM ta_user
			WHERE ta_user.id_user = ? AND
			ta_user.id_user_type = 3';
        $statement = $this->prepare($query);
        $statement->bind_param('i', $idUser);
        $this->execute($statement);

        $query = "SELECT 'record deleted' message";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    /**
     * @desc: add/register new trainer record
     * @param mixed $firstName
     * @param mixed $lastName
     * @param mixed $emailAddress
     * @param mixed $password
     * @param mixed $accessibleTestSets
     */
    public function addTrainer(
        $firstName = '', // trainer's first name
        $lastName = '', // last name
        $emailAddress = '', // email address
        $password = '', // password
        $accessibleTestSets = ''
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
        } else {
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

            foreach ($accessibleTestSets as $testSet) {
                $this->registerTrainerTestSetAccess($insert_id, $testSet);
            }

            $dataset = $this->getTrainerSpecific($insert_id);
        }

        return $dataset;
    }

    /**
     * @desc: update trainer record
     * @param mixed $idUser
     * @param mixed $firstName
     * @param mixed $lastName
     * @param mixed $emailAddress
     * @param mixed $password
     * @param mixed $accessibleTestSets
     */
    public function updateTrainer(
        $idUser = 0, // id of the trainer to be updated
        $firstName = '', // new/current first name
        $lastName = '', // new/current last name
        $emailAddress = '', // new/current email address
        $password = '', //  new/current password
        $accessibleTestSets = ''
    ) {
        $query = 'UPDATE ta_user
			SET
				ta_user.email_address = ?,
				ta_user.password = ?,
				ta_user.first_name = ?,
				ta_user.last_name = ?
			WHERE
				ta_user.id_user = ?';
        $statement = $this->prepare($query);
        $statement->bind_param(
            'ssssi',
            $emailAddress,
            $password,
            $firstName,
            $lastName,
            $idUser
        );
        $dataset = $this->execute($statement);

        $this->revokeAllTrainerTestSetAccess($idUser);

        foreach ($accessibleTestSets as $testSet) {
            $this->registerTrainerTestSetAccess($idUser, $testSet);
        }

        $dataset = $this->getTrainerSpecific($idUser);

        return $dataset;
    }

    /**
     * @desc: update trainer record
     * @param mixed $idUser
     * @param mixed $idSet
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
     * @desc: Fetch specific trainer record
     * @param mixed $idUser
     */
    public function getTrainerTestSetAccess(
        $idUser = 0 // id of the trainer that will be fetched
    ) {
        $query = "SELECT s.id_set, s.set_name FROM ta_user u LEFT JOIN ta_trainer_test_set_access a ON u.id_user = a.user_id LEFT JOIN ta_set s ON s.id_set = a.set_id WHERE u.id_user = $idUser order by s.set_name";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return collect($dataset)->all();
    }

    /**
     * @desc: update trainer record
     * @param mixed $idUser
     */
    public function revokeAllTrainerTestSetAccess(
        $idUser = 0 // id of the trainer to be remove access from
    ) {
        $query = "DELETE FROM ta_trainer_test_set_access WHERE user_id = $idUser";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

        return $dataset;
    }

    public function createToken($id){
        $token = md5($id . time());

        $this->execute($this->prepare('UPDATE ta_user SET access_token = "' . $token . '" WHERE id_user = ' . $id));

        return $token;
    }    
}
