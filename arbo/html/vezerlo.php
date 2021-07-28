<?php 
 	session_start();
	if (isset($_SESSION['login']) && $_SESSION['login'] > 0): ?>

	<?php session_regenerate_id(true); ob_clean(); ?>
	<!DOCTYPE html>

	<html lang="hu">
		<head>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
			<meta http-equiv="Cache-Control" content="no-cache, no-store, max-age=0, must-revalidate" />
			<meta http-equiv="Pragma" content="no-cache" />
			<meta http-equiv="Expires" content="0" />

			<title>Dashboard v1.2.6</title>
			<meta name="description" content="v1.2.6">
			<meta name="author" content="">

			<link rel="stylesheet" href="./css/bootstrap.min.css">
			<link rel="stylesheet" href="./css/fontawesome.css">
			<link rel="stylesheet" href="<?php echo './css/sr.css?' , time(); ?>">

			<!--[if lt IE 9]>
				<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
			<![endif]-->
		</head>

		<body>
			<script src="./js/jquery-3.3.1.min.js"></script>
			<script src="./js/bootstrap.min.js"></script>
			<script src="./js/charts.js"></script>
			<script src="<?php echo './js/vezerlok.js?' , time(); ?>"></script>
			<script>
				var vezerlok = null;		
				$( document ).ready(function() {
					vezerlok = new Vezerlok();
					vezerlok.init( <?php require_once('/var/www/html/config/Config.php');echo '"' , \Config\Config::SERVER_URL , '"';?> 
								   , <?php require_once('/var/www/html/config/Config.php');echo '"' , \Config\Config::NYOF_URL , '"';?>
								   , <?php require_once('/var/www/html/login/controller.php');echo '"' , LoginController::getNevekAsStream() , '"';?>
								   , <?php echo $_SESSION['login']; ?> );
				});
			</script>
			<table id="container" class="bg-light t-m">
				<tr class="control">
					<td id="status" class="sidebar text-center bg-success align-middle border border-dark text-dark">
					</td>
					<td colspan="6" class="text-left bg-light align-middle border border-dark">

						<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
							<td width="50%">
								<span><?php require_once('/var/www/html/config/Config.php');echo \Config\Config::CONTROL_PANEL_TITLE;?></span>
							</td>
							<td width="50%" class="text-right">
								<label id="waitnetw" style="display:none">&nbsp;<span style="color:red"><b>Várakozás az hálózati kapcsolatra...</b></red></label>
								<a class="plusz-btn" href="#" id="pid">Kapcsolási rajz</a>&nbsp;
							</td></tr>
						</table>
						
					</td>
					<td colspan="6" class="text-right bg-light align-middle border border-dark">
						<span id="time"></span>&nbsp;|
						<span id="username"></span>
						<a class="btn btn-danger logout-btn" id="logout">&nbsp;Kijelentkezés&nbsp;&nbsp;</a>
					</td>
				</tr>
				<tr class="devices">
					<td class="sidebar text-center bg-light border border-dark text-dark">
						<span>Eszközök</span>
					</td>
					<td id="p1" colspan="3" class="c4 bg-danger border border-dark">
						<table class="table table-sm">
							<tbody id="p1d">
								
							</tbody>
						</table>
					</td>
					<td id="p2" colspan="3" class="c4 bg-danger border border-dark">
						<table class="table table-sm">
							<tbody id="p2d">
								
							</tbody>
						</table>
					</td>
					<td id="p3" colspan="3" class="c4 bg-danger border border-dark">
						<table class="table table-sm">
							<tbody id="p3d">
								
							</tbody>
						</table>
					</td>
					<td colspan="3" class="c4 bg-secondary border border-dark">
						&nbsp;
					</td>
				</tr>
				<tr class="feedback">
					<td class="sidebar text-center bg-light border border-dark text-dark">
						<span>Üzemi sz.</span>
					</td>
					<td id="v2v4" colspan="3" class="c4 text-center align-middle bg-success border border-dark">
						<table cellpadding="0" cellspacing="0" class="full-width">
							<tbody>
								<tr>
									<td class="c4 text-center align-middle bg-success left-top-bottom-empty-border td-half">
										<a id="v2" class="btn full-height font-xxlarge">V2</a></td>
									<td class="c4 text-center align-middle bg-success borderless td-half">
										<a id="v4" class="btn full-height font-xxlarge">V4</a></td>
								</tr>
							</tbody>
						</table>
					</td>
					<td id="v4" colspan="3" class="c4 text-center align-middle bg-success border border-dark">
						<table cellpadding="0" cellspacing="0" class="full-width">
							<tbody>
								<tr>
									<td class="c4 text-center align-middle bg-success left-top-bottom-empty-border td-half">
										<a id="v6" class="btn full-height font-xxlarge">V6</a></td>
									<td class="c4 text-center align-middle bg-success borderless td-half">
										<a id="v8" class="btn full-height font-xxlarge">V8</a></td>
								</tr>
							</tbody>
						</table>
					</td>
					<td id="ksz" colspan="3" class="c4 text-center align-middle bg-danger border border-dark">KSZ</td>
					<td colspan="3" class="c4 text-center align-middle bg-secondary border border-dark">&nbsp;</td>
				</tr>
				<tr class="feedback">
					<td class="sidebar text-center bg-light border border-dark text-dark">
						<span>Savazó sz.</span>
					</td>

					<td id="v1v3" colspan="3" class="c4 text-center align-middle bg-danger border border-dark">
						<table cellpadding="0" cellspacing="0" class="full-width">
							<tbody>
								<tr>
									<td class="c4 text-center align-middle left-top-bottom-empty-border td-half">
										<a id="v1" class="btn full-height font-xxlarge">V1</a></td>
									<td class="c4 text-center align-middle borderless td-half">
										<a id="v3" class="btn full-height font-xxlarge">V3</a></td>
								</tr>
							</tbody>
						</table>
					</td>

					<td id="v5v7" colspan="3" class="c4 text-center align-middle bg-danger border border-dark">
						<table cellpadding="0" cellspacing="0" class="full-width">
							<tbody>
								<tr>
									<td class="c4 text-center align-middle left-top-bottom-empty-border td-half">
										<a id="v5" class="btn full-height font-xxlarge">V5</a></td>
									<td id="v7" class="c4 text-center align-middle borderless td-half">
										<a id="v7" class="btn full-height font-xxlarge">V7</a></td>
								</tr>
							</tbody>
						</table>
					</td>
					<td colspan="3" class="c4 text-center align-middle bg-danger border border-dark">
						<table cellpadding="0" cellspacing="0" class="full-width">
							<tbody>
								<tr>
									<td id="p1rs" class="c4 text-center align-middle bg-success left-top-bottom-empty-border td-half" style="font-size: 14pt">P1 reset</td>
									<td id="p2rs" class="c4 text-center align-middle bg-success borderless td-half" style="font-size: 14pt">P2 reset</td>
								</tr>
							</tbody>
						</table>
					</td>

					<?php if (isset($_SESSION['type']) && intval($_SESSION['type']) != 1): ?>
						<td colspan="3" class="c4 border border-dark">
							<a class="btn btn-success full-height" id="aVezKeziUzem">KÉZI<br>ÜZEMMÓD</a>
						</td>
					<?php else: ?>
						<td colspan="3" class="c4 border border-dark bg-secondary"></td>
					<?php endif ?>
				</tr>
				
				<?php if (isset($_SESSION['type']) && intval($_SESSION['type']) != 1): ?>
					<tr class="datas text-light" id="trVez">
						<td class="sidebar text-center bg-light border border-dark text-dark">
							<span>Vezérlés</span>	
						</td>
						<td colspan="3" class="c4 border border-dark overflow-hidden">
							<table cellpadding="0" cellspacing="0" class="full-width" id="tabVez1">
								<tbody>
									<tr class="tr-half">
										<td id="tdVez" class="text-center align-middle bg-secondary top-left-empty-border td-half">
											<a class="btn btn-secondary full-height" id="aVez000">SAVAZÁS</a>
										</td>
										<td class="text-center align-middle bg-secondary top-right-empty-border td-half">
											<a class="btn btn-secondary full-height" id="aVez001">MÉRÉS</a>
										</td>
									</tr>
									<tr class="tr-half">
										<td class="text-center align-middle bg-secondary bottom-left-empty-border td-half">
											<a class="btn btn-secondary full-height" id="aVez010">RESET</a>
										</td>
										<td class="text-center align-middle bg-secondary bottom-right-empty-border td-half">
											<a class="btn btn-secondary full-height" id="aVez011">LEZÁRÁS</a>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td colspan="3" class="c4 border border-dark overflow-hidden">
							<table cellpadding="0" cellspacing="0" class="full-width" id="tabVez2">
								<tbody>
									<tr class="tr-half">
										<td class="text-center align-middle bg-secondary top-left-empty-border td-half">
											<a class="btn btn-secondary full-height" id="aVez100">SAVAZÁS</a>
										</td>
										<td class="text-center align-middle bg-secondary top-right-empty-border td-half">
											<a class="btn btn-secondary full-height" id="aVez101">MÉRÉS</a>
										</td>
									</tr>
									<tr class="tr-half">
										<td class="text-center align-middle bg-secondary bottom-left-empty-border td-half">
											<a class="btn btn-secondary full-height" id="aVez110">RESET</a>
										</td>
										<td class="text-center align-middle bg-secondary bottom-right-empty-border td-half">
											<a class="btn btn-secondary full-height" id="aVez111">LEZÁRÁS</a>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
						<td colspan="3" class="c4 border border-dark">
							<a class="btn btn-secondary full-height" id="aVez2">ÜZEMÁLLAPOT</a>
						</td>
						<td colspan="3" class="c4 border border-dark">
							<a class="btn btn-secondary full-height" id="aVez3">AUTOMATA VEZÉRLÉS<br>KIKAPCSOLÁSA</a>
						</td>

					</tr>
				<?php endif ?>

				<tr class="datas text-light">
					<td class="sidebar text-center bg-light border border-dark text-dark">
						<span>Savazó értékek</span>	
					</td>
					<td colspan="3" class="c5 border border-dark">
						<div class="tdatas">
							<table class="table table-dark table-sm topics">
								<thead>
									<tr class="bg-info text-center">
										<th>Port</th>
										<th>Érték</th>
										<th>Kelte</th>
									</tr>
								</thead>
								<tbody id="p1m">
									
								</tbody>
							</table>
						</div>
					</td>
					<td colspan="3" class="c5 border border-dark">
						<div class="tdatas">
							<table class="table table-dark table-sm topics">
								<thead>
									<tr class="bg-info text-center">
										<th>Port</th>
										<th>Érték</th>
										<th>Kelte</th>
									</tr>
								</thead>
								<tbody id="p2m">
									
								</tbody>
							</table>
						</div>
					</td>
					<td colspan="3" class="c5 border border-dark">
						<div class="tdatas">
							<table class="table table-dark table-sm topics">
								<thead>
									<tr class="bg-info text-center">
										<th>Port</th>
										<th>Érték</th>
										<th>Kelte</th>
									</tr>
								</thead>
								<tbody id="p3m">
									
								</tbody>
							</table>
						</div>
					</td>
					<td colspan="3" class="c5 border border-dark">
						<div class="tdatas">
							<table class="table table-dark table-sm">
								<thead>
									<tr class="bg-info text-center">
										<th>Bejegyzés</th>
										<th>Kelte</th>
									</tr>
								</thead>
								<tbody id="ldata">
								
								</tbody>
							</table>
						</div>
					</td>
				</tr>

				<tr class="nyofdatas text-light">
					<td class="sidebar text-center bg-light border border-dark text-dark">
						<span>Szivattyúház</span>	
					</td>
					<td colspan="3" class="c5 border border-dark">
						<div class="tdatas">
							<table class="table table-dark table-sm topics">
								<thead>
									<tr class="bg-info text-center">
										<th>Port</th>
										<th>Érték</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="text-center">Hőmérséklet (°C)</td>
										<td class="text-right"><label id="nyfe1"></td>
									</tr>
									<!--tr>
										<td class="text-center">2. Hőmérséklet (°C)</td>
										<td class="text-right"><label id="nyfe2"></td>
									</tr-->
									<tr style="height:15px"><td class="text-center" colspan="3"></td></tr>
								</tbody>
							</table>
						</div>
					</td>
					<td colspan="3" class="c5 border border-dark">
						<div class="tdatas">
							<table class="table table-dark table-sm topics">
								<thead>
									<tr class="bg-info text-center">
										<th>Port</th>
										<th>Érték</th>
									</tr>
								</thead>
								<tbody>
									<tr id="nyfr">
										<td class="text-center">Nyomás (bar)</td>
										<td class="text-right"><label id="nyfe3"></td>
									</tr>
									<tr id="nyfi">
										<td class="text-center">Szivattyú inverter</td>
										<td class="text-right"><label id="nyfe4"></td>
									</tr>
									<tr style="height:15px"><td class="text-center" colspan="3"></td></tr>
								</tbody>
							</table>
						</div>
					</td>
					<td colspan="3" class="c5 border border-dark">
						<div class="tdatas">
							<table class="table table-dark table-sm topics">
								<thead>
									<tr class="bg-info text-center">
										<th>Utolsó mérés időpontja</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="text-center"><label id="nyfetm"></td>
									</tr>
									<tr style="height:15px"><td class="text-center" colspan="3"></td></tr>
								</tbody>
							</table>
						</div>
					</td>
					<td colspan="3" class="c5 border border-dark"><label id="nyflog"></td>
				</tr>

				<tr class="tasks text-light">
					<td class="sidebar text-center bg-light border border-dark text-dark">
						<span>Feladatok</span>	
					</td>
					<td colspan="12" class="c1 border border-dark">
						<div class="tdatas">
							<table class="table table-dark table-sm">
								<thead>
									<tr class="bg-info text-center">
										<th style="width:125px">Feladat</th>
										<th style="width:190px">Kiírás</th>
										<th style="width:190px">Kiolvasás</th>
										<th style="width:190px">Elkészült</th>
										<th style="width:100px">Eredmény</th>
										<th>Állapot</th>
										<th style="width:210px">Felhasználó</th>
									</tr>
								</thead>
								<tbody id="gst">
									
								</tbody>
							</table>
						</div>
					</td>
				</tr>
				<tr class="graph text-light">
					<td class="sidebar text-center  bg-light border border-dark text-dark">
						<span>Hőcserélők</span>	
					</td>
					<td colspan="6" class="c2 text-center align-middle border border-dark">
						<div id="gh1" class="chart-div"></div>
					</td>
					<td colspan="6" class="c2 text-center align-middle border border-dark">
						<div id="gh2" class="chart-div"></div>
					</td>
				</tr>

				<?php if (isset($_SESSION['type']) && intval($_SESSION['type']) != 1): ?>
					<tr>
						<td class="sidebar text-center bg-light border border-dark text-dark">
							<span>Karbantartás</span>
						</td>
						<td colspan="12" class="c2 text-center align-middle border border-dark">
							<a class="btn btn-danger logout-btn" id="p1restart">P1 indítása</a>&nbsp;|&nbsp;
							<a class="btn btn-danger logout-btn" id="p2restart">P2 indítása</a>&nbsp;|&nbsp;
							<a class="btn btn-danger logout-btn" id="p1ping">P1 ping</a>&nbsp;|&nbsp;
							<a class="btn btn-danger logout-btn" id="p2ping">P2 ping</a>&nbsp;|&nbsp;
							<a class="btn btn-danger logout-btn" id="p4reset">P4 RESET</a><hr>

							<?php if (intval($_SESSION['login']) == 1): ?>
								<a class="btn btn-danger logout-btn" id="usrmngmt">Felhasználók kezelése</a><hr>
							<?php else: ?>
								<a class="btn btn-danger logout-btn" id="passreplace">Jelszó csere</a><hr>
							<?php endif ?>

							<span style="color: lightgray">
							Eredmény:<br>
							<label id="cmdout"></label></span><br><br><hr>
						</td>
						
					</tr>
				<?php endif ?>

			</table>
		</body>
	</html>
<?php else: ?>
	<script>
		// window.location.assign("http://" + <?php require_once('/var/www/html/config/Config.php');echo '"' , \Config\Config::SERVER_URL , '"';?> + "/login.php");
		window.location.assign("login.php");
	</script>
<?php endif ?>
