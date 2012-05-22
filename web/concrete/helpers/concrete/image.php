<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Internal functions useful for working with images in the image editor.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteImageHelper {

	public function determineImageScale($sourceWidth, $sourceHeight, $targetWidth, $targetHeight) {
		$scalex =  $targetWidth / $sourceWidth;
		$scaley =  $targetHeight / $sourceHeight;
		return min($scalex, $scaley);
	}
	
	function startImageProcess($f){
		$function = '';
		$ext = strtolower($f->getExtension());
		//$ext = ($f->getExtension());
		switch($ext){
			case "png":
				$function = "imagecreatefrompng";
				break;
			case "jpeg":
				$function = "imagecreatefromjpeg";
				break;
			case "jpg":
				$function = "imagecreatefromjpeg";
				break;
			case "gif":
				$function = "imagecreatefromgif";
				break;
		}
		if ($function) {
			$image = $function($f->getPath());
			return $image;
		}
	}
	
	function parseImage($ext,$img,$file = null){
		switch(strtolower($ext)){
			case "png":
				imagepng($img,($file != null ? $file : ''));
				break;
			case "jpeg":
				imagejpeg($img,($file ? $file : ''),90);
				break;
			case "jpg":
				imagejpeg($img,($file ? $file : ''),90);
				break;
			case "gif":
				imagegif($img,($file ? $file : ''));
				break;
		}
	}
	
	function setTransparency($imgSrc,$imgDest,$ext){	
		if($ext == "png" || $ext == "gif"){
			$trnprt_indx = imagecolortransparent($imgSrc);
			// If we have a specific transparent color
			if ($trnprt_indx >= 0) {
				// Get the original image's transparent color's RGB values
				$trnprt_color    = imagecolorsforindex($imgSrc, $trnprt_indx);
				// Allocate the same color in the new image resource
				$trnprt_indx    = imagecolorallocate($imgDest, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
				// Completely fill the background of the new image with allocated color.
				imagefill($imgDest, 0, 0, $trnprt_indx);
				// Set the background color for new image to transparent
				imagecolortransparent($imgDest, $trnprt_indx);
			}
			// Always make a transparent background color for PNGs that don't have one allocated already
			elseif ($ext == "png") {
				// Turn off transparency blending (temporarily)
				imagealphablending($imgDest, true);
				// Create a new transparent color for image
				$color = imagecolorallocatealpha($imgDest, 0, 0, 0, 127);
				// Completely fill the background of the new image with allocated color.
				imagefill($imgDest, 0, 0, $color);
				// Restore transparency blending
				imagesavealpha($imgDest, true);
			}
	
		}
	}	

}