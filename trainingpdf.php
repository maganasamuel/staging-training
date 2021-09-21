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

$config = parse_ini_file('lib/class/conf/conf.ini');

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


$arrAttendee =  explode(',',$attendee);

$date = date("d-m-Y");

$divAttendee = "";
$crtAttendee = 0;

for($i = 0; $i< count($arrAttendee); $i++) {

  $data = $trainingController->getAttendee($arrAttendee[$i]);
  while ($row = $data->fetch_assoc()) {


    $firstName = $row["first_name"].' '.$row["last_name"];
    $emailAddress = $row["email_address"];
    $crtAttendee = $crtAttendee +1;

    $divAttendee .= '<div class="column">
                        <div style="margin-left: 72px;border-bottom: 1px solid #000; bottom:23px;">
                          <span style="font-style:normal;font-weight:normal;font-size:10pt;font-family:Calibri;color:#000000;"> '.$crtAttendee.'. </span> <span style="width: 200px; border-width: thin;font-style:normal;font-weight:normal;font-size:10pt;font-family:Calibri;color:#000000;">
                        '.$firstName.'
                          </span>
                          </div>
                    </div>
                    <div class="column">
                        <div style="text-align:center;margin-left: 72px;border-bottom: 1px solid #000; bottom:23px;">
                          <span style="width: 200px; border-width: thin;font-style:normal;font-weight:normal;font-size:11pt;font-family:Calibri;color:#000000;">
                        '.$emailAddress.'
                          </span>
                          </div>
                    </div>';
  }

}


for($i = 0; $i< count($dataTopicTitle); $i++) {
        if($dataTopicLevel[$i] == "0"){
          $levelText = ' (Marketing)';
        }elseif($dataTopicLevel[$i] == "1"){
         $levelText = ' (Product)';
        }elseif($dataTopicLevel[$i] == "2"){
          $levelText = ' (Compliance)';
        }
    $ctr = $ctr +1;
    $div .= '<div class="column">
                  <div style="margin-left: 72px;border-bottom: 1px solid #000; bottom:23px;"> <span style="font-style:normal;font-weight:normal;font-size:10pt;font-family:Calibri;color:#000000;"> '.$ctr.'. </span> <span style="width: 200px; border-width: thin;font-style:normal;font-weight:normal;font-size:10pt;font-family:Calibri;color:#000000;">
                    '.$dataTopicTitle[$i] . $levelText .'
                  </span></div>
              </div>';
          }

$attestText = '';
$compText ='';
if($hostName != ''){
  $attestText = 'This is to attest that';
  $compText = $compName;
  $textbuild = '<div class="trainer" style="position:absolute;top:2.23in;left:1.36in;width:8.86in;line-height:0.17in;"><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">'.$attestText.'
</div>

<div class="trainer" width="220" style="text-align:center;border-bottom: 1px solid #000;position:absolute;top:2.23in;left:2.66in;line-height:0.17in;">
  <span style="font-style:normal;font-weight:normal;font-size:11pt;font-family:Calibri;color:#000000">'.$hostName.'</span>
</div>

<div class="trainer" width="220" style="text-align:center;position:absolute;top:2.23in;left:3.95in;line-height:0.17in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">of</span>
</span>
</div>

<div class="trainer" width="220" style="text-align:center;border-bottom: 1px solid #000;position:absolute;top:2.23in;left:5.2in;line-height:0.17in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">'.$compText.'</span>
</span>
</div>


<div style="position:absolute;top:2.53in;left:1.36in;width:6.90in;line-height:0.17in;">
  <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000"></span>
  <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000"> has conducted a training/meeting on </span>
  </div>

<div class="trainer" width="150" style="text-align:center;border-bottom: 1px solid #000;position:absolute;top:2.52in;left:3.53in;line-height:0.17in;">
  <span style="font-style:normal;font-weight:normal;font-size:11pt;font-family:Calibri;color:#000000">'.$newDateTime.'</span>
</div>

<div class="trainer" width="150" style="position:absolute;top:2.52in;left:5.15in;line-height:0.17in;">
  <span style="font-style:normal;font-weight:normal;font-size:11pt;font-family:Calibri;color:#000000">at</span>
</div>

<div class="trainer" width="150" style="text-align:center;border-bottom: 1px solid #000;position:absolute;top:2.52in;left:5.33in;line-height:0.17in;">
  <span style="font-style:normal;font-weight:normal;font-size:11pt;font-family:Calibri;color:#000000">'.$trainingVenue.'</span>
</div> ';

$textbuildAdivser = '<div class="trainer" style="position:absolute;top:2.43in;left:1.36in;width:8.86in;line-height:0.17in;"><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">This is to confirm you have attended the meeting/training that was conducted by '.$hostName.'<br>on '.$newDateTime.' at '.$trainingVenue.'.
</div> ';

$divsig = '<div style="margin-left: 480px;left:5.36in;width:7.93in;">
  <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">'.$fullnameTrainer.'</span>
  <br/>
</div>';

}else{

  $textbuild = '<div class="trainer" style="position:absolute;top:2.23in;left:1.36in;width:8.86in;line-height:0.17in;"><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">This is to attest that I,
</div>

<div class="trainer" width="220" style="text-align:center;border-bottom: 1px solid #000;position:absolute;top:2.23in;left:2.66in;line-height:0.17in;">
<span style="font-style:normal;font-weight:normal;font-size:11pt;font-family:Calibri;color:#000000">'.$fullnameTrainer.'</span>
</div>

<div style="position:absolute;top:2.23in;left:4.9in;width:8.86in;line-height:0.17in;">
<span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000"> an ADR/SADR of Eliteinsure Limited has conducted</span>
</span>
</div>


<div style="position:absolute;top:2.53in;left:1.36in;width:6.90in;line-height:0.17in;">
  <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">a</span>
  <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">training/meeting on </span>
  </div>

<div class="trainer" width="150" style="text-align:center;border-bottom: 1px solid #000;position:absolute;top:2.52in;left:2.63in;line-height:0.17in;">
  <span style="font-style:normal;font-weight:normal;font-size:11pt;font-family:Calibri;color:#000000">'.$newDateTime.'</span>
</div>

<div class="trainer" width="150" style="position:absolute;top:2.52in;left:4.25in;line-height:0.17in;">
  <span style="font-style:normal;font-weight:normal;font-size:11pt;font-family:Calibri;color:#000000">at</span>
</div>

<div class="trainer" width="150" style="text-align:center;border-bottom: 1px solid #000;position:absolute;top:2.52in;left:4.43in;line-height:0.17in;">
  <span style="font-style:normal;font-weight:normal;font-size:11pt;font-family:Calibri;color:#000000">'.$trainingVenue.'</span>
</div>';

$textbuildAdivser = '<div class="trainer" style="position:absolute;top:2.43in;left:1.36in;width:8.86in;line-height:0.17in;"><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">This is to confirm you have attended the meeting/training
 that I have conducted on '.$newDateTime.' <div class="trainer" style="position:absolute;top:in;left:1.36in;width:8.86in;line-height:0.17in;"><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">
   at '.$trainingVenue.'.
</div>

</div> ';

$divsig = '<div style="margin-left: 480px;left:5.36in;width:7.93in;">
  <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">ADR/SADR Signature</span>
  <br/>
</div>';


}

$html = <<<EOF
<!DOCTYPE">
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css"/>
<style>

.column {
  float: left;
  width: 45%;
  padding: 10px;

  }

/* Clear floats after the columns */
  content: "";
  display: table;
  clear: both;
}
.trainer span span{
   width: 140px;
}
 table {
  table-layout: fixed ;
  width: 100% ;
  text-align:left;
  margin-left: 70px;
}
td{
  width: 50%;
}
</style>
</head>
<body>

<div style="position:absolute;top:0.26in;left:0in;width:90px;line-height:0.27in; background-color: #455a73;height:70px;">
    <span style="background-colro:red"></span>

</div>

<div style="position:absolute;top:0.18in;left:1.20in;width:4.36in;line-height:0.27in;">
  <img src="img/elitelogo.png" alt="eliteinsure" class="logo" width="100"/>
</div>

<div style="position:absolute;top:0.72in;left:3.48in;width:4.36in;line-height:0.27in;">
  <span style="font-style:normal;font-weight:bold;font-size:15pt;font-family:Calibri;color:#44546a">ATTESTATION RE MEETING/TRAINING</span>
</div>

<div style="position:absolute;top:0.26in;left:7.4in;width:90px;line-height:0.27in; background-color: #1881c7;height:70px;">
    <span style="background-colro:red"></span>

</div>


<div style="position:absolute;top:1.46in;left:1.36in;width:2.05in;line-height:0.17in;">
<br/>
  <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">DATE:
  <div style="text-align:center; margin-top: -18px; margin-left:40px; border-bottom: 1px solid #000;">
      <span style="font-style:normal;font-weight:normal;font-size:11pt;font-family:Calibri;color:#000000">{$date}</span>
  </div>
  </span>
  <br/>
</div>

{$textbuild}

<div style="position:absolute;top:2.96in;left:1.36in;">
  <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">The topics that were discussed/trained to the attendee/s were:  </span><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000"></span>
  <br/>
</div>


<div class="row"><br><br><br><br><br><br><br><br><br><br><br><br><br>
   {$div}
</div>


<div style="position:absolute;margin-top: 10px;left:1.36in;width:3.48in;line-height:0.17in;"><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">The attendee/s to the said training/meeting is/are:</span>
</div>


<div style="position:absolute;left:2.18in;width:1.24in;line-height:0.17in;"><br><br><br>
  <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Adviser Names</span>

</div>

<div style="position:absolute;left:6.00in;width:0.83in;line-height:0.17in;"><br><br><br>
 <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Email</span>
</div>

<div class="row" style="top:3.23in;left:1.36in;left:1.36in;"><br><br><br><br>
   {$divAttendee}
</div>
<br>
<div style="margin-left: 72px;left:1.36in;width:6.86in;line-height:0.17in;">
  <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Finally, this is to certify that the information enclosed and disclosed in this form and any attached</span>

</div>
<div style="margin-left: 72px;left:1.36in;width:6.86in;line-height:0.17in;">
  <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">documents are true and correct and any incorrect or false statement placed herein shall render this</span>
</div>

<div style="margin-left: 72px;left:1.36in;width:1.31in;line-height:0.17in;">
  <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">attestation invalid.</span>
  </div>

<div style="margin-left: 470px;left:4.86in;width:7.93in;">
  <img src="{$trainerSignature}" alt="eliteinsure" class="logo" width="200"/>
</div>

<div style="margin-top: -40px;margin-left: 400px;left:4.56in;width:7.93in;">
  <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">_______________________________________________</span>
</div>

{$divsig}

<div class="footer" style="font-size:6pt;>
    <img src="img/logo.png" alt="eliteinsure" class="logo" width="200"/>
  </div>
</body>
</html>


EOF;

$htmlFooter = <<<EOF
  <div class="footer" style="font-size:6pt;">
    <img src="img/logo.png" alt="eliteinsure" class="logo" width="200"/>
  </div>
EOF;



require_once __DIR__ . '/package/vendor/autoload.php';
$download = $_GET['download'];
$mail = $_GET['mail'];
$mpdf = new \Mpdf\Mpdf();
ob_clean();
$mpdf->WriteHTML($html);
$mpdf->SetHTMLFooter($htmlFooter);

if(isset($_GET['mail'])) {

      $content = $mpdf->Output('', 'S');
      $attachment = (new Swift_Attachment($content,'Training Record', 'application/pdf'));

      $message = new Swift_Message();
      $message->setSubject('Training Record');
      //$message->setFrom(array('executive.admin@eliteinsure.co.nz' => 'EliteInsure'));
      //Remove the venue at the certificate.
      //Move date to footer.

      $message->setFrom(array('executive.admin@eliteinsure.co.nz' => 'EliteInsure'));
      $message->setTo($emailTrainer);

      $message->setBody('Please see attached file');


      $message->attach($attachment);

      //$message->setBcc(array('admin@eliteinsure.co.nz' => 'Admin'));
      $transport = (new Swift_SmtpTransport('eliteinsure.co.nz', 587))
      ->setUsername('wilfred@eliteinsure.co.nz')
      ->setPassword('wilelite2021');

      // Create the Mailer using your created Transport
      $mailer = new Swift_Mailer($transport);

      // Send the created message
      $isSent = $mailer->send($message);
}


for($i = 0; $i< count($arrAttendee); $i++){


      $data = $trainingController->getAttendee($arrAttendee[$i]);
      while ($row = $data->fetch_assoc()) {

      if(isset($_GET['mail'])) {
           $mpdf = new \Mpdf\Mpdf();
      }

      $firstName = $row["first_name"].' '.$row["last_name"];
      $email = $row["email_address"];

      $mpdf->AddPage('P');
      $mpdf->SetDisplayMode('fullpage');

      $html = <<<EOF
<!DOCTYPE">
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css"/>
<style>
* {
  box-sizing: border-box;
}

/* Create two equal columns that floats next to each other */
.column {
  float: left;
  width: 45%;
  padding: 10px;

  }

/* Clear floats after the columns */
  content: "";
  display: table;
  clear: both;
}

.trainer span span{
   width: 140px;
}
</style>
</head>
<body>

<div style="position:absolute;top:0.26in;left:0in;width:90px;line-height:0.27in; background-color: #455a73;height:70px;">
    <span style="background-colro:red"></span>

</div>

<div style="position:absolute;top:0.18in;left:1.20in;width:4.36in;line-height:0.27in;">
  <img src="img/elitelogo.png" alt="eliteinsure" class="logo" width="100"/>
</div>

<div style="position:absolute;top:0.72in;left:3.48in;width:4.36in;line-height:0.27in;">
  <span style="font-style:normal;font-weight:bold;font-size:15pt;font-family:Calibri;color:#44546a">ATTESTATION RE MEETING/TRAINING</span>
</div>

<div style="position:absolute;top:0.26in;left:7.4in;width:90px;line-height:0.27in; background-color: #1881c7;height:70px;">
    <span style="background-colro:red"></span>

</div>


<div style="position:absolute;top:1.46in;left:1.36in;width:2.05in;line-height:0.17in;">
  <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">DATE:
  <div style="text-align:center; margin-top: -18px; margin-left:40px; border-bottom: 1px solid #000;">
      <span style="font-style:normal;font-weight:normal;font-size:11pt;font-family:Calibri;color:#000000">{$date}</span>
  </div>
  </span>
  <br/>
</div>

<div style="position:absolute;top:1.70in;left:1.36in;width:2.05in;line-height:0.17in;">
<br/>
  <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Dear $firstName,
  </span>
  <br/>
</div>

{$textbuildAdivser}


<div style="position:absolute;top:3.16in;left:1.36in;">
  <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">The topics that were discussed/trained to you were:  </span><span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000"></span>
  <br/>
</div>


<div class="row" style="top:3.23in;left:1.36in;left:1.36in;"><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
   {$div}
</div>


<br>
<div style="margin-left: 72px;left:1.36in;width:6.86in;line-height:0.17in;">
  <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">Finally, this is to certify that the information enclosed and disclosed in this form and any attached</span>

</div>
<div style="margin-left: 72px;left:1.36in;width:6.86in;line-height:0.17in;">
  <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">documents are true and correct and any incorrect or false statement placed herein shall render this</span>
</div>

<div style="margin-left: 72px;left:1.36in;width:1.31in;line-height:0.17in;">
  <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">attestation invalid.</span>
  </div>

<div style="margin-left: 470px;left:4.86in;width:7.93in;">
  <img src="{$trainerSignature}" alt="eliteinsure" class="logo" width="200"/>
</div>

<div style="margin-top: -40px;margin-left: 400px;left:4.56in;width:7.93in;">
  <span style="font-style:normal;font-weight:normal;font-size:9pt;font-family:Calibri;color:#000000">_______________________________________________</span>
</div>


{$divsig}


<div class="footer" style="font-size:6pt;>
    <img src="img/logo.png" alt="eliteinsure" class="logo" width="200"/>
  </div>
</body>
</html>


EOF;

    $mpdf->WriteHTML($html);
    $mpdf->SetHTMLFooter($htmlFooter);

    if(isset($_GET['mail'])) {

          $content = $mpdf->Output('', 'S');
          $attachment = (new Swift_Attachment($content,'Training Record', 'application/pdf'));

          $message = new Swift_Message();
          $message->setSubject('Training Record');
          //$message->setFrom(array('executive.admin@eliteinsure.co.nz' => 'EliteInsure'));
          //Remove the venue at the certificate.
          //Move date to footer.

          $message->setFrom(array('executive.admin@eliteinsure.co.nz' => 'EliteInsure'));
          $message->setTo($email);

          $link = '<a href="' . $config['app_url'] . '/feedback?id='.$idTrain.'">Feedback Form Link</a>';
          $message->setBody('
Dear Attendee/Trainee,<br><br>


Good day.<br><br>

Please see attached signed attestation re meeting/training.<br><br>

Also, we would appreciate if you can give us some feedback with regards for the same by clicking the link below. Your feedback will be sent to us anonymously.<br><br>

'.$link.'<br><br><br>


Regards,<br><br>

Eliteinsure Admin Team','text/html');

          $message->attach($attachment);

          $transport = (new Swift_SmtpTransport('eliteinsure.co.nz', 587))
          ->setUsername('wilfred@eliteinsure.co.nz')
          ->setPassword('wilelite2021');

          // Create the Mailer using your created Transport
          $mailer = new Swift_Mailer($transport);

          // Send the created message
          $isSent = $mailer->send($message);
        }
    }
  }

if($download == 1){
     $mpdf->Output('Training Record.pdf', "D");
  }elseif (isset($_GET['mail'])) {
    header("location: training?page=training_list&sent=1");
  }else{
   $mpdf->Output('Training Record.pdf', "I");
  //print_r(' <table> ' .$trv. '</table>');
}
ob_end_flush();













