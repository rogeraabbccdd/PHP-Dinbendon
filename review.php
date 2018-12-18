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
</head>

<body>
	<?php include("./assets/inc/bg.php"); ?>
    <?php include("./assets/inc/nav.php");	?>
    <div class="wrapper wrapper-full-page">
		<div class="full-page" style="padding-top: 10vh;">
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
										<table class="table col-md-auto" id="reviewtbl">
											<thead class="text-primary">
												<tr>
													<th>留言</th>
													<th>評價</th>
												</tr>
											</thead>
											<tbody>
												<?php
													$result = mysqli_query($link, "select * from ".$review_table." where res = '".$id."'");
													$numb=mysqli_num_rows($result); 
													if (!empty($numb)) 
													{ 
														while ($row = mysqli_fetch_array($result))
														{
															?>
																<tr>
																	<td class="text-left"><?=$row["comment"]?></td>
																	<td><span style="display:none"><?=$row["review"]?></span>
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
			include("./assets/js/loginform.js");
			include("./assets/js/review.js");
			include("./assets/js/l2d.js");
		?>
	});
</script>
</html>
<?php
	mysqli_close($link);
?>