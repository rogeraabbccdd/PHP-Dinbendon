<!doctype html>
<?php
	require_once "./assets/inc/config.php";
	require_once "./assets/inc/auth.php";
	
	if(isset($_SESSION["user"]))
	{
		echo "<script type='text/javascript'>window.location.href='./reslist.php';</script>"; 
		mysqli_close($link);
		exit;
	}
?>
<html>

<head>
	<meta charset="utf-8" />
	<link rel="shortcut icon" href="./assets/img/icon.ico" />
	<link rel="bookmark" href="./assets/img/icon.ico" />
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
	<link href="./assets/css/loading-bar.css" rel="stylesheet" />
	<link rel="manifest" href="./assets/others/manifest.json">
</head>

<body class="off-canvas-sidebar">
	<?php include("./assets/inc/bg.php"); ?>
	<?php include("./assets/inc/nav.php");	?>
	<div class="wrapper wrapper-full-page" style="display:none">
		<div class="page-header login-page" style="">
			<div class="container">
				<div class="row">
					<div class="col-lg-4 col-md-6 col-sm-8 ml-auto mr-auto">
						<form id="loginform" class="" name="loginform" action="#">
							<div class="card card-login" style="padding: 20px">
								<div class="card-header text-center" style="margin-top:-12px !important">
									<h2 class="modal-title">登入</h2>
								</div>
								<div class="card-body">
									<span class="bmd-form-group">
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="material-icons">face</i>
												</span>
											</div>
											<input type="text" class="form-control" name="loginnumber" id="loginnumber" required="true" placeholder="帳號...">
										</div>
									</span>
									<span class="bmd-form-group">
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="material-icons">lock</i>
												</span>
											</div>
											<input type="password" class="form-control" name="loginpass" id="loginpass" required="true" maxlength="4"
											 placeholder="密碼...">
										</div>
									</span>
								</div>
								<br>
								<div class="card-footer justify-content-center">
									<button type="submit" class="btn btn-rose btn-wd btn-lg" id="SubmitLogin">登入</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<footer class="footer position-absolute" style="bottom:0 !important">
				<?php include("./assets/inc/footer.php"); ?>
			</footer>
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
	$(document).ready(function () {
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