<?php
require_once('/var/www/html/config/Config.php');
$url = \Config\Config::NYOF_URL . '/reset.php';
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
$output=curl_exec($ch);
ob_clean();
header('Content-Type: text/html');
echo $output;
