<?php
//import the meekroDB class library
require_once 'meekrodb.2.3.class.php';

//database connection details
DB::$user = 'root';
DB::$password = '';
DB::$dbName ='softblaze';

//alert message pop-up
function alert($type, $msg) {
	echo '
	<div class="alert alert-'.$type.' alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'.
		$msg
	.'</div>';
}
?>