<?php
/**
@name: login_master.php
@author: Gio
@desc:
	login page for master admins/trainer
*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//include necessary files
include_once("lib/Session.helper.php");
include_once("lib/General.helper.php");
include_once("lib/Login.controller.php");

$session = new SessionHelper();
$app = new GeneralHelper();

//variables
$emailAddress = $app->param($_POST, "email_address");
$password = $app->param($_POST, "password");
$message = "";
$action = $app->param($_GET, "action");

//validate form
if ($emailAddress != "" && $password != "") {
	$login = new LoginController();
	$data = $login->userLogin($emailAddress, $password);
	
	if (is_array ($data)) {
		if (isset($data["message"])) {
			if ($data["message"] != "") {
				$message = "" . $data["message"];
			}
		}
		else {
			if ($session->createSession($data[0])) {
				header("Location: index.php");
			}
		}
	}
}
else {
	if ($action == "logout") {
		$session->destroySession();
	}
	
	if ($session->isSessionActive()) {
		header("Location: index.php");
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

		<title>Test Checker</title>

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
						<h1 class="text-center font-weight-bold text-tblue" style="letter-spacing: 0.25rem;">PORTAL</h1>
					</div>
				</div>
				<br/>
				<div class="row justify-content-md-center">
					<div class="col-3">
						<input class="form-control" name="email_address" type="text" placeholder="Username" required="" />
					</div>
				</div>
				<div class="row justify-content-md-center">
					<div class="col-3">
						<input class="form-control" name="password" type="password" placeholder="Password" required="" />
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
						<input type="hidden" name="action" value="login_master"/>
						<input type="submit" value="Login" class="btn btn-info width100" />
						<br/>
						<br/>
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