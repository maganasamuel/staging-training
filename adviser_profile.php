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
$indet = new IndetHelper();

use Carbon\Carbon;

$idProfile = $app->param($_GET, 'id', 0);
$emailID = $app->param($_GET, 'email', 0);
$usType = $app->param($_GET, 'user_type', 0);

$sessID = $app->param($_SESSION, 'id_user', 0);
$sessUserType = $app->param($_SESSION, 'id_user_type', 0);

$attendedTraining = $trainingController->attendedTraining($idProfile);
$trAttended = '';

$cpdTraining = $trainingController->cpdTraining($idProfile);
$trAttended = '';

$adviserTeam = $trainingController->adviserTeam($idProfile);
$usProfile = $trainingController->getSpecificUser($idProfile);

$usName = '';
$email = '';
$fsp = '';
$rows = '';

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

$deals = $indet->listDeals($emailID);
?>
<style>
  .bg-shark { background-color: #2B3036 }
  .bg-lmara { background-color: #0081B8 }
  .bg-tblue { background-color: #0F6497 }
  .bg-dsgreen { background-color: #0C4664 }

  .text-shark { color: #2B3036 }
  .text-lmara { color: #0081B8 }
  .text-tblue { color: #0F6497 }
  .text-dsgreen { color: #0C4664 }

  #adviserProfileTab .nav-link{
    text-transform: uppercase;
    letter-spacing: 0.125em;
    color: #2B3036;
  }

  .nav-link.active{
    font-weight: bold;
    color: #0081B8 !important;
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
          <p class="card-text">Adviser: <?php echo $usName; ?></p>
          <p>FSP: <?php echo $fsp; ?></p>
          <p>Email: <a href="mailto:<?php echo $email; ?>"> <?php echo $email; ?></a></p>

          <?php
          if (($sessID == $idProfile) || (1 == $sessUserType)) {
              ?>
              <p>Password: <?php echo $password; ?></p>
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
          <table class="table table-hoverable member">
            <thead style="background-color:#e9ecef;">
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
          <table class="table table-hoverable member">
            <thead style="background-color:#e9ecef;">
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
          <a class="nav-link <?php echo $authIsAdviser ? null : 'active'; ?>" id="trainDevTab" data-toggle="tab" href="#trainDevTabPanel" role="tab" aria-controls="profile" aria-selected="false">Training and Development</a>
        </li>
      </ul>
      <div class="tab-content p-3 border border-top-0" id="adviserProfileTabContent">
        <?php
        if ($authIsAdviser) {
            ?>
            <div class="tab-pane fade show active" id="dealTrackerTabPanel" role="tabpanel" aria-labelledby="deal-tracker-tab">
              <div class="row">
                <div class="col-lg-12">
                  <h6 class="text-tblue">
                    Previous Period - <?php echo $deals['previousPeriod']['from']->ordinal('day') . $deals['previousPeriod']['from']->format(' F Y') . ' to ' . $deals['previousPeriod']['to']->ordinal('day') . $deals['previousPeriod']['to']->format(' F Y'); ?>
                  </h6>
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                      <thead><tr class="bg-dsgreen text-white">
                        <th>Life Insured</th>
                        <th>Policy #</th>
                        <th>Co.</th>
                        <th>Issue Date</th>
                        <th>API</th>
                        <th>Record Keeping</th>
                        <th>Comp. Admin</th>
                        <th>Comp. CO</th>
                        <th>Notes</th>
                      </tr></thead>
                      <tbody>
                        <?php
                        if ($deals['previousDeals']->count()) {
                            foreach ($deals['previousDeals'] as $deal) {
                                ?>
                                <tr>
                                  <td><?php echo $deal['client_name_life_insured']; ?></td>
                                  <td><?php echo $deal['policy_number']; ?></td>
                                  <td><?php echo $deal['company']; ?></td>
                                  <td class="text-center"><?php echo Carbon::createFromFormat('Ymd', $deal['date_issued'])->format('d/m/Y'); ?></td>
                                  <td class="text-right">$<?php echo number_format($deal['issued_api'], 2); ?></td>
                                  <td><?php echo $deal['record_keeping']; ?></td>
                                  <td><?php echo $deal['compliance_status']; ?></td>
                                  <td><?php echo $deal['audit_status']; ?></td>
                                  <td><?php echo $deal['notes']; ?></td>
                                </tr>
                                <?php
                            } ?>
                            </tbody>
                            <tfoot>
                              <tr class="bg-lmara text-white">
                                <th colspan="4" class="text-right">Total API:</th>
                                <th class="text-right">$<?php echo number_format($deals['previousDeals']->sum('issued_api'), 2); ?></th>
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
              <div class="row mt-4">
                <div class="col-lg-12">
                  <h6 class="text-tblue">
                    Current Period - <?php echo $deals['currentPeriod']['from']->ordinal('day') . $deals['currentPeriod']['from']->format(' F Y') . ' to ' . $deals['currentPeriod']['to']->ordinal('day') . $deals['currentPeriod']['to']->format(' F Y'); ?>
                  </h6>
                  <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                      <thead><tr class="bg-dsgreen text-white">
                        <th>Life Insured</th>
                        <th>Policy #</th>
                        <th>Co.</th>
                        <th>Issue Date</th>
                        <th>API</th>
                        <th>Record Keeping</th>
                        <th>Comp. Admin</th>
                        <th>Comp. CO</th>
                        <th>Notes</th>
                      </tr></thead>
                      <tbody>
                        <?php
                        if ($deals['currentDeals']->count()) {
                            foreach ($deals['currentDeals'] as $deal) {
                                ?>
                                <tr>
                                  <td><?php echo $deal['client_name_life_insured']; ?></td>
                                  <td><?php echo $deal['policy_number']; ?></td>
                                  <td><?php echo $deal['company']; ?></td>
                                  <td class="text-center"><?php echo Carbon::createFromFormat('Ymd', $deal['date_issued'])->format('d/m/Y'); ?></td>
                                  <td class="text-right">$<?php echo number_format($deal['issued_api'], 2); ?></td>
                                  <td><?php echo $deal['record_keeping']; ?></td>
                                  <td><?php echo $deal['compliance_status']; ?></td>
                                  <td><?php echo $deal['audit_status']; ?></td>
                                  <td><?php echo $deal['notes']; ?></td>
                                </tr>
                                <?php
                            } ?>
                            </tbody>
                            <tfoot>
                              <tr class="bg-lmara text-white">
                                <th colspan="4" class="text-right">Total API:</th>
                                <th class="text-right">$<?php echo number_format($deals['currentDeals']->sum('issued_api'), 2); ?></th>
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
            <?php
        }
        ?>
        <div class="tab-pane fade <?php echo $authIsAdviser ? null : 'show active'; ?>" id="trainDevTabPanel" role="tabpanel" aria-labelledby="training-and-development-tab">
          <div class="row">
            <div class="col-lg-12">
              <h6 class="text-tblue">Continuing Professional Development</h6>
              <div class="table-responsive">
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
          <div class="row mt-4">
            <div class="col-lg-12">
              <h6 class="text-tblue">Modular Training</h6>
              <div class="table-responsive">
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
          <div class="row mt-4">
            <div class="col-lg-12">
              <h6 class="text-tblue">Team Training</h6>
              <div class="table-responsive">
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
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready( function () {
      $('.member').DataTable();
      $('.modular').DataTable();
      $('.cpd').DataTable();
      $('.team').DataTable();
  });
</script>