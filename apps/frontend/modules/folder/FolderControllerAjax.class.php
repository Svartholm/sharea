<?php	
	namespace apps\frontend\modules\folder;
	class FolderControllerAjax extends \lib\BackController
	{
		public function executeRemove(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			$error->setReturnType(\lib\Error::type_JSON);
			
			$um = $this->managers->getManagerOf("user");
			$um->bindVSS($this->app->vss_user);
			
			$foldermanager = $this->managers->getManagerOf("folder");
			$filemanager = $this->managers->getManagerOf("file");
			
			if(!$um->isAuth())
				{
					$error->setTemplate('!Auth');
					$this->error_collector->display($error);
				}
			
			$user = $um->getUser();
			if(\lib\ToolBox::is_Error($user))
				{
					$this->error_collector->display($error);
				}
				
			$foldermanager->setOwner($user->id());
			$filemanager->setowner($user->id());
			
			if(!$request->postExists('folder'))
				{
					$error->setMessage("Identifiant de dossier invalide");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
			
			$folder = $foldermanager->load((int) $request->postData('folder'));
			if(\lib\ToolBox::is_Error($folder))
				{
					$folder->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($folder);
				}
			
			if(!$folder->has_parent())
				{
					$error->setMessage("Vous ne pouvez pas supprimer le dossier racine");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
				
			$children = $folder->getAllChildren($foldermanager);
			$root_files = $folder->getFiles($foldermanager);
			foreach($root_files as $file)
				{
					$r = $filemanager->delete($file);
					if(\lib\ToolBox::is_Error($r))
						{
							$r->setReturnType(\lib\Error::type_JSON);
							$r->addRoute("FolderController, delete()");
							$r->setWarnLevel(\lib\Error::wl_CRITICAL);
							
							$this->error_collector->display($r);
						}
				}
			foreach($children as $child)
				{
					$child2 = $foldermanager->load($child);
					if(\lib\ToolBox::is_Error($child2))
						{
							$child2->setReturnType(\lib\Error::type_JSON);
							$child2->addRoute("FolderController, delete()");
							$child2->setWarnLevel(\lib\Error::wl_CRITICAL);
							
							$this->error_collector->display($child2);
						}
					$files = $child2->getFiles($foldermanager);

					if(\lib\ToolBox::is_Error($files))
						{
							$files->setReturnType(\lib\Error::type_JSON);
							$files->addRoute("FolderController, delete()");
							$files->setWarnLevel(\lib\Error::wl_CRITICAL);
							
							$this->error_collector->display($files);
						}
					foreach($files as $file)
						{
							$r = $filemanager->delete($file);
							if(\lib\ToolBox::is_Error($r))
								{
									$r->setReturnType(\lib\Error::type_JSON);
									$r->addRoute("FolderController, delete()");
									$r->setWarnLevel(\lib\Error::wl_CRITICAL);
							
									$this->error_collector->display($r);
								}
						}
					$foldermanager->delete($child);
				}
			
			$foldermanager->delete($folder->id());
			$this->page->addVar('notification', 'Dossier supprimé !');
		}
		
		public function executeChgPerm(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			$error->setReturnType(\lib\Error::type_JSON);
			
			$um = $this->managers->getManagerOf("user");
			$um->bindVSS($this->app->vss_user);
			$foldermanager = $this->managers->getManagerOf("folder");

			if(!$um->isAuth())
			{
				$error->setTemplate('!Auth');
				$this->error_collector->display($error);
			}
			
			$user = $um->getUser();
				
			$foldermanager->setOwner($user->id());
			
			if(!$request->postExists('folder'))
				{
					$error->setMessage("Identifiant de dossier invalide");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}

			if(!$request->postExists('perm'))
				{
					$error->setMessage("Aucune permission sélectionnée.");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
			
			$folder = $foldermanager->load((int) $request->postData('folder'));				
			if(\lib\ToolBox::is_Error($folder))
				{
					$folder->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($folder);
				}
			
			$perm = new \lib\Permission;
			$demand = $request->postData('perm');
			if($demand == \lib\Permission::P_Friends)
				{
					$perm->setMode(\lib\Permission::P_Friends);
				}
			else if($demand == \lib\Permission::P_Public)
				{
					$perm->setMode(\lib\Permission::P_Public);
				}
			else if($demand == \lib\Permission::P_Private)
				{
					$perm->setMode(\lib\Permission::P_Private);
				}
			else
				{
					$error->setMessage("Permissions du dossier invalide");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
				
			/* Now updating */
			$folder->setPermissions($perm->getSerial());
			$r = $foldermanager->update($folder, true);
			if(\lib\ToolBox::is_Error($r))
				{
					$r->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($r);
				}
			
			/* Si allfile est coché et vaut true */	
			if($request->postExists('allfile') && $request->postData('allfile'))
			{
				$filemanager = $this->managers->getManagerOf("file");
				$filemanager->setOwner($user->id());
				$allfiles = $folder->getFiles($foldermanager);
				
				foreach($allfiles as $file)
				{
					$fcourant = $filemanager->load($file);
					$fcourant->setPermissions($perm->getSerial());
					$r = $filemanager->update($fcourant, true);
					if(\lib\ToolBox::is_Error($r))
						{
							$r->setReturnType(\lib\Error::type_JSON);
							$this->error_collector->display($r);
						}
				}	
			}
		}
		
		public function executeCreate(\lib\HTTPRequest $request) {
            $error = new \lib\Error;
            $error->setReturnType(\lib\Error::type_JSON);

            $um = $this->managers->getManagerOf("user");
            $um->bindVSS($this->app->vss_user);
            $foldermanager = $this->managers->getManagerOf("folder");

            if(!$um->isAuth())
            {
                $error->setTemplate('!Auth');
                $this->error_collector->display($error);
            }

            $user = $um->getUser();
            $foldermanager->setOwner($user->id());

            if(!$request->postExists('folder_name'))
            {
                $error->setMessage("Vous n'avez pas entré de nom de dossier");
                $error->setWarnLevel(\lib\Error::wl_LOW);
                $this->error_collector->display($error);
            }
            else if(!$request->postExists('parent'))
            {
                $error->setMessage("Le parent du dossier n'a pas été précisé");
                $error->setWarnLevel(\lib\Error::wl_LOW);
                $this->error_collector->display($error);
            }

            $folder = new \lib\entities\Folder;

            $perm = new \lib\Permission;
            $perm->setMode(\lib\Permission::P_Private);

            $folder->setName($request->postData('folder_name'));
            $folder->setParent($request->postData('parent'));
            $folder->setOwner($user->id());
            $folder->setDate(date('d/m/Y H:i'));
            $folder->setPermissions($perm->getSerial());

            $path = $foldermanager->getFolderPath($folder);
            if(\lib\ToolBox::is_Error($path))
                $this->error_collector->display($path);

            $r = $folder->isValid(true);
            if(\lib\ToolBox::is_Error($r))
            {
                $this->error_collector->display($r);
            }

            $folder->setPath($path);
            $r = $foldermanager->add($folder);
            if(\lib\ToolBox::is_Error($r))
            {
                $this->error_collector->display($r);
            }
            $this->jsonPage->addVar('return', 0);
        }

        public function executeRename(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			$error->setReturnType(\lib\Error::type_JSON);
			
			$um = $this->managers->getManagerOf("user");
			$um->bindVSS($this->app->vss_user);
			$foldermanager = $this->managers->getManagerOf("folder");
			$filemanager = $this->managers->getManagerOf("file");
			
			if(!$um->isAuth())
			{
				$error->setTemplate('!Auth');
				$this->error_collector->display($error);
			}
			
			$user = $um->getUser();
				
			$foldermanager->setOwner($user->id());
			$filemanager->setOwner($user->id());
			
			if(!$request->postExists('folder'))
				{
					$error->setMessage("Identifiant de dossier invalide");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}

			if(!$request->postExists('newname'))
				{
					$error->setMessage("Nouveau nom inexistant");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
			
			$folder = $foldermanager->load((int) $request->postData('folder'));
			if(\lib\ToolBox::is_Error($folder))
				{
					$folder->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($folder);
				}
				
			if(!$folder->has_parent())
				{
					$error->setMessage("Vous ne pouvez pas renommer le dossier racine");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
		
			$children = $folder->getAllChildren($foldermanager);
			$folder->setName($request->postData('newname'));
			
			/* Generating new folder path */
			$newpath = $foldermanager->getFolderPath($folder);
			if(\lib\ToolBox::is_Error($newpath))
				{
					$newpath->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($newpath);
				}
			$folder->setPath($newpath);

			/* Checking folder validity */
			$r = $folder->isValid();
			if(\lib\ToolBox::is_Error($r))
				{
					$r->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($r);
				}
			
			/* Now updating */
			$r = $foldermanager->update($folder);
			if(\lib\ToolBox::is_Error($r))
				{
					$r->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($r);
				}
			
			$files = $folder->getFiles($foldermanager);
			foreach($files as $file)
				{
					$file = $filemanager->load($file);
					if(\lib\ToolBox::is_Error($file))
						{
							$file->setReturnType(\lib\Error::type_JSON);
							$this->error_collector->display($file);
						}
					$r = $filemanager->update($file);
					if(\lib\ToolBox::is_Error($r))
						{
							$r->setReturnType(\lib\Error::type_JSON);
							$this->error_collector->display($r);
						}
				}
				
			/* Now updating his children */
			foreach($children as $child)
				{
					$child = $foldermanager->load($child);
					
					if(\lib\ToolBox::is_Error($child))
						{
							$child->setReturnType(\lib\Error::type_JSON);
							$child->setWarnLevel(\lib\Error::wl_CRITICAL);
							$this->error_collector->display($child);
						}
					$child->setPath($foldermanager->getFolderPath($child));
					
					$r = $child->isValid();
					if(\lib\ToolBox::is_Error($r))
						{
							$r->setReturnType(\lib\Error::type_JSON);
							$r->setWarnLevel(\lib\Error::wl_CRITICAL);
							$this->error_collector->display($r);
						}
					
					$r = $foldermanager->update($child, true);
					if(\lib\ToolBox::is_Error($r))
						{
							$r->setReturnType(\lib\Error::type_JSON);
							$r->setWarnLevel(\lib\Error::wl_CRITICAL);
							$this->error_collector->display($r);
						}
				$files = $child->getFiles($foldermanager);
				foreach($files as $file)
					{
						$file = $filemanager->load($file);
						if(\lib\ToolBox::is_Error($file))
							{
								$file->setReturnType(\lib\Error::type_JSON);
								$this->error_collector->display($file);
							}
						$r = $filemanager->update($file);
						if(\lib\ToolBox::is_Error($r))
							{
								$r->setReturnType(\lib\Error::type_JSON);
								$this->error_collector->display($r);
							}
					}
				}
		}

		public function executeGetAll(\lib\HTTPRequest $request)
		{	
			$error = new \lib\Error;
			$error->setReturnType(\lib\Error::type_JSON);
			
			$foldermanager = $this->managers->getManagerOf('folder');
			$um = $this->managers->getManagerOf('user');
			$um->bindVSS($this->app->vss_user);
			if(!$um->isAuth())
				{
					$error->setTemplate("!Auth");
					$this->error_collector->display($error);
				}
				
			$u = $um->getUser();
			$foldermanager->setOwner($u->id());
			
			/* Si aucun dossier n'est précisé, on charge la Racine */
			if(!$request->postExists('folder') || $request->postData('folder') == NULL)
			{
				$folderid = (int) $foldermanager->getMainFolder();
			}
			/* Sinon on charge le dossier passé en POST */	
			else
			{	
				$folderid = (int) $request->postData('folder');
			}
			$folder = $foldermanager->load($folderid);
			if(\lib\ToolBox::is_Error($folder))
				{
					$folder->setWarnLevel(\lib\Error::wl_HIGH);
					$folder->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($folder);
				}
			$folders = $foldermanager->getFoldersOf($folder);
			//print_r($folders);			
			foreach($folders as $f) {
                $attr = $f->get_class_vars();
                $attr['permissions'] = $attr['permissions']->mode();
                $attributes[] = $attr;
			}
			//print_r($attributes);
			$this->jsonPage->addVar('folders', $attributes);
		}

		public function executeSearch(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			$error->setReturnType(\lib\Error::type_JSON);
			
			$usermanager = $this->managers->getManagerOf('user');
			$usermanager->bindVSS($this->app->vss_user);
			$foldermanager = $this->managers->getManagerOf('folder');
			
			if(!$usermanager->isAuth())
				{
					$error->setTemplate("!Auth");
					$this->error_collector->display($error);
				}

			$u = $usermanager->getUser();
			$foldermanager->setOwner($u->id());

			if(!$request->postExists('folder'))
				{
					$error->setMessage("Manque de données POST ");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
			else
				{
					$attributes = $foldermanager->searchFolder($request->postData('folder'));
					$this->jsonPage->addVar('folders', $attributes);
				}
		}
	}
?>
