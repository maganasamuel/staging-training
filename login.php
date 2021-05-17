<?php

/**
@name: login.php
@author: Gio
@desc:
Page that helps the user select on which type of user they would like to access trainer/admin/adviser;
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

	<title> </title>

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
		<div class="mainHolder">
			<div class="row justify-content-md-center">
				<div class="col-3 logo" style="padding-top:10px;">
					<img id="logo" src="img/logo.png">
				</div>
			</div>
			<div class="row">
				<div class="col">
					<p class="label">
						Login as:
					</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4 mt-3" style="border-right: 1px dotted #CCCCCC;">
					<a href="login_master">
						<div class="option" data-toggle="tooltip" data-placement="bottom" title="Users that has access to the checking tool.">
							<i class="material-icons">
								how_to_reg
							</i>
							<br />
							<span>Checker</span>
						</div>
					</a>
				</div>

				<div class="col-md-4 mt-3" style="border-right: 1px dotted #CCCCCC;">
					<a href="login_trainee?type=admin">
						<div class="option" data-toggle="tooltip" data-placement="bottom" title="Internal admins that will answer the assigned test.">
							<i class="material-icons">
								card_travel
							</i>
							<br />
							<span>Admin</span>
						</div>
					</a>
				</div>

				<div class="col-md-4 mt-3" data-toggle="tooltip">
					<a href="login_trainee?type=adviser">
						<div class="option" data-toggle="tooltip" data-placement="bottom" title="Adviser trainee that will answer the set of tests.">
							<i class="material-icons">
								face
							</i>
							<br />
							<span>Adviser</span>
						</div>
					</a>
				</div>
			</div>

			<div class="row">

				<div class="col-md-4 mt-3" data-toggle="tooltip">
					<a href="login_trainee?type=bdm">
						<div class="option" data-toggle="tooltip" data-placement="bottom" title="BDM trainee that will answer the set of tests.">
							<i class="material-icons">
								contact_mail
							</i>
							<br />
							<span>BDM</span>
						</div>
					</a>
				</div>

				<div class="col-md-4 mt-3" data-toggle="tooltip">
					<a href="login_trainee?type=telemarketer">
						<div class="option" data-toggle="tooltip" data-placement="bottom" title="BDM trainee that will answer the set of tests.">
							<i class="material-icons">
								contact_phone
							</i>
							<br />
							<span>Telemarketer</span>
						</div>
					</a>
				</div>
			</div>
		</div>
	</div>
	<script>
		$(function() {
			$('[data-toggle="tooltip"]').tooltip()
		})
	</script>
</body>

</html>
