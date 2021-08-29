<?php

include_once("lib/Session.helper.php");
include_once("lib/General.helper.php");
include_once("lib/Training.controller.php");


$session = new SessionHelper();
$app = new GeneralHelper();

$trainingController = new TrainingController();

$currentSessionFirstName = $app->param($_SESSION, "first_name", "User");



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

$conductedTraining = $trainingController->conductedTraining($id_user);
$trConducted = "";

$attendedTraining = $trainingController->attendedTraining($id_user);
$trAttended = "";

$totalConcducted = $trainingController->gettotalContducted($id_user);
$totalAttended = $trainingController->gettotalAttended($id_user);

$dataset = $trainingController->getTraining($id_user,$idUserType);
$rows = "";
$action = $app->param($_POST, "action");
$message = "";

if ($dataset->num_rows <= 0) {
}else {
	while ($row = $dataset->fetch_assoc()) {
		$topic = str_replace(',','<br>', $row["training_topic"]);
		$date = $row["training_date"];
		$trainerID = $row['trainer_id'];
    	$newDateTime = date('d-m-Y h:i A', strtotime($date));
		$trainer = $row["fullname"];
		$trainingID = $row["training_id"];
		$today = new DateTime();
		$status = "";
		
		$datasetRow = $trainingController->getTrainingTopic($id_user,$idUserType,$trainingID);
		$trow = "";
		$topicTitle = "";
		while ($trow = $datasetRow->fetch_assoc()) {
			$level = "";
			if($trow['topic_level'] == "0"){
				$level = '(Marketing)';
			}elseif($trow['topic_level'] == "1"){
				$level = '(Product)';
			}elseif($trow['topic_level'] == ""){
				$level = '';
			}else{
				$level = '(Compliance)';
			}
			$topicTitle .= $trow['topic_title'] .' '. $level . '<br>'; 
		}


		if( strtotime($row["training_date"]) < strtotime('now') ) {
			$status = "<span class='badge bg-success' style='color:white;'>Completed</span>";
		}else{
			$status = "<span class='badge bg-info' style='color:white;'>Not Completed</span>";
		}
		$edit = '<a href="training?page=training_add&id='.$trainingID.'" class="sendEmail" title="Edit Training" data-toggle="tooltip" data-placement="bottom"><i class="material-icons">edit</i></a>';
		if($idUserType != 1){
				if($trainerID != $id_user){
					$edit = '';
				}
		}
        $rows .= <<<EOF
		<tr>
			<td>{$newDateTime}</td>
			<td class="capitalize">{$topicTitle}</td>
			<td>{$trainer}</td>
			<td>{$status}</td>
			<td>
				{$edit}
				<a href="training?page=trainingpdf&id={$trainingID}" class="sendEmail" target="_blank" title="View Certificates" data-toggle="tooltip" data-placement="bottom">
					<i class="material-icons">insert_drive_file</i>
				</a>
				<a href="training?page=trainingpdf&id={$trainingID}&mail=1" class="sendEmail" title="Send Attendee Certificates" data-toggle="tooltip" data-placement="bottom">
					<i class="material-icons">email</i>
				</a>
				<a href="training?page=training_list&id={$trainingID}&action=del" title="Delete" class="delete" data-toggle="tooltip" data-placement="bottom" onclick="return confirm('Are you sure that you want to delete this training?')">
					<i class="material-icons">delete</i>
				</a>
				<a href="training?page=trainingpdf&id={$trainingID}&download=1" title="Download Certificate" class="donwloadPDF" data-toggle="tooltip" data-placement="bottom">
					<i class="material-icons">arrow_downward</i>
				</a>
			</td>
		</tr>

EOF;
}
}
?>
<div class="subHeader">
			<div class="row">
				<div class="col title">
					Training List
				</div>
				<ul class="subHeader-controls">
						<li>
						<a href="training?page=training_add"
						<?php if ( $idUserType == "2") {											
							echo 'style="display:none;"';
							}
						?> ><button type="button" class="btn btn-primary btn-sm" onclick="create()">Create Training Attestation	</button></a>

						</li>
					</ul>
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
			</div>
			<div class="row">
				<div class="col-sm-1"></div>
				<div class="col-sm-10">
			<div id="TrainingList" class="tabcontent">
			  <div class="row">
				<div class="col-sm-12">					
					
					 <table class="table table-responsive-md table-hoverable training">
							  <thead style="background-color:#e9ecef;">
							    <tr>
							      <th scope="col">Date</th>
							      <th scope="col">Topic</th>
							      <th scope="col">Trainer</th>
							      <th scope="col">Status</th>
							      <th scope="col">Action</th>
							    </tr>
							  </thead>
							  <tbody>
							    <?php
										echo $rows;
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
	<style type="text/css">
		th{
			width: 250px;
			text-align: center;
		}
		td:nth-child(2),td:nth-child(3),td:nth-child(1),td:nth-child(4),td:nth-child(5){
			text-align: center;
		}
		<?php if ( $idUserType == "2"){
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
		$(document).ready( function () {
          	$('.training').dataTable( {
 				 "pageLength": 25
			});
      	});
      	function create(){
      		window.location.href = '/training?page=training_add';
      	}
	</script>



