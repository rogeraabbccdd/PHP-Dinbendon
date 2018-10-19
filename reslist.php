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
    <title>DinBenDon</title>
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
		<div class="full-page" style="padding-top: 13vh;">
			<div class="content">
				<div class="container">
					<div class="grid">
						<?php
							$result=mysqli_query($link, "SELECT * FROM ".$rest_table."");
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
									<div class='col-lg-4 col-md-6 col-sm-6 grid-item'>
										<a href='./res.php?id=<?=$id?>'>
										<div class='card card-product'>
											<div class='card-image' data-header-animation='true'>										
												<?php if(isset($today_res) && $id == $today_res) { ?>
													<div class='ribbon ribbon-top-left'><span>今日餐廳</span></div>
												<?php }?>
												<img class='img' src='<?=$pic?>' width="625" height="275" />	
											</div>
											<div class='card-content'>
												<div class='card-description'>
													<?=$name?><br>
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
							}
						?>
						<div class='col-lg-4 col-md-6 col-sm-6 grid-item'>
							<a href='addres.php'>
								<div class='card card-product'>
									<div class='card-image' data-header-animation='true'>										
										<img class='img' src="./assets/img/res/pic/add.jpg" width="625" height="275" />	
									</div>
									<div class='card-content'>
										<div class='card-description'>
											<span style="line-height:300%">新增餐廳</span>
										</div>
									</div>
								</div>
							</a>
						</div>	
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
<script src="./assets/js/live2d.js"></script>
<script src="./assets/js/custom.js"></script>
<script src="./assets/js/scrollreveal.js"></script>
<script type="text/javascript">
	$(document).ready( function () {
		$('#menu').dataTable( {
            "language": {
                "url": "./assets/others/datatables-chinese-traditional.json"
            },
			"lengthMenu": [
                [20, 50, -1],
                [20, 50, "全部"]
            ],
			"columnDefs": [ 
				{
			      "targets": 2,
			      "searchable": false,
				  "orderable": false
			    }, 
			]
        });
		
		<?php
			include("./assets/js/loginform.js");
			include("./assets/js/l2d.js");
		?>
		
		window.sr = ScrollReveal();
		sr.reveal('.card', { duration: 1000 }, 50);
	});
</script>
</html>
<?php
	mysqli_close($link);
?>