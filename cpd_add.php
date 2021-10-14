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

$message = "";

$action = $app->param($_POST, "action");

$cpdID = $app->param($_GET, "id");  

if($cpdID){
$cpdList = $trainingController->getSpecificCpd($cpdID);
while ($row = $cpdList->fetch_assoc()) {
  $cpd_name = $row["cpd_name"];
  $cpd_description = trim($row["cpd_description"]);
  }
}


if ($action == "save_cpd") {
  $cpd_name = $app->param($_POST, "cpd_name");
  $cpd_description = $app->param($_POST, "cpd_description");
  if($cpdID != ""){
     $dataset = $trainingController->updateCPD($cpd_name,$cpd_description,$cpdID);   
  }else{
     $dataset = $trainingController->addCPD($cpd_name,$cpd_description);   
  }
  $message = "<div class=\"alert alert-success\" role=\"alert\">CPD topic created!.</div>";
  
  }

?>

    <div class="subHeader">
      <div class="row">
        <div class="col title">
          Add PDP TOPICS
        </div>
      </div>
    </div>
    
    <div align="container">
      <div class="row justify-content-md-center mt-4">

          <div class="col-sm-12 col-lg-3">
            
            <?php echo $message; ?>

          </div>
        </div>
      <form method="post">
        <br>
        <div class="row text-center">
        </div>
        <div class="row justify-content-md-center">
          <div class="col-sm-12 col-lg-3">
            <label class="font-weight-normal text-center">Topic</label>
            <input type="text" class="form-control mb-1" placeholder="Topic Title" value="<?= (empty($cpd_name)) ? '' : $cpd_name ?>" name="cpd_name" aria-label="Large" aria-describedby="inputGroup-sizing-sm">
          </div>
        </div>
        <br>
          <div class="row justify-content-md-center">
          <div class="col-sm-12 col-lg-3">
            <div class="form-group">
            <label class="font-weight-normal text-center">Topic Description</label>
              <textarea class="form-control" rows="5" placeholder="PDP Description" name="cpd_description"><?= (empty($cpd_description)) ? '' : $cpd_description ?></textarea>
            </div>
          </div>
        </div>
        <br>
        <div class="row justify-content-md-center">
          <div class="col-sm-12 col-lg-3">
            <input type="hidden" name="signature" id="imageUrl">
            <input type="hidden" name="action" value="save_cpd"/>
            <input id="generate" type="submit" value="Save" class="btn btn-info width100" />
            <br/>
          </div>
        </div>
      </form>
    </div>