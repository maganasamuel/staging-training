<?php
	// include_once("lib/Session.helper.php");
	// include_once("lib/General.helper.php");
	include_once("lib/Training.controller.php");

	// $session = new SessionHelper();
	// $app = new GeneralHelper();
	
	$trainingController = new TrainingController();
	
	$trainingMaterials = $trainingController->getTrainingMaterials();
	$rows = "";

	while ($row = $trainingMaterials->fetch_assoc()) {
		$id_material = $row['id_material'];
		$title = $row["material_title"];
		$filename = $row["file_name"];
		$filestatus = "<span style='color: red'>FILE NOT FOUND</span>";
		// $dir = $row['file_uploaded'];

		$dir = "training_materials/".$filename;
		if(file_exists($dir) && (($filename != '') || ($filename != null))) {
			$filestatus = "<span style='color: green'>FILE EXIST</span>";

			$rows .= <<<EOF
			<tr>
				<td>{$title}</td>
				<td>{$filename}</td>
				<td>{$filestatus}</td>
				<td><a href="{$dir}" target="_blank" title="View Material" data-toggle="tooltip" data-placement="bottom">
						<i class="material-icons">insert_drive_file</i>
					</a>
					<a href="{$dir}" title="Download Material" class="downloadMaterial" data-toggle="tooltip" data-placement="bottom" download="{$filename}">
						<i class="material-icons">arrow_downward</i>
					</a>
				</td>
			</tr>

			EOF;
		} else {
			$rows .= <<<EOF
			<tr>
				<td>{$title}</td>
				<td>{$filename}</td>
				<td>{$filestatus}</td>
				<td></td>
			</tr>

			EOF;        
		}
	}
?>

<div class="subHeader">
	<div class="row">
		<div class="col title">
			Training Materials
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-1"></div>
	<div class="col-sm-10">
		<div id="TrainingList" class="tabcontent">
		  	<div class="row">
				<div class="col-sm-12">								
					 <table class="table table-responsive-md table-hoverable training">
							  <thead style="background-color:#e9ecef;">
							    <tr>
							      <th scope="col">Title</th>
							      <th scope="col">Filename</th>
							      <th scope="col">File Status</th>
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
	</div>

	<div class="col-sm-1"></div>
</div>

<script type="text/javascript">
	$(document).ready( function () {
      	$('.training').dataTable( {
				 "pageLength": 25
		});
  	});
</script>