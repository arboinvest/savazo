<?php
session_start();
if (isset($_SESSION['login']) && $_SESSION['login'] == 1): ?>
<?php session_regenerate_id(true); ob_clean(); ?>

<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<meta name="description" content="v1.2.0">
	<meta name="author" content="">
	<meta http-equiv="Cache-control" content="no-cache">
	<meta http-equiv="Expires" content="-1">
	<link rel="stylesheet" href="./css/bootstrap.min.css">
	<title>Felhasználó menedzsment</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
</head>
<style>
	.divElement {
		/*position: absolute;*/
		/*top: 30%;*/
		/*left: 50%;*/
		/*margin-left: -350px;*/
		margin-top: 30px;
		width: 350px;
		/*height: 385px;*/
		background-color:#FFFFFF;
		border-radius: 15px;
		padding: 20px 20px 20px 20px;
	}​
</style>
<html lang="en">
<body style="background-color:#333333">
	<script src="./js/jquery-3.3.1.min.js"></script>
	<script src="<?php echo './js/usrmngmt.js?' , time(); ?>"></script>
	<script>
		var mngmt = null;
		$( document ).ready(function() {
			mngmt = new UserMngtController();
			mngmt.init();
		});
	</script>


<center>
	<div class="divElement">
	
		<h3>Aktív felhasználók</h3><br>
		<div id="activeDiv"></div>
		
	</div>
	<div class="divElement">


		<center><h3>Új felhasználó</h3></center><br>
		<form>
			<div class="form-group">
				<label for="user">Felhasználónév</label>
				<input class="form-control form-control-sm" id="user" type="text">
			</div>		
			<div class="form-group">
				<label for="fullname">Név</label>
				<input class="form-control form-control-sm" id="fullname" type="text">
			</div>		
			<div class="form-group">
				<label for="pass">Jelszó</label>
				<input class="form-control form-control-sm" id="pass" type="password">
			</div>
			<div class="form-group">
				<label for="pass2">Jelszó megerősítés</label>
				<input class="form-control form-control-sm" id="pass2" type="password">
			</div>
			<div style="text-align: center; margin-top: 25px;" ><a class="btn btn-primary" id="btn" style="color: white;">Küldés</a></div>
		</form>

	</div>
	<div class="divElement">

		<center><h3>Inaktív felhasználó</h3></center><br>
		<form>
			<div class="form-group">
				<label for="iauser">Felhasználónév</label>
				<input class="form-control form-control-sm" id="iauser" type="text">
			</div>		
			<div style="text-align: center; margin-top: 25px;" ><a class="btn btn-primary" id="iabtn" style="color: white;">Küldés</a></div>
		</form>

	</div>
	<div class="divElement">
		
		<center><h3>Jelszó csere</h3></center><br>
		<form>
			<div class="form-group">
				<label for="pruser">Felhasználónév</label>
				<input class="form-control form-control-sm" id="pruser" type="text">
			</div>		
			<div class="form-group">
				<label for="prpass">Jelszó</label>
				<input class="form-control form-control-sm" id="prpass" type="password">
			</div>
			<div class="form-group">
				<label for="prpass2">Jelszó megerősítés</label>
				<input class="form-control form-control-sm" id="prpass2" type="password">
			</div>
			<div style="text-align: center; margin-top: 25px;" ><a class="btn btn-primary" id="prbtn" style="color: white;">Küldés</a></div>
		</form>
		

	</div>
	<div class="divElement">

		<form>
			<div style="text-align: center; margin-top: 25px;" ><a class="btn btn-primary" id="backbtn" style="color: white;">Vissza a Dashboard-ra</a></div>
		</form>

	</div>

</center>

</body>
</html>
<?php else: ?>
	<script>
		window.location.assign("login.php");
	</script>
<?php endif ?>

