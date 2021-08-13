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
include_once("lib/Training.controller.php");

$app = new GeneralHelper();
$session = new SessionHelper();
$test = new TestController();
$training = new TrainingController();

//variables
$emailAddress = $app->param($_POST, "email_address");
$firstName = $app->param($_POST, "first_name");
$lastName = $app->param($_POST, "last_name");
$password = $app->param($_POST, "password");
$action = $app->param($_POST, "action");
$message = $app->param($_GET, "message");
$type = $app->param($_GET, "type");
$confirm = $app->param($_GET, "confirm");
$forgot_password = $app->param($_POST, "forgot_password");

if($_SERVER['SERVER_NAME'] == 'onlineinsure.co.nz'){
	$verifyAddress = 'https://onlineinsure.co.nz/staging/staging-training/login_trainee?confirm=yes&email_address='.$emailAddress;
} else {
	$verifyAddress = 'https://staging-training.test/login_trainee?confirm=yes&email_address='.$emailAddress;
};

//for adviser/tester
$venue_for_adviser = $app->param($_POST, "venue");

$idUserType = 0;

$testerDiv = false;

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
	case "trainer":
		$idUserType = 4;
		break;
	case "adr":
		$idUserType = 7;
		break;
	case "sadr":
		$idUserType = 8;
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



$mpassword = "";
if($forgot_password == "yes"){
		print_r("send password");
		$p_email = $app->param($_POST, "password_email");
		$recoveryPassword = $training->sendPassword($p_email);

		$content = new Swift_Message();
		$content->setSubject('Password Recovery');
		//$message->setfrom(array('executive.admin@eliteinsure.co.nz' => 'EliteInsure'));
		//Remove the venue at the certificate.
		//Move date to footer.

		$content->setfrom(array('executive.admin@eliteinsure.co.nz' => 'EliteInsure'));
		$content->setTo($p_email);

		$content->setBody('Your password is:'.$recoveryPassword);

		$transport = (new Swift_SmtpTransport('eliteinsure.co.nz', 587))
		->setusername('wilfred@eliteinsure.co.nz')
		->setPassword('wilelite2021');

		// Create the Mailer using your created Transport
		$mailer = new Swift_Mailer($transport);

		// Send the created message
		$isSent = $mailer->send($content);

}

$trainingLogin = $training->trainingLogin($emailAddress,$password);

if($type == "trainer"){
	if($emailAddress != "" || $password != ""){
		if ($trainingLogin->num_rows > 0) {
		while ($row = $trainingLogin->fetch_assoc()) {

				if($row['status'] == "0"){
					$message = "Account is deactivated!";
				}else{

					$data = [];
					if($password == $row['password']){
						$_SESSION['full_name']= $row['first_name'] . $row['last_name'] ;
						$_SESSION['fsp']= $row['ssf_number'];
						$_SESSION['email']= $row['email_address'];
						$_SESSION['id_user_type']= $row['id_user_type'];
						$_SESSION['id_user']= $row['id_user'];
						$_SESSION['grant']= 'yes';

						$training_details = $test->userCheck($row['email_address'],$row['id_user_type']);
						$training_details = $training_details->fetch_assoc();

						$location = 'training?page=adviser_profile&id='.$row['id_user'].'&email='.$row['email_address'].'&user_type='.$row['id_user_type'];
						
						$data[] = $training_details;
						if($session->createTemporarySession($data)) {
							if($row['id_user_type'] == 1 || $row['id_user_type'] == 3 ){
								header("location: training?page=training_list");
							}else{
								header("location:".$location);	
							}
							
						}
						
					}else{
						$message = "Email address and password do not match.";
					}								
				}

			}	
		}else{
			$message = "Email address and password do not match.";
		}
		
	}
}

if($confirm == "yes"){
	$emailVerify  = $app->param($_GET, "email_address");
	$trainingLogin = $training->verfiyEmail($emailVerify);
	header("location: login_trainee?type=adviser");	
}

if($idUserType == 2){
	 $userType = $test->userCheckType($emailAddress);
	if($userType->num_rows > 0) {
		$details = $userType->fetch_assoc();
		if($details['id_user_type'] == 7 || $details['id_user_type'] == 8){
			$idUserType = $details['id_user_type'];	
		}
	}
}

if($idUserType == 2 || $idUserType == 8 || $idUserType == 7) {
	$message = "";
	$message_green = "";	

	if($emailAddress != "" && $password != "") {
		if(str_ends_with($emailAddress, '@eliteinsure.co.nz')) {
			$details = $dataset = $test->userCheck($emailAddress,$idUserType);

			$data = [];
			if($dataset->num_rows > 0) {
				$details = $dataset->fetch_assoc();
				if($details['password'] == $password) {

					if($details['status'] == 1) {
						unset($data);

						$details["venue"] = $app->param($_POST, "venue");
						$details["id_user_type"] = 2;
						$data[] = $details;

						if ($session->createTemporarySession($data)) {
							header("Location: test.php?page=test_set");	
						}
					} else {
						$message = "Account is deactivated.";
						$message_green = "";	
					}
				} else {
					$message = "Email address and password do not match.";
					$message_green = "";
				}
			} else {
				if($emailAddress != "" && $firstName != "" && $lastName != "" && $password != "") {
					$dataset = $test->userAdd($emailAddress, $password, $firstName, $lastName, $idUserType);
					//params from $dataset
					//email function
					$content = new Swift_Message();
					$content->setSubject('Email Verification');
					//$message->setfrom(array('executive.admin@eliteinsure.co.nz' => 'EliteInsure'));
					//Remove the venue at the certificate.
					//Move date to footer.

					$content->setfrom(array('executive.admin@eliteinsure.co.nz' => 'EliteInsure'));
					$content->setTo($emailAddress);

					$content->setBody('Please click this link for activating your account <p>Verification Link: <a href="'. $verifyAddress .'">Verify my account</a></p>','text/html');


					$transport = (new Swift_SmtpTransport('eliteinsure.co.nz', 587))
					->setusername('wilfred@eliteinsure.co.nz')
					->setPassword('wilelite2021');

					// Create the Mailer using your created Transport
					$mailer = new Swift_Mailer($transport);

					// Send the created message
					$isSent = $mailer->send($content);

					$message_green = "An email has been sent to your email address. Please verify your account to proceed.";
					$message = "";
				} else {
					$message_green = "All fields are required.";
					$message = "";
					$testerDiv = true;
				}			
			}
		} else {
			$message = "Invalid email address.";	
			$message_green = "";
		} 
		

			
	}
} else {

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
				if($type == "trainer"){
					//do nothing
				} else {
					$message = "All fields are required.";	
				}
			}
		}
	} else {
		//$session->destroySession();
		if ($action == "login") {
			$message = "Invalid referral code.";
		}
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
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

						if($type == "trainer"){
							//display nothing
						}else{
							echo "---{$type}---";
						}
						?>
					</p>
				</div>
			</div>
			<?php if($type != "adviser") : ?>
			<div class="row justify-content-md-center">
				<div class="col-3">
					<input class="form-control" name="email_address" type="email" placeholder="Email address" />
				</div>
			</div>
			<div class="row justify-content-md-center" <?php
																								if ($type == "trainer") {
																									echo '
								style="display:none;"
							';
																								}
																								?> />
				<div class="col-3">
					<input class="form-control" name="first_name" type="text" placeholder="First name" />
				</div>
			</div>
			<div class="row justify-content-md-center" <?php
																								if ( $type == "trainer") {
																									echo '
								style="display:none;"
							';
																								}
																								?> />
				<div class="col-3">
					<input class="form-control" name="last_name" type="text" placeholder="Last name" />
				</div>
			</div>
			<div class="row justify-content-md-center">
				<div class="col-3">
					<input class="form-control" name="venue" type="text" placeholder="Venue" <?php
																								if ($type == "admin" || $type == "trainer") {
																									echo '
								style="display:none;"
							';
																								}
																								?> />
				</div>
			</div>

			<div class="row justify-content-md-center">
				<div class="col-3">
					<input class="form-control" name="password" type="password" placeholder="Password" />
				</div>
			</div>
			<div class="row justify-content-md-center">
				<div class="col-3">
					<?php
					if ($message != "") {
						echo '<div class="alert alert-danger" role="alert">'.$message.'</div>';

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
			<?php else : ?>
				<?php if($testerDiv) : ?>
				<div class="row justify-content-md-center">
					<div class="col-3">
						<input class="form-control" name="email_address" type="email" placeholder="Email address" value="<?php echo $emailAddress; ?>" />
					</div>
				</div>
				<div class="row justify-content-md-center"/>
					<div class="col-3">
						<input class="form-control" name="first_name" type="text" placeholder="First name" value="<?php echo $firstName; ?>"/>
					</div>
				</div>
				<div class="row justify-content-md-center"/>
					<div class="col-3">
						<input class="form-control" name="last_name" type="text" placeholder="Last name" value="<?php echo $lastName; ?>"/>
					</div>
				</div>
				<div class="row justify-content-md-center">
					<div class="col-3">
						<input class="form-control" name="venue" type="text" placeholder="Venue" value="<?php echo $venue_for_adviser; ?>"/>
					</div>
				</div>
				<div class="row justify-content-md-center">
					<div class="col-3">
						<input class="form-control" name="password" type="password" placeholder="Password" value="<?php echo $password; ?>"/>
					</div>
				</div>
				<?php else : ?>
				<div class="row justify-content-md-center">
					<div class="col-3">
						<input class="form-control" name="email_address" type="email" placeholder="Email address" value="<?php echo $emailAddress; ?>" />
					</div>
				</div>
				<div class="row justify-content-md-center">
					<div class="col-3">
						<input class="form-control" name="password" type="password" placeholder="Password" />
					</div>
				</div>
				<?php endif; ?>
				
				<div class="row justify-content-md-center">
					<div class="col-3">
						<?php
						if (isset($message_green) && $message_green != "") {
							echo '<div class="alert alert-success" role="alert">'.$message_green.'</div>';
						}


						if ($message != "") {
							echo '<div class="alert alert-danger" role="alert">'.$message.'</div>';
								
						}
						echo $mpassword;
						?>
						<input type="hidden" name="action" value="login_tester" />
						<input type="submit" value="Start" class="btn btn-info width100">
						<br />
						<br />
						<a href="javascript:;" onclick="sendPassword()" class="center">
							<p>
								Forgot Password
							</p>
						</a>
						<a href="login" class="center">
							<p>
								Back to login options
							</p>
						</a>
					</div>
				</div>
			<?php endif; ?>
		</form>
	</div>
<div class="modal"  id="myModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Password Recovery</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        	<input type="text" placeholder="Email Address" id="pemail" class="form-control">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="emailPassword()">Submit</button>
      </div>
    </div>
  </div>
</div>
</body>
<script type="text/javascript">
	function sendPassword(){
		 $("#myModal").modal('show');
	}
	function emailPassword(){
			 $.ajax({
                url: 'login_trainee',
                type: 'post',
                data: {
                   password_email: $("#pemail").val(),
                   forgot_password: "yes"
                },
                success: function(data) {
                	 $("#myModal").modal('hide');
					Swal.fire({
					  position: 'center',
					  icon: 'success',
					  title: 'Please check your email',
					  showConfirmButton: false,
					  timer: 1500
					})
                }
            });
	}
</script>

</html>