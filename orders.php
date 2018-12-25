<!doctype html>
<?php
	require_once "./assets/inc/config.php";
	require_once "./assets/inc/auth.php";
	
	if(!isset($_SESSION["user"]))
	{
		echo "<script type='text/javascript'>window.location.href='./index.php';</script>"; 
		mysqli_close($link);
		exit;
	}
?>
<html>
<head>
    <meta charset="utf-8" />
    <link rel="shortcut icon" href="./assets/img/icon.ico"/>
	<link rel="bookmark" href="./assets/img/icon.ico"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>DinBenDon | 今日訂單</title>
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
</head>

<body class="off-canvas-sidebar">
	<?php include("./assets/inc/bg.php"); ?>
    <?php include("./assets/inc/nav.php");	?>
    <div class="wrapper wrapper-full-page">
		<div class="full-page" style="padding-top: 10vh;">
			<div class="content">
				<div class="container">
					<div class="row">
						<div class="col-lg-12">
							<div class="card" style="padding: 20px">
								<div class="card-content">
									<?php
										if(isset($today_res) && !empty($today_res))
										{?>
											<div id="info" style='text-align:center;'>
												<a href="res.php?id=<?=$today_res?>"><h2><?=$today_name?></h2></a>
												<h4>電話:<?=$today_tel?><br>地址:<?=$today_address?></h4>
											</div>
										<?php
										}
										else
										{?>
											<div id="info" style='text-align:center;'>
												<h2>今天還沒有人訂餐喔</h2>	
											</div>
										<?php
										}
									?>
									<hr>
									<div class="material-datatables table-responsive">
										<table id="ordertable" class="table col-md-auto nowrap">
											<thead class="text-rose">
												<tr>
													<th>姓名</th>
													<th>座號</th>
													<th>便當</th>
													<th>價格</th>
													<th>數量</th>
													<th>備註</th>
												</tr>
											</thead>
											<tbody>
												<?php
													$money = 0;
													$tempDate = date("Y-m-d");
													$result=mysqli_query($link, "
													SELECT
														".$menu_table.".name AS ordername,
														".$student_table.".name AS name,
														".$student_table.".num AS num,
														".$menu_table.".price AS price,
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
														".$class_table.".id = '".$_SESSION['class']."'
													ORDER BY ".$order_table.".menu_id DESC");

													$numb=mysqli_num_rows($result); 
													if(!empty($numb)) 
													{ 
														while ($row = mysqli_fetch_array($result))
														{
															$money += $row['price']*$row['qty'];
															
															echo "<tr>
															<td>".$row['name']."</td>
															<td>".$row['num']."</td>
															<td>".$row['ordername']."</td>
															<td>".$row['price']."</td>
															<td>".$row['qty']."</td>
															<td>".$row['note']."</td>
															</tr>";						
														}
													}
												?>
											</tbody>
										</table>
									</div>
									<br>
									<p class="text-center" style="color:#FF0000">
										總金額：<?=$money?>
									</p>
								</div>
							</div>
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
		
		var ordertable = $('#ordertable').DataTable( {
			"responsive": true,
            "language": {
                "url": "./assets/others/datatables-chinese-traditional.json"
            },
			"lengthMenu": [
                [30, 50, -1],
                [30, 50, "全部"]
            ],
        });
		
		<?php
			include("./assets/js/inc/loginform.js");
			include("./assets/js/inc/l2d.js");
		?>
	});
</script>
</html>
<?php
	mysqli_close($link);
?>