<?php
	namespace apps\frontend\modules\notification;
	class NotificationControllerAjax extends \lib\BackController
	{
		public function executeGetNotifications(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			$um = $this->managers->getManagerOf('user');
			$um->bindVSS($this->app->vss_user);
			$nm = $this->managers->getManagerOf("notification");
			
			if(!$um->isAuth())
				{
					$error->setTemplate("!Auth");
					$error->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($error);
				}
			
			$u = $um->getUser();
			$nm->setOwner($u->id());
			
			$notifications = $nm->getNotifications();
			if(\lib\ToolBox::is_Error($notifications))
				{
					$notifications->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($notifications);
				}
				
			$this->jsonPage->addVar('notifications', $notifications);
		}
		
		public function executeSeen(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			$um = $this->managers->getManagerOf('user');
			$um->bindVSS($this->app->vss_user);
			$nm = $this->managers->getManagerOf("notification");
			
			if(!$um->isAuth())
				{
					$error->setTemplate("!Auth");
					$error->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($error);
				}
			
			$u = $um->getUser();
			$nm->setOwner($u->id());
			
			$notifications = $nm->getNotifications();
			if(\lib\ToolBox::is_Error($notifications))
				{
					$notifications->setReturnType(\lib\Error::type_JSON);
					$this->error_collector->display($notifications);
				}
			
			foreach($notifications as $notif)
				{
					$notif = $nm->load($notif['id']);
					$notif->setState(Notification::st_SEEN);
					$r = $nm->update($notif);
					
					if(\lib\ToolBox::is_Error($r))
						{
							$r->setReturnType(\lib\Error::type_JSON);
							$this->error_collector->display($r);
						}
				}
		}
	}
?>
