<div id="renommerfichier" class="modal hide fade">
	<div class="modal-header">
		<a data-dismiss="modal" class="close" href="#">×</a>
		<h3>Rename file</h3>
	</div>

	<div class="modal-body">
		<p>New name:</p>
		<input id="newname" type="text" name="newname" value=""/>
		<input id="filetorename" type="hidden" name="file" value=""/>
	</div>
		
	<div class="modal-footer">
		<button onclick="renameFile(document.getElementById('filetorename').value, document.getElementById('newname').value);" class="btn btn-primary success">Rename</button>
		<a data-dismiss="modal" class="close non-opac" href="#"><button class="btn">Cancel</button></a>
	</div>

    <script type="text/javascript">
        function actionEvent6(e){
            if(e.keyCode == 27) {
                $('#renommerfichier').modal('hide');
            }
        }

		function renameFile(id, name)
			{
				jQuery.ajax({
			 		type: 'POST',
			  		url: '/ajax/file/rename',
			  		data:
			  			{
				    		file: id,
				    		newname: name,
			  			},
			  		success: function(data, textStatus, jqXHR)
			  		{
			  			if(check(data, true, false) !== false)
			  				{
			  				}
			  		}
				});
                $('#renommerfichier').modal('hide');
                if (document.cookie == null || readCookie("display") == 0) {
                    displayList();
                }
                else {
                    displayIcon();
                }
			}
		</script>
</div>
