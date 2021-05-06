<?php
/**
@name: trainer.php
@author: Gio
@desc:
	handles the trainer-related data manipulation
*/

//secure the page
include_once("security.php");
$prop = array(
			"group_name" => "index",
			"allow" => "1"
		);
securePage($prop);

//include necessary files
include_once("lib/General.helper.php");
include_once("lib/User.controller.php");
include_once("lib/Session.helper.php");

$app = new GeneralHelper();
$userController = new UserController();
$session = new SessionHelper();


//delete trainer
$action = $app->param($_GET, "action");
if ($action == "del") {
	$idUser = $app->param($_GET, "id", 0);
	$deleteDataset = $userController->deleteTrainer($idUser);
}

//fetch/get all trainer
$keyword = $app->param($_GET, "keyword");
$dataset = $userController->getTrainerAll($keyword);

//display
$rows = "";

if ($dataset->num_rows <= 0) {
	$rows .= "<div class=\"row\"><div class=\"col\">No records found.</div></div>";
}
else {
	$counter = 0;
	$cols = "";
	while ($row = $dataset->fetch_assoc()) {
		$idUser = $row["id_user"];
		$emailAddress = $row["email_address"];
		$firstName = $row["first_name"];
		$lastName = $row["last_name"];
		$password = $row["password"];
		$idUserType = $row["id_user_type"];
		$dateRegistered = $row["date_registered"];
		$userType = $row["user_type"];
		
		//modify display
		$fullName = "{$lastName}, {$firstName}";
		
		$delete = ($idUser == 1) ? "" : "
				<a href=\"index.php?page=trainer&id={$idUser}&action=del\" title=\"Delete Trainer\" onclick=\"return confirm('Are you sure?')\" data-toggle=\"tooltip\" data-placement=\"bottom\">
					<i class=\"material-icons\">delete_forever</i>
				</a>";
				
		$edit = "";
		if ($session->get("id_user") == 1) {
			$edit = "
				<a href=\"index.php?page=trainer_edit&id={$idUser}\" title=\"Edit Trainer\" data-toggle=\"tooltip\" data-placement=\"bottom\">
					<i class=\"material-icons\">edit</i>
				</a>";
		}
		else if ($session->get("id_user") == $idUser) {
			$edit = "
				<a href=\"index.php?page=trainer_edit&id={$idUser}\" title=\"Edit Trainer\" data-toggle=\"tooltip\" data-placement=\"bottom\">
					<i class=\"material-icons\">edit</i>
				</a>";
		}
		if ($counter == 0) {
			$rows .= "<div class=\"row\" style=\"margin:0px;\">";
		}
		$rows .= <<<EOF
		<div class="col-3 cell-user">
			<div class="cell-controls">
				{$edit}
				{$delete}
			</div>
			<div class="cell-detail">
				<p class="capitalize">name: {$fullName}</p>
				<p>Email: {$emailAddress}</p>
				<p>Type: {$userType}</p>
				<p>Date Added: {$dateRegistered}</p>
			</div>
		</div>
EOF;
		$counter++;
		if ($counter == 3) {
			$rows .= "</div>";
			$counter = 0;
		}
	}
}


?>
<div class="subHeader">
	<div class="row">
		<div class="col-8 title">
			Trainers
		</div>
		<div class="col-4">
			<ul class="subHeader-controls">
				<li>
					<a href="index.php?page=trainer_add" title="Add new trainer" data-toggle=\"tooltip\" data-placement=\"bottom\">
						<i class="material-icons">person_add</i>
					</a>
				</li>
				<li>
					<form method="get" class="search">
						<div class="input-group">
							<input type="text" name="keyword" class="form-control" placeholder="Search here.."/>
							<div class="input-group-append">
								<button class="input-group-text" >
									<i class="material-icons">search</i>
								</button>
								<input type="hidden" name="page" value="trainer"/>
							</div>
						</div>
					</form>
				</li>
			</ul>
		</div>
	</div>
</div>
<div class="main">
	<?php
		echo $rows;
	?>
</div>