<?php
	namespace lib\entities;
	
	class User extends \lib\Record
  {
   	protected $password,
    					$profile,
    					$id_profile,
    					$config,
    					$date;

		/* Getters */
		public function password()
		{
			return $this->password;
		}

    public function profile()
    {
    	return $this->profile;
    }
    		
    public function errors()
    {
    	return $this->errors;
    }
    		
    public function id_profile()
    {
    	return $this->id_profile;
    }
    
    public function config()
    {
    	return $this->config;
    }
    
    public function date()
    {
    	return $this->date;
    }
    
    /* Setters */
    public function setProfile(Profile $profile)
    {
    	$this->profile = $profile;
    }
        
    public function setPassword($password)
    {
    	$this->password = $password;
    }
    
    public function setIdprofile($id)
    {
    	$this->id_profile = (int) $id;
    }
    
    public function setConfig($config)
    {
    	if(is_object($config) && get_class($config) == "lib\ConfigUser")
				$this->config = $config;
			else
    		$this->config = new \lib\ConfigUser($config);
    }
    
    public function setDate($date)
    {
    	$this->date = $date;
    }
    
    /* Methods */    
    public function isValid()
    {
    	$error = new \lib\Error();
      /* 128 = length of a sha512 hash */
      if(empty($this->password) || !is_string($this->password) || strlen($this->password) != 128)
      	{
      		$error->setWarnLevel(\lib\Error::wl_HIGH);
      		$error->setMessage("Mot de passe (hash) invalide");
      	}
      else if($this->id < 0)
      	{
      		$error->setWarnLevel(\lib\Error::wl_HIGH);
      		$error->setMessage("Numéro d'identification invalide");
      	}
      else if(!isset($this->profile))
      	{
      		$error->setWarnLevel(\lib\Error::wl_HIGH);
      		$error->setMessage("Profil invalide");
      	}
      else if($this->id_profile < 0)
      	{
      		$error->setWarnLevel(\lib\Error::wl_HIGH);
      		$error->setMessage("Identification de profil invalide");
      	}
        		
      $m = $error->message();
      if(empty($m))
				return true;
			else
				{
					$error->addRoute("User.class.php, isValid()");
					return $error;
				}
		}
		
		public function isAdmin($um)
		{
			return $um->isAdmin($this);
		}
	}
?>
