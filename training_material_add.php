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

if($action == "edit"){
	$dataset = $trainingController->getMaterial($id);   
	while ($row = $dataset->fetch_assoc()) {
		  $title = $row["material_title"];
		  $fileName = $row["file_name"];
	  }
}


$tp =   getcwd() . '/training_materials/';
if($saveMat == "save_material"){

    $fileUploaded = $app->param($_POST, 'fileUploaded', 1);
    $baseCode = $app->param($_POST, 'fileUploaded', 1);
    $topic_title = $app->param($_POST, 'topic_title', 1);
    $fileName = $app->param($_POST, 'fileName', 1);
    $oldFile = $app->param($_POST, 'oldFile', 1);
	$path = $tp.$fileName;

	if($fileUploaded == ""){
		$message = "<div class=\"alert alert-danger\" role=\"alert\">Please upload you material file.</div>";
	}elseif($topic_title == ""){
		$message = "<div class=\"alert alert-danger\" role=\"alert\">Please add Training Topic Title.</div>";
	}else{
		if($id != ""){
   			//unlink($tp.$oldFile);
   			$dataset = $trainingController->updateMaterial($topic_title,$fileName,$id); 
	   	}else{
	    	$dataset = $trainingController->addMaterial($topic_title,$fileName,$path);   
	   	}
	    	$decoded = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $fileUploaded));
	    	$status  = file_put_contents($tp . $fileName ,$decoded);
	    	$message = "<div class=\"alert alert-success\" role=\"alert\">Training material saved.</div>";
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
		<form method="POST">
			<div class="offset-md-5 col-md-3">
			<label>Training Topic Title</label>
            <input type="text" class="form-control" name="topic_title" value="<?= (empty($title)) ? '' : $title ?>">
            <?php (empty($title)) ? '' : '' ?>
            	<label class="mt-1"><?= (empty($title)) ? '' : 'Uploaded File:<br><a class="mt-2" href="/staging/staging-training/training_materials/'.$fileName.'" download="'.$fileName.'">' .$fileName. '</a>' ?></label>
            <?php ?>
            <div class="form-group">
                <label for="fileUp">Training File</label>
                <input type="file" class="form-control-file" id="uploadImages"  name="images_file" accept="video/mp4,video/x-m4v,video/*,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" id="fileUp">
            </div>
            <input type="hidden" name="fileUploaded" id="fileUploaded">
            <input type="hidden" name="fileName" id="fileName">
            <input type="hidden" name="baseCode" id="baseCode">
            <input type="hidden" name="oldFile" id="oldFile" value="<?= (empty($fileName)) ? '' : $fileName ?>">
            <input type="hidden" name="action" value="save_material">   
            <div class="preview"></div>
            <input id="generate" type="submit" value="Save" class="btn btn-primary btn-md btn-block mt-4" />
			</div>
		</form>
		</div>
	</div>
</div>
<script type="text/javascript">
if (window.File && window.FileList && window.FileReader) {
    $("#uploadImages").on("change", function(e) {
        var files = e.target.files,
        filesLength = files.length;
        var type = files[0].type;
        $("#fileName").val(files[0].name);
        for (var i = 0; i < filesLength; i++) {
            var f = files[i];
            var fileReader = new FileReader();
            if (f.size < 10000000) {
                fileReader.onload = function(e) {
                    $('#fileUploaded').val(/base64,(.+)/.exec(e.target.result)[1]);
                    $("#baseCode").val(e.target.result);
                    if(type == "video/mp4"){
						$(".preview").append('<video width="450" height="300" controls><source src="'+e.target.result+'" type="video/mp4"></video>');
                    }
                };
                fileReader.readAsDataURL(f);
            } else {
                //File Size Exceed
            }
        }
    });
} else {
    swal("Failed", "Your browser doesn't support to File API", "warning");
}
</script>