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
        $query = "call ad_referral_code (?)";
        $statement = $this->prepare($query);
        $statement->bind_param("i", $idUserType);
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
		//prepare/execute
        $query = "call ad_user_add (?, ?, ?, ?, ?)";
        $statement = $this->prepare($query);
        $statement->bind_param("ssssi", 
						$emailAddress,
						$password,
						$firstName,
						$lastName,
						$idUserType);
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
        $query = "call ad_set_all (?)";
        $statement = $this->prepare($query);
        $statement->bind_param("i", $idUserType);
        $dataset = $this->execute($statement);

		return $dataset;	
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
        $query = "call ad_add_test (?, ?, ?)";
        $statement = $this->prepare($query);
        $statement->bind_param("iis", 
						$idUser,
						$idSet,
						$venue);
        $dataset = $this->execute($statement);

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
        $query = "call ad_set_question_add (?, ?, ?, ?, ?, ?, ?, ?)";
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
        $query = "call ad_set_question_update (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $statement = $this->prepare($query);
		$statement->bind_param("iisssiiis", 
					$id_set_question,
					$id_set,
					$question,
					$set_question_index,
					$answer_index,
					$question_type,
					$max_score,
					$textfield_count,
					$choices);

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
        $query = "call ad_set_question_all (?)";
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
        $query = "call ad_set_question_specific (?)";
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
        $query = "call `ad_set_question_type_all` ()";
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
        $query = "call ad_add_test_detail (?, ?, ?)";
        $statement = $this->prepare($query);
        $statement->bind_param("iis", 
						$idTest,
						$idQuestion,
						$answer);
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
        $query = "call ad_score_test_detail (?, ?, ?, ?)";
        $statement = $this->prepare($query);
        $statement->bind_param("iiii", 
						$idTest,
						$idSetQuestion,
						$idUserChecked,
						$score);
        $dataset = $this->execute($statement);

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
        $query = "call ad_test (?)";
        $statement = $this->prepare($query);
        $statement->bind_param("i", $idTest);
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
        $query = "call ad_test_detail (?)";
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
        $query = "call ad_set_question_delete (?)";
        $statement = $this->prepare($query);
        $statement->bind_param("i", $idTest);
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
        $query = "call ad_test_delete (?)";
        $statement = $this->prepare($query);
        $statement->bind_param("i", $idTest);
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
        $query = "call ad_check_test_detail (?, ?, ?)";
        $statement = $this->prepare($query);
        $statement->bind_param("iii", 
						$idTestDetail,
						$idUserChecked,
						$score);
        $dataset = $this->execute($statement);

		return $dataset;
	}
	
}
?>