<?php
	//session_start();

	$servername = "localhost";
	$username = "blackgarlic";
	$password = "dbsgksmf1";
	$dbname = "blackgarlic";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	mysqli_query($conn, "set names utf8");

//	define("ADMIN_URL", "http://" . $_SERVER['HTTP_HOST'] . "/mileage-system/m-admin/");
?>
