<?php
		namespace lib;
    abstract class BackController extends ApplicationComponent
    {
        protected $action = '';
        protected $managers = null;
        protected $module = '';
        protected $page = null;
        protected $jsonPage = null;
        protected $view = '';
        protected $error_collector;
        
        public function __construct(Application $app, $module, $action)
        {
            parent::__construct($app);
            $factory = new PDOFactory();
            $this->managers = new Managers($factory->api(), $factory->getConnexion());
           
            $this->page = new Page($app);
            $this->jsonPage = new JSONPage($app);
            
            $this->setModule($module);
            $this->setAction($action);
            $this->setView($action);
            $this->error_collector = new \lib\ErrorCollector($this->app, $this->module, $this->action, $factory->getConnexion());
        }
        
        public function execute()
        {
            $method = 'execute'.ucfirst($this->action);
            if (!is_callable(array($this, $method)))
            {
                throw new RuntimeException('L\'action "'.$this->action.'" n\'est pas définie sur ce module');
            }
            
            $this->$method($this->app->httpRequest());
        }
        
        public function page()
        {
            return $this->page;
        }
        
        public function jsonPage()
        {
        	return $this->jsonPage;
        }
        
        public function setModule($module)
        {
            if (!is_string($module) || empty($module))
            {
                throw new InvalidArgumentException('Le module doit être une chaine de caractères valide');
            }
            
            $this->module = $module;
        }
        
        public function setAction($action)
        {
            if (!is_string($action) || empty($action))
            {
                throw new InvalidArgumentException('L\'action doit �tre une chaine de caract�res valide');
            }
            
            $this->action = $action;
        }
        
        public function setView($view)
        {
            if (!is_string($view) || empty($view))
            {
                throw new InvalidArgumentException('La vue doit être une chaine de caractères valide');
            }
            
            $this->view = $view;
            
		/* Routing depending on the language */
		if(isset($_COOKIE['lang'])) {
            		$langue = $_COOKIE['lang'];
		}
		else {
			if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
				$langue = explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']);
				$langue = strtolower(substr(chop($langue[0]),0,2));
			}
		}
		/* If language exists in our system */
		if(!is_dir(dirname(__FILE__).'/../apps/'.$this->app->name().'/modules/'.$this->module.'/'.$langue.'_views')) {
			$langue = 'en';
		}
        $this->page->setContentFile(dirname(__FILE__).'/../apps/'.$this->app->name().'/modules/'.$this->module.'/'.$langue.'_views/'.$this->view.'.php');
        }
    } 