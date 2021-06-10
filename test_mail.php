<?php
/**
@name: test_mail.php
@author: Gio
@desc:
	handles both PDF and emailing functionality of the system.
*/
//include necessary files
include_once("lib/General.helper.php");
include_once("lib/Test.controller.php");

$app = new GeneralHelper();
$testController = new TestController();

//variables
$idTest = $app->param($_GET, "id", 0);
$dataset = $testController->getTestDetail($idTest);

//display
$headers = array("#","Test Detail", "Score");
$rows = $app->getHeader($headers);

if ($dataset->num_rows <= 0) {
	$rows .= $app->emptyRow(count($headers));
}
else {
	while ($row = $dataset->fetch_assoc()) {
		$idTestDetail = $row["id_test_detail"];
		$idSetQuestion = $row["id_set_question"];
		$question = $row["question"];
		$answer = $row["answer"];
		$score = $row["score"];
		$maxScore = $row["max_score"];
		$answer = str_replace(";", "<br/>", $answer);
		$questionSetIndex = $row["question_set_index"];
		
		$choices = $row["choices"];
		$answerIndex = $row["answer_index"];
		
		$choicesArray = explode(";", $choices);
		$options = "";
		for ($i = 0; $i < count($choicesArray); $i++) {
			$option = $choicesArray[$i];
			
			if (strpos($answerIndex, "". $i) !== false) {
				if ($option != "") {
					$options .= "<span >{$option}</span><br/>";
				}
			}
		}
		if ($options != "") {
			$options = "<b>Correct Answer:</b><br/>".$options;
		}
		
		$rows .= <<<EOF
		<tr>
			<td>{$questionSetIndex}</td>
			<td>
				<p class="question">{$question}</p><hr/>
				<p class="answer">{$answer}</p>
				<br/>
				<p class="correctAnswer">
					{$options}
				</p>
			</td>
			<td>
				<p class="score">{$score}</p>
			</td>
		</tr>
EOF;
	}
}

//header
$dataset = $testController->getTestAll($idTest);
$idUserTestined = "";
$dateTook = "";
$fullName = "";
$emailAddress = "";
$totalScore = "";
$setName = "";
$timeTook = "";
$firstName = "";
$dateNow = "";

if ($dataset->num_rows > 0) {
	while ($row = $dataset->fetch_assoc()) {
		$idUserTestined = $row["id_user_tested"];
		$firstName = $row["first_name"];
		$fullName = $row["last_name"] . ", " . $row["first_name"];
		$emailAddress = $row["email_address"];
		$totalScore = $row["score"];
		$dateTook = $row["date_took"];
		$maxScore = $row["max_score"];
		$setName = $row["set_name"];
		$timeTook = $row["time_took"];
		$dateNow = $row["date_now"];
		
		$totalScore = (($totalScore / $maxScore) * 100);
		$tScore = number_format((float)$totalScore, 2, '.', '');
		if ($tScore >= 80) {
			$totalScore = "<span style=\"color:#3AA237;font-weight:bold;\">$tScore</span>";
		}
		else {
			$totalScore = "<span style=\"color:#CA4A4A;font-weight:bold;\">$tScore</span>";
		}
	}
}
foreach($row as $r){
    echo $r;
}
//formulate the filename
//<adviser name>_compliance101_testresults<DDMMYYYY>
$pdfFirstName = str_replace(" ", "_", $firstName);
$pdfSetName = str_replace(" ", "_" , $setName);
$pdfFilename = "{$firstName}_{$pdfSetName}_testresults{$dateNow}.pdf";
$pdfFilename = strtolower($pdfFilename);


$htmlHeader = <<<EOF
	<div class="header">
		<div class="logo">
			<img src="img/logo_vertical.svg" alt="eliteinsure" class="logo"/> 
		</div>
	</div>
EOF;

$html = <<<EOF
<!DOCTYPE html> 
<html lang="en">
	<head>
		<title>{$pdfFilename}</title>
		<style>
			.header,
			.detail,
			table,
			b,
			div,
			label {
				font-family: "helvetica";
				font-size:9pt;
			}
			.capitalize {
				text-transform:capitalize;
			}
			hr {
				height: 1px;
				color: #DADADA;
				background-color: #DADADA;
				border: none;
			}
			br {
				clear:both;
			}
			table {
				border-spacing: 0;
				border-collapse: collapse;
				width:100%;
				border:1px solid #DADADA;
			}
			table th {
				background-color:#DADADA;
				padding:4px;
				text-align:left;
				font-weight:bold;
			}
			td {
				border:1px solid #DADADA;
				padding:4px;
				border-bottom: 1px solid #CCCCCC;
			}
			div.header {
				width:100%;
				border-bottom: 1px solid #000000;
			}
			div.logo {
				width:180px;
				margin:4px auto;
				height:100px;
			}
			img.logo {
				height:180px;
			}
			div.title {
				width:40%;
				line-height:40px;
				height:40px;
				float:left;
				position:relative;
				width:300px;
				padding-left:10px;
			}
			img.signature {
				height:100px;
				margin-left:30px;
			}
			div.signatureHolder {
				width:200px;
				float:right;
			}
			p.question {
				color:#0F6497;
			}
			p.answer {
				color:#000000;
			}
			div.footer {
				text-align:center;
				color:#666666;
			}
		</style>
	</head>
	<body>
		{$htmlHeader}
		<br/>
		<h3 style="text-align:center; margin-top:0px;">{$setName}</h3>
		<table>
			<tr>
				<th>Name</th>
				<th>Date answered</th>
				<th>Time Spent</th>
				<th>Score</th>
			</tr>
			<tr>
				<td class="capitalize">{$fullName}</td>
				<td>{$dateTook}</td>
				<td>{$timeTook}</td>
				<td>{$totalScore} %</td>
			</tr>
		</table>
		<br/>
		<table class="table table-responsive-md table-hoverable">
			{$rows}
		</table>
		
		<br/>
		<div style="border-top:1px solid #CCCCCC; width:100%; height:100px;">
			<div class="signatureHolder">
				<p style="text-align:center;height:10px;padding:0px; margin: 0px; margin-top:20px;">EliteInsure Training Department</p>
				<hr/>
				<p style="text-align:center; margin-top:0px;">Prepared by</p>
			</div>
		</div>
	</body>
</html>
EOF;

$htmlFooter = <<<EOF
	<div class="footer" style="font-size:6pt;">
		<p>ELITEINSURE LIMITED</p>
		<p>Address: 3G/39 Mackelvie Street Grey Lynn 1021 Auckland New Zealand | Contact: 0508 123 467</p>
		<p>Email: admin@eliteinsure.co.nz | Website: www.eliteinsure.co.nz </p>           
	</div>
EOF;


//code for PDF display and emailer
require_once __DIR__ . '/package/vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf();
$email = $app->param($_GET, "email", ""); // if email parameter exists in the URL it means that the user is requesting to send a copy via email; if non display PDF instead;
$view = $app->param($_GET, "view", "");
$isForMailing = $email != "" ? 1 : 0;
if ($isForMailing == 1) {
	// Beginning Buffer to save PHP variables and HTML tags
	//first page
	ob_start();
	echo $html;
	$html1 = ob_get_contents();
	ob_end_clean();
	$mpdf->WriteHTML(utf8_encode($html1));
	$mpdf->SetHTMLFooter (utf8_encode($htmlFooter));
	
	$content = $mpdf->Output('', 'S');
	
	// Create instance of Swift_Attachment with our PDF file
	$attachment = (new Swift_Attachment())
	  ->setFilename($pdfFilename)
	  ->setContentType('application/pdf')
	  ->setBody($content);

	$message = new Swift_Message();
	$message->setSubject('Assessment Result');

	
	$message->setFrom(array('executive.admin@eliteinsure.co.nz' => 'EliteInsure'));
	$message->setTo(array($email));	
	$message->setBcc(array('compliance@eliteinsure.co.nz' => 'Compliance'));
	$message->setBcc(array('admin@eliteinsure.co.nz' => 'Admin'));
	
	$message->setBody('Dear ' . ucfirst($firstName) . ',

Attached is your Assessment Result for the ' . $setName . '



Regards,

Leif');
	$message->attach($attachment);

	$transport = (new Swift_SmtpTransport('eliteinsure.co.nz', 587))
	->setUsername('wilfred@eliteinsure.co.nz')
  	->setPassword('wilelite2021');

	// Create the Mailer using your created Transport
    $mailer = new Swift_Mailer($transport);

	// Send the created message
	$isSent = $mailer->send($message);

	//send
	if ($isSent) {
		header("location: index.php?page=test_result&message=sent&view={$view}");
	}else {
		header("location: index.php?page=test_result&message=failed&view={$view}");
	}
}
else {
	ob_clean();
	$mpdf->WriteHTML($html);
	$mpdf->SetHTMLFooter ($htmlFooter);
	$mpdf->Output($fullName . " - " . $setName  . '.pdf', "I");
	ob_end_flush();
}
?>