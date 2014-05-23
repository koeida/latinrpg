<?php
	require '/inc/utils.php';
	session_start();
	if(isset($_POST['username'])) {
		$res = sqlSelect("SELECT character_name, password FROM users WHERE id = '".$_POST['username']."'");
		if(count($res) > 0 && $res[0]['password'] == $_POST['password']) {
			$_SESSION['uid'] = $_POST['username'];
			$_SESSION['name'] = $res[0]['character_name'];
		} else {
		}
	}
?>
<!DOCTYPE html> 
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>LatinRPG</title>
	<link href="/css/bootstrap.min.css" rel="stylesheet">
	<style>
		body { 
			background-image: url("img/login_landscape.jpg");	
		}

		.form-signin {
			background-color: white;
			opacity: .75;
			max-width: 330px;
			padding: 15px;
			padding-top: 5px;
			margin: 120px auto;
			border-radius: 10px;
		}
		.form-signin .form-control {
			position: relative;
			height: auto;
			padding: 10px;
			font-size: 16px;
		}
		.form-signin .form-control:focus {
			z-index: 2;
		}
		.form-signin input[type="name"] {
			margin-bottom: 5px;
		}
		.form-signin input[type="password"] {
			margin-bottom: 5px;
		}
		.navbar-fixed-bottom {
			color: white;
		}
	</style>
</head>
	<body>
		<div class=container>
			<form class=form-signin action="login.php" method=post role=form id=loginform>
			<h2 class=form-signin-heading>RomaRPG</h2>
			<input type=name name=username class=form-control placeholder=nomen required autofocus>
			<input type=password name=password class="form-control" placeholder=clavis name=password required></input>
			<button class="btn btn-lg btn-primary btn-block" type=submit>intra</button>
			</form>
		</div>
		<span id=info class=navbar-fixed-bottom>LUDUM HOC A KEEGAN DUNN MENSE APRILIS ANNO MMXIV FACTUM EST</span> 
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="/javascripts/bootstrap.min.js"></script>
	</body>
</html>
