<?php

/**
@name: trainer_add.php
@author: Gio
@desc:
	Page that handles adding of new trainer
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

$action = $app->param($_POST, "action");
$message = "";

//save the submitted form
if ($action == "save") {
	$firstName = $app->param($_POST, "first_name");
	$lastName = $app->param($_POST, "last_name");
	$emailAddress = $app->param($_POST, "email_address");
	$password = $app->param($_POST, "password");
	$acessibleTestSets = $app->param($_POST, "sets_accessible");
	if (
		$firstName == "" ||
		$lastName == "" ||
		$emailAddress == "" ||
		$password == ""||
		$acessibleTestSets == ""
	) {
		$message = "<div class=\"alert alert-danger\" role=\"alert\">All fields are required.</div>";
	} else {
		$dataset = $userController->addTrainer(
			$firstName,
			$lastName,
			$emailAddress,
			$password,
			$acessibleTestSets
		);
		//Send email invite to the newly added trainer
		require_once __DIR__ . '/package/vendor/autoload.php';
		$bccEmail = "compliance@eliteinsure.co.nz";
		$loginLink = "http://onlineinsure.co.nz/training/login";
		$content = <<<EOF
		<p style="margin-bottom:16px;text-transform:capitalize;">Hi {$firstName},</p>
		<p style="margin-bottom:10px;">You have been added as a trainer to our online test platform. </p>
		<p style="margin-bottom:10px;">You may now login at <a herf="{$loginLink}">{$loginLink}</a>.</p>
		<p>Username: {$emailAddress}</p>
		<p style="margin-bottom:16px;">Password: {$password}</p>
		<p>Thank you,</p>
EOF;

		$message = new Swift_Message();
		$message->setSubject('Training Invite');
		$message->setFrom(array('noreply@onlineinsure.co.nz' => 'No Reply'));
		$message->setTo(array($emailAddress));
		$message->setBcc(array($bccEmail));
		$message->setContentType("text/html");
		$message->setBody($content);

		$transport = new Swift_SmtpTransport();

		// Create the Mailer using your created Transport
		$mailer = new Swift_Mailer($transport);

		// Send the created message
		$isSent = $mailer->send($message);

		//send
		if ($isSent) {
			header("location: index.php?page=trainer&status=sent");
		} else {
			header("location: index.php?page=trainer&status=failed");
		}
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
			Add New Trainer
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
				<input type="text" name="first_name" id="first_name" class="form-control" />
				<br />
				<label for="last_name"><span class="required">*</span> Last name:</label>
				<input type="text" name="last_name" id="last_name" class="form-control" />
				<br />
				<label for="email_address"><span class="required">*</span> Email Address:</label>
				<input type="email" name="email_address" id="email_address" class="form-control" />
				<br />
				<label for="password"><span class="required">*</span> Password:</label>

				<input type="password" name="password" id="password" class="form-control" />
				<br />

				<label for="sets_accessible"><span class="required">*</span> Test Set Access:</label>
				<select name="sets_accessible[]" id="sets_accessible" class="form-control" multiple="multiple">
					<option value="" disabled hidden></option>
					<?php
					$setDataset = $testController->getSetAll(-1);

					if (count($setDataset) > 0) {
						foreach ($setDataset as $row) {
							$idSet = $row["id_set"];
							$setName = $row["set_name"];
							echo "<option value='$idSet'>{$setName}</option>";
						}
					}

					?>
				</select>

				<br />
				<br />
				<input type="hidden" name="page" value="trainer_add" />
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