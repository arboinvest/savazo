<?php
session_start();
if (isset($_SESSION['login']) && $_SESSION['login'] > 1): ?>
<?php session_regenerate_id(true); ob_clean(); ?>

<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<meta name="description" content="v1.2.0">
	<meta name="author" content="">
	<meta http-equiv="Cache-control" content="no-cache">
	<meta http-equiv="Expires" content="-1">
	<link rel="stylesheet" href="./css/bootstrap.min.css">
	<title>Jelszó csere</title>
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
	<script src="<?php echo './js/passreplace.js?' , time(); ?>"></script>
	<script>
		var passReplace = null;
		$( document ).ready(function() {
			passReplace = new PassReplace();
			passReplace.init( <?php echo $_SESSION['login']; ?> );
		});
	</script>


<center>
	<div class="divElement">
		
		<center><h3>Jelszó csere</h3></center>
		<center><h6> <?php require_once('/var/www/html/login/controller.php');echo LoginController::getNevek()[intval($_SESSION['login'])]; ?></h6></center><br>
		<form>
			<div class="form-group">
				<label for="oldpass">Régi jelszó</label>
				<input class="form-control form-control-sm" id="oldpass" type="password">
			</div>		
			<div class="form-group">
				<label for="newpass">Új Jelszó</label>
				<input class="form-control form-control-sm" id="newpass" type="password">
			</div>
			<div class="form-group">
				<label for="newpass2">Új jelszó megerősítése</label>
				<input class="form-control form-control-sm" id="newpass2" type="password">
			</div>
			<div style="text-align: center; margin-top: 25px;" ><a class="btn btn-primary" id="submit" style="color: white;">Küldés</a></div>
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

