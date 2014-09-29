<?php
	namespace lib;
	class ErrorCollector extends ApplicationComponent
	{
		protected $app,
							$module,
							$action,
							$dao;
							
		public function __construct($app, $module, $action, $dao)
		{
			$this->app = $app;
			$this->module = $module;
			$this->action = $action;
			$this->dao = $dao;
		}
		
		public function display(\lib\Error $error)
		{
			$error->setApp($this->app->name());
			$error->setModule($this->module);
			$error->setAction($this->action);
			
			$type = $error->returnType();
			$tpl = $error->template();
			
			$this->log($error);
			if($type == \lib\Error::type_JSON)
				{
					/* JSON return */
					$page = new JSONPage($this->app);
					if(empty($tpl))
						{
							if($error->warnLevel() == \lib\Error::wl_LOW)
								{
									$page->addVar("json_error", 'Erreur : '.$error->message());
								}
							else
								{
									$page->addVar("json_error", 'Une erreur critique est survenue');
								}
						}
					else
						{
							$dom = new \DOMDocument();
							$dom->load(dirname(__FILE__).'/../config/json_errors_templates.xml');
							foreach($dom->getElementsByTagName('template') as $template)
								{
									if($template->getAttribute('name') == $error->template())
										{
											$filename = $template->getAttribute('uri');
											$page->setContentFile(dirname(__FILE__).'/../errors/templates/json/'.$filename);
											break;
										}
								}
						}
						
						$this->app->jsonResponse()->setPage($page);
						$this->app->jsonResponse()->send();
				}
			else
				{
					/* HTML return */
					$page = new Page($this->app);
					if(empty($tpl))
						{
							if($error->warnLevel() == \lib\Error::wl_LOW)
								{
									$page->setContentFile(dirname(__FILE__).'/../errors/a_type.php');
								}
							else
								{
									$page->setContentFile(dirname(__FILE__).'/../errors/b_type.php');
								}
			
							$page->addVar("error", $error);
						}
					else
						{
							$dom = new \DOMDocument();
							$dom->load(dirname(__FILE__).'/../config/errors_templates.xml');
					
							foreach($dom->getElementsByTagName('template') as $template)
								{
									if($template->getAttribute('name') == $error->template())
										{
											$filename = $template->getAttribute('uri');
											$page->setContentFile(dirname(__FILE__).'/../errors/templates/html/'.$filename);
											break;
										}
								}
						}
						
					$this->app->httpResponse()->setPage($page);
					$this->app->httpResponse()->send();
				}
		}
		
		public function log(Error $error)
		{
			$request = $this->dao->prepare("INSERT INTO errors(application, module, action, message, route, warn_level, return_type, template) VALUES (:application, :module, :action, :message, :route, :warn_level, :return_type, :template)");
			$request->bindValue(':application', $error->app());
			$request->bindValue(':module', $error->module());
			$request->bindValue(':action', $error->action());
			$request->bindValue(':message', $error->message());
			$request->bindValue(':route', serialize($error->route()));
			$request->bindValue(':warn_level', (int) $error->warnLevel());
			$request->bindValue(':return_type', (int) $error->returnType());
			$request->bindValue(':template', $error->template());
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					throw $e;
				}
		}
	}
?>
