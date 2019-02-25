 <nav class="navbar bg-info navbar-expand-lg fixed-top text-white border-bottom border-white">
	<div class="container">
		<div class="navbar-wrapper">
			<a class="navbar-brand" href="./index.php" style="font-weight:bold !important; font-size:30px !important;-webkit-text-fill-color:lemonchiffon"> 
				<i class="material-icons">fastfood</i>&nbsp;DinBenDon 
			</a>
		</div>
		<button class="navbar-toggler text-white" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
			<span class="sr-only">Toggle navigation</span>
			<span class="navbar-toggler-icon icon-bar"></span>
			<span class="navbar-toggler-icon icon-bar"></span>
			<span class="navbar-toggler-icon icon-bar"></span>
		</button>
		<div class="collapse navbar-collapse justify-content-end">
			<ul class="navbar-nav">
				<?php 
					if(!isset($_SESSION[ "user"])) 
					{ 
				?>
						<li class="nav-item">
							<a class='nav-link' onclick="javascript:showNotification('top', 'center', 'danger', '請先登入');" id="" style="font-size: 20px; !important; cursor:pointer;"> 
								<i class="fas fa-user"></i>&nbsp;未登入
							 </a>
						</li>
				<?php 
					}
					else 
					{ 
				?>
					<li class="nav-item">
						<a href="./reslist.php" style="font-size: 20px !important" class='nav-link  mx-0'>
							<i class="fas fa-utensils"></i>&nbsp;餐廳 
						</a>
					</li>
					<li class="nav-item">
						<a href="./orders.php" style="font-size: 20px !important" class='nav-link  mx-0'>
						<i class="fas fa-list-ul"></i>&nbsp;今日點餐 </a>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle mx-0" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 20px !important; cursor:pointer;">
							<i class="fas fa-user"></i>&nbsp;<?=$_SESSION["name"]?>
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
							<a class="dropdown-item" href="./history.php">訂餐紀錄</a>
							<a class="dropdown-item" href="javascript:void(0)" id="LogoutButton">登出</a>
						</div>
					</li>
				<?php 
					} 
				?> 
			</ul>
		</div>
	</div>
</nav>
<div id="loading-div" class="container-fluid position-fixed" style="height:100vh; z-index:1029;">
	<div class='blurbg' style='background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),url(<?=$bgimg?>);' data-filer="black"></div>
	<div class="h-100 w-100" style="display:table">
		<div class="d-table-cell align-middle">
			<div id="loading-pic" class="mx-auto" data-type="fill" data-img="./assets/img/hamburger.svg" data-img-size="100,100"></div>
		</div>
	</div>
</div>
