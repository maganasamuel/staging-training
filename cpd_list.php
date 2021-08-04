<?php

include_once("lib/Session.helper.php");
include_once("lib/General.helper.php");
include_once("lib/Training.controller.php");


$session = new SessionHelper();
$app = new GeneralHelper();

include_once("lib/Training.controller.php");
$trainingController = new TrainingController();


$action = $app->param($_GET, "action");

if ($action == "delCPD") {
	$cpdID = $app->param($_GET, "id", 0);
	$deteUser = $trainingController->deleteCPD($cpdID);
}

$cpdList = $trainingController->getCPD();
$cpd = "";

while ($row = $cpdList->fetch_assoc()) {

		$id_cpd = $row["id_cpd"];
		$cpd_name = $row["cpd_name"];
		$cpd_description = $row["cpd_description"];
		
		$cpd .= <<<EOF
		<tr>
			<td>{$cpd_name}</td>
			<td>{$cpd_description}</td>
			<td>
				<a href="training?page=cpd_add&id={$id_cpd}" title="Edit CPD"  data-toggle="tooltip" data-placement="bottom">
					<i class="material-icons">edit</i>
				</a>
				<a href="training?page=cpd_list&id={$id_cpd}&action=delCPD" title="Delete CPD" data-toggle="tooltip" data-placement="bottom" onclick="return confirm('Are you sure that you want to delete this topic?')">
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
							<a href="training?page=cpd_add" title="Add new cpd topic" data-toggle="tooltip" data-placement="bottom" <?php if ( $idUserType == "3") {											
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
						  <table class="table table-responsive-md table-hoverable cpdList">
							  <thead style="background-color:#e9ecef;">
							    <tr>
							      <th >CPD Topic</th>
							      <th>CPD Description</th>
							      <th>Action</th>
							    </tr>
							  </thead>
							  <tbody>
							    <?php
									echo $cpd;
								?>
							  </tbody>
						</table>
					</div>

				</div>
				<div class="col-sm-1"></div>
			</div>
		</div>
		<style type="text/css">
			table td:nth-child(1){
				width: 20%;
			}
			table td:nth-child(2){
				width: 70%;
			}
		</style>
		<script type="text/javascript">
			$(document).ready( function () {
          		$('.cpdList').dataTable( {
 				 "pageLength": 25
				});
      		});
		</script>