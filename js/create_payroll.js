var helper = "lib/Pay.helper.php";

$(document).ready(
	function () {
		$("form").submit(
			function( event ) {
			event.preventDefault();
			}
		);
		$("form").click(
			function( event ) {
			event.preventDefault();
			}
		);
		
		$("button#addClientButton").click(
			function() {
				var clientCount = $("input#clientCount").val();
				generateForm(clientCount);
			}
		);
	}
)

function generateForm (clientCount) {
	if (clientCount <= 0) {
		alert("Please enter a number higher and 0 then try again.");
		return;
	}
	var rows = "<h5>Clients:</h5><br/><div class=\"clients\">";
	for (var i = 1; i <= clientCount; i ++) {
		rows += "<div class=\"form-group\">"
		+ "<div class=\"input-group mb-3\">"
		+ "<div class=\"input-group-prepend\">"
		+ "<span class=\"input-group-text\">Client "
		+ i + "</span>"
		+ "</div>"
		+ "<input type=\"text\" class=\"form-control\" id=\"firstName\" placeholder=\"First name\" />"
		+ "<input type=\"text\" class=\"form-control\" id=\"lastName\" placeholder=\"Last name\" />"
		+ "<span class=\"input-group-text\">$</span>"
		+ "<input type=\"number\" step=\"0.01\" class=\"form-control\" id=\"commission\" placeholder=\"OnlineInsure Commission\" />"
		+ "</div>"
		+ "</div>";
	}
	rows += "</div>";
	rows += "<center>"
	+ "<button id=\"createPayrollButton\" class=\"btn btn-danger\">"
	+ "Create Payroll"
	+ "</button></center>";
	
	$("div.clientForm").html(rows);

	$("button#createPayrollButton").click(
		function(event) {
			var clientJSON = [];
			$( "div.clientForm div.clients div.form-group" ).each(function( index ) {
				var div = $(this);
				var firstName = $(div).find("#firstName").val();
				var lastName = $(div).find("#lastName").val();
				var commission = $(div).find("#commission").val();
				clientJSON.push({"first_name":firstName, "last_name":lastName, "commission":commission});
			});
			//console.log(clientJSON);
			var idUserRep = $("select#salesRep option:selected").val();
			var month = $("select#month option:selected").val();
			var period = $("select#period option:selected").val();
			var year = $("select#year option:selected").val();
			var bonus = $("input#bonus").val();
			var idUserGen = $("input#idUserGenerated").val();

			var payrollJSON = {
				"id_user_rep":idUserRep,
				"id_user_gen":idUserGen,
				"month":month,
				"period":period,
				"year":year,
				"bonus":bonus,
				"clients":clientJSON,
				"action":"generate"
			};

			$.post(
				helper,
				payrollJSON,
			function( data ) {
				console.log(data);
				window.location.href = "index.php?page=pdf";
/*
//refine the message handler
				if(typeof data["message"] === 'undefined') {
					console.log("Something went wrong. Please check the database if the record has been saved.");
				}
				else {
					// does exist
					if (data["message"] == "saved") {
					}
				}
*/
			});

			event.preventDefault();
		}
	);
}

