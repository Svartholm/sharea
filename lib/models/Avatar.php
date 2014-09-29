<?php
// Need to install php5-gd
class Avatar {
	
	function resizeImg($image, $newimage, $width=0, $height=0) {
			if(!function_exists('ImageTypes')) {
				echo "php5-gd is missing.";
			}
			list($oldwidth,$oldheight,$type) = GetImageSize($image);
			if ($im = self::ReadImageFromFile($image, $type)) {
				if ($width==0)
					$width = ($height / $oldheight) * $oldwidth;
				else if ($height==0)
					$height = ($width / $oldwidth) * $oldheight;
				elseif ($height && ($oldwidth < $oldheight))
					$width = ($height / $oldheight) * $oldwidth;
				else
					$height = ($width / $oldwidth) * $oldheight;
	 
				if (function_exists('ImageCreateTrueColor'))
					$im2 = ImageCreateTrueColor($width,$height);
				else
					$im2 = ImageCreate($width,$height);
	 
				if (function_exists('imagealphablending'))
					imagealphablending($im2, false);
				if (function_exists('imagesavealpha'))
					imagesavealpha ($im2 , true);
	 
				if (function_exists('ImageCopyResampled'))
					ImageCopyResampled($im2,$im,0,0,0,0,$width,$height,$oldwidth,$oldheight);
				else
					ImageCopyResized($im2,$im,0,0,0,0,$width,$height,$oldwidth,$oldheight);
	 
				if (self::WriteImageToFile($im2, $newimage, $type))
					return true;
			}
			return false;
		}
		
		private function ReadImageFromFile($filename, $type) {
			$imagetypes = ImageTypes();
			switch ($type) {
				case 1 :
					if ($imagetypes & IMG_GIF)
						return $im = ImageCreateFromGIF($filename);
					break;
				case 2 :
					if ($imagetypes & IMG_JPEG)
						return ImageCreateFromJPEG($filename);
					break;
				case 3 :
					if ($imagetypes & IMG_PNG)
						return ImageCreateFromPNG($filename);
					break;
				default:
					return 0;
			}
		}
		private function WriteImageToFile($im, $filename, $type) {
			switch ($type) {
				case 1 :
					return ImageGIF($im, $filename);
				case 2 :
					return ImageJpeg($im, $filename, 85);
				case 3 :
					return ImagePNG($im, $filename);
				default:
					return false;
			}
		}
}