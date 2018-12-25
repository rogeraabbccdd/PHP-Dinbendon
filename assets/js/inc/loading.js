$(window).on("load", function(){
	var bar1 = new ldBar("#loading-pic");
	var load = 0;
	bar1.set(0);
	var loadtimer = setInterval(fakeload, 150);
	function fakeload(){
		load+=5;
		if(load >= 100){
			bar1.set(100);
			clearInterval(loadtimer);
			setTimeout(' $("#loading-div").fadeOut(); window.sr = ScrollReveal(); sr.reveal(".card", { duration: 1000 }, 50); ', 500);
		}
		else{
			load+=5;
			bar1.set(load);
		}
	}
})