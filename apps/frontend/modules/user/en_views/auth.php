<div id="subcontent" class="container">
	<div class="row-fluid">
		<!-- Connexion -->
		<div class="span6 bordered">
			<strong>Log in :</strong>
			<br />
			<br />
			<form method="POST" class="form-horizontal form-flexible">
				<div class="control-group">
					<label class="control-label" for="login">Nickname or E-mail</label>
					<div class="controls">
						<input class="input-flexible" type="text" id="login" name="login" placeholder="" />
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="password">Password</label>
					<div class="controls">
						<input class="input-flexible" type="password" id="password" name="password" placeholder="" />
					</div>
				</div>

				<div class="control-group">
					<div class="controls">
						<label class="checkbox">
							<input type="checkbox" /> Remember me
						</label><br />
						<button type="submit" name="submit" class="btn">Log in!</button>
						<br />                                 
					</div>
				</div>
				<hr>
				<strong>Or log in with :</strong><br/>
				<a href="/facebook" class="btn btn-facebook"><i class="icon icon-facebook"></i> Facebook</a> 
				<a href="#" class="btn btn-twitter" style="opacity:0.5" onclick="alert('Log in with Twitter is not activated yet.')"><i class="icon icon-twitter"></i> Twitter</a>
			</form>
		</div><!-- /connexion-->
		
		<!-- Inscription -->
		<div class="span6">
			<strong>Sign in :</strong>
			<br /><br />
			<form method="POST" action="/signin" class="form-horizontal">  
				<div id="controlLastname" class="control-group">
					<label class="control-label" for="lastname">Lastname</label>
					<div class="controls">
						<input class="input-flexible" name="lastname" id="lastname" type="text" onblur="if(!isName(this.value)){setWrong('controlLastname');}else{setRight('controlLastname');}" x-webkit-speech />
					</div>
				</div>

				<div id="controlFirstname" class="control-group">
					<label class="control-label" for="firstname">Firstname</label>
					<div class="controls">
						<input class="input-flexible" type="text" name="firstname" id="firstname" onblur="if(!isName(this.value)){setWrong('controlFirstname');}else{setRight('controlFirstname');}" x-webkit-speech />
					</div>
				</div>

				<div id="controlPseudo" class="control-group">
					<label class="control-label" for="pseudo">Username</label>
					<div class="controls">
						<input class="input-flexible" type="text" name="pseudo" id="pseudo" onblur="if(!isPseudo(this.value)){setWrong('controlPseudo');}else{setRight('controlPseudo');}" x-webkit-speech />
					</div>
				</div>

				<div id="controlEmail" class="control-group">
					<label class="control-label" for="email">E-mail address</label>
					<div class="controls">
						<input class="input-flexible" type="text" name="email" id="email" onblur="if(!isEmail(this.value)){setWrong('controlEmail');}else{setRight('controlEmail');}" />
					</div>
				</div>

				<div id="controlPassword" class="control-group">
					<label class="control-label" for="password">Password</label>
					<div class="controls">
						<input type="password" id="signin_password" class="input-flexible" name="password" />
					</div>
				</div>

				<div id="controlPassword2" class="control-group">
					<label class="control-label" for="password2">Confirm password</label>
					<div class="controls">
						<input type="password" class="input-flexible" name="password2" id="password2" onblur="if(document.getElementById('signin_password').value == this.value && this.value != ''){setRight('controlPassword');setRight('controlPassword2');}else{setWrong('controlPassword');setWrong('controlPassword2');}" />
					</div>
				</div>

				<div class="control-group">
					<label class="control-label">Options</label>
					<div class="controls">
						<input name="show_lastname" type="checkbox" checked="checked"/> Display my lastname on the website<br/>
						<input name="show_firstname" type="checkbox" checked="checked"/> Display my name on the website
						<p class="help-block"><span class="label label-info">NOTE</span> These options can be changed later</p>
					</div>
				</div>

				<div class="control-group">
					<div class="controls">
						<label class="checkbox">
						<input name="term_accept" type="checkbox" /><a href="/terms" target="_blank">I accept the terms of use</a>
						</label>
						<button type="submit" class="btn" name="submit">Sign in!</button>
					</div>
				</div>
				<hr>
				<strong>Or sign in with :</strong><br/>
				<a href="#" class="btn btn-facebook"><i class="icon icon-facebook"></i> Facebook</a> 
				<a href="#" class="btn btn-twitter" style="opacity:0.5" onclick="alert('Log in with Twitter is not activated yet.')"><i class="icon icon-twitter"></i> Twitter</a>
			</form>    
  			</div><!--/span6-->
	</div><!--/row-fluid-->
</div><!-- /container -->
