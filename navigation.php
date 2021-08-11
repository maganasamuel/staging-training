<?php
/* //secure the page
include_once("security.php");
$prop = array(
            "group_name" => "index",
            "allow" => ""
        );
securePage($prop);

//include necessary files
include_once("lib/General.helper.php");
include_once("lib/Session.helper.php");

$app = new GeneralHelper();
$session = new SessionHelper(); */

//variables
$page = $app->param($_GET, 'page', 'home');
$isTest = $isPassword = $isTrainer = $isTrainee = '';
$idUserType = $app->param($_SESSION, 'id_user_type', 0);

//determine the active link
switch ($page) {
    case 'trainer':
    case 'trainer_add':
    case 'trainer_edit':
        $isTrainer = 'active';

    break;
    case 'test_questions':
    case 'test_questions_add':
    case 'test_questions_edit':
        $isTestQuestions = 'active';

    break;
    case 'trainee':
    case 'trainee_add':
    case 'trainee_edit':
        $isTrainee = 'active';

    break;
    case 'training_material':
    case 'training_material_add':
        $isTraining = 'active';


    break;
    case 'password':
        $isPassword = 'active';

    break;
    case 'test': default:
    case 'test_check':
    case 'test_result':
        $isTest = 'active';

    break;
}

?>
<nav class="navbar navbar-expand-lg navbar-dark bg-secondary sticky-top p-0 border-bottom">
	<a class="navbar-brand bg-white p-2" href="index">
		<img src="img/logo.png" alt="onlineinsure" class="logo logo-small" style="height:40px;"/>
	</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="mainNav">
		<ul class="navbar-nav mr-auto justify-content-end width100">
			<li class="nav-item <?php echo $isTest; ?>">
				<a class="nav-link" href="index.php?page=test_result">Tests</a>
			</li>
            <li class="nav-item <?php echo $isTraining; ?>">
                    <a class="nav-link" href="index.php?page=training_material">Training Material</a>
                </li>
				<?php
                    if (1 == $session->get('id_user_type')) {
                        ?>
				<li class="nav-item <?php echo $isTestQuestions; ?>">
					<a class="nav-link" href="index.php?page=test_questions">Test Questions</a>
				</li>
				<li class="nav-item <?php echo $isTrainer; ?>">
					<a class="nav-link" href="index.php?page=trainer">Trainer</a>
				</li>
				<li class="nav-item <?php echo $isPassword; ?>">
					<a class="nav-link" href="index.php?page=password">Password</a>
				</li>					
				<?php
                    }
                ?>
			<li class="nav-item">
				<a class="nav-link" href="login_master.php?action=logout">Log out</a>
			</li>
		</ul>
	</div>
</nav>