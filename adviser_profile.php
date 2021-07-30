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
$trainingController = new TrainingController();

$idProfile = $app->param($_GET, "id", 0);

$attendedTraining = $trainingController->attendedTraining($idProfile);
$trAttended = "";

$cpdTraining = $trainingController->cpdTraining($idProfile);
$trAttended = "";

$usProfile = $trainingController->getSpecificUser($idProfile);

$usName = '';
$email = '';
$fsp = '';

while ($row = $usProfile->fetch_assoc()) {
  $usName = $row["full_name"];
  $email = $row["email_address"];
  $fsp = $row["ssf_number"];

  }

while ($row = $attendedTraining->fetch_assoc()) {
    $topic = str_replace(',','<br>', $row["training_topic"]);
    $date = $row["training_date"];
    $newDateTime = date('d M Y h:i A', strtotime($date));

    $trainer = $row["trainer_id"];
   
    $trianerList = $trainingController->getAttendee($trainer);
    while ($row = $trianerList->fetch_assoc()) {
      $trainer_name = $row["full_name"];
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
      $trainer_name = $row["full_name"];
    }

    $cpdList .= <<<EOF
    <tr>
      <td>{$topic}</td>
      <td>{$newDateTime}</td>
      <td>{$trainer_name}</td>
    </tr>
EOF;
}

?>

    <div class="subHeader">
      <div class="row">
        <div class="col title">
          Add CPD TOPICS
        </div>
      </div>
    </div>
    
    <div align="container">
      
      
        <br>
        <div class="row text-center">
        </div>
        <div class="row  ml-5">
          <div class="col-3">
            <div class="card">
                <h5 class="card-header"></h5>
                  <div class="card-body">
                    <p class="card-text">Adviser: <?= $usName ?></p>
                    <p>FSP: <?= $fsp ?></p>
                    <p>Email:<a href="mailto:<?= $email ?>"> <?= $email ?></a></p>
                  </div>
                </div>
          </div>
           <div class="col-4">
           <h6>Continuing Professional Development Course</h6>
             <table class="table table-responsive-md table-hoverable">
                <thead style="background-color:#e9ecef;">
                  <tr>
                    <th>Topic Trained On</th>
                    <th>Training Date</th>
                    <th>Trainer</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                      echo $cpdList;
                ?>
                </tbody>
            </table>
          </div>
          <div class="col-4">
           <h6>Team Training Course</h6>
             <table class="table table-responsive-md table-hoverable">
                <thead style="background-color:#e9ecef;">
                  <tr>
                    <th>Topic Trained On </th>
                    <th>Training Date</th>
                    <th>Trainer</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                      echo $trAttended;
                ?>
                </tbody>
            </table>
          </div>
        </div>
    </div>