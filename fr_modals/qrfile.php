<div id="qrcodefichier" class="modal hide fade" onmouseover="document.getElementById('lien').select()">
	<div class="modal-header">
		<a data-dismiss="modal" class="close" href="#">×</a>
		<h4>QRcode for « <span id="qrname"></span> »</h4>
	</div>
	
	<div class="modal-body" style="text-align:center;">
		<img id="imageqrcode" src="" alt="Loading QRcode ..."/><br /><br />
		<input id="lien" type="text" value="" name="lien"/><br />
		<span class="help-block" style="font-size:12px"><span class="label label-info">Note</span> &nbsp;Ctrl + C to copy </span>
	</div>
	
	<div class="modal-footer">
		<a data-dismiss="modal" class="close" href="#">Close</a>
	</div>
    <script type="text/javascript">
    function actionEvent4(e){
        if(e.keyCode == 27) {
            $('#qrcodefichier').modal('hide');
        }
    }
    </script>
</div>
