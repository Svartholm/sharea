<?php
	namespace lib\models;
	
	class FriendManager_PDO extends FriendManager
	{
		protected $friends = array();

		public function after_setOwner()
		{
			$this->friends = $this->getFriends();
		}

		// Envoi une demande d'ajout � la liste d'amis
		public function invite($id)
		{
			if(!$this->hasInvite($this->owner, $id)) // si l'user n'a pas encore invit� la personne
			{
				$error = new \lib\Error();
				$req = "INSERT INTO frequests VALUES(:user, :id)";
				$request = $this->dao->prepare($req);
				$request->bindValue(':user',(int) $this->owner, \PDO::PARAM_INT);
				$request->bindValue(':id',(int) $id, \PDO::PARAM_INT);
				try {
					$request->execute();
				}
				catch(Exception $e) {	
					$error->setMessage($e);
					$error->addRoute("invite(), FriendManager_PDO.class.php");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);	
					return $error;
				}
				return true;
			}
			else 
				return false;
		}

		// D�cline l'invitation
		public function decline($id)
		{
			$error = new \lib\Error();
			$req = "DELETE FROM frequests WHERE (requester = :user AND requested = :id) OR (requester = :id AND requested = :user)";
			$request = $this->dao->prepare($req);
			$request->bindValue(':user',(int) $this->owner, \PDO::PARAM_INT);
			$request->bindValue(':id',(int) $id, \PDO::PARAM_INT);	
			try {
				$request->execute();
			}
			catch(Exception $e) {	
				$error->setMessage($e);
				$error->addRoute("decline(), FriendManager_PDO.class.php");
				$error->setWarnLevel(\lib\Error::wl_CRITICAL);
				return $error;
			}
			return true;		
		}
		
		// Ajoute l'ami apr�s confirmation
		public function add($id)
		{
			// Enregistre la nouvelle liste d'amis de l'user de l'instance
			$error = new \lib\Error();
			if(!in_array($id, $this->friends)) {
				$this->friends[] = $id;
			}
			$r = $this->save();
			if(\lib\ToolBox::is_Error($r)) {
					$r->addRoute("FriendManager_PDO, add()");
			}		
		}

		public function delete($id) // supprimes une personne de la liste et la save
		{
			$error = new \lib\Error();		
			if(!in_array($id, $this->friends)) 	{
				$error->setMessage("L'amis que vous avez demand� de supprimer n'existe pas");
				$error->addRoute("delete(), FriendManager");
				$error->setWarnLevel(\lib\Error::wl_LOW);
				return $error;
			}			
			unset($this->friends[array_search($id, $this->friends)]);
			$this->friends = array_values($this->friends);		
			$r = $this->save();
			if(\lib\ToolBox::is_Error($r)) {
					$r->addRoute("FriendManager_PDO, delete()");
			}				
			return $r;
		}

		public function getNumberOfFriends() // retournes le nombre d'amis
		{
			return count($this->friends);
		}
		
		public function getInvitations() // Renvoie un tableau contenant les invitations d'amis
		{
			$error = new \lib\Error();
			$request = $this->dao->prepare("SELECT requester FROM frequests WHERE requested = :user");
			$request->bindValue(':user',(int) $this->owner, \PDO::PARAM_INT);		
			try {
				$request->execute();
			}
			catch(Exception $e) {	
				$error->setMessage("Impossible d'�x�cuter la requ�te SQL");
				$error->addRoute("getInvitations(), FriendManager_PDO.class.php");
				$error->setWarnLevel(\lib\Error::wl_CRITICAL);
				return $error;
			}
			return $request->fetchAll();
		}			
		
		public function isFriend($id) // Fonction bool�enne pour savoir si $id fait parti de la liste d'amis
		{
			return in_array($id, $this->friends);
		}
		
		public function getSerial() // renvoit le serial d'amis de la forme a1;a2;a3 ...
		{
			$serial = "";
			foreach($this->friends as $friend) {
				$serial = $serial.$friend.';';
			}
			return $serial;		
		}

		public function getFriends() // renvoit le tableau contenant les id des amis
		{
			$error = new \lib\Error();
			$request = $this->dao->prepare("SELECT friendslist FROM friends WHERE id = :user");
			$request->bindValue(':user',(int) $this->owner, \PDO::PARAM_INT);	
			try {
				$request->execute();
			}
			catch(Exception $e) {	
				$error->setMessage("Impossible d'�x�cuter la requ�te SQL");
				$error->addRoute("getFriends(), FriendManager_PDO.class.php");
				$eror->setWarnLevel(\lib\Error::wl_CRITICAL);
				return $error;
			}
			$friends = $request->fetchColumn(0);
			$friends = explode(";", $friends);
			$keys = array_keys($friends, '');		
			foreach($keys as $key) {
				unset($friends[$key]);
			}			
			return $friends;
		}

		public function create() // cr�e une liste d'amis vide dans la BDD
		{
			$error = new \lib\Error();
			if($this->getFriends() == 0)
				$this->create();
			$request = $this->dao->prepare("INSERT INTO friends VALUES(:user, '')");
			$request->bindValue(':user',(int) $this->owner, \PDO::PARAM_INT);	
			try {
				$request->execute();
			}
			catch(Exception $e) {	
				$error->setMessage("Impossible d'�x�cuter la requ�te SQL");
				$error->addRoute("create(), FriendManager_PDO.class.php");
				$eror->setWarnLevel(\lib\Error::wl_CRITICAL);		
				return $error;
			}
			return $request;
		}
		
		public function save() // enregistre la nouvelle liste dans la BDD
		{
			if($this->owner != '*')
				{
					$request = $this->dao->prepare("INSERT INTO friends (id, friendslist) VALUES (:owner, :friendslist) ON DUPLICATE KEY UPDATE friendslist = :friendslist");
					$request->bindValue(':owner',(int) $this->owner, \PDO::PARAM_INT);
				}
			else
					$request = $this->dao->prepare("INSERT INTO friends (id, friendslist) VALUES (:owner, :friendslist) ON DUPLICATE KEY UPDATE friendslist = :friendslist");
					//UPDATE friends SET friendslist = :friendslist WHERE 1
			$request->bindValue(':friendslist', $this->getSerial());
			try {
				$request->execute();
			}
			catch(Exception $e) {
				$error = new \lib\Error();
				$error->setMessage("Impossible d'�x�cuter la requ�te SQL");
				$error->addRoute("save(), FriendManager_PDO.class.php");
				$error->setWarnLevel(\lib\Error::wl_CRITICAL);
				return $error;
			}
			return true;
		}
		
		public function hasInvite($id1, $id2) // regarde si une invitation a deja �t� envoy� entre $id1 et $id2
		{
			$error = new \lib\Error();
			$req = "SELECT COUNT(*) FROM frequests WHERE (requester = :id1 AND requested = :id2) OR (requester = :id2 AND requested = :id1)";
			$request = $this->dao->prepare($req);
			$request->bindValue(':id1',(int) $id1, \PDO::PARAM_INT);
			$request->bindValue(':id2',(int) $id2, \PDO::PARAM_INT);	
			try {
				$request->execute();
			}
			catch(Exception $e) {	
				$error->setMessage($e);
				$error->addRoute("hasInvite(), FriendManager_PDO.class.php");
				$error->setWarnLevel(\lib\Error::wl_CRITICAL);
				return $error;
			}
			return $request->fetchColumn(0) != 0;
		}
	}
?>
