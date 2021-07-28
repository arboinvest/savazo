<?php
	error_reporting(E_ALL);
	require_once('/var/www/html/login/controller.php');
	$user = isset($_POST['user']) ? $_POST['user'] : '';
	$pass = isset($_POST['pass']) ? $_POST['pass'] : '';
	ob_clean();
	header("Content-type:application/json");
	$ok = LoginController::ellenorzes($user, $pass);
	echo json_encode ( [ 'ok' => $ok ] );
