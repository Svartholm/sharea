<?php
		namespace lib;
    abstract class Application
    {
        protected $config;
        protected $httpRequest;
        protected $httpResponse;
        protected $jsonResponse;
        protected $name;
        
        public function __construct()
        {
            $this->config = new Config($this);
            $this->httpRequest = new HTTPRequest($this);
            $this->httpResponse = new HTTPResponse($this);
            $this->jsonResponse = new JSONResponse($this);
            $this->name = '';
        }
        
		    public function getController()
			  {
			    $router = new \lib\Router;
			    $xml = new \DOMDocument;
			    $isAjax = preg_match("#^/ajax/#", $this->httpRequest->requestURI());
			    
			    if($isAjax)
			    	{
			    		$xml->load(__DIR__.'/../apps/'.$this->name.'/config/ajax_routes.xml');
			    	}
			    else
			    	{
			    		$xml->load(__DIR__.'/../apps/'.$this->name.'/config/routes.xml');
			    	}
					    $routes = $xml->getElementsByTagName('route');
					    foreach ($routes as $route)
					    {
					      $vars = array();
					      
					      if ($route->hasAttribute('vars'))
					      {
					        $vars = explode(',', $route->getAttribute('vars'));
					      }
					      
					      $router->addRoute(new Route($route->getAttribute('url'), $route->getAttribute('module'), $route->getAttribute('action'), $vars));
					    }
					    
					    try
					    {
					      $matchedRoute = $router->getRoute($this->httpRequest->requestURI());
					    }
					    catch (\RuntimeException $e)
					    {
					      if ($e->getCode() == \lib\Router::NO_ROUTE)
					      {
					        $this->httpResponse->redirect404();
					      }
					    }
					    
					    $_GET = array_merge($_GET, $matchedRoute->vars());
					    
					    if($isAjax)
					    	$controllerClass = 'apps\\'.$this->name.'\\modules\\'.$matchedRoute->module().'\\'.ucfirst($matchedRoute->module()).'ControllerAjax';
					   	else
							$controllerClass = 'apps\\'.$this->name.'\\modules\\'.$matchedRoute->module().'\\'.ucfirst($matchedRoute->module()).'Controller';
								
			    return new $controllerClass($this, $matchedRoute->module(), $matchedRoute->action());
			  }
  
        abstract public function run();
        
        public function config()
        {
          return $this->config;
        }
        
        public function httpRequest()
        {
          return $this->httpRequest;
        }
        
        public function httpResponse()
        {
          return $this->httpResponse;
        }
        
        public function jsonResponse()
        {
        	return $this->jsonResponse;
        }
        
        public function name()
        {
          return $this->name;
        }
    } 
?>