<?php
	namespace apps\frontend\modules\friends;
	class FriendsController extends \lib\BackController
	{
		public function executeIndex(\lib\HTTPRequest $request)
		{
			$this->page->addVar('title', 'Amis');
			$um = $this->managers->getManagerOf('user');
			$um->bindVSS($this->app->vss_user);			
			
			if(!$um->isAuth())
				{
					$error = new \lib\Error;
					$error->setTemplate('!Auth');
					$this->error_collector->display($error);
				}
				
			$u = $um->getUser();			
			$friendmanager = $this->managers->getManagerOf("friend");
			$e = $friendmanager->setOwner($u->id());
			

			if(\lib\ToolBox::is_Error($e))
				$this->error_collector->display($e);
				
			/* Getting the friendlist and load it */
			$friends = $friendmanager->getFriends();
			$friends2 = array();
			foreach($friends as $friend)
				{
					$user = $um->load($friend);
					if(!\lib\ToolBox::is_Error($user))
						{
							$friends2[] = $user;
						}
				}
			$this->page->addVar('friends', $friends2);
			
			/* Getting the invitations and load it*/
			$ids = $friendmanager->getInvitations();
			$invit = array();
			foreach($ids as $id)
				{
					$user = $um->load($id[0]);
					if(!\lib\ToolBox::is_Error($user))
						$invit[] = $user;
				}

			/* Récupération des modals */
			$mm = new \lib\ModalFactory();

			$this->page->addVar('invit', $invit);
			$this->page->addVar('modal_removeFriend', $mm->getModal("removefriend"));
            $this->page->addVar('modal_inviteFB', $mm->getModal("facebookfriends"));
		}

		public function executeSearch(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			
			$um = $this->managers->getManagerOf('user');
			$um->bindVSS($this->app->vss_user);			
			if(!$um->isAuth())
				{
					$error->setTemplate('!Auth');
					$this->error_collector->display($error);
				}
			$u = $um->getUser();
			$friendmanager = $this->managers->getManagerOf("friend");
			$friendmanager->setOwner($u->id());
			
			
			if(!$request->postExists('searchFriends') || $request->postData('searchFriends') == '')
				{
					$error->setMessage("Votre recherche n'a retourné aucun résultat");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}

			$ids = $um->search($request->postData('searchFriends'));
			if(empty($ids))
				{
					$error->setMessage("Votre recherche n'a retourné aucun résultat");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
					
			foreach($ids as $id)
				{
					$user = $um->load($id);
					if(!\lib\ToolBox::is_Error($user))
						{
							if($u->id() != $user->id() && !$friendmanager->isFriend($user->id()))
								$found[] = $user;
						}
				}
			
			if(empty($found))
				{
					$error->setMessage("Votre recherche n'a retourné aucun résultat");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
				
			$this->page->addVar('found', $found);
			$this->page->addVar('nb', count($found));
		}

		// Envoi de la demande d'ajout à  la liste d'amis
		public function executeInvite(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			
			if(!$request->getExists('userid') || $request->getData('userid') == '')
				{
					$error->setMessage("Vous n'avez pas précisé quelle personne ajouter !");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}		
				
			$um = $this->managers->getManagerOf('user');
			$um->bindVSS($this->app->vss_user);			
			if(!$um->isAuth())
				{
					$error->setTemplate('!Auth');
					$this->error_collector->display($error);
				}
				
			$u = $um->getUser();
			
			$friendmanager = $this->managers->getManagerOf("friend");
			$friendmanager->setOwner($u->id());
			
			$nm = $this->managers->getManagerOf("notification");
			$nm->setOwner($u->id());
			
			$id = (int) $request->getData('userid');
			$my_profile = $u->profile();
			
			/* Si la personne demandée est l'user courant ou deja un amis */
			if(!$um->exists($id) || $id == $u->id() || $friendmanager->isFriend($id))
				{
					$error->setMessage("La personne demandée est invalide");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
			if($friendmanager->invite($id))
				{
					$this->page->addVar('invit', true);
					$notif = new \lib\entities\Notification;
					$notif->setMessage($my_profile->firstname().' '.$my_profile->lastname()." souhaite être votre ami");
					$notif->setLink('/friends');
					$notif->setThumbnail((int) $my_profile->avatar());
					$notif->setReceiver($id);
					
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
			else
				$this->page->addVar('invit', false);
			
		}
		
		public function executeDecline(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			if(!$request->getExists('userid') || $request->getData('userid') == '')
				{
					$error->setMessage("Vous n'avez pas précisé quelle invitation décliner.");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}		
			$um = $this->managers->getManagerOf('user');
			$um->bindVSS($this->app->vss_user);			
			if(!$um->isAuth())
				{
					$error->setTemplate('!Auth');
					$this->error_collector->display($error);
				}
			$u = $um->getUser();
			$friendmanager = $this->managers->getManagerOf("friend");
			$friendmanager->setOwner($u->id());		
			$id = (int) $request->getData('userid');
			
			/* Si la personne n'existe pas ou est l'user courant */
			if(!$um->exists($id) || $id == $u->id())
				{
					$error->setMessage("La personne demandée est invalide");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
			$r = $friendmanager->decline($id);
			if(\lib\ToolBox::is_Error($r))
				{
					$this->error_collector->display($r);
				}
		}
		
		// Ajout de l'ami après confirmation
		public function executeAccept(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;

			if(!$request->getExists('friendid') || $request->getData('friendid') == '')
				{
					$error->setMessage("Vous n'avez pas précisé quelle personne ajouter !");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}

			$um = $this->managers->getManagerOf('user');
			$um->bindVSS($this->app->vss_user);			
			if(!$um->isAuth())
				{
					$error->setTemplate('!Auth');
					$this->error_collector->display($error);
				}
			$u = $um->getUser();
			$friendmanager = $this->managers->getManagerOf("friend");
			$friendmanager->setOwner($u->id());	
			$nm = $this->managers->getManagerOf("notification");
			$nm->setOwner($u->id());
			
			$id = (int) $request->getData('friendid');
			if(!$um->exists($id) || $id == $u->id() || $friendmanager->isFriend($id) || !$friendmanager->hasInvite($id, $u->id()))
				{
					$error->setMessage("La personne demandée est invalide");
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
			$r = $friendmanager->add($id);
			if(\lib\ToolBox::is_Error($r)) 
				{
					$this->error_collector->display($r);
				}
						
			$idtoadd = $u->id();
			$friendmanager->setOwner($id);
			$r = $friendmanager->add($idtoadd);
			$friendmanager->decline($idtoadd);
			
			if(\lib\ToolBox::is_Error($r)) 
				{
					$this->error_collector->display($r);
				}
			
			$user_to_add = $um->load($idtoadd);
			$profile = $user_to_add->profile();
			
			$notif = new \lib\entities\Notification;
			$notif->setMessage($profile->firstname().' '.$profile->lastname()." est maintenant votre ami");
			$notif->setLink('/users/'.$profile->pseudo());
			$notif->setThumbnail((int) $profile->avatar());
			$notif->setReceiver($id);
					
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
		
		public function executeDelete(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			if(!$request->getExists('friendid') || $request->getData('friendid') == '')
			{
				$error->setMessage("Vous n'avez pas précisé quelle personne supprimer !");
				$error->setWarnLevel(\lib\Error::wl_LOW);
				$this->error_collector->display($error);
				}
			$um = $this->managers->getManagerOf('user');
			$um->bindVSS($this->app->vss_user);
			if(!$um->isAuth())
			{
				$error->setTemplate('!Auth');
				$this->error_collector->display($error);
				}
			$u = $um->getUser();
			$friendmanager = $this->managers->getManagerOf("friend");
			$friendmanager->setOwner($u->id());
			
			$id = (int) $request->getData('friendid');
			if($id == $u->id() || !$um->exists($id) || !$friendmanager->isFriend($id))
			{
				$error->setMessage("La personne spécifiée n'existe pas");
				$error->setWarnLevel(\lib\Error::wl_LOW);
				$this->error_collector->display($error);
				}
			$r = $friendmanager->delete($id);
			if(\lib\ToolBox::is_Error($r))
				$this->error_collector->display($r);
				
			$idtorm = $u->id();
			$friendmanager->setOwner($id);
			$r = $friendmanager->delete($idtorm);
			
			if(\lib\ToolBox::is_Error($r)) 
				{
				$this->error_collector->display($r);
				}
		}
	}
?>
