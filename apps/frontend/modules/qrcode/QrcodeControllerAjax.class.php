<?php
	namespace apps\frontend\modules\qrcode;
	class QrcodeControllerAjax extends \lib\BackController
	{
		public function executeIndex(\lib\HTTPRequest $request)
		{
			$error = new \lib\Error;
			$um = $this->managers->getManagerOf('user');
			$um->bindVSS($this->app->vss_user);
			
			if(!$um->isAuth())
				{
					$error->setTemplate('!Auth');
					$this->error_collector->display($error);
				}
			if(!$request->getExists('file'))
				{
					$error->setMessage("Aucun fichier spécifié !");
					$error->setWarnlevel(\lib\Error::wl_LOW);
					$this->error_collector->display($error);
				}

			$content = 'http://sharea.net/download/'.(int) $request->getData('file');
			$filename = './qrcode/qrcode'.(int) $request->getData('file').'.png';
			$errorCorrectionLevel = 'H';
			$matrixPointSize = 7;
	
			$test = QRcode::png($content, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
			$this->page->addVar('filename', $filename);

			$this->jsonPage->addVar('code', base64_encode(file_get_contents($filename)));
			unlink($filename);
		}
	}
?>
