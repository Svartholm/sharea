<?php
	namespace lib;
	class UTF8
	{
		public function substr_replace($str, $repl, $start, $length = NULL)
		{
			preg_match_all('/./us', $str, $ar);
			preg_match_all('/./us', $repl, $rar);
			if($length === NULL)
				{
				  $length = mb_strlen($str);
				}
				
			array_splice($ar[0], $start, $length, $rar[0]);
			return join('',$ar[0]);
		}
	}
?>
