<?php
	class Notification extends Record
	{
		protected $message,
							$receiver,
							$link,
							$thumbnail,
							$date,
							$state = self::st_UNSEEN;
		
		const st_UNSEEN = 0,
					st_SEEN = 1;
		
		/* Setters */
		public function setMessage($message)
		{
			$this->message = $message;
		}
		
		public function setReceiver($receiver)
		{
			$this->receiver = $receiver;
		}
		
		public function setLink($link)
		{
			$this->link = $link;
		}
		
		public function setThumbnail($thumbnail)
		{
			$this->thumbnail = $thumbnail;
		}
		
		public function setState($state)
		{
			$this->state = $state;
		}
		
		public function setDate($date)
		{
			$this->date = $date;
		}
		
		/* Getters */
		public function message()
		{
			return $this->message;
		}
		
		public function receiver()
		{
			return $this->receiver;
		}
		
		public function link()
		{
			return $this->link;
		}
		
		public function thumbnail()
		{
			return $this->thumbnail;
		}
		
		public function state()
		{
			return $this->state;
		}
		
		public function date()
		{
			return $this->date;
		}
		
		/* Methods */
		public function send(NotificationManager $manager)
		{
			return $manager->add($this);
		}
		
		public function isValid()
		{ 
		  $error = new Error();
		  if(empty($this->message) || !is_string($this->message))
		    {
		      $error->setMessage("Le message n'est pas valide");
		      $error->setWarnLevel(Error::wl_LOW);
		    }
		  else if(empty($this->link) || !is_string($this->link))
		    {
		      $error->setMessage("Le lien n'est pas valide");
		      $error->setWarnLevel(Error::wl_LOW);
		    }
		  else if(!is_numeric($this->thumbnail))
		    {
		      $error->setMessage("L'image n'est pas valide");
		      $error->setWarnLevel(Error::wl_LOW);
		    }
		  else if(!is_numeric($this->receiver))
		    {
		      $error->setMessage("Le récepteur est invalide");
		      $error->setWarnLevel(Error::wl_LOW);
		    }
		  else if(!is_numeric($this->state))
		  	{
		  		$error->setMessage("L'état est invalide");
		  		$error->setWarnLevel(Error::HIGH);
		  	}
		  $m = $error->message();
		  
		  if(empty($m))
				return true;
			else
				{
					$error->addRoute("isValid(), Notification.class.php");
					return $error;
				}
		}
		
	}
?>
