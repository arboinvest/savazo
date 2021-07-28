<?php
require_once('/var/www/html/config/Config.php');
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, \Config\Config::NYOF_URL);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
$output=curl_exec($ch);
ob_clean();
header('Content-Type: application/json');
echo $output;
