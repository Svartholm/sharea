<script type="text/javascript">lire(0);</script>
<h2 id="title"><?php echo $title; ?></h2>

<div id="contact">	
<p>Vous êtes confrontés à des soucis techniques ou divers bugs ? Vous souhaitez proposer un don pour nous aider ? Nous apporter vos idées et suggestions ? Une envie de partenariat peut-être ? Alors n'hésitez pas à nous contacter via le formulaire ci-dessous !</p><br />
	<form method="post" action="/contact/send" class="form">
		<input id="nom" type="text" placeholder="Nom" name="nom" <?php if(isset($identity)){echo "value=\"".$identity."\"";}?>/><br />
		<input id="email" type="email" placeholder="Email" name="email" <?php if(isset($email)){echo "value=\"".$email."\"";}?>/><br />
		<input id="objet" type="text" placeholder="Objet" name="objet" /><br />
			<textarea id="message" type="text" name="message" rows="3" placeholder="Message"></textarea>
			<p class="help-block">
				L'équipe de sharea.net fera tout son possible pour répondre dans les plus brefs délais.<br /><br />
			</p>
			<input type="submit" name="envoi" value="Envoyer !" class="btn btn-primary" />
	</form>
</div>

