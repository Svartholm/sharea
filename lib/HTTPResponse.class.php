<?php
	namespace lib;
		
    class HTTPResponse extends ApplicationComponent
    {
        protected $page;
        
        public function addHeader($header)
        {
            header($header);
        }
        
        public function redirect($location)
        {
            header('Location: '.$location);
            exit;
        }

        public function redirect400()
        {
            $this->page = new Page($this->app);
            $this->page->setContentFile(dirname(__FILE__).'/../errors/400.html');
            $this->addHeader('HTTP/1.0 400 Bad Request');
            $this->send();
        }

        public function redirect401()
        {
            $this->page = new Page($this->app);
            $this->page->setContentFile(dirname(__FILE__).'/../errors/401.html');
            $this->addHeader('HTTP/1.0 401 Unauthorized');
            $this->send();
        }

        public function redirect403()
        {
            $this->page = new Page($this->app);
            $this->page->setContentFile(dirname(__FILE__).'/../errors/403.html');
            $this->addHeader('HTTP/1.0 403 Forbidden');
            $this->send();
        }
        
        public function redirect404()
        {
            $this->page = new Page($this->app);
            $this->page->setContentFile(dirname(__FILE__).'/../errors/404.html');
            $this->addHeader('HTTP/1.0 404 Not Found');
            $this->send();
        }
        
        public function redirect500()
        {
            $this->page = new Page($this->app);
            $this->page->setContentFile(dirname(__FILE__).'/../errors/500.html');
            $this->addHeader('HTTP/1.0 500 Internal Server Error');
            $this->send();
        }

        public function send()
        {
            exit($this->page->getGeneratedPage());
        }
        
        public function setPage(Page $page)
        {
            $this->page = $page;
        }
        
        public function setCookie($name, $value = '', $expire = 0, $path = null, $domain = null, $secure = false, $httpOnly = true)
        {
            setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
        }
    }

