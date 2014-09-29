<?php
	namespace apps\frontend;
	
	class FrontendApplication extends \lib\Application
	{
		public function __construct()
		{
			parent::__construct();
			$this->name = "frontend";
			
			$this->session = new \lib\Session;
			$this->vss_user = $this->session->createVSpace("user", "user_");
			$this->vss_pool = $this->session->createVSpace("pool", "pool_");
			
			$factory = new \lib\PDOFactory();
			$this->user_manager = new \lib\models\UserManager_PDO($factory->api());
			$this->friend_manager = new \lib\models\FriendManager_PDO($this->name);
			$this->user_manager->bindVSS($this->vss_user);
			/* Have to lock the user vss */
		}
		
		public function run()
		{
			$controller = $this->getController();
			$controller->execute();
			
			if(preg_match("#^/ajax/#", $this->httpRequest->requestURI()))
				{
					/* Ajax request, sending a JSON response */
					$this->jsonResponse->setPage($controller->jsonPage());
					$this->jsonResponse->send();
				}
			else
				{
					/* HTML request, HTML response */
					$this->httpResponse->setPage($controller->page());
					$this->httpResponse->send();
				}
		}
	}
