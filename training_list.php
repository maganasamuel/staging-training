<?php
/**
@name: test.php
@author: Gio
@desc:
	master page for trainee/examinee that has access to the actual test page
*/
ob_start();

//secure the page
// include_once("security.php");
// $prop = array(
// 			"group_name" => "index",
// 			"allow" => "1"
// 		);
// securePage($prop);

//include necessary files
include_once("lib/Session.helper.php");
include_once("lib/General.helper.php");
include_once("lib/Training.controller.php");


$session = new SessionHelper();
$app = new GeneralHelper();

$trainingController = new TrainingController();

$currentSessionFirstName = $app->param($_SESSION, "first_name", "User");


$access = $app->param($_SESSION, "grant",-1);

if($access != "yes"){
	header("location: login_trainee?type=trainer");
}

$idUserType = $app->param($_SESSION, "id_user_type", -1);
$userFullName = $app->param($_SESSION, "full_name", -1);
$fsp = $app->param($_SESSION, "fsp", -1);
$id_user = $app->param($_SESSION, "id_user", -1);
$sent = "";
if(isset($_GET['sent'])) {
    $sent = '<div class="alert alert-success" role="alert">
    Certificates successfully sent!
</div>';
}

$action = $app->param($_GET, "action");

if ($action == "del") {
	$idTrain = $app->param($_GET, "id", 0);
	$deleteDataset = $trainingController->deleteTraining($idTrain);
}


if ($action == "delUser") {
	$idUser = $app->param($_GET, "id", 0);
	$deteUser = $trainingController->deteUsertraining($idUser);
}

$conductedTraining = $trainingController->conductedTraining($id_user);
$trConducted = "";

$attendedTraining = $trainingController->attendedTraining($id_user);
$trAttended = "";

$totalConcducted = $trainingController->gettotalContducted($id_user);
$totalAttended = $trainingController->gettotalAttended($id_user);


$userList = $trainingController->getUser();
$usList = "";

while ($row = $userList->fetch_assoc()) {

		$usID = $row["id_user"];
		$usFullanme = $row["full_name"];
		$usEmail = $row["email_address"];
		$usFSP = $row["ssf_number"];	
		$usNumber = $row["id_user_type"];	

		if($usNumber == "1"){
			$ustype = "Admin";
		}elseif ($usNumber == "2") {
			$ustype = "ADR/SADR";
		}else{
			$ustype = "Adviser";
		}

		$usList .= <<<EOF
		<tr>
			<td>{$usFullanme}</td>
			<td>{$usEmail}</td>
			<td>{$usFSP}</td>
			<td>{$ustype}</td>
			<td>
				<a href="training_user?id={$usID}" title="Edit User" class="delete" data-toggle="tooltip" data-placement="bottom">
					<i class="material-icons">edit</i>
				</a>
				<a href="training_list?id={$usID}&action=delUser" title="Delete User" class="donwloadPDF" data-toggle="tooltip" data-placement="bottom">
					<i class="material-icons">delete</i>
				</a>
			</td>
		</tr>

EOF;
}

while ($row = $conductedTraining->fetch_assoc()) {
		$topic = $row["training_topic"];
		$date = substr($row["training_date"], 0, -3);

		$trConducted .= <<<EOF
		<tr>
			<td>{$date}</td>
			<td class="capitalize">{$topic}</td>
		</tr>

EOF;
}

while ($row = $attendedTraining->fetch_assoc()) {
		$topic = $row["training_topic"];
		$date = substr($row["training_date"], 0, -3);

		$trAttended .= <<<EOF
		<tr>
			<td>{$date}</td>
			<td class="capitalize">{$topic}</td>
			
		</tr>

EOF;
}


$dataset = $trainingController->getTraining($id_user,$idUserType);
$headers = array("Date", "Topic", "Trainer", "Status","Action");
$tableHeader = $app->getHeader($headers);
$rows = $tableHeader;
$action = $app->param($_POST, "action");
$message = "";

if ($dataset->num_rows <= 0) {
	$rows .= $app->emptyRow(count($headers));
}
else {
	while ($row = $dataset->fetch_assoc()) {
		
		$topic = $row["training_topic"];
		$date = $row["training_date"];

    	$newDateTime = date('Y-m-d h:i A', strtotime($date));
		$trainer = $row["fullname"];
		$trainingID = $row["training_id"];
		$today = new DateTime();
		$status = "";
		
		if( strtotime($row["training_date"]) < strtotime('now') ) {
			$status = "<span class='badge bg-success' style='color:white;'>Completed</span>";
		}else{
			$status = "<span class='badge bg-info' style='color:white;'>Not Completed</span>";
		}


        $rows .= <<<EOF
		<tr>
			<td>{$newDateTime}</td>
			<td class="capitalize">{$topic}</td>
			<td>{$trainer}</td>
			<td>{$status}</td>
			<td><a href="trainingpdf?id={$trainingID}" class="sendEmail" target="_blank" title="View Certificates" data-toggle="tooltip" data-placement="bottom">
					<i class="material-icons">insert_drive_file</i>
				</a>
				<a href="trainingpdf?id={$trainingID}&mail=1" class="sendEmail" title="Send Attendee Certificates" data-toggle="tooltip" data-placement="bottom">
					<i class="material-icons">email</i>
				</a>
				<a href="training_list?id={$trainingID}&action=del" title="Delete" class="delete" data-toggle="tooltip" data-placement="bottom">
					<i class="material-icons">delete</i>
				</a>
				<a href="trainingpdf?id={$trainingID}&download=1" title="Download Certificate" class="donwloadPDF" data-toggle="tooltip" data-placement="bottom">
					<i class="material-icons">arrow_downward</i>
				</a>
			</td>
		</tr>

EOF;
}
}
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

		<title></title>
		
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
					<li class="nav-item">
					<?php 
						
							echo "<a class=\"nav-link\" style=\"color:#FFFFFF;\" href=\"login_trainee?type=trainer\">Sign out</a>";
						
					?>
					</li>
				</ul>
			</div>
		</nav>

		<div class="subHeader">
			<div class="row">
				<div class="col title">
					Training List
				</div>
			</div>
		</div>

			

		<div class="main">
				<div class="col-12">
						
				 		<?php echo $sent; ?>

					</div>
			<div class="row justify-content-md-center mt-4">
				

					<div class="col-3">
						
				 		<?php echo $message; ?>

					</div>
				</div>
			<div class="row">
				<div class="col-sm-1"></div>
				<div class="col-sm-10">
					<div class="tab">
					  <button class="tablinks" onclick="openCity(event, 'TrainingList')" id="defaultOpen">Training List</button>
					  <button class="tablinks" onclick="openCity(event, 'AddLeaders')"
					  	<?php if ( $idUserType != "1") {											
							echo 'style="display:none;"';
							}
						?> >Add Leaders / Members</button>
					  <button class="tablinks" onclick="openCity(event, 'MyProfile')" >My Profile</button>
					</div>

			<div id="TrainingList" class="tabcontent">
			  <div class="row">
				<div class="col-sm-12">					
					<ul class="subHeader-controls">
						<li>
							<a href="training" title="Add new training" data-toggle="tooltip" data-placement="bottom" <?php if ( $idUserType == "3") {											
							echo 'style="display:none;"';
							}
						?> >
								<i class="material-icons">add</i>	
							</a>
						</li>
					</ul>
					<table class="table table-striped">
					<?php
						echo $rows;
					?>
					</table>
				</div>
				
			</div>
			</div>

			<div id="AddLeaders" class="tabcontent">

				<div class="row">
					<div class="col-2">
						<!-- <div class="card">
						<h5 class="card-header">User Info</h5>
						<div class="card-body">
						<input type="text" placeholder="Full Name" class="form-control mb-2" name="full_name" aria-label="Large" aria-describedby="inputGroup-sizing-sm">
						<input type="text" placeholder="Email Address" class="form-control mb-2" name="email_address" aria-label="Large" aria-describedby="inputGroup-sizing-sm">
						<input type="password" placeholder="Password" class="form-control mb-2" name="password" aria-label="Large" aria-describedby="inputGroup-sizing-sm">
						<input type="text" placeholder="FSP Number" class="form-control mb-2" name="ssfnumber" aria-label="Large" aria-describedby="inputGroup-sizing-sm">		
						<div class="form-group">
						    <select class="form-control" id="exampleFormControlSelect1" name="user_type">
						      <option value="1">Admin</option>
						      <option value="2">ADR / SADR </option>
						      <option value="3">Adviser</option>
						    </select>
						    <input type="hidden" name="action" value="save_profile"/><br>
							<input id="generate" type="submit" value="Save" class="btn btn-info width100" />
						</div>
					</div>
					</div> -->
					</div>
					<div class="col-12">
						<ul class="subHeader-controls">
						<li>
							<a href="training_user" title="Add New User" data-toggle="tooltip" data-placement="bottom" <?php if ( $idUserType == "3") {											
							echo 'style="display:none;"';
							}
						?> >
								<i class="material-icons">add</i>	
							</a>
						</li>
					</ul>
						<table class="table">
							  <thead>
							    <tr>
							      <th scope="col">Full Name</th>
							      <th scope="col">Email Address</th>
							      <th scope="col">FSP</th>
							      <th scope="col">User Type</th>
							      <th scope="col">Action</th>
							    </tr>
							  </thead>
							  <tbody>
							    <?php
										echo $usList;
								?>
							  </tbody>
						</table>
					</div>

				</div>

			</div>

					<div id="MyProfile" class="tabcontent">
						<div class="row ml-3">
					 		<div class="col-3 cell-user">
								<div class="cell-controls"></div>
									<div class="cell-detail">
										<p class="capitalize">Adviser: <?= $userFullName; ?></p>
										<p >FSP: <?= $fsp; ?></p>
										<p class="trainConducted">Total Conducted: <?=$totalConcducted;?></p>
										<p>Total Attended:  <?=$totalAttended;?></p>
									</div>
							</div>	
							<div class="col-4 trainConducted" >
								<div class="cell-controls text-center" style="font-size: 15px;">Trainings Conducted</div>
								<table class="table">
								  <thead>
								    <tr>
								      <th scope="col">Date</th>
								      <th scope="col">Topic</th>
								    </tr>
								  </thead>
								  <tbody>
								    <?php
											echo $trConducted;
									?>
								  </tbody>
								</table>
							</div>	
							<div class="col-4">
								<div class="cell-controls text-center" style="font-size: 15px;">Trainings Attended</div>
								<table class="table">
								  <thead>
								    <tr>
								      <th scope="col">Date</th>
								      <th scope="col">Topic</th>
								    </tr>
								  </thead>
								  <tbody>
								  	<?php
											echo $trAttended;
									?>
								  </tbody>
								</table>
							</div>	
						</div>
					</div>	
				</div>

				<div class="col-sm-1"></div>
			</div>
		</div>
	</body>
	<style type="text/css">
		th{
			width: 250px;
			text-align: center;
		}
		td:nth-child(2),td:nth-child(3),td:nth-child(1),td:nth-child(4),td:nth-child(5){
			text-align: center;
		}
		body {font-family: Arial;}

		.tab {
		  overflow: hidden;
		  border: 1px solid #ccc;
		}

		.tab button {
		  background-color: inherit;
		  float: left;
		  border: none;
		  outline: none;
		  cursor: pointer;
		  padding: 14px 16px;
		  transition: 0.3s;
		  font-size: 17px;
		}

		.tab button:hover {
		  background-color: #ddd;
		}

		.tab button.active {
		  background-color: #ccc;
		}

		/* Style the tab content */
		.tabcontent {
		  display: none;
		  padding: 6px 12px;
		  border: 1px solid #ccc;
		  border-top: none;
		}
		.table .thead-light th{
			font-weight: bold;
			font-size: 16px;
		}
		.table .tr .td{
			font-size: 13px;
		}
		<?php if ( $idUserType == "3"){
			echo "
             .sendEmail{
             	display:none;
             }
             .pdfView{
             	display:none;
             }
             .delete{
             	display:none;
             }
             .fsp{
             	display:none;
             }
             .trainConducted{
             	display:none;
             }
			";
		}
		?>
	</style>
	<script type="text/javascript">
		function openCity(evt, cityName) {
			  var i, tabcontent, tablinks;
			  tabcontent = document.getElementsByClassName("tabcontent");
			  for (i = 0; i < tabcontent.length; i++) {
			    tabcontent[i].style.display = "none";
			  }
			  tablinks = document.getElementsByClassName("tablinks");
			  for (i = 0; i < tablinks.length; i++) {
			    tablinks[i].className = tablinks[i].className.replace(" active", "");
			  }
			  document.getElementById(cityName).style.display = "block";
			  evt.currentTarget.className += " active";
			}
			document.getElementById("defaultOpen").click();
	</script>
</html>



