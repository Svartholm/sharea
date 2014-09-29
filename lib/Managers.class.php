<?php
		namespace lib;
		
    class Managers
    {
        protected $api = null;
        protected $dao = null;
        protected $managers = array();
        
        public function __construct($api, $dao)
        {
            $this->api = $api;
            $this->dao = $dao;
        }
        
        public function getManagerOf($module, $extra_args = array())
        {
        	/* Returns appropriate manager, and bind variables passed by
        			extra_args (is exists) */
        			
            if (!is_string($module) || empty($module))
            {
                throw new InvalidArgumentException('Le module spécifié est invalide');
            }
            
            if (!isset($this->managers[$module]))
            {
                $manager = '\\lib\\models\\'.ucfirst($module).'Manager_'.strtoupper($this->api);
                $this->managers[$module] = new $manager($this->dao);
                
                if(is_array($extra_args) && !empty($extra_args))
                	{
                		foreach($extra_args as $extra)
                			{
                				$method = 'bind'.ucfirst($extra[0]);
                				if(is_callable(array($this->managers[$module], $method)))
                					{
                						$this->managers[$module]->$method($extra[1]);
                					}
                			}
                	}
            }
            
            return $this->managers[$module];
        }
    }

