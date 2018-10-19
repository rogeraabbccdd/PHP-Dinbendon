	$('.qtyplus').click(function(e) {
		e.preventDefault();
		var value = parseInt($(this).parents('td').find("input[name^=qty]").val());
		if (!isNaN(value) && value >= 0 ) {
			$(this).parents('td').find("input[name^=qty]").val(value + 1);
		} else {
			$(this).parents('td').find("input[name^=qty]").val("0");
		}
	});
	$(".qtyminus").click(function(e) {
		e.preventDefault();
		var value = parseInt($(this).parents('td').find("input[name^=qty]").val());
		if (!isNaN(value) && value > 0) {
			$(this).parents('td').find("input[name^=qty]").val(value - 1);
		} else {
			$(this).parents('td').find("input[name^=qty]").val("0");
		}
	});
	$('#orderbutton').click(function(e) {
		<?php
		if(!isset($_SESSION["user"]))
		{	
			?>
			showNotification("top","center","danger","請先登入");
			<?php
		}
		else
		{
			?>
			$('#orderbutton').val('載入中...');
			$('#orderbutton').attr('disabled',true);
			// https://stackoverflow.com/questions/39151643/make-jquery-datatables-submit-all-rows-by-default-not-just-ones-displayed-upon
			var disabled =  $('#orderform').find(':input:disabled').removeAttr('disabled');
			var data = menutable.$('input').serialize();
			$.ajax({
				type: "POST",
				url: "./assets/inc/order.php",
				data: {type:'order', data:data, res:'<?=$id?>'},
				dataType: "json",
				success: function(json) {
					if (json["responce"] == "plzlogin") {
						swal({
							title: '錯誤',
							text: '請先登入!',
							type: 'error',
							confirmButtonClass: "btn btn-info",
							buttonsStyling: false
						})
					}
					else if (json["responce"] == "timeover") {
						swal({
							title: '錯誤',
							text: '統計時間結束 :(',
							type: 'error',
							confirmButtonClass: "btn btn-info",
							buttonsStyling: false
						})
					}
					else if (json["responce"] == "notres") {
						swal({
							title: '錯誤',
							text: '今天的餐廳是 '+json["responce2"]+ ' 這家',
							type: 'error',
							confirmButtonClass: "btn btn-info",
							buttonsStyling: false
						})
					}
					else if (json["responce"] == "error") {
						swal({
							title: '錯誤',
							text: '系統發生錯誤 :(',
							type: 'error',
							confirmButtonClass: "btn btn-info",
							buttonsStyling: false
						});
					}
					else if (json["responce"] == "plzorder") {
						swal({
							title: '錯誤',
							text: '沒有餐點\n\n如果要取消下訂請使用"取消訂單"按鈕',
							type: 'error',
							confirmButtonClass: "btn btn-info",
							buttonsStyling: false
						})
					}
					else if (json["responce"] == "nodata") {
						swal({
							title: '錯誤',
							text: '系統沒有收到表單資料 :(',
							type: 'error',
							confirmButtonClass: "btn btn-info",
							buttonsStyling: false
						})
					}
					else if (json["responce"] == "success") {
						swal({
							title: '成功',
							text: '請記得把 '+json["responce2"]+ ' 元交給值日生',
							type: 'success',
							confirmButtonClass: "btn btn-info",
							buttonsStyling: false
						})
					}
					disabled.attr('disabled','disabled');
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(xhr.status);
					console.log(thrownError);
				}
			});
			$('#orderbutton').val('我要點餐');
			$('#orderbutton').attr('disabled',false);
		<?php
		}
		?>
	});
	$('#cancelbutton').click(function(e) {
		<?php
		if(!isset($_SESSION["user"]))
		{	
			?>
			showNotification("top","center","danger","請先登入");
			<?php
		}
		else
		{
			?>
			swal({
				title: '你確定?',
				text: '你的訂單將被取消',
				type: 'warning',
				showCancelButton: true,
				confirmButtonText: '確定',
				cancelButtonText: '取消',
				confirmButtonClass: "btn btn-success",
				cancelButtonClass: "btn btn-danger",
				buttonsStyling: false
			}).then(function() {
				$('#cancelbutton').val('載入中');
				$('#cancelbutton').attr('disabled',true);
				$.ajax({
					type: "POST",
					url: "./assets/inc/order.php",
					data: {type:'cancel'},
					dataType: "json",
					success: function(json) {
						if (json["responce"] == "cancel") {
							swal({
								title: '成功!',
								text: '成功取消訂單!',
								type: 'success',
								confirmButtonClass: "btn btn-info",
								buttonsStyling: false
							}).then(function () {
								window.setTimeout(function () {
									window.location.reload()
								}, 1000);
							})
						}
						else if (json["responce"] == "error") {
							swal({
								title: '錯誤',
								text: '系統發生錯誤 :(',
								type: 'error',
								confirmButtonClass: "btn btn-info",
								buttonsStyling: false
							})
						}
						else if (json["responce"] == "timeover") {
							swal({
								title: '錯誤',
								text: '統計時間結束 :(',
								type: 'error',
								confirmButtonClass: "btn btn-info",
								buttonsStyling: false
							})
						}
						$(".qty").val("0");
						$(".note").val("");
					},
					error: function (xhr, ajaxOptions, thrownError) {
						console.log(xhr.status);
						console.log(thrownError);
					}
				});
				$('#cancelbutton').val('取消點餐');
				$('#cancelbutton').attr('disabled',false);
			}, function(dismiss) {
				if (dismiss === 'cancel') {
					swal({
					  title: '取消',
					  text: '你的訂單未被取消 :)',
					  type: 'error',
					  confirmButtonClass: "btn btn-info",
					  buttonsStyling: false
					})
				  }
			})
			<?php
		}
		?>
	});