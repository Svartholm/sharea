<?php
	class File extends Record
	{				
		protected $name,
							$realname,
							$parent,
							$mimetype,
							$path,
							$owner,
							$permissions,
							$date,
							$tempname,
							$md5,
							$sha256,
							$size;
							
		/* Setters */
		public function setOwner($owner)
		{
			$this->owner = $owner;
		}
		
		public function setName($name)
		{
			$this->name = $name;
		}
		
		public function setParent($parent)
		{
			$this->parent = (int) $parent;
		}
		
		
		public function setRealname($realname)
		{
			$this->realname = $realname;
		}
		
		public function setMimetype($mimetype)
		{
			$this->mimetype = $mimetype;
		}
		
		public function setPermissions($permissions)
		{
			if(is_object($permissions) && get_class($permissions) == "Permission")
				$this->permissions = $permissions;
			else
				$this->permissions = new Permission($permissions);
		}
		
		public function setDate($date)
		{
			$this->date = $date;
		}

		public function setPath($path)
		{
			$this->path = $path;
		}
		
		public function setTempname($tmpname)
		{
			$this->tempname = $tmpname;
		}
		
		public function setMd5($md5)
		{
			$this->md5 = $md5;
		}
		
		public function setSha256($sha)
		{
			$this->sha256 = $sha;
		}
		
		public function setSize($size)
		{
			$this->size = $size;
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

		public function owner()
		{
			return $this->owner;
		}
		
		public function path()
		{
			return $this->path;
		}
		
		public function mimetype()
		{
			return $this->mimetype;
		}
		
		public function date()
		{
			return $this->date;
		}
		
		public function permissions()
		{
			return $this->permissions;
		}
		
		public function realname()
		{
			return $this->realname;
		}
		
		public function tempname()
		{
			return $this->tempname;
		}
		
		public function md5()
		{
			return $this->md5;
		}
		
		public function sha256()
		{
			return $this->sha256;
		}
		
		public function size()
		{
			return $this->size;
		}
		
		/* Methods */
		public function isValid($passthrough = false)
		{
			$error = new Error();
			if(!Regex::isFileName($this->name))
				{
					$error->setMessage("Nom de fichier invalide");
					$error->setWarnLevel(Error::wl_LOW);
				}
			else if(!is_numeric($this->owner) || $this->owner < 0)
				{
					$error->setMessage("Propriétaire du fichier invalide");
					$error->setWarnLevel(Error::wl_HIGH);
				}
				
			else if(!is_numeric($this->parent) || $this->parent < -1)
				{
					$error->setMessage("Parent du fichier invalide");
					$error->setWarnLevel(Error::wl_LOW);
				}
			else if(!is_string($this->mimetype))
				{
					$error->setMessage("Type de fichier invalide (".$this->mimetype.")");
					$error->setWarnLevel(Error::wl_LOW);
				}
			else if(!Regex::isPermissionSerial($this->permissions->getSerial()))
				{
					$error->setMessage("Permissions du fichier invalides");
					$errr->setWarnLevel(Error::wl_LOW);
				}
			else if(empty($this->path) or !is_string($this->path) or substr($this->path,-4) == "/../" or
																													substr($this->path,-3) == "/./" or
																													substr($this->path,-3) == "///")
				{
					$error->setMessage("Path du fichier invalide");
					$error->setWarnLevel(Error::wl_LOW);
				}
			else if(!is_numeric($this->size))
				{
					$error->setMessage("Taille du fichier invalide");
					$error->setWarnLevel(Error::wl_HIGH);
				}
			else if($passthrough == false)
				{
					if(!is_numeric($this->id) || $this->id < 0)
						{
							$error->setMessage("Identifiant de fichier invalide");
							$error->setWarnLevel(Error::wl_LOW);
						}
					else if(strlen($this->realname) != 64)
						{
							$error->setMessage("Chemin d'accès au fichier invalide");
							$error->setWarnLevel(Error::wl_LOW);
						}
				}
				
			$m = $error->message();
			if(!empty($m))
				{
					$error->addRoute("isValid(), File.class.php");
					return $error;
				}
			else
				return true;
		}
		
		public function has_links(FileManager $manager)
		{
			return $manager->file_has_links($this);
		}
	}
?>
