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

  $first_name = $app->param($_POST, "first_name");
  $last_name = $app->param($_POST, "last_name");
  $email_address = $app->param($_POST, "email_address");
  $password = $app->param($_POST, "password");
  $ssfnumber = $app->param($_POST, "ssfnumber");
  $user_type = $app->param($_POST, "user_type");
  $adr_id = $app->param($_POST, "adr");
  $sadr_id = $app->param($_POST, "sadr");


 if($first_name != ""|| $last_name != "" || $email_address != ""|| $password != "" ){
  
  if($usID){
      if($ssfnumber == '') $ssfnumber = 0;
      if($adr_id == '') $adr_id = 0;
      if($sadr_id == '') $sadr_id = 0;

      $datasetuser = $trainingController->updateUserTraining($first_name,$last_name,
            $email_address,
            $password,
            $ssfnumber,$user_type,$usID,$adr_id,$sadr_id
          ); 

  }else{
    $datasetuser = $trainingController->addUserTraining($email_address,
            $first_name,$last_name,
            $password,$user_type,$ssfnumber,$adr_id,$sadr_id
          );   
  }
  
  if($datasetuser == "existed"){
      $message = "<div class=\"alert alert-danger\" role=\"alert\">Email already registered.</div>";
  
  }elseif($datasetuser == "fspexisted"){
      $message = "<div class=\"alert alert-danger\" role=\"alert\">FSP number already registered.</div>";
  }else{
      $message = "<div class=\"alert alert-success\" role=\"alert\">User profile saved.</div>";
    }  
  }
  else{
      $message = "<div class=\"alert alert-danger\" role=\"alert\">Please fill out all required fields.</div>";
     
  }
}

if($usID){

$usList = $trainingController->getSpecificUser($usID);

while ($row = $usList->fetch_assoc()) {
  $usFirstName = $row["first_name"];
  $usLastName = $row["last_name"];
  $usEmail = $row["email_address"];
  $usFSP = $row["ssf_number"];
  $usPassword = $row["password"];
  $usType = $row["id_user_type"];  
  $usAdr = $row["adr_id"];  
  $usSadr = $row["sadr_id"];  
  }
}

$adviser = $trainingController->getADR();
$adrSet = "";
foreach($adviser as $row) {

    $name = $row["first_name"].' '.$row['last_name'];
    $id = $row["id_user"];
    if(isset($usAdr) && $usAdr == $id) {
      $adrSet .= <<<EOF
      <option value="{$id}" selected>{$name}</option>
      EOF;
    } else {
      $adrSet .= <<<EOF
      <option value="{$id}">{$name}</option>
      EOF;
    }
  }

$adr = $trainingController->getSADR();
$sadrSet = "";
foreach($adr as $row) {

    $name = $row["first_name"].' '.$row['last_name'];
    $id = $row["id_user"];
    if(isset($usSadr) && $usSadr == $id) {
      $sadrSet .= <<<EOF
      <option value="{$id}" selected>{$name}</option>
      EOF;
    } else {
      $sadrSet .= <<<EOF
      <option value="{$id}">{$name}</option>
      EOF;
    }
  }

?>
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
            <label class="font-weight-normal text-center">First Name<span style="color:red;">*</span></label>
            <input type="text" placeholder="Fist Name" class="form-control mb-2" value="<?= (empty($usFirstName)) ? '' : $usFirstName ?>" name="first_name" aria-label="Large" aria-describedby="inputGroup-sizing-sm">
          </div>
        </div>
        <div class="row justify-content-md-center">
          <div class="col-3">
            <label class="font-weight-normal text-center">Last Name<span style="color:red;">*</span></label>
            <input type="text" placeholder="Last Name" class="form-control mb-2" value="<?= (empty($usLastName)) ? '' : $usLastName ?>" name="last_name" aria-label="Large" aria-describedby="inputGroup-sizing-sm">
          </div>
        </div>
        <div class="row justify-content-md-center">
          <div class="col-3">
            <label class="font-weight-normal text-center">Email Address<span style="color:red;">*</span></label>
            <input type="text" placeholder="Email Address" class="form-control mb-2" value="<?= (empty($usEmail)) ? '' : $usEmail ?>" name="email_address" aria-label="Large" aria-describedby="inputGroup-sizing-sm">
          </div>
        </div>
        <div class="row justify-content-md-center">
          <div class="col-3">
            <label class="font-weight-normal text-center">Password<span style="color:red;">*</span></label>
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
            <label class="font-weight-normal text-center">User Type<span style="color:red;">*</span></label>
           <div class="form-group">
            <select class="form-control" id="userType" name="user_type" onchange="changeProp(this)">
              <option value="1" <?php if($usType == 1) echo "Selected";?> >Admin</option>
              <option value="8" <?php if($usType == 8) echo "Selected";?> >SADR </option>
              <option value="7" <?php if($usType == 7) echo "Selected";?> >ADR </option>
              <option value="2" <?php if($usType == 2) echo "Selected";?> >Adviser</option>
            </select>
            </div>
          </div>
        </div>
        <div class="row justify-content-md-center adrMember">
          <div class="col-3">
            <label class="font-weight-normal text-center">ADR Team<span style="color:red;">*</span></label>
           <div class="form-group">
            <select class="adr form-control" name="adr">
              <option value="0">N/A</option>
              <?php
                  echo $adrSet;
                ?>
            </select>
            </div>
          </div>
        </div>
        <div class="row justify-content-md-center sadrMember">
          <div class="col-3">
            <label class="font-weight-normal text-center">SADR Team<span style="color:red;">*</span></label>
           <div class="form-group">
            <select class="sadr js-states form-control" name="sadr">
              <option value="0">N/A</option>
              <?php
                  echo $sadrSet;
                ?>
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
    <script type="text/javascript">
      
      $( document ).ready(function() {
        if($("#userType").val() == "8" || $("#userType").val() == "1"  ){
            $(".adrMember").hide();
            $(".sadrMember").hide();
        }else if($("#userType").val() == "7"){
            $(".adrMember").hide();
        }else {
          $(".adrMember").show();
          $(".sadrMember").show();
        }
      });
       function changeProp(id){
          if(id.value == "2"){
              $(".adrMember").show();
              $(".sadrMember").show();   
          }else if(id.value == "8" || id.value == "1"){
              $(".adrMember").hide();
              $(".sadrMember").hide();
          }else if(id.value == "7"){
               $(".adrMember").hide();
               $(".sadrMember").show();
          }
        }
    </script>








