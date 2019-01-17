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
	$row = $pdo->query($sql)->fetch();
	if ($row) 
	{ 
		$name = $row['name'];
		$tel = $row['tel'];
		$address = $row['address'];
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
										<a href="<?="res.php?id=".$id?>"><h2><?=$name?></h2></a>
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
										<?php if(isset($pic))	echo "<a href='".$pic."' class='image-link'><img src='".$pic."' style='height:auto !important; width:500px !important'/></a>";?>
									</div>
									<div class="table-responsive material-datatables">
										<table class="table col-md-auto nowrap table-rwd" id="reviewtbl">
											<thead class="text-rose">
												<tr class="tr-only-hide">
													<th>留言</th>
													<th>評價</th>
												</tr>
											</thead>
											<tbody>
												<?php
													$sql = "select * from ".$review_table." where res = '".$id."'";
													$result = $pdo->query($sql)->fetchAll();
													if (count($result) > 0) 
													{ 
														foreach($result as $row)
														{
															?>
																<tr>
																	<td class="text-left" data-th="留言"><?=$row["comment"]?></td>
																	<td data-th="評價"><span style="display:none"><?=$row["review"]?></span>
																	<?=($row["review"] == "1")?'<font color="#4caf50"><i class="fas fa-thumbs-up"></i></font>':'<font color="#f44336"><i class="fas fa-thumbs-down"></i></font>'?></td>
																</tr>
															<?php
														}
													}
												?>
											</tbody>
										</table>
									</div>
									<br/>
									<br/>
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
		
		var reviewtbl = $('#reviewtbl').DataTable({
            "language": {
                "url": "./assets/others/datatables-chinese-traditional.json"
            },
			"lengthMenu": [
                [30, 50, -1],
                [30, 50, "全部"]
            ],
			"searching":false,
        });
			
		<?php
			include("./assets/js/inc/loginform.js");
			include("./assets/js/inc/review.js");
			include("./assets/js/inc/l2d.js");
		?>
	});
</script>
</html>
<?php
	$pdo = null;
?>