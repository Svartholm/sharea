<?php
	namespace lib;
	class JSONPage extends ApplicationComponent
	{
		protected $contentFile = "";
		protected $vars = array();
		
		public function addVar($var, $value)
		{
		
		 	if (!is_string($var) || is_numeric($var) || empty($var))
      		{
        		throw new InvalidArgumentException('Variable name should be a non-empty string');
        	}
        
			$this->vars[$var] = $value;
		}
		
		public function getGeneratedPage()
		{
			if(!empty($this->contentFile))
				{	
					$fd = fopen($this->contentFile, 'r');
					if($fd !== false)
						{
							while(!feof($fd))
								{
									$line = fgets($fd);
									if(!empty($line))
										{
											$arr = explode(' ##=>## ', $line);
											$this->vars[$arr[0]] = $arr[1];
										}
								}
							fclose($fd);
							return json_encode($this->vars);
						}
					else
						throw new Exception("Impossible to read the template");
				}
			else
				{
					return json_encode($this->vars);
				}
		}
		
		public function setContentFile($contentFile)
    {
    	if (!is_string($contentFile) || empty($contentFile))
      	{
        	throw new InvalidArgumentException('Invalid view.');
        }
            
      $this->contentFile = $contentFile;
    }
	}
?>
