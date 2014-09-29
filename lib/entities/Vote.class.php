<?php
	namespace lib\entities;
	class Vote extends \lib\Record
	{
		protected $id,
							$name, 
							$likes,
							$owner;
		
		/* Setters */
		public function setName($name)
		{
			$this->name = $name;
		}
		
		public function setLikes($likes)
		{
			$this->likes = $likes;
		}
		
		public function setOwner($owner)
		{
			$this->owner= $owner;
		}
		
		public function setId($id)
		{
			$this->id = $id;
		}
		
		/* Getters */
		public function name()
		{
			return $this->name;
		}
		
		public function likes()
		{
			return $this->likes;
		}
		
		public function owner()
		{
			return $this->owner;
		}
		
		public function id()
		{
			return $this->id;
		}
		
		/* Methods */
		public function isValid()
		{
			$e = new \lib\Error();
			
			if(!is_string($this->name) && strlen($this->name) < 10)
				$e->setMessage("Nom invalide");
			if(!is_numeric($this->likes))
				$e->setMessage("Likes invalides");
			if(!is_numeric($this->owner))
				$e->setMessage("Propriétaire invalide");
			
			$m = $e->message();
			if(!empty($m))
				{
					$e->setWarnLevel(\lib\Error::wl_LOW);
					$e->addRoute("isValid(), Vote");
					return $e;
				}
			
			return true;
		}
	}
?>
