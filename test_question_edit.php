<?php
/**
@name: trainer_add.php
@author: Gio
@desc:
	Page that handles adding of new trainer
*/

//secure the page
include_once("security.php");
$prop = array(
			"group_name" => "index",
			"allow" => "1"
		);
securePage($prop);


//include necessary files
include_once("lib/General.helper.php");
include_once("lib/User.controller.php");
include_once("lib/Test.controller.php");

$app = new GeneralHelper();
$testController = new TestController();

//variables
$action = $app->param($_POST, "action");
$message = $app->param($_GET, "message");
$id_set_question = $app->param($_GET, "id");
$question_set = "";
$question = ""; 
$question_set_index = ""; 
$answer_index = ""; 
$question_type = "";
$max_score = "";
$textfield_count = "";
$choices = "";

//save the submitted form
if ($action == "save") {
    $id_set_question = $app->param($_POST, "id_set_question");
	$question_set = $app->param($_POST, "question_set");
	$question = $app->param($_POST, "question");
	$question_set_index = $app->param($_POST, "question_set_index");
	$answer_index = $app->param($_POST, "answer_index");
	$question_type = $app->param($_POST, "question_type");
	$max_score = $app->param($_POST, "max_score");
	$textfield_count = $app->param($_POST, "textfield_count");
    $choices = $app->param($_POST, "choices");
	if ($question_set == "" ||
        $question == "" || 
		$question_set_index == "" || 
		$question_type == "" ||
		$max_score == "" ||
		$textfield_count == "" ) {
		$message = "<div class=\"alert alert-danger\" role=\"alert\">All fields are required." . var_dump($_POST) . "</div>";
	}
	else {
		$dataset = $testController->updateSetQuestion(
                        $id_set_question,
                        $question_set,
						$question,
						$question_set_index,
                        $answer_index,
                        $question_type,
                        $max_score,
                        $textfield_count,
                        $choices
                    );
                    
        header("location: index.php?page=test_questions&view=$question_set");
	}
}
else{
    //fetch the selected idUser/trainer
	$dataset = $testController->getSetQuestionSpecific($id_set_question);
	
	if ($dataset->num_rows > 0) {
		while ($row = $dataset->fetch_assoc()) {
			$id_set_question = $row["id_set_question"];
			$id_set = $row["id_set"];
			$question = $row["question"];
			$question_set_index = $row["question_set_index"];
			$answer_index = $row["answer_index"];
			$id_set_question_type = $row["id_set_question_type"];
			$max_score = $row["max_score"];
			$textfield_count = $row["textfield_count"];
			$choices = $row["choices"];
		}
	}
	else {
		header("location: index.php?page=test_question_edit&id={$id_set_question}&message=Invalid Question");
	}
}

?>
<style>
	label {
		width:120px;
	}
	span.required {
		color:red;
		font-size:8px;
	}
</style>
<div class="subHeader">
	<div class="row">
		<div class="col title">
			Edit Test Question
		</div>
	</div>
</div>
<div class="main" style="margin:0px 50px;">
	<div class="row">
		<div class="col">
			 <?php echo $message; ?>
		</div>
	</div><br/>
    <form method="post">
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-4">
                        
                    <label for="question_set"><span class="required">*</span> Question Set:</label>
                    <select type="text" name="question_set" id="question_set" class="form-control" value="<?php echo $question_set_index; ?>" />
                        <?php
                            //sets
                            $setDataset = $testController->getSetAll(-1);
                            $sets = "";
                            $view = $app->param($_GET, "view", 1);
                            if ($setDataset->num_rows > 0) {
                                while($row = $setDataset->fetch_assoc()) {
                                    $idSet = $row["id_set"];
                                    $setName = $row["set_name"];
                                    $selected = ($id_set == $idSet) ? " selected" : "";
                                    $active = ($view == $idSet) ? "active" : "";
                                    echo "
                                    <option $selected value='$idSet'>
                                        $setName
                                    </option>";
                                }
                            }
                        ?>
                    </select>
                    <br/>       
                    <label for="question"><span class="required">*</span> Set Index:</label>
                    <input type="text" value="<?php echo $question_set_index; ?>" name="question_set_index" id="question_set_index" class="form-control" placeholder="'Life Cover - 11' means this is the 11th question for Life Cover."/>
                    <small>The section and number on where and in what order this question will be. </small>
                    <br/>
            </div>
            <div class="col-sm-4">       
                    <label for="question_type"><span class="required">*</span> Question Type:</label>
                    <select type="text"  value="<?php echo $question_type; ?>" name="question_type" id="question_type" class="form-control"/>
                        <?php
                            //sets
                            $setDataset = $testController->getSetQuestionTypeAll();
                            $sets = "";
                            $view = $app->param($_GET, "view", 1);
                            if ($setDataset->num_rows > 0) {
                                while($row = $setDataset->fetch_assoc()) {
                                    $idSet = $row["id_set_question_type"];
                                    $setName = $row["set_question_type"];
                                    $selected = ($id_set_question_type == $idSet) ? " selected" : "";
                                    $active = ($view == $idSet) ? "active" : "";
                                    
                                    echo "
                                    <option value='$idSet'>
                                        " . ucfirst($setName) . "
                                    </option>";
                                }
                            }
                        ?>
                    </select>
                    <br/>       
                    <div class="row">
                        <div class="col-6">
                            <label for="question"><span class="required">*</span> Text Field Count:</label>
                            <input type="text"  value="<?php echo $textfield_count; ?>" name="textfield_count" id="textfield_count" class="form-control" value="1"/>
                            <small>Number of input fields for essay type/enumeration questions. </small>
                        </div>
                        <div class="col-6">
                            <label for="question"><span class="required">*</span> Max Score:</label>
                            <input type="text" value="<?php echo $max_score; ?>"  name="max_score" id="max_score" class="form-control" value="1"/>
                            <small>Highest possible score for this question. </small>
                        </div>
                    </div>
                    
                    
            </div>
	    </div>
        
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                    <label for="question"><span class="required">*</span> Question:</label>
                    <textarea  type="text" name="question" id="question" class="form-control" rows="3" /><?php echo $question; ?></textarea>
            </div>
	    </div>
        
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-4">                    
                <label for="choices"><span class="required">*</span> Choices:</label>
                <textarea readonly name="choices"  id="choices" class="form-control"/><?php echo $choices; ?></textarea>
                <small>Separate multiple choices with ';'. Ex: "True;False;" or "A) Choice 1; B) Choice 2;" </small><br>
                <small><span style='color:red;'>IMPORTANT:</span> DO NOT USE SPACES IN BETWEEN ";"</small><br>
                <br/>
            </div>
            <div class="col-sm-4">    
                    <label for="answer_index"><span class="required">*</span> Answer Index:</label>
                    <input type="text" name="answer_index" value="<?php echo $answer_index; ?>"  id="answer_index" class="form-control" placeholder="Ex: 0;2;3 = A C and D as correct answers"/>
                    
                    <small>From the choices, start counting from 0 to the correct answer's index. Ex: A = 0,B = 1, C =...</small>
                    <small>Separate multiple answers with ';'. Ex: "0;1;2;","0;3","2;3;"</small>
                    <br/><br/>

			        <input type="hidden" name="id_set_question" value="<?php echo $id_set_question; ?>"/>
                    <input type="hidden" name="page" value="test_question_edit"/>
                    <input type="hidden" name="action" value="save"/>
                    <input type="submit" class="btn btn-primary" style="float:right; position:relative;" value="save">
                    <br>
            </div>
            <div class="col"></div>
        
	    </div>
    </form>
    <script>
        $(function(){
            let active_question_type = <?php echo $id_set_question_type ?>;
            
            $("#question_type").on("change", function(){
                var type = $(this).val();
                var textfield_count = $("#textfield_count");
                var choices = $("#choices");

                if(type=="1"){
                    textfield_count.prop("readonly", false);
                    textfield_count.val("1");
                    choices.prop("readonly", true);
                    choices.val("");
                }
                else{
                    textfield_count.val("0");
                    textfield_count.prop("readonly", true);
                    choices.prop("readonly", false);
                }
            });
            
            $("#question_type").val(active_question_type).trigger("change");
            
        });
    </script>
</div>

