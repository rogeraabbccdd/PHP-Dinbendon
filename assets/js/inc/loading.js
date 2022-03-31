$(window).on("load", function(){
	var bar1 = new ldBar("#loading-pic");
	var load = 0;
	bar1.set(0);
	var loadtimer = setInterval(fakeload, 100);
	function fakeload(){
		load+=7;
		if(load >= 100){
			bar1.set(100);
			clearInterval(loadtimer);
			setTimeout(loadcomplete, 500);
		}
		else{
			load+=7;
			bar1.set(load);
		}
	}
})

function loadcomplete(){
	$("#loading-div").fadeOut(function(){
		$(".wrapper").fadeIn();
		let tl = new TimelineMax()
		tl.staggerFrom(".card", 0.5, {
			opacity:0,
			cycle: {
				y:function(){
					return -100;
				}
			},
		}, 0.1, null, function () {
			$($.fn.dataTable.tables(true)).DataTable().columns.adjust()
		})
		tl.timeScale(0.5);
	}); 
}