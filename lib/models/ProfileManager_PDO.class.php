<?php
	namespace lib\models;
	use \lib\entities\Profile;
	
	class ProfileManager_PDO extends ProfileManager
	{
		public function add(Profile $profile)
		{
			/* FUNCTION : add the profile to the db. This function does not verify the
				 integrity/authenticity of data */
			$request = $this->dao->prepare("INSERT INTO profiles (firstname, lastname, email, pseudo) VALUES(:firstname, :lastname, :email, :pseudo)");
			$request->bindValue(':firstname', $profile->firstname());
			$request->bindValue(':lastname', $profile->lastname());
			$request->bindValue(':email', $profile->email());
			$request->bindValue(':pseudo', $profile->pseudo());
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->addRoute("ProfileManager_PDO, add()");
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
				
			return $this->load($this->pseudoToId($profile->pseudo()));
		}
		
		public function delete(Profile $profile)
		{
			$request = $this->dao->prepare("DELETE FROM profiles WHERE id = :id");
			$request->bindValue(':id', $profile->id());
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->addRoute("ProfileManager_PDO, delete()");
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			return 0;
		}

		public function update(Profile $profile)
		{
			/* FUNCTION : update the profile to the db. This function does not verify the
				 integrity/authenticity of data */
			$request = $this->dao->prepare("UPDATE profiles SET firstname = :firstname, lastname = :lastname, email = :email, pseudo = :pseudo, avatar = :avatar WHERE id = :id");
			$request->bindValue(':firstname', $profile->firstname());
			$request->bindValue(':lastname', $profile->lastname());
			$request->bindValue(':email', $profile->email());
			$request->bindValue(':pseudo', $profile->pseudo());
			$request->bindValue(':id', $profile->id());
			$request->bindValue(':avatar', $profile->avatar());
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->addRoute("ProfileManager_PDO, update()");
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
				
			return $this->load($this->pseudoToId($profile->pseudo()));
		}
		
		public function exists($row, $value)
		{
			/* FUNCTION : Check if the row $row is equal to $value.
				 Returns the number of occurrences for $rox = value, 0 if any */
			$request = $this->dao->prepare("SELECT COUNT($row) FROM profiles WHERE $row = :value");
			$request->bindValue(':value', $value);
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->setMessage("Impossible d'éxécuter la requête SQL dans ProfileManager_PDO (exists())");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			
			return $request->fetchColumn(0);
		}
		
		public function pseudoToId($pseudo)
		{
			/* FUNCTION : returns the profile id using his pseudo. Returns -1 if the id
				 does not exist */
			$request = $this->dao->prepare("SELECT id FROM profiles WHERE pseudo = :pseudo");
			$request->bindValue(':pseudo', $pseudo);
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->setMessage("Impossible d'éxécuter la requête SQL dans ProfileManager_PDO (pseudoToId())");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			
			return $request->fetchColumn(0);
		}

		public function profileIdToId($id_profile)
		{
			/* FUNCTION : returns the profile id using his pseudo. Returns -1 if the id
				 does not exist */
			$request = $this->dao->prepare("SELECT id FROM users WHERE id_profile = :id_profile");
			$request->bindValue(':id_profile', $id_profile);
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->setMessage("Impossible d'éxécuter la requête SQL dans ProfileManager_PDO (pseudoToId())");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			
			return $request->fetchColumn(0);
		}
		
		public function emailToId($email)
		{
			/* FUNCTION : returns the profile id using his pseudo. Returns -1 if the id
				 does not exist */
			$request = $this->dao->prepare("SELECT id FROM profiles WHERE email = :email");
			$request->bindValue(':email', $email);
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->addRoute("emailToId, ProfileManager_PDO");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			
			return $request->fetchColumn(0);
		}
		
		public function load($id)
		{
			/* FUNCTION : Load a profile using his id and return his instance */
			$request = $this->dao->prepare("SELECT * FROM profiles WHERE id = :id");
			$request->bindValue(':id', (int)$id, \PDO::PARAM_INT);
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->setMessage("Impossible d'éxécuter la requête SQL dans ProfileManager_PDO (load())");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			
			$profile = new Profile($request->fetch(\PDO::FETCH_ASSOC));
			return $profile;
		}

	}
?>
