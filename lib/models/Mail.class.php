<?php
	class Mail
	{
		protected $subject,
							$message = "",
							$sender= array(),
							$headers = "",
							$receivers = array();
		
		/* Getters */
		public function subject()
		{
			return $this->subject;
		}

		public function message()
		{
			return $this->message;
		}

		public function sender()
		{
			return $this->sender;
		}
		
		public function receivers()
		{
			return $this->receivers;
		}
		
		public function headers()
		{
			return $this->headers;
		}
		
		/* Setters */
		public function setSubject($subject)
		{
			$this->subject = $subject;
		}
		
		public function setSender(array $sender)
		{
			$this->sender = $sender;
			$this->addHeader('From: '.$this->sender[0].' <'.$this->sender[1].'>');
		}

		public function setMessage($message)
		{
			$this->message = $message;
		}

		public function addReceiver($receiver)
		{
			$this->receivers[] = $receiver;
		}
		
		public function setReceivers($receivers = array())
		{
			$this->receivers = $receivers;
		}
		
		public function addHeader($header)
		{
			$this->headers .= $header . "\r\n";
		}
		
		/* Methods */
		public function isValid() 
		{
			$error = new Error();
				
			if(empty($this->sender) || count($this->sender) != 2 || !Regex::isEmail($this->sender[1]))
				{
					$error->setMessage("Expéditeur invalide");
					$error->setWarnLevel(Error::wl_LOW);
				}
				
			if(!empty($this->receivers))
				{
					foreach($this->receivers as $receiver)
						{
							if(!Regex::isEmail($receiver))
								{
									$error->setMessage("Destinataire invalide");
									$error->setWarnLevel(Error::wl_HIGH);
									break;
								}
						}
				}
			else
				{
					$error->setMessage("Aucun destinataire précisé");
					$error->setWarnLevel(Error::wl_HIGH);
				}
			
			if(empty($this->subject) || !is_string($this->subject))
				{
					$error->setMessage("Sujet invalide");
					$error->setWarnLevel(Error::wl_LOW);
				}
			
			$m = $error->message();
			if(empty($m))
				{
					return true;
				}
			else
				{
					return $error;
				}
		}

		public function send() 
		{				
			$this->message = html_entity_decode($this->message);
			$this->message = str_replace('&#039;',"'",$this->message);
			$this->message = str_replace('&#8217;',"'",$this->message);
			$this->message = str_replace('<br>','',$this->message);
			$this->message = str_replace('<br />','',$this->message);

			foreach($this->receivers as $receiver)
				{
					if(!mail($receiver, $this->subject, $this->message, $this->headers))
					{
						$error = new Error();
						$error->setWarnLevel(Error::wl_HIGH);
						$error->setMessage("Impossible d'envoyer le mail");
						return $error;
					}
				}
				
			return true;
		}
	}
?>		
