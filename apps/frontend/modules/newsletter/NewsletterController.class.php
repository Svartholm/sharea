<?php
	namespace apps\frontend\modules\newsletter;
	class NewsletterController extends \lib\BackController
	{
		public function executeIndex(\lib\HTTPRequest $request)
		{
			$this->page->addVar('title', "Newsletter");

			/* Si aucune adresse e-mail n'a été passé en POST, on génère une erreur */
			if(!$request->postExists("newsmail") || !\lib\Regex::isEmail($request->postData("newsmail"))) {
				$error = new \lib\Error;
				$error->setMessage("Veuillez entrer une adresse e-mail valide !");
				$error->setWarnLevel(\lib\Error::wl_LOW);
				$this->error_collector->display($error);
			}
			/* Sinon on le rajoute dans la BDD */
			else {
				$mm = $this->managers->getManagerOf('newsletter');
				$mm->add($request->postData("newsmail"));
			}
		}
	}
?>
