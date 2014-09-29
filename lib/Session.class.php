<?php
	namespace lib;
	class Session
	{
		protected $spaces = array();
		
		public function set($var, $value, $erase_old = false)
		{
			$_SESSION[$var] = $value;
		}
		
		public function del($var)
		{
			if(isset($_SESSION[$var]))
				unset($_SESSION[$var]);
		}
		
		public function exists($var)
		{	
			return isset($_SESSION[$var]);
		}
		
		public function get($var)
		{
			return $this->exists($var) ? $_SESSION[$var] : null;
		}
		
		public function regenerate_id($delete = true)
		{
			/* If delete is true, delete old session */
			if(is_bool($delete))
				session_regenerate_id($delete);
			else
				session_regenerate_id(true);
		}
		
		public function createVSpace($name, $prefix = "")
		{
			$space = new VSS;
			$space->setName($name);
			$space->setPrefix($prefix);
			$this->spaces[] = $name;
			
			return $space;
		}
		
		public function deleteVSpace($name)
		{
			unset($this->spaces[$name]);
		}
	}
?>
