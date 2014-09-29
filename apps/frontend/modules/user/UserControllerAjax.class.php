<?php
	namespace apps\frontend\modules\user;
	class UserControllerAjax extends \lib\BackController
	{
		public function executeGetNotifications()
		{
			$nm = $this->managers->getManagerOf("notification");
			$um = $this->managers->getManagerOf("user");
			$um->bindVSS($this->app->vss_user);
			
			if(!$um->isAuth())
				{
					$error = new \lib\Error;
					$error->setReturnType(\lib\Error::type_JSON);
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$error->setMessage("Impossible de récupérer les notifications");
					
					$this->error_collector->display($error);
				}
			
			$u = $um->getUser();
			$nm->setOwner($u->id());
			
			$nb = $nm->count();
			$this->jsonPage->addVar('notifications', $nm->count());
		}
		
		public function executeIsLogged()
		{
			$um = $this->managers->getManagerOf("user");
			$um->bindVSS($this->app->vss_user);
			
			$this->jsonPage->addvar('logged', $um->isAuth());
		}
	}
?>
