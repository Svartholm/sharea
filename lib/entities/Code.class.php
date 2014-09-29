<?php
	namespace lib\entities;
	class Code extends \lib\Record
	{
		protected $code,
							$description,
							$validity,
							$action,
							$dateStart,
							$dateEnd;
		
		/* Setters */
		public function setCode($code)
		{
			$this->code = $code;
		}
		
		public function setDescription($description)
		{
			$this->description = $description;
		}
		
		public function setValidity($validity)
		{
			$this->validity = (int) $validity;
		}
		
		public function setDateStart($date)
		{
			$this->dateStart = (int) $date;
		}
		
		public function setDateEnd($date)
		{
			$this->dateEnd = (int) $date;
		}
		
		public function setAction($action)
		{
			$this->action = $action;
		}
		
		/* Getters */
		public function code()
		{
			return $this->code;
		}
		
		public function description()
		{
			return $this->description;
		}
		
		public function validity()
		{
			return $this->validity;
		}
		
		public function dateStart()
		{
			return $this->dateStart;
		}
		
		public function dateEnd()
		{
			return $this->dateEnd;
		}
		
		public function action()
		{
			return $this->action;
		}
		
		/* Methods */
		public function isValid()
		{
			$error = new \lib\Error();
			if(!is_string($this->code))
				{
					$error->setMessage("Le code n'est pas valide'");
					$error->setWarnLevel(\lib\Error::wl_HIGH);
					$error->addRoute("isValid(), Code.class.php");
				}
			else if(!is_string($this->description))
				{
					$error->setMessage("La description est invalide");
					$error->setWarnLevel(\lib\Error::wl_HIGH);
					$error->addRoute("isValid(), Code.class.php");
				}
			else if(!is_int($this->validity))
				{
					$error->setMessage("La validité n'est pas valide");
					$error->setWarnLevel(\lib\Error::wl_HIGH);
					$error->addRoute("isValid(), Code.class.php");
				}
			else if(!is_int($this->dateStart) || !is_int($this->dateEnd))
				{
					$error->setMessage("Les dates ne sont valides");
					$error->setWarnLevel(\lib\Error::wl_HIGH);
					$error->addRoute("isValid(), Code.class.php");
				}
			
			$m = $error->message();
			if(!empty($m))
				{
					return $error;
				}
			else
				{
					return true;
				}
		}
	}
?>