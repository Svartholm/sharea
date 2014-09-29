<?php
	namespace apps\frontend\modules\modal;
	class ModalControllerAjax extends \lib\BackController
	{
		public function executeGet(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			
			if(!$request->getExists('modal_id'))
				{
					$error->setMessage("Le modal n'a pas été précisé");
					$error->setReturnType(\lib\Error::type_JSON);
					$error->setWarnLevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}
			
			$modal_id = $request->getData('modal_id');
			$dom = new \DOMDocument();
			$dom->load(dirname(__FILE__).'/../../../../config/modals.xml');
			foreach($dom->getElementsByTagName('modal') as $modal)
				{
					if($modal->getAttribute('id') == $modal_id)
						{
							$this->jsonPage->addVar('name', $modal->getAttribute('name'));
							$this->jsonPage->addVar('modal', file_get_contents('../../../../modals/'.$modal->getAttribute('uri')));
							break;
						}
				}
		}
	}
?>