<?php
	require_once "./config.php";
	require_once "./auth.php";
	
	EscapePostData($_POST);
	
	// too large
	if($_FILES["menupic"]["size"] > 1048576 || $_FILES["cover"]["size"] > 1048576)
	{
		echo "large";
		mysqli_close($link);
		exit;
	}
	
	// has id, update
	if(!empty($_GET["id"]))
	{
		$res_id = $_GET["id"];
		
		// today res
		if(!empty($today_res) && $today_res == $res_id)
		{
			echo "today";
			mysqli_close($link);
			exit;
		}
		
		// 基本資料
		$result = mysqli_query($link, "update ".$rest_table." set name = '".$_POST["resname"]."', tel = '".$_POST["tel"]."', address = '".$_POST["address"]."' where id = '".$res_id."'");
		echo mysqli_error($link);
		
		// 更新菜單
		if(!empty($_POST["id"]))
		{
			for($i=0;$i<count($_POST["id"]);$i++)
			{
				$result = mysqli_query($link, "update ".$menu_table." set name = '".$_POST["name"][$i]."', price = '".$_POST["price"][$i]."' where id = '".$_POST["id"][$i]."'");
				echo mysqli_error($link);
			}
		}
		// 新增菜單
		if(!empty($_POST["name2"]))
		{
			for($i=0;$i<count($_POST["name2"]);$i++)
			{
				$result = mysqli_query($link, "insert into ".$menu_table." values(null, '".$res_id."', '".$_POST["name2"][$i]."', '".$_POST["price2"][$i]."')");
				echo mysqli_error($link);
			}
		}
		// 刪除菜單
		if(!empty($_POST["del"]))
		{
			foreach($_POST["del"] as $del)
			{
				mysqli_query($link, "delete from ".$menu_table." where id = '".$del."'");
				echo mysqli_error($link);
			}
		}
		
		// has cover
		if($_FILES["cover"]["size"] > 0)
		{
			$fn = md5_file($_FILES["cover"]["tmp_name"]);
			$ext = pathinfo($_FILES["cover"]["name"], PATHINFO_EXTENSION);
			copy($_FILES["cover"]["tmp_name"], "../img/res/pic/".$fn.".".$ext);
			$result = mysqli_query($link, "update ".$rest_table." set cover = '".$fn.".".$ext."' where id = '".$res_id."'");
			echo mysqli_error($link);
		}
		
		// has menu
		if($_FILES["menupic"]["size"] > 0)
		{
			$fn = md5_file($_FILES["menupic"]["tmp_name"]);
			$ext = pathinfo($_FILES["menupic"]["name"], PATHINFO_EXTENSION);
			copy($_FILES["menupic"]["tmp_name"], "../img/res/menu/".$fn.".".$ext);
			$result = mysqli_query($link, "update ".$rest_table." set menu = '".$fn.".".$ext."' where id = '".$res_id."'");
			echo mysqli_error($link);
		}
	}
	else
	{
		// 基本資料
		$result = mysqli_query($link, "insert into ".$rest_table." values (null, '".$_POST["resname"]."', '".$_POST["tel"]."', '".$_POST["address"]."', '', '')");
		echo mysqli_error($link);
		
		$res_id = mysqli_insert_id($link);
		
		// 菜單
		for($i=0;$i<count($_POST["name2"]);$i++)
		{
			$result = mysqli_query($link, "insert into ".$menu_table." values(null, '".$res_id."', '".$_POST["name2"][$i]."', '".$_POST["price2"][$i]."')");
			echo mysqli_error($link);
		}
		
		// has cover
		if($_FILES["cover"]["size"] > 0)
		{
			$fn = md5_file($_FILES["cover"]["tmp_name"]);
			$ext = pathinfo($_FILES["cover"]["name"], PATHINFO_EXTENSION);
			copy($_FILES["cover"]["tmp_name"], "../img/res/pic/".$fn.".".$ext);
			$result = mysqli_query($link, "update ".$rest_table." set cover = '".$fn.".".$ext."' where id = '".$res_id."'");
			echo mysqli_error($link);
		}
		
		// has menu
		if($_FILES["menupic"]["size"] > 0)
		{
			$fn = md5_file($_FILES["menupic"]["tmp_name"]);
			$ext = pathinfo($_FILES["menupic"]["name"], PATHINFO_EXTENSION);
			copy($_FILES["menupic"]["tmp_name"], "../img/res/menu/".$fn.".".$ext);
			$result = mysqli_query($link, "update ".$rest_table." set menu = '".$fn.".".$ext."' where id = '".$res_id."'");
			echo mysqli_error($link);
		}
	}
	
	echo "ok";
	
	mysqli_close($link);
?>