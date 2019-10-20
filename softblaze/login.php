<?php
session_start();
if (isset($_SESSION['uid'])) {
	header('Location: account.php');
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>SoftBlaze | Log In</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/bs/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="softblaze.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="/bs/js/bootstrap.min.js"></script>
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300" rel="stylesheet">
	<link rel="icon" href="img/logo.png">
</head>
<body>

<nav class="navbar navbar-default navbar-fixed-top" style="background-color: white">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="./"><img src="img/logopink.png" height="40"></a>
		</div>

		<div class="collapse navbar-collapse" id="navbar">
			<ul class="nav navbar-nav">
				<li><a href="products.php">PRODUCTS</a></li>
				<li><a href="#">COMPANY</a></li>
				<li><a href="#">SUPPORT</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="account.php"><span class="glyphicon glyphicon-user"></span> MY ACCOUNT</a></li>
				<li><a href="cart.php"><span class="glyphicon glyphicon-shopping-cart"></span> CART</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="login-section">
	<div class="col-xs-12 col-sm-6">
		<form name="loginform" method="post" action="login.php">
			<h2>LOG IN</h2>
			<div class="form-group col-xs-12">
				<input class="form-control" type="email" name="email" placeholder="E-MAIL" required>
			</div>
			<div class="form-group col-xs-12">
				<input class="form-control" type="password" name="pwd" placeholder="PASSWORD" required>
			</div>
			<div class="checkbox col-xs-12">
				<label><input type="checkbox" checked>Keep me logged in</label><br><br>
			</div>
			<div class="form-group col-xs-12">
				<input class="button" type="submit" name="submitlogin" value="LOG IN">
			</div>
		</form>
	</div>

	<div class="col-xs-12 col-sm-6">
		<form name="signupform" method="post" action="login.php">
			<h2>SIGN UP</h2>
			<div class="form-group col-xs-12 col-sm-6">
				<input class="form-control" type="text" name="fname" placeholder="FIRST NAME" required>
			</div>
			<div class="form-group col-xs-12 col-sm-6">
				<input class="form-control" type="text" name="lname" placeholder="LAST NAME" required>
			</div>
			<div class="form-group col-xs-12">
				<input class="form-control" type="email" name="email" placeholder="E-MAIL" required>
			</div>
			<div class="form-group col-xs-12">
				<input class="form-control" type="password" name="pwd" placeholder="PASSWORD" required>
			</div>
			<div class="form-group col-xs-12">
				<input class="form-control" type="password" name="confirmpwd" placeholder="CONFIRM PASSWORD" required>
			</div>
			<div class="checkbox col-xs-12">
				<label><input type="checkbox" name="news" checked>I want to receive news & special offers</label>
			</div>
			<div class="checkbox col-xs-12">
				<label><input type="checkbox" name="t&c" required>I agree with the Terms & Conditions</label><br><br>
			</div>
			<div class="form-group col-xs-12" style="margin-bottom: 60px;">
				<input class="button" type="submit" name="submitsignup" value="SIGN UP">
			</div>
		</form>
	</div>
</div>

<?php
if (isset($_POST['submitlogin'])) {
	//log in attempted

	//get submitted credentials and sanitise/hash them
	$email = trim(stripslashes(htmlspecialchars($_POST['email'])));
	$pwd = sha1($_POST['pwd']);

	//connect to database
	require('connect.php');

	//attempt to get user's data
	$userData = DB::queryFirstRow('select * from users where email = %s', $email);
	if (!$userData) {
		//input email not found in database
		alert('danger', 'INVALID EMAIL OR PASSWORD');

	} else {
		//live user - check password
		if ($pwd != $userData['pwd']) {
			//invalid password
			alert('danger', 'INVALID EMAIL OR PASSWORD');

		} else {
			//user credentials match

			//set session variables for checking login state
			$_SESSION['fname'] = $userData['fname'];
			$_SESSION['uid'] = $userData['uid'];
			$_SESSION['loginSuccessful'] = true;

			//redirect to previous page
			header('Location: '.$_SESSION['prevPage']);
		}
	}
} elseif (isset($_POST['submitsignup'])) {
	//sign up attempted

	//get submitted credentials and sanitise/hash them
	$fname = trim(stripslashes(htmlspecialchars($_POST['fname']))); 
	$lname = trim(stripslashes(htmlspecialchars($_POST['lname']))); 
	$email = trim(stripslashes(htmlspecialchars($_POST['email']))); 
	$pwd = sha1($_POST['pwd']); 
	$confirmpwd = sha1($_POST['confirmpwd']);
	$news = (isset($_POST['news']) ? 1 : 0);

	//connect to dabatase
	require('connect.php');

	//check if email already exists
	$userData = DB::queryFirstRow('select * from users where email = %s', $email);

	if ($userData) {
		//duplicate user
		alert('danger', 'EMAIL NOT AVAILABLE');
	} else {
		//new user

		if ($pwd == $confirmpwd) {
			//passwords match

			//add user to database
			DB::insert('users', array(
				'fname' => $fname,
				'lname' => $lname,
				'email' => $email,
				'pwd' => $pwd,
				'news' => $news,
			));

			//set session variables for checking login state
			$_SESSION['fname'] = $fname;
			$_SESSION['uid'] = DB::queryFirstRow('select uid from users where email = %s', $email)['uid'];
			$_SESSION['signupSuccessful'] = true;

			//redirect to previous page
			header('Location: '.$_SESSION['prevPage']);
			
		} else {
			//passwords do not match
			alert('danger', 'PASSWORDS DO NOT MATCH');
		}
	}
}
?>

</body>
</html>