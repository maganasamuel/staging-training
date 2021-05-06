<?php
/**
@name: test_check.php
@author: Gio
@desc:
	used to check the examinee's answers
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
$idTest = $app->param($_GET, "id", 0);
$dataset = $testController->getTestDetail($idTest);
$setName = "Test Checker";
$totalScore = 0;
$totalMaxScore = 0;
$totalScoreAverage = "0.00%";

//prepare display
$headers = array("#","Test Detail", "Score");
$rows = $app->getHeader($headers);
$emailAddress = $app->param($_GET, "email", "");

if ($dataset->num_rows <= 0) {
	$rows .= $app->emptyRow(count($headers));
}
else {
	while ($row = $dataset->fetch_assoc()) {
		$idTestDetail = $row["id_test_detail"];
		$idSet = $row["id_set"];
		$idSetQuestion = $row["id_set_question"];
		$question = $row["question"];
		$answer = $row["answer"];
		$score = $row["score"];
		$maxScore = $row["max_score"];
		$questionSetIndex = $row["question_set_index"];
		$setName = $row["set_name"];
		
		$answer = str_replace(";", "<br/>", $answer);
		
		$choices = $row["choices"];
		$choicesArray = explode(";", $choices);
		$options = "";
		for ($i = 0; $i < count($choicesArray); $i++) {
			$option = $choicesArray[$i];
			$options .= "<span >{$option}</span><br/>";
		}
		$optionDetail = "";
		if ($choices != "") {
			$optionDetail = <<<EOF
				<p style="font-size:12px;padding:0px;">Options:</p>
				<p class="answer">{$options}</p>
EOF;
		}
		
		$rows .= <<<EOF
		<tr>
			<td style="width:80px;">{$questionSetIndex}</td>
			<td>
				<p class="question">{$question}</p>
				<p style="font-size:12px;padding:0px;">Answer:</p>
				<p class="answer">{$answer}</p>
				{$optionDetail}
			</td>
			<td>
				<div class="score">
					<span class="note">0 to {$maxScore} only.</span>
					<input type="text" class="form-control score" id="{$idTestDetail}" max="{$maxScore}" placeholder="{$score}" />
					<span class="save" title="save">
						<i class="material-icons">
						save
						</i>
					</span>
				</div>
			</td>
		</tr>
EOF;
		//calculate the score display
		$totalScore += $score;
		$totalMaxScore += $maxScore;
	}
}

//calculate the total score pecentage
$totalScoreAverage = ($totalScore / $totalMaxScore) * 100;
$tScore = number_format((float)$totalScoreAverage, 2, '.', '');
if ($tScore >= 80) {
	$totalScoreAverage = "<span style=\"color:#3AA237;font-weight:bold;\">$tScore%</span>";
}
else {
	$totalScoreAverage = "<span style=\"color:#CA4A4A;font-weight:bold;\">$tScore%</span>";
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

$calculateLink = "index.php?page=test_check&id={$idTest}&email={$emailAddress}";
?>
<link href="css/test_check.css" rel="stylesheet">
<script src="js/test_check.js"></script>
<div class="subHeader" id="subHeader">
	<div class="row">
		<div class="col-8 title">
			<?php
				echo "{$setName} | <b style=\"font-size:12px;\">SCORE: {$totalScore}/{$totalMaxScore} | PERCENTAGE: {$totalScoreAverage}</b>";
			?>
		</div>
		<div class="col-4">
			<ul class="subHeader-controls">
				<li>
					<a href="index.php?page=test_result&id=<?php echo $idTest ?>&action=answer_sheet&view=<?php echo $idSet ?>" data-toggle="tooltip" data-placement="bottom" title="Set as Answer Sheet" onclick="return confirm('Are you sure that you want to use this test as the answer sheet?')">
						<i class="material-icons">check_circle</i>
					</a>
				</li>
				<li>
					<a href="certificate.php?id=<?php echo $idTest ?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="View this test's certificate.">
						<i class="material-icons">insert_drive_file</i>
					</a>
				</li>
				<li>
					<a href="index.php?page=test_mail&id=<?php echo $idTest ?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="View this result in PDF format">
						<i class="material-icons">picture_as_pdf</i>
					</a>
				</li>
				<li>
					<a href="<?php echo "index.php?page=test_mail&id=$idTest&email=$emailAddress"; ?>" data-toggle="tooltip" data-placement="bottom" title="Mail this result to examinee">
						<i class="material-icons">mail</i>
					</a>
				</li>
				<li>
					<a href="index.php?page=test_result" data-toggle="tooltip" data-placement="bottom" title="Go back to list of tests">
						<i class="material-icons">arrow_back_ios</i>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>
<div class="main" style="padding:0px 50px;">
	<div class="alert alert-primary" role="alert">
		Type the score on the textbox located at the right side of each questions.
	</div>
	<table class="table table-responsive-md table-hoverable">
	<?php
		echo $rows;
	?>
	</table>
	
	<div style="width:100%; height:48px;">
			<a href="<?php echo $calculateLink; ?>" class="btn btn-lg btn-primary" style="position:relative; float:right;">
				Calculate
			</a>
	</div>
</div>
<script>
window.onscroll = function() {myFunction()};

var header = document.getElementById("subHeader");
var sticky = header.offsetTop;

function myFunction() {
	if (window.pageYOffset > sticky) {
		header.classList.add("sticky");
	} else {
		header.classList.remove("sticky");
	}
}
</script>