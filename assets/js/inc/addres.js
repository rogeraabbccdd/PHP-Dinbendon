		// Add new menu
		$('#newmenub').on('click', function() {
			$('.form-group').removeClass('has-error');
			$('#modalname').val("");
			$('#modalprice').val("");
		});
		
		// Add new menu to datatables
		$('#savemenum').on('click', function() {
			// get input value
			var name = $('#modalname').val();
			var price = $('#modalprice').val();	
			
			// check empty
			if(name && price)
			{
				// add to rows
				var r1 = "<input type='text' value='"+name+"' name='name2[]' class='namemenu'>";
				var r2 = "<input type='text' value='"+price+"' name='price2[]' class='pricemenu'>";
				var r3 = "<button type='button' class='delmenu2 btn btn-danger btn-simple'><i class='material-icons'>close</i></button>";
				var rowadd = menutable.row.add([r1, r2, r3]).draw().node();
				// hide modal
				$('#modal_form').modal('hide');
			}
			else
			{
				swal({
				  title: '錯誤',
				  text: '欄位不能為空',
				  type: 'error',
				  confirmButtonClass: "btn btn-info",
				  buttonsStyling: false
				})
			}
		});
		
		// 刪除菜單
		$(document).on("click", ".delmenu", function() {
			if( $(this).closest("td").find("i.material-icons").text() == "close")
			{
				// check
				$(this).closest("tr").find("input[name='del[]']").attr("checked", true);
				// change color
				$(this).closest("tr").css("background-color", "pink");
				// change button
				$(this).removeClass("btn-danger");
				$(this).addClass("btn-success");
				$(this).html("<i class='material-icons'>undo</i>");
			}
			else
			{
				// uncheck
				$(this).closest("tr").find("input[name='del[]']").attr("checked", false);
				// change color
				$(this).closest("tr").css("background-color", "white");
				// change button
				$(this).removeClass("btn-success");
				$(this).addClass("btn-danger");
				$(this).html("<i class='material-icons'>close</i>");
			}
		});
		
		$(document).on("click", ".delmenu2", function() {
			menutable.row( $(this).closest("tr") ).remove().draw();
		});
		
		// 保存
		$("#saveres").on("click", function(e){
			if($(".namemenu").length > 0 && $("input[name='resname']").val() && $("input[name='tel']").val())
			{
				$("#saveres").attr("disabled", true);
				$("#saveres").val("保存中");
			
				var fd = new FormData( $("#resform").get(0) ); 
				menutable.$('input').each(function(){
					if($(this).attr("type") == "checkbox")
					{
						if( $(this).is(":checked") )
							fd.append( $(this).attr("name"), $(this).val() );
					}
					else	fd.append( $(this).attr("name"), $(this).val() );
				});
				
				$.ajax({
					type: 'post',
					url: './assets/inc/addres.php<?=((!empty($id))?"?id=".$id:"")?>',
					data: fd,
					cache: false,
					contentType: false,
					processData: false,
					success: function(r){
						console.log(r);
						if(r == "large")
						{
							swal({
							  title: '錯誤',
							  text: '圖片大於1MB',
							  type: 'error',
							  confirmButtonClass: "btn btn-info",
							  buttonsStyling: false
							})
						}
						else if(r == "today")
						{
							swal({
							  title: '錯誤',
							  text: '不能編輯今日餐廳',
							  type: 'error',
							  confirmButtonClass: "btn btn-info",
							  buttonsStyling: false
							})
						}
						else if(r == "ok")
						{
							swal({
								title: '成功!',
								text: '新增成功',
								type: 'success',
								confirmButtonClass: "btn btn-success",
								buttonsStyling: false
							}).then(function () {
								window.location.href="./reslist.php";
							})
						}
					}
				})
				
				$("#saveres").attr("disabled", false);
				$("#saveres").val("保存");
			}
			else
			{
				swal({
				  title: '錯誤',
				  text: '菜單和餐廳名稱、電話不能為空',
				  type: 'error',
				  confirmButtonClass: "btn btn-info",
				  buttonsStyling: false
				})
			}
		});