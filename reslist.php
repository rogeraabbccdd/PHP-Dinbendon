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
	
	// admin
	if($_SESSION["class"] == 0)
	{
		echo "<script type='text/javascript'>window.location.href='./admin.php';</script>"; 
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
    <title>DinBenDon</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
    <!--  Material Dashboard CSS    -->
	<link href="./assets/css/material-dashboard.min.css" rel="stylesheet" />
	<!--     Fonts and icons     -->
	<link href="./assets/css/font-awesome.css" rel="stylesheet" />
	<link href="./assets/css/googlefonts.css" rel="stylesheet" />
	<link href="./assets/css/custom.css" rel="stylesheet" />
	<link href="./assets/css/l2d.css" rel="stylesheet" />
</head>

<body class="off-canvas-sidebar">
	<?php include("./assets/inc/bg.php"); ?>
    <?php include("./assets/inc/nav.php");	?>
    <div class="wrapper wrapper-full-page">
		<div class="full-page" style="padding-top: 10vh;">
			<div class="content">
				<div class="container"> 
					<div class="row">
						<?php
							$result=mysqli_query($link, "SELECT * FROM ".$rest_table);
							$numb=mysqli_num_rows($result); 
							if (!empty($numb)) 
							{ 
								$result2 = mysqli_query($link, "
									select (sum(case review when 1 then 1 else 0 end)/count(*))*100 as p, 
									res,
									count(*) as r, 
									sum(case review when 1 then 1 else 0 end) as y, 
									sum(case review when 0 then 1 else 0 end) as n 
									from ".$review_table." group by res");
									
								$numb2 = mysqli_num_rows($result2); 
								if (!empty($numb2)) 
								{ 
									while ($row2 = mysqli_fetch_array($result2))
									{
										$yes[$row2['res']] = round($row2['y']/$row2['r']*100, 2);
										$no[$row2['res']] = round($row2['n']/$row2['r']*100, 2);
									}
								}
								
								while ($row = mysqli_fetch_array($result))
								{
									$name = $row['name'];
									$id = $row['id'];
									
									$pic = "./assets/img/res/pic/unknown.jpg";
									
									if(!empty($row["cover"]) && file_exists("./assets/img/res/pic/".$row["cover"]))
										$pic = "./assets/img/res/pic/".$row["cover"];
									
									if(!isset($yes[$row['id']]))	$yes[$row['id']] = "-";
									if(!isset($no[$row['id']]))		$no[$row['id']] = "-";
									?>
									<div class='col-lg-4 col-md-6 col-sm-6'>
										<a href='./res.php?id=<?=$id?>'>
											<div class='card card-product'>
												<div class='card-header card-header-image' data-header-animation='true'>										
													<?php if(isset($today_res) && $id == $today_res) { ?>
														<div class='ribbon ribbon-top-left'><span>今日餐廳</span></div>
													<?php }?>
													<img class='img' src='<?=$pic?>' />	
												</div>
												<div class='card-body'>
													<div class='card-description'>
														<h4 class="card-title"><?=$name?></h4>
														<font color="#4caf50"><?=$yes[$row['id']]?>% <i class="fas fa-thumbs-up"></i></font>
														|
														<font color="#f44336"><?=$no[$row['id']]?>% <i class="fas fa-thumbs-down"></i></font>
													</div>
												</div>
											</div>
										</a>
									</div>	
									<?php
								}
								?>
									<div class='col-lg-4 col-md-6 col-sm-6' id="randombtn">
										<div class='card card-product'>
											<div class='card-header card-header-image' data-header-animation='true'>										
												<img class='img' src='assets/img/res/pic/itembox.jpg'/>	
											</div>
											<div class='card-body'>
												<div class='card-description'>
												<h4 class="card-title" style="line-height:300%">隨機</h4>
												</div>
											</div>
										</div>
									</div>
								<?php
							}
						?>
						<div class='col-lg-4 col-md-6 col-sm-6 grid-item'>
							<a href='addres.php'>
								<div class='card card-product'>
									<div class='card-header card-header-image' data-header-animation='true'>										
										<img class='img' src="./assets/img/res/pic/add.jpg"  />	
									</div>
									<div class='card-body'>
										<div class='card-description'>
											<h4 class="card-title" style="line-height:300%">新增餐廳</h4>
										</div>
									</div>
								</div>
							</a>
						</div>	
					</div>
					<footer class="footer">
						<?php include("./assets/inc/footer.php"); ?>
					</footer>
					<a id="btn-top" href="#" class="btn btn-rose btn-round btn-lg btn-top" role="button" title="回頁首" data-toggle="tooltip" data-placement="left">
						<i class="material-icons">
						arrow_upward
						</i>
					</a>
				</div>
			</div>
		</div>
    </div>
	<!--
		Edited from 
		https://codepen.io/n7best/pen/RWPpBx
	-->
	<div class="modal fade" id="raffle" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header"></div>
				<div class="modal-body text-center">
					<div class="row topbox">
						<div class="col-md-12 mx-auto rollbox">
							<div class="rollline"></div>
							<table>
								<tr id="loadout">
									<?php
										$result = mysqli_query($link, "select * from ".$rest_table);
										$numb=mysqli_num_rows($result); 
										if (!empty($numb)) 
										{ 
											$i=1;
											while ($row = mysqli_fetch_array($result))
											{
												$pic = "./assets/img/res/pic/unknown.jpg";
												if(!empty($row["cover"]) && file_exists("./assets/img/res/pic/".$row["cover"]))
													$pic = "./assets/img/res/pic/".$row["cover"];

												echo "
												<td class='items'>
													<div class='roller'>
														<img src='".$pic."' width='100%' height='100%'>
														<div>
															<p>".$row["name"]."</p>
															<span style='display:none'>".$row["id"]."</span>
														</div>
													</div>
												</td>";
												$i++;
											}
										}
									?>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer justify-content-center">
					<button class="btn btn-success btn-lg mx-auto w-75" id="roll">開始</button>	
				</div>	
				<div class="modal-footer justify-content-center py-0">	
					<p id="result">點擊按鈕開始</p>	
				</div>
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
<!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
<script src="./assets/js/plugins/nouislider.min.js"></script>
<!-- Include a polyfill for ES6 Promises (optional) for IE11, UC Browser and Android browser support SweetAlert -->
<script src="./assets/js/plugins/core.js"></script>
<!-- Library for adding dinamically elements -->
<script src="./assets/js/plugins/arrive.min.js"></script>
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
		<?php
			include("./assets/js/inc/loginform.js");
			include("./assets/js/inc/l2d.js");
			include("./assets/js/inc/raffle.js");
			if(isset($today_res))	{ ?>
				showNotification('top', 'center', 'primary ', '<a href="./res.php?id=<?=$today_res?>">今日餐廳為&nbsp;<strong style="color:yellow"><?=$today_name?></strong>，快點我訂餐！</a>');
		<?php }?>
	});
</script>
</html>
<?php
	mysqli_close($link);
?>