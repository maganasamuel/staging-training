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
include_once("Test.controller.php");
include_once("General.helper.php");
include_once("Session.helper.php");


//init class
$app = new GeneralHelper();

//fetch POST request parameter 
$action = $app->param($_POST, "action");

//determine which function to trigger
switch($action) {
	case "":
	default:
		echo json_encode(array("message"=>"invalid request"));
	break;
	case "qs-all":
		echo getSetQuestionAll ();
	break;
	case "q-a":
		echo addTestDetail ();
	break;
	case "t-c":
		echo addCheckTestDetail ();
	break;
	case "r-s":
		echo checkAnswer();
	break;
}

/**
	@desc: saves the score of the specificied id_test_detail
	@param:
		idtd: id_test_detail - id of the test answer
		id: id_user - id of the user who's checking the test
		score: - score given to the specified id_test_detail/ test answer
*/
function addCheckTestDetail () {
	$app = new GeneralHelper();
	$session = new SessionHelper();
	$idTestDetail = $app->param($_POST, "idtd");
	$idUserChecked = $session->get("id_user");
	$score = $app->param($_POST, "score");
	
	$test = new TestController();
	$dataset = $test->addCheckTestDetail($idTestDetail, $idUserChecked, $score);
	
	$data = array("message" => "No records found");
	
	if ($dataset->num_rows > 0) {
		unset($data);
		while ($row = $dataset->fetch_assoc()) {
			$data[] = $row;
		}
	}
	
	return json_encode($data);
}

/**
	@desc: Saves the examinee's answer for a specific question item
	@param:
		idt: id_test - id of the test to be fetched
		id: id_set_question - id of of question being answered
		answer: - answer of the examinee
*/
function addTestDetail () {
	$app = new GeneralHelper();
	$idTest = $app->param($_POST, "idt");
	$idQuestion = $app->param($_POST, "id");
	$answer = $app->param($_POST, "answer");
	
	$test = new TestController();
	$dataset = $test->addTestDetail($idTest, $idQuestion, $answer);
	
	$data = array("message" => "No records found");
	
	if ($dataset->num_rows > 0) {
		unset($data);
		while ($row = $dataset->fetch_assoc()) {
			$data[] = $row;
		}
	}
	
	return json_encode($data);
}

/**
	@desc: Get all Test sets
	@param:
		ids: id_set - id of a test set to be fetched
*/
function getSetQuestionAll() {
	$app = new GeneralHelper();
	$test = new TestController();
	$idSet = $app->param($_POST, "ids");
	$dataset = $test->getSetQuestionAll($idSet);
	$data = array("message" => "No records found");
	
	if ($dataset->num_rows > 0) {
		unset($data);
		while ($row = $dataset->fetch_assoc()) {
			$data[] = $row;
		}
	}
	
	return json_encode($data);
}


/**
	@desc: saves the score of the test set with "Auto-Check" status
	@param:
		idt: id_test - id of a test
		ids: id_set - id of a test set
		score: - scores of all items in the test in JSON string format
*/
function checkAnswer () {
	$app = new GeneralHelper();
	$test = new TestController();
	$idTest = $app->param($_POST, "idt");
	$idSet = $app->param($_POST, "ids");
	$score = $app->param($_POST, "score");
	
	$scores = json_decode($score);
	$data = [];
	if (is_array($scores)) {
		for ($i = 0; $i < count($scores); $i++) {
			$scoreCurrent = $scores[$i];
			$id = $scoreCurrent->id;
			$point = $scoreCurrent->score;
			
			$dataset = $test->addScoreTestDetail($idTest, $id, -1, $point);
			if ($dataset->num_rows > 0) {
				while ($row = $dataset->fetch_assoc()) {
					$data[] = array("id" => $id,
								"s" => $point,
								"m" => $row["message"]);
				}
			}			
			
		}
	}
	return json_encode($data);
}
?>