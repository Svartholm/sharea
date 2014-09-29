<?php
	namespace lib;
	class JSONResponse extends ApplicationComponent
	{
		protected $page;
		
		public function setPage(JSONPage $page)
		{
			$this->page = $page;
		}
		
		public function send()
		{
			exit($this->page->getGeneratedPage());
		}
		
		public function redirect404()
		{
		}
	}
?>
