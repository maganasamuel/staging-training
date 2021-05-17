<?php

/**
@name: login_trainee.php
@author: Gio
@desc:
	login page for all trainee or user types that takes the test
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//include necessary files
include_once("lib/General.helper.php");
include_once("lib/Session.helper.php");
include_once("lib/Test.controller.php");

$app = new GeneralHelper();
$session = new SessionHelper();
$test = new TestController();
//variables
$emailAddress = $app->param($_POST, "email_address");
$firstName = $app->param($_POST, "first_name");
$lastName = $app->param($_POST, "last_name");
$password = $app->param($_POST, "password");
$action = $app->param($_POST, "action");
$message = $app->param($_GET, "message");
$type = $app->param($_GET, "type");
$idUserType = 0;

switch ($type) {
	case "admin":
		$idUserType = 4;
		break;
	case "adviser":
		$idUserType = 2;
		break;
	case "bdm":
		$idUserType = 5;
		break;
	case "telemarketer":
		$idUserType = 6;
		break;
}


//check if type is included from the parameter
if ($type == "") {
	header("location: login");
}

//fetch all referral code related to the idUserType
$correctPassword = "";
$passwordDataset = $test->getReferralCode($idUserType);

if ($passwordDataset->num_rows > 0) {
	while ($row = $passwordDataset->fetch_assoc()) {
		$correctPassword = $row["password"];
	}
}

//checks if the referral code written in the form matches any of the existing referral code of the system
if ($password == $correctPassword) {
	if (
		$emailAddress != "" &&		//Email not empty
		$firstName != "" &&			//First name not empty
		$lastName != "" &&			//Last name not empty
		($idUserType != 2 || $idUserType != 4 || $idUserType != 5 || $idUserType != 6)		//Not an admin or an adviser
	) {
		$message = "";

		$dataset = $test->userAdd($emailAddress, $password, $firstName, $lastName, $idUserType);

		$data = [];
		if ($dataset->num_rows > 0) {
			unset($data);
			while ($row = $dataset->fetch_assoc()) {
				$row["venue"] = $app->param($_POST, "venue");
				$data[] = $row;
			}
			if ($session->createTemporarySession($data)) {
				
				header("Location: test.php?page=test_set");
			}
		} else {
			$message = "Something went wrong. Please try again.";
		}
	} else {
		if ($action != "") {
			//$session->destroySession();
			$message = "All fields are required.";
		}
	}
} else {
	//$session->destroySession();
	if ($action == "login") {
		$message = "Invalid referral code.";
	}
}



?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="robots" content="noindex, nofollow" />
	<meta name="description" content="">
	<meta name="author" content="Elite Insure">
	<link rel="icon" href="img/favicon.ico">

	<title><?php echo ucwords($type); ?>'s Login</title>

	<!-- CSS -->
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/styles.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/login.css">

	<!-- Icon font -->
	<link href="css/google-icons.css" rel="stylesheet">

	<!-- Script -->
	<script src="js/jquery-3.2.1.slim.min.js"></script>
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.js"></script>
</head>

<body>
	<div align="container">
		<form method="post">
			<div class="row justify-content-md-center">
				<div class="col-3 logo">
					<img id="logo" src="img/logo.png">
				</div>
			</div>
			<br />
			<div class="row">
				<div class="col center">
					<p style="font-size:14px; text-transform:capitalize;">
						<?php
						echo "---{$type}---";
						?>
					</p>
				</div>
			</div>
			<div class="row justify-content-md-center">
				<div class="col-3">
					<input class="form-control" name="email_address" type="email" placeholder="Email address" />
				</div>
			</div>
			<div class="row justify-content-md-center">
				<div class="col-3">
					<input class="form-control" name="first_name" type="text" placeholder="First name" />
				</div>
			</div>
			<div class="row justify-content-md-center">
				<div class="col-3">
					<input class="form-control" name="last_name" type="text" placeholder="Last name" />
				</div>
			</div>
			<div class="row justify-content-md-center">
				<div class="col-3">
					<input class="form-control" name="venue" type="text" placeholder="Venue" <?php
																								if ($type == "admin") {
																									echo '
								style="display:none;"
							';
																								}
																								?> />
				</div>
			</div>

			<div class="row justify-content-md-center">
				<div class="col-3">
					<input class="form-control" name="password" type="password" placeholder="Referral Code" />
				</div>
			</div>
			<div class="row justify-content-md-center">
				<div class="col-3">
					<?php
					if ($message != "") {
						echo <<<EOF
							<div class="alert alert-danger" role="alert">
							{$message}
							</div>
EOF;
					}
					?>
					<input type="hidden" name="action" value="login_trainee" />
					<input type="submit" value="Start" class="btn btn-info width100">
					<br />
					<br />
					<a href="login" class="center">
						<p>
							Back to login options
						</p>
					</a>
				</div>
			</div>
		</form>
	</div>
</body>

</html>