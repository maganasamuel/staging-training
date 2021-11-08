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

$cpdTraining = $trainingController->cpdAttended($idProfile);
$trAttended = "";
$rows = '';

$usProfile = $trainingController->getSpecificUser($idProfile);

while ($row = $usProfile->fetch_assoc()) {
	$usName = $row["first_name"] .' '.$row["last_name"];
	$email = $row["email_address"];
	$fsp = $row["ssf_number"];
	$password = $row["password"];
  $usType = $row['id_user_type'];
}

while ($row = $usProfile->fetch_assoc()) {
  $usName = $row["first_name"] .' '.$row["last_name"];
  $email = $row["email_address"];
  $fsp = $row["ssf_number"];
  $password = $row["password"];
  $usType = $row['id_user_type'];
}

while ($row = $attendedTraining->fetch_assoc()) {
    $topic = str_replace(',', '<br>', $row['training_topic']);
    $date = $row['training_date'];
    $trainerID = $row['trainer_id'];
    $newDateTime = date('d-m-Y h:i A', strtotime($date));

    if ('' == $row['host_name']) {
        $trainer = $row['fullname'];
    } else {
        $trainer = $row['host_name'];
    }
    $trainingID = $row['training_id'];
    $today = new DateTime();
    $status = '';

    $datasetRow = $trainingController->getTrainingTopic($idProfile, '', $trainingID);
    $trow = '';
    $topicTitle = '';
    while ($trow = $datasetRow->fetch_assoc()) {
        $level = '';

        if ('0' == $trow['topic_level']) {
            $level = '(Marketing)';
        } elseif ('1' == $trow['topic_level']) {
            $level = '(Product)';
        } elseif ('' == $trow['topic_level']) {
            $level = '';
        } else {
            $level = '(Compliance)';
        }
        $topicTitle .= $trow['topic_title'] . ' ' . $level . '<br>';
    }
    $rows .= <<<EOF
      <tr>
        <td>{$newDateTime}</td>
        <td class="capitalize">{$topicTitle}</td>
        <td>{$trainer}</td>
      </tr>
      EOF;
}

$cpdList = "";
    while ($row = $cpdTraining->fetch_assoc()) {
    $topic = str_replace(',','<br>', $row["training_topic"]);
    $date = $row["training_date"];
    $trainerID = $row['trainer_id'];
    $newDateTime = date('d-m-Y h:i A', strtotime($date));
   
    if($row['host_name'] == ""){
    		$trainer = $row["fullname"];
    	}else{
    		$trainer = $row["host_name"];
    	}
   
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
      }elseif($trow['topic_level'] == "3"){
          $level = '(Operations)';
      }elseif($trow['topic_level'] == ""){
        $level = '';
      }else{
        $level = '(Compliance)';
      }
      $topicTitle .= $trow['topic_title'] .' '. $level . '<br>'; 
    }


    
        $cpdList .= <<<EOF
    <tr>
      <td class="capitalize">{$topicTitle}</td>
      <td>{$newDateTime}</td>
      <td>{$trainer}</td>
    </tr>

EOF;
}


$dataset = $trainingController->alltimehour($idProfile);

//Eliteinsure Company Pointing Variable
$alltimehour = 0;
$minute = 0;

//Year Eliteinsure Company Pointing Variable
$yalltimehour = 0;
$yminute = 0;



//External Company Pointing
$hourtime = 0;
$minutetime = 0;

//Year External Company Pointing
$yhourtime = 0;
$yminutetime = 0;

//Final Computation
$allminute = 0;
$alltime = 0;
$totalPoints  = 0;

//Year Final Computation
$yallminute = 0;
$yalltime = 0;
$ytotalPoints  = 0;

$getTotalMinutes = 0;
$ygetTotalMinutes = 0;

$ygetMaxMinutes = 0;
$getMaxMinutes = 0;

$pointsEx = 0;
foreach($dataset as $row) {
      

    if( $row['id_user_type'] == 8 || $row['id_user_type'] == 7 && $row['comp_name'] == "" || $row['comp_name'] == 'Eliteinsure Limited' && $row['hour'] ){
        $alltimehour += $row['hour'];
        $minute += $row['minute'];

        if(in_array($row['id_user_type'], [7, 8])){
            $getTotalMinutes +=  $row['hour'] / 4;
            $getMaxMinutes += $row['minute'] / 240;
        }else{
            $getTotalMinutes += $row['hour'] / 2;
            $getMaxMinutes += $row['minute'] / 120;
        }

        $alltime = $alltimehour;
        $allminute = $minute;

   }elseif ($row['id_user_type'] == 1 && $row['comp_name'] != "Eliteinsure Limited" && $row['comp_name'] != "") {

        $alltimehour  += $row['hour'];
        $minute  += $row['minute'];

        $alltime = $alltimehour;

        $pointsEx = $row['hour'] * 60 + $row['minute'];

        $totalPoints += $pointsEx / 60;

        $allminute = $minute;

   }
   
if($row['year_date'] == date("Y")){
    if($row['id_user_type'] == 8 || $row['id_user_type'] == 7 && $row['comp_name'] == "" || $row['comp_name'] == 'Eliteinsure Limited'){
        
        $yalltimehour += $row['hour'];
        $yminute += $row['minute'];

        if(in_array($row['id_user_type'], [7, 8])){
            $ygetTotalMinutes +=  $row['hour'] / 4;
            $ygetMaxMinutes += $row['minute'] / 240;
        }else{
            $ygetTotalMinutes += $row['hour'] / 2;
            $ygetMaxMinutes += $row['minute'] / 120;
        }        

        $yalltime = $yalltimehour;
        $yallminute = $yminute;

    }elseif ($row['id_user_type'] == 1 && $row['comp_name'] != "Eliteinsure Limited" && $row['comp_name'] != "") {

        $yalltimehour  += $row['hour'];
        $yminute  += $row['minute'];


        $yalltime = $yalltimehour;


        $pointsExY = $row['hour'] * 60 + $row['minute'];

        $ytotalPoints += $pointsExY / 60;

        $yallminute = $yminute;

    }
 }
}


$totalPoints += $getTotalMinutes + $getMaxMinutes;
$ytotalPoints += $ygetTotalMinutes + $ygetMaxMinutes;

$min = $allminute % 60;
$miny = $yallminute % 60;

$yalltime += floor($allminute / 60); 
$alltime += floor($yallminute / 60); 
$alltime .= '.' . $min;
$yalltime .= '.' . $miny;




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

if(!in_array($usType, [2,7,8])){
    $name = "Name:";
    $nameType = "Contractor/Employee";
    $title = '<div style="position:absolute;top:0.72in;left:2.7in;width:5.36in;line-height:0.27in;">
        <span style="font-style:normal;font-weight:bold;font-size:15pt;font-family:Calibri;color:#44546a">CONTRACTOR/EMPLOYEE TRAINING REPORT</span>
      </div>';
}else{
    $name = "Adviser Name:";
    $nameType = "Adviser";
    $title = '  <div style="position:absolute;top:0.72in;left:4.4in;width:4.36in;line-height:0.27in;">
        <span style="font-style:normal;font-weight:bold;font-size:15pt;font-family:Calibri;color:#44546a">ADVISER TRAINING REPORT</span>
      </div>';
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
			<th colspan="4">'.$nameType.' Information</th>
		</tr>
		<tr>
			<td>'.$name.'</td>
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
      <th colspan="4">Personal Development Program</th>
    </tr>
    '.$cpdList.'
  </table>

  <br><br>

    <table class="table-head points" width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <th colspan="4">Continuing Professional Development</th>
    </tr>
    <tr>
      <th>Hours(Total): '.number_format((float) $alltime, 2, '.', '').'</th>
      <th>Points(Total): '.$totalPoints.'</th>
      <th>Hours(Current Year): '.number_format((float) $yalltime, 2, '.', '').'</th>
      <th>Points(Current Year): '.$ytotalPoints.'</th>
    </tr>
    '.$rows.'
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
	





</div>';

$htmlHeader = '<div style="position:absolute;top:0.26in;left:0in;width:90px;line-height:0.27in; background-color: #455a73;height:70px;">
			    <span style="background-colro:red"></span>
			</div>

			<div style="position:absolute;top:0.18in;left:1.20in;width:4.36in;line-height:0.27in;">
			  <img src="img/elitelogo.png" alt="eliteinsure" class="logo" width="100"/>
			</div>

		  '.$title.'

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