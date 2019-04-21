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
				$res_id = $_POST["resid"];
				
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
					move_uploaded_file($_FILES["cover"]["tmp_name"], "../img/res/pic/".$fn.".".$ext);
					
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
					move_uploaded_file($_FILES["menupic"]["tmp_name"], "../img/res/menu/".$fn.".".$ext);

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
					move_uploaded_file($_FILES["cover"]["tmp_name"], "../img/res/pic/".$fn.".".$ext);
					
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
					move_uploaded_file($_FILES["menupic"]["tmp_name"], "../img/res/menu/".$fn.".".$ext);
					
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
		
		/*************************************************************************************************/
		case "getorder":
			$data = array();
			if(!empty($_SESSION["user"]))
			{
				$sql = "
				SELECT
					".$menu_table.".name AS ordername,
					".$menu_table.".price AS price,
					SUM(".$order_table.".qty) AS qty,
					".$order_table.".menu_id AS id,
					GROUP_CONCAT(".$student_table.".name) AS sname,
					GROUP_CONCAT(".$student_table.".num) AS snum,
					GROUP_CONCAT(".$order_table.".qty) AS q,
					GROUP_CONCAT(".$order_table.".note) AS n
				FROM
					".$order_table.",
					".$menu_table.",
					".$class_table.",
					".$student_table."
				WHERE
					".$menu_table.".id = ".$order_table.".menu_id AND 
					DATE(".$order_table.".date) = CURDATE() AND
					".$student_table.".class = ".$class_table.".id AND
					".$order_table.".stu_num = ".$student_table.".id AND
					".$class_table.".id = '".$_SESSION['class']."'
				GROUP BY ".$menu_table.".name 
				ORDER BY ".$order_table.".menu_id DESC";

				$result = $pdo->query($sql)->fetchAll(); 
				if (count($result) > 0) 
				{ 
					$recordsTotal = count($result);
					foreach($result as $row)
					{
						
						$people = "";
						$template = '
						<div class="my-1 d-inline-block" data-toggle="popover" 
							data-placement="top"  
							data-trigger="hover" 
								data-container="body"
								data-content="NOTETEXT">
							<div class="d-inline bg-secondary text-white p-1" style=" border-radius: 25px 0px 0 25px;">NUMBER</div>
							
								<div class="d-inline bg-HASNOTE text-white p-1" style="margin-left:-4px" >NAME</div>
							
							<div class="d-inline bg-info text-white p-1" style="border-radius: 0 25px 25px 0;margin-left:-4px">xQTY</div>
						</div>';

						$sname = explode(",", $row["sname"]);
						$snum = explode(",", $row["snum"]);
						$q = explode(",", $row["q"]);
						$n = explode(",", $row["n"]);

						for($i=0;$i<count($sname);$i++){
							$snum[$i] = str_pad($snum[$i], 2, 0, STR_PAD_LEFT);
							$holder = array("NOTETEXT", "NUMBER", "HASNOTE", "NAME", "QTY");
							$hasnote = (empty($n[$i]))?"success":"danger";
							$value   = array($n[$i], $snum[$i], $hasnote, $sname[$i], $q[$i]);
							$people .= str_replace($holder, $value, $template);
						}

						array_push($data, array('name' => $row["ordername"], 'count' => $row["qty"], 'price' => $row["price"], 'people' => $people));
					}
				}
			}
			else
			{
				array_push($data, array('name' => "錯誤", 'count' => 0, 'price' => 0, 'people' => "請重新登入"));
			}
			$return = array("data"=>$data);
			echo json_encode($return, JSON_UNESCAPED_UNICODE);
			break;
	}

	$pdo = null;
?>