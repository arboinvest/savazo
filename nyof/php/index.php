<?php
$content = file_get_contents('/home/pi/nyof/dev/dev');
$content = explode(';', $content);
$values = ['G16' => $content[0], 'G17' => $content[1], 'A4' => $content[2], 'A5' => $content[3], 'time' => $content[4], 'alarm' => $content[5], 'inv' => $content[6] 
	, 'sms1' => $content[7], 'sms2' => $content[8], 'sms3' => $content[9], 'sms4' => $content[10] ];
ob_clean();
header('Content-Type: application/json');
echo json_encode($values);
