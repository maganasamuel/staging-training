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

$config = parse_ini_file('lib/class/conf/conf.ini');

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
		$file_name = $_FILES['file']['name'];
		$file_size =$_FILES['file']['size'];
		$file_tmp =$_FILES['file']['tmp_name'];
		$file_type=$_FILES['file']['type'];
		$topic_title = $app->param($_POST, 'topic_title', 1);

		if($file_name == ""){
			$file_name = $app->param($_POST, 'old_file', 1);
		}


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
				<div class="alert alert-success" id="alert" role="alert" style="display: none;">Training material saved.</div>
			</div>
		<form id="myForm" action="" method="POST" enctype="multipart/form-data">
			<div class="offset-md-5 col-md-3">
			<label>Training Topic Title</label>
            <input type="text" class="form-control" id="topic_title" name="topic_title" value="<?= (empty($title)) ? '' : $title ?>">
            <?php (empty($title)) ? '' : '' ?>
            	<label class="mt-1"><?= (empty($title)) ? '' : 'Uploaded File:<br><a class="mt-2" href="' . $config['app_url'] . '//training_materials//'.$fileName.'" download="'.$fileName.'">' .$fileName. '</a>' ?></label>
            <?php ?>
            <div class="form-group">
               <input type="file" name="file" />
               <input type="hidden" name="old_file" value="<?= (empty($fileName)) ? '' : $fileName ?>" />
            </div>
             <div class="progress" id="progressDiv" style="display: none;">
  				<div class="progress-bar" role="progressbar" aria-valuemax="100"></div>
  			</div>
             <input type="hidden" name="action" value="save_material">
            <input type="hidden" name="fileUploaded" id="fileUploaded">
            <div class="preview"></div>
            <input id="generate" type="submit" value="Save" class="btn btn-primary btn-md btn-block mt-4" onclick="upload_image()" />
 			</div>
		</form>
		</div>
	</div>
</div>
<script type="text/javascript">
function upload_image()
{
  var bar = $('.progress-bar');
  var percent= "";
  var progDiv = $("#progressDiv");
  $('#myForm').ajaxForm({
    beforeSubmit: function() {
       $("#alert").css('display',"none");
       progDiv.css('display', "block");
       bar.css('width',"0%");
    },
    uploadProgress: function(event, position, total, percentComplete) {
      bar.css('width', percentComplete + "%");
      bar.html(percentComplete + "%");
    },
	success: function() {
      var percentVal = '100%';
      bar.width(percentVal)
      bar.html(percentVal);
    },

    complete: function(xhr) {
      if(xhr.responseText)
      {
      	$("#alert").css('display',"block");
      	$("#topic_title").val('');
      	bar.css('width',"0%");
      	bar.html("0");
      	progDiv.css('display', "none");
      }
    }
  });
}
</script>
