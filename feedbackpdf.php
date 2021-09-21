<?php
/**
@name: test_mail.php
@author: Gio
@desc:
  handles both PDF and emailing functionality of the system.
*/
//include necessary files

include_once("lib/General.helper.php");
include_once("lib/Training.controller.php");

$app = new GeneralHelper();
$trainingController = new TrainingController();

$idTrain = $app->param($_GET, "id", 0);
$dataset = $trainingController->getTrainingDetail($idTrain);

$uTopic = $trainingController->getTopic($idTrain);

$dataTopicTitle = array();
$dataTopicLevel = array();

foreach($uTopic as $row) {
    array_push($dataTopicTitle,$row["topic_title"]);
    array_push($dataTopicLevel,$row["topic_level"]);
}

$topiclist = '';
$hostName = '';
  while ($row = $dataset->fetch_assoc()) {
    $trainingTopic = $row["training_topic"];
    $trainingDate = $row["training_date"];
    $newDateTime = date('d-m-Y h:i A', strtotime($trainingDate));
    $trainingVenue = $row["training_venue"];
    $attendee = $row["training_attendee"];
    $trainerSignature = $row["trainer_signiture"];
    $trainerID = $row["trainer_id"];
    $hostName = $row["host_name"];
    $compName = $row["comp_name"];
  }

$fullnameTrainer = "";
$emailTrainer = "";
$trainerName = $trainingController->getAttendee($trainerID);
  while ($row = $trainerName->fetch_assoc()) {
        $fullnameTrainer = $row["first_name"].' '.$row["last_name"];
        $emailTrainer = $row["email_address"];
  }

$div = "";
$ctr = 0;
for($i = 0; $i< count($dataTopicTitle); $i++) {
        if($dataTopicLevel[$i] == "0"){
          $levelText = ' (Marketing)';
        }elseif($dataTopicLevel[$i] == "1"){
         $levelText = ' (Product)';
        }elseif($dataTopicLevel[$i] == "2"){
          $levelText = ' (Compliance)';
        }
    $ctr = $ctr +1;
    $div .= '<tr><td style="font-size:12x;">'.$ctr.'. '.$dataTopicTitle[$i] . $levelText. '</td></tr>';
}

$improvements = $trainingController->getFeedback($idTrain);
$implist = array();

foreach($improvements as $row) {
    array_push($implist,$row["improvement"]);
}

$div1 = "";
$ctr1 = 0;
for($i = 0; $i< count($implist); $i++) {
    $ctr1 = $ctr1 +1;
    $div1 .= '<tr><td width="1%" style="text-align:justify;vertical-align:top">'.$ctr1.'.</td><td width="100%" style="font-size:12x; text-align:justify">'.$implist[$i].'</td></tr>';
}

if($hostName != ''){
  $fullnameTrainer = $hostName;
}else{
  $fullnameTrainer = $fullnameTrainer;
}

$total = $trainingController->getFeedback($idTrain);
$participants = $trainingController->participants($idTrain);


$strongly1  = $disagree1 = $neutral1 = $agree1 = $sagree1 = "";
$strongly2  = $disagree2 = $neutral2 = $agree2 = $sagree2 = "";
$strongly3  = $disagree3 = $neutral3 = $agree3 = $sagree3 = "";
$strongly4  = $disagree4 = $neutral4 = $agree4 = $sagree4 = "";
$strongly5  = $disagree5 = $neutral5 = $agree5 = $sagree5 = "";

while ($row = $total->fetch_assoc()) {
      $data = explode(",",$row['first_question']);
        if($data[0] === "true"){
           $strongly1 = (int)$strongly1 + 1;
        }
        if($data[1] === "true"){
           $disagree1 = (int)$disagree1 + 1;
        }
        if($data[2] === "true"){
           $neutral1 = (int)$neutral1 + 1;
        }
        if($data[3] === "true"){
           $agree1 = (int)$agree1 + 1;
        }
        if($data[4] === "true"){
           $sagree1 = (int)$sagree1 + 1;
        }

        $data = explode(",",$row['second_question']);

        if($data[0] === "true"){
           $strongly2 = (int)$strongly2 + 1;
        }
        if($data[1] === "true"){
           $disagree2  = (int)$disagree2 + 1;
        }
        if($data[2] === "true"){
           $neutral2 = (int)$neutral2 + 1;
        }
        if($data[3] === "true"){
           $agree2 = (int)$agree2 + 1;
        }
        if($data[4] === "true"){
           $sagree2 = (int)$sagree2 + 1;
        }

        $data = explode(",",$row['third_question']);
        if($data[0] === "true"){
           $strongly3 =(int)$strongly3 + 1;
        }
        if($data[1] === "true"){
           $disagree3 =(int)$disagree3 + 1;
        }
        if($data[2] === "true"){
           $neutral3 =(int)$neutral3 + 1;
        }
        if($data[3] === "true"){
           $agree3 =(int)$agree3 + 1;
        }
        if($data[4] === "true"){
           $sagree3 =(int)$sagree3 + 1;
        }

        $data = explode(",",$row['fourth_question']);
        if($data[0] === "true"){
           $strongly4 =(int)$strongly4 + 1;
        }
        if($data[1] === "true"){
           $disagree4 =(int)$disagree4 + 1;
        }
        if($data[2] === "true"){
           $neutral4 =(int)$neutral4 + 1;
        }
        if($data[3] === "true"){
           $agree4 =(int)$agree4 + 1;
        }
        if($data[4] === "true"){
           $sagree4 =(int)$sagree4 + 1;
        }

        $data = explode(",",$row['fifth_question']);
        if($data[0] === "true"){
           $strongly5 =(int)$strongly5 + 1;
        }
        if($data[1] === "true"){
           $disagree5 =(int)$disagree5 + 1;
        }
        if($data[2] === "true"){
           $neutral5 =(int)$neutral5 + 1;
        }
        if($data[3] === "true"){
           $agree5 =(int)$agree5 + 1;
        }
        if($data[4] === "true"){
           $sagree5 =(int)$sagree5 + 1;
        }
};

//$strongly1  = $disagree1 = $neutral1 = $agree1 = $sagree1 = "";

$total1 = $total2 = $total3 = $total4 = $total5 = 0;

$total1 = ((int)$strongly1*1) + ((int)$disagree1*2) + ((int)$neutral1*3) + ((int)$agree1*4) + ((int)$sagree1*5);
$total2 = ((int)$strongly2*1) + ((int)$disagree2*2) + ((int)$neutral2*3) + ((int)$agree2*4) + ((int)$sagree2*5);
$total3 = ((int)$strongly3*1) + ((int)$disagree3*2) + ((int)$neutral3*3) + ((int)$agree3*4) + ((int)$sagree3*5);
$total4 = ((int)$strongly4*1) + ((int)$disagree4*2) + ((int)$neutral4*3) + ((int)$agree4*4) + ((int)$sagree4*5);
$total5 = ((int)$strongly5*1) + ((int)$disagree5*2) + ((int)$neutral5*3) + ((int)$agree5*4) + ((int)$sagree5*5);
$totalscore =  $total1 + $total2 + $total3 + $total4 + $total5;
$scorep = ($participants*5)*5;

$percentage = $totalscore/$scorep*100;

$percent = number_format((float)$percentage, 2, '.', '');

require_once __DIR__ . '/package/vendor/autoload.php';

$html = <<<EOF
<html>
<head>
  <style type="text/css">
    tr td{
      padding: none;
      border:none;
      font-size:12px;
    }
    table {
       width:100%;
    }
   body {
      font-family: Trebuchet MS, sans-serif
    }

  .feedback{
    border:1px solid black;
      border-spacing: 0;

  }
  .feedback tr td{
    border: 1px solid black;
    padding: 10px;
    font-size: 12px;
    text-align: center;
  }

  .feedback tr th{
    color:white;
    font-size: 12px;
    padding:10px;
  }
  </style>
</head>
<body style="font-family: Trebuchet MS, sans-serif  ">


<div style="position:absolute;top:0.26in;left:0in;width:90px;line-height:0.27in; background-color: #455a73;height:70px;">
    <span style="background-colro:red"></span>
</div>

<div style="position:absolute;top:0.18in;left:1.20in;width:4.36in;line-height:0.27in;">
  <img src="img/elitelogo.png" alt="eliteinsure" class="logo" width="100"/>
</div>

<div style="position:absolute;top:0.72in;left:2.98in;width:4.86in;line-height:0.27in;">
  <span style="font-style:normal;font-weight:bold;font-size:15pt;font-family:Calibri;color:#44546a">MEETING/TRAINING FEEDBACK SUMMARY</span>
</div>

<div style="position:absolute;top:0.26in;left:7.4in;width:90px;line-height:0.27in; background-color: #1881c7;height:70px;">
    <span style="background-colro:red"></span><br><br><br>
</div>
<div style="margin-top: 310px;">&nbsp;</div>
<table style="position: absolute; margin-top: 200px" cellspacing="13">
  <tr>
    <td style="font-size:25px; text-align:right;">Trainer:</td>
    <td width="250px" style="font-size: 25px; text-align:left;">{$fullnameTrainer}</td>
    <td width="150px"></td>
    <td width="150px"></td>
    <td width="150px"></td>
    <td width="280px" style="font-size:25px; text-align:left;">Survery Participants:&nbsp;{$participants}</td>
    <td></td>
  </tr>
  <tr>
    <td style="font-size: 25px;"> Venue:</td>
    <td width="250px" colspan="3" style="font-size: 25px; text-align:left;">{$trainingVenue}</td>
    <td width="150px"></td>
    <td style="font-size: 25px;" colspan="5">Date:&nbsp;<span style="font-size: 25px;" >{$newDateTime}</span></td>
    <td width="250px"></td>
  </tr>
</table>
<table style="position: absolute; margin-top: 1px" cellspacing="6">
  <tr>
    <td style="font-size:12px; text-align:left;">Topics:</td>
  </tr>
  {$div}
</table>

 <table class="table feedback" style="margin-top: 18px">
          <thead class="text-center">
            <tr>
              <th colspan="7" style="font-size:15px; padding: 20px; background-color:#36465c; color:white;">Feedback Re Meeting/Training(Overall Summary) </th>
            </tr>
            <tr style="background-color:#8a8a8a;">
              <th>Statement</th>
              <th>Strongly Disagree (1)</th>
              <th>Disagree (2)</th>
              <th>Neutral (3)</th>
              <th>Agree (4)</th>
              <th>Strongly Agree (5)</th>
              <th>Score</th>
            </tr>
          </thead>
            <tbody>
                <tr>
                    <td style="width: 400px; text-align: left;">I am able to achieve learning outcomes.</td>
                    <td>{$strongly1}</td>
                    <td>{$disagree1}</td>
                    <td>{$neutral1}</td>
                    <td>{$agree1}</td>
                    <td>{$sagree1}</td>
                    <td>{$total1}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">The trainer is very effective in his/her delivery.</td>
                    <td>{$strongly2}</td>
                    <td>{$disagree2}</td>
                    <td>{$neutral2}</td>
                    <td>{$agree2}</td>
                    <td>{$sagree2}</td>
                    <td>{$total2}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">The content is relevant to me.</td>
                    <td>{$strongly3}</td>
                    <td>{$disagree3}</td>
                    <td>{$neutral3}</td>
                    <td>{$agree3}</td>
                    <td>{$sagree3}</td>
                    <td>{$total3}</td>
                </tr>
                <tr>
                    <td style="text-align: left;">The training was pitched in a level that I can understand.</td>
                    <td>{$strongly4}</td>
                    <td>{$disagree4}</td>
                    <td>{$neutral4}</td>
                    <td>{$agree4}</td>
                    <td>{$sagree4}</td>
                    <td>{$total4}</td>
                </tr>
                 <tr>
                    <td style="text-align: left;">The trainer is very efficient in using learning materials.</td>
                    <td>{$strongly5}</td>
                    <td>{$disagree5}</td>
                    <td>{$neutral5}</td>
                    <td>{$agree5}</td>
                    <td>{$sagree5}</td>
                    <td>{$total5}</td>
                </tr>
                <tr>
                  <td colspan="6" style="background-color:#8a8a8a; color:white; font-weight:bold;">Total Score</td>
                  <td style="background-color:#36465c; color:white; font-weight:bold;">{$totalscore}<br>{$percent}%</td>
                </tr>
            </tbody>
        </table>

<div class="points" style="margin-top: 20px">
  <span style="font-size:12px;">Points for improvements:</span>
</div>
<table class="improvement" style="margin-left: 10px; position: absolute;" cellspacing="6">
  {$div1}
</table>
</div>
</body>
</html>

EOF;

$htmlFooter = <<<EOF
<div class="footer" style="font-size:6pt; margin-top:50px;">
  <p style="font-size:9px;; text-align: justify; font-family: calibri;">Disclaimer: Eliteinsure has used reasonable endeavours to ensure the accuracy and completeness of the information provided but makes no warranties as to the accuracy or completeness of such information. The information should not be taken as advice. Eliteinsure accepts no responsibility for the results of any omissions or actions taken on basis of this information. This report includes commercially sensitive information. Accordingly, it may be used for the purpose provided; may not be disclosed to any third party; and will be subject to any obligation of confidence owed by the recipient under contract or otherwise.</p><br>
    <img src="img/logo.png" alt="eliteinsure" class="logo" width="200"/>
  </div>
EOF;

$mpdf = new \Mpdf\Mpdf();
ob_clean();
$mpdf->setAutoBottomMargin = 'stretch';
$mpdf->SetHTMLFooter($htmlFooter);
$mpdf->WriteHTML($html);
$mpdf->Output('Training Record.pdf', "I");


?>









