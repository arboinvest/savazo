<?php

require_once('/var/www/html/config/Config.php');
use Config\Config;


class LoginController {

	private static $nevek = [];

	public static function ellenorzes($name,$pass) {
		
		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");
		
		$stmt = $link->prepare('SELECT * FROM users WHERE name=? AND password=? AND inactive=0 LIMIT 0,1');

		$pre_key = Config::PASSWD_KEY1;
		$post_key = Config::PASSWD_KEY2;

		$md5pass = md5($pre_key . $pass . $post_key);
		$stmt->bind_param('ss', $name, $md5pass);
		$stmt->execute();
		
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();

		LoginController::nevekBetoltese($link);

		mysqli_close($link);

		ini_set("session.gc_maxlifetime", Config::LOGIN_TIMEOUT);
		session_start();
		if ($row) {
			$_SESSION['login'] = intval($row['id']);
			$_SESSION['type'] = intval($row['type']);
			return true;
		}
		
		$_SESSION['login'] = 0;
		return false;
	}
	
	public static function kilepes() {
		ini_set("session.gc_maxlifetime", Config::LOGIN_TIMEOUT);
		session_start();
		$_SESSION['login'] = 0;
	}

	public static function getNevek() { 
		if (count(LoginController::$nevek) > 0) {
			return LoginController::$nevek;
		}
		
		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");

		LoginController::nevekBetoltese($link);
		mysqli_close($link);
		return LoginController::$nevek;
	}

	public static function getNevekAsStream() {
		$nevek = LoginController::getNevek();
		$stream = '';
		foreach ($nevek as $key => $value) {
			if ($stream != '') {
				$stream = $stream . ',' . $key . ':' . $value;
			} else {
				$stream = $stream . $key . ':' . $value;
			}
		}
		return $stream;
	}


	public static function nevekBetoltese($link) {
		$users = mysqli_query($link, 'SELECT id,fullname FROM users');
		while ($user = mysqli_fetch_array($users)) {
			LoginController::$nevek[(int)$user['id']] = $user['fullname'];
		}
	}

}
