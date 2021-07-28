<?php
namespace Config;
class Config {
	// 1. dashboard
	const SERVER_URL = '109.99.9.99:5555';
	const CONTROL_PANEL_TITLE = "Savazó rendszer vezérlő I.";
	
	const MYSQL_HOST = 'localhost';
	const MYSQL_USER = 'root';
	const MYSQL_PASSWORD = 'mypass';
	const MYSQL_DB = 'arbo';
	const LOGIN_TIMEOUT = 31536000;

	const NYOF_URL = 'http://192.168.2.172:80'; // ha üres sztring, akkor elrejti a html elemeket

	const PASSWD_KEY1 = 'Zxn0Dg'; // a jelszó hash képzéshez használt kulcsok, a jelszó hash:  md5( PASSWD_KEY1 + <jelszo> + PASSWD_KEY2 )
	const PASSWD_KEY2 = '1DQftY';
}
