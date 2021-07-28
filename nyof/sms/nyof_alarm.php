<?php 
$pw = $_POST['pw'];
$msg = $_POST['msg'];
if ($pw == 'T2z76BqSb5fukPpRtzHt7Up1h6aYgpq6uQNvPAGiR0PRc51UJHCG5W3mjJpF3SNZ') {
	$output = shell_exec('node /home/csaba/nyof/send.js ' . $msg);
	echo $output;
} else {
	echo 'access denied';
}
