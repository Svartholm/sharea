<div id="renommerdossier" class="modal hide fade">
	<div class="modal-header">
		<a data-dismiss="modal" class="close" href="#">×</a>
		<h3>Rename folder</h3>
	</div>
	
	<div class="modal-body">
		<p>New name:</p>
		<input id="foldernewname" type="text" name="newname" value=""/>
		<input id="folderid" type="hidden" name="folder" value=""/>
	</div>

	<div class="modal-footer">
		<button onclick="renameFolder(document.getElementById('folderid').value, document.getElementById('foldernewname').value);" class="btn btn-primary success">Rename</button>
		<a data-dismiss="modal" class="close non-opac" href="#"><button class="btn">Cancel</button></a>
	</div>

    <script type="text/javascript">
        function actionEvent1(e){
            if(e.keyCode == 27) {
                $('#renommerdossier').modal('hide');
            }
        }

		function renameFolder(id, name)
			{
				jQuery.ajax({
			 		type: 'POST',
			  		url: '/ajax/folder/rename',
			  		data:
			  			{
				    		folder: id,
				    		newname: name,
			  			},
			  		success: function(data, textStatus, jqXHR)
			  		{
			  			if(check(data, true, false) !== false)
			  				{
			  				}
			  		}
				});
                $('#renommerdossier').modal('hide');
                if (document.cookie == null || readCookie("display") == 0) {
                    displayList();
                }
                else {
                    displayIcon();
                }
			}
		</script>
</div>
