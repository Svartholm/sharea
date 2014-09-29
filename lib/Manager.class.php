<?php
		namespace lib;
    abstract class Manager
    {
        protected $dao;
        protected $owner;
        
        public function __construct($dao)
        {
            $this->dao = $dao;
            if(is_callable(array($this, 'init')))
            	{
            		$this->init();
            	}
        }
        
        public function owner()
        {
        	return $this->owner;
        }
        
        public function setOwner($owner)
        {
        	$this->owner = $owner;
        	if(is_callable(array($this, 'after_setOwner')))
        		$this->after_setOwner();
        }
    } 
