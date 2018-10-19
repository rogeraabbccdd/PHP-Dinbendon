<div class="navbar-header">
	<button type="button" class="navbar-toggle" data-toggle="collapse"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
	<a class="navbar-brand" href="./index.php" style="font-weight:bold !important; font-size:30px !important;-webkit-text-fill-color:lemonchiffon"> <i class="material-icons">fastfood</i>&nbsp;DinBenDon </a>
</div>
<div class="collapse navbar-collapse">
	<ul class="nav navbar-nav navbar-right">
		<?php 
			if(!isset($_SESSION[ "user"])) 
			{ 
		?>
				<li>
					<a onclick="javascript:showNotification('top', 'center', 'danger', '請先登入');" id="" style="font-size: 20px; !important; cursor:pointer;"> <i class="fas fa-user"></i>&nbsp;未登入 </a>
				</li>
		<?php 
			}
			else 
			{ 
		?>
				<li>
					<a href="./reslist.php" style="font-size: 20px; !important"> <i class="fas fa-utensils"></i>&nbsp;餐廳 </a>
				</li>
				<li>
					<a href="./orders.php" style="font-size: 20px; !important"> <i class="fas fa-list-ul"></i>&nbsp;今日點餐 </a>
				</li>
				<li>
					<a href="javascript:void(0)" id='LogoutButton' type="button" class='' style="font-size: 20px; !important; cursor:pointer;">
						<i class="fas fa-user"></i>&nbsp;<?=$_SESSION["name"]?>
					</a>
				</li>
		<?php 
			} 
		?> 
	</ul>
</div>