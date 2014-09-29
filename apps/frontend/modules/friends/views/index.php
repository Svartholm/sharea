<script>lire(0);</script>
<script type="text/javascript" src="/js/bootstrap-modal.js"></script>

<?php
require $modal_removeFriend;
?>
		<!-- Invitations -->
		<div id="invitation-friend" class="alert friends-info">	
			<h4 class="alert-heading">Liste d'amis</h4><br />
			<?php
			if(count($invit) != 0) {
				foreach($invit as $personne)
				{
					echo "<span>".$personne->profile()->firstname()." ".$personne->profile()->lastname()."</span> &nbsp; <a class=\"btn btn-primary\" href=\"/friends/accept/".$personne->id()."\">Accepter</a> <a class=\"btn\" href=\"/friends/decline/".$personne->id()."\">Refuser</a><br /><br />";
				}
			}
			?>
			<p>Entrez ici le nom, prénom, ou pseudo de votre ami et commencez à partager des documents avec lui !</p>
		  <form class="form-search" action="/friends/search" method="POST" name="search">
			  <input type="text" id="searchFriends" name="searchFriends" placeholder="Exemple : jean dupont" onkeyup="cherche()">
			  <button type="submit" class="btn btn-primary">Rechercher</button>
			</form>
		</div>

	<!-- Liste des amis -->
	<ul id="friends-list" class="thumbnails">
		<script>
			var amis = new Array();

			function supprimer(id, nom) {
				document.getElementById("friendtoremove").value=id;
				document.getElementById("fname").innerHTML=nom;
			}

			function getFriends()
			{
				jQuery.post("/ajax/friends/getfriends", {}, 
					function(data) {
						amis = data;
						displayFriends(data);
					}
					, "json"
				);
			}

			function purge()
			{
				document.getElementById("friends-list").innerHTML = "";
			}

			getFriends();

			function displayFriends(data)
			{
				var space = document.getElementById("friends-list");
				for(var i=0; i < data.friends.length; i++) {
						var a = document.createElement("li");
						inner = '<a href="/users/'+data.friends[i].pseudo+'/files/"><li class="span2 friends"><div class="thumbnail thumbnail-friends"><p>'+data.friends[i].pseudo+'</p>';

						if(data.friends[i].avatar != null) {
							inner += '<img src="/download/'+data.friends[i].avatar+'/min" alt="avatar">';
						}
						else {
							inner += '<img src="/images/ami.png" alt="avatar">';
						}
						inner += '<p>'+data.friends[i].firstname+' '+data.friends[i].lastname+'</p></div><div class="link-friends"><a href="/users/'+data.friends[i].pseudo+'/files/"><button class="btn"><i class="icon-folder-open"></i></button></a>&nbsp;<button class="btn" onclick="supprimer('+data.friends[i].id+', \''+data.friends[i].firstname + ' ' + data.friends[i].lastname+'\')" data-controls-modal="supprimeramis" data-backdrop="static"><i class="icon-trash"></i></button></div></li></a>';
						a.innerHTML = inner;
						space.appendChild(a);
				}	
			}

			function cherche()
			{
				purge();
				var space = document.getElementById("friends-list");
				for(var i=0; i < amis.friends.length; i++) {
						var pseudo = amis.friends[i].pseudo.toLowerCase();
						var prenom = amis.friends[i].firstname.toLowerCase();
						var nom = amis.friends[i].lastname.toLowerCase();
						var pn = prenom+' '+nom;
						if(pseudo.indexOf(document.getElementById("searchFriends").value.toLowerCase()) != -1
							|| prenom.indexOf(document.getElementById("searchFriends").value.toLowerCase()) != -1
							|| nom.indexOf(document.getElementById("searchFriends").value.toLowerCase()) != -1
							|| pn.indexOf(document.getElementById("searchFriends").value.toLowerCase()) != -1) {
								var a = document.createElement("li");
								inner = '<a href="/users/'+amis.friends[i].pseudo+'/files/"><li class="span2 friends"><div class="thumbnail thumbnail-friends"><p>'+amis.friends[i].pseudo+'</p>';

								if(amis.friends[i].avatar != null) {
									inner += '<img src="/download/'+amis.friends[i].avatar+'/min" alt="avatar">';
								}
								else {
									inner += '<img src="/images/ami.png" alt="avatar">';
								}
								inner += '<p>'+amis.friends[i].firstname+' '+amis.friends[i].lastname+'</p></div><div class="link-friends"><a href="/users/'+amis.friends[i].pseudo+'/files/"><button class="btn"><i class="icon-folder-open"></i></button></a>&nbsp;<button class="btn" onclick="supprimer('+amis.friends[i].id+', \''+amis.friends[i].firstname + ' ' + amis.friends[i].lastname+'\')" data-controls-modal="supprimeramis" data-backdrop="static"><i class="icon-trash"></i></button></div></li></a>';
								a.innerHTML = inner;
								space.appendChild(a);
						}
				}
			}
		</script>
	</ul>
</div>
