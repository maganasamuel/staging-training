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
		$cpd_classification = $row["cpd_classification"];


		if($row['cpd_classification'] == "1"){
				$cpd_classification = 'Manager Account';
			}elseif($row['cpd_classification'] == "2"){
				$cpd_classification = 'Admin';
			}elseif($row['cpd_classification'] == "3"){
				$cpd_classification = 'IT Specialist';
			}
			elseif($row['cpd_classification'] == "4"){
				$cpd_classification = 'Adviser';
			}else{
				$cpd_classification = 'Compliance Officer';
			}

		$cpd .= <<<EOF
		<tr>
			<td>{$cpd_name}</td>
			<td>{$cpd_description}</td>
			<td>{$cpd_classification}</td>
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
					PDP List
				</div>
				<ul class="subHeader-controls">
						<li>
							<a href="training?page=cpd_add" title="Create CPD Course" data-toggle="tooltip" data-placement="bottom" <?php 
							if ($idUserType === "1" || $idUserType === "3") {											
										echo 'style="display:none;"';
									}
						?> >
								<button type="button" class="btn btn-primary btn-sm" onclick="create()">Create PDP Course</button></a>
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
							      <th>Topic</th>
							      <th>Description</th>
							       <th>Classification</th>
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
			table {
			  table-layout: fixed ;
			  width: 100% ;
			  text-align: center;
			}
			td {
			  width: 25% ;
			  word-wrap: break-word;
			}

		</style>
		<script type="text/javascript">
			$(document).ready( function () {
          		$('.cpdList').dataTable( {
 				 "pageLength": 25
				});
      		});
		</script>