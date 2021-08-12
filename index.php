<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();

//secure the page
include_once('security.php');
$prop = [
    'group_name' => 'index',
    'allow' => '',
];
securePage($prop);

//include nessary files
include_once('lib/Session.helper.php');
include_once('lib/General.helper.php');

$session = new SessionHelper();
$app = new GeneralHelper();

?>
<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="robots" content="noindex, nofollow" />
	<meta name="description" content="">
	<meta name="author" content="Elite Insure">
	<link rel="icon" href="img/favicon.ico">

	<title>Test Checker</title>

	<!-- CSS -->
	<!-- <link href="css/onlineinsure-bootstrap.css" rel="stylesheet"> -->
	<link rel="stylesheet" href="css/bootstrap.css">
	<link href="css/styles.css" rel="stylesheet">

	<!-- Icon font -->
	<link href="css/google-icons.css" rel="stylesheet">

	<!-- Script -->
	<script src="js/jquery-3.2.1.min.js"></script>
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.js"></script>
	<script src="js/bulk-email.js"></script>

	<!-- Select2 -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
	<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha512-YUkaLm+KJ5lQXDBdqBqk7EVhJAdxRnVdT2vtCzwPHSweCzyMgYV/tgGF4/dCyqtCC2eCphz0lRQgatGVdfR0ww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>	
</head>

<body>
	<?php
    include_once('navigation.php');
    ?>
	<main role="main">
		<?php
        $page = $app->param($_GET, 'page', 'test_result');

        include_once("{$page}.php");
        ?>
	</main><!-- /.container -->
	<script>
		$(function() {
			$('[data-toggle="tooltip"]').tooltip()
		})
	</script>
</body>

</html>