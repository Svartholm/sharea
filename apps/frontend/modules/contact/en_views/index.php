<script type="text/javascript">lire(0);</script>
<h2 id="title">Contact</h2>

<div id="contact">	
<p>You are faced with various technical problems or bugs? You want to offer a donation to help us? Give us your ideas and suggestions? A partnership like maybe? So do not hesitate to contact us using the form below!</p><br />
	<form method="post" action="/contact/send" class="form">
		<input id="nom" type="text" placeholder="Name" name="nom" <?php if(isset($identity)){echo "value=\"".$identity."\"";}?>/><br />
		<input id="email" type="email" placeholder="E-mail address" name="email" <?php if(isset($email)){echo "value=\"".$email."\"";}?>/><br />
		<input id="objet" type="text" placeholder="Object" name="objet" /><br />
			<textarea id="message" type="text" name="message" rows="3" placeholder="Message"></textarea>
			<p class="help-block">
                Sharea.net team will make every effort to respond as soon as possible.<br /><br />
			</p>
			<input type="submit" name="envoi" value="Send!" class="btn btn-primary" />
	</form>
</div>

