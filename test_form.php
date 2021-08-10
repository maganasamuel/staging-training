<?php
/**
@name: test_form.php
@author: Gio
@desc:
	actual test page which displays the questions controlled by a javascript
*/
//secure the page
include_once("security.php");
$prop = array(
			"group_name" => "trainee",
			"allow" => ""
		);
securePage($prop);

//include necessary files
include_once("lib/General.helper.php");
include_once("lib/Session.helper.php");
include_once("lib/Test.controller.php");

$app = new GeneralHelper();
$session = new SessionHelper();
$testController = new TestController();

//variables
$emailAddress = $session->get("email_address");
$firstName = $session->get("first_name");
$lastName = $session->get("last_name");
$idTest = $app->param($_GET, "idt", 0);
$idUserType = $session->get("id_user_type");
$venue = $session->get("venue");

if ($emailAddress == "" ||
	$firstName == "" ||
	$lastName == "") {
	header("Location: login");
}

$idSet = $app->param($_GET, "idqs");
$setName = "";
$idUser = $session->get("id_user");
$isAutoCheck = 0;

//adr and sadr share same set with adviser
if(($idUserType == 2) || ($idUserType == 7) || ($idUserType == 8))
	$idUserType = 2;

//check if the idset is set to the current requesting user.
$setDataset = $testController->getSetAll($idUserType);
$isAssigned = false;
if (count($setDataset) > 0) {
	foreach ($setDataset as $row) {
		$idSetCurrent = $row["id_set"];
		if ($idSetCurrent == $idSet) {
			$isAssigned = true;
			break;
		}
	}
}

if ($isAssigned == false) {
	//return to set page
	header("location: test?page=test_set");
	die();
}

//create new test
$testDataset = $testController->testAdd($idUser, $idSet, $venue);

if ($testDataset->num_rows > 0) {
	while($row = $testDataset->fetch_assoc()) {
		$idTest = $row["id_test"];
		$setName = $row["set_name"];
		$isAutoCheck = $row["is_auto_check"];
	}
}

if ($idTest <= 0) {
	header("Location: test.php?page=test_set&message=Unable to create new test.");
}

?>
<script type="text/javascript">
	var idTest = <?php echo $idTest; ?>;
	var idSet = <?php echo $idSet; ?>;
	var isAutoCheck = <?php echo $isAutoCheck;  ?>;
</script>
<script src="js/test_form.js"></script>

<div class="subHeader" id="subHeader">
	<div class="row">
		<div class="col-8 title">
			<?php echo $setName; ?>
		</div>
		<div class="col-4">
		</div>
	</div>
</div>
<div class="main" style="padding:0px 100px;">
	<div class="testFormWelcome">
		<h1 class="capitalize">Hello <?php echo $firstName; ?>!</h1>
		<p class="question">Welcome to <b><?php echo $setName; ?></b>. Please click "Start Test" to begin.</p>
		<hr/>
		<button class="btn btn-lg btn-primary float-right btn-next">Start Test &raquo;</button>
		<br class="spacer"/>
	</div>
	<div class="testForm">
		<h4 class="question">Question</h4>
		<div class="answerField">
			
		</div>
		<hr/>
		<span class="float-left timer"></span>
		<div class="float-right">
			<button class="btn btn-lg btn-primary btn-back">
				<i class="material-icons">
				arrow_back
				</i> Back
			</button>&nbsp;&nbsp;
			<button class="btn btn-lg btn-primary btn-next">
				Next
				<i class="material-icons">
				arrow_forward
				</i>
			</button>
		</div>
		<br class="spacer"/>
	</div>
</div>