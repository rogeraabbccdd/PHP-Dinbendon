	$("#yes").on("click", function(e){
		if(!$(this).hasClass("btn-success")){
			$(this).addClass("btn-success");
		}
		else
		{
			$(this).removeClass("btn-success");
		}
		
		if($("#no").hasClass("btn-danger")){
			$("#no").removeClass("btn-danger");
		}
	})
	
	$("#no").on("click", function(e){
		if(!$(this).hasClass("btn-danger")){
			$(this).addClass("btn-danger");
		}
		else
		{
			$(this).removeClass("btn-danger");
		}
		
		if($("#yes").hasClass("btn-success")){
			$("#yes").removeClass("btn-success");
		}
	})
	
	$("#review").on("submit", function(e){
		e.preventDefault();
		var text = $("#reviewtext").val();
		if(!text)
		{
			swal({
				title: '錯誤',
				text: '請輸入評論內容',
				type: 'error',
				confirmButtonClass: "btn btn-info",
				buttonsStyling: false
			})
		}
		else
		{
			// no
			if($("#no").hasClass("btn-danger") && 
			!$("#yes").hasClass("btn-success")){
				submitreview("0");
			}
			// yes
			else if(!$("#no").hasClass("btn-danger") && 
			$("#yes").hasClass("btn-success")){
				submitreview("1");
			}
			else
			{
				swal({
					title: '錯誤',
					text: '請選擇是否推薦',
					type: 'error',
					confirmButtonClass: "btn btn-info",
					buttonsStyling: false
				})
			}
		}
	})
	
	function submitreview(yn){
		var text = $("#reviewtext").val();
		$("#submitreview").attr("disabled");
		var res = <?=$id?>;
		$.post("./assets/inc/api.php?do=review", {text, yn, res}, function(r){
			if(r == "s")
			{
				swal({
					title: '成功',
					text: '謝謝你的評價',
					type: 'success',
					confirmButtonClass: "btn btn-info",
					buttonsStyling: false
				}).then(function () {
					window.setTimeout(function () {
						window.location.reload()
					}, 1000);
				})
			}
			else
			{
				swal({
					title: '錯誤',
					text: r,
					type: 'error',
					confirmButtonClass: "btn btn-info",
					buttonsStyling: false
				})
			}
		})
		$("#submitreview").removeAttr("disabled");
	}
	
	$("#delreview").on("click", function(e){
		swal({
			title: '你確定?',
			text: '你的評價將被刪除',
			type: 'warning',
			showCancelButton: true,
			confirmButtonText: '確定',
			cancelButtonText: '取消',
			confirmButtonClass: "btn btn-success",
			cancelButtonClass: "btn btn-danger",
			buttonsStyling: false
		}).then(function() {
			var yn = "-1";
			var res = <?=$id?>;
			$.post("./assets/inc/review.php", {yn, res}, function(r){
				if(r == "s")
				{
					swal({
						title: '成功',
						text: '成功刪除評價',
						type: 'success',
						confirmButtonClass: "btn btn-info",
						buttonsStyling: false
					}).then(function () {
						window.setTimeout(function () {
							window.location.reload()
						}, 1000);
					})
				}
				else
				{
					swal({
						title: '錯誤',
						text: r,
						type: 'error',
						confirmButtonClass: "btn btn-info",
						buttonsStyling: false
					})
				}
			})
		}, function(dismiss) {
			if (dismiss === 'cancel') {
				swal({
				  title: '取消',
				  text: '你的評價未被刪除 :)',
				  type: 'error',
				  confirmButtonClass: "btn btn-info",
				  buttonsStyling: false
				})
			  }
		})
	})
	