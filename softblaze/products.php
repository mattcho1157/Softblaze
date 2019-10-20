<?php
session_start();
$_SESSION['prevPage'] = 'products.php';
?>

<!DOCTYPE html>
<html>
<head>
	<title>SoftBlaze | Products</title>

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
				<li><a href="products.php" id="current-page">PRODUCTS</a></li>
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

<div class="banner" id="products-banner">
	<h1>PRODUCTS</h1>
</div>

<?php
//connect to database
require('connect.php');
?>

<div class="products-section">
	
		<h2>GAMING</h2>
		<?php
		//get gaming PC data
		$gamingProducts = DB::query('select * from products where type = "GAMING"');

		//display each product
		foreach ($gamingProducts as $pc) {
			echo '
			<form name="productform" method="post" action="cart.php">
				<div class="product col-xs-12 col-sm-6">
					<img src="img/'.$pc['image'].'">
					<h3>'.$pc['name'].'</h3>
					<p>'.$pc['tower'].' | '.$pc['chipset'].'</p>
					<p class="product-price">$'.$pc['price'].'</p><br>
					<input class="button" type="submit" name="submit" value="ADD TO CART">
					<input type="hidden" name="pid" value="'.$pc['pid'].'">
				</div>
			</form>';
		}
		?>

		<h2>OFFICE</h2>
		<?php
		//get office PC data
		$officeProducts = DB::query('select * from products where type = "OFFICE"');

		//display each product
		foreach ($officeProducts as $pc) {
			echo '
			<form name="productform" method="post" action="cart.php">
				<div class="product col-xs-12 col-sm-6">
					<img src="img/'.$pc['image'].'">
					<h3>'.$pc['name'].'</h3>
					<p>'.$pc['tower'].' | '.$pc['chipset'].'</p>
					<p class="product-price">$'.$pc['price'].'</p><br>
					<input class="button" type="submit" name="submit" value="ADD TO CART">
					<input type="hidden" name="pid" value="'.$pc['pid'].'">
				</div>
			</form>';
		}
		?>
	</form>
</div>

<?php
//alert message pop-up for account log in / sign up
if (isset($_SESSION['loginSuccessful'])) {
	alert('info', 'LOG IN SUCCESSFUL');
	unset($_SESSION['loginSuccessful']);

} elseif (isset($_SESSION['signupSuccessful'])) {
	alert('info', 'SIGN UP SUCCESSFUL');
	unset($_SESSION['signupSuccessful']);
}
?>

</body>
</html>