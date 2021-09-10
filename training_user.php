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

$activate = $app->param($_POST, "activate",0);

if ($activate != 0) {
	$myStatus = $app->param($_POST, "status",0);	
	$status = $trainingController->activeStatus($activate,$myStatus);
}

if ($action == "delUser") {
	$idUser = $app->param($_GET, "id", 0);
	$deteUser = $trainingController->deteUsertraining($idUser);
}

if($idUserType == "1"){
	$userList = $trainingController->getUser();
	$usList = "";

}elseif($idUserType == "3"){
	$userList = $trainingController->getUser();
	$usList = "";
}
elseif($idUserType == "7"){
	$userList = $trainingController->getAdrMember($id_user);
	$usList = "";
}elseif($idUserType == "8"){
	$userList = $trainingController->getSadrMember($id_user);
	$usList = "";
}

while ($row = $userList->fetch_assoc()) {

		$usID = $row["id_user"];
		$usFullanme = $row["first_name"].' '.$row['last_name'];
		$usEmail = $row["email_address"];
		$usFSP = $row["ssf_number"];	
		$usNumber = $row["id_user_type"];	
		$usStatus = $row["status"];			
		$newStatus = "";
		$chck = "";

		if($usStatus == "1"){
			$newStatus = "0";
		}else{
			$newStatus = "1";
		}

		if($usStatus == 1){
			$chck = "checked";
		}

		if($usNumber == "1"){
			$ustype = "Admin";
		}elseif ($usNumber == "7") {
			$ustype = "ADR";
		}elseif ($usNumber == "8") {
			$ustype = "SADR";
		}elseif ($usNumber == "3") {
			$ustype = "Checker";
		}else{
			$ustype = "Adviser";
		}

		/* $roles = [ 
			1 => 'Admin (Master)',
			2 => 'Adviser',
			3 => 'Trainer',
			4 => 'Admin',
			5 => 'BDM',
			6 => 'Telemarketer',
			7 => 'ADR',
			8 => 'SADR',
		]; */

		$ustype = $roles[$usNumber];

		$usList .= <<<EOF
		<tr>
			<td>
			<a href="training?page=adviser_profile&id={$usID}&email={$usEmail}&user_type={$usNumber}" title="View Profile" class="delete" data-toggle="tooltip" data-placement="bottom">
			{$usFullanme}</a></td>
			<td>{$usEmail}</td>
			<td>{$usFSP}</td>
			<td>{$ustype}</td>
			<td class="stat"><div class="custom-control custom-switch">
	  				<input type="checkbox" class="custom-control-input" id="{$usEmail}" onclick="test({$usID},{$newStatus})" {$chck}>
	  				<label class="custom-control-label" for="{$usEmail}"></label>
				</div>
			</td>
			<td class="act">
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
							<li>
							<a href="training?page=training_user_add" title="Add new user" data-toggle="tooltip" data-placement="bottom" <?php 
							if ( $idUserType != "1" ) {											
							echo 'style="display:none;"';
							}
						?> >
								<button type="button" class="btn btn-primary btn-sm" onclick="create()">Add New User</button></a>
						</li>
					</ul>
			</div>
		</div>
		<div class="main">
				<div class="row">
					<div class="col-1"></div>
					<div class="col-10">
						  <table class="table table-responsive-md table-hoverable usList">
							  <thead style="background-color:#e9ecef;">
							    <tr>
							      <th scope="col">Full Name</th>
							      <th scope="col">Email Address</th>
							      <th scope="col">FSP</th>
							      <th scope="col">User Type</th>
							      <th scope="col" class="stat">Status</th>
							      <th scope="col" class="act">Action</th>
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
		<script type="text/javascript">
			function test(id,status){

				 $.ajax({
                    url: 'training?page=training_user',
                    type: 'post',
                    data: {
                       activate: id,
                       status: status
                    },
                    success: function(data) {
						const Toast = Swal.mixin({
						toast: true,
						position: 'top-end',
						showConfirmButton: false,
						timer: 3000,
						didOpen: (toast) => {
						toast.addEventListener('mouseenter', Swal.stopTimer)
						toast.addEventListener('mouseleave', Swal.resumeTimer)
					  }
					})
						Toast.fire({
						icon: 'success',
						title: 'Account successfully updated'
						})
                    }
                });
			}
			$(document).ready( function () {
          		$('.usList').dataTable( {
 				 "pageLength": 25
				});
      		});
		</script>
		<style type="text/css">
			<?php 
				if ( $idUserType == "8" || $idUserType == "3" || $idUserType == "7" ){
					echo "
					.stat{
						display:none;
					}
					.act{
						display:none;
					}
					";
				}
			?>
		</style>










