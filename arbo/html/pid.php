<?php 
 	session_start();
	if (isset($_SESSION['login']) && $_SESSION['login'] > 0): ?>
	
	<?php session_regenerate_id(true); ob_clean(); ?>

	
	<html lang="hu">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="Cache-Control" content="no-cache, no-store, max-age=0, must-revalidate" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="0" />

		<title>P&ID v1.0.0</title>
		<meta name="description" content="v1.2.6">
		<meta name="author" content="">
		<link rel="stylesheet" href="<?php echo './css/pid.css?' , time(); ?>">
		<!--[if lt IE 9]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>

	<body style="background-color:gray">
		<script src="./js/jquery-3.3.1.min.js"></script>
		<script src="./js/bootstrap.min.js"></script>
		<script src="<?php echo './js/pid.js?' , time(); ?>"></script>
		<script>
			var pid = null;		
			$( document ).ready(function() {
				pid = new PID();
				pid.init( <?php require_once('/var/www/html/config/Config.php');echo '"' , \Config\Config::SERVER_URL , '"';?> 
								   , <?php require_once('/var/www/html/config/Config.php');echo '"' , \Config\Config::NYOF_URL , '"';?> );
			});
		</script>
		<div id="hatter" style="position: absolute; left: 0px; top: 0px;">
			<img src="./img/vezkep.php" border=1>
		</div>
		
		<?php if (isset($_SESSION['type']) && intval($_SESSION['type']) != 1): ?>
			<div class="floating" id="sz1Div"><a href="#" onclick="pid.sz1Click()"><img id="sz1" src="./img/sziv_all.png" border="0"></a></div>		
			<div class="floating" id="v1Div"><a href="#" onclick="pid.v1Click()"><img id="v1" src="./img/sz.png" border="0"></a></div>
			<div class="floating" id="v2Div"><a href="#" onclick="pid.v2Click()"><img id="v2" src="./img/sz.png" border="0"></a></div>
			<div class="floating" id="v3Div"><a href="#" onclick="pid.v3Click()"><img id="v3" src="./img/sz.png" border="0"></a></div>
			<div class="floating" id="v4Div"><a href="#" onclick="pid.v4Click()"><img id="v4" src="./img/sz.png" border="0"></a></div>	
			<div class="floating" id="v5Div"><a href="#" onclick="pid.v5Click()"><img id="v5" src="./img/sz.png" border="0"></a></div>
			<div class="floating" id="v6Div"><a href="#" onclick="pid.v6Click()"><img id="v6" src="./img/sz.png" border="0"></a></div>
			<div class="floating" id="v7Div"><a href="#" onclick="pid.v7Click()"><img id="v7" src="./img/sz.png" border="0"></a></div>
			<div class="floating" id="v8Div"><a href="#" onclick="pid.v8Click()"><img id="v8" src="./img/sz.png" border="0"></a></div>	
			<div class="floating" id="keziVezDiv"><input type="button" value="Kézi vezérlés BE" id="keziVez" onclick="pid.keziVezClick()"></input></div>
		<?php else: ?>
			<div class="floating" id="sz1Div"><img id="sz1" src="./img/sziv_all.png" border="0"></div>		
			<div class="floating" id="v1Div"><img id="v1" src="./img/sz.png" border="0"></div>
			<div class="floating" id="v2Div"><img id="v2" src="./img/sz.png" border="0"></div>
			<div class="floating" id="v3Div"><img id="v3" src="./img/sz.png" border="0"></div>
			<div class="floating" id="v4Div"><img id="v4" src="./img/sz.png" border="0"></div>	
			<div class="floating" id="v5Div"><img id="v5" src="./img/sz.png" border="0"></div>
			<div class="floating" id="v6Div"><img id="v6" src="./img/sz.png" border="0"></div>
			<div class="floating" id="v7Div"><img id="v7" src="./img/sz.png" border="0"></div>
			<div class="floating" id="v8Div"><img id="v8" src="./img/sz.png" border="0"></div>
			<div class="floating" id="keziVezDiv"></div>
		<?php endif ?>
		<div class="floating" id="logoutDiv"><input type="button" value="Kijelentkezés" id="logout" onclick="pid.logoutClick()"></input></div>
		<div class="floating" id="dashbDiv"><input type="button" value="Dashboard" id="dashb" onclick="pid.dashbClick()"></input></div>
		<div class="floating" id="hRiasztDiv"><img id="hRiaszt" src="./img/szint-tr.png" border="0"></div>		
		<div class="floating" id="hMaxDiv"><img id="hMax" src="./img/szint-tr.png" border="0"></div>		
		<div class="floating" id="hMinDiv"><img id="hMin" src="./img/szint-tr.png" border="0"></div>		
		<div class="floating" id="nyszDiv"><img id="nysz" src="./img/csziv-tr_all.png" border="0"></div>	
		
		<div class="floating" id="ph2ValueDiv"><label class="szamok" id="ph2Value">0.0 pH</label></div>	
		<div class="floating" id="ph1ValueDiv"><label class="szamok" id="ph1Value">0.0 pH</label></div>	
		<div class="floating" id="t1ValueDiv"><label class="szamok" id="t1Value">0.0 °C</label></div>	
		<div class="floating" id="t2ValueDiv"><label class="szamok" id="t2Value">0.0 °C</label></div>	
		<div class="floating" id="t3ValueDiv"><label class="szamok" id="t3Value">0.0 °C</label></div>	
		<div class="floating" id="t4ValueDiv"><label class="szamok" id="t4Value">0.0 °C</label></div>	
		<div class="floating" id="m4ValueDiv"><label class="szamok" id="m4Value">0.0 °m3/h</label></div>
		<div class="floating" id="p1ValueDiv"><label class="szamok" id="p1Value">0.0 bar</label></div>
		<div class="floating" id="p2ValueDiv"><label class="szamok" id="p2Value">0.0 bar</label></div>
		<div class="floating" id="t5ValueDiv"><label class="szamok" id="t5Value">0.0 °C</label></div>
		<div class="floating" id="p3ValueDiv"><label class="szamok" id="p3Value">0.0 bar</label></div>
		<div class="floating" id="nyszErtekDiv"><label class="vegallas" id="nyszErtek">H</label></div>
		
		<div class="floating ora" id="timeDiv"><label id="time">00:00</label></div>
		<div class="floating indicator" id="indDiv"><label id="ind"></label></div>
		<div class="floating vezerlo" id="p3Div">P3</div>
		<div class="floating vezerlo" id="p2Div">P2</div>
		<div class="floating vezerlo" id="p1Div">P1</div>
		<div class="floating vezerlo" id="p4Div">P4</div>
		
		<div class="floating" id="v1nyDiv"><label id="v1ny" class="vegallas"></label></div>
		<div class="floating" id="v2nyDiv"><label id="v2ny" class="vegallas"></label></div>
		<div class="floating" id="v3nyDiv"><label id="v3ny" class="vegallas"></label></div>
		<div class="floating" id="v4nyDiv"><label id="v4ny" class="vegallas"></label></div>
		<div class="floating" id="v5nyDiv"><label id="v5ny" class="vegallas"></label></div>
		<div class="floating" id="v6nyDiv"><label id="v6ny" class="vegallas"></label></div>
		<div class="floating" id="v7nyDiv"><label id="v7ny" class="vegallas"></label></div>
		<div class="floating" id="v8nyDiv"><label id="v8ny" class="vegallas"></label></div>
		
		<div class="floating" id="nyszRiasztDiv"><label id="nyszRiaszt" class="riasztas"></label></div>
		<div class="floating" id="szintRiasztDiv"><label id="szintRiaszt" class="riasztas"></label></div>
		

	</body>
	</html>
<?php else: ?>
	<script>
		// window.location.assign("http://" + <?php require_once('/var/www/html/config/Config.php');echo '"' , \Config\Config::SERVER_URL , '"';?> + "/login.php");
		window.location.assign("login.php?d=2");
	</script>
<?php endif ?>

