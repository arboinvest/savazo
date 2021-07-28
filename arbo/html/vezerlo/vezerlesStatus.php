<?php
	error_reporting(0);
	require_once('/var/www/html/vezerlo/controller.php');
	ob_clean();
	header("Content-type:application/json");
	$r = VezerloController::vezerlesStatus();
	echo json_encode($r);
