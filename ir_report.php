<?php

//include necessary files
include_once("lib/Session.helper.php");
include_once("lib/General.helper.php");
include_once("lib/Training.controller.php");

$session = new SessionHelper();
$app = new GeneralHelper();
$trainingController = new TrainingController();

$idProfile = $app->param($_GET, "id", 0);
$to_date = $app->param($_GET, "to_date", 0);
$from_date = $app->param($_GET, "from_date", 0);
$status = $app->param($_GET, "status", 0);
$currentSessionID = $app->param($_SESSION, 'id_user', -1);


$usProfile = $trainingController->getSpecificUser($idProfile);

$sesseProfile = $trainingController->getSpecificUser($currentSessionID);


$icList = $trainingController->getIRHistory($idProfile,$to_date,$from_date,$status);

$n_from_date =  date('d-m-Y', strtotime($from_date)); 
$n_to_date =  date('d-m-Y', strtotime($to_date)); 

$today = date('d-m-Y');

while ($row = $usProfile->fetch_assoc()) {
  $usName = $row["first_name"] .' '.$row["last_name"];
}
while ($row = $sesseProfile->fetch_assoc()) {
  $sessName = $row["first_name"] .' '.$row["last_name"];
}

while ($row = $icList->fetch_assoc()) {

    $date_created = $date_created = date('d-m-Y', strtotime($row['date_created'])); 
    $report_number = $row['report_number'];
    $result = $row['irstat'];
  
    $liable = substr($row['finalisation'], -7, 1);

    if($liable == 1){
        $status = 'Liable';
    }else{
        $status = 'Not Liable';
    }

    if($result == 1){
        $result = 'Completed';
    }else{
        $result = 'Not Completed';
    }


    $incidentList .= <<<EOF
      <tr>
        <td>IR2021{$report_number}</td>
        <td>{$date_created}</td>
        <td>{$result}</td>
        <td>{$status}</td>
      </tr>
      EOF;
}


$html = '
<style>
  .table-head {
    border: 1px solid #dddddd;
    font-family: arial, sans-serif;
    font-size: 10px;
    border-collapse: collapse;
    width: 100%;
  }

  .table-head td, th {
    text-align: left;
    padding: 8px;
  }

  .table-head tr:nth-child(even) {
    background-color: #dddddd;
  }
 
</style>

<div>
  <table class="table-head">
    <tr>
      <th colspan="4">Contractor/Employee Information</th>
    </tr>
    <tr>
      <td>Name:</td>
      <td>'.$usName.'</td>
    </tr>
    <tr>
      <td>Prepared By:</td>
      <td>'.$sessName.'</td>
    </tr>
    <tr>
      <td>Prepared Date:</td>
      <td>'.$today.'</td>
    </tr>
     <tr>
      <td>Period Cover:</td>
      <td>From: '.$n_from_date.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;To: '.$n_to_date.'</td>
    </tr>
  </table>

  <br><br>
  <table class="table-head" width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <th colspan="4">Incident Report</th>
    </tr>
    <tr>
      <th>IR Number</th>
      <th>Date</th>
      <th>Status</th>
      <th>Result</th>
    </tr>
    '.$incidentList.'
  </table>
</div>';

$htmlHeader = '<div style="position:absolute;top:0.26in;left:0in;width:90px;line-height:0.27in; background-color: #455a73;height:70px;">
          <span style="background-colro:red"></span>
      </div>

      <div style="position:absolute;top:0.18in;left:1.20in;width:4.36in;line-height:0.27in;">
        <img src="img/elitelogo.png" alt="eliteinsure" class="logo" width="100"/>
      </div>

      <div style="position:absolute;top:0.72in;left:4.4in;width:5.36in;line-height:0.27in;">
        <span style="font-style:normal;font-weight:bold;font-size:15pt;font-family:Calibri;color:#44546a">INCIDENT REPORT HISTORY</span>
      </div>

      <div style="position:absolute;top:0.26in;left:7.4in;width:90px;line-height:0.27in; background-color: #1881c7;height:70px;">
          <span style="background-colro:red"></span>
      </div>';

$htmlFooter = <<<EOF
  <div class="footer" style="font-size:6pt;"> 
    <img src="img/logo.png" alt="eliteinsure" class="logo" width="200"/>
  </div>
EOF;

$mpdf = new \Mpdf\Mpdf();
ob_clean();
$mpdf->AddPage('P','','','','',24,22,34,25,10,10);
$mpdf->SetHTMLHeader($htmlHeader,'',true);
$mpdf->SetHTMLFooter($htmlFooter);
$mpdf->WriteHTML($html);
$mpdf->Output('Adviser Training Report.pdf', "I"); 
?>