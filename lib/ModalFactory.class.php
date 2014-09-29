<?php
	namespace lib;
	class ModalFactory
	{
		protected $directory;
		
		public function __construct()
		{
            /* Routing depending on the language */
            if(isset($_COOKIE['lang'])) {
                $langue = $_COOKIE['lang'];
            }
            else {
                if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                    $langue = explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']);
                    $langue = strtolower(substr(chop($langue[0]),0,2));
                }
            }
            /* If language exists in our system */
            if(!is_dir(dirname(__FILE__).'/../'.$langue.'_modals')) {
                $langue = 'en';
            }

			$this->directory = dirname(__FILE__).'/../'.$langue.'_modals/';
		}
		
		public function getModal($modal)
		{
			return $this->directory.strtolower($modal).'.php';
		}
	}
?>
