<?php
	namespace lib;
	class Regex
	{
		public static function isEmail($email)
		{
			return preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email);
		}

		public static function isName($name)
		{
			/* Letters (Aa) + ' + -  --- 2-32 chars */
			return preg_match("#^[\w-' àáâãäåçèéêëìíîïðòóôõöùúûüýÿ]{2,32}$#u", $name);
		}

		public static function isPseudo($pseudo)
		{
			/* Letters (a) + numbers + ' + - + . + _  --- 3-15 chars */
			return preg_match("#^[a-z0-9._-]{3,15}$#", $pseudo);
		}

		public static function isFolderName($fname)
		{
			/* 1-128 chars */
			if(mb_strlen($fname, 'UTF-8') <= 0 || mb_strlen($fname, 'UTF-8') > 128)
				return false;
			else
				return true;
		}

		public static function isFileName($filename)
		{
			/* 1-128 chars */
			if(mb_strlen($filename, 'UTF-8') <= 0 || mb_strlen($filename, 'UTF-8') > 128)
				return false;
			else
				return true;
		}

		public static function isDate($date)
		{
			/* DD/MM/YY hh:mm */
			return preg_match("#^[01-31]{2}+/[01-12]{2}+/[00-99]{2}+ [00-24]{2}+:[00-60]{2}$#", $date);
		}
		
		public static function isPermissionSerial($serial)
		{
			return preg_match("#^[123];(\d+:[12];)*$#", $serial);
		}
	}
?>
