<?php
/**
@name: security.php
@author: Gio
@desc:
	Secures the page
*/
include_once("lib/Session.helper.php");

/**
	@desc: prevent user from playing around the URL
	
	
	@usage:
		//put this on top of every page right after the opening PHP tag
		include_once("security.php");
		$prop = array(
					"group_name" => "index",
					"allow" => ""
				);
		securePage($prop);
	
	@note:
		group_name = index > for master, trainer;
					 trainee > for admin, adviser;
					 
		allow = id_user_type of users that has access to the page.
				multiple type > separated by comma;
				all > empty;		
*/
function securePage (
	$prop // array with indeces described above
) {

	$groupName = isset($prop["group_name"]) ? $prop["group_name"] : "";
	$allow = isset($prop["allow"]) ? $prop["allow"] : "";
	$session = new SessionHelper();

	//check if there's no active session
	if (!$session->isSessionActive()) {
		destroyCurrentSession();
	}

	//check if the user has access to current page
	$idUserType = $session->get("id_user_type");
	if ($idUserType == "") {
		destroyCurrentSession();
	}
	else {
		if($idUserType == 4){
			correctPageURL($groupName, $allow);
		}else{
				switch ($idUserType) {
				case 1:case 3: //master, trainer
					if ($groupName != "index") {
						destroyCurrentSession();
					}
					else {
						correctPageURL($groupName, $allow);
					}
				break;
				case 2: case 5: case 6: case 7: case 8://adviser, admin, bdm, telemarketer
					if ($groupName != "trainee") {
						destroyCurrentSession();
					}
					else {
						correctPageURL($groupName);
					}
				break;
				default:
					destroyCurrentSession();
				break;
			}
		}
		
	}
}


/**
	@desc: destoys current session and redirect to login page options
*/
function destroyCurrentSession () {
	$session = new SessionHelper();
	$session->destroySession();
	header("Location: login");
	exit();
}

/**
	@desc: check if the requesting URL is valid and accessible by the visiting user.
*/
function correctPageURL (
	$groupName, //current "master" page viewing
	$allow = "" //user type allowed to access the page;
) {
	$session = new SessionHelper();
	$url =  "{$_SERVER['REQUEST_URI']}";
	$escaped_url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
	
	if ($groupName == "index") {
		if (strpos($escaped_url, "index") !== false ||
			strpos($escaped_url, "index.php?") !== false ||
			strpos($escaped_url, "index?") !== false) {
			
		}
		else {
			header("location: index");
		}
		//check permission
		if ($allow != "") {
			$idUserType = $session->get("id_user_type");
			if (strpos($allow, "{$idUserType}") !== false) {
				//allow
			}
			else {
				header("location: index");
			}
		}
	}
	else {
		if (strpos($escaped_url, "test.php?") !== false ||
			strpos($escaped_url, "test?") !== false) {
			//allow
		}
		else {
			header("location: test");
		}
	}
}


?>