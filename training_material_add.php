<?php

include_once("security.php");
$prop = array(
			"group_name" => "index",
			"allow" => ""
		);
securePage($prop);

//include necessary files
include_once("lib/General.helper.php");
include_once("lib/Test.controller.php");
include_once("lib/Training.controller.php");

$app = new GeneralHelper();
$testController = new TestController();
$trainingController = new TrainingController();
$saveMat = $app->param($_POST, 'action');
$message = "";
//variables
$currentSessionFirstName = $app->param($_SESSION, "first_name", "User");
$action = $app->param($_GET, "action");
$id = $app->param($_GET, "id");

if($saveMat == "save_material"){
	if(!isset($_FILES['file']) && $id != ""){
		$dataset = $trainingController->updateMaterial($topic_title,$file_name,$id);
		$message = "<div class=\"alert alert-success\" role=\"alert\">Training material saved.</div>"; 
	}
		else{
			$errors= array();
			$file_name = $_FILES['file']['name'];
			$file_size =$_FILES['file']['size'];
			$file_tmp =$_FILES['file']['tmp_name'];
			$file_type=$_FILES['file']['type'];
			$path = 'training_materials/';
			$topic_title = $app->param($_POST, 'topic_title', 1);

			if($topic_title == ""){
				$message = "<div class=\"alert alert-danger\" role=\"alert\">Please add Training Topic Title.</div>";
			}else{
				if($id != ""){
		   			//unlink($tp.$oldFile);
		   			$dataset = $trainingController->updateMaterial($topic_title,$file_name,$id); 
			   	}else{
			    	$dataset = $trainingController->addMaterial($topic_title,$file_name,$path);   
			   	}
			   		if(isset($_FILES['file'])){
					      $upload = move_uploaded_file($file_tmp, $path.$file_name);
					   }else{

					   }
			    	$message = "<div class=\"alert alert-success\" role=\"alert\">Training material saved.</div>";
					}
				}
			}


if($action == "edit"){
	$dataset = $trainingController->getMaterial($id);   
	while ($row = $dataset->fetch_assoc()) {
		  $title = $row["material_title"];
		  $fileName = $row["file_name"];
	  }
}
?>
<style>
li.active a { color:#FFFFFF; }
</style>
<div class="subHeader">
	<div class="row">
		<div class="col-8 title">
			Hi, <span class="capitalize"><?php echo $currentSessionFirstName; ?>!</span>
		</div>
		<div class="col-4">
			<ul class="subHeader-controls">
				
			</ul>
		</div>
	</div>
</div>
<div class="main">
	<div class="row">
		<div class="col-sm-12" >
			<div class="offset-md-5 col-3 text-center">
						
				 		<?php echo $message; ?>

					</div>
		<form action="" method="POST" enctype="multipart/form-data">
			<div class="offset-md-5 col-md-3">
			<label>Training Topic Title</label>
            <input type="text" class="form-control" name="topic_title" value="<?= (empty($title)) ? '' : $title ?>">
            <?php (empty($title)) ? '' : '' ?>
            	<label class="mt-1"><?= (empty($title)) ? '' : 'Uploaded File:<br><a class="mt-2" href="/staging/staging-training/training_materials/'.$fileName.'" download="'.$fileName.'">' .$fileName. '</a>' ?></label>
            <?php ?>
            <div class="form-group">
               <input type="file" name="file" />
            </div>
             <input type="hidden" name="action" value="save_material">   
            <input type="hidden" name="fileUploaded" id="fileUploaded">
            <div class="preview"></div>
            <input id="generate" type="submit" value="Save" class="btn btn-primary btn-md btn-block mt-4" />
			</div>
		</form>
	
		</div>
	</div>
</div>
