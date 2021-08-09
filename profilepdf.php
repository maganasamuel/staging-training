<?php

//include necessary files
include_once("lib/Session.helper.php");
include_once("lib/General.helper.php");
include_once("lib/Training.controller.php");

$session = new SessionHelper();
$app = new GeneralHelper();
$trainingController = new TrainingController();

$idProfile = $app->param($_GET, "id", 0);
$emailID = $app->param($_GET, "email", 0);

$attendedTraining = $trainingController->attendedTraining($idProfile);
$trAttended = "";

$cpdTraining = $trainingController->cpdTraining($idProfile);
$trAttended = "";

$usProfile = $trainingController->getSpecificUser($idProfile);

while ($row = $usProfile->fetch_assoc()) {
	$usName = $row["first_name"] .' '.$row["last_name"];
	$email = $row["email_address"];
	$fsp = $row["ssf_number"];
	$password = $row["password"];
}

while ($row = $attendedTraining->fetch_assoc()) {
    $topic = str_replace(',','<br>', $row["training_topic"]);
    $date = $row["training_date"];
    $newDateTime = date('d M Y h:i A', strtotime($date));

    $trainer = $row["trainer_id"];
   
    $trianerList = $trainingController->getAttendee($trainer);
    while ($row = $trianerList->fetch_assoc()) {
      $trainer_name = $row["first_name"].' '.$row["last_name"];
    }

    $trAttended .= <<<EOF
    <tr>
      <td>{$topic}</td>
      <td>{$newDateTime}</td>
      <td>{$trainer_name}</td>
    </tr>
EOF;
}

$cpdList = "";
while ($row = $cpdTraining->fetch_assoc()) {
    $topic = str_replace(',','<br>', $row["training_topic"]);
    $date = $row["training_date"];
    $newDateTime = date('d M Y h:i A', strtotime($date));

    $trainer = $row["trainer_id"];
   
    $trianerList = $trainingController->getAttendee($trainer);
    while ($row = $trianerList->fetch_assoc()) {
      $trainer_name = $row["first_name"].' '.$row["last_name"];
    }

    $cpdList .= <<<EOF
    <tr>
      <td>{$topic}</td>
      <td>{$newDateTime}</td>
      <td>{$trainer_name}</td>
    </tr>
EOF;
}


$modTraining = $trainingController->getModularTraining($emailID);
$modList = "";

$topic = '';
$module_taken = '';
$score = '';
$result = '';

while ($row = $modTraining->fetch_assoc()) {
  $topic = $row["set_name"];
  $module_taken = $row["date_took"];
  $score = $row["score"];
  $maxScore = $row["max_score"];
  $result = "<span style='color: red'>FAILED</span>";
  $attempts = $row["attempts"];

  //score
  $score = (($score / $maxScore) * 100);
  $score = number_format((float) $score, 2, '.', '');

  if($score >= 80)
    $result = "<span style='color: green'>PASSED</span>";

  $modList .= '
    <tr>
      <td>'.$topic.'</td>
      <td>'.$module_taken.'</td>
      <td>'.$score.'%</td>
      <td>'.$result.'</td>
      <td>'.$attempts.'</td>
    </tr>';
  
}

if($modList == "")
	$modList = '
<tr>
	<td colspan="5">No data available.</td>
</tr>
';

if($cpdList == "")
	$cpdList = '
<tr>
	<td colspan="3">No data available.</td>
</tr>
';

if($trAttended == "")
	$trAttended = '
<tr>
	<td colspan="3">No data available.</td>
</tr>
';

$html = 
'<style>
	.table-head {
		border: 1px solid #dddddd;
	  font-family: arial, sans-serif;
	  font-size: 10px;
	  border-collapse: collapse;
	  width: 100%;
	}

	.table-head td, th {
	  text-align: left;
	  padding: 8px;
	}

	.table-head tr:nth-child(even) {
	  background-color: #dddddd;
	}
</style>

<div>
	<table class="table-head">
		<tr>
			<th colspan="4">Adviser Information</th>
		</tr>
		<tr>
			<td>Adviser Name:</td>
			<td>'.$usName.'</td>
			<td>Financial Advice Provider Name:</td>
			<td>Eliteinsure Limited</td>
		</tr>
		<tr>
			<td>FSP Number:</td>
			<td>'.$fsp.'</td>
			<td>FAP FSP Number:</td>
			<td>706272</td>
		</tr>
		<tr>
			<td>Email Address:</td>
			<td>'.$email.'</td>
			<td>FAP Email Address::</td>
			<td>admin@eliteinsure.co.nz</td>
		</tr>
		<tr>
			<td>Physical Address:</td>
			<td colspan="3">3G/39 Mackelvie Street, Grey Lynn, Auckland, 1021, New Zealand</td>
		</tr>
	</table>
	
	<br><br>
	
	<table class="table-head" width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<th colspan="4">Modular Training</th>
		</tr>
		<tr>
			<th>Topics Trained On</th>
			<th>Module Take</th>
			<th>Score</th>
			<th>Results</th>
			<th>No. of Attempts</th>
		</tr>
		'.$modList.'
	</table>
	
	<br><br>
	
	<table class="table-head" width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<th colspan="4">Continuing Professional Development Course</th>
		</tr>
		'.$cpdList.'
	</table>

	<br><br>
	
	<table class="table-head" width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<th colspan="4">Team Training Course</th>
		</tr>
		'.$trAttended.'
	</table>
</div>';

$htmlHeader = '<div style="position:absolute;top:0.26in;left:0in;width:90px;line-height:0.27in; background-color: #455a73;height:70px;">
			    <span style="background-colro:red"></span>
			</div>

			<div style="position:absolute;top:0.18in;left:1.20in;width:4.36in;line-height:0.27in;">
			  <img src="img/elitelogo.png" alt="eliteinsure" class="logo" width="100"/>
			</div>

			<div style="position:absolute;top:0.72in;left:4.4in;width:4.36in;line-height:0.27in;">
			  <span style="font-style:normal;font-weight:bold;font-size:15pt;font-family:Calibri;color:#44546a">ADVISER TRAINING REPORT</span>
			</div>

			<div style="position:absolute;top:0.26in;left:7.4in;width:90px;line-height:0.27in; background-color: #1881c7;height:70px;">
			    <span style="background-colro:red"></span>
			</div>';

$htmlFooter = <<<EOF
  <div class="footer" style="font-size:6pt;"> 
    <img src="img/logo.png" alt="eliteinsure" class="logo" width="200"/>
  </div>
EOF;

$mpdf = new \Mpdf\Mpdf();
ob_clean();
$mpdf->AddPage('P','','','','',24,22,34,25,10,10);
$mpdf->SetHTMLHeader($htmlHeader,'',true);
$mpdf->SetHTMLFooter($htmlFooter);
$mpdf->WriteHTML($html);
$mpdf->Output('Adviser Training Report.pdf', "I"); 
?>