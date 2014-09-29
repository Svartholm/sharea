<?php
	namespace lib\models;
	use \lib\entities\Folder;
	
	class FolderManager_PDO extends FolderManager
	{ 		
		/* Methods */
		public function add(Folder $folder, $passthrough = false)
		{
			/* Path generation */
			if(!$passthrough)
				$folder->setPath($this->getFolderPath($folder));
			/* Owner generation */
			if(!$folder->owner())
				$folder->setOwner($this->owner);
			
			/* Check if the folder already exists */
			if($this->exists($this->path_to_id($folder->path())))
				{
					$error = new \lib\Error();
					$error->addRoute("add(), \lib\models\FolderManager_PDO.class.php");
					$error->setMessage("Le dossier existe déjà");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					
					return $error;
				}
				
			$request = $this->dao->prepare("INSERT INTO folders(name, parent, path, owner, permissions, date) VALUES(:name, :parent, :path, :owner, :permissions, :date)");
			$request->bindValue(':name',$folder->name());
			$request->bindValue(':parent',(int) $folder->parent());
			$request->bindvalue(':path',$folder->path());
			$request->bindValue(':owner',$this->owner());
			$request->bindValue(':permissions', $folder->permissions()->getSerial());
			$request->bindValue(':date', $folder->date());
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->setMessage("Impossible d'éxécuter la requête SQL dans \lib\models\FolderManager_PDO (add())");
					$error->addRoute("add(), \lib\models\FolderManager_PDO.class.php");
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
					$error->setMessage("Le dossier que vous avez demandé de charger n'existe pas");
					$error->addRoute("load(), \lib\models\FolderManager_PDO");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					return $error;
				}
				
			$request = $this->dao->prepare("SELECT * FROM folders WHERE id = :id AND owner = :owner");
			$request->bindValue(':id',(int) $id, \PDO::PARAM_INT);
			$request->bindValue(':owner',$this->owner());
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{	
					$error->setMessage("Impossible d'éxécuter la requête SQL dans \lib\models\FolderManager_PDO (load())");
					$error->addRoute("load(), \lib\models\FolderManager_PDO.class.php");
					$eror->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			
			$folder = new Folder($request->fetch(\PDO::FETCH_ASSOC));
			return $folder;
		}
		
		public function path_to_id($path)
		{
			$request = $this->dao->prepare("SELECT id FROM folders WHERE path = :path AND owner = :owner");
			$request->bindValue(":path", $path);
			$request->bindValue(":owner", $this->owner());
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->addRoute("path_to_id(), \lib\models\FolderManager_PDO.class.php");
					$error->setMessage("Impossible d'éxécuter la requête SQL dans \lib\models\FolderManager_PDO (path_to_id())");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			
			return $request->fetchColumn(0);
		}
		
		public function update(Folder $folder, $passthrough = false)
		{
			/* Path generation */
			$folder->setPath($this->getFolderPath($folder));
			/* Owner generation */
			if(!$folder->owner())
				$folder->setOwner($this->owner());
			
			if(!$passthrough)
				{
					if($this->exists($this->path_to_id($folder->path())))
						{
							$error = new \lib\Error();
							$error->addRoute("update(), \lib\models\FolderManager_PDO.class.php");
							$error->setMessage("Le dossier existe déjà");
							$error->setWarnLevel(\lib\Error::wl_LOW);
					
							return $error;
						}
				}
				
			$request = $this->dao->prepare("UPDATE folders SET name = :name, parent = :parent, path = :path, permissions = :permissions WHERE id = :id AND owner = :owner");
			$request->bindValue(':name',$folder->name());
			$request->bindValue(':path', $folder->path());
			$request->bindValue(':parent',(int) $folder->parent());
			$request->bindValue(':id',(int) $folder->id(), \PDO::PARAM_INT);
			$request->bindValue(':owner',$this->owner());
			$request->bindValue(':permissions', $folder->permissions()->getSerial());
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->setMessage("Impossible d'éxécuter la requête SQL dans \lib\models\FolderManager_PDO (update())");
					$error->addRoute("update(), \lib\models\FolderManager_PDO.class.php");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			return true;
		}
		
		public function delete($id)
		{
			$error = new \lib\Error();
			
			if(!$this->exists($id))
				{
					$error->setMessage("Le dossier que vous avez demandé de supprimer n'existe pas");
					$error->addRoute("delete(), \lib\models\FolderManager_PDO.class.php");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					
					return $error;
				}

			$request = $this->dao->prepare("DELETE FROM folders WHERE id = :id AND owner = :owner");
			$request->bindValue(':id',(int) $id, \PDO::PARAM_INT);
			$request->bindValue(':owner',$this->owner());
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error->setMessage("Impossible d'éxécuter la requête SQL dans \lib\models\FolderManager_PDO (delete())");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
		}
		
		public function getFolders()
		{
			$folderlist = array();
			$request = $this->dao->prepare("SELECT * FROM folders WHERE owner = :owner ORDER BY name");
			$request->bindValue(':owner',$this->owner());
			$request->execute();
			
			$result = $request->fetchAll(\PDO::FETCH_ASSOC); 
			foreach($result as $values)
				{
					$folderlist[] = new Folder($values);
				}
			return $folderlist;
		}
		
		/* Supprime tous les dossiers d'un utilisateur */
		public function deleteAllFolders()
		{
			$request = $this->dao->prepare("DELETE FROM folders WHERE owner = :owner");
			$request->bindValue(':owner', $this->owner());
			try
			{
				$request->execute();
			}
			catch(Exception $e)
			{
				$error->setMessage("Impossible d'éxécuter la requête SQL dans \lib\models\FolderManager_PDO (deleteAllFolders())");
				$error->setWarnLevel(\lib\Error::wl_CRITICAL);
				return $error;
			}
		}

		public function getFolderPath(Folder $folder)
		{
			if(!$folder->has_parent())
				{
					return '/'.$this->purifyName($folder->name()).'/';
				}
			else
				{
					$parent_folder = $this->load($folder->parent());
					if(\lib\ToolBox::is_Error($parent_folder))
						{
							$parent_folder->addRoute("getFolderPath(), \lib\models\FolderManager_PDO");
							return $parent_folder;
						}
					return $parent_folder->path().$this->purifyName($folder->name()).'/';
				}
		}
		
		public function purifyName($string)
		{
			$name = \lib\ToolBox::stripAccents($string);
			$name = str_replace(',', '_', $name);
			$name = str_replace(':', '_', $name);
			$name = str_replace(';', '_', $name);
			$name = str_replace(' ', '_', $name);
			$name = str_replace('"', '_', $name);
			$name = str_replace('\'', '_', $name);
			
			return $name;
		}
		
		public function exists($id)
		{
			$request = $this->dao->prepare("SELECT COUNT(id) as res FROM folders WHERE id = :id and owner = :owner");
			$request->bindValue(':id',(int) $id, \PDO::PARAM_INT);
			$request->bindValue(':owner',$this->owner());
			$request->execute();
			
			return $request->fetchColumn(0);
		}
		
		public function getFoldersOf(Folder $folder)
		{
			$request = $this->dao->prepare("SELECT * FROM folders WHERE parent = :parent AND owner = :owner ORDER BY name");
			$request->bindValue(':parent', (int) $folder->id(), \PDO::PARAM_INT);
			$request->bindValue(':owner', $this->owner());
			$request->execute();
			
			$result = $request->fetchAll(\PDO::FETCH_ASSOC); 
			foreach($result as $values)
				{
					$folderlist[] = new Folder($values);
				}
			return $folderlist;
		}
		
		public function getAllChildrenOf(Folder $folder)
		{	
			$request = $this->dao->prepare("SELECT id FROM folders WHERE SUBSTRING(path, 1, :len_path) = :path AND owner = :owner ORDER BY name");
			$request->bindValue(':len_path', strlen($folder->path()), \PDO::PARAM_INT);
			$request->bindValue(':path', $folder->path());
			$request->bindValue(':owner', $this->owner());
			$request->execute();
			
			$children = $request->fetchAll(\PDO::FETCH_ASSOC);
			
			foreach($children as $child)
				{
					$children2[] = $child['id'];
				}
			
			$children = $children2;
			
			unset($children[array_search($folder->id(), $children)]);
			$children = array_values($children);
			
			sort($children);
			
			return $children;
		}
		
		public function folderHasChildren(Folder $folder)
		{
			$request = $this->dao->prepare("SELECT count(id) FROM folders WHERE parent = :parent AND owner = :owner");
			$request->bindValue(':parent', $folder->id(), \PDO::PARAM_INT);
			$request->bindValue(':owner', $this->owner());
			$request->execute();
			
			return $request->fetchColumn(0);
		}
		
		public function getFilesOf(Folder $folder)
		{
			$filemanager = new FileManager_PDO($this->dao);
			$filemanager->setOwner($this->owner);
			
			$list = $filemanager->getFilesOfFolder($folder->id());
			if(\lib\ToolBox::is_Error($list))
				{
					$list->addRoute("getFilesOf(), \lib\models\FolderManager_PDO");
				}
			
			return $list;
		}

		public function getMainFolder()
		{
			$request = $this->dao->prepare("SELECT MIN(ID) FROM folders WHERE owner = :owner AND name = 'Racine'");
			$request->bindValue(':owner',$this->owner());
			$request->execute();
			
			return $request->fetchColumn(0);
		}
		
		/* Fonction qui recherche un dossier d'après son nom */
		/* Retourne les dossiers tel que le donne la requete (PAS OBJET) */
		
		public function searchFolder($foldername) {
			$error = new \lib\Error();
			$request = $this->dao->prepare("SELECT * FROM folders WHERE name LIKE :foldername AND owner = :owner ORDER BY name");
			$request->bindValue(':foldername', '%'.$foldername.'%', \PDO::PARAM_STR);
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
			$result = $request->fetchAll(\PDO::FETCH_ASSOC);
			foreach($result as $folder)
				{
					$folders[] = $folder;
				}
			return $folders;
		}
	}
?>
