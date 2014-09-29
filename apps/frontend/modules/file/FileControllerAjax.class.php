<?php
	namespace apps\frontend\modules\file;
	class FileControllerAjax extends \lib\BackController
	{
		public function executeGetFileInfos(\lib\HTTPRequest $request)
		{
			/* Return file infos */
			$error = new \lib\Error;
			$um = $this->managers->getManagerOf('user');
			$um->bindVSS($this->app->vss_user);
			$filemanager = $this->managers->getManagerOf("file");
			
			if(!$um->isAuth())
				{
					$error->setTemplate("!Auth");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$error->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($error);
				}
			
			$u = $um->getUser();
			$filemanager->setOwner($u->id());
			
			if(!$request->getExists('file'))
				{
					$error->setMessage("L'identifiant du fichier est invalide");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$error->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($error);
				}
			
			$file = $filemanager->load((int) $request->getData('file'));
			if(\lib\ToolBox::is_Error($file))
				{
					$file->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($file);
				}
			
			$vars = $file->get_class_vars();
			foreach($vars as $var => $value)
				{
					$this->jsonPage->addvar($var, $value);
				}
		}	
 		
 		public function executeUpload(\lib\HTTPRequest $request)
 		{
 			/* Upload a file using XHR */
 			$error = new \lib\Error;
			
			$filemanager = $this->managers->getManagerOf("file");
			$foldermanager = $this->managers->getManagerOf("folder");
			$um = $this->managers->getManagerOf('user');
			$um->bindVSS($this->app->vss_user);
			
			if(!$um->isAuth())
				{
					$error->setTemplate("!Auth");
					$error->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($error);
				}
			
			$user = $um->getUser();
			$filemanager->setOwner($user->id());
			$foldermanager->setOwner($user->id());
			
			$headers = $request->getHeaders();

			if(!isset($headers['X-Folder']))
				{
					$error->setMessage("Le parent du fichier n'a pas été précisé");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$error->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($error);
				}
			
			if(!$foldermanager->exists($headers['X-Folder']))
				{
					$error->setMessage("Le dossier parent au fichier n'existe pas ou ne vous appartient pas");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$error->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($error);
				}
			
			$prefix = $this->app->config()->get('temp_folder');
			$tmpname = $prefix.'/'.\lib\ToolBox::generateRandomString();
			
			$stream_in = fopen('php://input', "rb");
			$stream_out = fopen($tmpname, "ab");
			
			$buffer = (int) $this->app->config()->get('upload_buffer');
			
			if($stream_in == false || $stream_out == false)
				{
					$error->setMessage("Une erreur est survenue pendant l'envoi");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$error->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($error);
				}
			
			$total = 0;
			$free = $user->config()->get('free_space');;
			
			while(!feof($stream_in))
				{
					if($total <= ($free - $buffer))
						{
							if(fwrite($stream_out, fread($stream_in, $buffer)) === false)
								{
									$error->setMessage("Une erreur est survenue pendant l'envoi");
									$error->setWarnLevel(\lib\Error::wl_LOW);
									$error->setReturnType(\lib\Error::type_JSON);
									
									fclose($stream_in);
									fclose($stream_out); 
									
									if(file_exists($stream_out))
										unlink($stream_out);
										
									$this->error_collector->display($error);
								}
							$total += $buffer;
						}
					else
						{
							$error->setMessage("Vous n'avez pas assez de place pour envoyer ce document");
							$error->setWarnLevel(\lib\Error::wl_LOW);
							$error->setReturnType(\lib\Error::type_JSON);
							
							fclose($stream_in);
							fclose($stream_out);
							
							unlink($stream_out);
							$this->error_collector->display($error);
						}
				}
					
			fclose($stream_in);
			fclose($stream_out); 
		 		 				
 			$rfile = new \lib\entities\File;
			$perm = new \lib\Permission;
			$perm->setMode(\lib\Permission::P_Private);
			
			$newname = $headers['X-File-Name'];
			$rfile->setName($newname);
			$rfile->setOwner($user->id());
			$rfile->setParent($headers['X-Folder']);
			$rfile->setDate(date('d/m/Y H:i'));
			$rfile->setPermissions($perm->getSerial());
			$rfile->setTempname($tmpname);
			$rfile->setMd5(hash_file('md5',$rfile->tempname()));
			$rfile->setSha256(hash_file('sha256', $rfile->tempname()));
			$rfile->setSize(filesize($rfile->tempname()));
			
			/* Does the user have enough space ? */
			$freespace = $user->config()->get('free_space');
			$basespace = $user->config()->get('base_space');
			
			if($freespace - $rfile->size() < 0)
				{
					$error->setMessage("Désolé, vous n'avez plus assez de place dans votre espace personnel pour envoyer ce fichier");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$error->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($error);
				}
			
			/* Finding the mimetype */
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mt = finfo_file($finfo, $rfile->tempname());
			if(!empty($mt))
				$rfile->setMimetype(finfo_file($finfo, $rfile->tempname()));
			else if(!empty($file['type']))
				$rfile->setMimetype($file['type']);
			else
				$rfile->setMimetype("Inconnu");
			finfo_close($finfo);
			
			/* Calculating the path */
			$path = $filemanager->getFilepath($rfile);
			if(\lib\ToolBox::is_Error($path))
				{
					$path->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($path);
				}
			$rfile->setPath($path);
			
			/* Adding the file */
			$r = $rfile->isValid(true);
			if(\lib\ToolBox::is_Error($r))
				{
					$r->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($r);
				}
			
			$r = $filemanager->add($rfile, false);
			if(\lib\ToolBox::is_Error($r))
				{
					$r->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($r);
				}
			$this->page->addVar('file', $rfile);
			
			/* Recalculating freespace */
			$user->config()->set('free_space', $freespace - $rfile->size());
			$um->update($user);
			$um->reloadSession($user);
 		}
 		
 		public function executeRemove(\lib\HTTPRequest $request)
 		{
 			$error = new \lib\Error;
			$error->setReturnType(\lib\Error::type_JSON);
			
			$usermanager = $this->managers->getManagerOf('user');
			$usermanager->bindVSS($this->app->vss_user);
			
			if(!$usermanager->isAuth())
				{
					$error->setTemplate("!Auth");
					$this->error_collector->display($error);
				}
				
				
			$u = $usermanager->getUser();
			if(!$request->postExists('file'))
				{
					$error->setMessage("Vous n'avez pas précisé quel est le fichier à supprimer");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					
					$this->error_collector->display($error);
				}
			
			$filemanager = $this->managers->getManagerOf("file");
			$filemanager->setOwner($u->id());
			
			$id = (int) $request->postData('file');
			if(!$filemanager->exists($id))
				{
					$error->setMessage("Le fichier que vous avez demandé de supprimer n'existe pas ou ne vous appartient pas");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
				
			$file = $filemanager->load($id);
			if(\lib\ToolBox::is_Error($file))
				{
					$file->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($file);
				}
				
			$r = $filemanager->delete($file->id());
			if(\lib\ToolBox::is_Error($r))
				{
					$r->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($r);
				}
			
			/* Recalculating free space */
			$freespace = $u->config()->get('free_space');
			$u->config()->set('free_space', $freespace + $file->size());
			$usermanager->update($u);
			$usermanager->reloadSession($u);
			
			$this->jsonPage->addVar('notification', 'Fichier supprimé !');
 		}
 		
 		public function executeImport(\lib\HTTPRequest $request)
 		{
 			$error = new \lib\Error;
 			$error->setReturnType(\lib\Error::type_JSON);
 			
 			$usermanager = $this->managers->getManagerOf('user');
			$usermanager->bindVSS($this->app->vss_user);
			$filemanager = $this->managers->getManagerOf('file');
			$foldermanager = $this->managers->getManagerOf('folder');
			$friendmanager = $this->managers->getManagerOf('friend');
			
			if(!$usermanager->isAuth())
				{
					$error->setTemplate("!Auth");
					$this->error_collector->display($error);
				}
			
			$user = $usermanager->getUser();
			$filemanager->setOwner('*');
			$foldermanager->setOwner($user->id());
			$friendmanager->setowner($user->id());
			
			if(!$request->postExists('file'))
				{
					$error->setMessage("Vous n'avez pas précisé le fichier à importer");
					$error->setWarnlevel(\lib\Error::wl_LOW);
					$this->error_colector->display($error);
				}

			$file_id = (int) $request->postData('file');
			$file = $filemanager->load($file_id);
			
			$filemanager->setOwner($user->id());
			
			if(\lib\ToolBox::is_Error($file))
				{
					$file->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($file);
				}
			
			$perm_file = $file->permissions();
			
			$ok_public = ($perm_file->mode() == \lib\Permission::P_Public);
			$ok_friends = ($perm_file->mode() == \lib\Permission::P_Friends && $friendmanager->isFriend($file->id()));
			
			if(!(!$ok_public || !$ok_friends) || $user->id() == $file->owner() || $perm_file->mode() == \lib\Permission::P_Private)
				{
					/* The file already belongs to the user, or the file belongs to a user who is not in user's friendlist */
					$error->setMessage("Impossible d'importer ce fichier");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
			
			$id_parent = $foldermanager->path_to_id('/');
			$perm = new \lib\Permission;
			$perm->setMode(\lib\Permission::P_Private);
			$new_file = $file;
			$new_file->setOwner($user->id());
			$new_file->setParent($id_parent);
			$new_file->setPermissions($perm);
			$new_file->setDate(date('d/m/Y H:i'));
			
			$r = $new_file->isValid();
			if(\lib\ToolBox::is_Error($r))
				{
					$r->setReturnType(\lib\Error::type_JSON);
					$r->setWarnLevel(\lib\Error::wl_CRITICAL);
					$this->error_collector->display($r);
				}
			
			$r = $filemanager->add($new_file);
			if(\lib\ToolBox::is_Error($r))
				{
					$r->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($r);
				}
			
 		}
 		
 		public function executeChgPerm(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			$error->setReturnType(\lib\Error::type_JSON);
			
			$um = $this->managers->getManagerOf("user");
			$um->bindVSS($this->app->vss_user);
			$filemanager = $this->managers->getManagerOf("file");
			$friendmanager = $this->managers->getManagerOf('friend');
			$nm = $this->managers->getManagerOf("notification");

			if(!$um->isAuth())
			{
				$error = new \lib\Error;
				$error->setTemplate('!Auth');
				$this->error_collector->display($error);
			}
			
			$user = $um->getUser();
			$profile = $user->profile();
			$filemanager->setOwner($user->id());
			$friendmanager->setOwner($user->id());
			$nm->setOwner($user->id());
			
			if(!$request->postExists('file'))
				{
					$error->setMessage("Identifiant de fichier invalide");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}

			if(!$request->postExists('perm'))
				{
					$error->setMessage("Aucune permission sélectionnée.");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
			
			$file = $filemanager->load((int) $request->postData('file'));				
			if(\lib\ToolBox::is_Error($file))
				{
					$file->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($file);
				}
			
			/* Now adding a notification to the users affected by the sharing */
			$notifications = array();
			$notif = new \lib\entities\Notification;
			$notif->setMessage($profile->firstname().' '.$profile->lastname()." a partagé un fichier avec vous");
			$notif->setLink('/users/'.$profile->pseudo().'/files/');
			$notif->setThumbnail((int) $profile->avatar());
				
			$perm = new  \lib\Permission();
			$demand = $request->postData('perm');
			if($demand == \lib\Permission::P_Friends)
				{
					$perm->setMode(\lib\Permission::P_Friends);
					$friends = $friendmanager->getFriends();
					foreach($friends as $friend)
						{
							$notif->setReceiver($friend);
							$notifications[] = $notif;
						}
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
					$error->setMessage("Permissions du fichier invalide");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					
					$this->error_collector->display($error);
				}
			
			/* Now updating */
			$file->setPermissions($perm->getSerial());
			$r = $filemanager->update($file, true);
			if(\lib\ToolBox::is_Error($r))
				{
					$r->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($r);
				}
			
			/* Sending the notification */
			foreach($notifications as $notif)
				{
					$r = $notif->isValid();
					if(\lib\ToolBox::is_Error($r))
						{
							$this->error_collector->display($r);
						}
			  
					$r = $nm->add($notif);
					if(\lib\ToolBox::is_Error($r))
						{
							$this->error_collector->display($r);
						}
				}
		}
		
		public function executeRename(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			$error->setReturnType(\lib\Error::type_JSON);
			
			$usermanager = $this->managers->getManagerOf('user');
			$usermanager->bindVSS($this->app->vss_user);
			$filemanager = $this->managers->getManagerOf('file');
			
			if(!$usermanager->isAuth())
				{
					$error->setTemplate("!Auth");
					$this->error_collector->display($error);
				}

			$u = $usermanager->getUser();
			$filemanager->setOwner($u->id());
			
			if(!$request->postExists('file'))
				{
					$error->setMessage("Vous n'avez pas précisé quel fichier renommer");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					
					$this->error_collector->display($error);
				}
			else if(!$request->postExists('newname'))
				{
					$error->setMessage("Vous n'avez pas précisé le nouveau nom du fichier");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					
					$this->error_collector->display($error);
				}
				
			$file = $filemanager->load((int) $request->postData('file'));
			if(\lib\ToolBox::is_Error($file))
				{
					$file->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($file);
				}
				
			$file->setName($request->postData('newname'));
			$r = $file->isValid();
			
			if(\lib\ToolBox::is_Error($r))
				{
					$r->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($r);
				}
			
			$r = $filemanager->update($file);
			if(\lib\ToolBox::is_Error($r))
				{
					$r->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($r);
				}
		}
		
		public function executeGetAll(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			$error->setReturnType(\lib\Error::type_JSON);
			
			$foldermanager = $this->managers->getManagerOf('folder');
			$filemanager = $this->managers->getManagerOf('file');
			$um = $this->managers->getManagerOf('user');
			$um->bindVSS($this->app->vss_user);
			if(!$um->isAuth())
				{
					$error->setTemplate("!Auth");
					$this->error_collector->display($error);
				}
				
			$u = $um->getUser();
			$foldermanager->setOwner($u->id());
			$filemanager->setOwner($u->id());
			
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
			$files = $folder->getFiles($foldermanager);
			foreach($files as $file)
				{
					$file = $filemanager->load($file);
					if(\lib\ToolBox::is_Error($file))
						{
							$file->setReturnType(\lib\Error::type_JSON);
							$this->error_collector->display($file);
						}
					else
						{
							$attr = $file->get_class_vars();
                            $attr['permissions'] = $attr['permissions']->mode();
                            /*for($i=0; $i<count($attr); $i++) {
                                echo $attr[$i];
                                if(is_object($attr[$i])) {
                                    echo "coucou";
                                    $attr[$i] = serialize($a);
                                }
                            }*/
                            $attributes[] = $attr;
						}
				}
            $this->jsonPage->addVar('files', $attributes);
		}

		public function executeSearch(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			$error->setReturnType(\lib\Error::type_JSON);
			
			$usermanager = $this->managers->getManagerOf('user');
			$usermanager->bindVSS($this->app->vss_user);
			$filemanager = $this->managers->getManagerOf('file');
			
			if(!$usermanager->isAuth())
				{
					$error->setTemplate("!Auth");
					$this->error_collector->display($error);
				}

			$u = $usermanager->getUser();
			$filemanager->setOwner($u->id());

			if(!$request->postExists('file'))
				{
					$error->setMessage("Manque de données POST ");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
			else
				{
					$attributes = $filemanager->searchFile($request->postData('file'));
					$this->jsonPage->addVar('files', $attributes);
				}
		}
	}
?>
