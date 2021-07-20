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
// 			"group_name" => "trainee",
// 			"allow" => ""
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
$currentSessionID = $app->param($_SESSION, "id_user", -1);

$idUserType = $app->param($_SESSION, "id_user_type", -1);

$dataset = $trainingController->getTraining($currentSessionID,$idUserType);
$headers = array("Date", "Topic", "Trainer", "Action");
$tableHeader = $app->getHeader($headers);
$rows = $tableHeader;
$action = $app->param($_POST, "action");
$message = "";


if ($action == "save_profile") {

	$full_name = $app->param($_POST, "full_name");
	$email_address = $app->param($_POST, "email_address");
	$password = $app->param($_POST, "password");
	$ssfnumber = $app->param($_POST, "ssfnumber");
	$user_type = $app->param($_POST, "user_type");


	$datasetuser = $trainingController->addUserTraining($full_name,
						$email_address,
						$password,
						$ssfnumber,$user_type
					);   

	$message = "<div class=\"alert alert-success\" role=\"alert\">User profile saved.</div>";
	
	}




if ($dataset->num_rows <= 0) {
	$rows .= $app->emptyRow(count($headers));
}
else {
	while ($row = $dataset->fetch_assoc()) {
		
		$topic = $row["training_topic"];
		$date = substr($row["training_date"], 0, -3);
		$trainer = $row["fullname"];
		$trainingID = $row["training_id"];
		$today = new DateTime();

		if( strtotime($row["training_date"]) < strtotime('now') ) {
			
		}else{
        $rows .= <<<EOF
		<tr>
			<td>{$date}</td>
			<td class="capitalize">{$topic}</td>
			<td>{$trainer}</td>
			<td><a href="trainingpdf?id={$trainingID}" class="sendEmail" title="View Certificates" data-toggle="tooltip" data-placement="bottom">
					<i class="material-icons">insert_drive_file</i>
				</a>
				<a href="trainingpdf?i?id={$trainingID}" class="sendEmail" title="Send Training Email" data-toggle="tooltip" data-placement="bottom">
					<i class="material-icons">email</i>
				</a>
				<a href="?id={$trainingID}" title="Delete" class="delete" data-toggle="tooltip" data-placement="bottom">
					<i class="material-icons">delete</i>
				</a>
				<a href="?id={$trainingID}" title="Download Certificate" class="donwloadPDF" data-toggle="tooltip" data-placement="bottom">
					<i class="material-icons">arrow_downward</i>
				</a>
			</td>
		</tr>

EOF;
}
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
					  <button class="tablinks" onclick="openCity(event, 'MyProfile')">My Profile</button>
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
				<form method="post">
				<div class="row justify-content-md-center">
					<div class="col-6 mt-2">
						<label class="font-weight-normal text-center">Full Name</label>
						<input type="text" placeholder="Full Name" class="form-control mb-1" name="full_name" aria-label="Large" aria-describedby="inputGroup-sizing-sm">
					</div>
					<div class="col-6 mt-2">
						<label class="font-weight-normal text-center">Email Address</label>
						<input type="text" placeholder="Email Address" class="form-control mb-1" name="email_address" aria-label="Large" aria-describedby="inputGroup-sizing-sm">
					</div>
					<div class="col-4 mt-2">
						<label class="font-weight-normal text-center">Password</label>
						<input type="text" placeholder="Password" class="form-control mb-1" name="password" aria-label="Large" aria-describedby="inputGroup-sizing-sm">
					</div>
					
					<div class="col-4 mt-2">
						<label class="font-weight-normal text-center">SSF Number</label>
						<input type="text" placeholder="SSF Number" class="form-control mb-1" name="ssfnumber" aria-label="Large" aria-describedby="inputGroup-sizing-sm">		
					</div>
					<div class="col-4 mt-2">
						<label class="font-weight-normal text-center">User Type</label>
						<div class="form-group">
						    <select class="form-control" id="exampleFormControlSelect1" name="user_type">
						      <option value="2">ADR / SADR </option>
						      <option value="3">Adviser</option>
						    </select>
					  </div>		
					</div>
					<div class="col-3 mt-2 mb-2">
						<input type="hidden" name="action" value="save_profile"/>
						<input id="generate" type="submit" value="Save" class="btn btn-info width100" />
					</div>
					
				</div>
				</form>
				
			</div>

			<div id="MyProfile" class="tabcontent">
			  	
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



