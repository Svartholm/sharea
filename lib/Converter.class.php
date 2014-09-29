<?php
	namespace lib;
	class Converter
	{
		public static function size($size, $to = "*")
		{
			switch($to)
				{
					case "o":
						$realsize = $size;
						break;
					case "Ko":
						$realsize = $size / 1000;
						break;
					case "Mo":
						$realsize = $size / 1000 / 1000;
						break;
					case "Go":
						$realsize = $size / 1000 / 1000 / 1000;
						break;
				}
			return $realsize;
		}
	}
?>
