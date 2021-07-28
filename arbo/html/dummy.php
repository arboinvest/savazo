<?php
require_once(__DIR__.'/config/Config.php');
use Config\Config;

error_reporting(0);

session_start();
if (! isset($_SESSION['login']) || $_SESSION['login'] == 0) {
	exit(0);
}

$parancs = $_GET['parancs'];


switch($parancs) {
	case 'P1_ping' : {
		$exec = "sudo ping 192.168.5.152 -c 3";
		exec($exec,$out,$rcode);
		ob_clean();
		foreach ($out as $o) {
			echo $o,'<br>';
		}
		break;
	}
	case 'P2_ping' : {
		$exec = "sudo ping 192.168.5.151 -c 3";
		exec($exec,$out,$rcode);
		ob_clean();
		foreach ($out as $o) {
			echo $o,'<br>';
		}
		break;
	}
	case 'P1_start' : {
		$exec = "sudo python /home/pi/arbo/startP1.py";
		exec($exec,$out,$rcode);
		ob_clean();
		foreach ($out as $o) {
			echo $o,'<br>';
		}
		break;
	}
	case 'P2_start' : {
		$exec = "sudo python /home/pi/arbo/startP2.py";
		exec($exec,$out,$rcode);
		ob_clean();
		foreach ($out as $o) {
			echo $o,'<br>';
		}
		break;
	}
}

