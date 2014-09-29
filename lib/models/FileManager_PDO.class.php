<?php
	namespace lib\models;
	use \lib\entities\File;
	use \lib\Avatar;
	
	class FileManager_PDO extends FileManager
	{
		public function add(File $file, $passthrough = false)
		{
			/* Librairie à inclure pour créer la miniature si jamais $file est une img */
			include "../Avatar.class.php";
			
			/* Path generation */
			if(!$passthrough)
				{
					$path = $this->getFilePath($file);
					if(\lib\ToolBox::is_Error($path))
						{
							$path->addRoute('add(), FileManager_PDO.class.php');
							return $path;
						}
						
					$file->setPath($path);
					$file->setRealname($file->sha256());
				}
			/* Owner generation */
			if(!$file->owner())
				$file->setOwner($this->owner);
			
			$request = $this->dao->prepare("INSERT INTO files(name, realname, parent, mimetype, path, owner, permissions, date, sha256, md5, size) VALUES(:name, :realname, :parent, :mimetype, :path, :owner, :permissions, :date, :sha256, :md5, :size)");
			$request->bindValue(':name',$file->name());
			$request->bindValue(':realname',$file->realname());
			$request->bindValue(':parent',(int) $file->parent(), \PDO::PARAM_INT);
			$request->bindValue(':mimetype',$file->mimetype());
			$request->bindvalue(':path',$file->path());
			$request->bindValue(':owner',(int) $this->owner(), \PDO::PARAM_INT);
			$request->bindValue(':permissions', $file->permissions()->getSerial());
			$request->bindValue(':date', $file->date());
			$request->bindValue(':md5', $file->md5());
			$request->bindValue(':sha256', $file->sha256());
			$request->bindValue(':size', $file->size());
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->addRoute("add(), FileManager_PDO.class.php");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			
			if(!file_exists(dirname(__FILE__).'/../../files/'.$file->realname()))
				{
				/* Don't re-save te file if an user already uploaded it */
					if(!rename($file->tempname(), dirname(__FILE__).'/../../files/'.$file->realname()))
						{
							$error = new \lib\Error();
							$error->setMessage("Le fichier n'a pas pu être déplacé");
							$error->setWarnLevel(\lib\Error::wl_HIGH);
							$error->addRoute("add(), FileManager_PDO");
							return $error;
						}
						
					/* Then if the file is an image, creating the thumbnails */
					if(substr($file->mimetype(), 0, 6)  == 'image/')
						{
							\lib\Avatar::resizeImg(dirname(__FILE__).'/../../files/'.$file->realname(), dirname(__FILE__).'/../../files/'.$file->realname().'_min', 150, 150);
						}
				}
				
			return true;
		}

		public function update(File $file, $passthrough = false)
		{
			/* Path generation */
			if(!$passthrough)
				$file->setPath($this->getFilePath($file));
			/* Owner generation */
			if(!$file->owner())
				$file->setOwner($this->owner);
				
			$request = $this->dao->prepare("UPDATE files SET name = :name, realname = :realname, parent = :parent, mimetype = :mimetype, path = :path, permissions = :permissions, date = :date, sha256 = :sha256, md5 = :md5, size = :size WHERE id = :id AND owner = :owner");
			$request->bindValue(':name',$file->name());
			$request->bindValue(':realname',$file->realname());
			$request->bindValue(':parent',(int) $file->parent(), \PDO::PARAM_INT);
			$request->bindValue(':mimetype',$file->mimetype());
			$request->bindvalue(':path',$file->path());
			$request->bindValue(':owner',(int) $this->owner(), \PDO::PARAM_INT);
			$request->bindValue(':permissions', $file->permissions()->getSerial());
			$request->bindValue(':date', $file->date());
			$request->bindValue(':md5', $file->md5());
			$request->bindValue(':sha256', $file->sha256());
			$request->bindValue(':size', $file->size());
			$request->bindValue(':id', $file->id());
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->addRoute("add(), FileManager_PDO.class.php");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
				
			return true;
		}

		public function load($id)
		{
			$error = new \lib\Error();
			
			if(!$this->exists($id))
				{
					$error->setMessage("Le fichier que vous avez demandé de charger (".$id.") n'existe pas");
					$error->addRoute("load(), FileManager_PDO");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					return $error;
				}
			
			if($this->owner != '*')
				{
					$request = $this->dao->prepare("SELECT * FROM files WHERE id = :id AND owner = :owner ORDER BY name");
					$request->bindValue(':owner',$this->owner());
				}
			else
				$request = $this->dao->prepare("SELECT * FROM files WHERE id = :id ORDER BY name");
				
			$request->bindValue(':id',(int) $id, \PDO::PARAM_INT);
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{	
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->addRoute("load(), FileManager_PDO.class.php");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			
			$file = new File($request->fetch(\PDO::FETCH_ASSOC));
			return $file;
		}

		public function delete($id)
		{
			$error = new \lib\Error();
			
			$file = $this->load($id);
			if(\lib\ToolBox::is_Error($file))
				{
					$file->addRoute("delete(), FileManager_PDO.class.php");
					
					return $file;
				}

			if($this->owner != '*')
				{
					$request = $this->dao->prepare("DELETE FROM files WHERE id = :id AND owner = :owner");
					$request->bindValue(':owner',$this->owner());
				}
			else
				$request = $this->dao->prepare("DELETE FROM files WHERE id = :id");
				
			$request->bindValue(':id',(int) $id, \PDO::PARAM_INT);
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->addRoute("delete(), FileManager_PDO");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
				
			/* Now deleting the file */
			if(!$file->has_links($this))
				{
					if(!unlink(dirname(__FILE__).'/../../files/'.$file->sha256()))
						{
							$error->setMessage("Impossible du supprimer le fichier");
							$error->setWarnLevel(\lib\Error::wl_HIGH);
							$error->addRoute("delete(), FileManager_PDO");
					
							return $error;
						}
				}
			else
				return true;
		}

		public function exists($id) 
		{
			if($this->owner != '*')
				{
					$request = $this->dao->prepare("SELECT COUNT(id) as res FROM files WHERE id = :id and owner = :owner");
					$request->bindValue(':owner',$this->owner());
				}
			else
				$request = $this->dao->prepare("SELECT COUNT(id) as res FROM files WHERE id = :id");
			
			$request->bindValue(':id',(int) $id, \PDO::PARAM_INT);
			$request->execute();
			
			return $request->fetchColumn(0);
		}

		public function getFilePath(File $file)
		{
			$error = new \lib\Error();
			
			if(!$file->parent())
				{
					return '/'.$this->purifyName($file->name());
				}
			else
				{
					$foldermanager = new \lib\models\FolderManager_PDO($this->dao);
					$foldermanager->setOwner($this->owner);
					$parent_folder = $foldermanager->load($file->parent());
					if(\lib\ToolBox::is_Error($parent_folder))
						{
							$parent_folder->addRoute("getFilePath(), FileManager_PDO");
							return $parent_folder;
						}
					return $parent_folder->path().$this->purifyName($file->name());
				}
		}

		public function purifyName($string)
		{
			/* DEPRECATED 
			$name = \lib\ToolBox::stripAccents($string);
			$name = str_replace(',', '_', $name);
			$name = str_replace(':', '_', $name);
			$name = str_replace(';', '_', $name);
			$name = str_replace(' ', '_', $name);
			$name = str_replace('"', '_', $name);
			$name = str_replace('\'', '_', $name);
		
			return $name; */
			
			return $string;
		}
		
		public function pathToId($path)
		{
			$error = new \lib\Error();

			$request = $this->dao->prepare("SELECT id FROM files WHERE path = :path AND owner = :owner");
			$request->bindValue(':path',$path, \PDO::PARAM_INT);
			$request->bindValue(':owner',$this->owner());

			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->addRoute("pathtoId(), FileManager_PDO");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			return $request;
		}
		
		public function getFilesOfFolder($parent)
		{
			$error = new \lib\Error();
			
			if($this->owner != '*')
				{
					$request = $this->dao->prepare("SELECT id FROM files WHERE parent = :parent AND owner = :owner ORDER BY name");
					$request->bindValue(':owner', (int) $this->owner, \PDO::PARAM_INT);
				}
			else
				$request = $this->dao->prepare("SELECT id FROM files WHERE parent = :parent");
				
			$request->bindValue(':parent', (int) $parent, \PDO::PARAM_INT);
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->addRoute("getFilesOfFolder(), FileManager_PDO");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			$list2 = array();
			$list = $request->fetchAll(\PDO::FETCH_ASSOC);
			foreach($list as $id)
				{
					$list2[] = $id['id'];
				}
			return $list2;
		}
		
		public function file_has_links(File $file)
		{
			$request = $this->dao->prepare("SELECT COUNT(id) FROM files WHERE sha256 = :sha256 AND md5 = :md5");
			$request->bindValue(':sha256', $file->sha256());
			$request->bindValue(':md5', $file->md5());
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->addRoute("file_has_links(), FileManager_PDO");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			
			return $request->fetchColumn(0);
		}

		public function getFilesOfType($type)
		{
			$error = new \lib\Error();
			
			if($this->owner != '*')
				{
					$request = $this->dao->prepare("SELECT id FROM files WHERE mimetype LIKE :type AND owner = :owner ORDER BY name");
					$request->bindValue(':owner', (int) $this->owner, \PDO::PARAM_INT);
				}
			else
				$request = $this->dao->prepare("SELECT id FROM files WHERE mimetype like :type ORDER BY name");
				
			$request->bindValue(':type', $type, \PDO::PARAM_INT);
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->addRoute("getFilesOfFolder(), FileManager_PDO");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			$list2 = array();
			$list = $request->fetchAll(\PDO::FETCH_ASSOC);
			foreach($list as $id)
				{
					$list2[] = $id['id'];
				}
			return $list2;
		}

		/* Récupère tous les fichiers */
		public function getAllFiles()
		{
			$error = new \lib\Error();
			$request = $this->dao->prepare("SELECT id FROM files WHERE owner = :owner ORDER BY name");
			$request->bindValue(':owner', $this->owner);
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->addRoute("getFilesOfFolder(), FileManager_PDO");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			$list2 = array();
			$list = $request->fetchAll(\PDO::FETCH_ASSOC);
			foreach($list as $id)
				{
					$list2[] = $id['id'];
				}
			return $list2;
		}

		/* Fonction de suppression de tous les fichiers d'un utilisateur */
		public function deleteAllFiles()
		{
			$error = new \lib\Error();
			$files = $this->getAllFiles();

			/* Suppression physique de tous les fichiers */
			foreach($files as $id)
			{	
				$file = $this->load($id);
				
				/* Now deleting the file */
				if(!$file->has_links($this))
				{
					if(!unlink(dirname(__FILE__).'/../../files/'.$file->sha256()))
					{
						$error->setMessage("Impossible du supprimer le fichier");
						$error->setWarnLevel(\lib\Error::wl_HIGH);
						$error->addRoute("deleteAllFiles(), FileManager_PDO");
						return $error;
					}
				}
			}

			/* Suppression dans la BDD */
			$request = $this->dao->prepare("DELETE FROM files WHERE owner = :owner");
			$request->bindValue(':owner',$this->owner());
			try
			{
				$request->execute();
			}
			catch(Exception $e)
			{
				$error->setMessage("Impossible d'éxécuter la requête SQL");
				$error->addRoute("deleteAllFiles(), FileManager_PDO");
				$error->setWarnLevel(\lib\Error::wl_CRITICAL);
				return $error;
			}
		}


		/* Fonction qui recherche un fichier d'après son nom */
		/* Retourne les fichiers tel que le donne la requete (PAS OBJET) */
		public function searchFile($filename) {
			$error = new \lib\Error();
			$request = $this->dao->prepare("SELECT * FROM files WHERE UPPER(name) LIKE :filename AND owner = :owner ORDER BY name");
			$request->bindValue(':filename', '%'.strtoupper($filename).'%', \PDO::PARAM_STR);
			$request->bindValue(':owner', (int) $this->owner, \PDO::PARAM_INT);

			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error->setMessage("Impossible d'éxécuter la requête SQL");
					$error->addRoute("searchFile(), FileManager_PDO");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			//$files = array();
			$result = $request->fetchAll(\PDO::FETCH_ASSOC);
			foreach($result as $file)
				{
					$files[] = $file;
				}
			return $files;
		}
	}
?>
