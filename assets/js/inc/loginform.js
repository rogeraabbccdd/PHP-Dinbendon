	$('#LoginButton').on('click', function() {
		$('#loginform')[0].reset();
		$('.form-group').removeClass('has-error');
		$('.help-block').empty();
		$('#modal_form').modal('show');
	});
	
	var loginform = $('#loginform');
		loginform.validate({
			rules: {
				loginnumber: { required: true, minlength: 4, minlength: 4},
				loginpass: { required: true, minlength: 4, minlength: 4},
			},
			errorPlacement: function(error, element) {
				$(element).parent('div').addClass('has-error');
			},
		});
		
	loginform.on('submit', function(e) {
		e.preventDefault();
		if(loginform.valid())
		{
			var val = $('#loginnumber').val();
			var val2 = $('#loginpass').val();
			$('#SubmitLogin').text('登入中...');
			$('#SubmitLogin').attr('disabled',true);
			$.ajax({
				type: "post",
				url: "./assets/inc/auth.php",
				data: {	login: val, pass: val2},
				dataType: "text",
				success: function(r) {
					console.log(r);
					if(r == "success")
					{
						swal({
							title: '成功!',
							text: '成功登入.',
							type: 'success',
							confirmButtonClass: "btn btn-success",
							buttonsStyling: false
						}).then(function () {
							window.location.href="./reslist.php";
						})
					}
					else if(r == "failed")
					{
						swal({
						  title: '登入失敗',
						  text: '帳號或密碼錯誤',
						  type: 'error',
						  confirmButtonClass: "btn btn-info",
						  buttonsStyling: false
						});
						$('#SubmitLogin').text('登入');
						$('#SubmitLogin').attr('disabled',false);
					}
					else
					{
						swal({
						  title: '登入失敗',
						  text: '系統發生錯誤',
						  type: 'error',
						  confirmButtonClass: "btn btn-info",
						  buttonsStyling: false
						});
						$('#SubmitLogin').text('登入');
						$('#SubmitLogin').attr('disabled',false);
					}
				},
				error: function (xhr, ajaxOptions, thrownError) {
					console.log(xhr.status);
					console.log(thrownError);
				}
			});
		}
		else
		{
			swal({
			  title: '取消',
			  text: '輸入格式錯誤',
			  type: 'error',
			  confirmButtonClass: "btn btn-info",
			  buttonsStyling: false
			})
		}
	});
	
	$(document).on('click', "#LogoutButton", function() {
		console.log("aaa");
		swal({
			title: '你確定?',
			text: '你確定要登出嗎?',
			type: 'warning',
			showCancelButton: true,
			confirmButtonText: '確定',
			cancelButtonText: '取消',
			confirmButtonClass: "btn btn-success",
			cancelButtonClass: "btn btn-danger",
			buttonsStyling: false
		}).then(function(result) {
			if(result.value){
				$.ajax({
					type: "POST",
					url: "./assets/inc/auth.php",
					data: {	logout: "logout"},
					success:swal({
						title: '成功',
						text: '成功登出.',
						type: 'success',
						confirmButtonClass: "btn btn-success",
						buttonsStyling: false
					}).then(function () {
						window.location.href="./index.php";
					}),
					error: function (xhr, ajaxOptions, thrownError) {
						console.log(xhr.status);
						console.log(thrownError);
					}
				});
			}
			else{
				swal({
					title: '取消',
					text: '你沒有登出 :)',
					type: 'error',
					confirmButtonClass: "btn btn-info",
					buttonsStyling: false
				})
			}
		})
	});