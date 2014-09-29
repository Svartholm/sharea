<script>lire(0);</script>
<script src="/js/bootstrap-modal.js"></script>
<div class="container">
<h3>Utilisateurs correspondant à votre recherche : <?php echo $nb; ?></h3>
<br />
<table class="table table-striped">
<thead>
	<tr>
	<th>Pseudo</th>
	<th>Prénom</th>
	<th>Nom</th>
	<th></th>
	</tr>
</thead>

<tbody>
	<?php
	foreach($found as $user)
	{
		echo "<tr><td>".$user->profile()->pseudo()."</td><td>";
		
    $c = $user->config();
		if($c->get('show_firstname') !== false)
			echo $user->profile()->firstname();
		else
			echo "Non précisé";

		echo "</td><td>";

		if($c->get('show_lastname') !== false)
			echo $user->profile()->lastname();
		else
			echo "Non précisé";

		echo "</td><td><a href=\"/friends/invite/".$user->id()."\"><button class=\"btn btn-primary\">Ajouter à la liste d'amis</button></a></td></tr>";
	}
	?>
	</tobdy>
</table>
</div>
