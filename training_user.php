<?php
/**
@name: test.php
@author: Gio
@desc:
  master page for trainee/examinee that has access to the actual test page
*/
ob_start();

//secure the page
// include_once("security.php");
// $prop = array(
//      "group_name" => "index",
//      "allow" => "1"
//    );
// securePage($prop);

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

$usID = $app->param($_GET, "id");
$usType = "";
if ($action == "save_profile") {

  $full_name = $app->param($_POST, "full_name");
  $email_address = $app->param($_POST, "email_address");
  $password = $app->param($_POST, "password");
  $ssfnumber = $app->param($_POST, "ssfnumber");
  $user_type = $app->param($_POST, "user_type");

  if($usID){
      $datasetuser = $trainingController->updateUserTraining($full_name,
            $email_address,
            $password,
            $ssfnumber,$user_type,$usID
          ); 

  }else{
    $datasetuser = $trainingController->addUserTraining($full_name,
            $email_address,
            $password,
            $ssfnumber,$user_type
          );   
  }

  $message = "<div class=\"alert alert-success\" role=\"alert\">User profile saved.</div>";
  
  }

if($usID){

$usList = $trainingController->getSpecificUser($usID);

while ($row = $usList->fetch_assoc()) {
  $usName = $row["full_name"];
  $usEmail = $row["email_address"];
  $usFSP = $row["ssf_number"];
  $usPassword = $row["password"];
  $usType = $row["id_user_type"];  

  }
}
?>
<!doctype html>
<html lang="en">
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta name="robots" content="noindex, nofollow" /> 
      <meta name="description" content="">
      <meta name="author" content="Elite Insure">
      <link rel="icon" href="img/favicon.ico">

      <title></title>
      
      <!-- CSS -->
      <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/test_form.css" rel="stylesheet">
  
    <!-- Icon font -->
    <link href="css/google-icons.css" rel="stylesheet">
    
    <!-- Script -->
    <script src="js/jquery-3.2.1.slim.min.js"></script>
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.js"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>

  </head>

  <body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #0c4664; padding:0px 5px;">
      <a class="navbar-brand" href="#">
        <img src="img/logo_vertical.svg" alt="onlineinsure" class="logo logo-small" style="height:40px;"/>
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav mr-auto justify-content-end width100">
          <li class="nav-item">
          <?php 
            
              echo "<a class=\"nav-link\" style=\"color:#FFFFFF;\" href=\"training_list\">Training List</a>";
            
          ?>
          </li>
          <li class="nav-item">
          <?php 
            
              echo "<a class=\"nav-link\" style=\"color:#FFFFFF;\" href=\"login_trainee?type=trainer\">Sign out</a>";
            
          ?>
          </li>
        </ul>
      </div>
    </nav>

    <div class="subHeader">
      <div class="row">
        <div class="col title">
          Add New User
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
            <label class="font-weight-normal text-center">Full Name</label>
            <input type="text" placeholder="Full Name" class="form-control mb-2" value="<?= (empty($usName)) ? '' : $usName ?>" name="full_name" aria-label="Large" aria-describedby="inputGroup-sizing-sm">
          </div>
        </div>
        <div class="row justify-content-md-center">
          <div class="col-3">
            <label class="font-weight-normal text-center">Email Address</label>
            <input type="text" placeholder="Email Address" class="form-control mb-2" value="<?= (empty($usEmail)) ? '' : $usEmail ?>" name="email_address" aria-label="Large" aria-describedby="inputGroup-sizing-sm">
          </div>
        </div>
        <div class="row justify-content-md-center">
          <div class="col-3">
            <label class="font-weight-normal text-center">Password</label>
            <input type="password" placeholder="Password" class="form-control mb-2" value="<?=(empty($usPassword)) ? '' : $usPassword ?>" name="password" aria-label="Large" aria-describedby="inputGroup-sizing-sm">
          </div>
        </div>
        <div class="row justify-content-md-center">
          <div class="col-3">
            <label class="font-weight-normal text-center">FSP Number</label>
            <input type="text" placeholder="FSP Number" class="form-control mb-2" value="<?=(empty($usFSP)) ? '' : $usFSP?>" name="ssfnumber" aria-label="Large" aria-describedby="inputGroup-sizing-sm">    
          </div>
        </div>
        <div class="row justify-content-md-center">
          <div class="col-3">
            <label class="font-weight-normal text-center">User Type</label>
           <div class="form-group">
            <select class="form-control" id="exampleFormControlSelect1" name="user_type">
              <option value="1" <?php if($usType == 1) echo "Selected";?> >Admin</option>
              <option value="2" <?php if($usType == 2) echo "Selected";?> >ADR / SADR </option>
              <option value="3" <?php if($usType == 3) echo "Selected";?> >Adviser</option>
            </select>
            </div>
          </div>
        </div>
        <br>
        <div class="row justify-content-md-center">
          <div class="col-3">
            <input type="hidden" name="action" value="save_profile"/>
             <input id="generate" type="submit" value="Save" class="btn btn-info width100" />
          </div>
        </div>
      </form>
    </div>
  </body>
</html>



