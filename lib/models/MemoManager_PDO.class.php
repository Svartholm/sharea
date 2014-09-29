<?php
namespace lib\models;
use \lib\entities\Memo;

class MemoManager_PDO extends MemoManager
{
	public function getMemo()
	{
		$error = new \lib\Error();
		$request = $this->dao->prepare("SELECT * FROM memo WHERE owner = :user");
		$request->bindValue(':user',(int) $this->owner, \PDO::PARAM_INT);	
		try {
			$request->execute();
		}
		catch(Exception $e) {	
			$error->setMessage("Impossible d'éxécuter la requête SQL.");
			$error->addRoute("getMemo(), MemoManager_PDO.class.php");
			$error->setWarnLevel(\lib\Error::wl_CRITICAL);
			return $error;
		}
		return $request->fetch(\PDO::FETCH_ASSOC);
	}

	public function saveMemo(Memo $memo)
	{
		$error = new \lib\Error();
		$request = $this->dao->prepare("UPDATE memo SET content = :content WHERE id = :id AND owner = :owner");
		$request->bindValue(':id', (int) $memo->id(), \PDO::PARAM_INT);
		$request->bindValue(':owner', (int) $memo->owner(), \PDO::PARAM_INT);
		$request->bindValue(':content', htmlspecialchars($memo->content()));
		try {
			$request->execute();
		}
		catch(Exception $e) {
			$error->setMessage("Impossible d'éxécuter la requête SQL.");
			$error->addRoute("saveMemo(), MemoManager_PDO.class.php");
			$error->setWarnLevel(\lib\Error::wl_CRITICAL);
			return $error;
		}
		return true;
	}

	public function createMemo()
	{
		$error = new \lib\Error();
		$request = $this->dao->prepare("INSERT INTO memo(id, owner, content) VALUES(NULL, :ownerid, '')");
		$request->bindValue(':ownerid', (int) $this->owner, \PDO::PARAM_INT);
		try {
			$request->execute();
		}
		catch(Exception $e) {
			$error->setMessage("Impossible d'éxécuter la requête SQL.");
			$error->addRoute("createMemo(), MemoManager_PDO.class.php");
			$error->setWarnLevel(\lib\Error::wl_CRITICAL);
			return $error;
		}
		return true;
	}

	public function hasOwnerMemo()
	{
		$request = $this->dao->prepare("SELECT COUNT(*) FROM memo WHERE owner = :ownerid");
		$request->bindValue(':ownerid', (int) $this->owner);
			
		try
		{
			$request->execute();
		}
		catch(Exception $e)
		{
			$error = new \lib\Error();
			$error->setMessage("Impossible d'éxécuter la requête SQL");
			$error->addRoute("hasOwnerMemo(), CodeManager_PDO.class.php");
			$error->setWarnLevel(\lib\Error::wl_CRITICAL);
			return $error;
		}
		return $request->fetchColumn(0);
	}
}
