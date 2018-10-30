<!doctype html>
<?php
	require_once "./assets/inc/config.php";
	require_once "./assets/inc/auth.php";
	
	// 沒登入，回首頁
	if(!isset($_SESSION["user"]))
	{
		echo "<script type='text/javascript'>window.location.href='./index.php';</script>"; 
		mysqli_close($link);
		exit;
	}
	
	// 沒ID，回上一頁
	if (isset($_GET['id']) && !empty($_GET['id']))	$id = $_GET['id'];
	else 
	{
		echo "<script type='text/javascript'>alert('查無餐廳ID');window.history.back();</script>"; 
		mysqli_close($link);
		exit;
	}
	
	// ID不是數字，回上一頁
	if(!is_numeric($id))	
	{
		echo "<script type='text/javascript'>alert('查無餐廳ID');window.history.back();</script>"; 
		mysqli_close($link);
		exit;
	}
	
	$result=mysqli_query($link, "SELECT * FROM ".$rest_table." WHERE id = ".$id."");
	$numb=mysqli_num_rows($result); 
	if (!empty($numb)) 
	{ 
		while ($row = mysqli_fetch_array($result))
		{
			$name = $row['name'];
			$tel = $row['tel'];
			$address = $row['address'];
			
			if(!empty($row["menu"]) && file_exists("./assets/img/res/menu/".$row["menu"]))
				$pic = "./assets/img/res/menu/".$row["menu"];
		}
	}
	else
	{
		echo "<script type='text/javascript'>alert('查無餐廳ID');window.history.back();</script>"; 
		mysqli_close($link);
		exit;
	}
	
	$result=mysqli_query($link, "
	select (sum(case review when 1 then 1 else 0 end)/count(*))*100 as p, 
	count(*) as r, 
	sum(case review when 1 then 1 else 0 end) as y, 
	sum(case review when 0 then 1 else 0 end) as n 
	from ".$review_table." where res = ".$id." group by res");
	$numb=mysqli_num_rows($result); 
	if (!empty($numb)) 
	{ 
		$row = mysqli_fetch_array($result);

		$yes = round($row['y']/$row['r']*100, 2);
		$no = round($row['n']/$row['r']*100, 2);
	}
	
	if(!empty($today_res) && $today_res == $id)
	{
		$result = mysqli_query($link, "
			SELECT
				".$order_table.".note AS note,
				".$order_table.".qty AS qty,
				".$order_table.".menu_id AS id
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
				".$class_table.".id = '".$_SESSION['class']."' AND
				".$order_table.".stu_num = '".$_SESSION['user']."'
			ORDER BY ".$order_table.".menu_id DESC");
			
		$numb=mysqli_num_rows($result); 
		if (!empty($numb)) 
		{ 
			while ($row = mysqli_fetch_array($result))
			{
				$orders["id"][] = $row["id"];
				$orders["qty"][] = $row["qty"];
				$orders["note"][] = $row["note"];
			}
		}
	}
?>
<html>
<head>
    <meta charset="utf-8" />
    <link rel="shortcut icon" href="./assets/img/icon.ico"/>
	<link rel="bookmark" href="./assets/img/icon.ico"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>DinBenDon | <?=$name?></title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
    <!-- Bootstrap core CSS     -->
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet" />
    <!--  Material Dashboard CSS    -->
    <link href="./assets/css/material-dashboard.css" rel="stylesheet" />
    <!--     Fonts and icons     -->
    <link href="./assets/css/fontawesome-all.css" rel="stylesheet" />
    <link href="./assets/css/google-roboto-300-700.css" rel="stylesheet" />
	<link href="./assets/css/custom.css" rel="stylesheet" />
	<link href="./assets/css/viewbox.css" rel="stylesheet" />
	<link href="./assets/css/l2d.css" rel="stylesheet" />
</head>

<body>
	<?php include("./assets/inc/bg.php"); ?>
    <nav class="navbar navbar-info navbar-absolute">
        <div class="container">
			<?php include("./assets/inc/nav.php");	?>
        </div>
    </nav>
    <div class="wrapper wrapper-full-page">
		<div class="full-page" style="padding-top: 10vh;">
			<div class="content">
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							<div class="card" style="padding: 20px">
								<div class="card-content">
									<div id="info" style='text-align:center;'>
										<h2><?=$name?>
											<a href="./addres.php?id=<?=$id?>"><i class="material-icons">edit</i></a>
										</h2>	
										<h4>
											電話:<?=$tel?><br>
											地址:<?=$address?><br>
											<?php
												if(isset($yes) && isset($no))
												{
													?>
													<a href="review.php?id=<?=$id?>"><font color="#4caf50"><?=$yes?>% <i class="fas fa-thumbs-up"></i></font>
													|
													<font color="#f44336"><?=$no?>% <i class="fas fa-thumbs-down"></i></font></a>
													<?php
												}
												else echo "<font color='#00bcd4'>這間餐廳還沒有評價 :(</font>"
											?>
										</h4>
										<?php if(isset($pic))	echo "<a href='".$pic."' class='image-link'><img src='".$pic."' style='width:60% !important'/></a>";?>
									</div>
									<br/>
									<br/>
									<form id="orderform">
									<div class="material-datatables table-responsive">
										<table id="menu" class="table col-md-auto">
											<thead class="text-rose">
												<tr>
													<th style="display:none"></th>
													<th>名字</th>
													<th>價格</th>
													<th>數量</th>
													<th>備註</th>
												</tr>
											</thead>
											<tfoot>
												<tr>
													<th style="display:none"></th>
													<th>名字</th>
													<th>價格</th>
													<th>數量</th>
													<th>備註</th>
												</tr>
											</tfoot>
											<tbody>
												<?php
													$result=mysqli_query($link, "SELECT * FROM ".$menu_table." WHERE res_id = ".$id."");
													$numb=mysqli_num_rows($result); 
													if (!empty($numb)) 
													{ 
														while ($row = mysqli_fetch_array($result))
														{
															$m_name = $row['name'];
															$m_price = $row['price'];
															$m_id = $row['id'];
															
															$qty = 0;
															$note = "";

															// https://stackoverflow.com/questions/2581619/php-what-does-array-search-return-if-nothing-was-found
															if(!empty($orders))
															{
																$key = array_search($m_id, $orders["id"]);
																if ($key !== false) 
																{
																	$qty = $orders["qty"][$key];
																	$note = $orders["note"][$key];
																} 
															}
															?>
															<tr>
																<td style='display:none'><input type='hidden' name='id[]' value='<?=$m_id?>' /></td>
																<td class="menu_name"><?=$m_name?></td>
																<td><input type='nmtext' value='<?=$m_price?>' disabled name='price[]'/></td>
																<td>
																	<input type='button' value='-' class='qtyminus btn btn-primary btn-round btn-xs'  />
																	<input type='text' name='qty[]' value='<?=$qty?>' class='qty' size='2'/>
																	<input type='button' value='+' class='qtyplus btn btn-primary btn-round btn-xs' />
																</td>
																<td><input type='text' name='note[]' class="note" value="<?=$note?>"></td>
															</tr>
															<?php
														}
													}
												?>
											</tbody>
										</table>
									</div>
									<?php 
									if(!empty($EndTime)) 
									{ ?>
										<p class="text-center" style="color:#FF0000">
											統計截止時間：<?=$EndTime?><br>
											統計時間結束後無法點餐及取消
										</p>
									<?php }?>
								</div>
								<div class="card-footer text-center">
									<input id='orderbutton' type="button" class='btn btn-success btn-lg' style="padding: 15px 36px; font-size: 20px;" value="我要點餐">
									<input id='cancelbutton' type="button" class='btn btn-danger btn-lg' style="padding: 15px 36px; font-size: 20px;" value="取消點餐">
								</div>
								</form>
							</div>
							<?php
								// 檢查有沒有訂過這家，有的話顯示評價撰寫欄
								$result = mysqli_query($link, "
									SELECT
										".$order_table.".note AS note,
										".$order_table.".qty AS qty,
										".$order_table.".menu_id AS id
									FROM
										".$order_table.",
										".$menu_table.",
										".$class_table.",
										".$student_table."
									WHERE
										".$menu_table.".id = ".$order_table.".menu_id AND 
										".$student_table.".class = ".$class_table.".id AND
										".$order_table.".stu_num = ".$student_table.".id AND
										".$class_table.".id = '".$_SESSION['class']."' AND
										".$order_table.".stu_num = '".$_SESSION['user']."' AND
										".$menu_table.".res_id = '".$id."'
									ORDER BY ".$order_table.".menu_id DESC");
									
								$numb=mysqli_num_rows($result); 
								if (!empty($numb)) 
								{ 
									$text = "";
									$review = "";
									$result2 = mysqli_query($link, "select * from ".$review_table." where stu_num = '".$_SESSION["user"]."' and res = '".$id."'");
									$numb2=mysqli_num_rows($result2); 
									if(!empty($numb2))
									{
										$row2 = mysqli_fetch_array($result2);
										$text = $row2["comment"];
										$review = $row2["review"];
									}
									?>
										<form id="review">
											<div class="card">
												<div class="card-header card-header-text" data-background-color="rose">
														<h4 class="card-title">撰寫評價</h4>
														<p class="category"></p>
												</div>
												<div class="card-content text-center">
													<textarea name="reviewtext" id="reviewtext" style="width:100%; height:175px" maxlength="50" required="true"><?=$text?></textarea>
													<button id='yes' type="button" class='btn btn-gray btn-lg btn-round <?=(($review == "1")?"btn-success":"")?>' style="padding: 15px 36px; font-size: 20px;">
														<i class="fas fa-thumbs-up"></i>
													</button>
													<button id='no' type="button" class='btn btn-gray btn-lg btn-round <?=(($review == "0")?"btn-danger":"")?>' style="padding: 15px 36px; font-size: 20px;">
														<i class="fas fa-thumbs-down"></i>
													</button>
												</div>
												<div class="card-footer text-center">
													<input id='submitreview' type="submit" class='btn btn-info btn-lg' style="padding: 15px 36px; font-size: 20px;" value="<?=(!empty($numb2))?"更新評價":"送出"?>">
													<?php
														if(!empty($numb2))
														{
															?>
															<input id='delreview' type="button" class='btn btn-danger btn-lg' style="padding: 15px 36px; font-size: 20px;" value="刪除評價">
															<?php
														}
													?>
												</div>
											</div>
										</form>
									<?php
								}
							?>
						</div>
					</div>
				</div>
				<footer class="footer">
					<?php include("./assets/inc/footer.php"); ?>
				</footer>
			</div>
		</div>
    </div>
</body>

<!--   Core JS Files   -->
<script src="./assets/js/jquery-3.1.1.min.js" type="text/javascript"></script>
<script src="./assets/js/jquery-ui.min.js" type="text/javascript"></script>
<script src="./assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="./assets/js/material.min.js" type="text/javascript"></script>
<script src="./assets/js/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
<!-- Forms Validations Plugin -->
<script src="./assets/js/jquery.validate.min.js"></script>
<!-- Sliders Plugin -->
<script src="./assets/js/nouislider.min.js"></script>
<!--  DataTables.net Plugin    -->
<script src="./assets/js/jquery.datatables.js"></script>
<!-- Sweet Alert 2 plugin -->
<script src="./assets/js/sweetalert2.js"></script>
<!-- Material Dashboard javascript methods -->
<script src="./assets/js/material-dashboard.js"></script>
<script src="./assets/js/jquery.viewbox.js"></script>
<script src="./assets/js/responsive.bootstrap.js"></script>
<script src="./assets/js/live2d.js"></script>
<script src="./assets/js/custom.js"></script>
<script src="./assets/js/datatables-order.js"></script>
<script type="text/javascript">
	$(document).ready( function () {
		$(function(){
			$('.image-link').viewbox({
				setTitle: true,
				margin: 20,
				resizeDuration: 300,
				openDuration: 200,
				closeDuration: 200,
				closeButton: true,
				navButtons: true,
				closeOnSideClick: true,
				nextOnContentClick: true
			});
		});

		var menutable = $('#menu').DataTable( {
			"responsive": true,
            "language": {
                "url": "./assets/others/datatables-chinese-traditional.json"
            },
			"lengthMenu": [
                [15, 50, -1],
                [15, 50, "全部"]
            ],
			"columnDefs": [ 
				{
			      "targets": 0,
			      "searchable": false,
				  "orderDataType": "dom-text-numeric",
				  "visible": false
			    }, 
				{
			      "targets": 2,
			      "searchable": false,
				  "orderDataType": "dom-text-numeric"
			    }, 
				{
			      "targets": 3,
			      "searchable": false,
				  "orderDataType": "dom-text-numeric2"
			    }, 
				{
			      "targets": 4,
			      "searchable": false,
				   "orderDataType": "dom-text", type: 'string'
			    } 
			],
        });
		
		<?php
			include("./assets/js/order.js");
			include("./assets/js/loginform.js");
			include("./assets/js/l2d.js");
			include("./assets/js/review.js");
		?>
	});
</script>
</html>
<?php
	mysqli_close($link);
?>