<?php
	namespace lib\models;
	use \lib\entities\User;
	
	abstract class UserManager extends \lib\Manager
	{
		public abstract function add(User $user);
		public abstract function update(User $user);
		//public abstract function delete(User $user);
		public abstract function load($id);
		public abstract function pseudoToId($pseudo);
		public abstract function checkAuth($pseudo, $password, $method);
		public abstract function emailToId($email);
		public abstract function Auth($pseudo, $password);
		public abstract function isAuth();
		public abstract function getUser();
		public abstract function reloadSession(User $user);
		public abstract function logout();
		public abstract function exists($id);
		public abstract function search($term);
	}
?>
