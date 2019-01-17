<!doctype html>
<?php
	require_once "./assets/inc/config.php";
	require_once "./assets/inc/auth.php";
	
	// 沒登入，回首頁
	if(!isset($_SESSION["user"]))
	{
		echo "<script type='text/javascript'>window.location.href='./index.php';</script>"; 
		$pdo = null;
		exit;
	}
	
	// 沒ID，回上一頁
	if (isset($_GET['id']) && !empty($_GET['id']))	$id = $_GET['id'];
	else 
	{
		echo "<script type='text/javascript'>alert('查無餐廳ID');window.history.back();</script>"; 
		$pdo = null;
		exit;
	}
	
	// ID不是數字，回上一頁
	if(!is_numeric($id))	
	{
		echo "<script type='text/javascript'>alert('查無餐廳ID');window.history.back();</script>"; 
		$pdo = null;
		exit;
	}
	
	$sql = "SELECT * FROM ".$rest_table." WHERE id = ".$id."";
	$result = $pdo->query($sql)->fetchAll();
	if (count($result) > 0) 
	{ 
		foreach($result as $row)
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
		$pdo = null;
		exit;
	}
	
	$sql = "
	select (sum(case review when 1 then 1 else 0 end)/count(*))*100 as p, 
	count(*) as r, 
	sum(case review when 1 then 1 else 0 end) as y, 
	sum(case review when 0 then 1 else 0 end) as n 
	from ".$review_table." where res = ".$id." group by res";
	$row = $pdo->query($sql)->fetch();
	if ($row)
	{
		$yes = round($row['y']/$row['r']*100, 2);
		$no = round($row['n']/$row['r']*100, 2);
	}
	
	if(!empty($today_res) && $today_res == $id)
	{
		$sql = "
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
			ORDER BY ".$order_table.".menu_id DESC";
			
		$result = $pdo->query($sql)->fetchAll(); 
		if (count($result) > 0) 
		{ 
			foreach($result as $row)
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
    <!--  Material Dashboard CSS    -->
	<link href="./assets/css/material-dashboard.min.css" rel="stylesheet" />
	<!--     Fonts and icons     -->
	<link href="./assets/css/font-awesome.css" rel="stylesheet" />
	<link href="./assets/css/googlefonts.css" rel="stylesheet" />
	<link href="./assets/css/custom.css" rel="stylesheet" />
	<link href="./assets/css/l2d.css" rel="stylesheet" />
	<link href="./assets/css/viewbox.css" rel="stylesheet" />
	<link rel="manifest" href="./assets/others/manifest.json">
</head>

<body class="off-canvas-sidebar">
	<?php include("./assets/inc/bg.php"); ?>
    <?php include("./assets/inc/nav.php");	?>
	<div class="wrapper wrapper-full-page" style="display:none">
		<div class="" style="padding-top: 13vh;">
			<div class="content">
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							<div class="card" style="padding: 20px">
								<div class="card-body">
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
									<div class="material-datatables">
										<table id="menu" class="table col-md-auto nowrap table-rwd">
											<thead class="text-rose">
												<tr class="tr-only-hide">
													<th>名字</th>
													<th>價格</th>
													<th>數量</th>
													<th>備註</th>
												</tr>
											</thead>
											<tfoot>
												<tr>
													<th>名字</th>
													<th>價格</th>
													<th>數量</th>
													<th>備註</th>
												</tr>
											</tfoot>
											<tbody>
												<?php
													$sql = "SELECT * FROM ".$menu_table." WHERE res_id = ".$id."";
													$result = $pdo->query($sql)->fetchAll(); 
													if (count($result) > 0) 
													{ 
														foreach($result as $row)
														{
															$m_name = $row['name'];
															$m_price = $row['price'];
															$m_id = $row['id'];
															
															$qty = 0;
															$note = "";

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
																<td data-th="名字"><?=$m_name?><input type='hidden' name='id[]' value='<?=$m_id?>' /></td>
																<td data-th="價格"><input type='nmtext' value='<?=$m_price?>' disabled name='price[]'/></td>
																<td data-th="數量">
																	<input type='button' value='-' class='qtyminus btn btn-primary btn-round btn-sm'  />
																	<input type='number' name='qty[]' value='<?=$qty?>' class='qty' size='2'/>
																	<input type='button' value='+' class='qtyplus btn btn-primary btn-round btn-sm' />
																</td>
																<td data-th="備註"><input type='text' name='note[]' class="note" value="<?=$note?>"></td>
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
								<div class="card-footer text-center justify-content-center">
									<input id='orderbutton' type="button" class='btn btn-success btn-lg' style="padding: 15px 36px; font-size: 20px;" value="我要點餐">
									&emsp;
									<input id='cancelbutton' type="button" class='btn btn-danger btn-lg' style="padding: 15px 36px; font-size: 20px;" value="取消點餐">
								</div>
								</form>
							</div>
							<?php
								// 檢查有沒有訂過這家，有的話顯示評價撰寫欄
								$sql = "
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
									ORDER BY ".$order_table.".menu_id DESC";
									
								$result = $pdo->query($sql)->fetchAll(); 
								if (count($result) > 0) 
								{ 
									$text = "";
									$review = "";
									$sql = "select * from ".$review_table." where stu_num = '".$_SESSION["user"]."' and res = '".$id."'";
									$result2 = $pdo->query($sql); 
									if (count($result2) > 0) 
									{
										$row2 = $result2->fetch();
										$text = $row2["comment"];
										$review = $row2["review"];
									}
									?>
										<form id="review">
											<div class="card">
												<div class="card-header card-header-text card-header-rose">
													<div class="card-text">
														<h4 class="card-title">撰寫評價</h4>
													</div>
												</div>
												<div class="card-body text-center">
													<div class="form-group">
														<textarea name="reviewtext" id="reviewtext" style="width:100%; height:175px" maxlength="50" required="true" rows="3" class="form-control"><?=$text?></textarea>
													</div>
													<button id='yes' type="button" class='btn btn-gray btn-lg btn-round <?=(($review == "1")?"btn-success":"")?>' style="padding: 15px 36px; font-size: 20px;">
														<i class="fas fa-thumbs-up"></i>
													</button>
													<button id='no' type="button" class='btn btn-gray btn-lg btn-round <?=(($review == "0")?"btn-danger":"")?>' style="padding: 15px 36px; font-size: 20px;">
														<i class="fas fa-thumbs-down"></i>
													</button>
												</div>
												<div class="card-footer justify-content-center">
													<input id='submitreview' type="submit" class='btn btn-info btn-lg mx-1' style="padding: 15px 36px; font-size: 20px;" value="<?=(!empty($result2))?"更新":"送出"?>">
													<?php
														if (count($result2) > 0) 
														{
															?>
															<input id='delreview' type="button" class='btn btn-danger btn-lg mx-1' style="padding: 15px 36px; font-size: 20px;" value="刪除">
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
<!-- Core Js  -->					
<script src="./assets/js/core/jquery.min.js"></script>
<script src="./assets/js/core/popper.min.js"></script>
<script src="./assets/js/core/bootstrap-material-design.min.js"></script>
<script src="./assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
<!-- Notify -->
<script src="./assets/js/plugins/bootstrap-notify.js"></script>
<!--  Plugin for Sweet Alert -->
<script src="./assets/js/plugins/sweetalert2.js"></script>
<!-- Forms Validations Plugin -->
<script src="./assets/js/plugins/jquery.validate.min.js"></script>
<!--  DataTables.net Plugin, full documentation here: https://datatables.net/  -->
<script src="./assets/js/plugins/jquery.dataTables.min.js"></script>
<!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
<script src="./assets/js/plugins/nouislider.min.js"></script>
<!-- Include a polyfill for ES6 Promises (optional) for IE11, UC Browser and Android browser support SweetAlert -->
<script src="./assets/js/plugins/core.js"></script>
<!-- Library for adding dinamically elements -->
<script src="./assets/js/plugins/arrive.min.js"></script>
<!-- Custom orders for datatables -->
<script src="./assets/js/plugins/datatables-order.js"></script>
<!-- Viewbox -->
<script src="./assets/js/plugins/jquery.viewbox.js"></script>
<!-- Responsive datatables -->
<script src="./assets/js/plugins/responsive.bootstrap.js"></script>
<!-- Live 2D Plugin -->
<script src="./assets/js/plugins/live2d.js"></script>
<!-- Scrollreveal -->
<script src="./assets/js/plugins/scrollreveal.js"></script>
<!-- Custom JS -->
<script src="./assets/js/plugins/custom.js"></script>
<!-- Material dashboard JS -->
<script src="./assets/js/core/material-dashboard.min.js"></script>
<script async defer src="./assets/js/plugins/buttons.js"></script>
<!-- Loading bar JS -->
<script src="./assets/js/plugins/loading-bar.js"></script>
<script src="./assets/js/inc/loading.js"></script>
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
            "language": {
                "url": "./assets/others/datatables-chinese-traditional.json"
            },
			"lengthMenu": [
                [15, 50, -1],
                [15, 50, "全部"]
            ],
			"columnDefs": [ 
				{
			      "targets": 1,
			      "searchable": false,
				  "orderDataType": "dom-text-numeric"
			    }, 
				{
			      "targets": 2,
			      "searchable": false,
				  "orderDataType": "dom-text-numeric2"
			    }, 
				{
			      "targets": 3,
			      "searchable": false,
				  "orderDataType": "dom-text", type: 'string'
			    } 
			],
        });
		
		<?php
			include("./assets/js/inc/order.js");
			include("./assets/js/inc/loginform.js");
			include("./assets/js/inc/l2d.js");
			include("./assets/js/inc/review.js");
		?>
	});
</script>
</html>
<?php
	$pdo = null;
?>