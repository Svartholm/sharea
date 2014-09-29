<?php
	namespace lib;
	class ToolBox
	{
		public static function is_Error($object)
		{
			if(is_object($object) && get_class($object) == "lib\Error")
				return true;
			else 
				return false;
		}
		
		public static function stripAccents($string)
		{
			$string = htmlentities($string, ENT_NOQUOTES, 'utf-8');
			$string = preg_replace('#\&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring)\;#', '\1', $string);
			$string = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $string);
			$string = preg_replace('#\&[^;]+\;#','', $string);
			return $string;
		}
		
		public static function html($text)
		{
			return htmlspecialchars($text, ENT_COMPAT, 'UTF-8');
		}
		
		public static function unhtml($text)
		{
			return htmlspecialchars_decode($text);
		}
		
		public static function purify($object)
		{
			if(is_string($object))
				{
					return self::html($object);
				}
			else if(is_object($object))
				{
					if(is_callable(array($object, 'get_class_vars')))
				  	{
				    	$class_vars = $object->get_class_vars();
				      foreach($class_vars as $varname=>$class_var)
				      	{
				      		
				        	if(is_callable(array($object, $varname)))
				        		{
						      		$element = $object->$varname();
								    	if(is_string($element))
								    		{
													$method = 'set'.ucfirst($varname);
													if(is_callable(array($object, $method)))
														$object->$method(self::html($class_var));
												}
											else if(is_object($element))
												{
													$method = 'set'.ucfirst($varname);
													if(is_callable(array($object, $method)))
														{
															$object->$method(self::purify($class_var));
														}
												}
											else if(is_array($element))
												{
													foreach($element as $var_elem)
														{
															$var_elem = self::purify($var_elem);
														}
												}
										}
				        }
				    }
				  }
			else if(is_array($object))
				{
					foreach($object as $element)
						{
							$element = self::purify($element);
						}
				}
        
			return $object;
		}
		
		public static function generateRandomString($length = 10)
		{
		  $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		  $randomString = "";
		  for ($i = 0; $i < $length; $i++)
		  	{
		      $randomString .= $characters[rand(0, strlen($characters) - 1)];
		  	}
		  return $randomString;
		}
		
		public static function cut($string, $length, $suffix = "...", $html = false)
		{
			if(strlen($string) <= $length)
				{
					return $string;
				}
			
			if(!$html)
				{
					$shortstring = mb_substr($string, 0, $length, 'UTF-8');
					$shortstring .= $suffix;
					return $shortstring;	
				}
			else
				{
					$string = ToolBox::unhtml($string);
					$shortstring = mb_substr($string, 0, $length, 'UTF-8');
					$shortstring .= $suffix;
					return ToolBox::html($shortstring);
				}
		}
	}
?>
