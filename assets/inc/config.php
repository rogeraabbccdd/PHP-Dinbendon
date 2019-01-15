<?php
	// 資料庫IP
	$db_host = "localhost";		
	// 資料庫名稱
	$db_name = "dinbendon";	
	// 資料庫使用者	
	$db_user = "root";		
	// 資料庫密碼
	$db_password = "";

	$rest_table = "restaurant";
	$menu_table = "menu";
	$student_table = "student";
	$order_table = "orders";
	$class_table = "class";
	$review_table = "review";

	// 統計截止時間
	$EndTime ="";
	// 背景圖
	$bgimg = "./assets/img/background.jpg";
	// 時區
	date_default_timezone_set("Asia/Taipei");

	try{
		$pdo = new PDO("mysql:host=$db_host;dbname=$db_name",$db_user,$db_password);
		$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	}
	catch(PDOException $e){
		die( $e->getMessage() ); 
	}

	$pdo->query("set names utf8");
?>