<?php
		namespace lib;
		
    class HTTPRequest extends ApplicationComponent
    {
        public function addGetVar($key, $value)
        {
        	$_GET[$key] = $value;
        }
        
        public function cookieData($key)
        {
        	return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
        }
        
        public function cookieExists($key)
        {
        	return isset($_COOKIE[$key]);
        }
        
        public function getData($key)
        {
        	return isset($_GET[$key]) ? $_GET[$key] : null;
        }
        
        public function method()
	    {
		    return $_SERVER['REQUEST_METHOD'];
		}
        
        public function getExists($key)
        {
        	return isset($_GET[$key]);
        }
        
        public function postData($key)
        {
        	return isset($_POST[$key]) ? $_POST[$key] : null;
        }
        
        public function postExists($key)
        {
        	return isset($_POST[$key]);
        }
        
        public function hasFile()
        {
        	/* Has a file been sent by a form ? */
        	return empty($_FILES) ? false : true;
        }
        
        public function fileExists($file, $key)
        {
        	/* Tell if a certain var is in $_FILES array */
       		return isset($_FILES[$file][$key]);
        }
        
        public function fileData($key)
        {
        	/* Return a value in the $_FILES array */
        	return isset($_FILES[$key]) ? $_FILES[$key] : null;
        }
        
        public function getWholeFile()
        {
        	/* Return the $_FILES array */
        	return isset($_FILES) ? $_FILES : null;
        }
        
        public function requestURI()
        {
        	/* Return the request URI */
            return $_SERVER['REQUEST_URI'];
        }
        
        public function getHeaders()
        {
        	/* Return all the hearders */
        	return getallheaders();
        }
    }

