<div id="deplacerfichier" class="modal hide fade">
	<div class="modal-header">
		<a data-dismiss="modal" class="close" href="#">×</a>
		<h3>Move the file « <span id="mfile"></span> »</h3>
	</div>

	<form action="/movefile" method="POST" name="move_file" id="move_file">	
		<div class="modal-body">
			<div class="controls">
				<input type="hidden" id="newpath" name="newpath" value="" />
				<?php
				foreach($allfolders as $dossier)
				{
					echo '<div class="fd" onclick="changeFolder('.$dossier->id().')">';
					if(strtolower($dossier->name()) == "musique") {
						echo '<img src="/images/music.png" alt="musique">';
					}
					else if(strtolower($dossier->name()) == "photos") {
						echo '<img src="/images/pictures.png" alt="image">';
					}
					else if($dossier->name() == "Vidéos") {
						echo '<img src="/images/videos.png" alt="video">';
					}
					else {
						echo '<img src="/images/folder.png" alt="dossier">';
					}
					$shortname = \lib\ToolBox::cut($dossier->name(), 7, '...', true);
					echo '<br /><span class="shortname">'.$shortname.'</span><br />';
					echo '</div>';
					}
				?>			


			</div>
			<input id="filetomove" type="hidden" name="file" value=""/>

		</div>

		<div class="modal-footer">
			<a data-dismiss="modal" class="close non-opac" href="#"><button class="btn">Cancel</button></a>
		</div>
	</form>

	<script type="text/javascript">
	function changeFolder(folderid) {
		document.getElementById("newpath").value = folderid;
		document.getElementById("move_file").submit();
	}

    function actionEvent7(e){
        if(e.keyCode == 27) {
            $('#deplacerfichier').modal('hide')
        }
    }

	</script>
</div>
