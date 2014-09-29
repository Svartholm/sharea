<?php
	class Folder extends Record
	{
		const NO_PARENT = -1;
					
		protected $name,
							$parent = self::NO_PARENT,
							$path,
							$date,
							$permissions,
							$owner;
							
		/* Setters */
		public function setOwner($owner)
		{
			$this->owner = (int) $owner;
		}
		
		public function setName($name)
		{
			$this->name = $name;
		}
		
		public function setParent($parent)
		{
			$this->parent = (int) $parent;
		}

		public function setPath($path)
		{
			$this->path = $path;
		}
		
		public function setDate($date)
		{
			$this->date = $date;
		}
		
		public function setPermissions($permissions)
		{
			if(is_object($permissions) && get_class($permissions) == "Permission")
				$this->permissions = $permissions;
			else
				$this->permissions = new Permission($permissions);
		}
		
		/* Getters */
		public function name()
		{
			return $this->name;
		}
		
		public function parent()
		{
			return $this->parent;
		}
		
		public function id()
		{
			return $this->id;
		}
		
		public function owner()
		{
			return $this->owner;
		}
		
		public function path()
		{
			return $this->path;
		}
		
		public function date()
		{
			return $this->date;
		}
		
		public function permissions()
		{
			return $this->permissions;
		}
		
		/* Methods */
		public function has_parent()
		{
			if($this->parent >= 0)
				return true;
			else
				return false;
		}
		
		public function isValid($passthrough = false)
		{
			$error = new Error();
			
			if(!Regex::isFolderName($this->name))
				{
					$error->setMessage("Nom de dossier invalide");
					$error->setWarnLevel(Error::wl_LOW);
				}
			
			if(!is_numeric($this->owner) || $this->owner < 0)
				{
					$error->setMessage("Propriétaire du dossier invalide");
					$error->setWarnLevel(Error::wl_HIGH);
				}
				
			if(!is_numeric($this->parent) or $this->parent < -1)
				{
					$error->setMessage("Parent du dossier invalide");
					$error->setWarnLevel(Error::wl_LOW);
				}
				
			if($passthrough == false)
				{
					if(!is_numeric($this->id) || $this->id < 0)
						{
							$error->setMessage("Identifiant de dossier invalide");
							$error->setWarnLevel(Error::wl_HIGH);
						}
					if(empty($this->path) or !is_string($this->path) or substr($this->path,-4) == "/../" or
																													substr($this->path,-3) == "/./" or
																													substr($this->path,-3) == "///")
						{
							$error->setMessage("Chemin du dossier invalide");

							$error->setWarnLevel(Error::wl_HIGH);
						}
				}
			
			if(!Regex::isPermissionSerial($this->permissions->getSerial()))
				{
					$error->setMessage("Permissions du dossier invalide (".$this->permissions->getSerial().")");
					$error->setWarnLevel(Error::wl_HIGH);
				}
			
			/* NOTE : Vérifier la date */

			$m = $error->message();
			if(empty($m))
				return true;
			else
				{
					$error->addRoute("isValid(), Folder.class.php");
					return $error;
				}
		}
		
		public function getFolders(FolderManager $manager)
		{
			return $manager->getFoldersOf($this);
		}
		
		public function getAllChildren(FolderManager $manager)
		{
			return $manager->getAllChildrenOf($this);
		}
		
		public function getFiles(FolderManager $manager)
		{
			return $manager->getFilesOf($this);
		}
		
		public function hasChildren(FolderManager $manager)
		{
			return $manager->folderHasChildren($this);
		}
	}
?>
