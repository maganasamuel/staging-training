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
include_once("lib/User.controller.php");

$app = new GeneralHelper();
$testController = new TestController();
$userController = new UserController();

//variables
$currentSessionFirstName = $app->param($_SESSION, "first_name", "User");
$currentSessionUserType = $app->param($_SESSION, "id_user_type", -1);
$currentSessionUserID = $app->param($_SESSION, "id_user", -1);
$action = $app->param($_GET, "action");
//delete a test
if ($action == "del") {
    $idTest = $app->param($_GET, "id", 0);
    $deleteDataset = $testController->testDelete($idTest);
} elseif ($action == "answer_sheet") {
    $idTest = $app->param($_GET, "id", 0);
    $answer_sheet_result = $testController->setTestAnswerSheet($idTest);
}

//sets

$setDataset = null;

switch ($currentSessionUserType) {
    case 1:
        $setDataset = $testController->getSetAll(-1);
        break;
    case 3:
        $setDataset = $userController->getTrainerTestSetAccess($currentSessionUserID);
        break;
}
$sets = "";
$setsOptions = "";
$ctr = 0;
$view = $app->param($_GET, "view", 1);
$adviser_name = $app->param($_GET, "adviser_name", null);

if (count($setDataset) > 0) {
    foreach($setDataset as $row) {
        $idSet = $row["id_set"];

        if ($currentSessionUserType == 3 && $ctr == 0 && $view == 1)
            $view = $idSet;

        $setName = $row["set_name"];
        $active = ($view == $idSet) ? "active" : "";
        $sets .= <<<EOF
        <li class="list-group-item {$active}">
            <a href="index.php?page=test_result&view={$idSet}">
                {$setName}
            </a>
        </li>
EOF;
        
        if($idSet == $view)
            $setsOptions .= "<option value='{$idSet}' selected>{$setName}</option>";
        else
            $setsOptions .= "<option value='{$idSet}'>{$setName}</option>";

        $ctr++;
    }
}

//display
$dataset = $testController->getFilteredTestAll($view,$adviser_name);

// var_export($dataset);die();
$headers = array("Date Took", "Adviser Name", "E-mail Address", "Duration (H:M:S)", "Score (%)", "Action", "<div class=\"text-center\" data-toggle=\"modal\" data-target=\"#exampleModalCenter\">Bulk Email<br><i class=\"material-icons select-all\" style=\"cursor: pointer;\">mail</i></div>");
$tableHeader = $app->getHeader($headers);
$rows = $tableHeader;

if ($dataset->num_rows <= 0) {
    $rows .= $app->emptyRow(count($headers));
} else {
    while ($row = $dataset->fetch_assoc()) {
        $idSet = $row["id_set"];
        if ($idSet != $view) {
            continue;
        }

        $idTest = $row["id_test"];
        $dateTook = $row["date_took"];
        $idUserChecked = $row["id_user_checked"];
        $dateChecked = $row["date_checked"];
        $firstName = $row["first_name"];
        $lastName = $row["last_name"];
        $emailAddress = $row["email_address"];
        $score = $row["score"];
        $dateCompleted = $row["date_completed"];
        $timeTook = $row["time_took"];
        $setName = $row["set_name"];
        $maxScore = $row["max_score"];
        $isAutoCheck = $row["is_auto_check"];
        $idUserTypeTest = $row["id_user_type_test"];

        //modify display

        //fullName
        $fullName = "{$lastName}, {$firstName}";
        $fname = $firstName . " " . $lastName;

        //score
        $score = (($score / $maxScore) * 100);
        $score = number_format((float) $score, 2, '.', '');
        if ($score >= 80) {
            $totalScore = "<span style=\"color:#3AA237;font-weight:bold;\">$score %</span>";
        } else {
            $totalScore = "<span style=\"color:#CA4A4A;font-weight:bold;\">$score %</span>";
        }
        $totalScore = $idUserChecked == 0 ? "Unchecked" : $totalScore;

        //check link
        $checkLink = $isAutoCheck == 1 ? "" : "<a href=\"index.php?page=test_check&id={$idTest}&email={$emailAddress}\" title=\"Check Test\"><i class=\"material-icons\">playlist_add_check</i></a>";

        //mail link
        $mailLink = $totalScore == "Unchecked" ? "" : "<a href=\"index.php?page=test_mail&id={$idTest}&email={$emailAddress}&view={$view}\" title=\"Mail Test\"><i class=\"material-icons\">mail</i></a>";

        //pdf link
        //$pdfLink = $totalScore == "Unchecked" ? "" : "<a href=\"index.php?page=test_mail&id={$idTest}\" title=\"View Test\" target=\"_blank\"><i class=\"material-icons\">picture_as_pdf</i></a>";
        $pdfLink = "<a href=\"index.php?page=test_mail&id={$idTest}\" title=\"View Test\" target=\"_blank\"><i class=\"material-icons\">picture_as_pdf</i></a>";

        //certificate link
        $certificateLink = $totalScore == "Unchecked" ? "" : "<a href=\"certificate.php?id={$idTest}\" title=\"View Certificate\" target=\"_blank\"><i class=\"material-icons\">insert_drive_file</i></a>";


        //mail link
        $mailCLink = $totalScore == "Unchecked" ? "" : "<a href=\"certificate.php?id={$idTest}&email={$emailAddress}&view={$view}\" title=\"Mail Certificate\"><i class=\"material-icons\">mail</i></a>";

        if ($score <= 79) {
            $certificateLink = "";
            $mailCLink = "";
        }
        /*				        $checkBox = $totalScore == "Unchecked" ? "" : "				 <label class=\"main\">                  <input class=\"form-check-input\" type=\"checkbox\" value={$emailAddress} data-id={$idTest} data-set={$view} data-score={$score} data-name=\"{$firstName} {$lastName}\" id=\"flexCheckDefault\">					<span class=\"myDefault\"></span> 				  </label>				  ";				*/
        $checkBox = ($totalScore == "Unchecked" || $score <= 79) ? "" : "				        <label class=\"main\">		<input class=\"form-check-input\" type=\"checkbox\" value={$emailAddress} data-id={$idTest} data-set={$view} data-score={$score} data-name=\"{$firstName} {$lastName}\" id=\"flexCheckDefault\">		<span class=\"myDefault\"></span> 	</label> 				  ";
        $rows .= <<<EOF
        <tr>
            <td>{$dateTook}</td>
            <td class="capitalize">{$fullName}</td>
            <td>{$emailAddress}</td>
            <td style="width:140px;">{$timeTook}</td>
            <td style="width:120px;">{$totalScore}</td>
            <td style="min-width:80px;">
                {$checkLink}
                {$certificateLink}
                {$mailCLink}

                {$pdfLink}
                {$mailLink}
                <a href="index.php?page=test_result&id={$idTest}&action=del&view={$view}" title="Delete Test" onclick="return confirm('Are you sure that you want to delete this test?')">
                    <i class="material-icons">delete_forever</i>
                </a>
            </td>
            <td class="text-center">
                {$checkBox}
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
} else if ($message == "failed") {
    echo <<<EOF
    <div class="alert alert-warning" role="alert">
        Oops! Failed sending email. Please try again.
    </div>
EOF;
} else if ($action == "del") {
    echo <<<EOF
    <div class="alert alert-success" role="alert">
        Test deleted successfully.
    </div>
EOF;
} else if ($action == "answer_sheet") {
    echo <<<EOF
    <div class="alert alert-success" role="alert">
        $answer_sheet_result
    </div>
EOF;
}
?>
<style>
    li.active a {
        color: #FFFFFF;
    }
</style>

<div class="subHeader">
    <div class="row">
        <div class="col title">
            Hi, <span class="capitalize"><?php echo $currentSessionFirstName; ?>!</span>
        </div>
    </div>
</div>
<div class="main">
    <form id="filter-form" action="index.php">
        <div class="row">
            <div class="col-sm-12">
                <fieldset class="filter-border">            
                    <legend class="filter-border">Filter</legend>
                    <div class="row">
                        
                            <div class="col-sm-3">
                                <input type="hidden" name="page" value="test_result">
                                <select type="text" name="view" id="view" class="form-control"/>
                                    <option value="null" disabled selected>-- Select type of insurance test --</options>
                                    <?php echo $setsOptions; ?>
                                </select>
                            </div>

                            <div class="col-sm-3">
                                <input type="text" name="adviser_name" id="adviser_name" class="form-control" placeholder="Adviser Name" value="<?php echo $adviser_name; ?>"/>
                            </div>

                            <div class="col-sm-3">
                                <button type="submit" id="searchBtn" class="btn btn-primary">Search</button>
                            </div>
                        
                    </div>
                </fieldset>
            </div>
        </div>
    </form>
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

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Bulk Email</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Please choose an option below:
                <br>
                <div class="d-flex justify-content-center">
                    <button type="button" id="sendAllTest" class="btn btn-primary m-2" data-toggle="modal" data-target="#confirmationModal">Bulk Send Test Results</button>
                    <button type="button" id="sendAllCert" class="btn btn-primary m-2" data-toggle="modal" data-target="#confirmationModal">Bulk Send Certificates</button>
                    <!--Kevin Edit 021720-->
                    <button type="button" id="sendAllBoth" class="btn btn-primary m-2" data-toggle="modal" data-target="#confirmationModal">Bulk Send Both</button>
                    <!--Kevin Edit 021720-->
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!--Confirmation Modal-->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to send bulk emails to:
                <div id="to"></div>
            </div>
            <div class="modal-footer">
                <button type="button" id="confirmSend" class="btn btn-primary" data-dismiss="modal">Confirm</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!--Notification Modal-->
<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<script>
    var arrayData = [];

    // purpose is to identify whether we remove send certificate or not
    var score = 1;

    var sendAllTest = document.getElementById('sendAllTest');
    var sendAllCert = document.getElementById('sendAllCert');
    // Kevin Edit 021720
    var sendAllBoth = document.getElementById('sendAllBoth');
    // Kevin Edit 021720


    var removeByAttr = function(arr, attr, value) {
        var i = arr.length;
        while (i--) {
            if (arr[i] &&
                arr[i].hasOwnProperty(attr) &&
                (arguments.length > 2 && arr[i][attr] === value)) {

                arr.splice(i, 1);

            }
        }
        return arr;
    }

    let jsonarr;

    $.ajax({
        url: 'myJSON.json',
        dataType: 'JSON',
        'async': false,
        success: function(data) {
            console.log(data);
            let arr = data.map(data => {
                return data.id;
            });

            jsonarr = arr;
            $('.form-check-input').each(function() {
                if (arr.includes($(this).attr('data-id'))) {
                    $(this).attr('checked', true).attr('class', 'done form-check-input').siblings().attr('class', 'geekmark');

                }


            });
			

			
			



        }
    });




					$('.done').on('click', function(){
					console.log('working');
			$(this).attr('class', 'form-check-input').siblings().attr('class','myDefault');

		});




    document.querySelectorAll('.form-check-input').forEach(function(input) {
        input.addEventListener('change', function(e) {
            var objData = {};
            if (e.target.checked) {
                objData["email"] = e.target.value;
                objData["id"] = e.target.getAttribute('data-id');
                objData["view"] = e.target.getAttribute('data-set');
                objData["fullname"] = e.target.getAttribute('data-name');
                if (e.target.getAttribute('data-score') <= 79) {
                    score *= 0;
                } else {
                    (score != 0) ? score += parseInt(e.target.getAttribute('data-score')): score = parseInt(e.target.getAttribute('data-score'));
                }
                arrayData.push(objData);
                const map1 = arrayData.map(x => x.fullname);
                document.getElementById('to').innerHTML = map1.toString();
            } else {
                (score != 0) ? score -= e.target.getAttribute('data-score'): score = 0;
                (score == 0) ? score = 1: "";
                removeByAttr(arrayData, 'id', e.target.getAttribute('data-id'));
                console.log(arrayData);
                const map1 = arrayData.map(x => x.fullname);
                $.ajax({
                    url: 'createJSON.php',
                    type: 'post',
                    data: {
                        data: JSON.stringify(arrayData)
                    },
                    success: function(data) {
                        console.log(data);
                    }
                });
                document.getElementById('to').innerHTML = map1.toString();
            }

            // if(score != 0 && score > 0){
            //     sendAllCert.classList.add("d-block");
            //     sendAllBoth.classList.add("d-block");
            //     sendAllCert.classList.remove("d-none");
            //     sendAllBoth.classList.remove("d-none");
            // } else {
            //     sendAllCert.classList.add("d-none");
            //     sendAllBoth.classList.add("d-none");
            //     sendAllCert.classList.remove("d-block");
            //     sendAllBoth.classList.remove("d-block");
            // }

            if (score == 0) {
                sendAllCert.disabled = true;
                sendAllBoth.disabled = true;
            } else {
                sendAllCert.disabled = false;
                sendAllBoth.disabled = false;
            }
        })
    });

    arrayData.forEach(function(data) {
        console.log(data.email);
    });





    $('head').append(`<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">`);

    const sendCert = () => {
        $.each(arrayData, function(ind, data) {
            console.log(data.email);
            console.log(data.id);

            if (!jsonarr.includes(data.id)) {
                $.ajax({
                    url: `certificate.php?id=${data.id}&email=${data.email}&view=${data.view}`,
                    type: 'get',
                    success: function() {

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                    }
                });
            }

        });

    }

    const sendTest = () => {
        $.each(arrayData, function(ind, data) {
            console.log(data.email);
            console.log(data.id);

            if (!jsonarr.includes(data.id)) {
                $.ajax({
                    url: `index.php?page=test_mail&id=${data.id}&email=${data.id}&email=${data.email}&view=${data.view}`,
                    type: 'get',
                    success: function() {

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus, errorThrown);
                    }
                });
            }
        });
    }


    $('#sendAllCert').on('click', () => {
        $('#confirmSend').attr('data-func', 'sendAllCert');
        // sendCert();
       // window.location.replace(`index.php?page=test_result&message=sent`);
    });

    $('#sendAllTest').on('click', () => {
        $('#confirmSend').attr('data-func', 'sendAllTest');
        //  sendTest();
        //window.location.replace(`index.php?page=test_result&message=sent`);
    });

    $('#sendAllBoth').on('click', () => {
        $('#confirmSend').attr('data-func', 'sendAllBoth');
        // sendCert();
        // sendTest();
        //window.location.replace(`index.php?page=test_result&message=sent`);
    });

		

 

	let arrKevin = [];
    $('#confirmSend').on('click', function(e) {
        e.preventDefault();
		
		$('.form-check-input').each(function(){
		if($(this).is(':checked')){
		let objKevin = {};
        objKevin["email"] = $(this).val();
        objKevin["id"] = $(this).attr('data-id');
        objKevin["view"] = $(this).attr('data-set');
        objKevin["fullname"] = $(this).attr('data-name');
        if ($(this).attr('data-score') <= 79) {
            score *= 0;
        } else {
            (score != 0) ? score += parseInt($(this).attr('data-score')): score = parseInt($(this).attr('data-score'));
        }
		
        arrKevin.push(objKevin);		
		}
		
    });

	   console.log('arrKevin',arrKevin);
		
		
        $.ajax({
            url: 'createJSON.php',
            type: 'post',
            data: {
                data: JSON.stringify(arrKevin)
            },
            success: function(data) {
                console.log(data);
            }
        });

        $('#exampleModalCenter .modal-body').html('Sending. Please Wait <i class="fas fa-spinner fa-spin"></i>');
        $('#exampleModalCenter .modal-footer .btn').attr('disabled', true);

        let func = $('#confirmSend').attr('data-func');
        switch (func) {
            case 'sendAllCert':
                sendCert();
					window.location.replace(`index.php?page=test_result&message=sent`);
                break;
            case 'sendAllTest':
                sendTest();
					window.location.replace(`index.php?page=test_result&message=sent`);
                break;
            case 'sendAllBoth':
                sendCert();
                sendTest();
					window.location.replace(`index.php?page=test_result&message=sent`);
                break;
            default:
                break;
        }
	
    });

    $("#filter-form").on("submit",function(e) {
        var view = $('#view').val();
        var adviser_name = $('#adviser_name').val();

        if (view !== null && view !== undefined) {
            // $('#notificationModal .modal-title').html('Success Message');
            // $('#notificationModal .modal-body').html('Loading details with selected filter/s... <i class="fas fa-spinner fa-spin"></i>');
            // $('#notificationModal .modal-footer').html('');
            // $('#notificationModal').modal('show');
            return true; 
        } else {
            // $('#notificationModal .modal-title').html('Failed Message');
            // $('#notificationModal .modal-body').html('Please select type of insurance test to proceed.');
            // $('#notificationModal .modal-footer').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');
            // $('#notificationModal').modal('show');
            return false; 
        }
        return true; 
    });

</script>
<style>
    .main {
        position: relative;
        padding-left: 45px;
        margin-bottom: 15px;
        cursor: pointer;
        font-size: 20px;
    }

    input[type=checkbox] {
        visibility: hidden;
    }

    /* Creating a custom checkbox 		based on demand */
    /*my default*/
    .myDefault {
        position: absolute;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        border: 1px solid #495057;
        border-radius: 5px 5px;
    }

    .main input:checked~.myDefault {
        background-color: #0F6497;
    }

    .myDefault:after {
        content: "";
        position: absolute;
        display: none;
    }

    .main input:checked~.myDefault:after {
        display: block;
    }

    .main .myDefault:after {
        left: 8px;
        bottom: 5px;
        width: 6px;
        height: 12px;
        border: solid white;
        border-width: 0 4px 4px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    /*my default*/
    .geekmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        border: 1px solid #495057;
        border-radius: 5px 5px;
    }

    .main input:checked~.geekmark {
        background-color: green;
    }

    .geekmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    .main input:checked~.geekmark:after {
        display: block;
    }

    .main .geekmark:after {
        left: 8px;
        bottom: 5px;
        width: 6px;
        height: 12px;
        border: solid white;
        border-width: 0 4px 4px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    fieldset.filter-border {
        border: 1px groove #ddd !important;
        padding: 0 1.4em 1.4em 1.4em !important;
        margin: 0 0 1.5em 0 !important;
        -webkit-box-shadow:  0px 0px 0px 0px #000;
                box-shadow:  0px 0px 0px 0px #000;
    }

    legend.filter-border {
        font-size: 1.2em !important;
        font-weight: bold !important;
        text-align: left !important;
        width:auto;
        padding:0 10px;
        border-bottom:none;
    }
</style>