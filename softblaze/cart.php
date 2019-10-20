<?php
session_start();
$_SESSION['prevPage'] = 'cart.php';
if (isset($_POST['submit'])) {
	$_SESSION['pid'] = $_POST['pid'];
}
if (!isset($_SESSION['uid'])) {
	header('Location: login.php');
	exit();
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>SoftBlaze | Cart</title>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/bs/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="softblaze.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="/bs/js/bootstrap.min.js"></script>
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300" rel="stylesheet">
	<link rel="icon" href="img/logo.png">

	<script type="text/javascript">
		$(document).ready(function(){
			$('.qty-select').change(function() {
				$(this).closest('form').submit();
			});
		});
	</script>
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
				<li><a href="cart.php" id="current-page"><span class="glyphicon glyphicon-shopping-cart"></span> CART</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="banner" id="cart-banner">
	<h1>CART</h1>
</div>

<?php
//connect to database
require('connect.php');

if (isset($_POST['qty-select'])) {
	//user has changed qty of a cart item

	//get posted hidden values
	$newQty = $_POST['qty-select'];
	$newSubtot = $newQty * $_POST['qty-price'];

	//change qty of product with pid
	DB::update('cart', array(
		'qty' => $newQty,
		'subtot' => $newSubtot,
	), 'cid = %s', $_POST['qty-cid']);
}

if (isset($_POST['remove'])) {
	//user has removed a cart item
	DB::query('delete from cart where cid = %s', $_POST['remove-cid']);
}

//processing added product
if (isset($_SESSION['pid'])) {

	$pid = $_SESSION['pid'];
	unset($_SESSION['pid']);

	//retrieving product info
	$addedProduct = DB::queryFirstRow('select * from products where pid = %s', $pid);

	//checking if product already exists in cart
	$existingProduct = DB::queryFirstRow('select * from cart where uid = %s and pid = %s', $_SESSION['uid'], $pid);

	if ($existingProduct) {
		//product exists in cart

		//add 1 to qty
		$newQty = $existingProduct['qty'] + 1;
		$newSubtot = $existingProduct['subtot'] + $addedProduct['price'];

		//update table for this specific cid
		DB::update('cart', array(
			'qty' => $newQty,
			'subtot' => $newSubtot,
		), 'cid = %s', $existingProduct['cid']);

	} else {
		//new product added

		//add user to database
		DB::insert('cart', array(
			'uid' => $_SESSION['uid'],
			'pid' => $pid,
			'name' => $addedProduct['name'],
			'uprice' => $addedProduct['price'],
			'qty' => 1,
			'subtot' => $addedProduct['price'],
		));
	}
}

//retrieving product data from products table
$userCart = DB::query('select * from cart where uid = %s', $_SESSION['uid']);

//display final cart
if ($userCart) {
	//user has at least 1 item in cart
	echo '
	<table class="table table-hover cart-table">
		<thead>
			<tr>
				<th colspan="2">PRODUCT</th>
				<th>UNIT PRICE</th>
				<th>QUANTITY</th>
				<th>SUBTOTAL</th>
				<th></th>
			</tr>
		</thead>
		<tbody>';

	$totPrice = 0;
	foreach ($userCart as $product) {
		$pc = DB::queryFirstRow('select * from products where pid = %s', $product['pid']);
		$totPrice += $product['subtot'];
		echo '
		<tr>
			<td><img src="img/'.$pc['image'].'" width="130px;"></td>
			<td style="text-align: left;">
				<h4>'.$product['name'].'</h4>
				<p>'.$pc['tower'].' | '.$pc['chipset'].'</p>
			</td>
			<td>$'.$product['uprice'].'</td>
			<td>
				<form name="addProduct" method="post" action="cart.php">
					<select class="form-control qty-select" id="'.$product['pid'].'" name="qty-select">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
					</select>
					<input type="hidden" name="qty-cid" value="'.$product['cid'].'">
					<input type="hidden" name="qty-price" value="'.$product['uprice'].'">
				</form>
				<script>$("#'.$product['pid'].'").val('.$product['qty'].');</script>
			</td>
			<td>$'.$product['subtot'].'</td>
			<td>
				<form name="removeProduct" method="post" action="cart.php">
					<input class="button" type="submit" name="remove" value="REMOVE" onclick="return confirm(\'Remove '.$product['name'].' from your cart?\')">
					<input type="hidden" name="remove-cid" value="'.$product['cid'].'">
				</form>
			</td>
		</tr>';
	}
	printf('<tr><td colspan="5" style="text-align: right;"><h4 style="color:#ff8898; font-weight: 400;">TOTAL PRICE: $%.2f</h4></td><td></td></tr>', $totPrice);
	echo '</tbody></table>';
	

} else {
	echo '<h2>YOUR CART IS EMPTY</h2>';
}
?>

<br><br>

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
