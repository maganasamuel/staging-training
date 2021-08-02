<?php
/**
@name: test.php
@author: Gio
@desc:
	master page for trainee/examinee that has access to the actual test page
*/
ob_start();

//secure the page

//include necessary files
include_once("lib/Session.helper.php");
include_once("lib/General.helper.php");
include_once("lib/Training.controller.php");

$session = new SessionHelper();
$app = new GeneralHelper();

$access = $app->param($_SESSION, "grant",-1);

if($access != "yes"){
	header("location: login_trainee?type=trainer");
}

$trainingController = new TrainingController();
$action = $app->param($_POST, "action");
$message = "";

$currentSessionFirstName = $app->param($_SESSION, "first_name", "User");
$currentSessionID = $app->param($_SESSION, "id_user", -1);

if ($action == "save_training") {
	$topic_type = $app->param($_POST, "topic_type");
	if($topic_type == "1"){
		$topic =  $app->param($_POST, "cpd_topic");
	}else{
		$topic =  $app->param($_POST, "trainig_topic");
	}
	$attendee = $app->param($_POST, "traning_attendee");
	$date = $app->param($_POST, "training_date");
	$venue = $app->param($_POST, "training_venue");
	$attendee_id = $app->param($_POST, "traning_attendee");
	$trainer_id = $currentSessionID;
	$trainer_signature = $app->param($_POST, "signature");


	$dataset = $trainingController->addTraining($trainer_id,
						implode(',',$topic),
						implode(',',$attendee),
						$date,$venue,implode(',',$attendee_id),$trainer_signature,$topic_type
					);   

	$message = "<div class=\"alert alert-success\" role=\"alert\">Training session saved.</div>";
	
	}

$adviser = $trainingController->getAdviser();
$sets = "";
foreach($adviser as $row) {

		$name = $row["first_name"].' '.$row['last_name'];
		$id = $row["id_user"];
		
		$sets .= <<<EOF
		<option value="{$id}">{$name}</option>
EOF;
	}

$cpd = $trainingController->getCPD();
$cpdList = "";
$ctr = 0;
foreach($cpd as $row) {

		$cpdTopic = $row["cpd_name"];
		$cpdDesc = $row["cpd_description"];
		$ctr++;

		$cpdList .= <<<EOF
		<input class="form-check-input mr-2 chkbox" type="checkbox" value="$cpdTopic" id="$ctr" name="cpd_topic[]"><label title = "$cpdDesc" class="form-check-label chkbox mr-4" for="$ctr">$cpdTopic</label></br>
EOF;
	}
?>

		<div class="subHeader">
			<div class="row">
				<div class="col title">
					Add New Traning
				</div>
			</div>
		</div>
		
		<div align="container">
			<div class="row justify-content-md-center mt-4">

					<div class="col-3">
						
				 		<?php echo $message; ?>

					</div>
				</div>
			<form method="post">
				<div class="row justify-content-md-center">
					<div class="col-3">
						<label class="font-weight-normal text-center">Training Date</label>
						<input type="datetime-local" class="form-control" name="training_date"/>

					</div>
				</div>
				<br>
				<div class="row justify-content-md-center">
					<div class="col-3">
						<label class="font-weight-normal text-center">Venue</label>
						<input type="text" placeholder="Venue" class="form-control mb-1" name="training_venue" aria-label="Large" aria-describedby="inputGroup-sizing-sm">
					</div>
				</div>
				<br>
				<div class="row justify-content-md-center">
					<div class="col-3 mb-1">
						<label class="font-weight-normal text-center">Nature Of Training / Meeting</label>
						<select class="form-control mb-1" id="natureTraining" onchange="show()" name="topic_type">

						   <option value="0" disabled selected>Select Option</option>
						   <option value="1">Continuing Professional Development (CPD)</option>
						   <option value="2">Team Training</option>
						</select>

						<div class="teamTraining">
						</br>
						<input type="hidden" value="1" id="numberChk">	
						<div id="topicTag">
							<label class="font-weight-normal text-center">Topics that will discuss</label>
							<input type="text" placeholder="Topic 1" class="form-control mb-1"name="trainig_topic[]" aria-label="Large" aria-describedby="inputGroup-sizing-sm">
						</div>
						<button type="button" onclick="addTopic()" class="btn btn-info width mt-1">Add Topic</button>
						</div>

						<div class="cpdTraining">
							</br>
							<label class="font-weight-normal text-center">Topics that will discuss</label>
							<div class="form-check">
							  <?= $cpdList; ?>
							</div>
						</div>

					</div>
				</div>
				<br>
				<div class="row justify-content-md-center">
					<div class="col-3">
						<label class="font-weight-normal text-center">Attendee on the training</label>
						<select class="adviser js-states form-control" multiple="multiple" name="traning_attendee[]">
								<?php
									echo $sets;
								?>
						</select>
					</div>
				</div>
				<br>
				<div class="row justify-content-md-center">
					<div class="col-3">
						<label class="font-weight-normal text-center">Add Signature</label>
						<div class="wrapper" style="margin-bottom: 5px;">
						  <canvas style="border: 1px solid #ced4da;" id="signature-pad" class="signature-pad" width=400 height=200></canvas>
						</div>
						<button type="button" id="clear">Clear</button>
					</div>
				</div>

				<br>
				<div class="row justify-content-md-center">
					<div class="col-3">
						<input type="hidden" name="signature" id="imageUrl">
						<input type="hidden" name="action" value="save_training"/>
						<input id="generate" type="submit" value="Save" class="btn btn-info width100" />
						<br/>
						<br/>
					</div>
				</div>
			</form>
		</div>
		<style type="text/css">
			.select2-results__options {
 	  			max-height: 500px;
			}
		</style>
		<script type="text/javascript">

			var canvas = document.getElementById('signature-pad');	

			function resizeCanvas() {
			    var ratio =  Math.max(window.devicePixelRatio || 1, 1);
			    canvas.width = canvas.offsetWidth * ratio;
			    canvas.height = canvas.offsetHeight * ratio;
			    canvas.getContext("2d").scale(ratio, ratio);
			}

			window.onresize = resizeCanvas;
			resizeCanvas();

			var signaturePad = new SignaturePad(canvas, {
	 			backgroundColor: 'rgb(255, 255, 255)' 
			});

			function get(){
				var data = signaturePad.toDataURL('image/png');
				$("#imageUrl").val(data);
			}

			document.getElementById('clear').addEventListener('click', function () {
			  	signaturePad.clear();
			});
		
			document.getElementById('generate').addEventListener('click', function () {
			  	var data = signaturePad.toDataURL('image/png');
				$("#imageUrl").val(data);
			});

			function addTopic(){
				var newID = parseInt($('#numberChk').val()) + 1;
				var new_input = "<input type='text' placeholder='Topic "+newID+"' id='topic" + newID + "' name='trainig_topic[]' class='form-control mb-1' aria-label='Large' aria-describedby='inputGroup-sizing-sm'>";

				$('#topicTag').append(new_input);
				$('#numberChk').val(newID);

			}

			function addAttendee(){
				var newID = parseInt($('#numberChkAt').val()) + 1;
				var new_input = "<input type='text' placeholder='Attendee "+newID+"' id='attendee" + newID + "' name='traning_attendee[]' class='form-control mb-1' aria-label='Large' aria-describedby='inputGroup-sizing-sm'>";

				$('#attendeeTag').append(new_input);
				$('#numberChkAt').val(newID);

			}
		

			$(".adviser").select2({
			    placeholder: "Select a adviser"
			});

			$(".cpd").select2({
			    placeholder: "Select a topic"
			});
			$(".teamTraining").hide();
			$(".cpdTraining").hide();
			function show(){
				var id = $('#natureTraining :selected').val();
				if(id == 1){
					$(".cpdTraining").show();
					$(".teamTraining").hide();
				}else{
					$(".teamTraining").show();
					$(".cpdTraining").hide();
				}
			}
		</script>
		<style type="text/css">
			.wrapper {
			  position: relative;
			  width: 400px;
			  height: 200px;
			  -moz-user-select: none;
			  -webkit-user-select: none;
			  -ms-user-select: none;
			  user-select: none;
			}

			.signature-pad {
			  position: absolute;
			  left: 0;
			  top: 0;
			  width:400px;
			  height:200px;
			  background-color: white;
			}
			.chkbox{
				font-size: 15px;
			}
		</style>



