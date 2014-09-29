<?php
	namespace lib;
	class Permission
	{
		const P_Private = 1,
				P_Public = 2,
				P_Custom = 3,
				P_Friends = 4;
		
		const PermOffset = 1,
				IdOffset = 0;
		
		const Granted = 1,
				Denied = 2,
				NoRule = -1;
					
		protected $mode = self::P_Private;
		protected $users = array(array());
		
		public function __construct($serial = "")
		{
			if(!empty($serial))
				{
					$this->permission = $this->parseSerial($serial);
				}
		}
		
		/* Setters */
		public function setMode($mode = self::P_Private)
		{
			$this->mode = $mode;
		}
		
		/* Getters */
		public function mode()
		{
			return $this->mode;
		}
		
		/* Methods */
		public function addRule($user, $rule)
		{
			$offset = count($this->users) - 1;
			$this->users[$offset][self::IdOffset] = $user;
			$this->users[$offset][self::PermOffset] = $rule;
		}
		
		public function getRule($id)
		{
			foreach($this->users as $user)
				{
					if($user[IdOffset] == $id)
						{
							return $user[PermOffset];
						}
				}
			
			/* No rules found, trying to use default rules */
			if($this->mode == self::P_Public)
				return self::Granted;
			else if($this->mode == self::P_Private)
				return self::Denied;
			else
				return self::NoRule;
		}
		
		public function getSerial()
		{
			$serial = "";
			
			$serial = $serial.$this->mode.';';
			
			if($this->mode == self::P_Custom)
				{
					foreach($this->users as $user)
						{
							$serial = $serial.$user[self::PermOffset].':'.$user[self::IdOffset].';';
						}
				}
				
			return $serial;
		}
		
		public function parseSerial($serial)
		{
			$this->mode = $serial[0];
			
			/* End of serial */
			if(strlen($serial) == 2)
				{
					return true;
				}
				
			$users = explode(';', substr($serial, 1));
			
			foreach($users as $user)
				{	
					if(!empty($user))
						{
							$a = explode(':', $user);
							$this->addRule($a[self::IdOffset], $a[self::PermOffset]);
						}
				}
				
			return true;
		}
	}
?>
