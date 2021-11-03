<?php
/**
 * @name: test.php
 * @author: Gio
 * @desc:
 * master page for trainee/examinee that has access to the actual test page
 */
ob_start();

//secure the page

//include necessary files
include_once('lib/Session.helper.php');
include_once('lib/General.helper.php');
include_once('lib/Training.controller.php');
include_once('lib/IndetHelper.php');

$session = new SessionHelper();
$app = new GeneralHelper();
$trainingController = new TrainingController();

use Carbon\Carbon;

$idProfile = $app->param($_GET, 'id', 0);
$emailID = $app->param($_GET, 'email', 0);
$usType = $app->param($_GET, 'user_type', 0);

$sessID = $app->param($_SESSION, 'id_user', 0);
$sessUserType = $app->param($_SESSION, 'id_user_type', 0);

$attendedTraining = $trainingController->attendedTraining($idProfile);
$trAttended = '';

$cpdTraining = $trainingController->cpdAttended($idProfile);
$trAttended = '';

$adviserTeam = $trainingController->adviserTeam($idProfile);
$usProfile = $trainingController->getSpecificUser($idProfile);

$usName = '';
$email = '';
$fsp = '';
$rows = '';

$indet = new IndetHelper($emailID);

while ($row = $usProfile->fetch_assoc()) {
    $usName = $row['first_name'] . ' ' . $row['last_name'];
    $email = $row['email_address'];
    $fsp = $row['ssf_number'];
    $password = $row['password'];
}

while ($row = $attendedTraining->fetch_assoc()) {
    $topic = str_replace(',', '<br>', $row['training_topic']);
    $date = $row['training_date'];
    $trainerID = $row['trainer_id'];
    $newDateTime = date('d-m-Y h:i A', strtotime($date));

    if ('' == $row['host_name']) {
        $trainer = $row['fullname'];
    } else {
        $trainer = $row['host_name'];
    }
    $trainingID = $row['training_id'];
    $today = new DateTime();
    $status = '';

    $datasetRow = $trainingController->getTrainingTopic($id_user, $idUserType, $trainingID);
    $trow = '';
    $topicTitle = '';
    while ($trow = $datasetRow->fetch_assoc()) {
        $level = '';

        if ('0' == $trow['topic_level']) {
            $level = '(Marketing)';
        } elseif ('1' == $trow['topic_level']) {
            $level = '(Product)';
        } elseif ('' == $trow['topic_level']) {
            $level = '';
        } else {
            $level = '(Compliance)';
        }
        $topicTitle .= $trow['topic_title'] . ' ' . $level . '<br>';
    }
    $rows .= <<<EOF
      <tr>
        <td>{$newDateTime}</td>
        <td class="capitalize">{$topicTitle}</td>
        <td>{$trainer}</td>
      </tr>
      EOF;
}

$cpdList = '';

while ($row = $cpdTraining->fetch_assoc()) {
    $topic = str_replace(',', '<br>', $row['training_topic']);
    $date = $row['training_date'];
    $trainerID = $row['trainer_id'];
    $newDateTime = date('d-m-Y h:i A', strtotime($date));

    if ('' == $row['host_name']) {
        $trainer = $row['fullname'];
    } else {
        $trainer = $row['host_name'];
    }

    $trainingID = $row['training_id'];
    $today = new DateTime();
    $status = '';

    $datasetRow = $trainingController->getTrainingTopic($id_user, $idUserType, $trainingID);
    $trow = '';
    $topicTitle = '';

    while ($trow = $datasetRow->fetch_assoc()) {
        $level = '';

        if ('0' == $trow['topic_level']) {
            $level = '(Marketing)';
        } elseif ('1' == $trow['topic_level']) {
            $level = '(Product)';
        } elseif ('' == $trow['topic_level']) {
            $level = '';
        } else {
            $level = '(Compliance)';
        }
        $topicTitle .= $trow['topic_title'] . ' ' . $level . '<br>';
    }

    $cpdList .= <<<EOF
      <tr>
        <td>{$newDateTime}</td>
        <td class="capitalize">{$topicTitle}</td>
        <td>{$trainer}</td>
      </tr>
      EOF;
}

$adviserList = '';

$type = "";
if(in_array($usType, [2,7,8])){
    $type = 4;
}elseif($usType == 1){
    $type = 1;
}elseif($usType == 9) {
    $type = 3;
}elseif ($usType == 3) {
    $type = 5;
}elseif ($usType == 4) {
    $type = 2;
}
$pdpRate = $trainingController->pdpRate($idProfile, $type);

$dataset = $trainingController->alltimehour($idProfile);

//Eliteinsure Company Pointing Variable
$alltimehour = 0;
$minute = 0;

//Year Eliteinsure Company Pointing Variable
$yalltimehour = 0;
$yminute = 0;



//External Company Pointing
$hourtime = 0;
$minutetime = 0;

//Year External Company Pointing
$yhourtime = 0;
$yminutetime = 0;

//Final Computation
$allminute = 0;
$alltime = 0;
$totalPoints  = 0;

//Year Final Computation
$yallminute = 0;
$yalltime = 0;
$ytotalPoints  = 0;

$getTotalMinutes = 0;
$ygetTotalMinutes = 0;

$ygetMaxMinutes = 0;
$getMaxMinutes = 0;

$pointsEx = 0;
foreach($dataset as $row) {


    if( $row['id_user_type'] == 8 || $row['id_user_type'] == 7 && $row['comp_name'] == "" || $row['comp_name'] == 'Eliteinsure Limited' && $row['hour'] ){
        $alltimehour += $row['hour'];
        $minute += $row['minute'];

        if(in_array($row['id_user_type'], [7, 8])){
            $getTotalMinutes +=  $row['hour'] / 4;
            $getMaxMinutes += $row['minute'] / 240;
        }else{
            $getTotalMinutes += $row['hour'] / 2;
            $getMaxMinutes += $row['minute'] / 120;
        }

        $alltime = $alltimehour;
        $allminute = $minute;

   }elseif ($row['id_user_type'] == 1 && $row['comp_name'] != "Eliteinsure Limited" && $row['comp_name'] != "") {

        $alltimehour  += $row['hour'];
        $minute  += $row['minute'];

        $alltime = $alltimehour;

        $pointsEx = $row['hour'] * 60 + $row['minute'];

        $totalPoints += $pointsEx / 60;

        $allminute = $minute;

   }

if($row['year_date'] == date("Y")){
    if($row['id_user_type'] == 8 || $row['id_user_type'] == 7 && $row['comp_name'] == "" || $row['comp_name'] == 'Eliteinsure Limited'){

        $yalltimehour += $row['hour'];
        $yminute += $row['minute'];

        if(in_array($row['id_user_type'], [7, 8])){
            $ygetTotalMinutes +=  $row['hour'] / 4;
            $ygetMaxMinutes += $row['minute'] / 240;
        }else{
            $ygetTotalMinutes += $row['hour'] / 2;
            $ygetMaxMinutes += $row['minute'] / 120;
        }

        $yalltime = $yalltimehour;
        $yallminute = $yminute;

    }elseif ($row['id_user_type'] == 1 && $row['comp_name'] != "Eliteinsure Limited" && $row['comp_name'] != "") {

        $yalltimehour  += $row['hour'];
        $yminute  += $row['minute'];


        $yalltime = $yalltimehour;


        $pointsExY = $row['hour'] * 60 + $row['minute'];

        $ytotalPoints += $pointsExY / 60;

        $yallminute = $yminute;

    }
 }
}


$totalPoints += $getTotalMinutes + $getMaxMinutes;
$ytotalPoints += $ygetTotalMinutes + $ygetMaxMinutes;

$min = $allminute % 60;
$miny = $yallminute % 60;

$yalltime += floor($allminute / 60);
$alltime += floor($yallminute / 60);
$alltime .= '.' . $min;
$yalltime .= '.' . $miny;



while ($row = $adviserTeam->fetch_assoc()) {
    $sadr_id = $row['id_user'];
    $trianerList = $trainingController->getAttendee($sadr_id);

    while ($row = $trianerList->fetch_assoc()) {
        $adviser_name = $row['first_name'] . ' ' . $row['last_name'];
    }

    $adviserList .= <<<EOF
      <tr>
        <td>{$adviser_name}</td>
      </tr>
      EOF;
}

$adrTeam = $trainingController->adrTeam($idProfile);
$adrList = '';

while ($row = $adrTeam->fetch_assoc()) {
    $name = $row['first_name'];
    $usID = $row['id_user'];
    $usEmail = $row['email_address'];
    $usNumber = $row['id_user_type'];

    $adrList .= <<<EOF
      <tr><td>
          <a href="training?page=adviser_profile&id={$usID}&email={$usEmail}&user_type={$usNumber}" title="View Profile" class="delete" data-toggle="tooltip" data-placement="bottom">
        {$name}</a></td>
      </tr>
      EOF;
}

$icList = $trainingController->incidentList($emailID);
while ($row = $icList->fetch_assoc()) {

    $date_created = $row['date_created'];
    $report_number = $row['report_number'];
    $status = $row['irstat'];

    if($status == 1){
        $status = '<span class="badge bg-success" style="color:white;">Completed</span>';
    }else{
        $status = '<span class="badge bg-danger" style="color:white;">Not Completed</span>';
    }

    //https://onlineinsure.co.nz/cir-beta/admin/Compliance_Report?report_number=0034&type=0

    $incidentList .= <<<EOF
      <tr>
        <td>IR2021{$report_number}</td>
        <td>{$date_created}</td>
        <td><a href="https://onlineinsure.co.nz/cir-beta/admin/Compliance_Report?report_number={$report_number}&type=0" target="_blank">View Summary</a></td>
        <td>{$status}</td>
      </tr>
      EOF;
}

$adminadrTeam = $trainingController->adminadrTeam($idProfile);
$adminadrList = '';

while ($row = $adminadrTeam->fetch_assoc()) {
    $name = $row['first_name'];
    $usID = $row['id_user'];
    $usEmail = $row['email_address'];
    $usNumber = $row['id_user_type'];

    $adminadrList .= <<<EOF
      <tr>
        <td>
        <a href="training?page=adviser_profile&id={$usID}&email={$usEmail}&user_type={$usNumber}" title="View Profile" class="delete" data-toggle="tooltip" data-placement="bottom">
        {$name}</a></td>
      </tr>
      EOF;
}

$modTraining = $trainingController->getModularTraining($emailID);
$modList = '';

$topic = '';
$module_taken = '';
$score = '';
$result = '';

while ($row = $modTraining->fetch_assoc()) {
    $topic = $row['set_name'];
    $module_taken = $row['date_took'];
    $score = $row['score'];
    $maxScore = $row['max_score'];
    $result = "<span style='color: red'>FAILED</span>";
    $attempts = $row['attempts'];

    //score
    $score = (($score / $maxScore) * 100);
    $score = number_format((float) $score, 2, '.', '');

    if ($score >= 80) {
        $result = "<span style='color: green'>PASSED</span>";
    }

    $modList .= '
      <tr>
        <td>' . $topic . '</td>
        <td>' . $module_taken . '</td>
        <td>' . $score . '%</td>
        <td>' . $result . '</td>
        <td>' . $attempts . '</td>
      </tr>';
}

$authIsAdviser = in_array($usType, [2, 7, 8]) ? true : false;

$pendingIssuedPolicies = $indet->listPendingIssuedPolicies();

$clawbacks = $indet->listClawbacks();

$arrears = $indet->listArrears();

$submittedDeals = $indet->listSubmittedDeals();
?>
<style>
    .bg-shark {
        background-color: #2B3036
    }

    .bg-lmara {
        background-color: #0081B8
    }

    .bg-tblue {
        background-color: #0F6497
    }

    .bg-dsgreen {
        background-color: #0C4664
    }

    .text-shark {
        color: #2B3036
    }

    .text-lmara {
        color: #0081B8
    }

    .text-tblue {
        color: #0F6497
    }

    .text-dsgreen {
        color: #0C4664
    }

    #adviserProfileTab .nav-link {
        background-color: lightgray;
        text-transform: uppercase;
        letter-spacing: 0.125em;
        color: #2B3036;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
    }

    #adviserProfileTab .nav-link:first-child {
        margin-right: 0.25rem;
    }

    #adviserProfileTab .nav-link.active {
        background-color: white;
        font-weight: bold;
        color: #0081B8 !important;
    }

    .cpd {
        table-layout: fixed;
        width: 100%;
    }

    .cpd td {
        width: 25%;
        word-wrap: break-word;
    }

</style>
<div class="subHeader">
    <div class="row">
        <div class="col title">
            Member Profile
        </div>
    </div>
</div>

<div class="container-fluid mb-4">
    <div class="row">
        <div class="col-lg-3">
            <div class="card">
                <h5 class="card-header"></h5>
                <div class="card-body">
                    <p class="card-text">
                        <?php

                    if ('1' == $usType) {
                        echo 'Manager Account';
                    } elseif ('7' == $usType) {
                        echo 'ADR';
                    } elseif ('8' == $usType) {
                        echo 'SADR';
                    } elseif ('3' == $usType) {
                        echo 'Compliance Officer';
                    } elseif ('4' == $usType) {
                        echo 'Admin';
                    } elseif ('9' == $usType) {
                        echo 'IT Specialist';
                    } elseif ('5' == $usType) {
                        echo 'Face to Face Marketer';
                    } elseif ('6' == $usType) {
                        echo 'Telemarketer';
                    } else {
                        echo 'Adviser';
                    }
                ?>:
                        <?php echo $usName; ?>
                    </p>
                    <p>FSP:
                        <?php echo $fsp; ?>
                    </p>
                    <p>Email: <a href="mailto:<?php echo $email; ?>">
                            <?php echo $email; ?>
                        </a></p>

                    <?php
            if (($sessID == $idProfile) || (1 == $sessUserType)) {
                ?>
                    <p>Password:
                        <?php echo $password; ?>
                    </p>
                    <?php
            }
            ?>

                    <a href="<?php echo 'profilepdf?id=' . $idProfile . '&email=' . $emailID; ?>" class="sendEmail" target="_blank" title="Print Adviser Profile" data-toggle="tooltip" data-placement="bottom">

                        <button class="btn btn-primary btn-sm">Print to PDF</button>
                    </a>
                </div>
            </div>

            <div class="<?php echo in_array($usType, ['2', '7']) ? 'd-none' : null;  ?> mt-4">
                <div class="table-responsive">
                    <table class="table table-striped table-hover member">
                        <thead class="bg-dsgreen text-white">
                            <tr>
                                <th>ADR Team Member</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php echo $adminadrList; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="<?php echo in_array($usType, ['2', '8']) ? 'd-none' : null; ?> mt-4">
                <div class="table-responsive">
                    <table class="table table-striped table-hover member">
                        <thead class="bg-dsgreen text-white">
                            <tr>
                                <th>Adviser Team Member</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php echo '7' == $usType ? $adrList : $adviserList; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <ul class="nav nav-tabs mt-4 mt-lg-0" id="adviserProfileTab" role="tablist">
                <?php if ($authIsAdviser) { ?>
                <li class="nav-item">
                    <a class="nav-link active" id="dealTrackerTab" data-toggle="tab" href="#dealTrackerTabPanel" role="tab" aria-controls="home" aria-selected="true">Policy Tracker</a>
                </li>
                <?php } ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $authIsAdviser ? null : 'active'; ?>" id="trainDevTab" data-toggle="tab" href="#trainDevTabPanel" role="tab" aria-controls="profile" aria-selected="false">Training and
                        Development</a>
                </li>
                <?php if (!$authIsAdviser) { ?>
                <li class="nav-item">
                    <a class="nav-link" id="incidentTab" data-toggle="tab" href="#incident" role="tab" aria-controls="profile" aria-selected="false">Incident Report</a>
                </li>
                 <?php } ?>
            </ul>
            <div class="tab-content p-3 border border-top-0" id="adviserProfileTabContent">
                <?php
            if ($authIsAdviser) {
                ?>
                <div class="tab-pane fade show active" id="dealTrackerTabPanel" role="tabpanel" aria-labelledby="deal-tracker-tab">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h6 class="text-tblue mr-5" style="float: left;">Pending Issued Policies</h6> <span style="display: inline;">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="customSwitch5" data-toggle="collapse" data-target="#pendingCollapse" aria-expanded="false"
                                                aria-controls="pendingCollapse">
                                            <label class="custom-control-label" for="customSwitch5" style="font-size: 10px"></label>
                                        </div>
                                    </span>
                                </div>
                            </div>
                            <div class="collapse" id="pendingCollapse">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead>
                                            <tr class="bg-dsgreen text-white">
                                                <th>Life Insured</th>
                                                <th>Policy #</th>
                                                <th>Co.</th>
                                                <th>Issue Date</th>
                                                <th>API</th>
                                                <th>Record Keeping</th>
                                                <th>Comp. Admin</th>
                                                <th>Comp. CO</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                if ($pendingIssuedPolicies['currentDeals']->count()) {
                                    foreach ($pendingIssuedPolicies['currentDeals'] as $deal) {
                                        ?>
                                            <tr>
                                                <td>
                                                    <?php echo $deal['client_name_life_insured']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $deal['policy_number']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $deal['company']; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php echo Carbon::createFromFormat('Ymd', $deal['date_issued'])->format('d/m/Y'); ?>
                                                </td>
                                                <td class="text-right">$
                                                    <?php echo number_format($deal['issued_api'], 2); ?>
                                                </td>
                                                <td>
                                                    <?php echo $deal['record_keeping']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $deal['compliance_status']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $deal['audit_status']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $deal['notes']; ?>
                                                </td>
                                            </tr>
                                            <?php
                                    } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-lmara text-white">
                                                <th colspan="4" class="text-right">Total API:</th>
                                                <th class="text-right">$
                                                    <?php echo number_format($pendingIssuedPolicies['currentDeals']->sum('issued_api'), 2); ?>
                                                </th>
                                                <th colspan="4"></th>
                                            </tr>
                                        </tfoot>
                                        <?php
                                } else {
                                    ?>
                                        <tr>
                                            <td colspan="9">No available deals.</td>
                                        </tr>
                                        </tbody>
                                        <?php
                                } ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h6 class="mt-2 text-tblue mr-5" style="float: left;">Pipeline</h6>
                                    <span style="display: inline;">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="customSwitchPipeline" data-toggle="collapse" data-target="#pipelineCollapse" aria-expanded="false" aria-controls="pipelineCollapse">
                                            <label class="custom-control-label" for="customSwitchPipeline" style="font-size: 10px; margin-top: 5px;"></label>
                                        </div>
                                    </span>
                                </div>
                            </div>
                            <div id="pipelineCollapse" class="collapse">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead>
                                            <tr class="bg-dsgreen text-white">
                                                <th>Life Insured</th>
                                                <th>Company</th>
                                                <th>Submission Date</th>
                                                <th>API</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if ($submittedDeals->count()) {
                                                foreach ($submittedDeals as $deal) {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $deal->client_name_life_insured; ?></td>
                                                        <td><?php echo $deal->company; ?></td>
                                                        <td class="text-center"><?php echo Carbon::createFromFormat('Ymd', $deal->submission_date)->format('d/m/Y'); ?></td>
                                                        <td class="text-right">$<?php echo number_format($deal->original_api, 2); ?></td>
                                                    </tr>
                                                    <?php
                                                } ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr class="bg-lmara text-white">
                                                        <th colspan="3" class="text-right">Total API:</th>
                                                        <th class="text-right">$<?php echo number_format($submittedDeals->sum('original_api'), 2); ?></th>
                                                    </tr>
                                                </tfoot>
                                                <?php
                                            } else {
                                                ?>
                                                <tr>
                                                    <td colspan="4">No available deals.</td>
                                                </tr>
                                                </tbody>
                                                <?php
                                            } ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h6 class="mt-2  text-tblue mr-5" style="float: left;">Clawbacks and Possible Clawbacks</h6> <span style="display: inline;">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="customSwitch4" data-toggle="collapse" data-target="#clawCollapse" aria-expanded="false" aria-controls="clawCollapse">
                                            <label class="custom-control-label" for="customSwitch4" style="font-size: 10px"></label>
                                        </div>
                                    </span>
                                </div>
                            </div>
                            <div class="collapse" id="clawCollapse">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead>
                                            <tr class="bg-dsgreen text-white">
                                                <th>Client Name</th>
                                                <th>Insurer</th>
                                                <th>Policy Number</th>
                                                <th>Status</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                        if ($clawbacks['currentDeals']->count()) {
                                            foreach ($clawbacks['currentDeals'] as $deal) { ?>
                                            <tr>
                                                <td>
                                                    <?php echo $deal['client_name_life_insured']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $deal['company']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $deal['policy_number']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $deal['clawback_status']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $deal['clawback_notes'] ?? ''; ?>
                                                </td>
                                            </tr>
                                            <?php }
                                        } else { ?>
                                            <tr>
                                                <td colspan="5">No available deals.</td>
                                            </tr>
                                            <?php
                                        } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h6 class="mt-2 text-tblue mr-5" style="float: left;">Arrears Tracker</h6> <span style="display: inline;">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="customSwitch6" data-toggle="collapse" data-target="#arrearCollapse" aria-expanded="false"
                                                aria-controls="arrearCollapse">
                                            <label class="custom-control-label" for="customSwitch6" style="font-size: 10px"></label>
                                        </div>
                                    </span>
                                </div>
                            </div>
                            <div class="collapse" id="arrearCollapse">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-striped">
                                        <thead>
                                            <tr class="bg-dsgreen text-white">
                                                <th>Client Name</th>
                                                <th>Insurer</th>
                                                <th>Policy Number</th>
                                                <th>Status</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                        if ($arrears->count()) {
                                            foreach ($arrears as $deal) {
                                                ?>
                                            <tr>
                                                <td>
                                                    <?php echo $deal->client_name_life_insured; ?>
                                                </td>
                                                <td class="text-nowrap">
                                                    <?php echo $deal->company; ?>
                                                </td>
                                                <td class="text-nowrap">
                                                    <?php echo $deal->policy_number; ?>
                                                </td>
                                                <td class="text-nowrap">
                                                    <?php echo $deal->arrear_status; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php echo $deal->arrear_notes ?? ''; ?>
                                                </td>
                                            </tr>
                                            <?php
                                            }
                                        } else {
                                            ?>
                                            <td colspan="5">No available deals.</td>
                                            <?php
                                        } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
                <div class="tab-pane fade <?php echo $authIsAdviser ? null : 'show active'; ?>" id="trainDevTabPanel" role="tabpanel" aria-labelledby="training-and-development-tab">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h6 class="text-tblue mr-5" style="float: left;">
                                        <?php if (in_array($idUserType, [2, 7, 8, 3])) { ?>
                                        Personal Development Program
                                        <?php } else { ?>
                                        Onboarding
                                        <?php } ?>
                                    </h6><span style="display: inline;">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="customSwitch1" data-toggle="collapse" data-target="#pdpCollapse" aria-expanded="false" aria-controls="pdpCollapse">
                                            <label class="custom-control-label" for="customSwitch1" style="font-size: 10px"></label>
                                        </div>
                                    </span>
                                </div>
                            </div>
                            <div class="collapse" id="pdpCollapse">
                                <h6 class="pdpTable">Completion:
                                    <?php echo $pdpRate; ?>%
                                </h6>
                                <div class="table-responsive pdpTable">
                                    <table class="table table-striped table-hover cpd" style="border: 1px solid lightgray;">
                                        <thead class="bg-dsgreen text-white">
                                            <tr>
                                                <th>Training Date</th>
                                                <th>Topic Trained</th>
                                                <th>Trainer</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php echo $cpdList; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row mt-2">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h6 class="text-tblue mr-5" style="float: left;">Continuing Professional Development</h6> <span style="display: inline;">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="customSwitch2" data-toggle="collapse" data-target="#cpdCollapse" aria-expanded="false" aria-controls="cpdCollapse">
                                            <label class="custom-control-label" for="customSwitch2" style="font-size: 10px"></label>
                                        </div>
                                    </span>
                                </div>
                            </div>
                            <div class="collapse" id="cpdCollapse">
                                <div class="row mb-1 cpdTable">
                                    <div class="col-lg-3">
                                        <h6>Hours(Total):
                                            <?php echo number_format((float) $alltime, 2, '.', ''); ?>
                                        </h6>
                                    </div>
                                    <div class="col-lg-3">
                                        <h6>Points(Total):
                                            <?php echo $totalPoints; ?>
                                        </h6>
                                    </div>
                                    <div class="col-lg-3">
                                        <h6>Hours(Current Year):
                                            <?php echo number_format((float) $yalltime, 2, '.', ''); ?>
                                        </h6>
                                    </div>
                                    <div class="col-lg-3">
                                        <h6>Point(Current Year):
                                            <?php echo $ytotalPoints; ?>
                                        </h6>
                                    </div>
                                </div>
                                <div class="table-responsive cpdTable">
                                    <table class="table table-striped table-hover team" style="border: 1px solid lightgray;">
                                        <thead class="bg-dsgreen text-white">
                                            <tr>
                                                <th>Training Date</th>
                                                <th>Topic Trained</th>
                                                <th>Trainer</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php echo $rows; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h6 class="text-tblue mr-5" style="float: left;">Tests/Assesstments Result</h6> <span style="display: inline;">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="customSwitch3" data-toggle="collapse" data-target="#assCollapse" aria-expanded="false" aria-controls="assCollapse">
                                            <label class="custom-control-label" for="customSwitch3" style="font-size: 10px"></label>
                                        </div>
                                    </span>
                                </div>
                            </div>
                            <div class="collapse" id="assCollapse">
                                <div class="table-responsive modularTable">
                                    <table class="table table-striped table-hover modular" style="border: 1px solid lightgray;">
                                        <thead class="bg-dsgreen text-white">
                                            <tr>
                                                <th>Topics Trained On</th>
                                                <th>Module Take</th>
                                                <th>Score</th>
                                                <th>Results</th>
                                                <th>No. of Attempts</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                            echo $modList;
                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="tab-pane fade" id="incident" role="tabpanel">
                   <div class="row mt-2">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h6 class="text-tblue mr-5" style="float: left;">Incident Report</h6> <span style="display: inline;">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="customSwitch9" data-toggle="collapse" data-target="#irCollapse" aria-expanded="false" aria-controls="irCollapse">
                                            <label class="custom-control-label" for="customSwitch9" style="font-size: 10px"></label>
                                        </div>
                                    </span>
                                </div>
                            </div>
                            <div class="collapse" id="irCollapse">
                                <div class="table-responsive modularTable">
                                    <table class="table table-striped table-hover modular" style="border: 1px solid lightgray;">
                                        <thead class="bg-dsgreen text-white">
                                            <tr>
                                                <th>IR Number</th>
                                                <th>Date</th>
                                                <th>Summary</th>
                                                <th>Results</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                echo $incidentList;
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Incident Report Summary</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            
      </div>
      <div class="modal-footer">
        
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.member').DataTable();
        $('.modular').DataTable();
        $('.cpd').DataTable();
        $('.team').DataTable();
    });
</script>
