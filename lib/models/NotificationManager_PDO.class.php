<?php
	namespace lib\models;
	use \lib\entities\Notification;
	
	class NotificationManager_PDO extends NotificationManager
	{
		public function add(Notification $notif)
		{
			$request = $this->dao->prepare("INSERT INTO notifications(id, message, receiver, link, thumbnail, state) VALUES(:id, :message, :receiver, :link, :thumbnail, :state)");
			$request->bindValue(':id', (int) $notif->id(), \PDO::PARAM_INT);
			$request->bindValue(':message', $notif->message());
			$request->bindValue(':receiver', (int) $notif->receiver(), \PDO::PARAM_INT);
			$request->bindValue(':link', $notif->link());
			$request->bindValue(':thumbnail', (int) $notif->thumbnail(), \PDO::PARAM_INT);
			$request->bindValue(':state', (int) $notif->state(), \PDO::PARAM_INT);
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->setMessage($e->getMessage());
					$error->addRoute("add(), NotificationManager_PDO.class.php");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
		}
		
		public function count()
		{
			if($this->owner != '*')
				{
					$request = $this->dao->prepare("SELECT COUNT(id) FROM notifications WHERE receiver = :receiver");
					$request->bindValue(':receiver',(int) $this->owner, \PDO::PARAM_INT);
				}
			else
					$request = $this->dao->prepare("SELECT COUNT(id) FROM notifications");
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{	
					$error = new \lib\Error();
					$error->setMessage($e->getMessage());
					$error->addRoute("count(), NotificationManager_PDO.class.php");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					return $error;
				}
			
			$nb = $request->fetchColumn(0);
			return $nb;
		}
		
		/* Returns an array with all notifications (id, message, receiver etc ...) */
		public function getNotifications($only_recent = true)
		{
			if($this->owner != '*')
				{
					if($only_recent)
						{
							$request = $this->dao->prepare("SELECT * FROM notifications WHERE receiver = :receiver AND state = :state ORDER BY date");
							$request->bindValue(':receiver',(int) $this->owner, \PDO::PARAM_INT);
							$request->bindValue(':state',(int) Notification::st_UNSEEN, \PDO::PARAM_INT);
						}
					else
						{
							$request = $this->dao->prepare("SELECT * FROM notifications WHERE receiver = :receiver ORDER BY date");
							$request->bindValue(':receiver',(int) $this->owner, \PDO::PARAM_INT);
						}
				}
			else
					$request = $this->dao->prepare("SELECT * FROM notifications");
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{	
					$error = new \lib\Error();
					$error->setMessage($e);
					$error->addRoute("getNotifications(), NotificationManager_PDO.class.php");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					return $error;
				}
			
			$notifs = $request->fetchAll();
			return $notifs;
		}
		
		public function update(Notification $notif)
		{
			$request = $this->dao->prepare("UPDATE notifications SET message = :message, receiver = :receiver, link = :link, thumbnail = :thumbnail, state = :state WHERE id = :id");
			$request->bindValue(':id', (int) $notif->id(), \PDO::PARAM_INT);
			$request->bindValue(':message', $notif->message());
			$request->bindValue(':receiver', (int) $notif->receiver(), \PDO::PARAM_INT);
			$request->bindValue(':link', $notif->link());
			$request->bindValue(':thumbnail', (int) $notif->thumbnail(), \PDO::PARAM_INT);
			$request->bindValue(':state', (int) $notif->state(), \PDO::PARAM_INT);
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->setMessage($e->getMessage());
					$error->addRoute("update(), NotificationManager_PDO.class.php");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
		}
		
		public function load($id)
		{
			$request = $this->dao->prepare("SELECT * FROM notifications WHERE id = :id");
			$request->bindValue(':id', (int) $id, \PDO::PARAM_INT);
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->setMessage($e->getMessage());
					$error->addRoute("load(), NotificationManager_PDO.class.php");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
			
			$notif = new Notification($request->fetch(\PDO::FETCH_ASSOC));
			return $notif;
		}
		
		public function delete($id)
		{
			$request = $this->dao->prepare("DELETE FROM notifications WHERE id = :id");
			$request->bindValue(':id', (int) $id, \PDO::PARAM_INT);
			
			try
				{
					$request->execute();
				}
			catch(Exception $e)
				{
					$error = new \lib\Error();
					$error->setMessage($e->getMessage());
					$error->addRoute("delete(), NotificationManager_PDO.class.php");
					$error->setWarnLevel(\lib\Error::wl_CRITICAL);
					
					return $error;
				}
		}
	}
?>
