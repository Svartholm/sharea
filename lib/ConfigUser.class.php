<?php
	namespace lib;
	
	class ConfigUser
	{
		protected $config = array();

		public function __construct($serial)
		{
			$this->config = unserialize($serial);
		}
		
		public function set($attribute, $value)
		{
			$this->config[$attribute] = $value;
		}
		
		public function get($attribute)
		{
			return isset($this->config[$attribute]) ? $this->config[$attribute] : null;
		}
		
		public function getSerial()
		{
			return serialize($this->config);
		}
	}
?>
