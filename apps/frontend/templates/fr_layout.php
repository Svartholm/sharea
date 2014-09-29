<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php if (isset($title)) { echo $title; } ?> - sharea.net</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">

    <link rel="stylesheet" href="/css/bootstrap.css">
    <link rel="stylesheet" href="/css/font-awesome.css">
    <link rel="stylesheet" href="/css/base.css">
    <link rel="stylesheet" href="/css/design.css">

	<link rel="shortcut icon" type="image/png" href="/images/sharea.jpg" />
	
	<script type="text/javascript" src="/js/jquery.min.js"></script>
	<script type="text/javascript" src="/js/bootstrap-dropdown.js"></script>
	<script type="text/javascript" src="/js/regex.js"></script>
	<script type="text/javascript" src="/js/video.js"></script>
	<script type="text/javascript" src="/js/base64.js"></script>
	<script type="text/javascript" src="/js/main.js"></script>
	<script type="text/javascript" src="/js/ajax.js"></script>
    <script type="text/javascript" src="/js/bootstrap-twipsy.js"></script>
    <script type="text/javascript" src="/js/bootstrap-tooltip.js"></script>
    <script type="text/javascript" src="/js/bootstrap-transition.js"></script>
    <script type="text/javascript" src="/js/bootstrap-collapse.js"></script>

    <!-- Skins (pick one)
    <link rel="stylesheet" href="../css/green.css">-->
    <link rel="stylesheet" title="blue" href="/css/blue.css">
    <!--<link rel="alternate stylesheet" title="red" href="../css/red.css">-->
        
    <!-- Delete this-->
    <script src="../js/styleswitch.js" type="text/javascript"></script>
    <link rel="stylesheet" href="/css/styleswitch.css">
    <!-- End delete-->

    <!-- [if lt IE 9]>
	    <link rel="stylesheet" href="css/font-awesome-ie7.css">
	    <script src="js/html5-3.6-respond-1.1.0.min.js"></script>
    <![endif]-->


	</head>
	<body>
    <!-- Delete this
		<div class="switcher">
			<i class="icon icon-bookmark green"></i> <a href="javascript:chooseStyle('none',%2060)" checked="checked">Vert nature</a> <br>
			<i class="icon icon-bookmark blue"></i> <a href="javascript:chooseStyle('blue',%2060)">Bleu sophistiqué</a> <br>
			<i class="icon icon-bookmark red"></i> <a href="javascript:chooseStyle('red',%2060)">Rouge sexy</a>
		</div>End Delete-->
        
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<i class="icon icon-reorder"></i> Menu
					</a>
					<div class="nav-collapse collapse">
						<a href="/" class="brand">sharea<span class="blue">.net</span></a>
						<ul class="nav" id="nav1">
						<?php
							if(!$user_manager->isAuth())
							{ ?>
							<li class="bordered-grey"><a href="/"><i class="icon-home"></i>&nbsp;Accueil</a></li>
							<li class="bordered-grey"><a href="/faq"><i class="icon-th-list"></i>&nbsp;Pour commencer</a></li>
							<li class="bordered-grey"><a href="/about"><i class="icon-comment"></i>&nbsp;Qui sommes-nous ?</a></li>
							<li class="bordered-grey"><a href="/contact"><i class="icon-envelope"></i>&nbsp;Contact</a></li>
							<li><a href="/terms"><i class="icon-bullhorn"></i>&nbsp;À propos</a></li>
						</ul>
					</div><!--/.nav-collapse -->
				</div><!--/.container-->
			</div><!--/.navbar-inner -->
			<div class="sub-navbar">
				<div class="container">
					<p><i class="icon icon-user"></i> Vous avez déjà un compte ? <a class="btn btn-login" href="/login">Connectez vous »</a></p>
				</div>
			</div>

			<?php
				}
			else
				{
				$user=$user_manager->getUser();
				$profile=$user->profile();
?>
								<!-- Rubriques de "base" -->
								<li class="bordered-grey"><a href="/"><i class="icon-home icon-white"></i>&nbsp;Accueil</a></li>
								<li class="bordered-grey"><a href="/files/"><i class="icon-folder-open icon-white"></i>&nbsp;Documents</a></li>
								<li class="bordered-grey"><a href="/friends"><i class="icon-user icon-white"></i>&nbsp;Amis</a></li>

								<!-- Notifs dropdown -->
								<li class="dropdown bordered-grey" id="dropnotif" data-dropdown="dropdown">
									<a class="dropdown-toggle" href="#" id="notifs_dd"><i class="icon-tags icon-white"></i>&nbsp;Notifications <span id="nbr_notifs">(0)</span></a>
									<ul id="dropdown-notif" class="dropdown-menu">
										<li><a href="#" id="no_notif">Vous n'avez pas de notifications pour le moment</a></li>
									</ul>
								</li> <!-- //notifs-dropdown -->

								<!-- memo dropdown -->
								<li class="dropdown bordered-grey" id="dropmemo" data-dropdown="dropdown">
									<a class="dropdown-toggle" href="#"><i class="icon-edit"></i> Notes</a>
									<ul id="dropdown-memo" class="dropdown-menu">
										<input id="idmemo" name="idmemo" value="5" type="hidden">
										<textarea id="notesarea" cols="" rows="" onkeypress="showBtnSave()"></textarea>
										<button id="savebutton" onclick="saveMemo()" class="btn">Sauvegarder</button>
									</ul>
								</li> <!-- //memo-dropdown -->

								<!-- music dropdown -->
								<li class="dropdown" id="dropmusic" data-dropdown="dropdown">
									<a class="dropdown-toggle" href="#"><i class="icon-music"></i> Lecteur audio</a>
									<ul id="dropdown-music" class="dropdown-menu">
										<div id="playercontainer"><object id="player" type="application/x-shockwave-flash" data="/dewplayer-rect.swf?mp3=/download/0&amp;autoplay=1&amp;showtime=true" height="20" width="220"></object></div>
									</ul>
								</li> <!-- //music-dropdown -->

							</ul>
					</div><!--/.nav-collapse -->
				</div><!--/.container-->
			</div><!--/.navbar-inner -->
			
			<div class="sub-navbar">
				<div class="container">
					<p>
						<?php
						if($user->profile()->avatar() != null) {
							echo '<a href="/users/'.STRTOLOWER($profile->pseudo()).'"><img id="avatarprofil" src="/download/'.$user->profile()->avatar().'/min" /></a>';
						}
						else {
							echo '<img id="avatarprofil" src="/images/transparent.png" />';
						}
						?>
						<span class="bordered">
							<a class="p" href="/users/<?php echo(STRTOLOWER($profile->pseudo())); ?>">» <?php echo($profile->firstname().' '.$profile->lastname()); ?></a>
						</span>						
						<span class="bordered menu">
							<i class="icon-hdd"></i> Espace libre : <?php echo round(\lib\Converter::size($user->config()->get('free_space'), "Go"), 3); ?> Go
						</span>
						<span class="menu"><a class="btn btn-login" href="/logout">Se déconnecter »</a></span>
					</p>
				</div>
			</div>
<?php
				}
?>
		</div>
			<div id="content">
			<?php echo $content; ?>
			</div>
		<footer>
			<div class="container">
					<div class="span12">
						<div class="row">
							<div class="span2">
								<h5><i class="icon-th-list"></i> Plan du site</h5>
									<div class="frub">
                                            <a href="/about">Qui sommes-nous ?</a><br />
                                            <a href="/contact">Contact</a><br />
                                            <a href="/terms#disponibilite" >Mentions légales</a><br />
                                            <a href="/terms">Conditions d'utilisations</a><br />
                                            <a href="/faq" >FAQ</a><br />
                                            <a href="/partners">Partenaires</a>
                                        </p>
                                    </div>
							</div>
							<div class="span3" style="margin-top:30px">
			                    <script type="text/javascript">google_ad_client="ca-pub-4983973769928411";google_ad_slot="4830354866";google_ad_width=234;google_ad_height=60;</script>
			                    <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
			                    <script type="text/javascript">google_ad_client="ca-pub-4983973769928411";google_ad_slot="4830354866";google_ad_width=234;google_ad_height=60;</script>
			                    <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
			                </div>
							<div class="span3">
                                <h5><i class="icon-comments"></i> Réseaux sociaux</h5>
                                <p>Discutez avec nous, partagez vos avis, suivez et soutenez le développement du projet sur <a href="http://twitter.com/shareanet"><i class="icon-twitter"></i> Twitter</a> et <a href="http://facebook.com/shareanet"> <i class="icon-facebook"></i> Facebook</a></p>
                                <select onchange="document.cookie = 'lang='+this.value+'; expires=Fri, 01 Jan 2100 00:0:00 UTC; path=/'; location.reload()">
                                    <option value="fr">Français</option>
                                    <option value="en">English</option>
                                </select>
                            </div>
							<div class="span4">
                                <h5><i class="icon-envelope-alt"></i> Newsletter</h5>
                                <p>Abonnez-vous à la newsletter pour être tenu au courant des nouveautés sur la plateforme!</p>
                                <form method="POST" action="/newsletter">
                                    <input id="newsmail" type="email" name="newsmail" placeholder="Votre e-mail"/><button type="submit" class="btn btn-primary">Go</button>
                                </form>
							</div>
						</div>
					</div>
			</div>
		</footer>

		<?php if($user_manager->isAuth()){ ?>
		<script type="text/javascript">
		function initPlayer() {
			var player = document.getElementById('player');
			player.setAttribute("data", "/dewplayer-rect.swf?mp3=/download/0&autoplay=1");
		}
		initPlayer();
		</script>
		<script type="text/javascript" src="/js/notifications.js"></script>
		<script type="text/javascript" src="/js/widgets.js"></script>
		<script type="text/javascript">getMemo()</script>
		<?php } ?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-28945226-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
	</body>
</html>
