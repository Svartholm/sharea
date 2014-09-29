<div id="supprimerfichier" class="modal hide fade">
	<div class="modal-header">
		<a data-dismiss="modal" class="close" href="#">×</a>
		<h3>Confirmation</h3>
	</div>
	<div class="modal-body">
		<p class="center">Are you sure you want to remove the file « <span id="rname"></span> » ?</p><br />
		<div id="supprimer-btns" class="center">
			<button class="btn btn-danger" onclick="removeFile();">Yes</button>
			<a href="#" class="close non-opac" data-dismiss="modal"><button class="btn btn-large btn-primary">No</button></a>
			<input type="hidden" id="filetoremove" name="filetoremove" value="" />
		</div>
            <script type="text/javascript">
            function actionEvent9(e){
                if(e.keyCode == 27) {
                    $('#supprimerfichier').modal('hide');
                }
            }

			function removeFile()
			{
				jQuery.ajax({
			 		type: 'POST',
			  		url: '/ajax/file/remove',
			  		data: 
			  		{
				    	file: document.getElementById("filetoremove").value
			  		},
			  		success: function(data, textStatus, jqXHR)
			  		{
			  		}
				});
                $('#supprimerfichier').modal('hide');
                if (document.cookie == null || readCookie("display") == 0) {
                    displayList();
                }
                else {
                    displayIcon();
                }
			}
		</script>
	</div>
</div>
