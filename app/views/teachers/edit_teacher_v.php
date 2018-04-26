<div class="container" style="position: relative; top: -200px;">
    <form action="/?a=change_teacher&id=<?=$teacher[id_teacher]?>" method="POST" id="edit_teacher">
        <table>
            <tr>
                <td>
                    Имя
                </td>
                <td>
                    <input type="text" name="name" value="<?=$teacher[name]?>">
                </td>
            </tr>
            <tr>
                <td>
                    Фамилия
                </td>
                <td>
                    <input type="text" name="last_name" value="<?=$teacher[last_name]?>">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" value="Подтвердить" style="float: right">
                </td>
            </tr>
        </table>
    </form>
</div>
<script>
	$(document).ready(function(){
        var id = <?=$teacher['id_teacher']?>;
		$('#edit_teacher').on('submit', function(e){
			e.preventDefault();

			//$form = $(this)
			
			//$.ajax => [$.get(), $.post(), $.load()]
			$.ajax({
				url: $(this).attr('action'), //$(this).action / $(this).attr('action')
				type: 'POST', 
				data: $(this).serialize(),
				dataType: 'JSON',

				beforeSend: function(){
					//alert(1)
				},

				success: function(response){
					//alert(2);
					alert(response.message)
					if(response.status){
                        parent.jQuery.fancybox.close();
                        
                        $('#test').empty()
						$('#test').load('/?a=teacher&id=' + id);    
					}
				},

				error: function(){
					//alert(3);
				},

				complete: function(){
					//alert(4);
				}

			})
		})

	})
</script>