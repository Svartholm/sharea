<?php
	namespace lib;
	class VSS
	{
		protected $name,
					$prefix;
		
		/* Setters */
		public function setName($name)
		{
			$this->name = $name;
		}
		
		public function setPrefix($prefix)
		{
			$this->prefix = $prefix;
		}
		
		/* Getters */
		public function name()
		{
			return $this->name;
		}
		
		public function prefix()
		{
			return $this->prefix;
		}
		
		/* Methods */
		public function set($var, $value)
		{
			$_SESSION[$this->prefix.$var] = $value;
		}
		
		public function del($var)
		{
			if(isset($_SESSION[$this->prefix.$var]))
				unset($_SESSION[$this->prefix.$var]);
		}
		
		public function get($var)
		{
			return $this->exists($var) ? $_SESSION[$this->prefix.$var] : null;
		}
		
		public function exists($var)
		{	
			return isset($_SESSION[$this->prefix.$var]);
		}
	}
?>
