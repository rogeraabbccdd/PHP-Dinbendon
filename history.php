<!doctype html>
<?php
	require_once "./assets/inc/config.php";
	require_once "./assets/inc/auth.php";
	
	if(!isset($_SESSION["user"]))
	{
		echo "<script type='text/javascript'>window.location.href='./index.php';</script>"; 
		$pdo = null;
		exit;
	}
	
	// admin
	if($_SESSION["class"] == 0)
	{
		echo "<script type='text/javascript'>window.location.href='./admin.php';</script>"; 
		$pdo = null;
		exit;
	}
?>
<html>
<head>
    <meta charset="utf-8" />
    <link rel="shortcut icon" href="./assets/img/icon.ico"/>
	<link rel="bookmark" href="./assets/img/icon.ico"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>DinBenDon | 訂餐紀錄</title>
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
								<div id="info" style='text-align:center;'>
									<h2><?=$_SESSION["cname"]?>的訂餐紀錄</h2>	
								</div>
								<div id='calendar' style=""></div>
								<?php
									

								?>
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
<!-- Calender JS -->
<script src="./assets/js/plugins/moment.min.js"></script>
<script src="./assets/js/plugins/fullcalendar.min.js"></script>
<script src="./assets/js/plugins/fullcalendar-locale-all.js"></script>
<script>
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

		$('#calendar').fullCalendar({
			locale:"zh-tw",
			themeSystem: "bootstrap4",
			navLinks: true,
			eventLimit: true,
			events: [
				<?php
					$sql = "SELECT
								DATE(orders.date) AS date,
								restaurant.name AS name,
								restaurant.id AS resid
							FROM
								orders,
								restaurant,
								menu,
								student,
								class
							WHERE
								student.class = class.id
								and orders.stu_num = student.id
								and restaurant.id = menu.res_id
								and orders.menu_id = menu.id
								and student.class = '".$_SESSION["class"]."'
							GROUP by
								DATE(orders.date)
							ORDER by
								orders.date";

					$result = $pdo->query($sql)->fetchAll();
					foreach($result as $row)
					{
						echo '
						{
							title: "'.$row["name"].'",
							start : "'.$row["date"].'",
							url: "./res.php?id='.$row["resid"].'",
						},';
					}
				?>
			],
			header: {
				left: 'title',
				right: 'prev,next,today'
			},
		});
			
		<?php
			include("./assets/js/inc/loginform.js");
			include("./assets/js/inc/l2d.js");
		?>
	});
</script>
</html>
<?php
	$pdo = null;
?>