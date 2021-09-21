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

$session = new SessionHelper();
$app = new GeneralHelper();

$access = $app->param($_SESSION, 'grant', -1);

if ('yes' != $access) {
    header('location: login_trainee?type=trainer');
}

$trainingController = new TrainingController();
$action = $app->param($_POST, 'action');
$delete = $app->param($_POST, 'delete');

$message = '';

$currentSessionFirstName = $app->param($_SESSION, 'first_name', 'User');
$currentSessionID = $app->param($_SESSION, 'id_user', -1);

$tID = $app->param($_GET, 'id');

$newDateTime = '';
$trainDate = '';
$venue = '';
$topics_title = '';
$attendeeUID = '';
$topic_id = '';
$uLevel = '';
$uType = '';
$host_name = '';
$comp_name = '';

function formValidated($app, $tID)
{
    if (! $tID && ! $app->param($_POST, 'training_date')) {
        $_SESSION['errorMessage'] = 'Please provide a training date';

        return false;
    }

    if (! $app->param($_POST, 'training_venue')) {
        $_SESSION['errorMessage'] = 'Please provide a venue';

        return false;
    }

    if (! $app->param($_POST, 'host_name')) {
        $_SESSION['errorMessage'] = 'Please provide a trainer name';

        return false;
    }

    if (! $app->param($_POST, 'comp_name')) {
        $_SESSION['errorMessage'] = 'Please provide a company name';

        return false;
    }

    if (! $tID && ! $app->param($_POST, 'topic_type')) {
        $_SESSION['errorMessage'] = 'Please provide a nature of training / meeting';

        return false;
    }

    if (! $tID && ! in_array($_POST['topic_type'] ?? '', [1, 2])) {
        $_SESSION['errorMessage'] = 'Please provide a valid nature of training / meeting';

        return false;
    }

    if (! $app->param($_POST, 'training_attendee')) {
        $_SESSION['errorMessage'] = 'Please select attendees';

        return false;
    }

    return true;
}

if ('' != $tID) {
    $uTraining = $trainingController->getTrainingSpecific($tID);

    foreach ($uTraining as $row) {
        $trainDate = $row['training_date'];
        $uType = $row['training_type'];
        $newDateTime = date('d/m/Y h:i A', strtotime($trainDate));
        $venue = $row['training_venue'];
        $attendeeUID = $row['training_attendee'];
        $host_name = $row['host_name'];
        $comp_name = $row['comp_name'];
    }

    $dataTopicTitle = [];
    $dataTopicLevel = [];
    $dataTopicID = [];

    $uTopic = $trainingController->getTopic($tID);

    foreach ($uTopic as $row) {
        array_push($dataTopicTitle, $row['topic_title']);
        array_push($dataTopicLevel, $row['topic_level']);
        array_push($dataTopicID, $row['id']);
    }

    foreach ($uTopic as $row) {
        $topics_title .= $row['topic_title'] . ',';
        $uLevel .= $row['topic_level'] . ',';
        $topic_id .= $row['id'] . ',';
    }
    $topics_title = substr($topics_title, 0, -1);
    $uLevel = substr($uLevel, 0, -1);
    $topic_id = substr($topic_id, 0, -1);
}

if ('delete' == $delete) {
    $id = $app->param($_POST, 'id');
    $status = $trainingController->deletetopic($id);
}

if ('save_training' == $action) {
    if ($currentSessionID < 0) {
        header('location: login_trainee?type=trainer');
    } else {
        $topic_type = $app->param($_POST, 'topic_type');

        if ('1' == $topic_type) {
            $topic = $app->param($_POST, 'cpd_topic');
        } else {
            $topic = $app->param($_POST, 'trainig_topic');
        }

        $attendee = $app->param($_POST, 'training_attendee');

        if ('' != $app->param($_POST, 'training_date')) {
            $date = $app->param($_POST, 'training_date');
        } else {
            $format = date('Y-m-d H:i:s', strtotime($trainDate));
            $date = $format;
        }

        $venue = $app->param($_POST, 'training_venue');
        $attendee_id = $app->param($_POST, 'training_attendee');
        $trainer_id = $currentSessionID;
        $trainer_signature = $app->param($_POST, 'signature');
        $topic_level = $app->param($_POST, 'level_topic');
        $host_name = $app->param($_POST, 'host_name');
        $comp_name = $app->param($_POST, 'comp_name');

        if (formValidated($app, $tID)) {
            if ('' != $tID) {
                $topic_id_save = $app->param($_POST, 'topic_id');
                $dataset = $trainingController->updateTraining(
                    $trainer_id,
                    $topic,
                    implode(',', $attendee),
                    $date,
                    $venue,
                    implode(',', $attendee_id),
                    $uType,
                    $tID,
                    $topic_id_save,
                    $topic_level,
                    $host_name,
                    $comp_name
                ); ?>
                <script type="text/javascript">
                  swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Training session successfully updated',
                    showConfirmButton: false,
                    timer: 2500
                  }).then(function () {
                    window.location = 'training?page=training_list';
                  });
                </script>
                <?php
            } else {
                $dataset = $trainingController->addTraining(
                    $trainer_id,
                    $topic,
                    implode(',', $attendee),
                    $date,
                    $venue,
                    implode(',', $attendee_id),
                    $trainer_signature,
                    $topic_type,
                    $topic_level,
                    $host_name,
                    $comp_name
                );

                $_SESSION['successMessage'] = 'Training session saved.';
            }
        } else {
            $message = '<div class="alert alert-danger" role="alert">' . $_SESSION['errorMessage'] . '</div>';
        }
    }
}

$adviser = $trainingController->getAdviser();
$sets = '';

$arrAttendee = explode(',', $attendeeUID);

foreach ($adviser as $row) {
    try {
        $name = $row['first_name'] . ' ' . $row['last_name'];
        $id = $row['id_user'];

        if (in_array($id, $arrAttendee)) {
            $sets .= ' <option value="' . $id . '" selected="selected">' . $name . '</option>';
        } else {
            $sets .= '<option value="' . $id . '">' . $name . '</option>';
        }
    } catch (Exception $e) {
    }
}

$cpd = $trainingController->getCPD();
$cpdList = '';
$ctr = 0;

foreach ($cpd as $row) {
    $cpdTopic = $row['cpd_name'];
    $cpdDesc = $row['cpd_description'];
    $ctr++;

    $cpdList .= <<<EOF
    <input class="form-check-input mr-2 chkbox" type="checkbox" value="$cpdTopic" id="$ctr" name="cpd_topic[]"><label title = "$cpdDesc" class="form-check-label chkbox mr-4" for="$ctr">$cpdTopic</label></br>
EOF;
}
?>

<div class="subHeader">
  <div class="row">
    <div class="col title">
      Add New Training
    </div>
  </div>
</div>

<div align="container">
  <div class="row justify-content-md-center mt-4">
    <div class="col-3">
      <?php if (isset($_SESSION['errorMessage']) || isset($_SESSION['successMessage'])) { ?>
      <?php
            if (isset($_SESSION['errorMessage'])) {
                $alertType = 'alert-danger';
                $message = $_SESSION['errorMessage'];
            } elseif (isset($_SESSION['successMessage'])) {
                $alertType = 'alert-success';
                $message = $_SESSION['successMessage'];
            }
            ?>
      <div class="alert <?php echo $alertType; ?>" role="alert">
        <?php echo $message; ?>
      </div>
      <?php
            unset($_SESSION['errorMessage'], $_SESSION['successMessage']);
            ?>
      <?php } ?>
    </div>
  </div>
  <form method="post">
    <div class="row justify-content-md-center">
      <div class="col-sm-12 col-lg-3">
        <label class="font-weight-normal text-center">Training Date</label>
        <div class="form-group form-inline" id="datePicker" <?php if ('' != $newDateTime) {
                echo 'style=display:none;';
            } ?>>
          <input type="datetime-local" class="form-control" name="training_date" id="training_date" value="" /><a href="javascript:;" onclick="cancel()" title="Cancel"><i class="material-icons ml-2 block"
              style="font-size: 17px; color:red;">block</i></a>
        </div>
        <p id="dateText" style="font-size: 17px;">
          <?php echo $newDateTime; ?><a href="javascript:;" onclick="showDate()" title="Change Date"><i class="material-icons ml-2 edit-icon" id="edit-icon" style="font-size: 17px;">edit</i></a>
        </p>

      </div>
    </div>
    <div class="row justify-content-md-center">
      <div class="col-sm-12 col-lg-3">
        <label class="font-weight-normal text-center">Venue</label>
        <input type="text" placeholder="Venue" class="form-control mb-1" name="training_venue" aria-label="Large" aria-describedby="inputGroup-sizing-sm" value="<?php echo $venue; ?>">
      </div>
    </div>
    <br>
    <?php if (1 == $idUserType) { ?>
    <div class="row justify-content-md-center">
      <div class="col-sm-12 col-lg-3">
        <label class="font-weight-normal text-center">Trainer Name</label>
        <input type="text" placeholder="Trainer name" class="form-control mb-1" name="host_name" aria-label="Large" aria-describedby="inputGroup-sizing-sm" value="<?php echo $host_name; ?>">
      </div>
    </div>
    <br>
    <div class="row justify-content-md-center">
      <div class="col-sm-12 col-lg-3">
        <label class="font-weight-normal text-center">Company Name</label>
        <input type="text" placeholder="Company name" class="form-control mb-1" name="comp_name" aria-label="Large" aria-describedby="inputGroup-sizing-sm" value="<?php echo $comp_name; ?>">
      </div>
    </div>
    <br>
    <?php } ?>
    <div class="row justify-content-md-center">
      <div class="col-sm-12 col-lg-3">

        <?php if ('' != $topics_title) { ?>
        <label class="font-weight-normal text-center">Topics that will discuss</label>
        <div id="topicTag">
          <?php

                            $arr = explode(',', $topics_title);
                            $arr2 = explode(',', $topic_id);
                            $arr3 = explode(',', $uLevel);

                            $option1 = '';
                            $option2 = '';
                            $option3 = '';
                            $level = '';

                            for ($i = 0; $i < count($dataTopicTitle); $i++) {
                                $selected = '';

                                if ('0' == $dataTopicLevel[$i]) {
                                    $option1 = 'selected';
                                } elseif ('1' == $dataTopicLevel[$i]) {
                                    $option2 = 'selected';
                                } elseif ('2' == $dataTopicLevel[$i]) {
                                    $option3 = 'selected';
                                }

                                if ('1' != $uType) {
                                    $level = '<select class="form-control mb-1" id="' . $dataTopicID[$i] . '"  name="level_topic[]">
                         <option value="0"' . $option1 . '>Marketing</option>
                         <option value="1"' . $option2 . '>Product</option>
                         <option value="2"' . $option3 . '>Compliance</option>
                    </select>';
                                } else {
                                    $level = '';
                                }
                                echo '
                <div class="row">
                   <div class="col-lg-12">
                    <div class="input-group input-group-md">
                      <input type="hidden" value="' . count($dataTopicTitle) . '" id="numberChk">
                       <input type="hidden" value="' . $dataTopicID[$i] . '" name="topic_id[]">
                    <input type="text" placeholder="Topic 1" class="form-control mb-1"name="trainig_topic[]" id="' . $dataTopicID[$i] . '" aria-label="Large" aria-describedby="inputGroup-sizing-sm" value="' . $dataTopicTitle[$i] . '">' . $level . '
                      <span class="input-group-btn">
                        <a href="javascript:;" class="topic' . $i . '" id="' . $dataTopicID[$i] . '" onclick="removeTopic(this)" title="Remove Topic"><i class="material-icons ml-2 block" style="font-size: 17px; color:red;">delete</i></a>
                      </span>
                    </div>
                  </div>
                </div>';
                            }?>
        </div>
        <button type="button" onclick="addTopic()" class="btn btn-info width mt-1" <?php if (1 == $uType) {
                                echo "style='display:none;'";
                            } ?>
          >Add Topic</button>
        <?php } else { ?>

        <label class="font-weight-normal text-center">Nature Of Training / Meeting</label>
        <select class="form-control mb-1" id="natureTraining" onchange="show()" name="topic_type">
          <option value="0" disabled selected>Select Option</option>
          <option value="1">Continuing Professional Development (CPD)</option>
          <option value="2">Team Training</option>
        </select>

        <div class="teamTraining">
          </br>
          <input type="hidden" value="1" id="numberChk">
          <div id="topicTag">
            <label class="font-weight-normal text-center">Topics that will discuss</label>
            <input type="text" placeholder="Topic 1" class="form-control mb-1" name="trainig_topic[]" aria-label="Large" aria-describedby="inputGroup-sizing-sm">
            <select class="form-control mb-1" id="level_topic" name="level_topic[]">
              <option value="0">Marketing</option>
              <option value="1">Product</option>
              <option value="2">Compliance</option>
            </select>
          </div>
          <button type="button" onclick="addTopic()" class="btn btn-info width mt-1">Add Topic</button>
        </div>

        <div class="cpdTraining">
          </br>
          <label class="font-weight-normal text-center">Topics that will discuss</label>
          <div class="form-check">
            <?php echo $cpdList; ?>
          </div>
        </div>
        <?php } ?>
      </div>
    </div>
    <br>
    <div class="row justify-content-md-center">
      <div class="col-sm-12 col-lg-3">
        <label class="font-weight-normal text-center">Attendee on the training</label>
        <select class="adviser js-states form-control" multiple="multiple" name="training_attendee[]">
          <?php
                                    echo $sets;
                                ?>
        </select>
      </div>
    </div>
    <br>

    <div class="row justify-content-md-center" <?php if ('' != $topics_title) {
                                    echo "style='display:none'";
                                } ?>
      >
      <div class="col-3">
        <label class="font-weight-normal text-center">Add Signature</label>
        <div class="wrapper" style="margin-bottom: 5px;">
          <canvas style="border: 1px solid #ced4da;" id="signature-pad" class="signature-pad" width=400 height=200></canvas>
        </div>
        <button type="button" id="clear">Clear</button>
      </div>
    </div>
    <br>
    <div class="row justify-content-md-center">
      <div class="col-3">
        <input type="hidden" name="signature" id="imageUrl">
        <input type="hidden" name="action" value="save_training" />
        <input id="generate" type="submit" value="Save" o class="btn btn-info width100" />
        <br />
        <br />
      </div>
    </div>
  </form>
</div>
<style type="text/css">
  .select2-results__options {
    max-height: 500px;
  }

</style>
<script type="text/javascript">

  var canvas = document.getElementById('signature-pad');

  function resizeCanvas() {
    var ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);
  }

  window.onresize = resizeCanvas;
  resizeCanvas();

  var signaturePad = new SignaturePad(canvas, {
    backgroundColor: 'rgb(255, 255, 255)'
  });

  function get() {
    var data = signaturePad.toDataURL('image/png');
    $("#imageUrl").val(data);
  }

  document.getElementById('clear').addEventListener('click', function () {
    signaturePad.clear();
  });

  document.getElementById('generate').addEventListener('click', function () {
    var data = signaturePad.toDataURL('image/png');
    $("#imageUrl").val(data);
  });

  function addTopic() {
    var newID = parseInt($('#numberChk').val()) + 1;
    var new_input = "<input type='text' placeholder='Topic " + newID + "' id='topic" + newID + "' name='trainig_topic[]' class='form-control mb-1' aria-label='Large' aria-describedby='inputGroup-sizing-sm'><input type='hidden' value='0' name='topic_id[]'><select class='form-control mb-1' id='level_topic' name='level_topic[]''><option value='0'>Marketing</option><option value='1'>Product</option><option value='2'>Compliance</option></select>";

    $('#topicTag').append(new_input);
    $('#numberChk').val(newID);

  }

  function addAttendee() {
    var newID = parseInt($('#numberChkAt').val()) + 1;
    var new_input = "<input type='text' placeholder='Attendee " + newID + "' id='attendee" + newID + "' name='training_attendee[]' class='form-control mb-1' aria-label='Large' aria-describedby='inputGroup-sizing-sm'>";

    $('#attendeeTag').append(new_input);
    $('#numberChkAt').val(newID);

  }


  $(".adviser").select2({
    placeholder: "Select a adviser"
  });

  $(".cpd").select2({
    placeholder: "Select a topic"
  });
  $(".teamTraining").hide();
  $(".cpdTraining").hide();
  function show() {
    var id = $('#natureTraining :selected').val();
    if (id == 1) {
      $(".cpdTraining").show();
      $(".teamTraining").hide();
    } else {
      $(".teamTraining").show();
      $(".cpdTraining").hide();
    }
  }

  function showDate() {
    $('#datePicker').show();
    $('#dateText').hide();
  }

  function cancel() {
    $('#training_date').val('');
    $('#datePicker').hide();
    $('#dateText').show();
  }
  function removeTopic(id) {
    var id_count = $('[id=' + id.id + ']');
    if (id_count.length > 0) {
      $('[id=' + id.id + ']').remove();
    }
    $.ajax({
      url: 'training?page=training_add',
      type: 'post',
      data: {
        id: id.id,
        delete: 'delete'
      },
      success: function (data) {
        //console.log(data);
      }
    });


  }
</script>
<style type="text/css">
  .wrapper {
    position: relative;
    width: 400px;
    height: 200px;
    -moz-user-select: none;
    -webkit-user-select: none;
    -ms-user-select: none;
    user-select: none;
  }

  .signature-pad {
    position: absolute;
    left: 0;
    top: 0;
    width: 400px;
    height: 200px;
    background-color: white;
  }

  .chkbox {
    font-size: 15px;
  }

  <?php if ('' == $newDateTime) {
                                    echo '
.block {
      display: none;
    }

    .edit-icon {
      display: none;
    }
  }

  ';
                                }

  ?>

</style>
