<?php
	namespace apps\frontend\modules\account;
	class AccountController extends \lib\BackController
	{
		public function executeIndex(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			
			$um = $this->managers->getManagerOf('user');
			$um->bindVSS($this->app->vss_user);
			
			if(!$um->isAuth())
				{
					$error->setTemplate('!Auth');
					$this->error_collector->display($error);
				}
			
			$user = $um->getUser();
			$this->page->addVar('user', $user);
		}
	}
?>
