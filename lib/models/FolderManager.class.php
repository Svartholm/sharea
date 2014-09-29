<?php
	namespace lib\models;
	use \lib\entities\Folder;
	abstract class FolderManager extends \lib\Manager
	{
		public abstract function add(Folder $folder);
		public abstract function delete($path);
		public abstract function load($id);
		public abstract function update(Folder $folder);
		public abstract function getFolders();
		public abstract function getFolderPath(Folder $folder);
		public abstract function exists($id);
		public abstract function path_to_id($path);
		public abstract function getFoldersOf(Folder $folder);
		public abstract function deleteAllFolders();
		public abstract function getMainFolder();
		public abstract function searchFolder($foldername);
	}
?>
