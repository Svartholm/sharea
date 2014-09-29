<?php
	namespace lib;
	class Error extends Record
	{
		protected $app,
					$module,
					$action,
					$message,
					$template,
					$route,
					$warn_level,
					$return_type = self::type_HTML;
		
		/* Define three levels of severity */
		const	wl_LOW = 01,
					wl_HIGH = 02,
					wl_CRITICAL = 03;
		
		const type_HTML = 01,
					type_JSON = 02;
					
		/* Getters */
		public function app()
		{
			return $this->app;
		}
		
		public function module()
		{
			return $this->module;
		}
		
		public function action()
		{
			return $this->action;
		}
		
		public function message()
		{
			return $this->message;
		}
		
		public function warnLevel()
		{
			return $this->warn_level;
		}
		
		public function template()
		{
			return $this->template;
		}
		
		public function route()
		{
			return $this->route;
		}
		
		public function returnType()
		{
			return $this->return_type;
		}
		
		/* Setters */
		public function setApp($app)
		{
			$this->app = $app;
		}
		
		public function setModule($module)
		{
			$this->module = $module;
		}
		
		public function setAction($action)
		{
			$this->action = $action;
		}
		
		public function setMessage($message)
		{
			$this->message = $message;
		}
		
		public function setWarnLevel($level)
		{
			$this->warn_level = $level;
		}
		
		public function setTemplate($template)
		{
			$this->template = $template;
		}
		
		public function addRoute($route)
		{
			$this->route[] = $route;
		}
		
		public function setReturnType($type)
		{
			$this->return_type = $type;
		}
	}
?>
