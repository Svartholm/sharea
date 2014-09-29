<?php
	namespace lib;
	class PDOFactory extends DBMSFactory
	{
		public function getConnexion()
		{	
			$config_db = $this->vars['dbms'].':host='.$this->vars['host'].';dbname='.$this->vars['dbname'];
			$db = new \PDO($config_db, $this->vars['login'], $this->vars['password']);
			$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			return $db;
		}
	}

