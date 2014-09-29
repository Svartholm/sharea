<div id="supprimerdossier" class="modal hide fade">
	<div class="modal-header">
		<a data-dismiss="modal" class="close" href="#">×</a>
		<h3>Confirmation</h3>
	</div>
	<div class="modal-body">
		<p class="center">Are you sure you want to remove the folder « <span id="foldername"></span> » ?</p>
	</div>
	<div class="modal-footer">
		<div id="supprimer-btns" class="center">
			<button class="btn btn-danger" onclick="removeFolder();">Yes</button>
			<a href="#" class="close non-opac" data-dismiss="modal"><button class="btn btn-primary">No</button></a>
			<input type="hidden" id="foldertoremove" name="foldertoremove" value="" />
		</div>
        <script type="text/javascript">
            function actionEvent3(e){
                if(e.keyCode == 27) {
                    $('#supprimerdossier').modal('hide');
                }
            }

			function removeFolder()
			{
				jQuery.ajax({
			 		type: 'POST',
			  		url: '/ajax/folder/remove',
			  		data:
			  			{
				    		folder: document.getElementById("foldertoremove").value,
			  			},
			  		success: function(data, textStatus, jqXHR)
			  		{
			  		}
                });
                $('#supprimerdossier').modal('hide');
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
