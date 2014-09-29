<?php
	namespace lib;
	abstract class DBMSFactory
	{
		protected $vars = array();
		protected $api_name;
		
		public function __construct($config_file = "")
		{
			if(empty($config_file))
				{
					$config_file = dirname(__FILE__)."/../config/params_db.xml";
				}
				
			$xml = new \DOMDocument;
      $xml->load($config_file);
      
      $elements = $xml->getElementsByTagName('var');
      
      foreach($elements as $element)
		    {
		    	$this->vars[$element->getAttribute('name')] = $element->getAttribute('value');
		    }
		  
		  $this->api_name = $this->vars['api'];
		}
		
		public function api()
		{
			return $this->api_name;
		}
	}
?>