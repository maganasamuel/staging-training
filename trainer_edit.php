<?php

/**
@name: trainer_edit.php
@author: Gio
@desc:
	Modifies the current selected trainer info
 */
//secure the page
include_once("security.php");
$prop = array(
	"group_name" => "index",
	"allow" => "1"
);
securePage($prop);

//include necessary files
include_once("lib/General.helper.php");
include_once("lib/User.controller.php");
include_once("lib/Test.controller.php");

$app = new GeneralHelper();
$userController = new UserController();
$testController = new TestController();

//variables
$action = $app->param($_POST, "action");
$idUser = $app->param($_GET, "id");
$message = $app->param($_GET, "message");
$firstName = "";
$lastName = "";
$emailAddress = "";
$password = "";
$acessibleTestSets = "";

//save record
if ($action == "save") {
	$idUser = $app->param($_POST, "id_user");
	$firstName = $app->param($_POST, "first_name");
	$lastName = $app->param($_POST, "last_name");
	$emailAddress = $app->param($_POST, "email_address");
	$password = $app->param($_POST, "password");
	$acessibleTestSets = $app->param($_POST, "sets_accessible");

	//validates the form
	if (
		$idUser == "" ||
		$firstName == "" ||
		$lastName == "" ||
		$emailAddress == "" ||
		$password == "" ||
		$acessibleTestSets == ""
	) {
		$message = "<div class=\"alert alert-danger\" role=\"alert\">All fields are required.</div>";
	} else {
		$dataset = $userController->updateTrainer(
			$idUser,
			$firstName,
			$lastName,
			$emailAddress,
			$password,
			$acessibleTestSets
		);
		header("location: index.php?page=trainer");
	}
} else {
	//fetch the selected idUser/trainer
	$dataset = $userController->getTrainerSpecific($idUser);

	if ($dataset->num_rows > 0) {
		while ($row = $dataset->fetch_assoc()) {
			$idUser = $row["id_user"];
			$emailAddress = $row["email_address"];
			$firstName = $row["first_name"];
			$lastName = $row["last_name"];
			$password = $row["password"];
			$setsAccessibleDataSet = $userController->getTrainerTestSetAccess($idUser);
			$setsAccessible = [];

			if ($setsAccessibleDataSet->num_rows > 0) {
				while ($setsAccessibleRow = $setsAccessibleDataSet->fetch_assoc()) {
					$setsAccessible[] = $setsAccessibleRow["id_set"];
				}
			}
		}
	} else {
		header("location: index.php?page=trainer_edit&id={$idUser}&message=Invalid User");
	}
}

?>
<style>
	label {
		width: 120px;
	}

	span.required {
		color: red;
		font-size: 8px;
	}
</style>
<div class="subHeader">
	<div class="row">
		<div class="col title">
			Edit Trainer
		</div>
	</div>
</div>
<div class="main" style="margin:0px 50px;">
	<div class="row">
		<div class="col">
			<?php echo $message; ?>
		</div>
	</div><br />
	<div class="row">
		<div class="col-4">
			<form method="post">
				<label for="first_name"><span class="required">*</span> First name:</label>
				<input type="text" name="first_name" id="first_name" class="form-control" value="<?php echo $firstName; ?>" />
				<br />
				<label for="last_name"><span class="required">*</span> Last name:</label>
				<input type="text" name="last_name" id="last_name" class="form-control" value="<?php echo $lastName; ?>" />
				<br />
				<label for="email_address"><span class="required">*</span> Email Address:</label>
				<input type="email" name="email_address" id="email_address" class="form-control" value="<?php echo $emailAddress; ?>" />
				<br />
				<label for="password"><span class="required">*</span> Password:</label>

				<input type="password" name="password" id="password" class="form-control" value="<?php echo $password; ?>" />
				<br />

				<label for="sets_accessible"><span class="required">*</span> Test Set Access:</label>
				<select name="sets_accessible[]" id="sets_accessible" class="form-control" multiple="multiple">
					<option value="" disabled hidden></option>
					<?php
					$setDataset = $testController->getSetAll(-1);

					if ($setDataset->num_rows > 0) {
						while ($row = $setDataset->fetch_assoc()) {
							$idSet = $row["id_set"];
							$setName = $row["set_name"];
							$selected = (in_array($idSet, $setsAccessible)) ? " selected" : "";

							echo "<option value='$idSet' {$selected}>{$setName}</option>";
						}
					}

					?>
				</select>

				<br />
				<br />
				<input type="hidden" name="id_user" value="<?php echo $idUser; ?>" />
				<input type="hidden" name="page" value="trainer_edit" />
				<input type="hidden" name="action" value="save" />
				<input type="submit" class="btn btn-primary" style="float:right; position:relative;" value="save">
			</form>
		</div>
		<div class="col"></div>
	</div>
</div>


<script>
$(function(){
	$("#sets_accessible").select2({
		"placeholder" : "Select a Test Set that this Trainer can access"
	})
});
</script>