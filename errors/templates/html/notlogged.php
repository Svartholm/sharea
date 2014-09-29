<div style="width:400px; margin-left:auto; margin-right:auto">
	<div class="alert-message block-message warning">
	<a class="close" href="#">×</a>
	<p style="font-size:16px"><strong>Warning!</strong> You're not connected.</p>
	<div class="alert-actions" style="margin-top:15px">
	<a class="btn small" href="/login">Log in</a>&nbsp;&nbsp;<a class="btn small" href="/signin">Sign in</a>
	</div>
	</div>
</div>

<?php
	$this->app->vss_pool->set('redirect_to', $_SERVER['REDIRECT_URL']);
?>
