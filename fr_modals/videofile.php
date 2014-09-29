<div id="videofile" class="modal hide fade" style="width:680px">
	<div class="modal-header">
		<a data-dismiss="modal" class="close" href="#">×</a>
		<h4>Playing  « <span id="videoname"></span> » </h4>
	</div>

	<div class="modal-body" style="text-align:center;">

   	<video width="600" height="380" controls="controls">
		<source id="videolink" src="">
	</video>
	</div>

	<div class="modal-footer">
		<a data-dismiss="modal" class="close" href="#">Close</a>
	</div>
    <script type="text/javascript">
        function actionEvent8(e){
            if(e.keyCode == 27) {
                $('#videofile').modal('hide');
            }
        }
    </script>
</div>