<?php
namespace Config;
class Config {
	// 1. dashboard
	// 1. server ip address & port
	const SERVER_URL = '***.***.***.***:****';
	const CONTROL_PANEL_TITLE = "Savazó rendszer vezérlő I.";
	
	const MYSQL_HOST = 'localhost';
	const MYSQL_USER = 'root';
	const MYSQL_PASSWORD = 'mypass';
	const MYSQL_DB = 'arbo';
	const LOGIN_TIMEOUT = 31536000;

	// P4 web server ip address & port
	const NYOF_URL = 'http://***.***.***.***:**'; // ha üres sztring, akkor elrejti a html elemeket

	const PASSWD_KEY1 = 'Zxn0Dg'; // a jelszó hash képzéshez használt kulcsok, a jelszó hash:  md5( PASSWD_KEY1 + <jelszo> + PASSWD_KEY2 )
	const PASSWD_KEY2 = '1DQftY';
}
