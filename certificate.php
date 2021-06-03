<?php
/**
@name: test_mail.php
@author: Gio
@desc:
	handles both PDF and emailing functionality of the system.
*/
//include necessary files
//code for PDF display and emailer
require_once __DIR__ . '/package/vendor/autoload.php';

include_once("lib/General.helper.php");
include_once("lib/Test.controller.php");

$app = new GeneralHelper();
$testController = new TestController();

class MyCustomPDFWithWatermark extends TCPDF {
	var $date;
	//Watermark
    public function Header() {
		//TCPDF FULL WIDTH 210
		//TCPDF FULL HEIGHT 297
		// Get the current page break margin
        $bMargin = $this->getBreakMargin();

        // Get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;

        // Disable auto-page-break
		$this->SetAutoPageBreak(false, 0);
		
		// Render the image
		$yPointer = 0;
		$this->Image('img/Company Training Certificate BG.jpg', 0, $yPointer, 297, 210, '', '', '', false, 300, '', false, false, 0);
		$yPointer += 25;
		$this->Image('img/logo_vertical.png', 129.3, $yPointer, 38.4, 23.8125, '', '', '', false, 300, '', false, false, 0);
		$yPointer += 190;
		$this->Image('img/signature001.png', 90, $yPointer, 25.4, 19.4, '', '', '', false, 300, '', false, false, 0);
		$yPointer += 40;
		$this->Text(100,$yPointer,$this->date);
		$Raleway = TCPDF_FONTS::addTTFfont('font/Raleway.ttf', 'TrueTypeUnicode', '', 32);
		$this->SetFont($Raleway,"",12);
		
        $this->SetY(195);
        $this->SetTextWhite();
		$this->Cell(267, 5, "Address: 3G/39 Mackelvie Street Grey Lynn 1021 Auckland New Zealand | Contact: 0508 123 467", "", 1, 'C', 0, '', 0);
		$this->Cell(267, 5, "Email: admin@eliteinsure.co.nz | Website: www.eliteinsure.co.nz", "", 1, 'C', 0, '', 0);
        /*

		$this->SetY(100);
		$this->SetX(0);
		$this->Cell(210, 10, "Sumit Monga", "", 1, 'C', 0, '', 0);
		$this->Ln(2);
		$this->SetX(0);
		$this->Cell(210, 10, "Trainer", "", 1, 'C', 0, '', 0);
        */
        // Restore the auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);

        // Set the starting point for the page content
        $this->setPageMark();
    }
    
    public function SetTextDarkElite(){
        $this->SetTextColor(12,71,100);
    }

    public function SetTextBlack(){
        $this->SetTextColor(0,0,0);
    }

    public function SetTextGray(){
        $this->SetTextColor(51,51,51);
	}
	
    public function SetTextWhite(){
        $this->SetTextColor(255,255,255);
    }
}

class Certificate {
	var $pdf;
	
	function __construct($fullName, $rScore, $totalScore, $set_name, $dayTook, $monthTook, $yearTook, $venue){

		$pdf = new MyCustomPDFWithWatermark();

		// set raleway font
		$Raleway = TCPDF_FONTS::addTTFfont('font/Raleway.ttf', 'TrueTypeUnicode', '', 32);

		//set raleway font
		$Ralewaysemibb = TCPDF_FONTS::addTTFfont('font/RalewaySemiBold-Bold.ttf', 'TrueTypeUnicode', '', 32);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Jesse Dwight Hernandez');
		$pdf->SetTitle('Certificate');
		$yPointer = 20;

		$pdf->setFont('Helvetica', 'B', 32);
		$pdf->Text(20, $yPointer, 'CERTIFICATE OF COMPLETION');
		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 021', PDF_HEADER_STRING);
	
		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	
		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	
		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	
		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	
		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}
		// add a page
		$pdf->AddPage("L");
	
		// create some HTML content
		//$html = '<h1>Example of HTML text flow</h1>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. <em>Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?</em> <em>Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</em><br /><br /><b>A</b> + <b>B</b> = <b>C</b> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>B</i> = <i>A</i> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>A</i> = <i>B</i> -&gt; &nbsp;&nbsp; <b>A</b> + <b>B</b> = <b>C</b> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>B</i> = <i>A</i> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>A</i> = <i>B</i> -&gt; &nbsp;&nbsp; <b>A</b> + <b>B</b> = <b>C</b> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>B</i> = <i>A</i> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>A</i> = <i>B</i> -&gt; &nbsp;&nbsp; <b>A</b> + <b>B</b> = <b>C</b> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>B</i> = <i>A</i> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>A</i> = <i>B</i> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <b>A</b> + <b>B</b> = <b>C</b> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>B</i> = <i>A</i> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>A</i> = <i>B</i> -&gt; &nbsp;&nbsp; <b>A</b> + <b>B</b> = <b>C</b> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>B</i> = <i>A</i> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>A</i> = <i>B</i> -&gt; &nbsp;&nbsp; <b>A</b> + <b>B</b> = <b>C</b> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>B</i> = <i>A</i> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>A</i> = <i>B</i> -&gt; &nbsp;&nbsp; <b>A</b> + <b>B</b> = <b>C</b> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>B</i> = <i>A</i> &nbsp;&nbsp; -&gt; &nbsp;&nbsp; <i>C</i> - <i>A</i> = <i>B</i><br /><br /><b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u> <b>Bold</b><i>Italic</i><u>Underlined</u>';
	
	
		// ---------------------------------------------------------
		$darkelite = array(12, 71, 100);
		$black = array(0,0,0);
	
		$yPointer += 35;
		$pdf->SetY($yPointer);
		$pdf->SetFont($Ralewaysemibb, "", 40);
		// set font
		$pdf->SetTextDarkElite();
		$pdf->Cell(267, 5, "CERTIFICATE OF COMPLETION", "", 1, 'C', 0, '', 0);
		$pdf->Ln(5);
		$pdf->SetTextGray();
		$pdf->SetFont($Raleway, "", 12);
		$pdf->Cell(267, 5, "This certificate is hereby bestowed upon", "", 1, 'C', 0, '', 0);
	
		$pdf->Ln(5);
		$pdf->SetFont($Ralewaysemibb, "", 30);
		// set font
		$pdf->SetTextDarkElite();
		$pdf->Cell(267, 5, $fullName, "", 1, 'C', 0, '', 0);
		// output the HTML content
		$pdf->Ln(5);
		$yPointer += 50;
		$pdf->SetFont($Raleway, "", 12);
		$pdf->SetTextGray();
		$html = "<p style='text-align:center;'>for successfully completing the test with a score of {$rScore}</span>  ({$totalScore}%)<br>
		therefore, reaching the required level of competency in <strong style='text-decoration:underline;'>{$set_name}</strong>  <br> conducted by <b>EliteInsure Limited</b>.</p>";
	
		$pdf->writeHTMLCell(267, 5, 15, $yPointer, $html,0 , 1,0, true, 'C');
		$pdf->Ln(5);
		$pdf->Cell(267, 5, "Awarded this {$dayTook} of {$monthTook}, {$yearTook}", "", 1, 'C', 0, '', 0);
	
		$pdf->Image('img/signature001.png', 135.8, 150, 25.4, 19.4, '', '', '', false, 300, '', false, false, 0);
		$pdf->Line(120.8, 167, 176.2, 167);
	
		$pdf->Ln(33);
		$pdf->Cell(267, 5, "Sumit Monga", "", 1, 'C', 0, '', 0);
		$pdf->Cell(267, 5, "Trainer", "", 1, 'C', 0, '', 0);
		// reset pointer to the last page
		$pdf->lastPage();
	
		// ---------------------------------------------------------
	
		$this->pdf = $pdf;
	}
}

//variables
$idTest = $app->param($_GET, "id", 0);
$dataset = $testController->getTestDetail($idTest);


//display
$headers = array("#","Test Detail", "Score");
$rows = $app->getHeader($headers);
$now = date("d F, Y");

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
		$set_name = isset($row["set_name"]) ? $row["set_name"] : "SET 1";
		$venue = isset($row["venue"]) ? $row["venue"] : "";

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
$assessment = "";

if ($dataset->num_rows > 0) {
	while ($row = $dataset->fetch_assoc()) {
		$idUserTestined = $row["id_user_tested"];
		$firstName = $row["first_name"];
		$fullName = ucwords($row["last_name"] . ", " . $row["first_name"]);
		$emailAddress = $row["email_address"];
		$totalScore = $row["score"];
		$dateTook = $row["date_took"];
		$maxScore = $row["max_score"];
		$setName = $row["set_name"];
		$timeTook = $row["time_took"];
        $dateNow = $row["date_now"];
        
        $rScore = $totalScore;  //raw score
		
		$totalScore = (($totalScore / $maxScore) * 100);
        $tScore = number_format((float)$totalScore, 2, '.', '');
		if ($tScore >= 80) {
            $totalScore = "<span style=\"color:#0c4664;font-weight:bold;\">$tScore</span>";
            $assessment = "passed";
		}
		else {
			$totalScore = "<span style=\"color:#CA4A4A;font-weight:bold;\">$tScore</span>";
            $assessment = "failed";
        }
        
        $dateTook = date_create_from_format("d/M/Y", $dateTook);
        $dayTook = $dateTook->format("dS");
        $monthTook = $dateTook->format("F");
        $yearTook = $dateTook->format("Y");
	}
}

//formulate the filename
//<adviser name>_compliance101_testresults<DDMMYYYY>
$pdfFirstName = str_replace(" ", "_", $firstName);
$pdfSetName = str_replace(" ", "_" , $setName);
$pdfFilename = "{$firstName}_{$pdfSetName}_certificate_{$dateNow}.pdf";
$pdfFilename = strtolower($pdfFilename);

$certificate = new Certificate($fullName, $rScore, $totalScore, $set_name, $dayTook, $monthTook, $yearTook, $venue);

$email = $app->param($_GET, "email", ""); // if email parameter exists in the URL it means that the user is requesting to send a copy via email; if non display PDF instead;
$view = $app->param($_GET, "view", "");
$isForMailing = $email != "" ? 1 : 0;
if ($isForMailing == 1) {
	$content = $certificate->pdf->Output("","S");
	
	// Create instance of Swift_Attachment with our PDF file
	$attachment = (new Swift_Attachment($content, $pdfFilename, 'application/pdf'));

	$message = new Swift_Message();
	$message->setSubject('Assessment Certificate');
	//$message->setFrom(array('executive.admin@eliteinsure.co.nz' => 'EliteInsure'));
	//Remove the venue at the certificate.
	//Move date to footer.

	$message->setFrom(array('executive.admin@eliteinsure.co.nz' => 'EliteInsure'));
	$message->setTo(array($email));
	//$message->setTo(array("programmingwhilesleeping@gmail.com"));
	//$message->setTo(array("jesse@eliteinsure.co.nz"));
	
	$message->setBcc(array('compliance@eliteinsure.co.nz' => 'Compliance'));
	$message->setBcc(array('admin@eliteinsure.co.nz' => 'Admin'));
	
	$message->setBody('Dear ' . ucfirst($firstName) . ',

Attached is your Certificate of Completion for reaching the required level of competency for the ' . $set_name . '



Regards,

Leif');
	$message->attach($attachment);

	$transport = (new Swift_SmtpTransport('eliteinsure.co.nz', 587))
	->setUsername('wilfred@eliteinsure.co.nz')
  	->setPassword('wilfred2000');

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
    $certificate->pdf->Output($pdfFilename,"I");
}
?>