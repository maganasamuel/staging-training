/**
@name: test_form.js
@author: Gio
@desc:
	handles the test page
*/


$(document).ready(
	function () {
		//hide everything
		$("div.testFormWelcome").hide();
		$("div.testForm").hide();
		
		//setup
		getQuestions();
		
		//bind
		bindButtons();
	}
);

//variables
var api = "lib/Test.helper.php";
var questionIndex = -1;
var questions = [];
var debug = 0;
var start = new Date;
var answers = [];

/**
@desc: fetch the questions for the specified ID_SET
*/
function getQuestions () {
	$.post(api, 
		{
			"action": "qs-all",
			"ids": idSet
		},
		function (data) {
			json = jQuery.parseJSON(data);
			questions = json;
			log("q-all");
			log(questions);
			log("q-all");
			toggleQuestion();
		}
	);
}

/**
@desc: bind all actions to the buttons
*/
function bindButtons () {
	$("button.btn-back").on("click", 
		function(e) {
			var text = $(this).text();
			questionIndex--;
			toggleQuestion();
			e.preventDefault();
		}
	);
	$("button.btn-next").on("click", 
		function(e) {
			var text = $(this).text();
			if (text == "Ok") {
				window.location.href = "test?page=test_set";
				return;
			}
			if (submitAnswer()) {
				if (questionIndex < questions.length - 1) {
					questionIndex++;
					toggleQuestion();
				}
				else {
					displayThankYou();
				}
			}
			e.preventDefault();
		}
	);
}

/**
@desc: handles the display of questions whenever the next/back button is tapped
*/
function toggleQuestion () {
	if (questionIndex == 0 && answers.length <= 0) {
		start = new Date(); //start the timestamp;
	}
	if (questionIndex == -1) {
		//display the welcome page
		$("div.testFormWelcome").show();
		$("div.testForm").hide();
	}
	else {
		//display the questions
		if (questionIndex > 1) {
			updateQuestion();
			$("div.testForm").hide().fadeIn("slow");
		}
		else {
			updateQuestion();
			$("div.testFormWelcome").hide();
			$("div.testForm").fadeIn();
			setInterval(function() {
				var seconds = parseInt((new Date - start) / 1000);
				var mins = parseInt(seconds/60);
				
				if (mins > 0) {
					seconds = seconds - (mins * 60);
				}
				
				var secondsText = seconds + " sec.";
				var minuteText = (mins > 0) ?  mins + " min." : "";
				
				$("span.timer").text(minuteText + " " + secondsText);
			}, 1000);
		}
	}
}

/**
@desc: submits the answer to the API
*/
function submitAnswer () {
	if (questionIndex == -1 ||
		questionIndex >= questions.length) {
		return true;
	}
	
	var question = questions[questionIndex];
	
	var idSetQuestion = question.id_set_question;
	var questionCurrent = question.question;
	var idSetQuestionType = question.id_set_question_type;
	
	var answer = "";
	if (idSetQuestionType == 1) {
		$("textarea.answer").each(
			function (index) {
				var textField = $(this);
				answer += (index + 1) + ". " + textField.val() + ";\n";
			}
		);
	}
	else if (idSetQuestionType == 2) {
		$.each($("input[name='chk']:checked"), function(){     
			var checkbox = $(this);
			answer += checkbox.val() + ";\n";
		});
	}
	else if (idSetQuestionType == 3) {
		$.each($("input[name='rad']:checked"), function(){     
			var radiobox = $(this);
			answer += radiobox.val() + ";\n";
		});
	}
	
	
	log("answer for:" + questionIndex);
	log(answer);
	
	if (answer == "") {
		log("empty answer");
		alert("Please provide an answer");
		return false;
	}
	
	answers[questionIndex] = answer;
	
	//submit
	$.post(api, 
		{
			"action": "q-a",
			"idt": idTest,
			"id": idSetQuestion,
			"answer": answer
		},
		function (data) {
			json = jQuery.parseJSON(data);
			log(data);
		}
	);
	pause(300);
	return true;
}

/**
@desc: updates the question display
*/
function updateQuestion () {
	var question = questions[questionIndex];
	console.log("index:" + questionIndex);
	console.log(question);
	
	var idSetQuestion = question.id_set_question;
	var questionCurrent = question.question;
	var textFieldCount = question.textfield_count;
	var questionSetIndex = question.question_set_index;
	var choices = question.choices;
	var idSetQuestionType = question.id_set_question_type;
	
	//question
	questionCurrent = questionSetIndex + ". " + questionCurrent;
	$("div.testForm h4.question").html(questionCurrent);
	
	//answer
	var answer = arrayValue(answers, questionIndex);
	console.log("answer:" + answer);
	var answerArray = answer.split(";\n");
	
	
	if (idSetQuestionType == 1) { // textFields
		var textFields = "";
		for ( i = 0; i < textFieldCount; i++) {
			var answerCurrent = arrayValue(answerArray, i);
			//console.log("cur" + answerCurrent);
			var answerFin = answerCurrent.substring(answerCurrent.indexOf(".") + 2, answerCurrent.length); 
			//console.log("fin" + answerFin);
			textFields += "<textarea class=\"form-control answer\" placeholder=\"Answer\">" + answerFin + "</textarea>";
		}
		$("div.testForm div.answerField").html(textFields);
	}
	else if (idSetQuestionType == 2) { //multiple choice
		if (choices.length > 0) { // checkboxes
			var checkboxes = "";
			var choicesArray = choices.split(";");
			console.log("choice:" + choice);
			if (choicesArray[choicesArray.length - 1] == "") {
				choicesArray.splice(choicesArray.length - 1, 1);
			}
			for (i = 0; i < choicesArray.length; i++) {
				var isChecked = "";
				var choice = choicesArray[i];
				var index = answerArray.indexOf(choice);
				if (index >= 0) {
				//if (i == choicesArray.length - 1) { //TODO: comment this and uncomment the above code
					//console.log("matched:" + choice);
					isChecked = "checked";
				}
				console.log("choice:" + choice);
				checkboxes += "<div class=\"form-check\">"
					+ "<input class=\"form-check-input\" type=\"checkbox\" value=\""+ choice +"\" id=\"chk"+i+"\" name=\"chk\" "
					+ isChecked + "/>"
					+ "<label class=\"form-check-label\" for=\"chk"+i+"\">"
					+ choice
					+ "</label>"
					+ "</div>";
			}
		}
		$("div.testForm div.answerField").html(checkboxes);
	}
	else if (idSetQuestionType == 3) { // yes/no or true/flase
			console.log("Select 1");
		if (choices.length > 0) { // radiobox
			var radiobox = "";
			choices = choices.slice(0,-1)
			var choicesArray = choices.split(";");
			if (choicesArray[choicesArray.length - 1] == "") {
				console.log("trimming last choice");
				choicesArray.splice(choicesArray.length - 1, 1);
			}
			
			for (i = 0; i < choicesArray.length; i++) {
				var isChecked = "";
				var choice = choicesArray[i];
				var index = answerArray.indexOf(choice);
				if (index >= 0) {
				//if (i == choicesArray.length - 1) { //TODO: comment this and uncomment the above code
					//log("matched:" + choice);
					isChecked = "checked";
				}
				radiobox += "<div class=\"form-check\">"
					+ "<input class=\"form-check-input\" type=\"radio\" value=\""+ choice +"\" id=\"rad"+i+"\" name=\"rad\" "
					+ isChecked + "/>"
					+ "<label class=\"form-check-label\" for=\"rad"+i+"\">"
					+ choice
					+ "</label>"
					+ "</div>";
			}
		}
		$("div.testForm div.answerField").html(radiobox);
	}
}

/**
@desc: checks if the array being submitted is correct
*/
function arrayValue (varArray, key) {
	return (typeof varArray[key] === 'undefined') ? "" : varArray[key];
}

/**
@desc: displays the last page/thank you page
*/
function displayThankYou() {
	if (isAutoCheck > 0) {
		processChecking();
	}
	$("button.btn-back").hide();
	$("div.testForm").hide();
	$("div.testFormWelcome p.question").html("Thanks for taking time in answering the test. We'll get back to you with the results via email");
	$("div.testFormWelcome button.btn-next").html("Ok");
	$("div.testFormWelcome").show("slow");
}

/**
@desc: handles the auto checker
*/
function processChecking () {
	log("checking start==========");
	var score = [];
	for (i = 0; i < questions.length; i ++) {
		//question detail	
		var question = questions[i];
		var idSetQuestion = question.id_set_question;
		var maxScore = question.max_score;
		
		log("q:" + idSetQuestion +";");

		//choices
		var choices = question.choices;
		var choicesArray = choices.split(";");	
		
		
		//if last_index is blank remove it from array
		if (choicesArray[choicesArray.length - 1] == "") {
			choicesArray.splice(choicesArray.length - 1, 1);
		}
		
		choicesArray.forEach(function(choice, index){
			choicesArray[index] = choice.replace(/(?:\\[rn]|[\r\n]+)+/g, " ").trim();
		});
		
		//answer
		var answer = arrayValue(answers, i);

		log("answer:" + answer);
		var answerArray = answer.split(";\n");
		//if last_index is blank remove it from array
		if (answerArray[answerArray.length - 1] == "") {
			answerArray.splice(answerArray.length - 1, 1);
		}
		
		var correct = question.answer_index;
		var correctArray = correct.split(";");
		//if last_index is blank remove it from array
		if (correctArray[correctArray.length - 1] == "") {
			correctArray.splice(correctArray.length - 1, 1);
		}
		
		//get all answer index
		var scoreCurrent = 0;
		var incorrect = 0;

		for (j = 0; j < answerArray.length; j++) {
			var answerCurrent = answerArray[j];
			answerCurrent = answerCurrent.replace(/(?:\\[rn]|[\r\n]+)+/g, " ").trim();

			//index = answer
			var index = choicesArray.indexOf(answerCurrent);

			//scoreCurrent = maxScore;
			//log("ans index:" +index);
			log(correctArray);
			if (jQuery.inArray("" + index, correctArray) >= 0) {
				log("correct:" + index);
				scoreCurrent += 1;
			}
			else{
				incorrect++;
			}
			log("[j]:" + j + "; sc:" + scoreCurrent);
		} // end of answer loop
	
		if(scoreCurrent == correctArray.length && incorrect == 0){
			scoreCurrent = 1;			
			log("correct!");
		}
		else{			
			scoreCurrent = 0;		
			log("incorrect!");
		}


		log("s:" + scoreCurrent + ";");
		log("--------------------------------------");
		score[i] = {"id": idSetQuestion, "score": scoreCurrent};
	} // end of question loop
	var json = JSON.stringify(score);
	
	//submit score
	$.post(api, 
		{
			"action": "r-s",
			"idt": idTest,
			"ids": idSet,
			"score": json
		},
		function (data) {
			json = jQuery.parseJSON(data);
			log(data);
		}
	);
	pause(300);
	log("checking end==========");
}

/**
@desc: console.log display handler
*/
function log (message) {
	if (debug == 1) {
		console.log(message);
	}
}

/**
@desc: put a small pause on transition to make sure everything is captured
*/
function pause(numberMillis) { 
    var now = new Date(); 
    var exitTime = now.getTime() + numberMillis; 
    while (true) { 
        now = new Date(); 
        if (now.getTime() > exitTime) 
            return; 
    } 
} 
