<?php
	namespace lib\entities;
	class Memo extends \lib\Record
	{
		protected $content,
							$owner;
	
		/* Constructeur */
		public function __construct($pid, $powner, $pcontent)
		{
			$this->id = (int) $pid;
			$this->owner = (int) $powner;
			$this->content = $pcontent;
		}
	
		/* Setters */
		public function setContent($content)
		{
			$this->content = $content;
		}
	
		public function setOwner($owner)
		{
			$this->owner = (int) $owner;
		}
	
		
		/* Getters */
		public function content()
		{
			return $this->content;
		}
	
		public function owner()
		{
			return $this->owner;
		}
	
	}
?>
