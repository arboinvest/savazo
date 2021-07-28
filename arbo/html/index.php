<?php

require_once(__DIR__.'/config/Config.php');
use Config\Config;

//error_reporting(E_ERROR | E_WARNING);
error_reporting(0);
define("SAVAZASI_HATAR", 100);

function getMacLinux() {
	exec('netstat -ie', $result);
	if (is_array($result)) {
		$iface = array();
		foreach($result as $key => $line) {
			if($key > 0) {
				$macpos = strpos($line, "ether");
				if($macpos !== false) {
					$iface[] = array('mac' => strtolower(substr($line, $macpos+6, 17)));
				}
			}
		}
		return $iface[0]['mac'];
	} else {
		return false;
	}
}

$action = $_POST['action'];

if ($action < 8) {

	$ipAddress = $_SERVER['REMOTE_ADDR'];
	$macAddr = false;

	#run the external command, break output into lines
	$arp = `arp -a $ipAddress`;
	$lines = explode("\n", $arp);

	#look for the output line describing our IP address
	foreach($lines as $line)
	{
	   $cols=preg_split('/\s+/', trim($line));
	   if ($cols[1] == '('.$ipAddress.')')
	   {
		   $macAddr = $cols[3];
	   }
	}
	if ($macAddr == false) {
		$macAddr = getMacLinux();
	}
	$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
	if (!$link) {
		ob_clean();
		echo -11109;
		exit;
	}

	mysqli_set_charset($link, "utf8");
	$lekerdezes = mysqli_query($link, 'select id,master,name,closed from devices where mac_address=\'' . $macAddr . '\''); //  and ip_address='".$ipAddress."'
	$lekerdezes_ = mysqli_fetch_assoc($lekerdezes);


	if ($lekerdezes_["id"] == NULL) {
		
			// /***LOG*/ mysqli_query($link, "insert into logs values('','0','Sikertelen azonosítás: ".$macAddr." - ".$ipAddress."',NOW())");
			mysqli_close($link);		
			ob_clean();
			echo -11111;
			exit;

	} else {
		mysqli_query($link, "update devices set last_message=NOW() where id='".$lekerdezes_["id"]."'");
	}
} else {
	$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
}

// /***LOG*/ mysqli_query($link, "insert into logs values ('',3,'D: action=".$action.",NOW())");


switch($action) {

	case 0: {
			// /***LOG*/ mysqli_query($link, "insert into logs values('','".$lekerdezes_["id"]."','Eszköz észlelve',NOW())");
			ob_clean();
			echo $lekerdezes_["master"];
		}
		break;



	case 1: {
			$ertekek = $_POST['ertekek'];
			$ertekek = explode(',',$ertekek);
			foreach ($ertekek as $ertekpar) {
				$ertekpar = explode(":",$ertekpar);
				$port_lekerdezes = mysqli_query($link, "select id from ports where device_id='".$lekerdezes_["id"]."' and virtual_name='".$ertekpar[0]."'");
				$port_lekerdezes_ = mysqli_fetch_assoc($port_lekerdezes);
				mysqli_query($link, "insert into measurements values('','".$port_lekerdezes_["id"]."','".$ertekpar[1]."',NOW())");
			}
			ob_clean();
			echo 1;
		}
		break;



	case 2: {
			/*
				0 = nincs parancs
				1 = üzemállapot
				2 = zár
				3 = mér
				4 = savaz
				
				#0 - létrehozva
				#1 - folyamatban
				#9 - végrehajtva
			*/
			$feladat = mysqli_query($link, 'select * from statuses where status != 9 AND instruction != \'p3_kezi\'');
			if (mysqli_num_rows($feladat) == 0) {
				#nincs folyamatban lévő feladat
				if ($lekerdezes_['name'] == 'p3' && $lekerdezes_['closed'] == 0) {
					#csak p3 adhat ki feladatot
					#aktív állapotok ellenőrzése
					$chk = mysqli_query($link, "select * from devices where (NOW()-last_message) > 60");
					if (mysqli_num_rows($chk) > 0) {
						#nem adható ki parancs, üzemi állapot fenntartása, időlimit ellenőrzése
						#P1 időlimit ellenőrzése (3 perc)
						$chk = mysqli_query($link, "select id from devices where name='p1' and (NOW()-last_message) > 180");
						#utolsó reset jel lekérdezése
						$pc = mysqli_query($link, "select id from statuses where instruction='p1_reset' and status = 9 and (NOW()-date) < 180");
						if (mysqli_num_rows($chk) == 1 && mysqli_num_rows($pc) == 0) {
							#P1-et resetelni kell
							#eszközök lekérdezése utasítás kiadáshoz
							$eszkozok = mysqli_query($link, "select * from devices");
							while($eszkoz = mysqli_fetch_assoc($eszkozok)) {
								$eszkozok_[] = $eszkoz;
							}
							$p1_index = array_search('p1', array_column($eszkozok_, 'name'));
							$p2_index = array_search('p2', array_column($eszkozok_, 'name'));
							$p3_index = array_search('p3', array_column($eszkozok_, 'name'));
							#tranzakció létrehozása
							mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
							#feladat rekord létrehozása
							mysqli_query($link, "insert into statuses values('','p1_reset',NOW(),'0',0)");
							$statuses_id = mysqli_insert_id($link);
							#utasítás rekordok létrehozása
							mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','5','0',NULL,NOW(),NULL)");
							#tranzakció mentése
							mysqli_commit($link);
						} else {
							#P2 időlimit ellenőrzése (3 perc)
							$chk = mysqli_query($link, "select id from devices where name='p2' and (NOW()-last_message)>180");
							#utolsó reset jel lekérdezése
							$pc = mysqli_query($link, "select id from statuses where instruction='p2_reset' and status='9' and (NOW()-date)<180 order by id desc limit 0,1");
							if (mysqli_num_rows($chk) == 1 && mysqli_num_rows($pc) == 0) {
								#P2-t resetelni kell
								#eszközök lekérdezése utasítás kiadáshoz
								$eszkozok = mysqli_query($link, "select * from devices");
								while($eszkoz = mysqli_fetch_assoc($eszkozok)) {
									$eszkozok_[] = $eszkoz;
								}
								$p1_index = array_search('p1', array_column($eszkozok_, 'name'));
								$p2_index = array_search('p2', array_column($eszkozok_, 'name'));
								$p3_index = array_search('p3', array_column($eszkozok_, 'name'));
								#tranzakció létrehozása
								mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
								#feladat rekord létrehozása
								mysqli_query($link, "insert into statuses values('','p2_reset',NOW(),'0',0)");
								$statuses_id = mysqli_insert_id($link);
								#utasítás rekordok létrehozása
								mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','6','0',NULL,NOW(),NULL)");
								#tranzakció mentése
								mysqli_commit($link);
							}
						}
						ob_clean();
						echo 0;
					} else {
						#p1 utolsó mérésének ellenőrzése
						$chk = mysqli_query($link, "select * from statuses where instruction='p1_meres' and status='9' and (NOW()-date)<604800 order by id desc limit 0,1");
						#p1 utolsó savazásának ellenőrzése
						$chk2 = mysqli_query($link, "select id from statuses where id > IFNULL((select id from statuses where instruction='p1_savazas' order by id DESC limit 0,1), 0) and instruction = 'p1_meres'");
						if (mysqli_num_rows($chk) == 0 || mysqli_num_rows($chk2) == 0) {
							#az elmúlt 7 napba nem történt mérés, vagy a savazás után még nem mértünk
							#eszközök lekérdezése utasítás kiadáshoz
							$eszkozok = mysqli_query($link, "select * from devices");
							while($eszkoz = mysqli_fetch_assoc($eszkozok)) {
								$eszkozok_[] = $eszkoz;
							}
							$p1_index = array_search('p1', array_column($eszkozok_, 'name'));
							$p2_index = array_search('p2', array_column($eszkozok_, 'name'));
							$p3_index = array_search('p3', array_column($eszkozok_, 'name'));
							#tranzakció létrehozása
							mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
							#feladat rekord létrehozása
							mysqli_query($link, "insert into statuses values('','p1_meres',NOW(),'0',0)");
							$statuses_id = mysqli_insert_id($link);
							#utasítás rekordok létrehozása
							mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','1','0',NULL,NOW(),NULL)");
							mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','2','0',NULL,NOW(),NULL)");
							mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','3','0',NULL,NOW(),NULL)");
							mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','1','0',NULL,NOW(),NULL)");
							#tranzakció mentése
							mysqli_commit($link);
							ob_clean();
							echo 0;
							return 1;
						} else {
							#az utolsó mérés eredményének az ellenőrzése
							$chk_ = mysqli_fetch_assoc($chk);
							$utolso_vegrehajtas = mysqli_query($link, "select result from instructions where statuses_id='".$chk_['id']."' and instruction='3' order by id DESC limit 0,1");
							$utolso_vegrehajtas_ = mysqli_fetch_row($utolso_vegrehajtas);
							if ($utolso_vegrehajtas_[0] >= SAVAZASI_HATAR*1.5) {
								$e1 = mysqli_query($link, "select value from measurements left join ports on source_id=ports.id left join devices on device_id=devices.id where devices.name='p3' and ports.virtual_name='G13' and (NOW()-measurements.date)<=15 order by measurements.id DESC limit 0,1");
								$e2 = mysqli_query($link, "select value from measurements left join ports on source_id=ports.id left join devices on device_id=devices.id where devices.name='p3' and ports.virtual_name='G12' and (NOW()-measurements.date)<=15 order by measurements.id DESC limit 0,1");
								$e3 = mysqli_query($link, "select value from measurements left join ports on source_id=ports.id left join devices on device_id=devices.id where devices.name='p3' and ports.virtual_name='G6' and (NOW()-measurements.date)<=15 order by measurements.id DESC limit 0,1");
								if (mysqli_num_rows($e1) == 1 && mysqli_num_rows($e2) == 1 && mysqli_num_rows($e3) == 1) {
									$e1 = mysqli_fetch_row($e1);
									$e2 = mysqli_fetch_row($e2);
									if ($e1[0] != 1 || $e2[0] != 1 || $e3[0] != 1) {
										// /***LOG*/ mysqli_query($link, "insert into logs values('','0','Riasztás! Keverőtartály hiba!',NOW())");
									} else {
										#eszközök lekérdezése utasítás kiadáshoz
										$eszkozok = mysqli_query($link, "select * from devices");
										while($eszkoz = mysqli_fetch_assoc($eszkozok)) {
											$eszkozok_[] = $eszkoz;
										}
										$p1_index = array_search('p1', array_column($eszkozok_, 'name'));
										$p2_index = array_search('p2', array_column($eszkozok_, 'name'));
										$p3_index = array_search('p3', array_column($eszkozok_, 'name'));
										#tranzakció létrehozása
										mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
										#feladat rekord létrehozása
										mysqli_query($link, "insert into statuses values('','p1_savazas',NOW(),'0',0)");
										$statuses_id = mysqli_insert_id($link);
										#utasítás rekordok létrehozása
										mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','2','0',NULL,NOW(),NULL)");
										mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','4','0',NULL,NOW(),NULL)");
										mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','4','0',NULL,NOW(),NULL)");
										mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','1','0',NULL,NOW(),NULL)");
										mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','2','0',NULL,NOW(),NULL)");
										mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','1','0',NULL,NOW(),NULL)");
										#tranzakció mentése
										mysqli_commit($link);
										ob_clean();
										echo 0;
										return 1;
									}
								}
							}
						}
						#p2 utolsó mérésének ellenőrzése
						$chk = mysqli_query($link, "select * from statuses where instruction='p2_meres' and status = 9 and (NOW()-date) < 604800 order by id desc limit 0,1");
						#p2 utolsó savazásának ellenőrzése
						$chk2 = mysqli_query($link, "select id from statuses where id > IFNULL((select id from statuses where instruction='p2_savazas' order by id DESC limit 0,1),0) and instruction = 'p2_meres'");
						if (mysqli_num_rows($chk) == 0 || mysqli_num_rows($chk2) == 0) {
							#az elmúlt 7 napba nem történt mérés, vagy a savazás után még nem mértünk
							#eszközök lekérdezése utasítás kiadáshoz
							$eszkozok = mysqli_query($link, "select * from devices");
							while($eszkoz = mysqli_fetch_assoc($eszkozok)) {
								$eszkozok_[] = $eszkoz;
							}
							$p1_index = array_search('p1', array_column($eszkozok_, 'name'));
							$p2_index = array_search('p2', array_column($eszkozok_, 'name'));
							$p3_index = array_search('p3', array_column($eszkozok_, 'name'));
							#tranzakció létrehozása
							mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
							#feladat rekord létrehozása
							mysqli_query($link, "insert into statuses values('','p2_meres',NOW(),'0',0)");
							$statuses_id = mysqli_insert_id($link);
							#utasítás rekordok létrehozása
							mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','1','0',NULL,NOW(),NULL)");
							mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','2','0',NULL,NOW(),NULL)");
							mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','3','0',NULL,NOW(),NULL)");
							mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','1','0',NULL,NOW(),NULL)");
							#tranzakció mentése
							mysqli_commit($link);
							ob_clean();
							echo 0;
							return 1;
						} else {
							#az utolsó mérés eredményének az ellenőrzése
							$chk_ = mysqli_fetch_assoc($chk);
							$utolso_vegrehajtas = mysqli_query($link, "select result from instructions where statuses_id='".$chk_['id']."' and instruction='3' order by id DESC limit 0,1");
							$utolso_vegrehajtas_ = mysqli_fetch_row($utolso_vegrehajtas);
							if ($utolso_vegrehajtas_[0] >= SAVAZASI_HATAR*1.5) {
								$e1 = mysqli_query($link, "select value from measurements left join ports on source_id=ports.id left join devices on device_id=devices.id where devices.name='p3' and ports.virtual_name='G13' and (NOW()-measurements.date)<=15 order by measurements.id DESC limit 0,1");
								$e2 = mysqli_query($link, "select value from measurements left join ports on source_id=ports.id left join devices on device_id=devices.id where devices.name='p3' and ports.virtual_name='G12' and (NOW()-measurements.date)<=15 order by measurements.id DESC limit 0,1");
								$e3 = mysqli_query($link, "select value from measurements left join ports on source_id=ports.id left join devices on device_id=devices.id where devices.name='p3' and ports.virtual_name='G6' and (NOW()-measurements.date)<=15 order by measurements.id DESC limit 0,1");
								if (mysqli_num_rows($e1) == 1 && mysqli_num_rows($e2) == 1 && mysqli_num_rows($e3) == 1) {
									$e1 = mysqli_fetch_row($e1);
									$e2 = mysqli_fetch_row($e2);
									if ($e1[0] != 1 || $e2[0] != 1 || $e3[0] != 1) {
										// /***LOG*/ mysqli_query($link, "insert into logs values('','0','Riasztás! Keverőtartály hiba!',NOW())");
									} else {
										#eszközök lekérdezése utasítás kiadáshoz
										$eszkozok = mysqli_query($link, "select * from devices");
										while($eszkoz = mysqli_fetch_assoc($eszkozok)) {
											$eszkozok_[] = $eszkoz;
										}
										$p1_index = array_search('p1', array_column($eszkozok_, 'name'));
										$p2_index = array_search('p2', array_column($eszkozok_, 'name'));
										$p3_index = array_search('p3', array_column($eszkozok_, 'name'));
										#tranzakció létrehozása
										mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
										#feladat rekord létrehozása
										mysqli_query($link, "insert into statuses values('','p2_savazas',NOW(),'0',0)");
										$statuses_id = mysqli_insert_id($link);
										#utasítás rekordok létrehozása
										mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','2','0',NULL,NOW(),NULL)");
										mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','4','0',NULL,NOW(),NULL)");
										mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','4','0',NULL,NOW(),NULL)");
										mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','1','0',NULL,NOW(),NULL)");
										mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','2','0',NULL,NOW(),NULL)");
										mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','1','0',NULL,NOW(),NULL)");
										#tranzakció mentése
										mysqli_commit($link);
										ob_clean();
										echo 0;
										return 1;
									}
								}
							}
						}
						#nincs teendő
						ob_clean();
						echo 0;
					}
				} else {
					#csak p3 adhat ki feladatatot
					ob_clean();
					echo 0;
				}
			} else {
				$feladat_ = mysqli_fetch_assoc($feladat);
				$utasitas = mysqli_query($link, "select * from instructions where state < 9 and statuses_id=".$feladat_['id']." order by id ASC limit 0,1");
				if (mysqli_num_rows($utasitas) == 0) {
					#nincs hátralévő utasítás, a feladat viszont nem lett készre jelentve
					#feladat készre jelentése

					mysqli_query($link, 'update statuses set status = 9 where id='.$feladat_['id'].' and instruction != \'p3_kezi\'');
					ob_clean();
					echo 0;
				} else {
					$utasitas_ = mysqli_fetch_assoc($utasitas);
					if ($utasitas_['receiver_id'] != $lekerdezes_['id']) {
						#az utasítás másnak szól, nincs teendő
						ob_clean();
						echo 0;
					} else {
						mysqli_query($link, 'update instructions set state = 1 where id = '.$utasitas_['id']);
						ob_clean();
						echo $utasitas_['instruction'];
					}
				}
			}
		}
		break;






	case 3: {
			// /***LOG*/ mysqli_query($link, "insert into logs values ('',3,'D: action=3',NOW())");
			$allapot = $_POST['allapot'];
			$feladat = mysqli_query($link, "select * from statuses where status != 9 AND instruction != 'p3_kezi'");
			$feladat_ = mysqli_fetch_assoc($feladat);
			$utasitas = mysqli_query($link, "select * from instructions where state < 9 and statuses_id = '".$feladat_["id"]."' order by id ASC limit 0,1");
			$utasitas_ = mysqli_fetch_assoc($utasitas);
			if ($utasitas_["receiver_id"] != $lekerdezes_["id"]) {
			// /***LOG*/ mysqli_query($link, "insert into logs values ('',3,'D: receiver_id=id',NOW())");
				#az utasítás másnak szól, soron kívüli jelentés volt, vagy nincs feladat, nincs módosítás
			} else {
				if ($utasitas_["instruction"] == $allapot) {
				// /***LOG*/ mysqli_query($link, "insert into logs values ('',3,'D: instruction=allapot',NOW())");
					#a visszajelzés egyezik a kéréssel
					if ($allapot == 3 && $lekerdezes_['name'] == 'p3') {
						#kiszámítjuk a mért értéket
						if ($feladat_['instruction'] == 'p1_meres' || $feladat_['instruction'] == 'p2_meres') {
							$e1 = mysqli_query($link, "select value from measurements left join ports on source_id=ports.id left join devices on device_id=devices.id where devices.name='p3' and ports.virtual_name='A4' and (NOW()-measurements.date)<=15 order by measurements.id DESC limit 0,1");
							$e2 = mysqli_query($link, "select value from measurements left join ports on source_id=ports.id left join devices on device_id=devices.id where devices.name='p3' and ports.virtual_name='A5' and (NOW()-measurements.date)<=15 order by measurements.id DESC limit 0,1");
							$e3 = mysqli_query($link, "select value from measurements left join ports on source_id=ports.id left join devices on device_id=devices.id where devices.name='p3' and ports.virtual_name='A3' and (NOW()-measurements.date)<=15 order by measurements.id DESC limit 0,1");
						}
						if (mysqli_num_rows($e1) == 1 && mysqli_num_rows($e2) == 1 && mysqli_num_rows($e3) == 1) {
							$e1 = mysqli_fetch_row($e1);
							$e2 = mysqli_fetch_row($e2);
							$e3 = mysqli_fetch_row($e3);
							$ellenallas = ($e1[0]-$e2[0])/$e3[0];
							mysqli_query($link, "update instructions set state='9',result='$ellenallas',ready='".time()."' where id='".$utasitas_["id"]."'");
						}
					} elseif ($allapot == 4 && $lekerdezes_['name'] == 'p3') {
						#várakozási idő ellenőrzése
						$utolso_vegrehajtott_utasitas = mysqli_query($link, "select ready from instructions where state='9' and statuses_id='".$feladat_["id"]."' order by id DESC limit 0,1");
						$utolso_vegrehajtott_utasitas_ = mysqli_fetch_row($utolso_vegrehajtott_utasitas);
						if (time()-$utolso_vegrehajtott_utasitas_[0]>120) {
							#a savazás kezdete óta több mint 2 perc telt el
							$e1 = mysqli_query($link, "select value from measurements left join ports on source_id=ports.id left join devices on device_id=devices.id where devices.name='p3' and ports.virtual_name='A1' and (NOW()-measurements.date)<=15 order by measurements.id DESC limit 0,1");
							$e2 = mysqli_query($link, "select value from measurements left join ports on source_id=ports.id left join devices on device_id=devices.id where devices.name='p3' and ports.virtual_name='A2' and (NOW()-measurements.date)<=15 order by measurements.id DESC limit 0,1");
							#golyósszelep állapotának lekérdezése
							if ($feladat_['instruction'] == 'p1_savazas')
								$e3 = mysqli_query($link, "select value from measurements left join ports on source_id=ports.id left join devices on device_id=devices.id where devices.name='p1' and ports.virtual_name='G13' and (NOW()-measurements.date)<=15 order by measurements.id DESC limit 0,1");
							elseif ($feladat_['instruction'] == 'p2_savazas')
								$e3 = mysqli_query($link, "select value from measurements left join ports on source_id=ports.id left join devices on device_id=devices.id where devices.name='p2' and ports.virtual_name='G13' and (NOW()-measurements.date)<=15 order by measurements.id DESC limit 0,1");
							if (mysqli_num_rows($e1) == 1 && mysqli_num_rows($e2) == 1 && mysqli_num_rows($e3) == 1) {
								#cél vizsgálat
								$e1 = mysqli_fetch_row($e1);
								$e2 = mysqli_fetch_row($e2);
								$e3 = mysqli_fetch_row($e3);
								$allapot = ($e2[0]-$e1[0])/$e1[0];
								if (time()-$utolso_vegrehajtott_utasitas_[0]>7200 || $e3[0] != 0) {
									//2 óra eltelt vagy a golyósszelep nincs nyitva; állítsa le a savazást
									mysqli_query($link, "update instructions set state='9',result='$allapot',ready='".time()."' where id='".$utasitas_["id"]."'");
								} else {
									/*	Végállapot kivéve amíg a szenzort javítják!
										if ($allapot < 0.1) {
											mysqli_query($link, "update instructions set state='9',result='$allapot',ready='".time()."' where id='".$utasitas_["id"]."'");
										}
									*/
								}
							} else {
								#nem elérhető a golyósszelep állapota, a savazás leáll
								mysqli_query($link, "update instructions set state='9',ready='".time()."' where id='".$utasitas_["id"]."'");
							}
						}
					} else {
						// /***LOG*/ mysqli_query($link, "insert into logs values ('',3,'D: a feladat létre lett hajtva, teljesítettnek jelöljük',NOW())");
						#a feladat létre lett hajtva, teljesítettnek jelöljük
						mysqli_query($link, "update instructions set state = 9, ready = ".time()." where id = ".$utasitas_["id"]);
					}
				} else {
					#a visszajelzés nem egyezik a kéréssel, az utasítás változatlan
				}
			}
			ob_clean();
			echo 1;
		}
		break;




	///// p1,p2 -től jövő üzenetek beírása
	case 4: {
			$p1id = mysqli_query($link, 'SELECT id FROM devices WHERE name = \'p1\' LIMIT 0,1');
			$p1id = mysqli_fetch_assoc($p1id)['id'];
			mysqli_query($link, "insert into logs values ('',".$p1id.",'hiba: V1,V3 nem nyitható!',NOW())");
			ob_clean();
			echo 0;
		}
		break;

	case 5: {
			$p1id = mysqli_query($link, 'SELECT id FROM devices WHERE name = \'p1\' LIMIT 0,1');
			$p1id = mysqli_fetch_assoc($p1id)['id'];
			mysqli_query($link, "insert into logs values ('',".$p1id.",'hiba: V2,V4 nem nyitható!',NOW())");
			ob_clean();
			echo 0;
		}
		break;

	case 6: {
			$p2id = mysqli_query($link, 'SELECT id FROM devices WHERE name = \'p2\' LIMIT 0,1');
			$p2id = mysqli_fetch_assoc($p2id)['id'];
			mysqli_query($link, "insert into logs values ('',".$p2id.",'hiba: V5,V7 nem nyitható!',NOW())");
			ob_clean();
			echo 0;
		}
		break;

	case 7: {
			$p2id = mysqli_query($link, 'SELECT id FROM devices WHERE name = \'p2\' LIMIT 0,1');
			$p2id = mysqli_fetch_assoc($p2id)['id'];
			mysqli_query($link, "insert into logs values ('',".$p2id.",'hiba: V6,V8 nem nyitható!',NOW())");
			ob_clean();
			echo 0;
		}
		break;

	case 8: {
			if (file_exists('/home/pi/arbo/reset/p1')) {
				mysqli_query($link, "update instructions set state='9',ready='".time()."' where statuses_id in (select S.id from statuses S where S.status = 0 AND S.instruction = 'p1_reset')");
				mysqli_query($link, "update statuses set status=9 where S.status = 0 AND S.instruction = 'p1_reset')");
				unlink('/home/pi/arbo/reset/p1');
			}
			ob_clean();
			echo 1;
		}
		break;

	case 9: {
			if (file_exists('/home/pi/arbo/reset/p2')) {
				unlink('/home/pi/arbo/reset/p2');
				mysqli_query($link, "update instructions set state='9',ready='".time()."' where statuses_id in (select S.id from statuses S where S.status = 0 AND S.instruction = 'p2_reset')");
				mysqli_query($link, "update statuses set status=9 where S.status = 0 AND S.instruction = 'p2_reset')");
				unlink('/home/pi/arbo/reset/p2');
			}
			ob_clean();
			echo 1;
		}
		break;

	case 10: {
			// eszköz értékének lekérdezése 
			$pxid = mysqli_query($link, 'SELECT id FROM devices WHERE name = \'' . $_POST['device'] . '\' LIMIT 0,1');
			$pxid = mysqli_fetch_assoc($pxid)['id'];
			$portid = mysqli_query($link, 'SELECT id FROM ports WHERE virtual_name = \'' . $_POST['name'] . '\' AND device_id = ' . $pxid . ' LIMIT 0,1');
			$portid = mysqli_fetch_assoc($portid)['id'];
			$mes = mysqli_query($link, 'SELECT value FROM measurements WHERE source_id = ' . $portid . ' ORDER BY id DESC LIMIT 0,1');
			$mes = mysqli_fetch_assoc($mes)['value'];
			ob_clean();
			echo $mes;
		}
		break;

	///// p1,p2 -től jövő üzenetek beírása
	case 11: {
			$p1id = mysqli_query($link, 'SELECT id FROM devices WHERE name = \'p1\' LIMIT 0,1');
			$p1id = mysqli_fetch_assoc($p1id)['id'];
			mysqli_query($link, "insert into logs values ('',".$p1id.",'hiba: V1,V3 nem zárható!',NOW())");
			ob_clean();
			echo 0;
		}
		break;

	case 12: {
			$p1id = mysqli_query($link, 'SELECT id FROM devices WHERE name = \'p1\' LIMIT 0,1');
			$p1id = mysqli_fetch_assoc($p1id)['id'];
			mysqli_query($link, "insert into logs values ('',".$p1id.",'hiba: V2,V4 nem zárható!',NOW())");
			ob_clean();
			echo 0;
		}
		break;

	case 13: {
			$p2id = mysqli_query($link, 'SELECT id FROM devices WHERE name = \'p2\' LIMIT 0,1');
			$p2id = mysqli_fetch_assoc($p2id)['id'];
			mysqli_query($link, "insert into logs values ('',".$p2id.",'hiba: V5,V7 nem zárható!',NOW())");
			ob_clean();
			echo 0;
		}
		break;

	case 14: {
			$p2id = mysqli_query($link, 'SELECT id FROM devices WHERE name = \'p2\' LIMIT 0,1');
			$p2id = mysqli_fetch_assoc($p2id)['id'];
			mysqli_query($link, "insert into logs values ('',".$p2id.",'hiba: V6,V8 nem zárható!',NOW())");
			ob_clean();
			echo 0;
		}
		break;

	case 15: {
			$p1id = mysqli_query($link, 'SELECT id FROM devices WHERE name = \'p1\' LIMIT 0,1');
			$p1id = mysqli_fetch_assoc($p1id)['id'];
			mysqli_query($link, "insert into logs values ('',".$p1id.",'hiba: A P1 hőmérs.érzékelője nem üzemel, ezért a vezérlés leállt!',NOW())");
			ob_clean();
			echo 0;
		}
		break;

	case 16: {
			$p2id = mysqli_query($link, 'SELECT id FROM devices WHERE name = \'p2\' LIMIT 0,1');
			$p2id = mysqli_fetch_assoc($p2id)['id'];
			mysqli_query($link, "insert into logs values ('',".$p2id.",'hiba: A P2 hőmérs.érzékelője nem üzemel, ezért a vezérlés leállt!',NOW())");
			ob_clean();
			echo 0;
		}
		break;

	 //// összes P1 folyamatban lévő kézi utasítások törlése
	case 17: {
			mysqli_query($link, "UPDATE statuses SET status = 9 WHERE status != 9 AND (instruction like '%V1%' OR instruction like '%V2%' OR instruction like '%V3%' OR instruction like '%V4%' OR instruction like '%_kezi')");
			mysqli_query($link, "UPDATE instructions SET state = 2 WHERE state in (0,1,3) AND (instruction = 10 OR (instruction >= 30 AND instruction <= 37))");
			ob_clean();
			echo 0;
		}
		break;

	 //// összes P1 folyamatban lévő kézi utasítások törlése
	case 18: {
			mysqli_query($link, "UPDATE statuses SET status = 9 WHERE status != 9 AND (instruction like '%V5%' OR instruction like '%V6%' OR instruction like '%V7%' OR instruction like '%V8%' OR instruction like '%_kezi')");
			mysqli_query($link, "UPDATE instructions SET state = 2 WHERE state in (0,1,3) AND (instruction = 10 OR (instruction >= 38 AND instruction <= 45))");
			ob_clean();
			echo 0;
		}
		break;

	 //// összes P3 folyamatban lévő kézi utasítások törlése
	case 19: {
			mysqli_query($link, "UPDATE statuses SET status = 9 WHERE status != 9 AND (instruction like '%KSZ%' OR instruction like '%_kezi')");
			mysqli_query($link, "UPDATE instructions SET state = 2 WHERE state in (0,1,3) AND instruction = 10 OR (instruction IN (46, 47))");
			ob_clean();
			echo 0;
		}
		break;


	case 20: {
//error_reporting(E_ERROR | E_WARNING);
			$p1id = mysqli_query($link, 'SELECT id FROM devices WHERE name = \'p1\' LIMIT 0,1');
			$p1id = mysqli_fetch_assoc($p1id)['id'];
			mysqli_query($link, "INSERT INTO statuses VALUES ('', 'p1_uzemallapot', NOW(), 1,0)");
			$statuses_id = mysqli_query($link, 'SELECT id FROM statuses WHERE instruction = \'p1_uzemallapot\' ORDER BY id DESC LIMIT 0,1');
			$statuses_id = mysqli_fetch_assoc($statuses_id)['id'];
			mysqli_query($link, "INSERT INTO instructions VALUES ('', " . $statuses_id . ", " . $p1id . ", 48, 1, null, NOW(), 0)");
			echo 1;
		}
		break;

	case 21: {
			$p1id = mysqli_query($link, 'SELECT id FROM devices WHERE name = \'p2\' LIMIT 0,1');
			$p1id = mysqli_fetch_assoc($p1id)['id'];
			mysqli_query($link, "INSERT INTO statuses VALUES ('', 'p2_uzemallapot', NOW(), 1,0)");
			$statuses_id = mysqli_query($link, 'SELECT id FROM statuses WHERE instruction = \'p2_uzemallapot\' ORDER BY id DESC LIMIT 0,1');
			$statuses_id = mysqli_fetch_assoc($statuses_id)['id'];
			mysqli_query($link, "INSERT INTO instructions VALUES ('', " . $statuses_id . ", " . $p1id . ", 49, 1, null, NOW(), 0)");
			echo 1;
		}
		break;

	case 22: {
			mysqli_query($link, "UPDATE statuses SET status = 9 WHERE status != 9 AND instruction = 'p1_uzemallapot' ");
			mysqli_query($link, "UPDATE instructions SET state = 2, ready = ".time()." WHERE state = 1 AND instruction = 48 ");
			$p1id = mysqli_query($link, 'SELECT id FROM devices WHERE name = \'p1\' LIMIT 0,1');
			$p1id = mysqli_fetch_assoc($p1id)['id'];
			$statuses_id = mysqli_query($link, 'SELECT id FROM statuses WHERE instruction = \'p1_uzemallapot\' ORDER BY id DESC LIMIT 0,1');
			$statuses_id = mysqli_fetch_assoc($statuses_id)['id'];
			mysqli_query($link, "INSERT INTO instructions VALUES ('', " . $statuses_id . ", " . $p1id . ", 50, 2, null, NOW(), ".time().")");
			echo 1;
		}
		break;

	case 23: {
			mysqli_query($link, "UPDATE statuses SET status = 9 WHERE status != 9 AND instruction = 'p2_uzemallapot' ");
			mysqli_query($link, "UPDATE instructions SET state = 2, ready = ".time()." WHERE state = 1 AND instruction = 49 ");
			$p1id = mysqli_query($link, 'SELECT id FROM devices WHERE name = \'p2\' LIMIT 0,1');
			$p1id = mysqli_fetch_assoc($p1id)['id'];
			$statuses_id = mysqli_query($link, 'SELECT id FROM statuses WHERE instruction = \'p2_uzemallapot\' ORDER BY id DESC LIMIT 0,1');
			$statuses_id = mysqli_fetch_assoc($statuses_id)['id'];
			mysqli_query($link, "INSERT INTO instructions VALUES ('', " . $statuses_id . ", " . $p1id . ", 51, 2, null, NOW(), ".time().")");
			echo 1;
		}
		break;

	case 24: {
			$error = urlencode($_POST['error']);
			mysqli_query($link, "INSERT INTO errors VALUES ('', '".$error."', NOW())");
			echo 1;
		}
		break;


	default: {
			// /***LOG*/ mysqli_query($link, "insert into logs values('','".$lekerdezes_["id"]."','Ismeretlen parancs: ".$action."',NOW())");
			ob_clean();
			echo -11110;
		}
		break;
}


mysqli_close($link);
