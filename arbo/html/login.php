<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<meta name="description" content="v1.2.0">
	<meta name="author" content="">
	<meta http-equiv="Cache-control" content="no-cache">
	<meta http-equiv="Expires" content="-1">
	<link rel="stylesheet" href="./css/bootstrap.min.css">
	<title>Bejelentkezés</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
</head>
<style>
	.divElement {
		position: absolute;
		top: 30%;
		left: 50%;
		margin-left: -150px;
		margin-top: -150px;
		width: 300px;
		height: 385px;
		background-color:#FFFFFF;
		border-radius: 15px;
		padding: 20px 20px 20px 20px;
	}​
</style>
<html lang="en">
    <body style="background-color:#333333">
		<div class="divElement">
		
			<script src="./js/jquery-3.3.1.min.js"></script>
			<script src="./js/login.js?<?php echo time(); ?>"></script>
			<script>
				var login = null;
				$( document ).ready(function() {
					login = new Login();
					login.init( <?php require_once('/var/www/html/config/Config.php');echo '"' , \Config\Config::SERVER_URL , '"';?> );
				});
			</script>
			
			<div style="text-align: center"><h5>Bejelentkezés</h5></div>
			<strong>&nbsp;</strong>
			<form>
			
				<div class="form-group">
					<label for="inputsm">Felhasználónév:</label>
					<input class="form-control form-control-sm" id="user" type="text">
				</div>					
			
				<div class="form-group">
					<label for="inputsm">Jelszó:</label>
					<input class="form-control form-control-sm" id="pass" type="password">
				</div>				
				<div class="form-group">
					<?php if (! isset($_GET['d']) || $_GET['d'] == '1'): ?>
						<input type="radio" id="d1" name="dd" value="d1" checked><label for="d1" style="padding-left: 10px">Dashboard</label><br>
					<?php else: ?>
						<input type="radio" id="d1" name="dd" value="d1"><label for="d1" style="padding-left: 10px">Dashboard</label><br>
					<?php endif ?>
					
					<?php if (isset($_GET['d']) && $_GET['d'] == '2'): ?>
						<input type="radio" id="d2" name="dd" value="d2" checked><label for="d2" style="padding-left: 10px">Kapcsolási rajz</label><br>
					<?php else: ?>
						<input type="radio" id="d2" name="dd" value="d2"><label for="d2" style="padding-left: 10px">Kapcsolási rajz</label><br>
					<?php endif ?>
						
				</div>
				
				<div style="text-align: center; margin-top: 25px;" ><a class="btn btn-primary" id="btn" style="color: white;">Bejelentkezés</a></div>
				<div style="display: none; color: red;" id="error"><h7>Hibás felhasználónév, jelszó!</h7></div>
			</form>
			
		</div>
	</body>
	
	
</html>
