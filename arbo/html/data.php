<?php

require_once(__DIR__.'/config/Config.php');
use Config\Config;

ob_clean();
header('Content-type:application/json');
error_reporting(0);
$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
if (!$link) {
	echo -1;
} else {
	mysqli_set_charset($link, 'utf8');
	$p1_ = mysqli_query($link, "select id,last_message,UNIX_TIMESTAMP(last_message) as last_message_diff from devices where name='p1'");
	$p1__ = mysqli_fetch_array($p1_);
	$p1_id = $p1__["id"];
	$p1 = new stdClass();
	$p1->last_message = $p1__['last_message'];
	$p1->last_message_diff = $p1__['last_message_diff'];
	if (time()-$p1->last_message_diff > 30) {
		$p1->class = 'bg-danger';
	} else {
		$p1->class = 'bg-success';
	}
	$p1_log = mysqli_query($link, "select UNIX_TIMESTAMP(date) AS date_diff from logs where details='Eszköz észlelve' and device_id='$p1_id' order by date desc limit 0,1");
	$p1_log_ = mysqli_fetch_row($p1_log);
	$p1->last_login = $p1_log_[0];
	$elem = NULL;
	$p1_instructions = mysqli_query($link, "select instruction, state, date from instructions where receiver_id='$p1_id' order by id DESC limit 0,7");
	$utasitasok = array();
	while ($p1_instructions_ = mysqli_fetch_array($p1_instructions)) {
		$elem = new stdClass();
		switch ($p1_instructions_['instruction']) {
			case 0: 
				$elem->instruction = 'IDLE';
				$elem->state = $p1_instructions_['state'];
				break;
			case 1: 
				$elem->instruction = 'Üzemállapot';
				$elem->state = $p1_instructions_['state'];
				break;
			case 2: 
				$elem->instruction = 'Zárt állapot';
				$elem->state = $p1_instructions_['state'];
				break;
			case 3: 
				$elem->instruction = 'Mérés';
				$elem->state = $p1_instructions_['state'];
				break;
			case 4: 
				$elem->instruction = 'Savazó állapot';
				$elem->state = $p1_instructions_['state'];
				break;
			case 5: 
				$elem->instruction = 'P1 reset';
				$elem->state = $p1_instructions_['state'];
				break;
			case 6: 
				$elem->instruction = 'P2 reset';
				$elem->state = $p1_instructions_['state'];
				break;
			case 30:
			case 34:
				$elem->instruction = 'v1,v3 nyitás';
				$elem->state = $p1_instructions_['state'];
				break;
			case 31:
			case 35:
				$elem->instruction = 'v1,v3 zárás';
				$elem->state = $p1_instructions_['state'];
				break;
			case 32:
			case 36:
				$elem->instruction = 'v2,v4 nyitás';
				$elem->state = $p1_instructions_['state'];
				break;
			case 33:
			case 37:
				$elem->instruction = 'v2,v4 zárás';
				$elem->state = $p1_instructions_['state'];
				break;
			case 48:
				$elem->instruction = 'Üzemállapot foly...';
				$elem->state = $p1_instructions_['state'];
				break;
			case 50:
				$elem->instruction = 'Üzemállapot kész.';
				$elem->state = $p1_instructions_['state'];
				break;
			default:
				$elem->instruction = 'ismeretlen utasítás';
				$elem->state = $p1_instructions_['state'];
				break;
		}
		$elem->date = $p1_instructions_['date'];
		$utasitasok[] = $elem;
		unset($elem);
	}
	$p1->instructions = $utasitasok;
	$elem = NULL;
	$p1_ports = mysqli_query($link, "select * from ports where device_id='$p1_id' order by sort ASC, virtual_name ASC");
	$meresek = [];
	$controllers['v1'] = 'btn-warning'; // köztes állapot
	$controllers['v2'] = 'btn-warning';
	$controllers['v3'] = 'btn-warning';
	$controllers['v4'] = 'btn-warning';

	while ($p1_ports_ = mysqli_fetch_array($p1_ports)) {
		$elem = new stdClass();
		$p1_port_value = mysqli_query($link, "select *, UNIX_TIMESTAMP(date) AS date_diff from measurements where source_id='".$p1_ports_[0]."' order by id desc limit 0,1");
		$p1_port_value_ = mysqli_fetch_array($p1_port_value);
		$elem->virtual_name = $p1_ports_['description'];
		$elem->value = $p1_port_value_['value'];
		$elem->date = $p1_port_value_['date_diff'];
		$elem->unit = intval($p1_ports_['unit']);
		$elem->port = $p1_ports_['virtual_name'];
		$meresek[] = $elem;
		if ($p1_ports_['virtual_name'] == 'G16' && $p1_port_value_['value'] == 0) {
			$controllers['v1'] = 'btn-danger';
		} elseif ($p1_ports_['virtual_name'] == 'G13' && $p1_port_value_['value'] == 0) {
			$controllers['v1'] = 'btn-success';
		}
		if ($p1_ports_['virtual_name'] == 'G22' && $p1_port_value_['value'] == 0) {
			$controllers['v2'] = 'btn-danger';
		} elseif ($p1_ports_['virtual_name'] == 'G21' && $p1_port_value_['value'] == 0) {
			$controllers['v2'] = 'btn-success';
		}
		if ($p1_ports_['virtual_name'] == 'G12' && $p1_port_value_['value'] == 0) {
			$controllers['v3'] = 'btn-danger';
		} elseif ($p1_ports_['virtual_name'] == 'G6' && $p1_port_value_['value'] == 0) {
			$controllers['v3'] = 'btn-success';
		}
		if ($p1_ports_['virtual_name'] == 'G20' && $p1_port_value_['value'] == 0) {
			$controllers['v4'] = 'btn-danger';
		} elseif ($p1_ports_['virtual_name'] == 'G19' && $p1_port_value_['value'] == 0) {
			$controllers['v4'] = 'btn-success';
		}
	}

	$p1->measurements = $meresek;
	$elem = NULL;
	$ellenallasok = array();
	$p1_meresek = mysqli_query($link, "select * from statuses where instruction='p1_meres' and status='9'");
	while ($p1_meresek_ = mysqli_fetch_array($p1_meresek)) {
		$elem = new stdClass();
		$ellenallas = mysqli_query($link, "select DATE(date) AS date, result from instructions where statuses_id='".$p1_meresek_[0]."' and instruction='3' order by date desc limit 0,1");
		$ellenallas_ = mysqli_fetch_array($ellenallas);
		$elem->date = $ellenallas_[0];
		$elem->result = $ellenallas_[1];
		$ellenallasok[] = $elem;
		unset($elem);
	}
	$p1->resistance = $ellenallasok;
	$elem = NULL;
	//////////////////////////////////////////////////////
	$p2_ = mysqli_query($link, "select id,last_message, UNIX_TIMESTAMP(last_message) as last_message_diff from devices where name='p2'");
	$p2__ = mysqli_fetch_array($p2_);
	$p2_id = $p2__["id"];
	$p2 = new stdClass();
	$p2->last_message = $p2__["last_message"];
	$p2->last_message_diff = $p2__["last_message_diff"];
	if (time()-$p2->last_message_diff > 30) {
		$p2->class = 'bg-danger';
	} else {
		$p2->class = 'bg-success';
	}
	$p2_log = mysqli_query($link, "select UNIX_TIMESTAMP(date) as date_diff from logs where details='Eszköz észlelve' and device_id='$p2_id' order by date desc limit 0,1");
	$p2_log_ = mysqli_fetch_row($p2_log);
	$p2->last_login = $p2_log_[0];
	$elem = NULL;
	$p2_instructions = mysqli_query($link, "select instruction, state, date from instructions where receiver_id='$p2_id' order by id DESC limit 0,7");
	$utasitasok = array();
	while ($p2_instructions_ = mysqli_fetch_array($p2_instructions)) {
		$elem = new stdClass();
		switch ($p2_instructions_['instruction']) {
			case 0: 
				$elem->instruction = 'IDLE';
				$elem->state = $p2_instructions_['state'];
				break;
			case 1: 
				$elem->instruction = 'Üzemállapot';
				$elem->state = $p2_instructions_['state'];
				break;
			case 2: 
				$elem->instruction = 'Zárt állapot';
				$elem->state = $p2_instructions_['state'];
				break;
			case 3: 
				$elem->instruction = 'Mérés';
				$elem->state = $p2_instructions_['state'];
				break;
			case 4: 
				$elem->instruction = 'Savazó állapot';
				$elem->state = $p2_instructions_['state'];
				break;
			case 5: 
				$elem->instruction = 'P1 reset';
				$elem->state = $p2_instructions_['state'];
				break;
			case 6: 
				$elem->instruction = 'P2 reset';
				$elem->state = $p2_instructions_['state'];
				break;
			case 38:
			case 42:
				$elem->instruction = 'v5,v7 nyitás';
				$elem->state = $p2_instructions_['state'];
				break;
			case 39:
			case 43:
				$elem->instruction = 'v5,v7 zárás';
				$elem->state = $p2_instructions_['state'];
				break;
			case 40:
			case 44:
				$elem->instruction = 'v6,v8 nyitás';
				$elem->state = $p2_instructions_['state'];
				break;
			case 41:
			case 45:
				$elem->instruction = 'v6,v8 zárás';
				$elem->state = $p2_instructions_['state'];
				break;
			case 49:
				$elem->instruction = 'Üzemállapot foly...';
				$elem->state = $p2_instructions_['state'];
				break;
			case 51:
				$elem->instruction = 'Üzemállapot kész.';
				$elem->state = $p2_instructions_['state'];
				break;
			default:
				$elem->instruction = 'ismeretlen utasítás';
				$elem->state = $p2_instructions_['state'];
				break;
		}
		$elem->date = $p2_instructions_['date'];
		$utasitasok[] = $elem;
		unset($elem);
	}
	$p2->instructions = $utasitasok;
	$elem = NULL;
	$p2_ports = mysqli_query($link, "select * from ports where device_id='$p2_id' order by sort ASC, virtual_name ASC");
	$meresek = array();
	$controllers['v5'] = 'btn-warning'; // köztes állapot
	$controllers['v6'] = 'btn-warning';
	$controllers['v7'] = 'btn-warning';
	$controllers['v8'] = 'btn-warning';

	while ($p2_ports_ = mysqli_fetch_array($p2_ports)) {
		$elem = new stdClass();
		$p2_port_value = mysqli_query($link, "select *,UNIX_TIMESTAMP(date) as date_diff from measurements where source_id='".$p2_ports_[0]."' order by id desc limit 0,1");
		$p2_port_value_ = mysqli_fetch_array($p2_port_value);
		$elem->virtual_name = $p2_ports_['description'];
		$elem->value = $p2_port_value_['value'];
		$elem->date = $p2_port_value_['date_diff'];
		$elem->port = $p2_ports_['virtual_name'];
		$elem->unit = intval($p2_ports_['unit']);
		$meresek[] = $elem;

		if ($p2_ports_['virtual_name'] == 'G16' && $p2_port_value_['value'] == 0) {
			$controllers['v5'] = 'btn-danger';
		} elseif ($p2_ports_['virtual_name'] == 'G13' && $p2_port_value_['value'] == 0) {
			$controllers['v5'] = 'btn-success';
		}
		if ($p2_ports_['virtual_name'] == 'G22' && $p2_port_value_['value'] == 0) {
			$controllers['v6'] = 'btn-danger';
		} elseif ($p2_ports_['virtual_name'] == 'G21' && $p2_port_value_['value'] == 0) {
			$controllers['v6'] = 'btn-success';
		}
		if ($p2_ports_['virtual_name'] == 'G12' && $p2_port_value_['value'] == 0) {
			$controllers['v7'] = 'btn-danger';
		} elseif ($p2_ports_['virtual_name'] == 'G6' && $p2_port_value_['value'] == 0) {
			$controllers['v7'] = 'btn-success';
		}
		if ($p2_ports_['virtual_name'] == 'G20' && $p2_port_value_['value'] == 0) {
			$controllers['v8'] = 'btn-danger';
		} elseif ($p2_ports_['virtual_name'] == 'G19' && $p2_port_value_['value'] == 0) {
			$controllers['v8'] = 'btn-success';
		}
	}
	$p2->measurements = $meresek;
	$elem = NULL;
	$ellenallasok = array();
	$p2_meresek = mysqli_query($link, "select * from statuses where instruction='p2_meres' and status='9'");
	while ($p2_meresek_ = mysqli_fetch_array($p2_meresek)) {
		$elem = new stdClass();
		$ellenallas = mysqli_query($link, "select DATE(date) AS date, result from instructions where statuses_id='".$p2_meresek_[0]."' and instruction='3' order by date desc limit 0,1");
		$ellenallas_ = mysqli_fetch_array($ellenallas);
//			echo "<hr>" . json_encode($ellenallas_) . "<hr>";
		$elem->date = $ellenallas_[0];
		$elem->result = $ellenallas_[1];
		$ellenallasok[] = $elem;
		unset($elem);
	}
//		echo "<hr>" . json_encode($ellenallasok) . "<hr>";
	$p2->resistance = $ellenallasok;
	$elem = NULL;
	//////////////////////////////////////////////////////
	$p3_ = mysqli_query($link, "select id,last_message, UNIX_TIMESTAMP(last_message) as last_message_diff from devices where name='p3'");
	$p3__ = mysqli_fetch_array($p3_);
	$p3_id = $p3__["id"];
	$p3 = new stdClass();
	$p3->last_message = $p3__["last_message"];
	$p3->last_message_diff = $p3__["last_message_diff"];
	if (time()-$p3->last_message_diff > 30) {
		$p3->class = "bg-danger";
	} else {
		$p3->class = "bg-success";
	}
	$p3_log = mysqli_query($link, "select UNIX_TIMESTAMP(date) AS date_diff from logs where details='Eszköz észlelve' and device_id='$p3_id' order by date desc limit 0,1");
	$p3_log_ = mysqli_fetch_row($p3_log);
	$p3->last_login = $p3_log_[0];
	$elem = NULL;
	$p3_instructions = mysqli_query($link, "select instruction, date from instructions where receiver_id='$p3_id' and (instruction != 10 or state != 2) order by id DESC limit 0,7");
	$utasitasok = array();
	$counter = 0;
	while ($p3_instructions_ = mysqli_fetch_array($p3_instructions)) {
		$elem = new stdClass();
		if ($counter == 0 && ($p3_instructions_['instruction'] == 5 || $p3_instructions_['instruction'] == 6)) {
			$p3->class = 'bg-warning';
		}
		switch ($p3_instructions_['instruction']) {
			case 0: 
				$elem->instruction = 'IDLE';
				break;
			case 1: 
				$elem->instruction = 'Üzemállapot';
				break;
			case 2: 
				$elem->instruction = 'Zárt állapot';
				break;
			case 3: 
				$elem->instruction = 'Mérés';
				break;
			case 4: 
				$elem->instruction = 'Savazó állapot';
				break;
			case 5: 
				$elem->instruction = 'P1 reset';
				break;
			case 6: 
				$elem->instruction = 'P2 reset';
				break;
			case 10:
				$elem->instruction = 'Kézi üzemmód';
				break;
			default:
				$elem->instruction = 'ismeretlen utasítás';
				break;
		}
		$elem->date = $p3_instructions_['date'];
		$utasitasok[] = $elem;
		$counter++;
	}
	$p3->instructions = $utasitasok;
	$elem = NULL;
	/*
	
		v1-v8, ksz
		G16 = 0 : v1/v5 zárva 
		G13 = 0 : v1/v5 nyitva
		G22 = 0 : v2/v6 zárva
		G21 = 0 : v2/v6 nyitva
		G12 = 0 : v3/v7 zárva
		 G6 = 0 : v3/v7 nyitva
		G20 = 0 : v4/v8 zárva
		G19 = 0 : v4/v8 nyitva
		G19 = 0 : ksz jár
		G19 = 1 : ksz áll
	*/
	$p3_ports = mysqli_query($link, "select * from ports where device_id='$p3_id' order by sort ASC, virtual_name ASC");
	$meresek = array();
	while ($p3_ports_ = mysqli_fetch_array($p3_ports)) {
		$elem = new stdClass();
		$p3_port_value = mysqli_query($link, "select *, UNIX_TIMESTAMP(date) as date_diff from measurements where source_id='".$p3_ports_[0]."' order by id desc limit 0,1");
		$p3_port_value_ = mysqli_fetch_array($p3_port_value);
		$elem->virtual_name = $p3_ports_['description'];
		$elem->value = $p3_port_value_['value'];
		$elem->date = $p3_port_value_['date_diff'];
		$elem->unit = intval($p3_ports_['unit']);
		$elem->port = $p3_ports_['virtual_name'];
		$meresek[] = $elem;
		if ($p3_ports_['virtual_name'] == 'G22' && $p3_port_value_['value'] == 0) {
			$controllers['ksz'] = 'bg-success';
		} elseif ($p3_ports_['virtual_name'] == 'G22' && $p3_port_value_['value'] == 1) {
			$controllers['ksz'] = 'bg-danger';
		}
	}
	$p3->measurements = $meresek;
	$elem = NULL;
	//////////////////////////////////////////////////////
	$log_array = array();
	$logs = mysqli_query($link, "select details, UNIX_TIMESTAMP(date) AS date_diff, device_id from logs order by id desc limit 0,10");
	while ($logs_ = mysqli_fetch_array($logs)) {
		if ($logs_[2] != 0) {
			$device = mysqli_query($link, "select name from devices where id='".$logs_[2]."'");
			$device_ = mysqli_fetch_row($device);
			$nev = $device_[0].": ";
		} else {
			$nev = "";
		}
		$elem = new stdClass();
		$elem->details = $nev.$logs_[0];
		$elem->date_diff = $logs_[1];
		$log_array[] = $elem;
		unset($elem);
	}
	$elem = NULL;
	//////////////////////////////////////////////////////
	$status_array = array();
	$statuses = mysqli_query($link, 'select * from statuses order by id desc limit 0,10');
	while ($statuses_ = mysqli_fetch_array($statuses)) {
		$elem = new stdClass();
		$elem->task = $statuses_['instruction'];
		$elem->wdate = $statuses_['date'];
		if ($statuses_['status'] == 0) {
			$elem->status = 'LÉTREHOZVA';
			$elem->class = 'danger';
		} elseif ($statuses_['status'] == 1) {
			$elem->status = 'FOLYAMATBAN';
			$elem->class = 'warning';
		} elseif ($statuses_['status'] == 9) {
			$elem->status = 'KÉSZ';
			$elem->class = '';
		}
		$sdate = mysqli_query($link, "select ready from instructions where statuses_id='".$statuses_["id"]."' order by id asc limit 0,1");
		$sdate_ = mysqli_fetch_row($sdate);
		$rdate = mysqli_query($link, "select ready from instructions where statuses_id='".$statuses_["id"]."' order by id desc limit 0,1");
		$rdate_ = mysqli_fetch_row($rdate);
		$result = mysqli_query($link, "select result from instructions where statuses_id='".$statuses_["id"]."' and result IS NOT NULL order by id desc limit 0,1");
		$result_ = mysqli_fetch_row($result);
		$elem->sdate = date('Y-m-d H:i:s', $sdate_[0]);
		$elem->rdate = date('Y-m-d H:i:s', $rdate_[0]);
		$elem->result = $result_[0];
		$elem->userid = $statuses_['user_id'];
		$status_array[] = $elem;
		unset($elem);
	}
	//////////////////////////////////////////////////////
	// reset állapotok
	$p1->reset = file_exists('/home/pi/arbo/reset/p1') ? 1 : 0;
	$p2->reset = file_exists('/home/pi/arbo/reset/p2') ? 1 : 0;
	if ($p1->reset) {
		$p1->class = 'bg-danger';
	}
	if ($p2->reset) {
		$p2->class = 'bg-danger';
	}
	//////////////////////////////////////////////////////
	$p1->checksum = md5(serialize($p1));
	$json['p1'] = $p1;
	
	$p2->checksum = md5(serialize($p2));
	$json['p2'] = $p2;
	
	$p3->checksum = md5(serialize($p3));
	$json['p3'] = $p3;
	
	$json['general']['controllers'] = $controllers;
	$json['general']['logs'] = $log_array;
	$json['general']['statuses'] = $status_array;
	
	$json['general']['checksum'] = md5(serialize($json['general']));
	
	$json = json_encode($json);
	echo $json;
}
mysqli_close($link);
