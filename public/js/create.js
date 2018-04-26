<script>
	$(document).ready(function(){

		$('#edit_pupil').on('submit', function(e){
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
					// alert(2);
					alert(response.message)
					if(response.status){
						$.fancybox.close( true );

						// $('.container').empty()
						// $('.container').load('/?c=news&a=reload_list' + get_params);

						$('#list_of_pupils').DataTable().ajax.reload();
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