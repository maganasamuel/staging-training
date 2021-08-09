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
$emailID = $app->param($_GET, "email", 0);
$usType = $app->param($_GET, "user_type", 0);

$sessID = $app->param($_SESSION, "id_user", 0);
$sessUserType = $app->param($_SESSION, "id_user_type", 0);

$attendedTraining = $trainingController->attendedTraining($idProfile);
$trAttended = "";

$cpdTraining = $trainingController->cpdTraining($idProfile);
$trAttended = "";

$adviserTeam = $trainingController->adviserTeam($idProfile);
$usProfile = $trainingController->getSpecificUser($idProfile);

$usName = '';
$email = '';
$fsp = '';


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

$adviserList = "";
while ($row = $adviserTeam->fetch_assoc()) {
    $sadr_id = $row["id_user"];
    $trianerList = $trainingController->getAttendee($sadr_id);
    while ($row = $trianerList->fetch_assoc()) {
      $adviser_name = $row["first_name"].' '.$row["last_name"];
    }

    $adviserList .= <<<EOF
    <tr>
      <td>{$adviser_name}</td>
    </tr>
EOF;
}

$adrTeam = $trainingController->adrTeam($idProfile);
$adrList = "";
while ($row = $adrTeam->fetch_assoc()) {
    $name = $row["first_name"];
    $usID = $row["id_user"];
    $usEmail = $row["email_address"];
    $usNumber = $row["id_user_type"];
    $adrList .= <<<EOF
    <tr><td>
        <a href="training?page=adviser_profile&id={$usID}&email={$usEmail}&user_type={$usNumber}" title="View Profile" class="delete" data-toggle="tooltip" data-placement="bottom">
      {$name}</a></td>
    </tr>
EOF;
}

$adminadrTeam = $trainingController->adminadrTeam($idProfile);
$adminadrList = "";
while ($row = $adminadrTeam->fetch_assoc()) {
    $name = $row["first_name"];
    $usID = $row["id_user"];
    $usEmail = $row["email_address"];
    $usNumber = $row["id_user_type"];
    $adminadrList .= <<<EOF
    <tr>
      <td>
      <a href="training?page=adviser_profile&id={$usID}&email={$usEmail}&user_type={$usNumber}" title="View Profile" class="delete" data-toggle="tooltip" data-placement="bottom">
      {$name}</a></td>
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

?>


    <div class="subHeader">
      <div class="row">
        <div class="col title">
            Member Profile
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
                <p>Email: <a href="mailto:<?= $email ?>"> <?= $email ?></a></p>
                <?php if(($sessID == $idProfile) || ($sessUserType == 1)) : ?>
                  <p>Password: <?= $password ?></p>
                <?php endif; ?>
                <a href="<?php echo 'profilepdf?id='.$idProfile.'&email='.$emailID; ?>" class="sendEmail" target="_blank" title="Print Adviser Profile" data-toggle="tooltip" data-placement="bottom">
                  <button class="btn btn-primary btn-sm">Print to PDF</button>
                </a>
              </div>
            </div>
          </div>
           <div class="col-4">
           <h6>Continuing Professional Development Course</h6>
             <table class="table table-responsive-md table-hoverable cpd">
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
             <table class="table table-responsive-md table-hoverable team">
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
        <div class="row  ml-5">
          
          <div class="col-3" >
            <br>
            <div <?php if($usType == "2" || $usType == "7" ){echo "style='display:none;'"; }?>>
            <table class="table table-responsive-md table-hoverable modular" >
              <thead style="background-color:#e9ecef;">
                <tr>
                  <th>ADR Team Member</th>
                </tr>
              </thead>
              <tbody>
              <?php
                echo $adminadrList; 
              ?>
            </tbody>
        </table>
    </div>
     <div <?php if($usType == "2" || $usType == "8"){echo "style='display:none;'"; }?>>
            <table class="table table-responsive-md table-hoverable modular">
              <thead style="background-color:#e9ecef;">
                <tr>
                  <th>Adviser Team Member</th>
                </tr>
              </thead>
              <tbody>
              <?php

                if($usType == "7"){
                   echo $adrList;
                }else{
                  echo $adviserList; 
                }
              ?>
        </tbody>
    </table>
    </div>
          </div>
  <div class=" <?php if($usType == "2"){echo "offset-md-3"; }?> col-md-8">
    <br><br>
   <h6>Modular Training</h6>
     <table class="table table-responsive-md table-hoverable modular">
        <thead style="background-color:#e9ecef;">
          <tr>
            <th>Topics Trained On</th>
            <th>Module Take</th>
            <th>Score</th>
            <th>Results</th>
            <th>No. of Attempts</th>
          </tr>
        </thead>
        <tbody>
        <?php
              echo $modList;
        ?>
        </tbody>
    </table>
  </div>
</div>
    </div>
    <script type="text/javascript">
      $(document).ready( function () {
          $('.modular').DataTable();
          $('.cpd').DataTable();
          $('.team').DataTable();
      });

    </script>