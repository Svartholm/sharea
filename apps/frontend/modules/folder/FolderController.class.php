<?php	
	namespace apps\frontend\modules\folder;
	class FolderController extends \lib\BackController
	{
		public function executeShowFolder(\lib\HTTPRequest $request)
		{
			/* Affiche le contenu d'un dossier */
			$error = new \lib\Error;
			/* Ask for an action, redirecting */
			if($request->postExists('action'))
				{
					$todo = $this->app->config()->get('folder_redirection_'.$request->postData('action'));
					if(!$todo)
						{
							$error->setMessage("Action inconnnue");
							$error->setWarnLevel(\lib\Error::wl_LOW);
							$error->addRoute("executeShowFolder(), FolderController.class.php");
							$this->error_collector->display($error);
						}
					if(is_callable(array($this, $todo)))
						$this->$todo($request);
					else
						{
							$error->setMessage("Impossible d'apeller cette action");
							$error->setWarnLevel(\lib\Error::wl_CRITICAL);
							$error->addRoute("executeShowFolder(), FolderController.class.php");
							$this->error_collector->display($error);
						}
				}
			
			/* Initialisation du gestionnaire d'user */
			$um = $this->managers->getManagerOf("user");
			$um->bindVSS($this->app->vss_user);
			
			if(!$um->isAuth())
				{
					$error->setTemplate('!Auth');
					$this->error_collector->display($error);
				}
			
			$user = $um->getUser();
			/* Initialisation du manager de dossiers */
			$manager = $this->managers->getManagerOf("folder");
			$manager->setOwner($user->id());
			$filemanager = $this->managers->getManagerOf("file");
			$filemanager->setOwner($user->id());
			
			$r = $request->getData("id");
			if($r == "/")
				{
						$id = $manager->path_to_id($r);
				}
			else
				{
						$id = (int) substr($r,1);
				}
			
			/* Chargement du dossier courant */
			$folder = $manager->load($id); 
			if(\lib\ToolBox::is_Error($folder))
				{
					$this->error_collector->display($folder);
				}
					
			/* Chargement des dossiers enfants */
			$folders = array();
			$children = $folder->getFolders($manager);
			foreach($children as $child_folder)
				{
					$folders[] = $manager->load($child_folder['id']);
				}
			
			/* Chargement des fichiers du dossier parent */
			$c_files = $folder->getFiles($manager);
			asort($c_files);
			$files = array();
			
			foreach($c_files as $file)
				{
					$files[] = $filemanager->load($file);
				}
			
			/* Permet l'affichage d'un bouton pour revenir au dossier parent */
			if($folder->has_parent())
				{
					$parent = $manager->load($folder->parent());
					$this->page->addVar('parent_id', $parent->id());
				}
			
			/* Remplissage du chemin du dossier pour le breadcrumb */
			$bread_folder = $folder;
			$bread_path = array();
			$c = true;
			while($c)
				{
					if($bread_folder->has_parent())
						{
							$parent = $manager->load($bread_folder->parent());
					
							$parent_infos['name'] = $parent->name();
							$parent_infos['id'] = $parent->id();
					
							$bread_path[] = $parent_infos;
							$bread_folder = $parent;
						}
					else
						{
							$c = false;
						}
				}
			$this->page->addVar('breadcrumb', array_reverse($bread_path));
			
			/* Récupération des modals */
			$mm = new \lib\ModalFactory();
			
			/* Transmisson des variables à la vue */
			$this->page->addVar('title', "Documents");
			$this->page->addVar('folders', $folders);
			$this->page->addVar('allfolders', $manager->getFolders());
			$this->page->addVar('files', $files);
			$this->page->addVar('current_folder_id', $folder->id());
			$this->page->addVar('current_folder_name', $folder->name());
			$this->page->addVar('modal_renameFolder', $mm->getModal("renamefolder"));
			$this->page->addVar('modal_removeFolder', $mm->getModal("removefolder"));
			$this->page->addVar('modal_shareFolder', $mm->getModal("sharefolder"));
			$this->page->addVar('modal_renameFile', $mm->getModal("renamefile"));
			$this->page->addVar('modal_removeFile', $mm->getModal("removefile"));
			$this->page->addVar('modal_shareFile', $mm->getModal("sharefile"));
			$this->page->addVar('modal_qrFile', $mm->getModal("qrfile"));
			$this->page->addVar('modal_videoFile', $mm->getModal("videofile"));
			$this->page->addVar('modal_pictureFile', $mm->getModal("picturefile"));
			$this->page->addVar('modal_moveFile', $mm->getModal("movefile"));
		}

		public function executeShareFolder(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			
			$um = $this->managers->getManagerOf("user");
			$um->bindVSS($this->app->vss_user);
			$foldermanager = $this->managers->getManagerOf("folder");

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
					$this->error_collector->display($folder);
				}
			
			$perm = new \lib\Permission();
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
							$this->error_collector->display($r);
						}
				}	
			}
			$this->page->addVar('notification', "Les modifications ont bien été effectuées");
		}

		public function executeRenameFolder(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			
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
					$this->error_collector->display($newpath);
				}
			$folder->setPath($newpath);

			/* Checking folder validity */
			$r = $folder->isValid();
			if(\lib\ToolBox::is_Error($r))
				{
					$this->error_collector->display($r);
				}
			
			/* Now updating */
			$r = $foldermanager->update($folder);
			if(\lib\ToolBox::is_Error($r))
				{
					$this->error_collector->display($r);
				}
			
			$files = $folder->getFiles($foldermanager);
			foreach($files as $file)
				{
					$file = $filemanager->load($file);
					if(\lib\ToolBox::is_Error($file))
						{
							$this->error_collector->display($file);
						}
					$r = $filemanager->update($file);
					if(\lib\ToolBox::is_Error($r))
						{
							$this->error_collector->display($r);
						}
				}
				
			/* Now updating his children */
			foreach($children as $child)
				{
					$child = $foldermanager->load($child);
					
					if(\lib\ToolBox::is_Error($child))
						{
							$child->setWarnLevel(\lib\Error::wl_CRITICAL);
							$this->error_collector->display($child);
						}
					$child->setPath($foldermanager->getFolderPath($child));
					
					$r = $child->isValid();
					if(\lib\ToolBox::is_Error($r))
						{
							$r->setWarnLevel(\lib\Error::wl_CRITICAL);
							$this->error_collector->display($r);
						}
					
					$r = $foldermanager->update($child, true);
					if(\lib\ToolBox::is_Error($r))
						{
							$r->setWarnLevel(\lib\Error::wl_CRITICAL);
							$this->error_collector->display($r);
						}
				$files = $child->getFiles($foldermanager);
				foreach($files as $file)
					{
						$file = $filemanager->load($file);
						if(\lib\ToolBox::is_Error($file))
							{
								$this->error_collector->display($file);
							}
						$r = $filemanager->update($file);
						if(\lib\ToolBox::is_Error($r))
							{
								$this->error_collector->display($r);
							}
					}
				}
			
			$this->page->addVar('notification', 'Dossier renommé !');
		}
				
			
		public function executeRedirect_root(\lib\HTTPRequest $request)
		{
			/* Redirige l'user vers /files/ si il va sur /files */
			$this->app->httpresponse()->redirect("/files/");
		}
		
		public function executeCreate(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			
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
			else if(!$request->postExists('parent2'))
				{
					$error->setMessage("Le parent du dossier n'a pas été précisé");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
			echo $request->postData('parent2');
			$folder = new \lib\entities\Folder;
			
			$perm = new \lib\Permission;
			$perm->setMode(\lib\Permission::P_Private);
			
			$folder->setName($request->postData('folder_name'));
			$folder->setParent($request->postData('parent2'));
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
		}

		public function executeDeleteFolder(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			
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
							$child2->addRoute("FolderController, delete()");
							$child2->setWarnLevel(\lib\Error::wl_CRITICAL);
							
							$this->error_collector->display($child2);
						}
					$files = $child2->getFiles($foldermanager);

					if(\lib\ToolBox::is_Error($files))
						{
							$files->addRoute("FolderController, delete()");
							$files->setWarnLevel(\lib\Error::wl_CRITICAL);
							
							$this->error_collector->display($files);
						}
					foreach($files as $file)
						{
							$r = $filemanager->delete($file);
							if(\lib\ToolBox::is_Error($r))
								{
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
	}
?>
