<?php
/**
@name: test.php
@author: Gio
@desc:
	master page for trainee/examinee that has access to the actual test page
*/
ob_start();
include_once("lib/Session.helper.php");
include_once("lib/General.helper.php");

$session = new SessionHelper();
$app = new GeneralHelper();


$access = $app->param($_SESSION, "grant",-1);
if($access != "yes"){
	header("location: login_trainee?type=trainer");
}


$idUserType = $app->param($_SESSION, "id_user_type", -1);
$userFullName = $app->param($_SESSION, "full_name", -1);
$fsp = $app->param($_SESSION, "fsp", -1);
$id_user = $app->param($_SESSION, "id_user", -1);


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

		<title>Training</title>
		
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

		<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>

	</head>
	<style type="text/css">
	.main{
	margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
    font-size: 1.1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    text-align: left;
    background-color: #fff
}

	</style>
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
					
					<li class="nav-item">
					<?php 

						echo "<a class=\"nav-link\" style=\"color:#FFFFFF;\" href=\"training?page=training_list\">Training List</a>";
					?>
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
						if ($idUserType == 1){
							echo "<a class=\"nav-link\" style=\"color:#FFFFFF;\" href=\"training?page=training_user\">Member List</a>";
						}
					?>
					</li>
					<li class="nav-item">
					<?php 
						if ($idUserType == 3){
							echo "<a class=\"nav-link\" style=\"color:#FFFFFF;\" href=\"training?page=adviser_profile&id={$id_user}\">My Profile</a>";
						}
					?>
					</li>
					<li class="nav-item">
					<?php 
						echo "<a class=\"nav-link\" style=\"color:#FFFFFF;\" href=\"login_trainee?type=trainer\">Sign out</a>";
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