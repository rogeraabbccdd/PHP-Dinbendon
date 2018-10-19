<?php
	session_start();
	
	if (isset($_POST['login']) && isset($_POST['pass']))
	{
		require_once "./config.php";
		$number = $_POST["login"];
		$password = $_POST["pass"];
		
		$result = mysqli_query($link, "SELECT * FROM ".$student_table." WHERE number = '".$number."' AND pass = '".$password."'");
		if($result && mysqli_num_rows($result) > 0)
		{
			while($row = mysqli_fetch_array($result))
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
		mysqli_close($link);
		exit;
	}
	if (isset($_POST['logout']))
	{
		session_unset();
		session_destroy();
	}
	
	// 今日餐廳
	if(!empty($_SESSION))
	{
		$result=mysqli_query($link, "
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
		ORDER BY 
			".$rest_table.".id LIMIT 1;");

		$numb=mysqli_num_rows($result); 
		if (!empty($numb)) 
		{ 
			while ($row = mysqli_fetch_array($result))
			{
				$today_res = $row['id'];
				$today_name = $row['name'];
				$today_tel = $row['tel'];
				$today_address = $row['address'];
			}
		}
	}
?>