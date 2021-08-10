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
	$autoloadPath = $_SERVER['DOCUMENT_ROOT'] . '/package/vendor/autoload.php';
};

require_once $autoloadPath;

include_once("class/DB.class.php");

class TestController extends DB {	
    /**
        @desc: Init class
    */
    public function __construct () {
        // init API
        parent::__construct();
    }
	
	/**
		@desc: fetch referral code of the specified idUserType
	*/
	public function getReferralCode (
		$idUserType = 0 // idUserType of the referral code to be retrieved
	) {
		$query = "SELECT
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
			)";
        $statement = $this->prepare($query);
        $statement->bind_param("iii", $idUserType, $idUserType, $idUserType);
        $dataset = $this->execute($statement);

		return $dataset;
	}
	
	//tester section
	/**
		@desc: add trainee/examinee to the system
	*/
	public function userAdd (
		$emailAddress = "", //email address of the examinee
		$password = "", //referral code used
		$firstName = "", // first name
		$lastName = "", //last name
		$idUserType = 0 // user type /admin(2) or adviser (4)
	) {

		$emailAddress = $this->clean($emailAddress);
		$password = $this->clean($password);
		$firstName = $this->clean($firstName);
		$lastName = $this->clean($lastName);

		//prepare/execute
		$query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

		//Check if existing email address 
		$query = "SELECT 1=1 FROM ta_user
		WHERE ta_user.email_address = '$emailAddress' AND
			ta_user.first_name = '$firstName' AND
			ta_user.last_name = '$lastName' AND
			ta_user.id_user_type = $idUserType";
			
		$statement = $this->prepare($query);
		$dataset = $this->execute($statement);
		
		if ($dataset->num_rows > 0) {
			$query = " SELECT 
				ta_user.id_user,
				ta_user.email_address,
				ta_user.first_name,
				ta_user.last_name,
				ta_user.id_user_type,
				ta_user.date_registered,
				ta_user_type.user_type
			FROM
				ta_user
			LEFT JOIN ta_user_type
			ON ta_user.id_user_type = ta_user_type.id_user_type
			WHERE ta_user.email_address = '$emailAddress' AND
				ta_user.first_name = '$firstName' AND
				ta_user.last_name = '$lastName' AND
				ta_user.id_user_type = $idUserType";
			$statement = $this->prepare($query);
			$dataset = $this->execute($statement);
		} else {
			$status = 1;
			if($idUserType == 2) $status = 0;

			$query = "INSERT INTO ta_user (
					email_address,
					password,
					first_name,
					last_name,
					id_user_type,
					status
				)
				VALUES (
					'$emailAddress',
					'$password',
					'$firstName',
					'$lastName',
					$idUserType,
					$status
				)";

			$statement = $this->prepare($query);
			$dataset = $this->execute($statement);
			$insert_id = $this->mysqli->insert_id;
			
			$dataset = $this->getUserSpecific($insert_id);
		}

		return $dataset;
	}
	
	/**
		@desc: check existing trainee/examinee to the system
	*/
	public function userCheck (
		$emailAddress = "", //email address of the examinee
		$idUserType = 0 // user type 
	) {

		$emailAddress = $this->clean($emailAddress);

		//prepare/execute
		$query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

		//Check if existing email address 
		$query = "SELECT ta_user.*, ta_user_type.user_type FROM ta_user LEFT JOIN ta_user_type ON ta_user.id_user_type = ta_user_type.id_user_type
		WHERE ta_user.email_address = '$emailAddress' AND
			ta_user.id_user_type = $idUserType
		ORDER BY ta_user.date_registered DESC
		LIMIT 0,1";
			
		$statement = $this->prepare($query);
		$dataset = $this->execute($statement);
		return $dataset;
	}

	public function userCheckType (
		$emailAddress = "" //email address of the examinee
	) {

		$emailAddress = $this->clean($emailAddress);

		//prepare/execute
		$query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

		//Check if existing email address 
		$query = "SELECT * FROM ta_user
		WHERE ta_user.email_address = '$emailAddress'
		ORDER BY date_registered DESC
		LIMIT 0,1";
			
		$statement = $this->prepare($query);
		$dataset = $this->execute($statement);
		return $dataset;
	}

	/**
		@desc: Fetch specific trainee/examinee record
	 */
	public function getUserSpecific(
		$idUser = 0 // id of the trainer that will be fetched
	) {
		$query = "SELECT 
			ta_user.id_user,
			ta_user.email_address,
			ta_user.first_name,
			ta_user.last_name,
			ta_user.id_user_type,
			ta_user.date_registered,
			ta_user_type.user_type
		FROM
			ta_user
			LEFT JOIN ta_user_type
				ON ta_user.id_user_type = ta_user_type.id_user_type
		WHERE ta_user.id_user = ?";

		$statement = $this->prepare($query);
		$statement->bind_param("i", $idUser);
		$dataset = $this->execute($statement);

		return $dataset;
	}
	
	/**
		@desc: Fetch all question sets depending on the userType
	*/
	public function getSetAll (
		$idUserType = 0 // idUserType of requesting user
	) {
		//prepare/execute
		$query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);
		
		$query = "SELECT
			ta_set.id_set,
			ta_set.set_name,
			ta_set.date_added,
			ta_set.is_auto_check
		FROM
			ta_set
		WHERE (
				ta_set.id_user_type_test = ? AND
				? > 0
			) OR
			(
				? = -1 AND
				ta_set.id_user_type_test > 0
			)
		ORDER BY ta_set.set_index";

        $statement = $this->prepare($query);
        $statement->bind_param("iii", $idUserType, $idUserType, $idUserType);
        $dataset = $this->execute($statement);

		return collect($dataset)->sortBy('set_name')->all();
	}
	
	/**
		@desc: Saves new instance of test answer for a user
	*/
	public function testAdd (
		$idUser = 0, // requesting user
		$idSet = 0, // test set being answered
		$venue = ""
	) {		
		//prepare/execute
		$query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

		$query = "SELECT 1=1 FROM ta_test
			WHERE ta_test.id_user_tested = ? AND
				ta_test.id_set = ? AND
				ta_test.id_user_checked = 0";
        $statement = $this->prepare($query);
        $statement->bind_param("ii", 
						$idUser,
						$idSet);
        $dataset = $this->execute($statement);

		if ($dataset->num_rows > 0) {
			$query = "SELECT 
				ta_test.id_test,
				ta_set.set_name,
				ta_set.is_auto_check
			FROM
				ta_test
				LEFT JOIN ta_set
					ON ta_test.id_set = ta_set.id_set
			WHERE ta_test.id_user_tested = ? AND
				ta_test.id_set = ? AND
				ta_test.id_user_checked = 0
			ORDER BY ta_test.id_test DESC
			LIMIT 1";
			$statement = $this->prepare($query);
			$statement->bind_param("ii", 
						$idUser,
						$idSet);
			$dataset = $this->execute($statement);
		} else {
			$query = "INSERT INTO ta_test (
				id_user_tested,
				id_user_checked,
				id_set,
				venue
			)
			VALUES (
				?,
				0,
				?,
				?
			)";
			$statement = $this->prepare($query);
			$statement->bind_param("iis", 
						$idUser,
						$idSet
						,$venue);
			$this->execute($statement);
			$insert_id = $this->mysqli->insert_id;
				
			$query = "SELECT 
				ta_test.id_test,
				ta_set.set_name,
				ta_set.is_auto_check
			FROM
				ta_test
				LEFT JOIN ta_set
					ON ta_test.id_set = ta_set.id_set
			WHERE
				ta_test.id_test = ?";
			$statement = $this->prepare($query);
			$statement->bind_param("i", 
						$insert_id);
			$dataset = $this->execute($statement);
		}

		return $dataset;
	}
	
	/**
		@desc: add/register new trainer record
	*/
	public function addSetQuestion (
		$question_set = "", 
		$question = "", 
		$set_question_index = "", 
		$answer_index = "", 
		$question_type = "",
		$max_score = "",
		$textfield_count = "1",
		$choices = ""
	) {
		 // make sure that the user type is 3. Please refer to the database
		 // and select * from ta_user_type for the complete list of user type
		$idUserType = 3;

		$query = "INSERT INTO ta_set_question (
			id_set,
            question,
            question_set_index,
            answer_index,
            id_set_question_type,
            max_score,
            textfield_count,
            choices
        )
		VALUES (
			?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )";
        $statement = $this->prepare($query);
        $statement->bind_param("isssiiis", 
					$question_set,
					$question,
					$set_question_index,
					$answer_index,
					$question_type,
					$max_score,
					$textfield_count,
					$choices);
        $this->execute($statement);
		$insert_id = $this->mysqli->insert_id;
		
		$query = " SELECT 
		? id_set_question,
		? id_set,
		? question,
		? question_set_index,
		? answer_index,
		? id_set_question_type,
		? max_score,
		? textfield_count,
		? choices";
		$statement = $this->prepare($query);
		$statement->bind_param("iisssiiis", 
					$insert_id,
					$question_set,
					$question,
					$set_question_index,
					$answer_index,
					$question_type,
					$max_score,
					$textfield_count,
					$choices);
        $this->execute($statement);
		$dataset = $this->execute($statement);

		return $dataset;
	}

	/**
		@desc: update test record
	*/
	public function updateSetQuestion (
		$id_set_question = 0, // id of the question to be updated
		$id_set = 0,
		$question = "", 
		$set_question_index = "", 
		$answer_index = "", 
		$question_type = 0,
		$max_score = 1,
		$textfield_count = 1,
		$choices = ""
	) {
		$query = "UPDATE ta_set_question 
			SET
				id_set = ?,
				question = ?,
				question_set_index = ?,
				answer_index = ?,
				id_set_question_type = ?,
				max_score = ?,
				textfield_count = ?,
				choices = ?
		WHERE
			ta_set_question.id_set_question = ?";
        $statement = $this->prepare($query);
		$statement->bind_param("isssiiisi", 
					$id_set,
					$question,
					$set_question_index,
					$answer_index,
					$question_type,
					$max_score,
					$textfield_count,
					$choices,
					$id_set_question);

        $dataset = $this->execute($statement);

		return $dataset;
	}
	/**
		@desc: fetch all questions connected to the specified idSet
	*/
	public function getSetQuestionAll (
		$idSet = 0 // id of the test set to be taken
	) {
		//prepare/execute
		$query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

		$query = "SELECT
			ta_set_question.id_set_question,
			ta_set_question.id_set,
			ta_set_question.question,
			ta_set_question.date_added,
			ta_set_question.question_set_index,
			ta_set_question.answer_index,
			ta_set_question.id_set_question_type,
			ta_set_question.max_score,
			ta_set_question.textfield_count,
			ta_set_question.choices,
			ta_set_question_type.set_question_type,
			IF( 
				ta_set_question.question_set_index LIKE '% - %', 
				ta_set_question.question_set_index, 
				CONCAT('DEALS - ',ta_set_question.question_set_index)
			) as set_index
		FROM
			ta_set_question
			LEFT JOIN ta_set_question_type
				ON ta_set_question.id_set_question_type = ta_set_question_type.id_set_question_type
		WHERE
			ta_set_question.id_set = ?
		ORDER BY
			SUBSTRING_INDEX(set_index, ' - ', 1), CAST(SUBSTRING_INDEX(set_index, ' - ', -1) AS UNSIGNED)";
        $statement = $this->prepare($query);
        $statement->bind_param("i", $idSet);
        $dataset = $this->execute($statement);

		return $dataset;
	}
	/**
		@desc: fetch all questions connected to the specified idSet
	*/

	public function getSetQuestionSpecific (
		$idSet = 0 // id of the test set to be taken
	) {
		//prepare/execute
		$query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

		$query = "SELECT
			ta_set_question.id_set_question,
			ta_set_question.id_set,
			ta_set_question.question,
			ta_set_question.date_added,
			ta_set_question.question_set_index,
			ta_set_question.answer_index,
			ta_set_question.id_set_question_type,
			ta_set_question.max_score,
			ta_set_question.textfield_count,
			ta_set_question.choices,
			ta_set_question_type.set_question_type
		FROM
			ta_set_question
			LEFT JOIN ta_set_question_type
				ON ta_set_question.id_set_question_type = ta_set_question_type.id_set_question_type
		WHERE
			ta_set_question.id_set_question = ?
		ORDER BY
			ta_set_question.id_set_question";

        $statement = $this->prepare($query);
        $statement->bind_param("i", $idSet);
        $dataset = $this->execute($statement);

		return $dataset;
	}

	/**
		@desc: fetch all questions connected to the specified idSet
	*/
	public function getSetQuestionTypeAll () {
		//prepare/execute
		$query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

		$query = "SELECT
			id_set_question_type,
			set_question_type,
			date_added
		FROM
			ta_set_question_type";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

		return $dataset;
	}
	
	/**
		@desc: saves the actual answer of the current test item
	*/
	public function addTestDetail (
		$idTest = 0, // id of the test instance
		$idQuestion = 0, // id of the question being answered
		$answer = "" // answer for the current item
	) {
		//prepare/execute
        $query = "UPDATE ta_test SET is_deleted = 0 WHERE id_test = $idTest";
        $statement = $this->prepare($query);
		$this->execute($statement);
		
		//prepare/execute
		$query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

		//Check if existing test detail
		$query = "SELECT 1 = 1 FROM ta_test_detail
			WHERE ta_test_detail.id_set_question = ? AND
				ta_test_detail.id_test = ?";
        $statement = $this->prepare($query);
        $statement->bind_param("ii", 
						$idQuestion,
						$idTest);
        $dataset = $this->execute($statement);
		
		if ($dataset->num_rows > 0) {
			$idTestDetail = $this->getTestDetailSpecific($idTest, $idQuestion);
			$query = "UPDATE ta_test_detail tad
			SET
				tad.answer = ?
			WHERE
				tad.id_test_detail = ?";
			$statement = $this->prepare($query);
			$statement->bind_param("is", 
						$idTest,
						$idTestDetail);
			$this->execute($statement);

			$query = "SELECT 'answer modified.' message";
			$statement = $this->prepare($query);
			$dataset = $this->execute($statement);
		} else {
			$query = "INSERT INTO ta_test_detail (
					id_test,
					id_set_question,
					answer
				)
				VALUES (
					?,
					?,
					?
				)";
			$statement = $this->prepare($query);
			$statement->bind_param("iis", 
						$idTest,
						$idQuestion,
						$answer);
			$this->execute($statement);

			$query = "SELECT 'answer saved.' message";
			$statement = $this->prepare($query);
			$dataset = $this->execute($statement);
		}

		return $dataset;
	}
	
	public function getTestDetailSpecific (
		$idTest = 0, // specific ID to be displayed
		$idQuestion = 0 // specific ID to be displayed
	) {
		// $query = "SELECT id_test_detail INTO p_id_test_detail FROM ta_test_detail
		$query = "SELECT id_test_detail FROM ta_test_detail
		WHERE
			ta_test_detail.id_test = ? AND
			ta_test_detail.id_set_question = ?
		LIMIT 1";
        $statement = $this->prepare($query);
        $statement->bind_param("ii"
							,$idTest
							,$idQuestion);
        $dataset = $this->execute($statement);

		return $dataset;
	}

	/**
		@desc: Saves the score for the specified item
	*/
	public function addScoreTestDetail (
		$idTest = 0, // id of test set being scored
		$idSetQuestion = 0, // id of the question
		$idUserChecked = -1, // id of the user checking the exam; 0 for auto check
		$score = 0 // score
	) {
		//prepare/execute
		$query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

		$query = "UPDATE ta_test
			SET
				ta_test.id_user_checked = ?,
				ta_test.date_checked = NOW()
			WHERE
				ta_test.id_test = ?";
		
		$statement = $this->prepare($query);
        $statement->bind_param("ii", 
						$idUserChecked,
						$idTest);
        $this->execute($statement);

		//Check if existing test 
		$query = "SELECT 1=1 FROM ta_test_detail
			WHERE ta_test_detail.id_test = ? AND
				ta_test_detail.id_set_question = ?";
			
		$statement = $this->prepare($query);
		$statement->bind_param("ii", 
						$idTest,
						$idSetQuestion);
		$dataset = $this->execute($statement);

		if ($dataset->num_rows > 0) {
			$query = "UPDATE ta_test_detail
				SET ta_test_detail.score = ?
				WHERE ta_test_detail.id_test = ? AND
					ta_test_detail.id_set_question = ?";
			$statement = $this->prepare($query);
			$statement->bind_param("iii", 
						$score,
						$idTest,
						$idSetQuestion);

			$this->execute($statement);

			$query = "SELECT 'Score saved.' message";
			$statement = $this->prepare($query);
			$dataset = $this->execute($statement);
		} else {
			$query = "SELECT 'Invalid test item to check.' message";
			$statement = $this->prepare($query);
			$dataset = $this->execute($statement);
		}
		return $dataset;
	}
	
	
	
	
	
	//checker section
	/**
		@desc: get all test result
	*/
	public function getTestAll (
		$idTest = 0 // specific ID to be displayed
	) {
		//prepare/execute
		$query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);
		
		$query ="SELECT
			ta_test.id_test,
			ta_test.id_user_tested,
			DATE_FORMAT(DATE(ta_test.date_took), '%d/%b/%Y') date_took,
			DATE_FORMAT(fn_get_completion_date(ta_test.id_test), '%d/%b/%Y') date_completed,
			TIMEDIFF(fn_get_completion_date(ta_test.id_test), ta_test.date_took) time_took,
			ta_test.id_user_checked,
			ta_test.date_checked,
			ta_user_took.first_name,
			ta_user_took.last_name,
			ta_user_took.email_address,
			fn_get_test_score(ta_test.id_test) score,
			fn_get_test_max_score(ta_test.id_set) max_score,
			ta_set.id_set,
			ta_set.set_name,
			ta_set.is_auto_check,
			ta_set.id_user_type_test,
			DATE_FORMAT(NOW() , '%d%m%Y') date_now
		FROM
			ta_test
			LEFT JOIN ta_user ta_user_took
				ON ta_test.id_user_tested = ta_user_took.id_user
			LEFT JOIN (
				SELECT
					COUNT(*) answer_count,
					ta_test_detail.id_test
				FROM
					ta_test_detail
				GROUP BY
					ta_test_detail.id_test
			)
			test_detail
				ON ta_test.id_test = test_detail.id_test
			LEFT JOIN ta_set
				ON ta_test.id_set = ta_set.id_set
			LEFT JOIN (
				SELECT
					COUNT(*) question_count,
					ta_set_question.id_set
				FROM
					ta_set_question
				GROUP BY
					ta_set_question.id_set
			) set_question
				ON ta_set.id_set = set_question.id_set
		WHERE (
			(
				? != 0 AND
				ta_test.id_test = ?
			) OR (
				? = 0
			)
		) AND ta_test.is_deleted = 0 AND
		test_detail.answer_count = set_question.question_count
		ORDER BY
			ta_test.id_test DESC";
        $statement = $this->prepare($query);
        $statement->bind_param("iii", $idTest, $idTest, $idTest);
        $dataset = $this->execute($statement);

		return $dataset;
	}
	
	/**
		@desc: get the detail of request test set
	*/
	public function getTestDetail (
		$idTest = 0 // specific ID to be displayed
	) {
		//variables
		
		//prepare/execute
		$query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

		$query = "SELECT
			DISTINCT
			ta_set.id_set,
			ta_set.set_name,
			ta_test_detail.id_test_detail,
			ta_test_detail.id_test,
			ta_test_detail.id_set_question,
			ta_test_detail.answer,
			ta_set_question.question,
			ta_test_detail.date_answered,
			ta_test_detail.score,
			ta_set_question.max_score,
			ta_set_question.question_set_index,
			ta_set_question.choices,
			ta_set_question.answer_index
		FROM
			ta_test_detail
			LEFT JOIN ta_set_question
				ON ta_test_detail.id_set_question = ta_set_question.id_set_question
			LEFT JOIN ta_test
				ON ta_test_detail.id_test = ta_test.id_test
			LEFT JOIN ta_set
				ON ta_test.id_set = ta_set.id_set
		WHERE
			ta_test_detail.id_test = ? AND
			ta_test.is_deleted = 0
		ORDER BY
			CAST(ta_set_question.question_set_index AS SIGNED) ASC";
        $statement = $this->prepare($query);
        $statement->bind_param("i", $idTest);
        $dataset = $this->execute($statement);

		return $dataset;
	}
	
	
	/**
		@desc: get the detail of request test set
	*/
	public function setTestAnswerSheet (
		$idTest = 0 // specific ID to be displayed
	) {
		//variables
		$dataset = $this->getTestDetail($idTest);
		
		$updated = 0;

		while($row = $dataset->fetch_assoc()){
			extract($row);
				
			if($answer == "")
				continue;
			

			$answer = trim(str_replace(";", "", $answer));
			$answer = str_replace("\n", " ", $answer);

			//$answer = json_encode($answer);
			$choicesArray = explode(';', $choices);
			$options = "";
			

			$answer_index = "";

			$choicesArray = explode(";", $choices);
			
			for ($i = 0; $i < count($choicesArray); $i++) {
				$option = $choicesArray[$i];
				$option = str_replace("\r\n", " ", $option);
				$option = trim($option);
				//$option = json_encode($option);
				if ($option == $answer) {
					if ($option != "") {
						$answer_index = $i;
					}
				}
				//var_dump($option);
				//echo "<br>";
			}

			//var_dump($answer);
			//echo "<br>"; 

			//echo "$updated) $answer_index <hr>";
			$query = "UPDATE ta_set_question SET answer_index = '$answer_index' WHERE id_set_question = '$id_set_question'";
			
			$statement = $this->prepare($query);
			$data = $this->execute($statement);
			$updated++;
		}

		return "Successfully updated $updated test answers." ;
	}

	/**
		@desc: deletes a specific id test
	*/
	public function setQuestionDelete (
		$idTest = 0 // id of the test to be deleted
	) {		
		//prepare/execute
		$query = "DELETE FROM ta_set_question
			WHERE ta_set_question.id_set_question = ?";
        $statement = $this->prepare($query);
        $statement->bind_param("i", $idTest);
        $this->execute($statement);

		$query = "SELECT 'record deleted'  message";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);

		return $dataset;
	}

	/**
		@desc: deletes a specific id test
	*/
	public function testDelete (
		$idTest = 0 // id of the test to be deleted
	) {		
		//prepare/execute
		$query = "UPDATE ta_test
			SET
				ta_test.is_deleted = 1
			WHERE
				ta_test.id_test = ?";
        $statement = $this->prepare($query);
        $statement->bind_param("i", $idTest);
        $this->execute($statement);

		$query = "SELECT 'record deleted'  message";
        $statement = $this->prepare($query);
        $dataset = $this->execute($statement);
		return $dataset;
	}
	
	/**
		@desc: add score to a test specified by the auto-checker
	*/
	public function addCheckTestDetail (
		$idTestDetail = 0, //current item to be marked
		$idUserChecked = 0, // id User checker
		$score = 0 // score
	) {
		//prepare/execute
		$query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);

		$query = "UPDATE ta_test
			SET
				ta_test.id_user_checked = ?,
				ta_test.date_checked = NOW()
			WHERE
				ta_test.id_test IN (SELECT ta_test_detail.id_test FROM ta_test_detail
									WHERE ta_test_detail.id_test_detail = ?)";
        $statement = $this->prepare($query);
        $statement->bind_param("ii", 
			$idUserChecked,
			$idTestDetail);
        $this->execute($statement);

		//Check if existing test details 
		$query = "SELECT 1=1 FROM ta_test_detail
				WHERE ta_test_detail.id_test_detail = ?";
		$statement = $this->prepare($query);
		$statement->bind_param("i", $idTestDetail);
		$dataset = $this->execute($statement);

		if ($dataset->num_rows > 0) {
			$query = "SELECT 'Score saved.' message";
			$statement = $this->prepare($query);
			$dataset = $this->execute($statement);
		} else {
			$query = "SELECT 'Invalid test item to check.' message";
			$statement = $this->prepare($query);
			$dataset = $this->execute($statement);
		}
		return $dataset;
	}
	
	/**
		@desc: fetch filtered data
	*/
	public function getFilteredTestAll (
		$idSet = 0 // specific ID to be displayed
		,$adviser_name = null // filtered name to be displayed
	) {
		$adviser_name_where = "";
		if(isset($adviser_name) && !empty($adviser_name)) 
			$adviser_name_where = "AND (
					ta_user_took.first_name LIKE '%".$adviser_name."%' OR
					ta_user_took.last_name LIKE '%".$adviser_name."%' OR
					CONCAT(ta_user_took.last_name,',') LIKE '%".$adviser_name."%' OR
					CONCAT(ta_user_took.last_name,', ',ta_user_took.first_name) LIKE '%".$adviser_name."%'
				)";
		
		//prepare/execute
        $query = "SET time_zone = '+13:00'";
        $statement = $this->prepare($query);
        $this->execute($statement);
		
        $query = "SELECT
			ta_test.id_test,
			ta_test.id_user_tested,
			DATE_FORMAT(DATE(ta_test.date_took), '%d/%b/%Y') date_took,
			DATE_FORMAT(fn_get_completion_date(ta_test.id_test), '%d/%b/%Y') date_completed,
			TIMEDIFF(fn_get_completion_date(ta_test.id_test), ta_test.date_took) time_took,
			ta_test.id_user_checked,
			ta_test.date_checked,
			ta_user_took.first_name,
			ta_user_took.last_name,
			ta_user_took.email_address,
			fn_get_test_score(ta_test.id_test) score,
			fn_get_test_max_score(ta_test.id_set) max_score,
			ta_set.id_set,
			ta_set.set_name,
			ta_set.is_auto_check,
			ta_set.id_user_type_test,
			DATE_FORMAT(NOW() , '%d%m%Y') date_now
		FROM
			ta_test
			LEFT JOIN ta_user ta_user_took
				ON ta_test.id_user_tested = ta_user_took.id_user
			LEFT JOIN (
				SELECT
					COUNT(*) answer_count,
					ta_test_detail.id_test
				FROM
					ta_test_detail
				GROUP BY
					ta_test_detail.id_test
			)
			test_detail
				ON ta_test.id_test = test_detail.id_test
			LEFT JOIN ta_set
				ON ta_test.id_set = ta_set.id_set
			LEFT JOIN (
				SELECT
					COUNT(*) question_count,
					ta_set_question.id_set
				FROM
					ta_set_question
				GROUP BY
					ta_set_question.id_set
			) set_question
				ON ta_set.id_set = set_question.id_set
		WHERE (
			(
				? != 0 AND
				ta_test.id_set = ?
			) OR (
				? = 0
			)
		) 
		".$adviser_name_where."
		AND ta_test.is_deleted = 0
		AND test_detail.answer_count = set_question.question_count
		ORDER BY
			ta_test.id_test DESC";
        $statement = $this->prepare($query);
        $statement->bind_param("iii", $idSet, $idSet, $idSet);
        $dataset = $this->execute($statement);

		return $dataset;
	}
}
?>