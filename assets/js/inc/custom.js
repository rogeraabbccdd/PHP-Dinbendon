	function showNotification (from, align, style, text) {
		$.notify({
			icon: "notifications",
			message: text,
		}, {
			type: style,
			timer: 3000,
			placement: {
				from: from,
				align: align
			}
		});
	}
	
    $(window).scroll(function () {
		if ($(this).scrollTop() > 50) {
			$('#btn-top').fadeIn();
		} else {
			$('#btn-top').fadeOut();
		}
	});
	// scroll body to 0px on click
	$('#btn-top').click(function () {
		$('#btn-top').tooltip('hide');
		$('body,html').animate({
			scrollTop: 0
		}, 800);
		return false;
	});
        
	$('#btn-top').tooltip('show');