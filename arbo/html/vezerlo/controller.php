<?php

require_once('/var/www/html/config/Config.php');
use Config\Config;

class VezerloController {

	const ZOLD = 1;
	const SARGA = 2;
	const PIROS = 3;
	const SZURKE = 0;

	public static function p1Savazas($uid) {

		#p1 savazás:
		/*
			zöld, ha nincs a statuses táblába olyan rekord aminek a statusa nem 9, és a devices táblában a p1 és a p2 sorban a closed értéke 0
			szürke és nem kattintható, ellenkező esetben
		*/
		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");

		$ready = mysqli_query($link, 'SELECT COUNT(id) AS id from statuses WHERE status != 9');
		$ready = mysqli_fetch_assoc($ready)['id'];
		if (intval($ready) > 0) {
			return 0;
		}

		$eszkozok = mysqli_query($link, "select * from devices");
		while($eszkoz = mysqli_fetch_assoc($eszkozok)) {
			$eszkozok_[] = $eszkoz;
		}
		$p1_index = array_search('p1', array_column($eszkozok_, 'name'));
		$p2_index = array_search('p2', array_column($eszkozok_, 'name'));
		$p3_index = array_search('p3', array_column($eszkozok_, 'name'));
		#tranzakció létrehozása
		mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
		#automata kikapcsolása
		mysqli_query($link, "update devices set closed='1' where name='p3'");
		#feladat rekord létrehozása
		mysqli_query($link, "insert into statuses values('','p1_savazas',NOW(),'0',".$uid.")");
		$statuses_id = mysqli_insert_id($link);
		#utasítás rekordok létrehozása
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','2','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','4','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','4','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','1','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','2','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','1','0',NULL,NOW(),NULL)");
		#tranzakció mentése
		$r = mysqli_commit($link);
		mysqli_close($link);
		return $r;
	}
	

	public static function p1Meres($uid) {

		#p1 mérés:
		/*
			zöld, ha nincs a statuses táblába olyan rekord aminek a statusa nem 9, és a devices táblában a p1 és a p2 sorban a closed értéke 0
			szürke és nem kattintható, ellenkező esetben
		*/
		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");

		$ready = mysqli_query($link, 'SELECT COUNT(id) AS id from statuses WHERE status != 9');
		$ready = mysqli_fetch_assoc($ready)['id'];
		if (intval($ready) > 0) {
			return 0;
		}

		$eszkozok = mysqli_query($link, "select * from devices");
		while($eszkoz = mysqli_fetch_assoc($eszkozok)) {
			$eszkozok_[] = $eszkoz;
		}
		$p1_index = array_search('p1', array_column($eszkozok_, 'name'));
		$p2_index = array_search('p2', array_column($eszkozok_, 'name'));
		$p3_index = array_search('p3', array_column($eszkozok_, 'name'));
		#tranzakció létrehozása
		mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
		#automata kikapcsolása
		mysqli_query($link, "update devices set closed='1' where name='p3'");
		#feladat rekord létrehozása
		mysqli_query($link, "insert into statuses values('','p1_meres',NOW(),'0',".$uid.")");
		$statuses_id = mysqli_insert_id($link);
		#utasítás rekordok létrehozása
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','1','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','2','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','3','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','1','0',NULL,NOW(),NULL)");
		#tranzakció mentése
		$r = mysqli_commit($link);
		mysqli_close($link);
		return $r;
	}


	public static function p1Reset($uid) {

		#p1 reset:
		/*
			sárga a gomb
		*/
		if (! file_exists('/home/pi/arbo/reset/rebootP1')) {
			file_put_contents('/home/pi/arbo/reset/rebootP1','1');
		}

		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");

		$eszkozok = mysqli_query($link, "select * from devices");
		while($eszkoz = mysqli_fetch_assoc($eszkozok)) {
			$eszkozok_[] = $eszkoz;
		}
		$p1_index = array_search('p1', array_column($eszkozok_, 'name'));
		$p2_index = array_search('p2', array_column($eszkozok_, 'name'));
		$p3_index = array_search('p3', array_column($eszkozok_, 'name'));
		$feladat = mysqli_query($link, "select * from statuses where status<>'9'");
		$feladat_ = mysqli_fetch_assoc($feladat);
		#tranzakció létrehozása
		mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
		#automata kikapcsolása
		mysqli_query($link, "update devices set closed='1' where name='p3'");
		#félbehagyott utasítás visszavonása
		// mysqli_query($link, "delete from statuses where id='".$feladat_["id"]."'");
		mysqli_query($link, "delete from instructions where statuses_id='".$feladat_["id"]."'");
		#feladat rekord létrehozása
		mysqli_query($link, "insert into statuses values('','p1_reset',NOW(),'0',".$uid.")");
		$statuses_id = mysqli_insert_id($link);
		#utasítás rekordok létrehozása
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','5','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','1','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','1','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','1','0',NULL,NOW(),NULL)");
		#tranzakció mentése
		$r = mysqli_commit($link);
		mysqli_close($link);
		return $r;
	}


	public static function p1Lezaras($uid) {

		#p1 lezárás:
		/*
			self::PIROS, ha a devices táblában a p1 sorában a closed mezőben 0 szerepel
			szürke, és nem kattintható, ha a p1 sorában a closed mezőben nem 0 szerepel
		*/
		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");

		$ready = mysqli_query($link, 'SELECT COUNT(id) AS id from statuses WHERE status != 9');
		$ready = mysqli_fetch_assoc($ready)['id'];
		if (intval($ready) > 0) {
			return 0;
		}

		$eszkozok = mysqli_query($link, "select * from devices");
		while($eszkoz = mysqli_fetch_assoc($eszkozok)) {
			$eszkozok_[] = $eszkoz;
		}
		$p1_index = array_search('p1', array_column($eszkozok_, 'name'));
		$p2_index = array_search('p2', array_column($eszkozok_, 'name'));
		$p3_index = array_search('p3', array_column($eszkozok_, 'name'));
		$feladat = mysqli_query($link, "select * from statuses where status<>'9'");
		$feladat_ = mysqli_fetch_assoc($feladat);
		#tranzakció létrehozása
		mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
		#automata kikapcsolása
		mysqli_query($link, "update devices set closed='1' where name='p3'");
		#p1 lezárása
		mysqli_query($link, "update devices set closed='1' where name='p1'");
		#félbehagyott utasítás visszavonása
		mysqli_query($link, "delete from statuses where id='".$feladat_["id"]."'");
		mysqli_query($link, "delete from instructions where statuses_id='".$feladat_["id"]."'");
		#feladat rekord létrehozása
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','1','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','1','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','2','0',NULL,NOW(),NULL)");
		#tranzakció mentése
		$r = mysqli_commit($link);
		mysqli_close($link);
		return $r;
	}


	public static function p2Savazas($uid) {
		#p2 savazás:
		/*
			zöld, ha nincs a statuses táblába olyan rekord aminek a statusa nem 9, és a devices táblában a p1 és a p2 sorban a closed értéke 0
			szürke és nem kattintható, ellenkező esetben
		*/
		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");

		$ready = mysqli_query($link, 'SELECT COUNT(id) AS id from statuses WHERE status != 9');
		$ready = mysqli_fetch_assoc($ready)['id'];
		if (intval($ready) > 0) {
			return 0;
		}

		$eszkozok = mysqli_query($link, "select * from devices");
		while($eszkoz = mysqli_fetch_assoc($eszkozok)) {
			$eszkozok_[] = $eszkoz;
		}
		$p1_index = array_search('p1', array_column($eszkozok_, 'name'));
		$p2_index = array_search('p2', array_column($eszkozok_, 'name'));
		$p3_index = array_search('p3', array_column($eszkozok_, 'name'));
		#tranzakció létrehozása
		mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
		#automata kikapcsolása
		mysqli_query($link, "update devices set closed='1' where name='p3'");
		#feladat rekord létrehozása
		mysqli_query($link, "insert into statuses values('','p2_savazas',NOW(),'0',".$uid.")");
		$statuses_id = mysqli_insert_id($link);
		#utasítás rekordok létrehozása
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','2','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','4','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','4','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','1','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','2','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','1','0',NULL,NOW(),NULL)");
		#tranzakció mentése
		$r = mysqli_commit($link);
		mysqli_close($link);
		return $r;
	}


	public static function p2Meres($uid) {

		#p2 mérés:
		/*
			zöld, ha nincs a statuses táblába olyan rekord aminek a statusa nem 9, és a devices táblában a p1 és a p2 sorban a closed értéke 0
			szürke és nem kattintható, ellenkező esetben
		*/
		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");

		$ready = mysqli_query($link, 'SELECT COUNT(id) AS id from statuses WHERE status != 9');
		$ready = mysqli_fetch_assoc($ready)['id'];
		if (intval($ready) > 0) {
			return 0;
		}

		$eszkozok = mysqli_query($link, "select * from devices");
		while($eszkoz = mysqli_fetch_assoc($eszkozok)) {
			$eszkozok_[] = $eszkoz;
		}
		$p1_index = array_search('p1', array_column($eszkozok_, 'name'));
		$p2_index = array_search('p2', array_column($eszkozok_, 'name'));
		$p3_index = array_search('p3', array_column($eszkozok_, 'name'));
		#tranzakció létrehozása
		mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
		#automata kikapcsolása
		mysqli_query($link, "update devices set closed='1' where name='p3'");
		#feladat rekord létrehozása
		mysqli_query($link, "insert into statuses values('','p2_meres',NOW(),'0',".$uid.")");
		$statuses_id = mysqli_insert_id($link);
		#utasítás rekordok létrehozása
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','1','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','2','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','3','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','1','0',NULL,NOW(),NULL)");
		#tranzakció mentése
		$r = mysqli_commit($link);
		mysqli_close($link);
		return $r;
	}


	public static function p2Reset($uid) {

		#p2 reset:
		/*
			sárga a gomb
		*/
		if (! file_exists('/home/pi/arbo/reset/rebootP2')) {
			file_put_contents('/home/pi/arbo/reset/rebootP2','1');
		}
		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");

		$eszkozok = mysqli_query($link, "select * from devices");
		while($eszkoz = mysqli_fetch_assoc($eszkozok)) {
			$eszkozok_[] = $eszkoz;
		}
		$p1_index = array_search('p1', array_column($eszkozok_, 'name'));
		$p2_index = array_search('p2', array_column($eszkozok_, 'name'));
		$p3_index = array_search('p3', array_column($eszkozok_, 'name'));
		$feladat = mysqli_query($link, "select * from statuses where status<>'9'");
		$feladat_ = mysqli_fetch_assoc($feladat);
		#tranzakció létrehozása
		mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
		#automata kikapcsolása
		mysqli_query($link, "update devices set closed='1' where name='p3'");
		#félbehagyott utasítás visszavonása
		// mysqli_query($link, "delete from statuses where id='".$feladat_["id"]."'"); // ne törölje, a log-ba legyen benne
		mysqli_query($link, "delete from instructions where statuses_id='".$feladat_["id"]."'");
		#feladat rekord létrehozása
		mysqli_query($link, "insert into statuses values('','p2_reset',NOW(),'0',".$uid.")");
		$statuses_id = mysqli_insert_id($link);
		#utasítás rekordok létrehozása
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','6','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','1','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','1','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','1','0',NULL,NOW(),NULL)");
		#tranzakció mentése
		$r = mysqli_commit($link);
		mysqli_close($link);
		return $r;
	}


	public static function p2Lezaras($uid) {

		#p2 lezárás:
		/*
			self::PIROS, ha a devices táblában a p2 sorában a closed mezőben 0 szerepel
			szürke, és nem kattintható, ha a p2 sorában a closed mezőben nem 0 szerepel
		*/
		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");

		$ready = mysqli_query($link, 'SELECT COUNT(id) AS id from statuses WHERE status != 9');
		$ready = mysqli_fetch_assoc($ready)['id'];
		if (intval($ready) > 0) {
			return 0;
		}

		$eszkozok = mysqli_query($link, "select * from devices");
		while($eszkoz = mysqli_fetch_assoc($eszkozok)) {
			$eszkozok_[] = $eszkoz;
		}
		$p1_index = array_search('p1', array_column($eszkozok_, 'name'));
		$p2_index = array_search('p2', array_column($eszkozok_, 'name'));
		$p3_index = array_search('p3', array_column($eszkozok_, 'name'));
		$feladat = mysqli_query($link, "select * from statuses where status<>'9'");
		$feladat_ = mysqli_fetch_assoc($feladat);
		#tranzakció létrehozása
		mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
		#automata kikapcsolása
		mysqli_query($link, "update devices set closed='1' where name='p3'");
		#p2 lezárása
		mysqli_query($link, "update devices set closed='1' where name='p2'");
		#félbehagyott utasítás visszavonása
		mysqli_query($link, "delete from statuses where id='".$feladat_["id"]."'");
		mysqli_query($link, "delete from instructions where statuses_id='".$feladat_["id"]."'");
		#feladat rekord létrehozása
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','1','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','1','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','2','0',NULL,NOW(),NULL)");
		#tranzakció mentése
		$r = mysqli_commit($link);
		mysqli_close($link);
		return $r;
	}


	public static function kenyszerUzemallapot($uid) {

		#kényszer üzemállapot
		/*
			self::PIROS, ha van a statuses táblába olyan rekord aminek a statusa nem 9, vagy a devices táblában a p1 vagy a p2 sorában a closed értéke nem 0
			szürke és nem kattintható, ellenkező esetben
		*/
		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");

		$ready = mysqli_query($link, 'SELECT COUNT(id) AS id from statuses WHERE status != 9');
		$ready = mysqli_fetch_assoc($ready)['id'];
		if (intval($ready) > 0) {
			return 0;
		}

		$eszkozok = mysqli_query($link, "select * from devices");
		while($eszkoz = mysqli_fetch_assoc($eszkozok)) {
			$eszkozok_[] = $eszkoz;
		}
		$p1_index = array_search('p1', array_column($eszkozok_, 'name'));
		$p2_index = array_search('p2', array_column($eszkozok_, 'name'));
		$p3_index = array_search('p3', array_column($eszkozok_, 'name'));
		$feladat = mysqli_query($link, "select * from statuses where status<>'9'");
		$feladat_ = mysqli_fetch_assoc($feladat);
		#tranzakció létrehozása
		mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
		#automata kikapcsolása
		mysqli_query($link, "update devices set closed='1' where name='p3'");
		#lezárások feloldása
		mysqli_query($link, "update devices set closed='0' where name='p1'");
		mysqli_query($link, "update devices set closed='0' where name='p2'");
		#félbehagyott utasítás visszavonása
		mysqli_query($link, "delete from statuses where id='".$feladat_["id"]."'");
		mysqli_query($link, "delete from instructions where statuses_id='".$feladat_["id"]."'");
		#feladat rekord létrehozása
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','1','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','1','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','1','0',NULL,NOW(),NULL)");
		#tranzakció mentése
		$r = mysqli_commit($link);
		mysqli_close($link);
		return $r;
	}


	public static function automataVezerlesBekapcsolasa($uid) {

		#automata vezérlés bekapcsolása
		/*
			zöld a gomb, és automata vezérlés bekapcsolása szöveg szerepel rajta, ha a devices táblába a p3 rekordjában 1 szerepel a closed mezőbe
		*/
		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");

		$ready = mysqli_query($link, 'SELECT COUNT(id) AS id from statuses WHERE status != 9');
		$ready = mysqli_fetch_assoc($ready)['id'];
		if (intval($ready) > 0) {
			return 0;
		}


		$eszkozok = mysqli_query($link, "select * from devices");
		while($eszkoz = mysqli_fetch_assoc($eszkozok)) {
			$eszkozok_[] = $eszkoz;
		}
		$p1_index = array_search('p1', array_column($eszkozok_, 'name'));
		$p2_index = array_search('p2', array_column($eszkozok_, 'name'));
		$p3_index = array_search('p3', array_column($eszkozok_, 'name'));
		$feladat = mysqli_query($link, "select * from statuses where status<>'9'");
		$feladat_ = mysqli_fetch_assoc($feladat);
		#tranzakció létrehozása
		mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
		#lezárások feloldása
		mysqli_query($link, "update devices set closed='0' where name='p1'");
		mysqli_query($link, "update devices set closed='0' where name='p2'");
		#félbehagyott utasítás visszavonása
		mysqli_query($link, "delete from statuses where id='".$feladat_["id"]."'");
		mysqli_query($link, "delete from instructions where statuses_id='".$feladat_["id"]."'");
		#feladat rekord létrehozása
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','1','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','1','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','1','0',NULL,NOW(),NULL)");
		#automata bekapcsolása
		mysqli_query($link, "update devices set closed='0' where name='p3'");
		#tranzakció mentése
		$r = mysqli_commit($link);
		mysqli_close($link);
		return $r;
	}


	public static function automataVezerlesKikapcsolasa($uid) {

		#automata vezérlés kikapcsolása
		/*
			sárga a gomb, és automata vezérlés kikapcsolás szöveg szerepel rajta, ha a devices táblába a p3 rekordjában 0 szerepel a closed mezőbe
		*/
		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");

		$eszkozok = mysqli_query($link, "select * from devices");
		while($eszkoz = mysqli_fetch_assoc($eszkozok)) {
			$eszkozok_[] = $eszkoz;
		}
		$p1_index = array_search('p1', array_column($eszkozok_, 'name'));
		$p2_index = array_search('p2', array_column($eszkozok_, 'name'));
		$p3_index = array_search('p3', array_column($eszkozok_, 'name'));
		$feladat = mysqli_query($link, "select * from statuses where status<>'9'");
		$feladat_ = mysqli_fetch_assoc($feladat);
		#tranzakció létrehozása
		mysqli_begin_transaction($link, MYSQLI_TRANS_START_READ_WRITE);
		#automata kikapcsolása
		mysqli_query($link, "update devices set closed='1' where name='p3'");
		#félbehagyott utasítás visszavonása
		mysqli_query($link, "delete from statuses where id='".$feladat_["id"]."'");
		mysqli_query($link, "delete from instructions where statuses_id='".$feladat_["id"]."'");
		#feladat rekord létrehozása
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p3_index]['id']."','1','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p1_index]['id']."','1','0',NULL,NOW(),NULL)");
		mysqli_query($link, "insert into instructions values('','$statuses_id','".$eszkozok_[$p2_index]['id']."','1','0',NULL,NOW(),NULL)");
		#tranzakció mentése
		$r = mysqli_commit($link);
		mysqli_close($link);
		return $r;
	}

	public static function keziUzemmodBe($uid) {
		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");

		$ready = mysqli_query($link, 'SELECT COUNT(id) AS id from statuses WHERE status != 9');
		$ready = mysqli_fetch_assoc($ready)['id'];
		if (intval($ready) > 0) {
			return 0;
		}

		$p3id = mysqli_query($link, 'SELECT id FROM devices WHERE name = \'p3\' LIMIT 0,1');
		$p3id = mysqli_fetch_assoc($p3id)['id'];

		mysqli_query($link, 'INSERT INTO statuses VALUES (\'\', \'p3_kezi\', NOW(), 1,'.$uid.')');
		$r = mysqli_commit($link);
		if (!$r) {
			mysqli_close($link);
			return $r;
		}

		$statusid = mysqli_query($link, 'SELECT id FROM statuses WHERE instruction = \'p3_kezi\' AND status = 1 ORDER BY id DESC LIMIT 0,1');
		$statusid = mysqli_fetch_assoc($statusid)['id'];

		mysqli_query($link, 'insert into instructions values(\'\',' . $statusid . ',' . $p3id .', 10, 0, NULL, NOW(), NULL)');
		$r = mysqli_commit($link);

		mysqli_close($link);
		return $r;
	}

	public static function keziUzemmodKi($uid) {
		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");

//		MYSQL version
//		mysqli_query($link, 'UPDATE statuses SET status = 9 WHERE id IN (SELECT S.id FROM statuses S WHERE S.instruction = \'p3_kezi\' AND S.status != 9 ORDER BY S.id DESC LIMIT 0,1)');

//		MariaDB version
		mysqli_query($link, 'UPDATE statuses SET status = 9 WHERE id IN (SELECT S2.id FROM (SELECT S.id FROM statuses AS S WHERE S.instruction = \'p3_kezi\' AND S.status != 9 ORDER BY S.id DESC LIMIT 0,1) AS S2)');
		$r = mysqli_commit($link);
		if (!$r) {
			mysqli_close($link);
			return $r;
		}
		
		$statusid = mysqli_query($link, 'SELECT id FROM statuses WHERE instruction = \'p3_kezi\' AND status = 9 ORDER BY id DESC LIMIT 0,1');
		$statusid = mysqli_fetch_assoc($statusid)['id'];
		
		mysqli_query($link, 'UPDATE instructions SET ready = '.time().', state = 2 WHERE statuses_id = ' . $statusid);
		$r = mysqli_commit($link);

		mysqli_close($link);
		return $r;
	}


	public static function keziUtasitas($uid) {
		$data = file_get_contents('php://input');
		$data = json_decode($data, true);

		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");
		
		$receiver = $data['receiver'];
		$instructionid = $data['instruction']['id'];
		$instructionname = $data['instruction']['name'];
		
		$pid = mysqli_query($link, 'SELECT id FROM devices WHERE name = \'' . $receiver . '\' LIMIT 0,1');
		$pid = mysqli_fetch_assoc($pid)['id'];

		// csak akkor szúrható be, ha nincs folyamatban lévő utasítás
		$q1 = mysqli_query($link, 'SELECT id FROM statuses WHERE status != 9 AND instruction != \'p3_kezi\' LIMIT 0,1');
		if (mysqli_num_rows($q1) != 0) {
			return 0;
		}

		mysqli_query($link, 'INSERT INTO statuses VALUES(\'\',\''. $instructionname . '\', NOW(), 1,'.$uid.')');
		$r = mysqli_commit($link);
		if (!$r) {
			mysqli_close($link);
			return $r;
		}

		$statusid = mysqli_query($link, 'SELECT id FROM statuses WHERE status = 1 AND instruction = \'' . $instructionname . '\' ORDER BY id DESC LIMIT 0,1');
		$statusid = mysqli_fetch_assoc($statusid)['id'];

		mysqli_query($link, 'INSERT INTO instructions VALUES(\'\',' . $statusid . ',' . $pid . ',' . $instructionid . ', 1, NULL, NOW(), NULL)');
		$r = mysqli_commit($link);
		
		return $r;
	}


	public static function vezerlesStatus($uid) {
	
		$status = [];

		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");

		$q1 = mysqli_query($link, "SELECT id, instruction FROM statuses WHERE status != 9 LIMIT 0,1");
		$q2 = null;
		$q3 = null;
		if (mysqli_num_rows($q1) == 0) {
			$q2 = mysqli_query($link, "SELECT id FROM devices WHERE name = 'p1' AND closed = 0 LIMIT 0,1");
			if (mysqli_num_rows($q2) == 1) {
				$q3 = mysqli_query($link, "SELECT id FROM devices WHERE name = 'p2' AND closed = 0 LIMIT 0,1");
			}
		}
		
		$q1Fetch = mysqli_fetch_assoc($q1);
		if ($q1Fetch['instruction'] != 'p3_kezi') {
			unset($q1Fetch);
			$status['KeziVezerles'] = self::ZOLD;

			if (mysqli_num_rows($q1) == 0 && ($q2 != null && mysqli_num_rows($q2) == 1) && ($q3 != null && mysqli_num_rows($q3) == 1)) {
				$status['p1']['Savazas'] = self::ZOLD;
				$status['p1']['Meres'] = self::ZOLD;
				$status['p2']['Savazas'] = self::ZOLD;
				$status['p2']['Meres'] = self::ZOLD;
			} else {
				$status['p1']['Savazas'] = self::SZURKE;
				$status['p1']['Meres'] = self::SZURKE;
				$status['p2']['Savazas'] = self::SZURKE;
				$status['p2']['Meres'] = self::SZURKE;
			}
			unset($q1,$q2,$q3);

			if (! file_exists('/home/pi/arbo/reset/p1')) {
				$status['p1']['Reset'] = self::SARGA;
			} else {
				$status['p1']['Reset'] = self::SZURKE;
			}

			if (! file_exists('/home/pi/arbo/reset/p2')) {
				$status['p2']['Reset'] = self::SARGA;
			} else {
				$status['p2']['Reset'] = self::SZURKE;
			}

			$q2 = mysqli_query($link, "SELECT id FROM devices WHERE name = 'p1' AND closed = 0 LIMIT 0,1");
			if (mysqli_num_rows($q2) == 1) {
				$status['p1']['Lezaras'] = self::PIROS;
			} else {
				$status['p1']['Lezaras'] = self::SZURKE;
			}
			unset($q2);


			$q2 = mysqli_query($link, "SELECT id FROM devices WHERE name = 'p2' AND closed = 0 LIMIT 0,1");
			if (mysqli_num_rows($q2) == 1) {
				$status['p2']['Lezaras'] = self::PIROS;
			} else {
				$status['p2']['Lezaras'] = self::SZURKE;
			}
			unset($q2);

			$q1 = mysqli_query($link, "SELECT id FROM statuses WHERE status != 9 LIMIT 0,1");
			$q2 = null;
			if (mysqli_num_rows($q1) == 0) {
				$q2 = mysqli_query($link, "SELECT id FROM devices WHERE name IN ('p1','p2') AND closed != 0 LIMIT 0,1");
			}
			if (mysqli_num_rows($q1) == 1 || ($q2 != null && mysqli_num_rows($q2) == 1) )  {
				$status['KenyszerUzemallapot'] = self::PIROS;
			} else {
				$status['KenyszerUzemallapot'] = self::SZURKE;
			}
			unset($q1,$q2);

			$q2 = mysqli_query($link, "SELECT id FROM devices WHERE name = 'p3' AND closed = 1 LIMIT 0,1");
			if (mysqli_num_rows($q2) == 1) {
				$status['AutomataVezerles'] = self::ZOLD;
			} else {
				$status['AutomataVezerles'] = self::SARGA;
			}
			unset($q2);
		} else {
			unset($q1Fetch);
			$status['KeziVezerles'] = self::SARGA;
			$status['p1']['Savazas'] = self::SZURKE;
			$status['p1']['Meres'] = self::SZURKE;
			$status['p1']['Reset'] = self::SZURKE;
			$status['p1']['Lezaras'] = self::SZURKE;
			$status['p2']['Savazas'] = self::SZURKE;
			$status['p2']['Meres'] = self::SZURKE;
			$status['p2']['Reset'] = self::SZURKE;
			$status['p2']['Lezaras'] = self::SZURKE;
			$status['AutomataVezerles'] = self::SZURKE;
		}

		$status['checksum'] = md5(serialize($status));

		mysqli_close($link);
		return $status;
	}

//	public static function maintenance() {
//		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
//		$time = date('Y-m-d H:i:s', strtotime("-7 day", time() ));
//		$q = mysqli_query($link, 'SELECT id FROM measurements WHERE date < \'' . $time . '\' ORDER BY id DESC LIMIT 0,1');
//		if (mysqli_num_rows($q) == 0) {
//			return;
//		}
//		$qf = mysqli_fetch_assoc($q);
//		$min = $qf['id'];
//		mysqli_query($link, 'DELETE FROM measurements WHERE id < ' . $min);
//		mysqli_commit($link);
//		mysqli_close($link);
//	}

}
