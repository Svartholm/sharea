<?php
	namespace apps\frontend\modules\contact;
	class ContactController extends \lib\BackController
	{
		public function executeIndex(\lib\HTTPRequest $request)
		{
			$this->page->addVar("title", "Contact");
			$um = $this->managers->getManagerOf('user');
			$um->bindVSS($this->app->vss_user);
			
			if($um->isAuth())
				{
					$u = $um->getUser();
					$profile = $u->profile();
					$this->page->addVar('email', $profile->email());
					$this->page->addVar('identity', $profile->firstname().' '.$profile->lastname());
				}
		}

		public function executeSend(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			
			$nom = $request->postExists("nom") ? $request->postData("nom") : null;
			$email = $request->postExists("email") ? $request->postData("email") : null;
			$objet = $request->postExists("objet") ? $request->postData("objet") : null;
			$message = $request->postExists("message") ? $request->postData("message") : null;
			
			if(empty($nom) || !is_string($nom))
				$error->setMessage("Nom invalide");
			else if(empty($email) || !\lib\Regex::isEmail($email))
				$error->setMessage("Email invalide");
			if(empty($objet) || !is_string($objet))
				$error->setMessage("Objet invalide");
			else if(empty($message))
				$error->setMessage("Message invalide");
			
			$m = $error->message();
			if(!empty($m))
				{
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
				
			$mail = new \lib\entities\Mail;
			$mail->setSender(array($nom, $email));
			$mail->setSubject('[/contact] '. $objet);
			$mail->setMessage($message);
			$mail->addReceiver("thuzhen@gmail.com");
			$ret = $mail->isValid();
			if(\lib\ToolBox::is_Error($ret))
				{
					if(\lib\ToolBox::is_Error($ret))
						$this->error_collector->display($ret);
				}
			
			$ret = $mail->send();
			if(\lib\ToolBox::is_Error($ret))
				{
					if(\lib\ToolBox::is_Error($ret))
						$this->error_collector->display($ret);
				}
		}
	}
?>
