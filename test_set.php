<?php
/**
@name: test_set.php
@author: Gio
@desc:
	Displays all test set registered in the system
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
$testController = new TestController();
$session = new SessionHelper();

//variables
$emailAddress = $session->get("email_address");
$firstName = $session->get("first_name");
$lastName = $session->get("last_name");
$message = $app->param($_GET, "message");
$idUserType = $session->get("id_user_type");
$venue = $session->get("venue");
$userType = $session->get("user_type");

if ($emailAddress == "" ||
	$firstName == "" ||
	$lastName == "") {
	//header("Location: login");
}

$dataset = $testController->getSetAll($idUserType);
$headers = array("#", "Test Set", "Action");
$rows = $app->getHeader($headers);
if ($dataset->num_rows <= 0) {
	$rows .= $app->emptyRow(count($headers));
}
else {
	$i = 1;
	while ($row = $dataset->fetch_assoc()) {
		$idSet = $row["id_set"];
		$setName = $row["set_name"];
		
		$rows .= <<<EOF
		<tr>
			<td>{$i}</td>
			<td class="capitalize">{$setName}</td>
			<td>
				<a href="test.php?page=test_form&idqs={$idSet}" title="Take this test.">
					<i class="material-icons">create</i>
				</a>
			</td>
		</tr>
EOF;
		$i++;
	}
}


?>

<div class="subHeader" id="subHeader">
	<div class="row">
		<div class="col-8 title">
			Test Set
		</div>
		<div class="col-4">
		</div>
	</div>
</div>
<div class="main" style="padding:0px 50px;">
	<div class="col">
	<?php
		if ($message != "") {
			echo <<<EOF
			<div class="alert alert-danger" role="alert">
			{$message}
			</div>
EOF;
		}
	?>
	</div>
	
	<table class="table table-responsive-md table-hoverable">
	<?php
		echo $rows;
	?>
	</table>
</div>
<br/>