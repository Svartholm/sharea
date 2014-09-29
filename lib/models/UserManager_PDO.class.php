<?php
	namespace lib\models;
	use \lib\entities\User;
	
	class UserManager_PDO extends UserManager
	{
		protected $vss;
		
		const checkByPseudo = 1,
					checkByMail = 2;
					
		public function bindVSS(\lib\VSS $space)
		{
			$this->vss = $space;
		}
		
		public function add(User $user)
		{
			/* FUNCTION : Add a user to the db */
			$request = $this->dao->prepare(	"INSERT INTO users (password, id_profile, config, date) ".
																			"VALUES(:password, :id_profile, :config, :date)");
			$request->bindValue(':password', $user->password());
			$request->bindValue(':id_profile', $user->profile()->id(), \PDO::PARAM_INT);
			$request->bindValue(':config', $user->config()->getSerial());
			$request->bindValue(':date', $user->date());
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->addRoute("UserManager_PDO, add()");
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
				
			return $this->load($this->pseudoToId($user->profile()->pseudo()));
		}
		
		public function delete(User $user)
		{
			$request = $this->dao->prepare("DELETE FROM users WHERE id = :id");
			$request->bindValue(':id', $user->id());
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->addRoute("UserManager_PDO, delete()");
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}

			return 0;
		}

		public function update(User $user)
		{
			/* FUNCTION : Updates an user to the db */
			$request = $this->dao->prepare(	"UPDATE users ".
																			"SET password = :password, ".
																			"id_profile = :id_profile, ".
																			"config = :config, ".
																			"date = :date ".
																			"WHERE id = :id");
																			
			$request->bindValue(':password', $user->password());
			$request->bindValue(':id_profile', $user->profile()->id(), \PDO::PARAM_INT);
			$request->bindValue(':config', $user->config()->getSerial());
			$request->bindValue(':id', (int) $user->id(), \PDO::PARAM_INT);
			$request->bindValue(':date', $user->date());
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->addRoute("UserManager_PDO, update()");
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
				
			return $this->load($this->pseudoToId($user->profile()->pseudo()));
		} 
		public function load($id)
		{
			/* FUNCTION : Load a user using his id. Return the user's instance */
			$request = $this->dao->prepare("SELECT * FROM users WHERE id = :id");
			$request->bindValue(':id', (int)$id, \PDO::PARAM_INT);
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->addRoute("load, UserManager_PDO");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			
			$result = $request->fetch(\PDO::FETCH_ASSOC);
			
			if(!$result)
				{
					$error = new \lib\Error();
					$error->setMessage("Impossible d'associer le tableau SQL à l'user");
					$error->addRoute("load, UserManager_PDO");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			
			$user = new \lib\entities\User($result);
			$profile_manager = new ProfileManager_PDO($this->dao);
			$profile = $profile_manager->load($user->id_profile());
			
			if(\lib\ToolBox::is_Error($profile))
				{
					return $profile;
				}
			
			$user->setProfile($profile);
			return $user;
		}
		
		public function pseudoToId($pseudo)
		{
			/* FUNCTION : Return the user id using his pseudo. */
			$profile_manager = new ProfileManager_PDO($this->dao);
			$profile_id = $profile_manager->pseudoToId($pseudo);
			$request = $this->dao->prepare(	"SELECT id ".
																			"FROM users ".
																			"WHERE id_profile = :id_profile" );
																			
			$request->bindValue(":id_profile", $profile_id, \PDO::PARAM_INT);
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->addRoute("UserManager_PDO, pseudoToId()");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			
			$id = $request->fetchColumn(0);
			if(!$id)
				{
					/* Wrong username */
					$error = new \lib\Error();
					$error->setMessage("L'utilisateur n'existe pas ou plus");
					$error->addRoute("UserManager_PDO, pseudoToId()");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					
					return $error;
				}
			else
				return $id;
		}
		
		public function checkAuth($pseudo, $password, $method)
		{
			/* FUNCTION : Check if the user correspond to the password. 
				 Return user's id if true */
			if($method == self::checkByMail)
				$id = $this->emailToId($pseudo);
			else
				$id = $this->pseudoToId($pseudo);
				
			if(\lib\ToolBox::is_Error($id))
				{
					return $id;
				}
			
			$password = hash("sha512", $password);
			$request = $this->dao->prepare(	"SELECT COUNT(id) ".
																			"FROM users ".
																			"WHERE id = :id AND password = :password");
			$request->bindValue(":id",(int) $id, \PDO::PARAM_INT);
			$request->bindValue(":password", $password);
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->addRoute("checkAuth(), UserManager_PDO");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			
			if ($request->fetchColumn(0) == 1)
				return $id;
			else if($request->fetchColumn(0) == 0)
				{
					$error = new \lib\Error();
					$error->setMessage("Pseudonyme/email ou mot de passe invalide");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$error->addRoute("checkAuth(), UserManager_PDO");
					return $error;
				}
			else
				{
					$error = new \lib\Error();
					$error->setWarnLevel(\lib\Error::wl_HIGH);
					$error->setMessage("Identifiant retourné invalide");
					$error->addRoute("checkAuth(), UserManager_PDO");
					return $error;
				}
		}
		
		public function emailToId($email)
		{
			/* FUNCTION : Return the user id using his email. */
			$profile_manager = new ProfileManager_PDO($this->dao);
			$profile_id = $profile_manager->emailToId($email);
			if(\lib\ToolBox::is_Error($profile_id))
				{
					$profile_id->addRoute("emailtoId, UserManager_PDO");
					return $profile_id;
				}
			$request = $this->dao->prepare(	"SELECT id ".
																			"FROM users ".
																			"WHERE id_profile = :id_profile" );
																			
			$request->bindValue(":id_profile", $profile_id, \PDO::PARAM_INT);
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->addRoute("emailToId, UserManager_PDO");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			
			$id = $request->fetchColumn(0);
			if(!isset($id))
				{
					/* Wrong username */
					$error = new \lib\Error();
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->addRoute("emailToId, UserManager_PDO");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					
					return $error;
				}
			else
				return $id;
		}
		
		public function Auth($pseudo, $password)
		{
			/* Auth the user. Returns user's id if success */
			if(\lib\Regex::isEmail($pseudo))
				$id = $this->checkAuth($pseudo, $password, self::checkByMail);
			else
				$id = $this->checkAuth($pseudo, $password, self::checkByPseudo);
				if(\lib\ToolBox::is_Error($id))
				{
					$id->addRoute("Auth, UserManager_PDO");
					return $id;
				}
			
			/* Does not need to be checked for error (already done in checkAuth) */
			$r = $this->load((int) $id);
			if(\lib\ToolBox::is_Error($r))
				{
					$r->addRoute("Auth, UserManager_PDO");
					return $r;
				}
			
			/* Now setting all user infos in session */
				/* Profile */
			$this->vss->set('user', serialize($r));
			
			/* Set user's state to connected */
			$this->vss->set('connected', true);
			return true;
		}
		
		public function AuthFB($email) // faudra rajouter $id encore
		{			
			/* Does not need to be checked for error (already done in checkAuth) */
			$r = $this->load((int) $this->emailToId($email));
			if(\lib\ToolBox::is_Error($r))
				{
					$r->addRoute("AuthFB, UserManager_PDO");
					return $r;
				}
			
			/* Now setting all user infos in session */
				/* Profile */
			$this->vss->set('user', serialize($r));
			
			/* Set user's state to connected */
			$this->vss->set('connected', true);
			return true;
		}
		
		public function isAuth()
		{
			return $this->vss->get('connected') == true ? true : false;
		}
		
		public function getUser()
		{
			if(!$this->isAuth())
				{
					$error = new \lib\Error();
					$error->addRoute("getuser(), UserManager_PDO.class.php");
					$error->setTemplate('!Auth');
					return $error;
				}
			
			$user = unserialize($this->vss->get('user'));
			
			return $user;
		}
		
		public function reloadSession(User $user)
		{
			$this->vss->set('user', serialize($user));
		}
		
		public function logout()
		{
			$this->vss->set('connected', false);
		}
		
		public function exists($id)
		{
			$request = $this->dao->prepare("SELECT COUNT(id) FROM users WHERE id = :id");
			$request->bindValue(':id', (int) $id, \PDO::PARAM_INT);
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->addRoute("exists(), UserManager_PDO");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			$r = $request->fetchColumn(0);
			if(!$r)
				return false;
			else
				return true;
		}

		public function search($term)
		{
			$infos = explode(" ",$term);
			for($i=0; $i<count($infos); $i++)
			{
				$request = $this->dao->prepare("SELECT id FROM users WHERE id_profile IN(SELECT id FROM profiles WHERE lower(pseudo) LIKE lower(:term) OR lower(firstname) LIKE lower(:term) OR lower(lastname) LIKE lower(:term))");
				$request->bindValue(':term', $infos[$i]);
			
				try
					{
						$request->execute();
					}
				catch(Exception $e)
					{
						$error = new \lib\Error();
						$error->setMessage("Impossible d'éxécuter la requête SQL");
						$error->addRoute("load, UserManager_PDO");
						$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
						return $error;
					}

				$results = $request->fetchAll();
				foreach($results as $result)
					{
						$r[] = $result['id'];
					}
			}
			$r = array_unique($r);
			return $r;
		}
		
		public function isAdmin(\lib\entities\User $user)
		{
			$c = $user->config();
			return $c->get('isAdmin') == true ? true : false;
		}
	}
?>
