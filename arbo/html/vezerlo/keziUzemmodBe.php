<?php
	error_reporting(0);
	require_once('/var/www/html/vezerlo/controller.php');
	ob_clean();
	header("Content-type:application/json");

	session_start();
	$uid = 0;
	if (! isset($_SESSION['login']) || $_SESSION['login'] == 0) {
		exit(0);
	} else {
		$uid = $_SESSION['login'];
	}

	$rs = VezerloController::keziUzemmodBe( $uid );
	echo json_encode(['ok' => $rs]);

