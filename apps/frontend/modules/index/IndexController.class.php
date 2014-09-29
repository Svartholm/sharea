<?php
	namespace apps\frontend\modules\index;
	class IndexController extends \lib\BackController
	{
		public function executeIndex(\lib\HTTPRequest $request)
		{
			$this->page->addVar('title', 'Accueil');
			$um = $this->managers->getManagerOf('user');
			$um->bindVSS($this->app->vss_user);
			$isAuth = false;
			$this->page->addVar('isAuth', $um->isAuth());
		}
		
		public function execute24h(\lib\HTTPRequest $request)
		{
			$this->app->httpresponse()->redirect('/users/24h/files/');
		}

		public function executeZenk()
		{
			;;
		}
	}
?>
