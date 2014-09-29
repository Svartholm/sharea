<?php
	namespace lib\models;
	abstract class FriendManager extends \lib\Manager
	{
		public abstract function invite($id); // envoi une demande d'ajout � la liste d'amis
		public abstract function add($id); // ajoute une personne � la liste d'amis
		public abstract function delete($id); // supprime l'ami de la liste
		public abstract function decline($id); // refuse la demande d'ajout
		public abstract function getNumberOfFriends(); // retourne le nombre d'amis
		public abstract function isFriend($id); // renvoit vrai si les 2 personnes sont amis
		public abstract function getSerial(); // renvoit la liste d'amis sous forme id1;id2;id3
		public abstract function getFriends(); // retourne la liste d'amis sous forme d'array
		public abstract function getInvitations(); // retourne les demandes d'ajout dans un array
		public abstract function save(); // update la liste d'amis
		public abstract function create(); // cr�e une liste d'amis vide
		public abstract function hasInvite($id1, $id2); // regarde si 2 personnes se sont deja envoy�es des invitations
	}
?>
