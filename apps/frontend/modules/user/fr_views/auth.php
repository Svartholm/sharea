<div id="subcontent" class="container">
	<div class="row-fluid">
		<!-- Connexion -->
		<div class="span6 bordered">
			<strong>Se connecter :</strong>
			<br />
			<br />
			<form method="POST" class="form-horizontal form-flexible">
				<div class="control-group">
					<label class="control-label" for="login">Pseudo ou e-mail</label>
					<div class="controls">
						<input class="input-flexible" type="text" id="login" name="login" placeholder="" />
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="password">Mot de passe</label>
					<div class="controls">
						<input class="input-flexible" type="password" id="password" name="password" placeholder="" />
					</div>
				</div>

				<div class="control-group">
					<div class="controls">
						<label class="checkbox">
							<input type="checkbox" /> Se souvenir de moi
						</label><br />
						<button type="submit" name="submit" class="btn">Connexion</button>
						<br />                                 
					</div>
				</div>
				<hr>
				<strong>Ou connexion via :</strong><br/>
				<a href="/facebook" class="btn btn-facebook"><i class="icon icon-facebook"></i> Facebook</a> 
				<a href="#" class="btn btn-twitter" style="opacity:0.5" onclick="alert('La connexion via Twitter n\'est pas encore activée')"><i class="icon icon-twitter"></i> Twitter</a>
			</form>
		</div><!-- /connexion-->
		
		<!-- Inscription -->
		<div class="span6">
			<strong>Créer un compte :</strong>
			<br /><br />
			<form method="POST" action="/signin" class="form-horizontal">  
				<div id="controlLastname" class="control-group">
					<label class="control-label" for="lastname">Nom</label>
					<div class="controls">
						<input class="input-flexible" name="lastname" id="lastname" type="text" onblur="if(!isName(this.value)){setWrong('controlLastname');}else{setRight('controlLastname');}" x-webkit-speech />
					</div>
				</div>

				<div id="controlFirstname" class="control-group">
					<label class="control-label" for="firstname">Prénom</label>
					<div class="controls">
						<input class="input-flexible" type="text" name="firstname" id="firstname" onblur="if(!isName(this.value)){setWrong('controlFirstname');}else{setRight('controlFirstname');}" x-webkit-speech />
					</div>
				</div>

				<div id="controlPseudo" class="control-group">
					<label class="control-label" for="pseudo">Pseudonyme</label>
					<div class="controls">
						<input class="input-flexible" type="text" name="pseudo" id="pseudo" onblur="if(!isPseudo(this.value)){setWrong('controlPseudo');}else{setRight('controlPseudo');}" x-webkit-speech />
					</div>
				</div>

				<div id="controlEmail" class="control-group">
					<label class="control-label" for="email">Adresse e-mail</label>
					<div class="controls">
						<input class="input-flexible" type="text" name="email" id="email" onblur="if(!isEmail(this.value)){setWrong('controlEmail');}else{setRight('controlEmail');}" />
					</div>
				</div>

				<div id="controlPassword" class="control-group">
					<label class="control-label" for="password">Mot de passe</label>
					<div class="controls">
						<input type="password" id="signin_password" class="input-flexible" name="password" />
					</div>
				</div>

				<div id="controlPassword2" class="control-group">
					<label class="control-label" for="password2">Confirmer le mot de passe</label>
					<div class="controls">
						<input type="password" class="input-flexible" name="password2" id="password2" onblur="if(document.getElementById('signin_password').value == this.value && this.value != ''){setRight('controlPassword');setRight('controlPassword2');}else{setWrong('controlPassword');setWrong('controlPassword2');}" />
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

				<div class="control-group">
					<div class="controls">
						<label class="checkbox">
						<input name="term_accept" type="checkbox" /><a href="/terms" target="_blank">J'accepte les conditions d'utilisation</a>
						</label>
						<button type="submit" class="btn" name="submit">Créer le compte</button>
					</div>
				</div>
				<hr>
				<strong>Ou créer un compte via :</strong><br/>
				<a href="#" class="btn btn-facebook"><i class="icon icon-facebook"></i> Facebook</a> 
				<a href="#" class="btn btn-twitter" style="opacity:0.5" onclick="alert('La connexion via Twitter n\'est pas encore activée')"><i class="icon icon-twitter"></i> Twitter</a>
			</form>    
  			</div><!--/span6-->
	</div><!--/row-fluid-->
</div><!-- /container -->
