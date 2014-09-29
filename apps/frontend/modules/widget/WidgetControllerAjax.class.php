<?php
namespace apps\frontend\modules\widget;
class WidgetControllerAjax extends \lib\BackController
{
	public function executeGetmemo()
	{
		$um = $this->managers->getManagerOf("user");
		$um->bindVSS($this->app->vss_user);

		if(!$um->isAuth())
		{
			$error = new \lib\Error;
			$error->setReturnType(\lib\Error::type_JSON);
			$error->setWarnLevel(\lib\Error::wl_LOW);
			$error->setMessage("Vous n'êtes pas connecté.");
			$this->error_collector->display($error);
		}
		
		$umemo = $this->managers->getManagerOf("memo");
		$u = $um->getUser();
		$umemo->setOwner($u->id());

		if(!$umemo->hasOwnerMemo())
		{
			$umemo->createMemo();
		}	
		$m = $umemo->getMemo();	
		$this->jsonPage->addVar('memo', $m);
	}

	public function executeSavememo(\lib\HTTPRequest $request)
	{
                $um = $this->managers->getManagerOf("user");
                $um->bindVSS($this->app->vss_user);

                if(!$um->isAuth())
                {
                        $error = new \lib\Error;
                        $error->setReturnType(\lib\Error::type_JSON);
                        $error->setWarnLevel(\lib\Error::wl_LOW);
                        $error->setMessage("Vous n'êtes pas connecté.");
                        $this->error_collector->display($error);
                }

		$umemo = $this->managers->getManagerOf("memo");
		$u = $um->getUser();
		$umemo->setOwner($u->id());
		if(!$request->postExists('idmemo') || !$request->postExists('notesarea'))
		{
			$error = new \lib\Error;
                        $error->setReturnType(\lib\Error::type_JSON);
                        $error->setWarnLevel(\lib\Error::wl_LOW);
                        $error->setMessage("Manque de données POST.");
                        $this->error_collector->display($error);
		}
		$m = new \lib\entities\Memo((int) $request->postData('idmemo'), $u->id(), htmlspecialchars($request->postData('notesarea')));
		$umemo->saveMemo($m);
	}
}
