<?php
	namespace lib\models;
	use lib\entities\Profile;
	abstract class ProfileManager extends \lib\Manager
	{
		abstract public function add(Profile $profile);
		abstract public function delete(Profile $profile);
		abstract public function exists($row, $value);
		abstract public function load($id);
		abstract public function pseudoToId($pseudo);
		abstract public function profileIdToId($id_profile);
	}
?>
