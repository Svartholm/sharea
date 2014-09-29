<?php
	if(!isset($filled))
	{
		/* If the user has not filled the form */
		if(isset($noterm))
		{
		?>
			<div class="alert alert-error" id="notifok">
			<a class="close">&times;</a>
        		Veuillez accepter les conditions d'utilisation.
			</div>
		<?php
		}
		?>
			
		<h2 id="title"><?php echo $title; ?></h2>
		<div id="register">
			<fieldset>
			<form method="POST" class="form-horizontal" name="inscription">
			
				<div id="controlLastname" class="control-group">
					<label class="control-label" for="lastname">Nom</label>
					<div class="controls">
						<input class="large" name="lastname" id="lastname" onblur="if(!isName(this.value)){setWrong('controlLastname');}else{setRight('controlLastname');}">
					</div>
				</div>

				<div id="controlFirstname" class="control-group">
					<label class="control-label" for="firstname">Prénom</label>
					<div class="controls">
						<input class="large" name="firstname" id="firstname" onblur="if(!isName(this.value)){setWrong('controlFirstname');}else{setRight('controlFirstname');}">
					</div>
				</div>

				<div id="controlPseudo" class="control-group">
					<label class="control-label" for="pseudo">Pseudonyme</label>
					<div class="controls">
						<input class="large" name="pseudo" id="pseudo" onblur="if(!isPseudo(this.value)){setWrong('controlPseudo');}else{setRight('controlPseudo');}">
					</div>
				</div>

				<div id="controlEmail" class="control-group">
					<label class="control-label" for="email">Adresse email</label>
					<div class="controls">
						<input class="large" name="email" id="email" onblur="if(!isEmail(this.value)){setWrong('controlEmail');}else{setRight('controlEmail');}">
					</div>
				</div>

				<div id="controlPassword" class="control-group">
					<label class="control-label" for="password">Mot de passe</label>
					<div class="controls">
						<input type="password" id="password" class="large" name="password">
					</div>
				</div>

				<div id="controlPassword2" class="control-group">
					<label class="control-label" for="password2">Confirmer le mot de passe</label>
					<div class="controls">
						<input type="password" class="large" name="password2" id="password2" onblur="if(document.getElementById('password').value == this.value && this.value != ''){setRight('controlPassword');setRight('controlPassword2');}else{setWrong('controlPassword');setWrong('controlPassword2');}">
					</div>
				</div>

				<div class="control-group">
					<label class="control-label">Options</label>
					<div class="controls">
						<input name="show_lastname" type="checkbox" checked="checked"/> Afficher mon nom sur la plateforme<br/>
						<input name="show_firstname" type="checkbox" checked="checked"/> Afficher mon prénom sur la plateforme
						<p class="help-block"><span class="label label-info">NOTE</span> Ces options pourront être modifiées plus tard</p>
					</div>
				</div>

				<div class="form-actions">
					<input name="term_accept" type="checkbox" /> J'accepte les <a href="/terms" target="_blank">conditions d'utilisation</a><br /><br />
					<input type="submit" name="submit" value="M'inscrire !" class="btn btn-primary">
				</div>
			</form>
			</fieldset>
		</div>
		<?php
		}
	else
		{
			/* The user has filled the form */
			?>
			<div class="alert alert-success" id="notifok">
				<a class="close">&times;</a>
        		Votre compte a bien été créé ! Veuillez maintenant vous <a href="/login">identifier</a>.
			</div>
			<?php
		}
?>
