<div id="supprimeramis" class="modal hide fade">
	<div class="modal-header">
		<a data-dismiss="modal" class="close" href="#">×</a>
		<h3>Confirmation</h3>
	</div>
	<div class="modal-body">
		<p class="center">Remove « <span id="fname"></span> » from friend list ?</p><br />
		<div id="supprimer-btns" class="center">
			<button class="btn btn-danger" onclick="removeFriend();">Yes</button>
			<a href="#" class="close non-opac" data-dismiss="modal"><button class="btn btn-large btn-primary">No</button></a>
			<input type="hidden" id="friendtoremove" name="friendtoremove" value="" />
		</div>
        <script type="text/javascript">
            function actionEvent(e){
                if(e.keyCode == 27) {
                    $('#supprimeramis').modal('hide');
                }
            }
            document.onkeydown = actionEvent;

			function removeFriend()
			{
				jQuery.ajax({
			 		type: 'POST',
			  		url: '/ajax/friends/delete',
			  		data: 
			  			{
				    		friendid: document.getElementById("friendtoremove").value
                        },
			  		success: function(data, textStatus, jqXHR)
			  		{
			  			if(check(data, true, false) !== false)
			  				{
			  					location.reload();
			  				}
			  		},
			 		error: function(jqXHR, textStatus, errorThrown)
			 		{
			 			alert("Error :/");
			  	}
				});
			}
		</script>
	</div>
</div>
