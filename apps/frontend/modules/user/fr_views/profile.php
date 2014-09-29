<script type="text/javascript" src="/js/bootstrap-modal.js"></script>
<script>lire(0);</script>
<div class="container">
	<?php
		if($is_users_profile == true){ ?>
			<h3>Votre profil</h3><hr/>
<?php } else {?>
			<h3>Profil de <?php echo $user->profile()->pseudo();?></h3><hr/>
<?php } ?>
<?php
	if(isset($notification))
		{
			echo "<div class=\"alert alert-success\">
			   <a class=\"close\" href=\"".$_SERVER['REDIRECT_URL']."\">&times;</a>
        		".$notification."
     			 </div>";
		}
	if($is_users_profile == true)
		{ ?>
	<fieldset>
	<form class="form-horizontal" method="POST" action="<?php echo $_SERVER['REDIRECT_URL']; ?>">

	<div class="row">
		<div class="span6">
			<div class="control-group">
				<label class="control-label" for="disabledInput">Pseudo</label>
				<div class="controls">
					<input class="large disabled" id="disabledInput" name="disabledInput" size="30" placeholder="Disabled input here… carry on." disabled="disabled" type="text" value="<?php echo $user->profile()->pseudo();?>">
					<p id="helpProfil" class="help-block">Vous ne pouvez pas changer votre pseudo sur la plateforme. Si toutefois cela est nécessaire, veuillez <a href="/contact">nous contacter.</a></p>
				</div>
			</div>

			<div id="controlFirstname" class="control-group">
				<label class="control-label" for="firstname">Prénom</label>
				<div class="controls">
						<input type="text" class="large" id="firstname" onblur="if(!isName(this.value)){setWrong('controlFirstname');}else{setRight('controlFirstname');}" name="firstname" value="<?php echo $user->profile()->firstname();?>">
				</div>
			</div>

			<div id="controlLastname" class="control-group">
				<label class="control-label" for="lastname">Nom</label>
				<div class="controls">
						<input type="text" class="large" id="lastname" onblur="if(!isName(this.value)){setWrong('controlLastname');}else{setRight('controlLastname');}" name="lastname" value="<?php echo $user->profile()->lastname();?>">
				</div>
			</div>

			<div id="controlEmail" class="control-group">
				<label class="control-label" for="email">Adresse email</label>
				<div class="controls">
						<input type="email" class="large" name="email" id="email" value="<?php echo $user->profile()->email();?>" onblur="if(!isEmail(this.value)){setWrong('controlEmail');}else{setRight('controlEmail');}">
					</div>
				</div>
<?php $c = $user->config();require $modalCode;?>
			<div class="control-group">
				<label class="control-label">Options</label>
				<div class="controls">
					<input name="show_lastname" type="checkbox" <?php if($c->get('show_lastname') !== false) echo 'checked';?>/> Afficher mon nom sur la plateforme<br/>
					<input name="show_firstname" type="checkbox" <?php if($c->get('show_firstname') !== false) echo 'checked=\'checked\'';?>/> Afficher mon prénom sur la plateforme<br/><br/>
					<button class="btn btn-info btn-small" data-controls-modal="codePromo" data-backdrop="static" <?php if($c->get('used_code') != 0) echo 'disabled=\'disabled\''; ?>)>Rentrer un code promotionel</button>
				</div>
			</div>
			
			<center><input class="btn btn-primary btn-save-profil" value="Enregistrer les modifications" name="submit" type="submit"></center>
		</div>

		<div class="span6">
			<div class="control-group">
				<label for="old_password" class="control-label">Ancien mot de passe</label>
					<div class="controls">
						<input type="password" id="old_password" class="large" name="old_password">
					</div>
			</div>

			<div id="controlPassword" class="control-group">
				<label class="control-label" for="password">Nouveau mot de passe</label>
					<div class="controls">
						<input id="password" type="password" class="large" name="new_password">
					</div>
			</div>

			<div id="controlPassword2" class="control-group">
				<label class="control-label" for="newpass">Retaper le nouveau mot de passe</label>
					<div class="controls">
						<input type="password" id="newpass" class="large" onblur="if(document.getElementById('password').value == this.value && this.value != ''){setRight('controlPassword');setRight('controlPassword2');}else{setWrong('controlPassword');setWrong('controlPassword2');}" name="new_password2">
					</div>
			</div>


<!-- Image de profil -->
			<div class="control-group control-profil">		
				<h3>Avatar</h3>			
				<?php
				if($user->profile()->avatar() == '') {
					echo '<img id="apercu-profil" src="/images/ami.png" /><br /><br />';
				}
				else {
					echo '<img id="apercu-profil" src="/download/'.$user->profile()->avatar().'/min" /><br /><br />';
				}
				?>
				<input type="hidden" id="avatar" name="avatar" value="<?php echo $user->profile()->avatar(); ?>">
				

				<?php
				if(count($files) > 0) {
					?>
					<br /><span class="help-block"><span class="label label-info">Note</span> &nbsp; Cliquez sur une image pour choisir votre nouvel avatar</span><br /><br />
				   <div id="carousel" class="carousel">
				   <!-- Carousel items -->
					<div class="carousel-inner" style="height:150px">	    
				   <?php
					foreach($files as $f) {
								if($user->profile()->avatar() == $f->id()) {
										echo '<div class="active item item-profil"><img src="/download/';
										echo $f->id();
										echo '/min" onclick="document.getElementById(\'apercu-profil\').src=\'/download/';
										echo $f->id();
										echo '/min\'; document.getElementsByName(\'avatar\')[0].value=\'';
										echo $f->id();
										echo '\'">';
								}
								else { 
								  		echo '<div class="item item-profil"><img src="/download/';
								  		echo $f->id();
								  		echo '/min" onclick="document.getElementById(\'apercu-profil\').src=\'/download/';
								  		echo $f->id();
								  		echo '/min\'; document.getElementsByName(\'avatar\')[0].value=\'';
								  		echo $f->id().'\'">';                  	
								}
	                   	echo '<div class="carousel-caption"><h4>'.$f->name().'</h4></div>';
								echo "</div>\n";
	            		}
	
					?>
				   </div>
				   <!-- Carousel nav -->
					<a class="carousel-control left" href="#carousel" data-slide="prev">&lsaquo;</a>
					<a class="carousel-control right" href="#carousel" data-slide="next">&rsaquo;</a>
				   </div>
				<?php
				}
				else {
					echo "<p style=\"color:red\">Vous n'avez aucune image en ligne.</p>";
				}
				?>	
			</div>
		</div>
	</div>
	</form>
	</fieldset>
<?php
	}
	else
	{
		echo '<div class="center profil-user"><div class="thumbnail thumbnail-profil center" onmouseover="changeColor(this)">';
		$avatar = $user->profile()->avatar();
		if($avatar != '') {
			echo '<img src="/download/'.$avatar.'/min" alt="avatar">';
		}
		else {
			echo '<img src="/images/ami.png" alt="ami">';
		}
		echo '<br /><p>Nom : <b>';
		if(!$user->config()->get('show_lastname')) {
			echo "Non précisé";
		}
		else {
			echo $user->profile()->lastname();
		}
		echo '<br /></b><br/>Prénom : <b>';
		if(!$user->config()->get('show_firstname')) {
			echo "Non précisé";
		}
		else {
			echo $user->profile()->firstname();
		}
		echo '<br /></b><br/>Pseudonyme : <b>'.$user->profile()->pseudo().'</b><br/></p>';
		echo '</div></div>';
	}
?>
</div>
<script type="text/javascript" src="/js/bootstrap-carousel.js"></script>
<script type="text/javascript">
$(function(){
   $('.carousel').carousel(1);
});
</script>

