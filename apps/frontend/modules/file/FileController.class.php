<?php
	namespace apps\frontend\modules\file;
	class FileController extends \lib\BackController
	{
		public function executeYtdl(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			$um = $this->managers->getManagerOf('user');
			$um->bindVSS($this->app->vss_user);
			$filemanager = $this->managers->getManagerOf("file");
			$foldermanager = $this->managers->getManagerOf("folder");
			
			if(!$um->isAuth())
				{
					$error->setTemplate('!Auth');
					$this->error_collector->display($error);
				}
			
			$u = $um->getUser();
			$filemanager->setOwner($u->id());
			$foldermanager->setOwner($u->id());
			
			if(!$request->postExists('parent'))
				{
					$error->setMessage("Le dossier dans lequel télécharger la vidéo n'a pas été précisé");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
			
			$parent = (int)$request->postData('parent');
			
			if(!$foldermanager->exists($parent))
				{
					$error->setMessage("Le dossier dans lequel télécharger la vidéo n'existe pas ou ne vuos appartient pas");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
				
			if(!$request->postExists('video_url'))
				{
					$error->setMessage("Vous n'avez pas précisé de lien");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
				
			$tube = new \lib\Youtube;
			$links = $tube->get($request->postData('video_url'));
			if(\lib\ToolBox::is_Error($links))
				{
					$this->error_collector->display($links);
				}

			$prefix = $this->app->config()->get('temp_folder');
			$name = $prefix.'/'.\lib\ToolBox::generateRandomString();
			
			$stream_in = fopen($links[0]['url'], "rb");
			$stream_out = fopen($name, "ab");
			
			if($stream_in === false || $stream_out === false)
				{
					$error->setMessage("Une erreur est survenue pendant le téléchargement (stream)");
					$error->setWarnLevel(\lib\Error::wl_HIGH);
					$this->error_collector->display($error);
				}
				
			while(!feof($stream_in))
				{
					if(fwrite($stream_out, fread($stream_in, 1024)) === false)
						{
							$error->setMessage("Une erreur est survenue pendant le téléchargement");
							$error->setWarnLevel(\lib\Error::wl_LOW);
							$this->error_collector->display($error);
						}
					}
					
			fclose($stream_in);
			fclose($stream_out); 
			
			if($request->postExists('mp3'))
			{
				$cmd = 'ffmpeg -i "'.$name.'" -acodec copy "'.$name.'".mp3';
				exec($cmd);
				$name = $name.".mp3";
			}
			
			
			/* Saving the file */
			$file = new \lib\entities\File;
			$perm = new \lib\Permission;
			$perm->setMode(\lib\Permission::P_Private);

			if(!preg_match("#^[\w~. :&%,-;'()&!/\[\]]{1,128}$#u", $links[0]['name']))
				$links[0]['name'] = "Youtube";
			if($request->postExists('mp3'))
				$links[0]['name'] .= '.mp3';
			else
				$links[0]['name'] .= '.'.$links[0]['ext'];
			$file->setName($links[0]['name']);
			$file->setOwner($u->id());
			$file->setParent($parent);
			$file->setDate(date('d/m/Y H:i'));
			$file->setPermissions($perm->getSerial());
			$file->setTempname($name);
			$file->setMd5(hash_file('md5',$file->tempname()));
			$file->setSha256(hash_file('sha256', $file->tempname()));
			$file->setSize(filesize($file->tempname()));

			// Does the user have enough space ? 
			$freespace = $u->config()->get('free_space');
			$basespace = $u->config()->get('base_space');
			
			if($freespace - $file->size() < 0)
				{
					$error->setMessage("Désolé, vous n'avez plus assez de place dans votre espace personnel pour envoyer ce fichier");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					
					$this->error_collector->display($error);
				}
			
			// Finding the mimetype
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mt = finfo_file($finfo, $file->tempname());
			if(!empty($mt))
				$file->setMimetype(finfo_file($finfo, $file->tempname()));
			else if(!empty($file['type']))
				$file->setMimetype($file['type']);
			else
				$file->setMimetype("Inconnu");
			finfo_close($finfo);
			
			// Calculating the path
			$path = $filemanager->getFilepath($file);
			if(\lib\ToolBox::is_Error($path))
				$this->error_collector->display($path);
			$file->setPath($path);
			
			// Adding the file
			$r = $file->isValid(true);
			if(\lib\ToolBox::is_Error($r))
				$this->error_collector->display($r);
			
			$r = $filemanager->add($file, false);
			if(\lib\ToolBox::is_Error($r))
				$this->error_collector->display($r);
			
			// Recalculating freespace
			$user = $um->getUser();
			$user->config()->set('free_space', $freespace - $file->size());
			$um->update($user);
			$um->reloadSession($user);
		}
		
		public function executeUpload(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			
			$filemanager = $this->managers->getManagerOf("file");
			$foldermanager = $this->managers->getManagerOf("folder");
			$um = $this->managers->getManagerOf('user');
			$um->bindVSS($this->app->vss_user);
			
			if(!$um->isAuth())
				{
					$error->setTemplate('!Auth');
					$this->error_collector->display($error);
				}
			
			$user = $um->getUser();
			$filemanager->setOwner($user->id());
			$foldermanager->setOwner($user->id());
			
			if(!$request->hasFile())
				{
					$error->setMessage("Aucun fichier envoyé");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
			
			if(!$request->postExists('parent'))
				{
					$error->setMessage("Le parent du fichier n'a pas été précisé");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
			
			if(!$foldermanager->exists($request->postData('parent')))
				{
					$error->setMessage("Le dossier parent au fichier n'existe pas ou ne vous appartient pas");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
			
			$file = $request->getWholeFile();
			$file = $file['file'];
			
			if($file['error'] != 0)
				{
					switch ($file['error'])
						{
							case 1:
								$error->setMessage("Le fichier dépasse la taille maximum acceptée par la plateforme web.");
								break;
							case 2:
								$error->setMessage("Le fichier dépasse la taille maximum acceptée par la plateforme web.");
								break;
							case 3:
								$error->setMessage("Le fichier n'a pas été envoyé en entier");
								break;
							case 4:
								$error->setMessage("Le fichier n'a pas été envoyé ...");
								break;
							case 6:
								$error->setMessage("Erreur interne lors de l'envoi du fichier");
								break;
							case 7:
								$error->setMessage("Erreur interne lors de l'envoi du fichier");
								break;
							case 8:
								$error->setMessage("Erreur interne lors de l'envoi du fichier");
								break;
							default:
								$error->setMessage("Une erreur inconnue s'est produite lors de l'envoi du fichier");
								break;
						}
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}		
					
			$rfile = new \lib\entities\File;
			$perm = new \lib\Permission();
			$perm->setMode(\lib\Permission::P_Private);
			
			$newname = $file['name'];
			$rfile->setName($newname);
			$rfile->setOwner($user->id());
			$rfile->setParent($request->postData('parent'));
			$rfile->setDate(date('d/m/Y H:i'));
			$rfile->setPermissions($perm->getSerial());
			$rfile->setTempname($file['tmp_name']);
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
					
					$this->error_collector->display($error);
				}
			
			/* Finding the mimetype */
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mt = finfo_file($finfo, $file['tmp_name']);
			if(!empty($mt))
				$rfile->setMimetype(finfo_file($finfo, $file['tmp_name']));
			else if(!empty($file['type']))
				$rfile->setMimetype($file['type']);
			else
				$rfile->setMimetype("Inconnu");
			finfo_close($finfo);
			
			/* Calculating the path */
			$path = $filemanager->getFilepath($rfile);
			if(\lib\ToolBox::is_Error($path))
				$this->error_collector->display($path);
			$rfile->setPath($path);
			
			/* Adding the file */
			$r = $rfile->isValid(true);
			if(\lib\ToolBox::is_Error($r))
				$this->error_collector->display($r);
			
			$r = $filemanager->add($rfile);
			if(\lib\ToolBox::is_Error($r))
				$this->error_collector->display($r);
			$this->page->addVar('file', $rfile);
			
			/* Recalculating freespace */
			$user->config()->set('free_space', $freespace - $rfile->size());
			$um->update($user);
			$um->reloadSession($user);
		}

		public function executeShare(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			
			$um = $this->managers->getManagerOf("user");
			$um->bindVSS($this->app->vss_user);
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
				
			$filemanager->setOwner($user->id());
			
			if(!$request->postExists('file'))
				{
					$error->setMessage("Identifiant de fichier invalide");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}

			if(!$request->postExists('permission'))
				{
					$error->setMessage("Aucune permission sélectionnée.");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
			
			$file = $filemanager->load((int) $request->postData('file'));				
			if(\lib\ToolBox::is_Error($file))
				{
					$this->error_collector->display($file);
				}
			
			$perm = new \lib\Permission;
			$demand = $request->postData('permission');
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
					$error->setMessage("Permissions du fichier invalide");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					
					$this->error_collector->display($error);
				}
				
			/* Now updating */
			$file->setPermissions($perm->getSerial());
			$r = $filemanager->update($file, true);
			if(\lib\ToolBox::is_Error($r))
				{
					$this->error_collector->display($r);
				}
			$this->page->addVar('notification', "Les modifications ont bien été effectuées");
		}
	
		public function executeRename(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			
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
					$this->error_collector->display($file);
				}
				
			$file->setName($request->postData('newname'));
			$r = $file->isValid();
			
			if(\lib\ToolBox::is_Error($r))
				{
					$this->error_collector->display($r);
				}
			
			$r = $filemanager->update($file);
			if(\lib\ToolBox::is_Error($r))
				{
					$this->error_collector->display($r);
				}
		}
	
		public function executeMove(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
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
					$error->setMessage("Vous n'avez pas précisé quel fichier renommer.");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					
					$this->error_collector->display($error);
				}
			else if(!$request->postExists('newpath'))
				{
					$error->setMessage("Vous n'avez pas précisé dans quel dossier déplacer le fichier.");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					
					$this->error_collector->display($error);
				}
				
			$file = $filemanager->load((int) $request->postData('file'));
			if(\lib\ToolBox::is_Error($file))
				{
					$this->error_collector->display($file);
				}
				
			// reste à lui donner l'id du nouveau parent
			$file->setParent($request->postData('newpath'));

			$r = $file->isValid();
			
			if(\lib\ToolBox::is_Error($r))
				{
					$this->error_collector->display($r);
				}
			
			$r = $filemanager->update($file);
			if(\lib\ToolBox::is_Error($r))
				{
					$this->error_collector->display($r);
				}
			}
		
		public function executeRemove(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			
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
					$error->setMessage("Vous n'avez pas précisé quel est le fichier Ã  supprimer");
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
					$this->error_collector->display($file);
				}
				
			$r = $filemanager->delete($file->id());
			if(\lib\ToolBox::is_Error($r))
				{
					$this->error_collector->display($r);
				}
			
			/* Recalculating free space */
			$freespace = $u->config()->get('free_space');
			$u->config()->set('free_space', $freespace + $file->size());
			$usermanager->update($u);
			$usermanager->reloadSession($u);
			
			$this->page->addVar('notification', 'Fichier supprimé !');
		}
		
		public function executeDownload(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			
			$um = $this->managers->getManagerOf('user');
			$um->bindVSS($this->app->vss_user);
			$filemanager = $this->managers->getManagerOf('file');
			$friendmanager = $this->managers->getManagerOf('friend');
			
			/* Si l'utilisateur n'est pas enregistré ou n'a pas demandé la miniature d'une image, alors erreur */
			
			if($um->isAuth() || ($request->getExists('min') && $request->getData('min') == '/min'))
				{
					if($um->isAuth())
						{
							$u = $um->getUser();
							$friendmanager->setOwner($u->id());
						}
					else
						{
							$friendmanager->setOwner(0);
						}
						
					$filemanager->setOwner('*');
					/* Si le fichier n'existe pas, erreur */
					if(!$request->getExists('file'))
						{
							$error->setMessage("Vous n'avez pas précisé quel fichier télécharger");
							$error->setWarnlevel(\lib\Error::wl_LOW);
							
							$this->error_collector->display($error);
						}
						
					/* On charge le fichier */
					$file = $filemanager->load((int) $request->getData('file'));
					/* Si il y a eu une erreur, on l'affiche */
					if(\lib\ToolBox::is_Error($file))
						{
							$this->error_collector->display($file);
						}
		
					/* Si les permissions correspondent */
					if($file->permissions()->mode() == \lib\Permission::P_Public || (isset($u) && $u->id() == $file->owner()) || ($file->permissions()->mode() == \lib\Permission::P_Friends && $friendmanager->isFriend($file->owner())))
						{
							$response = $this->app->httpresponse();
							
							/* Si la miniature a été demandé et que le fichier est bien une image */
							if($request->getExists('min') && $request->getData('min') == '/min' && substr($file->mimetype(), 0, 6)  == 'image/') {
								$path_dl = dirname(__FILE__).'/../../../../files/'.$file->realname().'_min';
								$size = filesize($path_dl);
							}
							else {
								$path_dl = dirname(__FILE__).'/../../../../files/'.$file->realname();
								$size = $file->size();
							}
		
							$response->addHeader("Content-disposition: attachment; filename=\"".$file->name()."\"");
							$response->addHeader("Content-Type: ".$file->mimetype());
							$response->addHeader("Content-Transfer-Encoding: binary\n");
							$response->addHeader("Content-Length: ".$size);
							ob_clean();
							flush();
							ini_set("memory_limit","1024M");
							readfile($path_dl);
							exit();
						}
					else
						{
							$error->setMessage("Vous n'avez pas les droits pour télécharger ce fichier.");
							$error->setWarnLevel(\lib\Error::wl_LOW);
							$this->error_collector->display($error);
						}
				}
			else
				{
					$error->setTemplate('!Auth');
					$this->error_collector->display($error);		
				}
		}
}
?>
