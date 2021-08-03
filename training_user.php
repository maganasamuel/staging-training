<?php

include_once("lib/Session.helper.php");
include_once("lib/General.helper.php");
include_once("lib/Training.controller.php");


$session = new SessionHelper();
$app = new GeneralHelper();

include_once("lib/Training.controller.php");
$trainingController = new TrainingController();


$action = $app->param($_GET, "action");

if ($action == "delUser") {
	$idUser = $app->param($_GET, "id", 0);
	$deteUser = $trainingController->deteUsertraining($idUser);
}

$userList = $trainingController->getUser();
$usList = "";

while ($row = $userList->fetch_assoc()) {

		$usID = $row["id_user"];
		$usFullanme = $row["first_name"].' '.$row['last_name'];
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
			<td>
			<a href="training?page=adviser_profile&id={$usID}" title="View Profile" class="delete" data-toggle="tooltip" data-placement="bottom">
			{$usFullanme}</a></td>
			<td>{$usEmail}</td>
			<td>{$usFSP}</td>
			<td>{$ustype}</td>
			<td>
				<a href="training?page=training_user_add&id={$usID}" title="Edit User" class="delete" data-toggle="tooltip" data-placement="bottom">
					<i class="material-icons">edit</i>
				</a>
				<a href="training?page=training_user&id={$usID}&action=delUser" title="Delete User" class="donwloadPDF" data-toggle="tooltip" data-placement="bottom" onclick="return confirm('Are you sure that you want to delete this user?')">
					<i class="material-icons">delete</i>
				</a>
			</td>
		</tr>

EOF;
}
?>
		<div class="subHeader">
			<div class="row">
				<div class="col title">
					Member List
				</div>
				<ul class="subHeader-controls">
						<li>
							<a href="training?page=training_user_add" title="Add new user" data-toggle="tooltip" data-placement="bottom" <?php if ( $idUserType == "3") {											
							echo 'style="display:none;"';
							}
						?> >
								<i class="material-icons">add</i>	
							</a>
						</li>
					</ul>
			</div>
		</div>
		<div class="main">
				<div class="row">
					<div class="col-1"></div>
					<div class="col-10">
						  <table class="table table-responsive-md table-hoverable">
							  <thead style="background-color:#e9ecef;">
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
				<div class="col-sm-1"></div>
			</div>
		</div>