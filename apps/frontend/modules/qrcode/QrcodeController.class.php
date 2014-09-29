<?php
	namespace apps\frontend\modules\qrcode;

	class QrcodeController extends \lib\BackController
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

			$content = '/download/'.(int) $request->getData('file');
			$filename = '../web/qrcode/qrcode'.(int) $request->getData('file').'.png';
			$errorCorrectionLevel = 'H';
			$matrixPointSize = 7;
	
			$test = \lib\QRcode::png($content, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
			$this->page->addVar('filename', $filename);


			$response = $this->app->httpresponse();
			$path_dl = $filename;

			$response->addHeader("Connection: Keep-Alive");
			$response->addHeader("Keep-Alive: max=100, timeout=7");
			$response->addHeader("Content-disposition: attachment; filename=\"".$filename."\"");
			$response->addHeader("Content-Type: image/png");
			$response->addHeader("Content-Transfer-Encoding: binary\n");
			$response->addHeader("Content-Length: ".filesize($filename));

			ob_clean();
			flush();
			readfile($path_dl);
			unlink($filename);
			exit();
		}
	}
?>
