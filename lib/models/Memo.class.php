<?php

class Memo
{
	protected $id, $content, $owner;

	/* Constructeur */
	public function __construct($pid, $powner, $pcontent)
	{
		$this->id = (int) $pid;
		$this->owner = (int) $powner;
		$this->content = $pcontent;
	}

	/* Setters */
	public function setId($id)
	{
		$this->id = (int) $id;
	}

	public function setContent($content)
	{
		$this->content = $content;
	}

	public function setOwner($owner)
	{
		$this->owner = (int) $owner;
	}

	
	/* Getters */
	public function id()
	{
		return $this->id;
	}

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
