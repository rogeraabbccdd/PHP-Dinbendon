<?php
	require_once "./config.php";
	require_once "./auth.php";
	
	// 沒有登入
	if(!isset($_SESSION["user"]))
	{
		$json = array(
			"responce" => "plzlogin"
		);
		
		echo json_encode($json, true);
		mysqli_close($link);
		exit;
	}

	if(!empty($EndTime))
	{
		if (time() >= strtotime($EndTime)) 
		{
			$json = array(
				"responce" => "timeover"
			);
			
			echo json_encode($json, true);
			mysqli_close($link);
			exit;
		}
	}
	if(!empty($_POST["type"]))
	{
		$type = $_POST["type"];
		$date = date('Y-m-d H:i:s');
		
		// 取消訂單
		if($type == "cancel")
		{
			$result=mysqli_query($link, "DELETE FROM ".$order_table." WHERE stu_num = ".$_SESSION["user"]." AND DATE(".$order_table.".date) = CURDATE()");
			$json = array(
				"responce" => "cancel"
			);
			echo json_encode($json, true);
			mysqli_close($link);
			exit;
		}
		// 下訂
		elseif($type == "order" && !empty($_POST["data"]) && !empty($_POST["res"]))
		{
			$order_res = $_POST['res'];
			
			// 今天不是這家
			if(!empty($today_res) && $order_res != $today_res)
			{
				$json = array(
					"responce" => "notres",
					"responce2" => $today_name
				);
				
				echo json_encode($json, true);
				mysqli_close($link);
				exit;
			}
			
			// 清除舊訂單
			$result=mysqli_query($link, "DELETE FROM ".$order_table." WHERE stu_num = ".$_SESSION["user"]." AND DATE(".$order_table.".date) = CURDATE()");
			
			// 下訂
			$orders = array();
			parse_str($_POST["data"], $orders);
			$total = count($orders["id"]);
			mysqli_query($link, "SET NAMES utf8");
			
			$order_count = "0";
			$money = "0";
			
			// 建立下訂SQL
			$query = "INSERT INTO ".$order_table." VALUES ";
			
			// 迴圈每筆訂單資料
			for($i=0; $i<$total; $i++)
			{
				// 如果數量為1
				if($orders["qty"][$i] > 0)
				{
					$query .= "(NULL, '".$date."', '".$_SESSION["user"]."', '".$orders["id"][$i]."', '".$orders["qty"][$i]."', '".$orders["note"][$i]."'),";
					$order_count++;
					$money += $orders["price"][$i]*$orders["qty"][$i];
				}
			}
			
			if($order_count > 0)	
			{
				$query = substr($query, 0, -1);
				$result = mysqli_query($link, $query);
				
				if($result)
				{
					$json = array(
						"responce" => "success",
						"responce2" => $money
					);
				}
				else
				{
					$json = array(
						"responce" => "error",
					);
				}
				echo json_encode($json, true);
				mysqli_close($link);
				exit;
			}
			else
			{
				$json = array(
					"responce" => "plzorder"
				);
				
				echo json_encode($json, true);
				mysqli_close($link);
				exit;
			}
		}
		else
		{
			$json = array(
				"responce" => "error"
			);
			
			echo json_encode($json, true);
			mysqli_close($link);
			exit;
		}
	}
	else
	{
		$json = array(
			"responce" => "nodata"
		);
		
		echo json_encode($json, true);
		mysqli_close($link);
		exit;
	}
?>