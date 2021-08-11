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


//variables
$currentSessionFirstName = $app->param($_SESSION, "first_name", "User");
$action = $app->param($_GET, "action");


if($action == "del"){
	$id = $app->param($_GET, "id");
	$dataset = $trainingController->deleteMaterials($id);	
}

$dataset = $trainingController->getMaterials();
$rows = "";

if ($dataset->num_rows <= 0) {
}
else {
	while ($row = $dataset->fetch_assoc()) {
		$material_title = $row["material_title"];
		$id_material = $row["id_material"];
		$file_name = $row["file_name"];
        $rows .= <<<EOF
		<tr>
			<td>{$material_title}</td>
			<td>
			<a href="/staging/staging-training/training_materials/{$file_name}" target="_blank" title="Preview" class="download" data-toggle="tooltip" data-placement="bottom" >{$file_name}</a></td>
			<td>
				<a href="index?page=training_material_add&id={$id_material}&action=edit" title="Edit Traning Material" class="edit" data-toggle="tooltip" data-placement="bottom">
					<i class="material-icons">edit</i>
				</a>
				<a href="index?page=training_material&id={$id_material}&action=del" title="Delete Training Material" class="delete" data-toggle="tooltip" data-placement="bottom" onclick="return confirm('Are you sure that you want to delete this training?')">
					<i class="material-icons">delete</i>
				</a>
			</td>
		</tr>

EOF;
}
}

?>

<style>
	#material a { color:#FFFFFF; }

		
		 td:nth-child(3){
			text-align: left;
		}
</style>

<div class="subHeader">
	<div class="row">
		<div class="col-8 title">
			Hi, <span class="capitalize"><?php echo $currentSessionFirstName; ?>!</span>
		</div>
		<div class="col-4">
			<ul class="subHeader-controls">
				<li>
					<a href="index.php?page=training_material_add" title="Add new material training" data-toggle="tooltip" data-placement="bottom">
						<i class="material-icons">add</i>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>
<div class="main">
	<div class="row">
		<div class="offset-sm-3 col-sm-7">
			 <table class="table table-responsive-md table-hoverable material">
				<thead style="background-color:#e9ecef;">
				    <tr>
				      <th scope="col">Training Material Title</th>
				      <th scope="col">File Uploaded</th>
				      <th scope="col">Action</th>
				    </tr>
				 </thead>
				 <tbody>
				    <?php
							echo $rows;
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Preivew</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <video width="450" height="300" id="videoSource" controls><source src="" type="video/mp4"></video>
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
	function preview(id){
		
		$('.modal').modal('show');
  		$("#videoSource").attr('src','/staging/staging-training/training_materials/' + id.text);
  		$("#videoSource").play();
	}
	$(document).ready( function () {
          		$('.material').dataTable( {
 				 "pageLength": 25
				});
      		});
</script>





