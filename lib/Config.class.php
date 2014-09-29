<?php
	namespace lib;
	
	class Config extends ApplicationComponent
  {
  	protected $vars = array();
        
    public function get($var)
		{
			if (!$this->vars)
      	{
        	$xml = new \DOMDocument;
        	$xml->load(dirname(__FILE__).'/../apps/'.$this->app->name().'/config/app.xml');
                
        	$elements = $xml->getElementsByTagName('define');
                
         	foreach ($elements as $element)
          	{
            	$this->vars[$element->getAttribute('var')] = $element->getAttribute('value');
          	}
        }
            
			if (isset($this->vars[$var]))
		    {
		    	return $this->vars[$var];
		    }
            
      return null;
		}
	}
