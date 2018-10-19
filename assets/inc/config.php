<?php
	$mysql_ip = "localhost";		
	$mysql_db = "dinbendon";		
	$mysql_user = "root";		
	$mysql_pass = "";
	$rest_table = "restaurant";
	$menu_table = "menu";
	$student_table = "student";
	$order_table = "orders";
	$class_table = "class";
	$review_table = "review";
	$EndTime ="";
	$bgimg = "./assets/img/background.jpg";
	$link = mysqli_connect($mysql_ip, $mysql_user, $mysql_pass, $mysql_db);
	mysqli_query($link, "SET NAMES utf8'");
	mysqli_query($link, "SET CHARACTER_SET_CLIENT utf8");
	mysqli_query($link, "SET CHARACTER_SET_RESULTS utf8");
	mysqli_query($link, "SET CHARACTER SET utf8");
	mysqli_query($link, "SET collate utf8_unicode_ci");
	mysqli_set_charset($link, "utf8");
	date_default_timezone_set("Asia/Taipei");
?>