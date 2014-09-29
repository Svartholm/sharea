<?php	
	namespace apps\frontend\modules\friends;
	class FriendsControllerAjax extends \lib\BackController
	{
		public function executeGetAll(\lib\HTTPRequest $request)
		{	
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
			$friendsLoaded = array();
			foreach($friends as $friend)
				{
					$user = $um->load($friend);
					if(!\lib\ToolBox::is_Error($user))
						{
							/* Id de l'user à rajouter */
							$friendsLoaded[] = array(
								"id" => $friend,
								"lastname" => $user->profile()->lastname(),
								"firstname" => $user->profile()->firstname(),
								"pseudo" => $user->profile()->pseudo(),
								"avatar" => $user->profile()->avatar()
							);
						}
				}
			$this->jsonPage->addVar('friends', $friendsLoaded);
		}

		public function executeDelete(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			if(!$request->postExists('friendid') || $request->postData('friendid') == NULL)
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
			
			$id = (int) $request->postData('friendid');
			if($id == $u->id() || !$um->exists($id) || !$friendmanager->isFriend($id))
			{
				$error->setMessage("La personne spécifiée n'existe pas : ".$id);
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


        public function executeFbInvite(\lib\HTTPRequest $request)
        {
            /* Partie de récupération des infos */
            $app_id = $this->app->config()->get('app_id');
            $app_secret = $this->app->config()->get('app_secret');
            $my_url = "http://sharea.net/facebook";

            session_start();
            $code = $_REQUEST["code"];

            if (empty($code)) {
                $this->jsonPage->addVar('success', 1);
            }

            if ($_SESSION['state'] && ($_SESSION['state'] === $_REQUEST['state'])) {
                $token_url = "https://graph.facebook.com/oauth/access_token?"
                    . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
                    . "&client_secret=" . $app_secret . "&scope=read_friendlists&code=" . $code;
                $response = file_get_contents($token_url);
                $params = null;
                parse_str($response, $params);

                $graph_url = "https://graph.facebook.com/me?access_token="
                    . $params['access_token'];
                $page = file_get_contents($graph_url);
                print_r($page);
                $user = json_decode($page);
                $this->jsonPage->addVar('user', $user);
                $this->jsonPage->addVar('success', 0);
            }
        }
	}
?>
