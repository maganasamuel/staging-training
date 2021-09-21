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

$action = $app->param($_POST, "action", 0);


if($action === "savefeedback"){

  $first_question = $app->param($_POST, "first_question");
  $second_question = $app->param($_POST, "second_question");
  $third_question = $app->param($_POST, "third_question");
  $fourth_question = $app->param($_POST, "fourth_question");
  $fifth_question = $app->param($_POST, "fifth_question");
  $improvement = $app->param($_POST, "improvement");
  $training_id = $app->param($_POST, "training_id");

  $save = $trainingController->addFeedback($training_id,$first_question,$second_question,$third_question,$fourth_question,$fifth_question,$improvement);

}


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
    $div .= '<tr><td>'.$ctr.'. '.$dataTopicTitle[$i] . $levelText .'</td><tr>';


}

if($hostName != ''){
  $fullnameTrainer = $hostName;
}else{
  $fullnameTrainer = $fullnameTrainer;
}

?>


<html lang="en">
<head>
  <title>Training Feedback</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style type="text/css">
    body{
      background-color:#f2f3f8!important;
    }
    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{
      border-top: 0px!important;
      font-size: 14px;
    }
    .title{
      font-size: 15px;
    }
    .builder tr{
      width: 200px;
    }
    .feedback>thead>tr>th{
      text-align: center;
    }
    .feedback>tbody>tr>td{
      text-align: center;
    }
    .container{
      padding-left: 10px!important;
      padding-right: 10px!important;
      margin: auto!important;
    }
    .feedback tbody td:nth-child(1) {
      text-align:left;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="row" style="margin-top: 40px;">
    <div class="panel panel-default" style="border: 0px!important;">
      <div class="panel-heading" style="padding: 10px; font-size: 18px; border: 0px!important; background-color: #f2f3f8!important;">
        <span class="glyphicon glyphicon-book"></span>&nbsp;&nbsp;&nbsp;Training Details</div>
      <div class="panel-body">
        <table class="table builder">
            <tbody>
                <tr>
                    <td style="width: 200px">Trainer Name</td>
                    <td><?= $fullnameTrainer ?></td>
                </tr>
                <tr>
                    <td >Training Date</td>
                    <td><?= $newDateTime ?></td>
                </tr>
                <tr>
                    <td>Venue</td>
                    <td><?= $trainingVenue ?></td>
                </tr>
                <tr>
                    <td>Topic Discussed</td>
                    <td><table style="font-size: 14px;"><?= $div ?></table></td>
                </tr>
            </tbody>
        </table>
      </div>
    </div>
     <div class="panel panel-default" style="border: 0px!important;">
      <div class="panel-heading" style="padding: 10px; font-size: 18px; border: 0px!important; background-color: #f2f3f8!important;">
        <span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;&nbsp;Feedback </div>
      <div class="container">
        <table class="table feedback">
          <thead class="text-center">
            <tr>
              <th>Statement</th>
              <th>Strongly Disagree (1)</th>
              <th>Disagree (2)</th>
              <th>Neutral (3)</th>
              <th>Agree (4)</th>
              <th>Strongly Agree (5)</th>
            </tr>
          </thead>
            <tbody>
                <tr>
                    <td style="width: 400px">I am able to achieve learning outcomes.</td>
                    <td><input class="form-check-input" type="radio" name="1question[]"></td>
                    <td><input class="form-check-input" type="radio" name="1question[]"></td>
                    <td><input class="form-check-input" type="radio" name="1question[]"></td>
                    <td><input class="form-check-input" type="radio" name="1question[]"></td>
                    <td><input class="form-check-input" type="radio" name="1question[]"></td>
                </tr>
                <tr>
                    <td>The trainer is very effective in his/her delivery.</td>
                    <td><input class="form-check-input" type="radio" name="2question[]"></td>
                    <td><input class="form-check-input" type="radio" name="2question[]"></td>
                    <td><input class="form-check-input" type="radio" name="2question[]"></td>
                    <td><input class="form-check-input" type="radio" name="2question[]"></td>
                    <td><input class="form-check-input" type="radio" name="2question[]"></td>
                </tr>
                <tr>
                    <td>The content is relevant to me.</td>
                    <td><input class="form-check-input" type="radio" name="3question[]"></td>
                    <td><input class="form-check-input" type="radio" name="3question[]"></td>
                    <td><input class="form-check-input" type="radio" name="3question[]"></td>
                    <td><input class="form-check-input" type="radio" name="3question[]"></td>
                    <td><input class="form-check-input" type="radio" name="3question[]"></td>
                </tr>
                <tr>
                    <td>The training was pitched in a level that I can understand.</td>
                    <td><input class="form-check-input" type="radio" name="4question[]"></td>
                    <td><input class="form-check-input" type="radio" name="4question[]"></td>
                    <td><input class="form-check-input" type="radio" name="4question[]"></td>
                    <td><input class="form-check-input" type="radio" name="4question[]"></td>
                    <td><input class="form-check-input" type="radio" name="4question[]"></td>
                </tr>
                 <tr>
                    <td>The trainer is very efficient in using learning materials.</td>
                    <td><input class="form-check-input" type="radio" name="5question[]"></td>
                    <td><input class="form-check-input" type="radio" name="5question[]"></td>
                    <td><input class="form-check-input" type="radio" name="5question[]"></td>
                    <td><input class="form-check-input" type="radio" name="5question[]"></td>
                    <td><input class="form-check-input" type="radio" name="5question[]"></td>
                </tr>
            </tbody>
        </table>
      </div>
    </div>
    <div class="panel panel-default" style="border: 0px!important;">
      <div class="panel-heading" style="padding: 10px; font-size: 18px; border: 0px!important; background-color: #f2f3f8!important;">
      <span class="glyphicon glyphicon-comment"></span>&nbsp;&nbsp;&nbsp;One point for improvement</div>
      <div class="panel-body">
        <textarea class="form-control" placeholder="Enter your one point for improvement" cols="5" rows="5" id="improvement"></textarea>
        <br>
        <button type="button" class="btn btn-primary" onclick="savefeedback()">Submit</button>
        <input type="hidden" id="training_id" value="<?= $_GET['id']  ?>">
      </div>
    </div>
  </div>
</div>
</body>
<script type="text/javascript">
  function savefeedback(argument) {
    var first_question = $("input[name='1question[]']").map(function(){return $(this).is(':checked');}).get();
    var second_question = $("input[name='2question[]']").map(function(){return $(this).is(':checked');}).get();
    var third_question = $("input[name='3question[]']").map(function(){return $(this).is(':checked');}).get();
    var fourth_question = $("input[name='4question[]']").map(function(){return $(this).is(':checked');}).get();
    var fifth_question = $("input[name='5question[]']").map(function(){return $(this).is(':checked');}).get();
    var improvement = $("#improvement").val();
    var training_id = $("#training_id").val();
    console.log(fifth_question.toString());
    $.ajax({
          url: 'feedback',
          type: 'POST',
          data: {
              first_question: first_question.toString(),
              second_question: second_question.toString(),
              third_question: third_question.toString(),
              fourth_question: fourth_question.toString(),
              fifth_question: fifth_question.toString() ,
              improvement: improvement,
              training_id:training_id,
              action:'savefeedback'
          },
          success: (res) => {
            swal.fire({
               position: 'center',
                icon: 'success',
                title: 'Feedback successfully submitted',
                showConfirmButton: false,
                timer: 2500
              }).then(function() {
                 window.location.href = '<?php echo $config['app_url'] ?>/login_trainee?type=trainer';
            });
          }
  });

    console.log(first_question)
  }
</script>
</html>












