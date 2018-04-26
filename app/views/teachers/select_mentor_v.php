<div class="container" style="text-align: center">
	<?if(!empty($teachers)){?>
		<p>
			Список учитилей, которые пока не знанимают должность классного руководителя
		</p>
		<form action="/?a=change_mentor&id_class=<?=$id_class?>" id="select_mentor">
			
			<select name="new_mentor" style="width: 340px; background: #D3D3D3; height: 30px; margin-bottom: 20px; font: italic small-caps 100 16px Tahoma; cursor: pointer; color: #A52A2A">
				<?
				foreach ($teachers as $teacher){
				?>
					<option value="<?=$teacher[id_teacher]?>"><?=$teacher['last_name'] . ' ' . $teacher['name']?></option>
				<? }?>
			</select>
			<input type="submit" value="Подтвердить" style="position: absolute; top: 120px; left: 20px; cursor: pointer">
			<input type="button" value="Отмена" id="cancel" style="position: absolute; top: 120px; right: 20px; cursor: pointer">
		</form>
	<?} else {?>
		<div> В данный момент все учителя уже ведут менторскую деятельность</div>
	<?}?>
</div>
<script>
	$(document).ready(function(){
        var id = <?=$id_class?>;
		$('#select_mentor').on('submit', function(e){
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
						$.fancybox.close( true );
						
						
                        //window.location.reload();
						// $('#class_info').empty()
						$('#teacher').html(response.teacher);
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
        
    $('#cancel').on('click', function(){
        $.fancybox.close( true );
    });

	})
</script>