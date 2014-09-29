<?php		
		namespace lib;
		
    class Page extends ApplicationComponent
    {
        protected $contentFile;
        protected $vars = array();
        
        public function addVar($var, $value, $passthrough = false)
        {
            if (!is_string($var) || is_numeric($var) || empty($var))
            {
                throw new InvalidArgumentException('Le nom de la variable doit être une chaine de caractère non nulle');
            }
            
           if($passthrough)
           	{
            	$this->vars[$var] = $value;
            }
           else
           	{
            	$this->vars[$var] = \lib\ToolBox::purify($value);
            }
        }
        
        public function getGeneratedPage()
        {
            if (!file_exists($this->contentFile))
            {
                throw new RuntimeException('La vue spécifiée n\'existe pas');
            }
            
            $user_manager = $this->app->user_manager;
            $friend_manager = $this->app->friend_manager;
            extract($this->vars);
            
            ob_start();
            require $this->contentFile;
            $content = ob_get_clean();
            
            ob_start();

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
            if(!is_file(dirname(__FILE__).'/../apps/'.$this->app->name().'/templates/'.$langue.'_layout.php')) {
                $langue = 'en';
            }

            require dirname(__FILE__).'/../apps/'.$this->app->name().'/templates/'.$langue.'_layout.php';
            return ob_get_clean();
        }
        
        public function setContentFile($contentFile)
        {
            if (!is_string($contentFile) || empty($contentFile))
            {
                throw new InvalidArgumentException('La vue spécifiée est invalide');
            }
            
            $this->contentFile = $contentFile;
        }
    }

