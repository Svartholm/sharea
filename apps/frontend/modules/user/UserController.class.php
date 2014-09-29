<?php
	namespace apps\frontend\modules\user;
	class UserController extends \lib\BackController
	{
		public function executeCreate(\lib\HTTPRequest $request)
		{
			/* FUNCTION : Let the user creates an account */
			$this->page->addVar("title", "Rejoignez-nous !");
			
			/* Initializing managers */
			$profile_manager = $this->managers->getManagerOf("profile");
			$foldermanager = $this->managers->getManagerOf("folder");
			$user_manager = $this->managers->getManagerOf("user");
			$user_manager->bindVSS($this->app->vss_user);
			
			if($user_manager->isAuth())
				{
					$error = new \lib\Error;
					$error->setTemplate('IsLogged');
					$this->error_collector->display($error);
				}
						
			if($request->postExists("submit"))
			{
				if($request->postExists("term_accept"))
				{					
					/* Check out if all the inputs has been filled */
					$error = new \lib\Error;
					if (!$request->postExists("firstname"))
						$error->setMessage("Entrez un prénom");
					else if(!$request->postExists("lastname"))
						$error->setMessage("Entrez un prénom");
					else if(!$request->postExists("email"))
						$error->setMessage("Entrez un prénom");
					else if(!$request->postExists("pseudo"))
						$error->setMessage("Entrez un prénom");
					else if(!$request->postExists("password"))
						$error->setMessage("Entrez un prénom");
						
					$m = $error->message();
					if(!empty($m))
						{
							$error->setWarnLevel(\lib\Error::wl_LOW);
							$this->error_collector->display($error);
						}
					
					$user = new \lib\entities\User;
					$user_profile = new \lib\entities\Profile;
					
					/* Creating user's profile */
					$user_profile->setFirstname(ucfirst(mb_strtolower($request->postData("firstname"), 'UTF-8')));
					$user_profile->setLastname(ucfirst(mb_strtolower($request->postData("lastname"), 'UTF-8')));
					$user_profile->setEmail(strtolower($request->postData("email")));
					$user_profile->setPseudo(strtolower($request->postData("pseudo")));
					
					$r = $user_profile->isValid();
					if(\lib\ToolBox::is_Error($r))
						{
							/* Invalid profile */
							$this->error_collector->display($r);
						}
						
					/* Check out if the user already exists */
					if($profile_manager->exists("pseudo", $user_profile->pseudo()))
						{
							$error = new \lib\Error;
							$error->setWarnLevel(\lib\Error::wl_LOW);
							$error->setMessage("Ce pseudo déjà utilisé");
							$this->error_collector->display($error);
						}
					else if($profile_manager->exists("email", $user_profile->email()))
						{
							$error = new \lib\Error;
							$error->setWarnLevel(\lib\Error::wl_LOW);
							$error->setMessage("Cette adresse e-mail est déjà utilisée");
							$this->error_collector->display($error);
						}
					
					$user_profile = $profile_manager->add($user_profile);
					if(\lib\ToolBox::is_Error($user_profile))
						{
							$this->error_collector->display($user_profile);
					
						}
						
					$user_config = new \lib\ConfigUser();

					$user_config->set('show_lastname', $request->postExists('show_lastname'));
					$user_config->set('show_firstname', $request->postExists('show_firstname'));
					$user_config->set('base_space', '3Go');
					$user_config->set('free_space', 3000000000);
					
					$user->setPassword(hash("sha512", $request->postData("password")));
					$user->setProfile($user_profile);
					$user->setConfig($user_config->getSerial());
					$user->setDate(date("d/m/Y"));

					/* The user is now complete, checking out his validity */
					$r = $user->isValid();
					if(\lib\ToolBox::is_Error($r))
						{	
							/* Invalid user */
							$this->error_collector->display($r);
						}
					else
						{
							/* Alright, adding the user ! */
							$r = $user_manager->add($user);
							
							/* If the adding has failed */
							if(\lib\ToolBox::is_Error($r))
								{
									$r->setMessage($r->message()." Line 101");
									$this->error_collector->display($r);
								}
							
							/* Now some folders */
								/* Temporarily logged */
							$r = $user_manager->Auth($request->postData("pseudo"), $request->postData("password"));
							if(\lib\ToolBox::is_Error($r))
								{
									$this->error_collector->display($r);
								}
								/* In order the get the user id */
							$user = $user_manager->getUser();
							$foldermanager->setOwner($user->id());
							
							$perm = new \lib\Permission;
							$perm->setMode(\lib\Permission::P_Private);
							
								/* Root folder */
							$folder = new \lib\entities\Folder;
							$folder->setName('Racine');
							$folder->setParent(\lib\entities\Folder::NO_PARENT);
							$folder->setPath('/');
							$folder->setDate(date('d/m/Y H:i'));
							$folder->setPermissions($perm->getSerial());
							
							$r = $foldermanager->add($folder, true);
							if(\lib\ToolBox::is_Error($r))
								$this->error_collector->display($r);
							
							$root = $foldermanager->load($foldermanager->path_to_id('/'));
							
							$folder = new \lib\entities\Folder;
							$folder->setName('Musique');
							$folder->setParent($root->id());
							$folder->setPath('/Musique');
							$folder->setDate(date('d/m/Y H:i'));
							$folder->setPermissions($perm->getSerial());
							$folders[] = $folder;
							
							$folder = new \lib\entities\Folder;
							$folder->setName('Photos');
							$folder->setParent($root->id());
							$folder->setPath('/Photos');
							$folder->setDate(date('d/m/Y H:i'));
							$folder->setPermissions($perm->getSerial());
							$folders[] = $folder;
							
							$folder = new \lib\entities\Folder;
							$folder->setName('Vidéos');
							$folder->setParent($root->id());
							$folder->setPath('/Videos');
							$folder->setDate(date('d/m/Y H:i'));
							$folder->setPermissions($perm->getSerial());
							$folders[] = $folder;
							
							foreach($folders as $folder)
								{
									$r = $foldermanager->add($folder, true);
									if(\lib\ToolBox::is_Error($r))
										{
											$user_manager->logout();
											$this->error_collector->display($r);
										}
								}
								
								/* Logging out now */
							$user_manager->logout();
						}
						
						/* If the user sent the form */
						$this->page->addVar("filled", 1);
			
				}
				/* The user hasn't accept terms */
				else
				{
					$this->page->addVar("noterm", 1);
				}
			}
		}
		
		public function executeDelete(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			/* Initializing managers */
			$profilemanager = $this->managers->getManagerOf("profile");
			$foldermanager = $this->managers->getManagerOf("folder");
			$filemanager = $this->managers->getManagerOf("file");
			$usermanager = $this->managers->getManagerOf("user");
			$usermanager->bindVSS($this->app->vss_user);
			
			/* If the user isn't logged */
			if(!$usermanager->isAuth())
				{
					$error->setTemplate('!Auth');
					$this->error_collector->display($error);
				}
			$user = $usermanager->getUser();
			
			$foldermanager->setOwner($user->id());
			$filemanager->setOwner($user->id());

			
			
			/* Deleting user's profile */
			$profilemanager->delete($user->profile());
			
			/* Deleting all folders */
			$foldermanager->deleteAllFolders();
			
			/* Deleting all files */
			$filemanager->deleteAllFiles();
			
			/* Deleting the user */
			$usermanager->delete($user);
			
			/* Disconnect the user */
			$usermanager->logout();
		}
		
		public function executeAuth(\lib\HTTPRequest $request)
		{
			/* FUNCTION : Auth the user */
			$this->page->addVar("title", "Connexion");
			
			if($request->postExists("submit"))
				{
					/* Checking out if the inputs were filled correctly */
					if (!$request->postExists("login"))
						$errors[] = "Entrez votre nom d'utilisateur";
					else if(!$request->postExists("password"))
						$errors[] = "Entrez votre mot de passe";
					
					if(!empty($errors))
						{
							/* Display the error */
							$error = new \lib\Error;
							$error->setWarnLevel(\lib\Error::wl_LOW);
							$error->setMessage($errors[0]);
						}
					
					$user_manager = $this->managers->getManagerOf("user");
					$user_manager->bindVSS($this->app->vss_user);
					$r = $user_manager->Auth($request->postData('login'), $request->postData('password'));
					if(\lib\ToolBox::is_Error($r))
						{
							$this->error_collector->display($r);
						}
					
					/* If the user was trying to access a page, redirect him on it */
					$redirect = $this->app->vss_pool->get('redirect_to');
					$this->app->vss_pool->del('redirect_to');
					if($redirect)
						{
							$this->app->httpresponse()->redirect($redirect);
						}
					else
						{
							$this->app->httpresponse()->redirect("/files");
						}			
				}
		}

		public function executeLogout(\lib\HTTPRequest $request)
		{
			$this->page->addVar('title', 'Déconnexion');
			$error = new \lib\Error;
								
			$user_manager = $this->managers->getManagerOf("user");
			$user_manager->bindVSS($this->app->vss_user);
			
			if(!$user_manager->isAuth())
				{
					/* Not logged, displaying appropriate message */
					$error->setTemplate('!Auth');
					$this->error_collector->display($error);
				}
			$user_manager->logout();
			$this->app->httpresponse()->redirect("/");
		}
		
		public function executeProfile(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			$is_users_profile = false;
			$pseudo = $request->getData('pseudo');
			$this->page->addVar('title', 'Profil de '.$pseudo);
			$changepass = false;
			
			$um = $this->managers->getManagerOf("user");
			$um->bindVSS($this->app->vss_user);
			$profilemanager = $this->managers->getManagerOf("profile");
			$filemanager = $this->managers->getManagerOf("file");
			
			$id = $um->pseudoToId($pseudo);
			$filemanager->setOwner($id);
			$c_files = $filemanager->getFilesOfType('image%');
			$files = array();
			
			foreach($c_files as $file)
				{
					$files[] = $filemanager->load($file);
				}
			
			if(\lib\ToolBox::is_Error($id))
				{
					$this->error_collector->display($id);
				}

			$user = $um->load($id);
			if(\lib\ToolBox::is_Error($user))
				{
					$this->error_collector->display($user);
				}

			if(!$um->isAuth())
				{
					$error->setTemplate('!Auth');
					$this->error_collector->display($error);
				}
				
			$currentuser = $um->getUser();
			$profile = $currentuser->profile();

			if(\lib\ToolBox::is_Error($currentuser))
				{
					$this->error_collector->display($currentuser);
				}

			if($pseudo == $profile->pseudo()) 
				{ 
					$is_users_profile = true; 
				}


			/* The user decided to changes his profile */
			if($request->postExists("submit") && $is_users_profile == true)
				{
					if(!$request->postExists("firstname"))
						$error->setMessage("Vous n'avez pas bien spécifié votre prénom");
					else if(!$request->postExists("lastname"))
						$error->setMessage("Vous n'avez pas bien spécifié votre nom de famille");
					else if(!$request->postExists("email"))
						$error->setMessage("Vous n'avez pas bien spécifié votre email");
					
					if($request->postExists('new_password') && $request->postData('new_password') != "")
						{
							$changepass = true;
							if(!$request->postExists('old_password'))
								$error->setMessage("Veuillez entrer votre ancien mot de passe");
							else if(!$request->postExists('new_password2'))
								$error->setMessage("Veuillez retaper votre nouveau mot de passe");
							
							if($currentuser->password() != hash('sha512', $request->postData('old_password')))
								{
									$error->setMessage("Votre ancien mot de passe est invalide");
								}
							if($request->postData('new_password') != $request->postData('new_password2'))
								$error->setMessage("Les deux mots de passes ne sont pas identiques");
						}
					
					$m = $error->message();
					if(!empty($m))
						{
							$error->setWarnLevel(\lib\Error::wl_LOW);
							$error->addRoute("UserController executeProfile()");
							$this->error_collector->display($error);
						}
					
					if($profilemanager->exists("email", $request->postData("email")) && $request->postData("email") != $profile->email())
						{
							$error = new \lib\Error;
							$error->setWarnLevel(\lib\Error::wl_LOW);
							$error->setMessage("Cette adresse e-mail est déjà utilisée");
							$this->error_collector->display($error);
						}
						
					if($changepass)
						{
							$currentuser->setPassword(hash('sha512', $request->postData('new_password')));
						}
						
					$profile->setFirstname(ucfirst(mb_strtolower($request->postData("firstname"), 'UTF-8')));
					$profile->setLastname(ucfirst(mb_strtolower($request->postData("lastname"), 'UTF-8')));
					$profile->setEmail(strtolower($request->postData("email")));
					if($request->postExists('avatar'))
						$a = $request->postData('avatar');
					if(isset($a) && $a != -1 && !empty($a))
						{
							$profile->setAvatar((int) $request->postData("avatar"));
					
							/* Need to change the permission of the avatar */
							$avatar = $filemanager->load((int) $request->postData('avatar'));				
							if(\lib\ToolBox::is_Error($avatar)) {
								$this->error_collector->display($avatar);
							}
							$perm = new \lib\Permission;
							$perm->setMode(\lib\Permission::P_Public);
							$avatar->setPermissions($perm->getSerial());
							$r = $filemanager->update($avatar, true);
							if(\lib\ToolBox::is_Error($r)) {
								$this->error_collector->display($r);
							}
						}

					/* Updating profile */
					$r = $profile->isValid();
					if(\lib\ToolBox::is_Error($r))
						{
							$this->error_collector->display($r);
						}	
					$r = $profilemanager->update($profile);
					if(\lib\ToolBox::is_Error($r))
						{
							$this->error_collector->display($r);
						}
					$config = $currentuser->config();
					$config->set('show_lastname', $request->postExists('show_lastname'));
					$config->set('show_firstname', $request->postExists('show_firstname'));
					
					$currentuser->setProfile($r);
					$currentuser->setConfig($config->getSerial());
					
					/* Updating user */
					$r = $currentuser->isValid();
					if(\lib\ToolBox::is_Error($r))
						{
							$this->error_collector->display($r);
						}	
						
					$user = $um->update($currentuser);
					if(\lib\ToolBox::is_Error($user))
						{
							$this->error_collector->display($user);
						}
						
					/* Update user session */
					$um->reloadSession($user);
					$this->page->addVar('notification', 'Votre profil a bien été mis à jour');
				}

			$this->page->addVar("user", $user);
			$this->page->addVar("pseudo", $pseudo);
			$this->page->addVar("is_users_profile", $is_users_profile);
			$this->page->addVar("files", $files);
			$mm = new \lib\ModalFactory();
			$this->page->addVar("modalCode", $mm->getModal("code"));
		}
		
		public function executeFacebook(\lib\HTTPRequest $request)
		{
			$usermanager = $this->managers->getManagerOf("user");
			$usermanager->bindVSS($this->app->vss_user);
			
			if($usermanager->isAuth())
			  {
				  $this->page->addVar("res", 0);
			  }
			else
			  {
				  /* Partie de récupération des infos */
				 $app_id = $this->app->config()->get('app_id');
			     $app_secret = $this->app->config()->get('app_secret');
			     $my_url = $this->app->config()->get('callback_url'); //"http://sharea.net/facebook";
			
			     session_start();
			     $code = $_REQUEST["code"];
			
			     if(empty($code))
			       {
			         $_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
			         $dialog_url = "https://www.facebook.com/dialog/oauth?client_id=" 
			           . $app_id . "&redirect_uri=" . urlencode($my_url) . "&scope=email&state="
			           . $_SESSION['state'];
			         echo("<script> top.location.href='" . $dialog_url . "';</script>");
			       }
			
			     if($_SESSION['state'] && ($_SESSION['state'] === $_REQUEST['state']))
			       {
			         $token_url = "https://graph.facebook.com/oauth/access_token?"
			           . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
			           . "&client_secret=" . $app_secret . "&scope=email&code=" . $code;
			         $response = file_get_contents($token_url);
			         $params = null;
			         parse_str($response, $params);
			
			         $graph_url = "https://graph.facebook.com/me?access_token=" 
			           . $params['access_token'];
			         $user = json_decode(file_get_contents($graph_url));
			         
			         /* Partie "d'examination" dans notre BDD */
			         $pmanager = $this->managers->getManagerOf("profile");
			         	
			         	/* Si l'email est déjà  dans notre BDD, alors on le loggue */
			         	if($pmanager->exists('email', $user->email))
			           	{
			           		$r = $usermanager->AuthFB($user->email);
						        if(\lib\ToolBox::is_Error($r))
						          {
							          $r->addRoute("AuthFB, UserManager_PDO");
							          return $r;
						          }
						        header("Location: /");
			           	}
			         	
			         	/* Sinon, il faut le rajouter dans la BDD, et le logguer */
			         	else
			           	{
			           		$this->page->addVar("res", 2);
			           		$this->page->addVar("fbuser", $user);
			           	}
				    }
			  }
		}
	}
?>
