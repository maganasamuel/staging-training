<?php
/**
@name: test_result.php
@author: Gio
@desc:
	Display list of tests that has been taken by the examinees
*/
//secure the page
include_once("security.php");
$prop = array(
			"group_name" => "index",
			"allow" => ""
		);
securePage($prop);

//include necessary files
include_once("lib/General.helper.php");
include_once("lib/Test.controller.php");

$app = new GeneralHelper();
$testController = new TestController();

//variables
$currentSessionFirstName = $app->param($_SESSION, "first_name", "User");
$action = $app->param($_GET, "action");

//delete a test
if ($action == "del") {
	$idTest = $app->param($_GET, "id", 0);
	$deleteDataset = $testController->setQuestionDelete($idTest);
}

//sets
$setDataset = $testController->getSetAll(-1);
$sets = "";
$view = $app->param($_GET, "view", 1);
if ($setDataset->num_rows > 0) {
	while($row = $setDataset->fetch_assoc()) {
		$idSet = $row["id_set"];
		$setName = $row["set_name"];
		$active = ($view == $idSet) ? "active" : "";
		$sets .= <<<EOF
		<li class="list-group-item {$active}">
			<a href="index.php?page=test_questions&view={$idSet}">
				{$setName}
			</a>
		</li>
EOF;
	}
}


//display
$dataset = $testController->getSetQuestionAll($view);

$headers = array("Question Type", "Index", "Questions", "Choices", "Answer(s)", "Max Score", "Action");
$tableHeader = $app->getHeader($headers);
$rows = $tableHeader;

if ($dataset->num_rows <= 0) {
	$rows .= $app->emptyRow(count($headers));
}
else {
	while ($row = $dataset->fetch_assoc()) {
		$idSet = $row["id_set"];
		if ($idSet != $view) {
			continue;
		}
		
		$idTestQ = $row["id_set_question"];
		$idTest = $row["id_set"];
		$question = $row["question"];
		$set_question_type = $row["set_question_type"];
		$question_set_index = $row["question_set_index"];
		$choices = $row["choices"];
		$answer_index = $row["answer_index"];
		$max_score = $row["max_score"];
        $choices_array = explode(";", $choices);
        $answers_array = explode(";", $answer_index);

		//modify display
        $choices = str_replace(";", "<br>", $choices);
        
		$answers = array();
		
		/*
        foreach($answers_array as $ans){
            if($ans!=""){
                $answers[] = $choices_array[(int)$ans];
            }
        }
		*/

        $answers = $answer_index;

        if(empty($choices)){
            $choices = "N/A";
        }
        
        if(count($answers_array) == 0){
            $answers = "N/A";
        }
        
        $rows .= <<<EOF
		<tr>
			<td class="capitalize">{$set_question_type}</td>
			<td>{$question_set_index}</td>
			<td>{$question}</td>
			<td>{$choices}</td>
			<td style="width:140px;">{$answers}</td>
			<td style="width:120px;">{$max_score}</td>
			<td style="min-width:80px;">
				<a href="index.php?page=test_question_edit&id={$idTestQ}" title="Edit Test Question" data-toggle="tooltip" data-placement="bottom">
					<i class="material-icons">edit</i>
				</a>
				<a href="index.php?page=test_questions&id={$idTestQ}&action=del&view={$view}" title="Delete Test Question" data-toggle="tooltip" data-placement="bottom" onclick="return confirm('Are you sure?')">
					<i class="material-icons">delete_forever</i>
				</a>
			</td>
		</tr>
EOF;
	}
	
	if ($rows == $tableHeader) {
		$rows .= $app->emptyRow(count($headers));
	}
}
$message = $app->param($_GET, "message");

if ($message == "sent") {
	echo <<<EOF
	<div class="alert alert-success" role="alert">
		An email has been successfully sent!
	</div>
EOF;
}
else if ($message == "failed") {
	echo <<<EOF
	<div class="alert alert-warning" role="alert">
		Oops! Failed sending email. Please try again.
	</div>
EOF;
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
				<li>
					<a href="index.php?page=test_question_add" title="Add new test question" data-toggle="tooltip" data-placement="bottom">
						<i class="material-icons">add</i>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>
<div class="main">
	<div class="row">
		<div class="col-sm-3 testSets">
			<ul class="list-group list-group-flush">
				<?php echo $sets; ?>
			</ul>
		</div>
		<div class="col-sm-9">
			<table class="table table-responsive-md table-hoverable">
			<?php
				echo $rows;
			?>
			</table>
		</div>
	</div>
</div>