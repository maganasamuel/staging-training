<?php
/**
@name: test.php
@author: Gio
@desc:
	master page for trainee/examinee that has access to the actual test page
*/
ob_start();
//secure the page
include_once("security.php");
$prop = array(
			"group_name" => "trainee",
			"allow" => ""
		);
securePage($prop);

//include necessary files
include_once("lib/Session.helper.php");
include_once("lib/General.helper.php");

$session = new SessionHelper();
$app = new GeneralHelper();

//variables
$emailAddress = $session->get("email_address");
$firstName = $session->get("first_name");
$lastName = $session->get("last_name");
$idUserType = $session->get("id_user_type");
$userType = $session->get("user_type");
$idTest = $app->param($_GET, "idt", 0);

$email = $app->param($_SESSION, "email", 0);
$id_user = $app->param($_SESSION, "id_user", 0);
$idUserType = $app->param($_SESSION, "id_user_type", 0);

//validate
if ($emailAddress == "" ||
	$firstName == "" ||
	$lastName == "") {
	//header("Location: login?type={$userType}&message=Please sign up here.");
}

//determine the page to be displayed
$page = $app->param($_GET, "page", "test_set");
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="robots" content="noindex, nofollow" />
		<meta name="description" content="">
		<meta name="author" content="Elite Insure">
		<link rel="icon" href="img/favicon.ico">

		<title><?php echo ucwords($userType); ?>'s Test</title>

		<!-- CSS -->
		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/styles.css" rel="stylesheet">
		<link href="css/test_form.css" rel="stylesheet">

		<!-- Icon font -->
		<link href="css/google-icons.css" rel="stylesheet">

		<!-- Script -->
		<script src="js/jquery-3.2.1.slim.min.js"></script>
		<script src="js/jquery-3.2.1.min.js"></script>
		<script src="js/popper.min.js"></script>
		<script src="js/bootstrap.js"></script>
	</head>

	<body>
		<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #0c4664; padding:0px 5px;">
			<a class="navbar-brand" href="#">
				<img src="img/logo_vertical.svg" alt="onlineinsure" class="logo logo-small" style="height:40px;"/>
			</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="mainNav">
				<ul class="navbar-nav mr-auto justify-content-end width100">
					<li class="nav-item dropdown">
					<a href="#" class="nav-link dropdown-toggle" id="otherSoftwareDropdown" style="color: white;" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Other Software</a>
					<div class="dropdown-menu" aria-labelledby="otherSoftwareDropdown">
						<a href="https://onlineinsure.co.nz/adviceprocess/" class="dropdown-item" target="_blank">Advice Process</a>
						<?php
                        if (in_array($idUserType, [1, 7, 8])) {
                            ?>
							<a href="training?page=login_to_clients_feedback" class="dropdown-item" target="_blank">Clients Feedback</a>
							<?php
                        }
                        ?>
                         <?php
                        if (in_array($idUserType, [1,2,7,8,5])) {
                            ?>
							<a href="training?page=login_to_leadtracker" class="dropdown-item" target="_blank">Leadtracker</a>
							<?php
                        }
                        ?>
					</div>
				</li>
				<li class="nav-item dropdown">
					<?php

						echo "<a class='nav-link dropdown-toggle' href='#' id='navbarDropdown' style='color:white;'' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Resources</a>";
					?>
						<div class="dropdown-menu" aria-labelledby="navbarDropdown">
							<a class="dropdown-item" target="_blank" href="https://www.eliteinsure.co.nz/forms/">Forms</a>
							<a class="dropdown-item" target="_blank" href="https://www.eliteinsure.co.nz/fact-sheets/">Fact Sheet</a>
	        			</div>
					</li>
					<li class="nav-item dropdown">
					<?php

						echo "<a class='nav-link dropdown-toggle' href='#' id='navbarDropdown' style='color:white;' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Training</a>";
					?>
						<div class="dropdown-menu" aria-labelledby="navbarDropdown">
							<a class='dropdown-item' href="test?page=test_set">Take Assessment</a>
							<?php
                   				if (5 != $idUserType) {
                       		?>
								<a class="dropdown-item" href="training?page=training_list">Training List</a>
								<a class="dropdown-item" href="training?page=training_material_view">Training Materials</a>
							<?php
                    			}
                    		?>
							
	        			</div>
					</li>
					<li class="nav-item">

					</li>
					
					<li class="nav-item">
					<?php
						if ($idUserType == 1){
							echo "<a class=\"nav-link\" style=\"color:#FFFFFF;\" href=\"training?page=cpd_list\">CPD Topics</a>";
						}
					?>
					</li>
					<li class="nav-item">
					<?php
						if ($idUserType == 1 || $idUserType == 7 || $idUserType == 8){
							echo "<a class=\"nav-link\" style=\"color:#FFFFFF;\" href=\"training?page=training_user\">Member List</a>";
						}
					?>
					</li>
					<li class="nav-item">
					<?php

							echo "<a class=\"nav-link\" style=\"color:#FFFFFF;\" href=\"training?page=adviser_profile&id={$id_user}&email={$email}&user_type={$idUserType}\">My Profile</a>";

					?>
					</li>
					<li class="nav-item">
					<?php
						echo "<a class=\"nav-link\" style=\"color:#FFFFFF;\" href=\"login?type=trainer&action=logout\">Sign out</a>";
					?>
					</li>
				</ul>
			</div>
		</nav>

		<main role="main">
		<?php
			include_once("{$page}.php");
		?>
		</main><!-- /.container -->
	</body>
</html>
