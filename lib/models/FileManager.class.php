<?php
	namespace lib\models;
	use \lib\entities\File;
	abstract class FileManager extends \lib\Manager
	{
		public abstract function add(File $file, $passthrough = false);
		public abstract function delete($id);
		public abstract function load($id); 
		public abstract function getFilePath(File $file); 
		public abstract function exists($id);     
		public abstract function pathToId($path);
		public abstract function getFilesOfFolder($folder); 
		public abstract function getFilesOfType($type);
		public abstract function deleteAllFiles();
		public abstract function searchFile($file);
	}
?>
