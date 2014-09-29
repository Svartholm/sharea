<script type="text/javascript" src="/js/bootstrap-modal.js"></script>
<script>lire(0);</script>
<div class="container">
	<?php
		if($is_users_profile == true){ ?>
			<h3>Profil</h3><hr/>
<?php } else {?>
			<h3><?php echo $user->profile()->pseudo();?></h3><hr/>
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
				<label class="control-label" for="disabledInput">Username</label>
				<div class="controls">
					<input class="large disabled" id="disabledInput" name="disabledInput" size="30" placeholder="Disabled input here… carry on." disabled="disabled" type="text" value="<?php echo $user->profile()->pseudo();?>">
					<p id="helpProfil" class="help-block">You can not change your username on the platform. However, if this is required, please <a href="/contact">contact us.</a></p>
				</div>
			</div>

			<div id="controlFirstname" class="control-group">
				<label class="control-label" for="firstname">Name</label>
				<div class="controls">
						<input type="text" class="large" id="firstname" onblur="if(!isName(this.value)){setWrong('controlFirstname');}else{setRight('controlFirstname');}" name="firstname" value="<?php echo $user->profile()->firstname();?>">
				</div>
			</div>

			<div id="controlLastname" class="control-group">
				<label class="control-label" for="lastname">Lastname</label>
				<div class="controls">
						<input type="text" class="large" id="lastname" onblur="if(!isName(this.value)){setWrong('controlLastname');}else{setRight('controlLastname');}" name="lastname" value="<?php echo $user->profile()->lastname();?>">
				</div>
			</div>

			<div id="controlEmail" class="control-group">
				<label class="control-label" for="email">E-mail address</label>
				<div class="controls">
						<input type="email" class="large" name="email" id="email" value="<?php echo $user->profile()->email();?>" onblur="if(!isEmail(this.value)){setWrong('controlEmail');}else{setRight('controlEmail');}">
					</div>
				</div>
<?php $c = $user->config();require $modalCode;?>
			<div class="control-group">
				<label class="control-label">Options</label>
				<div class="controls">
					<input name="show_lastname" type="checkbox" <?php if($c->get('show_lastname') !== false) echo 'checked';?>/> Display my lastname on the website<br/>
					<input name="show_firstname" type="checkbox" <?php if($c->get('show_firstname') !== false) echo 'checked=\'checked\'';?>/> Display my firstname on the website<br/><br/>
					<button class="btn btn-info btn-small" data-controls-modal="codePromo" data-backdrop="static" <?php if($c->get('used_code') != 0) echo 'disabled=\'disabled\''; ?>)>Enter code</button>
				</div>
			</div>
			
			<input class="btn btn-primary btn-save-profil" value="Save!" name="submit" type="submit">
		</div>

		<div class="span6">
			<div class="control-group">
				<label for="old_password" class="control-label">Old password</label>
					<div class="controls">
						<input type="password" id="old_password" class="large" name="old_password">
					</div>
			</div>

			<div id="controlPassword" class="control-group">
				<label class="control-label" for="password">New password</label>
					<div class="controls">
						<input id="password" type="password" class="large" name="new_password">
					</div>
			</div>

			<div id="controlPassword2" class="control-group">
				<label class="control-label" for="newpass">Validate new password</label>
					<div class="controls">
						<input type="password" id="newpass" class="large" onblur="if(document.getElementById('password').value == this.value && this.value != ''){setRight('controlPassword');setRight('controlPassword2');}else{setWrong('controlPassword');setWrong('controlPassword2');}" name="new_password2">
					</div>
			</div>


<!-- Image de profil -->
			<div class="control-group control-profil">		
				<h3>Picture</h3>
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
					<br /><span class="help-block"><span class="label label-info">Note</span> &nbsp; Click on an image to select your new profile picture</span><br /><br />
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
					echo "<p style=\"color:red\">You have no picture online.</p>";
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
			echo "Unavailable";
		}
		else {
			echo $user->profile()->lastname();
		}
		echo '<br /></b><br/>Prénom : <b>';
		if(!$user->config()->get('show_firstname')) {
			echo "Unavailable";
		}
		else {
			echo $user->profile()->firstname();
		}
		echo '<br /></b><br/>Username : <b>'.$user->profile()->pseudo().'</b><br/></p>';
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

