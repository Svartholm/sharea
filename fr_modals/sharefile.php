<div id="partagerfichier" class="modal hide fade">
	<div class="modal-header">
		<a data-dismiss="modal" class="close" href="#">×</a>
		<h3>Share the file</h3>
	</div>
	
		<div class="modal-body">
			<label class="control-label">Who can see the file « <span id="fileshared"></span> » ?</label><br />
			<div class="controls">
				<input name="file" type="hidden" id="file" value=""/>
				<ul style="display:table; text-align:center; margin-left:auto; margin-right:auto">
					<li onclick="chgPermFile(document.getElementById('file').value, <?php echo \lib\Permission::P_Public; ?>);" class="permission main"><img src="/images/permissions/all.png" alt="public" /><br />Everybody</li>
					<li onclick="chgPermFile(document.getElementById('file').value, <?php echo \lib\Permission::P_Friends; ?>);" class="permission main"><img src="/images/permissions/friends.png" alt="friends"/><br />Friends only</li>
					<li onclick="chgPermFile(document.getElementById('file').value, <?php echo \lib\Permission::P_Private; ?>);" class="permission main"><img src="/images/permissions/private.png" alt="private" /><br />Me only</li>
				</ul>
			</div>
		</div> <!-- /modal-body -->

    	<script type="text/javascript">
	        function actionEvent5(e){
	            if(e.keyCode == 27) {
	                $('#partagerfichier').modal('hide');
	            }
	        }

			function chgPermFile(id, perm)
			{
				jQuery.ajax({
			 		type: 'POST',
			  		url: '/ajax/file/permissions/edit',
			  		data:
			  			{
				    		file: id,
				    		perm: perm
			  			},
			  		success: function(data, textStatus, jqXHR)
			  		{
			  			if(check(data, true, false) !== false)
			  				{
			  				}
			  		}
				});
                $('#partagerfichier').modal('hide');
                if (document.cookie == null || readCookie("display") == 0) {
                    displayList();
                }
                else {
                    displayIcon();
                }
			}
		</script>
		
		<div class="modal-footer">
			<a data-dismiss="modal" class="close" href="#">Close</a>
		</div>
</div>
