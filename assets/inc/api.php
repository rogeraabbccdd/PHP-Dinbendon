<?php
	require_once "./config.php";
	require_once "./auth.php";

	switch($_GET["do"])
	{
		case "addres":
			// too large
			if($_FILES["menupic"]["size"] > 1048576 || $_FILES["cover"]["size"] > 1048576)
			{
				echo "large";
				$pdo = null;
				exit;
			}
			
			// has id, update
			if($_POST["resid"] != -1)
			{
				$res_id = $_GET["id"];
				
				// today res
				if(!empty($today_res) && $today_res == $res_id)
				{
					echo "today";
					$pdo = null;
					exit;
				}
				
				// 基本資料
				$input = array(
					":resname" => $_POST["resname"],
					":tel" => $_POST["tel"],
					":address" => $_POST["address"],
					":resid" => $res_id
				);
				$sql = "update ".$rest_table." set name = :resname, tel = :tel, address = :address where id = :resid";
				$result = $pdo->prepare($sql)->execute($input);

				// 更新菜單
				if(!empty($_POST["id"]))
				{
					for($i=0;$i<count($_POST["id"]);$i++)
					{
						$input = array(
							":name" => $_POST["name"][$i],
							":price" => $_POST["price"][$i],
							":id" => $_POST["id"][$i]
						);
						$sql = "update ".$menu_table." set name = :name, price = :price where id = :id";
						$result = $pdo->prepare($sql)->execute($input);
					}
				}
				// 新增菜單
				if(!empty($_POST["name2"]))
				{
					for($i=0;$i<count($_POST["name2"]);$i++)
					{
						$input = array(
							":resid" => $res_id,
							":name2" => $_POST["name2"][$i],
							":price2" => $_POST["price2"][$i],
						);
						$sql = "insert into ".$menu_table." values(null, :resid, :name2, :price2)";
						$result = $pdo->prepare($sql)->execute($input);
					}
				}
				// 刪除菜單
				if(!empty($_POST["del"]))
				{
					foreach($_POST["del"] as $del)
					{
						$input = array(
							":del" => $del,
						);
						$sql  ="delete from ".$menu_table." where id = :del";
						$result = $pdo->prepare($sql)->execute($input);
					}
				}
				
				// has cover
				if($_FILES["cover"]["size"] > 0)
				{
					$fn = md5_file($_FILES["cover"]["tmp_name"]);
					$ext = pathinfo($_FILES["cover"]["name"], PATHINFO_EXTENSION);
					copy($_FILES["cover"]["tmp_name"], "../img/res/pic/".$fn.".".$ext);
					
					$input = array(
						":cover" => $fn.".".$ext,
						":id" => $res_id
					);
					$sql = "update ".$rest_table." set cover = :cover where id = :id";					
					$result = $pdo->prepare($sql)->execute($input);
				}
				
				// has menu
				if($_FILES["menupic"]["size"] > 0)
				{
					$fn = md5_file($_FILES["menupic"]["tmp_name"]);
					$ext = pathinfo($_FILES["menupic"]["name"], PATHINFO_EXTENSION);
					copy($_FILES["menupic"]["tmp_name"], "../img/res/menu/".$fn.".".$ext);

					$input = array(
						":menu" => $fn.".".$ext,
						":id" => $res_id
					);
					$sql = "update ".$rest_table." set menu = :menu where id = :id";					
					$result = $pdo->prepare($sql)->execute($input);
				}
			}
			else
			{
				// 基本資料
				$input = array(
					":resname" => $_POST["resname"],
					":tel" => $_POST["tel"],
					":address" => $_POST["address"],
				);
				$sql = "insert into ".$rest_table." values (null, :resname, :tel, :address, '', '')";
				$result = $pdo->prepare($sql)->execute($input);
				
				$res_id = $pdo->lastInsertId();
				
				// 菜單
				for($i=0;$i<count($_POST["name2"]);$i++)
				{
					$input = array(
						":resid" => $res_id,
						":name2" => $_POST["name2"][$i],
						":price2" => $_POST["price2"][$i],
					);
					$sql = "insert into ".$menu_table." values(null, :resid, :name2, :price2)";
					$result = $pdo->prepare($sql)->execute($input);
				}
				
				// has cover
				if($_FILES["cover"]["size"] > 0)
				{
					$fn = md5_file($_FILES["cover"]["tmp_name"]);
					$ext = pathinfo($_FILES["cover"]["name"], PATHINFO_EXTENSION);
					copy($_FILES["cover"]["tmp_name"], "../img/res/pic/".$fn.".".$ext);
					
					$input = array(
						":cover" => $fn.".".$ext,
						":id" => $res_id
					);
					$sql = "update ".$rest_table." set cover = :cover where id = :id";					
					$result = $pdo->prepare($sql)->execute($input);
	
				}
				
				// has menu
				if($_FILES["menupic"]["size"] > 0)
				{
					$fn = md5_file($_FILES["menupic"]["tmp_name"]);
					$ext = pathinfo($_FILES["menupic"]["name"], PATHINFO_EXTENSION);
					copy($_FILES["menupic"]["tmp_name"], "../img/res/menu/".$fn.".".$ext);
					
					$input = array(
						":menu" => $fn.".".$ext,
						":id" => $res_id
					);
					$sql = "update ".$rest_table." set menu = :menu where id = :id";					
					$result = $pdo->prepare($sql)->execute($input);	
				}
			}
			
			echo "ok";
			break;
		
		/*************************************************************************************************/
		case "order":
			// 沒有登入
			if(!isset($_SESSION["user"]))
			{
				$json = array(
					"responce" => "plzlogin"
				);
				
				echo json_encode($json, true);
				$pdo = null;
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
					$pdo = null;
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
					$result = $pdo->query("DELETE FROM ".$order_table." WHERE stu_num = ".$_SESSION["user"]." AND DATE(".$order_table.".date) = CURDATE()");
					$json = array(
						"responce" => "cancel"
					);
					echo json_encode($json, true);
					$pdo = null;
					exit;
				}
				// 下訂
				elseif($type == "order" && !empty($_POST["res"]))
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
						$pdo = null;
						exit;
					}
					
					// 清除舊訂單
					$result = $pdo->query("DELETE FROM ".$order_table." WHERE stu_num = ".$_SESSION["user"]." AND DATE(".$order_table.".date) = CURDATE()");
					
					// 下訂
					$total = count($_POST["id"]);
					$pdo->query("SET NAMES utf8");
					
					$hasorder = false;
					$money = "0";
					
					for($i=0; $i<$total; $i++)
					{
						// 如果數量為1
						if($_POST["qty"][$i] > 0)
						{
							$hasorder = true;
							$money += $_POST["price"][$i]*$_POST["qty"][$i];

							$input = array(
								":id" => $_POST["id"][$i],
								":qty" => $_POST["qty"][$i],
								":note" => $_POST["note"][$i],
							);
							$sql = "INSERT INTO ".$order_table." VALUES (NULL, '".$date."', '".$_SESSION["user"]."', :id, :qty, :note)";					
							$result = $pdo->prepare($sql)->execute($input);	
						}
					}
					
					if($hasorder)	
					{
						if($result)
						{
							$json = array(
								"responce" => "success",
								"responce2" => $money
							);
						}
						else
						{
							$err = $pdo->errorInfo();
							$json = array(
								"responce" => $err,
							);
						}
						echo json_encode($json, true);
						$pdo = null;
						exit;
					}
					else
					{
						$json = array(
							"responce" => "plzorder"
						);
						
						echo json_encode($json, true);
						$pdo = null;
						exit;
					}
				}
				else
				{
					$json = array(
						"responce" => "error"
					);
					
					echo json_encode($json, true);
					$pdo = null;
					exit;
				}
			}
			else
			{
				$json = array(
					"responce" => "nodata"
				);
				
				echo json_encode($json, true);
				$pdo = null;
				exit;
			}
			break;
		
		/*************************************************************************************************/
		case "review":
			if(!empty($_POST))
			{
				// del
				if($_POST["yn"] == "-1")
				{
					$input = array(
						":res" => $_POST["res"]
					);
					$sql = "delete from ".$review_table." where stu_num = '".$_SESSION["user"]."' and res = :res";					
					$result = $pdo->prepare($sql);
					$result->execute($input);
					if($pdo->rowCount() > 0)	echo "s";
					else echo "n";
				}
				else
				{
					$input = array(
						":res" => $_POST["res"],
						":text" => $_POST["text"],
						":yn" => $_POST["yn"],
					);
					$sql = "update ".$review_table." set comment = :text, review = :yn where stu_num = '".$_SESSION["user"]."' and res = :res";					
					$result = $pdo->prepare($sql);
					$result->execute($input);
					if($result->rowCount() > 0)	echo "s";
					else
					{
						
						$input = array(
							":res" => $_POST["res"],
							":yn" => $_POST["yn"],
							":text" => $_POST["text"]
						);
						$sql = "insert into ".$review_table." values(null, '".$_SESSION["user"]."', :res, :yn, :text)";					
						$result = $pdo->prepare($sql);
						$result->execute($input);
						if($pdo->lastInsertId() > -1)	echo "s";
						else echo "error";
					}
				}
			}
			break;
	}

	$pdo = null;
?>