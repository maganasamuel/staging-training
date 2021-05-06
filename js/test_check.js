/**
@name: test_check.js
@author: Gio
@desc:
	handles the checking of test_check.php page
*/
$(document).ready (
	function () {
		bindTextFields();
		bindButtons();
	}
);

//variables
var api = "lib/Test.helper.php";

/**
@desc: binds all buttons to necessary actions
*/
function bindButtons () {
	$("span.save").on("click",
		function (e) {
			$input = $(this).prev("input");
			
			var idTestDetail = $input .attr("id");
			var score = $input .val();
			var maxScore = $input .attr("max");
			
			score = score * 1;
			maxScore = maxScore * 1;
			
			console.log("id: " + idTestDetail + "; score:" + score + "; max:" + maxScore);
			if (score <= maxScore) {
				submitScore(idTestDetail, score);
				$input.parent("div.score").children("span.note").css({"color":"green"});
			}
			else {
				$input.parent("div.score").children("span.note").css({"color":"red"});
				$input.val("");
			}
		}
	);
}

/**
@desc: binds textfields to handle saving of score on textbox leave event
*/
function bindTextFields () {
	$("input.score").on("keydown",
		function (e) { 
			var keycode = (e.keyCode ? e.keyCode : e.which);
			//console.log("[key]" + keycode);
			if (keycode == 9 ||
				keycode == 13) {
				var idTestDetail = $(this).attr("id");
				var score = $(this).val();
				var maxScore = $(this).attr("max");
				
				score = score * 1;
				maxScore = maxScore * 1;
				
				console.log(keycode + ") id: " + idTestDetail + "; score:" + score + "; max:" + maxScore);
				if (score <= maxScore) {
					submitScore(idTestDetail, score);
					$(this).parent("div.score").children("span.note").css({"color":"green"});
				}
				else {
					$(this).parent("div.score").children("span.note").css({"color":"red"});
					$(this).val("");
				}
			}
		}
	);
}

/**
@desc: saves the score using the API
*/
function submitScore (
	idTestDetail,
	score
) {
	$.post(api, 
		{
			"action": "t-c",
			"idtd": idTestDetail,
			"score": score
		},
		function (data) {
			console.log(data);
		}
	);
}