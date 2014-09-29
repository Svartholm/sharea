<div class="container">
	<ul class="nav nav-tabs" id="tab">
		<li class="active"><a data-toggle="tab" href="#profile">Mon profil</a></li>
		<li class=""><a data-toggle="tab" href="#storage">Stockage</a></li>
	</ul>

	<div class="tab-content" id="myTabContent">
			<div id="profile" class="tab-pane fade active in">
				<p>Votre profil</p>
			</div>
	</div>
	
	<script>
	$(function () {
	$('.tabs a:last').tab('show')
	})
	</script>
</div>
