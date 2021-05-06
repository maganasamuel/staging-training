<?php
/**
@name: password.php
@author: Gio
@desc:
	maintenance page for referral code/password
*/
//secure the page
include_once("security.php");
$prop = array(
			"group_name" => "index",
			"allow" => "1"
		);
securePage($prop);

// include necessary files
include_once("lib/General.helper.php");
include_once("lib/User.controller.php");

$app = new GeneralHelper();
$user = new UserController();

//variables
$firstName = $app->param($_SESSION, "first_name", "User");
$action = $app->param($_POST, "action");
$message = "";

//save the form
if ($action == "save") {
	$newPassword = $app->param($_POST, "password");
	$idPassword = $app->param($_POST, "id");
	if ($newPassword != "") {
		$dataset = $user->updatePassword($newPassword, $idPassword);
		$message =  "<div class=\"alert alert-success\" role=\"alert\">New Generic Password/Referral Code saved.</div>";
	}
	else {
		$message = "<div class=\"alert alert-danger\" role=\"alert\">Please provide a valid password/referral code.</div>";
	}
}

//generates the form accordingly
$dataset = $user->getReferralCode(-1);
$forms = "";
if ($dataset->num_rows > 0) {
	while ($row = $dataset->fetch_assoc()) {
		$currentPassword = $row["password"];
		$currentType = $row["user_type"];
		$currentIdPassword = $row["id_password"];
		
		$forms .= <<<EOF
	<div class="row">
		<div class="col-6">
			<form method="post">
				<div class="input-group mb-3">
					<input class="form-control" name="password" type="password" placeholder="The current {$currentType} password is: {$currentPassword}" />
					<div class="input-group-append">
						<input type="hidden" name="action" value="save"/>
						<input type="hidden" name="id" value="{$currentIdPassword}"/>
						<input type="submit" value="Save" class="btn btn-info width100">
					</div>
				</div>
			</form>	
		</div>
	</div>
EOF;
	}
}


?>
<div class="subHeader">
	<div class="row">
		<div class="col title">
			Passwords/ Referral Code
		</div>
	</div>
</div>
<div class="main" style="margin:0px 50px;">
	<p>To modify the Generic Password/Referral Code, please type on the respective text fields and hit "save":</p>
		<?php
			if ($message != "") {
				echo <<<EOF
				{$message}
EOF;
			}
			echo $forms;
		?>
</div>