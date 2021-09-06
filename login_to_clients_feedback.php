<?php
include_once('lib/Session.helper.php');
include_once('lib/General.helper.php');
include_once('lib/User.controller.php');

$session = new SessionHelper();
$app = new GeneralHelper();
$user = new UserController();

$idUserType = $app->param($_SESSION, 'id_user_type', -1);
$id_user = $app->param($_SESSION, 'id_user', -1);

if (in_array($idUserType, [1, 7, 8])) {
    $config = parse_ini_file('lib/class/conf/conf.ini');

    $token = $user->createToken($id_user);

    header('location: ' . $config['clients_feedback_login_url'] . '?token=' . $token);
}
?>

<div class="subHeader">
	<div class="row">
		<div class="col title">Login to Clients Feedback</div>
	</div>
</div>
<div class="main">
	<div class="row">
		<div class="col-md-4 offset-md-4">
			<div class="alert alert-danger" role="alert">
				Could not login to Clients Feedback. You do not have enough priviledge to access this page.
				
				<p><?php echo json_encode($_SESSION); ?></p>
				<p>ID User Type: <?php echo $idUserType; ?></p>
			</div>
		</div>
	</div>
</div>