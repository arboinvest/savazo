<?php

require_once('/var/www/html/config/Config.php');
use Config\Config;

class UserMngtController {

	public static function listUsers() {
		$data = '';
		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");

		$users = mysqli_query($link, "SELECT CONCAT(name,' (',fullname,')') AS name FROM users WHERE inactive = 0");
		while ($user = mysqli_fetch_array($users)) {
			if ($data != '') {
				$data = $data . ',' . $user['name'];
			} else {
				$data = $data . $user['name'];
			}
		}
		mysqli_close($link);
		return $data;
	}



	public static function newUser($name, $fullname, $pass) {

		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");
		
		$stmt = $link->prepare("SELECT COUNT(1) AS cnt FROM users WHERE name = ? ");
		$stmt->bind_param('s', $name);
		$stmt->execute();

		$result = $stmt->get_result();
		$row = $result->fetch_assoc();

		if ( (int)$row['cnt'] > 0) {
			return 'Már van ilyen felhasználó!';
		}

		$pre_key = Config::PASSWD_KEY1;
		$post_key = Config::PASSWD_KEY2;
		$md5pass = md5($pre_key . $pass . $post_key);
		
		$stmt = $link->prepare("INSERT INTO users VALUES('', ?, ?, 0, ?, 0)");
		$stmt->bind_param('sss', $name, $md5pass, $fullname);
		$stmt->execute();
		
		mysqli_close($link);
		return '1';
	}


	public static function iaUser($name) {
		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");
		
		$stmt = $link->prepare("SELECT COUNT(1) AS cnt FROM users WHERE name = ? ");
		
		$stmt->bind_param('s', $name);
		$stmt->execute();
		
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();

		if ( (int)$row['cnt'] == 0) {
			return 'Nincs ilyen felhasználó!';
		}
		
		$stmt = $link->prepare("UPDATE users SET inactive = 1 WHERE name = ?");
		$stmt->bind_param('s', $name);
		$stmt->execute();
		
		mysqli_close($link);
		return '1';
	}

	public static function passReplUser($name, $pass) {
		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");
		
		$stmt = $link->prepare("SELECT COUNT(1) AS cnt FROM users WHERE name = ? ");
		
		$stmt->bind_param('s', $name);
		$stmt->execute();
		
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();

		if ( (int)$row['cnt'] == 0) {
			return 'Nincs ilyen felhasználó!';
		}

		$pre_key = Config::PASSWD_KEY1;
		$post_key = Config::PASSWD_KEY2;

		$md5pass = md5($pre_key . $pass . $post_key);
		
		$stmt = $link->prepare("UPDATE users SET password = ? WHERE name = ?");
		$stmt->bind_param('ss', $md5pass, $name);
		$stmt->execute();
		
		mysqli_close($link);
		return '1';
	}

	public static function passReplByOldPass($uid, $oldpass, $newpass) {
		$link = mysqli_connect(Config::MYSQL_HOST, Config::MYSQL_USER, Config::MYSQL_PASSWORD, Config::MYSQL_DB);
		mysqli_set_charset($link, "utf8");
		
		$pre_key = Config::PASSWD_KEY1;
		$post_key = Config::PASSWD_KEY2;

		$md5pass = md5($pre_key . $oldpass . $post_key);

		$stmt = $link->prepare("SELECT COUNT(1) AS cnt FROM users WHERE id = ? AND UPPER(password) = UPPER(?) ");
		
		$stmt->bind_param('is', $uid, $md5pass);
		$stmt->execute();
		
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();

		if ( (int)$row['cnt'] == 0) {
			return 'Nincs ilyen felhasználó!';
		}

		$md5pass = md5($pre_key . $newpass . $post_key);
		
		$stmt = $link->prepare("UPDATE users SET password = ? WHERE id = ?");
		$stmt->bind_param('si', $md5pass, $uid);
		$stmt->execute();
		
		mysqli_close($link);
		return '1';
	}



	public static function run($pars) {
		$cmd = (int)$pars['cmd'];
		switch ($cmd) {
			case 0:
				ob_clean();
				echo \UserMngtController::listUsers();
				break;
			case 1:
				$name = $pars['name'];
				$fullname = $pars['fullname'];
				$pass = $pars['pass'];
				$rs = \UserMngtController::newUser($name, $fullname, $pass);
				ob_clean();
				echo $rs;
				break;
			case 2:
				$name = $pars['name'];
				$rs = UserMngtController::iaUser($name);
				ob_clean();
				echo $rs;
				break;
			case 3:
				$name = $pars['name'];
				$pass = $pars['pass'];
				$rs = UserMngtController::passReplUser($name, $pass);
				ob_clean();
				echo $rs;
				break;
			case 4:
				$uid = intval($pars['uid']);
				$oldpass = $pars['oldpass'];
				$newpass = $pars['newpass'];
				$rs = UserMngtController::passReplByOldPass($uid, $oldpass, $newpass);
				ob_clean();
				echo $rs;
				break;
		}

	}

}

\UserMngtController::run($_POST);

