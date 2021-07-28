<?php

require_once 'lib/General.helper.php';
require_once 'lib/Training.controller.php';
require_once __DIR__ . '/package/vendor/autoload.php';

$general = new GeneralHelper();
$training = new TrainingController();

$defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
$fontDirs = $defaultConfig['fontDir'];

$defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
$fontData = $defaultFontConfig['fontdata'];

$mpdf = new \Mpdf\Mpdf([
    'fontDir' => array_merge($fontDirs, [
        __DIR__ . '/font',
    ]),
    'fontdata' => $fontData + [
        'calibri' => [
            'R' => 'CALIBRI.TTF',
            'B' => 'CALIBRIB.TTF',
            'I' => 'CALIBRII.TTF',
            'BI' => 'CALIBRIZ.TTF',
        ],
    ],
    'default_font' => 'calibri',
]);

$trainer = [
    'date' => date('d/m/Y'),
    'training_date' => date('Y-m-d H:i:s'),
    'full_name' => 'Juan dela Cruz',
	'training_topic' => 'alpha, bravo, charlie, delta, echo, foxtrot',
];

$html = htmlHeader() . trainer($trainer) . adviser() . htmlFooter();

$mpdf->WriteHtml($html);
$mpdf->Output();

function htmlHeader()
{
    ob_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ATTESTATION RE MEETING/TRAINING</title>

	<link rel="stylesheet" href="css/pdf.css">
</head>

<body>
	<htmlpageheader name="page-header-first">
		<table class="header">
			<tr>
				<td class="header-left-box">&nbsp;</td>
				<td class="header-image"><img src="img/elitelogo.png" height="0.76in" />&nbsp;</td>
				<td class="header-title">ATTESTATION RE MEETING/TRAINING</td>
				<td class="header-right-box">&nbsp;</td>
			</tr>
		</table>
	</htmlpageheader>

	<htmlpageheader name="page-header">
		<table class="header">
			<tr>
				<td class="header-left-box">&nbsp;</td>
				<td class="header-image"><img src="img/elitelogo.png" height="0.76in" />&nbsp;</td>
				<td class="header-title">&nbsp;</td>
				<td class="header-right-box">&nbsp;</td>
			</tr>
		</table>
	</htmlpageheader>

	<htmlpagefooter name="page-footer">
		<table class="table-footer">
			<tr>
				<td class="footer-logo">
					<img src="img/logo.png" width="2.12in" />
				</td>
				<td class="footer-page">
					<a href="https://eliteinsure.co.nz" class="footer-link" target="_blank">
						www.eliteinsure.co.nz
					</a>&nbsp;|&nbsp;Page
					{PAGENO}
				</td>
			</tr>
		</table>
	</htmlpagefooter>

	<div class="margin">
		<?php
    $html = ob_get_contents();

    ob_end_clean();

    return $html;
}

function htmlFooter()
{
    ob_start(); ?>
	</div>
	</body>
	</html>
<?php
    $html = ob_get_contents();
    ob_end_clean();

    return $html;
}

function trainer($data)
{
    ob_start(); ?>

	<p>&nbsp;</p>
	<p>Date:&emsp;<span class="underline">&emsp;<?php echo $data['date']; ?>&emsp;</span></p>
	<p>&nbsp;</p>
	<p class="leading-8">
		This is to attest that I, <span class="underline">&emsp;<?php echo $data['full_name']; ?>&emsp;</span> an ADR/SADR of Eliteinsure Limited has conducted a training/ meeting on <span class="underline">&emsp;<?php echo $data['training_date']; ?>&emsp;</span> at <span class="underline">&emsp;<?php echo $data['training_date']; ?>&emsp;</span>.
	</p>
	<p>The topics that were discussed/trained to the attendee/s were:</p>


	<?php
    $html = ob_get_contents();

    ob_end_clean();

    return $html;
}

function adviser()
{
    ob_start(); ?>
<div class="page-break"></div>
<p>Capture 2 - paragraph and unordered list</p>
<ul>
	<li>alpha</li>
	<li>bravo</li>
	<li>charlie</li>
</ul>
<?php
    $html = ob_get_contents();

    ob_end_clean();

    return $html;
}
