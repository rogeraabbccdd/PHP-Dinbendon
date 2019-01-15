<?php
	session_start();
	
	if (isset($_POST['login']) && isset($_POST['pass']))
	{
		require_once "./config.php";
		$number = $_POST["login"];
		$password = $_POST["pass"];
		
		$sql = "SELECT * FROM ".$student_table." WHERE number = '".$number."' AND pass = '".$password."'";
		$result = $pdo->query($sql)->fetchAll();
		if(!empty($result) && count($result) > 0)
		{
			foreach($result as $row)
			{
				$_SESSION['name'] = $row["name"];
				$_SESSION['class'] = $row["class"];
				$_SESSION['user'] = $row["id"];
			}
			echo "success";
		}
		else
		{
			echo "failed";
		}
		$pdo = null;
		exit;
	}
	if (isset($_POST['logout']))
	{
		session_unset();
		session_destroy();
	}
	
	// 今日餐廳
	if(!empty($_SESSION["class"]))
	{
		$sql = "
		SELECT
			".$rest_table.".*
		FROM
			".$order_table.",
			".$menu_table.",
			".$rest_table.",
			".$student_table.",
			".$class_table."
		WHERE
			".$menu_table.".id = ".$order_table.".menu_id AND 
			".$menu_table.".res_id = ".$rest_table.".id AND 
			".$order_table.".stu_num = ".$student_table.".id AND
			".$student_table.".class = ".$class_table.".id AND
			".$student_table.".class = '".$_SESSION["class"]."' AND
			DATE(".$order_table.".date) = CURDATE()
		ORDER BY ".$rest_table.".id LIMIT 1;";

		$result = $pdo->query($sql)->fetchAll();
		if (!empty($result)) 
		{ 
			foreach($result as $row)
			{
				$today_res = $row['id'];
				$today_name = $row['name'];
				$today_tel = $row['tel'];
				$today_address = $row['address'];
			}
		}
	}
?>