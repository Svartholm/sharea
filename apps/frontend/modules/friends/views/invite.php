<?php
if($invit)
{
	?>
	<div class="alert alert-success" id="notifok">
		<a class="close">&times;</a>
	  Invitation envoyée ! Vous allez être redirigé. Si rien ne se passe, <a href="/friends">cliquez ici</a>
	</div>
	
	<?php
}
else
{
	?>
	<div class="alert alert-error" id="notifok">
		<a class="close">&times;</a>
		Une demande d'ajout a déjà été envoyée ! Vous allez être redirigé. Si rien ne se passe, <a href="/friends">cliquez ici</a>
	</div>
	<?php
}
?>
<script type="text/javascript">
window.location = "/friends";
</script>
