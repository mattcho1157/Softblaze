<?php
session_start();
$_SESSION['prevPage'] = 'account.php';
if (!isset($_SESSION['uid'])) {
	header('Location: login.php');
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>SoftBlaze | My Account</title>

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
				<li><a href="account.php" id="current-page"><span class="glyphicon glyphicon-user"></span> MY ACCOUNT</a></li>
				<li><a href="cart.php"><span class="glyphicon glyphicon-shopping-cart"></span> CART</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="banner" id="account-banner">
	<h1><?php echo strtoupper($_SESSION['fname']); ?></h1> 
</div>

<?php
//connect to database
require('connect.php');
?>

<form name="logoutform" method="post" action="account.php" style="text-align: center;">
	<input class="button" id="button-spec" type="submit" name="logout" value="LOG OUT">
</form>

<?php
if (isset($_POST['logout'])) {
	session_destroy();
	header('Location: account.php');
}
?>

<form name="deleteform" method="post" action="account.php" style="text-align: center;">
	<input class="button" id="button-warning" type="submit" name="delete" value="DELETE ACCOUNT">
</form>

<?php
if (isset($_POST['delete'])) {
	session_destroy();
	DB::query('delete from users where uid = %s', $_SESSION['uid']);
	header('Location: account.php');
}
?>

<?php
//get user's data
$userData = DB::queryFirstRow('select * from users where uid = %s', $_SESSION['uid']);
$fname = $userData['fname'];
$lname = $userData['lname'];
$email = $userData['email'];
$pwd = $userData['pwd'];
?>

<div class="account-section">
	<div class="col-xs-12 col-sm-6">
		<form name="profileform" method="post" action="account.php">
			<h2>EDIT PROFILE</h2>
			<div class="form-group col-xs-12 col-sm-6">
				<input class="form-control" type="text" name="fname" placeholder="FIRST NAME" value= "<?php echo $fname; ?>" required>
			</div>
			<div class="form-group col-xs-12 col-sm-6">
				<input class="form-control" type="text" name="lname" placeholder="LAST NAME" value= "<?php echo $lname; ?>" required>
			</div>
			<div class="form-group col-xs-12">
				<input class="form-control" type="email" name="email" placeholder="E-MAIL" value= "<?php echo $email; ?>" required>
			</div>
			<div class="checkbox col-xs-12">
				<label><input type="checkbox" name="news" checked>I want to receive news & special offers<br><br></label>
			</div>
			<div class="form-group col-xs-12">
				<input class="button" type="submit" name="submitprofile" value="SAVE PROFILE">
			</div>
		</form>
	</div>

	<div class="col-xs-12 col-sm-6">
		<form name="passwordform" method="post" action="account.php">
			<h2>EDIT PASSWORD</h2>
			<div class="form-group col-xs-12">
				<input class="form-control" type="password" name="currentpwd" placeholder="CURRENT PASSWORD" required>
			</div>
			<div class="form-group col-xs-12">
				<input class="form-control" type="password" name="newpwd" placeholder="NEW PASSWORD" required>
			</div>
			<div class="form-group col-xs-12">
				<input class="form-control" type="password" name="confirmpwd" placeholder="CONFIRM PASSWORD" required><br>
			</div>
			<div class="form-group col-xs-12">
				<input class="button" type="submit" name="submitpassword" value="SAVE PASSWORD">
			</div>
		</form>
	</div>
</div>

<?php

if (isset($_POST['submitprofile'])) {
	//save profile

	//get submitted credentials and sanitise them
	$fname = trim(stripslashes(htmlspecialchars($_POST['fname'])));
	$lname = trim(stripslashes(htmlspecialchars($_POST['lname'])));
	$newEmail = trim(stripslashes(htmlspecialchars($_POST['email'])));
	$news = (isset($_POST['news']) ? 1 : 0);

	//check if email already exists
	$userData = DB::queryFirstRow('select * from users where email = %s', $newEmail);

	if ($userData && $newEmail != $email) {
		//duplicate user
		alert('danger', 'EMAIL NOT AVAILABLE');
	} else {
		//new user

		//update user's profile data
		DB::update('users', array(
			'fname' => $fname,
			'lname' => $lname,
			'email' => $newEmail,
			'news' => $news
		), 'uid = %s', $_SESSION['uid']);

		//change session variables
		$_SESSION['fname'] = $fname;
		$_SESSION['profileChanged'] = true;

		//refresh page
		header('Location: account.php');
		exit();
	}

} elseif (isset($_POST['submitpassword'])) {
	//save password

	//get submitted credentials and hash them
	$currentpwd = sha1($_POST['currentpwd']);
	$newpwd = sha1($_POST['newpwd']);
	$confirmpwd = sha1($_POST['confirmpwd']);

	$correctpwd = DB::queryFirstRow('select pwd from users where uid = %s', $_SESSION['uid']);
	if ($currentpwd == $correctpwd['pwd']) {
		//given password is correct
		
		if ($newpwd == $confirmpwd) {
			//passwords match

			//update user's password
			DB::update('users', array(
				'pwd' => $newpwd,
			), 'uid = %s', $_SESSION['uid']);

			//change session variable for alert pop-up
			$_SESSION['passwordChanged'] = true;

			//refresh page
			header('Location: account.php');
			exit();

		} else {
			//passwords do not match
			alert('danger', 'PASSWORDS DO NOT MATCH');
		}
	} else {
		//incorrect password
		alert('danger', 'INCORRECT PASSWORD');
	}
}
?>


<?php
//alert message pop-up for account log in / sign up
if (isset($_SESSION['loginSuccessful'])) {
	alert('info', 'LOG IN SUCCESSFUL');
	unset($_SESSION['loginSuccessful']);

} elseif (isset($_SESSION['signupSuccessful'])) {
	alert('info', 'SIGN UP SUCCESSFUL');
	unset($_SESSION['signupSuccessful']);
}

//alert message pop-up for profile change
if (isset($_SESSION['profileChanged'])) {
	alert('info', 'PROFILE HAS BEEN CHANGED');
	unset($_SESSION['profileChanged']);
}

//alert message pop-up for password change
if (isset($_SESSION['passwordChanged'])) {
	alert('info', 'PASSWORD HAS BEEN CHANGED');
	unset($_SESSION['passwordChanged']);
}
?>

</body>
</html>