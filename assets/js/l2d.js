loadlive2d("live2d", "./assets/model/wanko.model.json");
function showMessage(a,b){
    if(b==null) b = 10000;
    jQuery(".l2d-text").hide().stop();
    jQuery(".l2d-text").html(a);
    jQuery(".l2d-text").fadeTo("10",1);
    jQuery(".l2d-text").fadeOut(b);
}
(function(){
    var text;
    var SiteIndexUrl = window.location.protocol+'//'+window.location.hostname+'/';
	var SiteIndexUrl2 = SiteIndexUrl + 'index.php';
	var ResListUrl = SiteIndexUrl + 'reslist.php';
	var OrdersUrl = SiteIndexUrl + 'orders.php';
	var ResUrl = SiteIndexUrl + 'res.php';
    if(window.location.href == SiteIndexUrl || window.location.href == SiteIndexUrl2){
       text = '歡迎來到DinBenDon！請先登入哦~~~';
    }
	else if(window.location.href == ResListUrl){
		<?php if(!isset($today_res)) { ?>
	   text = '今天想吃什麼?';
		<?php } else { ?>
		text = '今天訂<?=$today_name?>這家哦~~~';
		<?php } ?>
	}
	else if(window.location.href == OrdersUrl){
		<?php if(!isset($today_res)) {?>
	   text = '今天還沒有人下訂哦~~~';
		<?php } elseif(isset($numb)) { ?>
		text = '已經有<?=$numb?>筆訂單囉~~~';
		<?php } ?>
	}
	else text = '今天想吃什麼?';

    $(".l2d").animate({top:$(window).height()-250},800);
    showMessage(text,8000);
})();
$(window).resize(function(){
    $(".l2d").css('top',window.innerHeight-250);
});
$("#live2d").mouseover(function(){
    msgs = ["汪汪","滑鼠放錯地方了啦"];
    var i = Math.floor(Math.random()*msgs.length);
    showMessage(msgs[i]);
});
$('.qtyminus').click(function(){
	<?php if(isset($today_res) && isset($id) && $id == $today_res) {?>
    var data =  $(this).closest('tr').find('.menu_name').text();
	showMessage(data + '減一份');
	<?php } else if(isset($today_res) && isset($id) && $id != $today_res) { ?>
	text = '今天訂的是<?=$today_name?>啦!';
	<?php } else {?>
	 var data =  $(this).closest('tr').find('.menu_name').text();
	showMessage(data + '減一份');
	<?php } ?>
});
$('.qtyplus').click(function(){
	<?php if(isset($today_res) && isset($id) && $id == $today_res) {?>
    var data =  $(this).closest('tr').find('.menu_name').text();
	showMessage(data + '加一份');
	<?php } else if(isset($today_res) && isset($id) && $id != $today_res) { ?>
	text = '今天訂的是<?=$today_name?>啦!';
	<?php } else {?>
	var data =  $(this).closest('tr').find('.menu_name').text();	
	showMessage(data + '加一份');
	<?php } ?>
});
$('.qty').focus(function(){
	<?php if(isset($today_res) && isset($id)  && $id == $today_res) {?>
    var data =  $(this).closest('tr').find('.menu_name').text();
	showMessage('一個便當吃不飽，你可以吃兩個');
	<?php } else if(isset($today_res) && isset($id)  && $id != $today_res) { ?>
	text = '今天訂的是<?=$today_name?>啦!';
	<?php } else {?>
	showMessage('一個便當吃不飽，你可以吃兩個');
	<?php } ?>
});
var stat_click = 0;
$("#live2d").click(function(){
	if(!ismove){
		stat_click++;
		if(stat_click>6) {
			msgs = ["你摸爽了嗎？","你已經摸我"+stat_click+"次了","110嗎，這裡有個變態一直摸我 (ó﹏ò｡)"];
			var i = Math.floor(Math.random()*msgs.length);
		}else{
			msgs = ["是…是不小心碰到了吧", "再摸的話我就要報警了！⌇●﹏●⌇","别摸我，有什麼好摸的！","小心我咬你喔！"];
			var i = Math.floor(Math.random()*msgs.length);
		}
	s = [0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.75,-0.1,-0.2,-0.3,-0.4,-0.5,-0.6,-0.7,-0.75];
	var i1 = Math.floor(Math.random()*s.length);
	var i2 = Math.floor(Math.random()*s.length);
		$(".l2d").animate({
		left:(document.body.offsetWidth-210)/2*(1+s[i1]),
		top:(window.innerHeight-240)-(window.innerHeight-240)/2*(1-s[i2])
		},
		{
			duration:500,
			complete:showMessage(msgs[i])
		});
	}else{
		ismove = false;
	}
});
var _move = false;
var ismove = false;
var _x, _y;
$(".l2d").mousedown(function(e){
	_move = true;
	_x = e.pageX-parseInt($(".l2d").css("left"));
	_y = e.pageY-parseInt($(".l2d").css("top"));
 });
$(document).mousemove(function(e){
	if(_move){
		var x = e.pageX-_x; 
		var y = e.pageY-_y;
		var wx = $(window).width()-$('.l2d').width();
		var dy = $(document).height()-$('.l2d').height();
		if(x>=0&&x<=wx&&y>0&&y<=dy){
			$(".l2d").css({
				top:y,
				left:x
			});
		ismove = true;
		}
	}
}).mouseup(function(){
	_move = false;
});