<?php
	namespace apps\frontend\modules\share;
	class ShareController extends \lib\BackController
	{
		public function executeDisplay(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			/* Utilisateur à qui appartient la sharebox */
			$profile_manager = $this->managers->getManagerOf("profile");
			$profile_id = $profile_manager->pseudoToId($request->getData("pseudo"));
			$userid = $profile_manager->profileIdToId($profile_id);

			/* Utilisateur "visiteur" */
			$friendmanager = $this->managers->getManagerOf("friend");
			$um = $this->managers->getManagerOf('user');
			$um->bindVSS($this->app->vss_user);			
			if(!$um->isAuth())
				{
					$error->setTemplate("!Auth");
					$this->error_collector->display($error);
					$u = $um->getUser();
					$friendmanager->setOwner($u->id());
				}
			$u = $um->getUser();
			$friendmanager->setOwner($u->id());
				
			$manager = $this->managers->getManagerOf("folder");
			$manager->setOwner($userid);
			$filemanager = $this->managers->getManagerOf("file");
			$filemanager->setOwner($userid);
			
			/* Chargement du dossier courant */
			$r = $request->getData("id");
			if($r == "/")
				{
						$id = $manager->path_to_id($r);
				}
			else
				{
						$id = (int) substr($r,1);
				}
			
			/* The user is trying to access his own sharebox, redirecting to his files */
			if($userid == $u->id())
				{
					$this->app->httpresponse()->redirect('/files/'.$id);
				}
				
			$folder = $manager->load($id); 
			if(\lib\ToolBox::is_Error($folder))
				{
					$this->error_collector->display($folder);
				}
					
			/* Chargement des dossiers enfants publics */
			$folders = array();
			$children = $folder->getFolders($manager);
			foreach($children as $child_folder)
				{
					$f = $manager->load($child_folder['id']);
					if($f->permissions()->mode() == \lib\Permission::P_Public || ($f->permissions()->mode() == \lib\Permission::P_Friends && $friendmanager->isFriend($userid)))
						$folders[] = $manager->load($child_folder['id']);
				}
			
			/* Chargement des fichiers du dossier parent */
			$c_files = $folder->getFiles($manager);
			$files = array();
			
			foreach($c_files as $file)
				{
					$f = $filemanager->load($file);
					if($f->permissions()->mode() == \lib\Permission::P_Public || ($f->permissions()->mode() == \lib\Permission::P_Friends && $friendmanager->isFriend($userid)))
						$files[] = $f;
				}

			/* Permet l'affichage d'un bouton pour revnir au dossier parent */
			if($folder->has_parent())
				{
					$this->page->addVar('has_parent', true);
				}
            else
                {
                    $this->page->addVar('has_parent', false);
                }
			
			/* Récupération des modals */
			$mm = new \lib\ModalFactory();
			
			/* Transmisson des variables à la vue */
			$this->page->addVar('title', "Liste de vos documents");
			$this->page->addVar('folders', $folders);
			$this->page->addVar('files', $files);
			$this->page->addVar('pseudo', $request->getData("pseudo"));
			$this->page->addVar('current_folder_id', $folder->id());
			$this->page->addVar('current_folder_name', $folder->name());
			$this->page->addVar('modal_videoFile', $mm->getModal("videofile"));
			$this->page->addVar('modal_pictureFile', $mm->getModal("picturefile"));
			$this->page->addVar('modal_qrFile', $mm->getModal("qrfile"));
		}
	}
?>
