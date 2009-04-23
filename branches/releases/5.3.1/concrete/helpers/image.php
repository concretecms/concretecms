<?php 
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions useful for working with images.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die(_("Access Denied."));
class ImageHelper {

		
	/**
	 * Creates a new image given an original path, a new path, a target width and height.
	 * @params string $originalPath, string $newpath, int $width, int $height
	 * @return void
	 */		
	public function create($originalPath, $newPath, $width, $height) {
		// first, we grab the original image. We shouldn't ever get to this function unless the image is valid
		$imageSize = @getimagesize($originalPath);
		$oWidth = $imageSize[0];
		$oHeight = $imageSize[1];
		$finalWidth = 0;
		$finalHeight = 0;

		// first, if what we're uploading is actually smaller than width and height, we do nothing
		if ($oWidth < $width && $oHeight < $height) {
			$finalWidth = $oWidth;
			$finalHeight = $oHeight;
		} else {
			// otherwise, we do some complicated stuff
			// first, we subtract width and height from original width and height, and find which difference is greater
			$wDiff = $oWidth - $width;
			$hDiff = $oHeight - $height;
//				if ($wDiff > $hDiff) {
			if ($wDiff > $hDiff && (($oHeight / ($oWidth / $width)) < $height)) { // check to ensure that the finalHeight won't be too large still
				// there's more of a difference between width than height, so if we constrain to width, we should be safe
				$finalWidth = $width;
				$finalHeight = $oHeight / ($oWidth / $width);
			} else {
				// more of a difference in height, so we do the opposite
				$finalWidth = $oWidth / ($oHeight / $height);
				$finalHeight = $height;
			}
		}

		$image = @imageCreateTrueColor($finalWidth, $finalHeight);
		switch($imageSize[2]) {
			case IMAGETYPE_GIF:
				$im = @imageCreateFromGIF($originalPath);
				break;
			case IMAGETYPE_JPEG:
				$im = @imageCreateFromJPEG($originalPath);
				break;
			case IMAGETYPE_PNG:
				$im = @imageCreateFromPNG($originalPath);
				break;
		}
		
		if ($im) {
			$res = @imageCopyResampled($image, $im, 0, 0, 0, 0, $finalWidth, $finalHeight, $oWidth, $oHeight);
			if ($res) {
				$res2 = @imageJPEG($image, $newPath, 80);
			}
		}
	}
	
	/** 
	 * Returns a path to the specified item, resized to meet max width and height. $obj can either be
	 * a string (path) or a file object. 
	 * Returns an object with the following properties: src, width, height, alt
	 * @param mixed $obj
	 * @param int $maxWidth
	 * @param int $maxHeight
	 */
	public function getThumbnail($obj, $maxWidth, $maxHeight) {
		if ($obj instanceof File) {
			$path = $obj->getPath();
		} else {
			$path = $obj;
		}		

		// first we check to see if a file with these dimensions exists
		$filename = md5($path . ':' . $maxWidth . ':' . $maxHeight) . '.jpg';
		
		if (!file_exists(DIR_FILES_CACHE . '/' . $filename)) {
			// create image there
			$this->create($path, DIR_FILES_CACHE . '/' . $filename, $maxWidth, $maxHeight);
		}
		
		$src = REL_DIR_FILES_CACHE . '/' . $filename;
		$abspath = DIR_FILES_CACHE . '/' . $filename;
		$thumb = new stdClass;
		if (isset($abspath) && file_exists($abspath)) {			
			$thumb->src = $src;
			$dimensions = getimagesize($abspath);
			$thumb->width = $dimensions[0];
			$thumb->height = $dimensions[1];
			return $thumb;
		}					
	}
	
	/** 
	 * Runs getThumbnail on the path, and then prints it out as an XHTML image
	 */
	public function outputThumbnail($obj, $maxWidth, $maxHeight, $alt = null) {
		$thumb = $this->getThumbnail($obj, $maxWidth, $maxHeight);
		print '<img class="ccm-output-thumbnail" alt="' . $alt . '" src="' . $thumb->src . '" width="' . $thumb->width . '" height="' . $thumb->height . '" />';
	}
	
	public function output($obj, $alt = null) {
		$s = @getimagesize($obj->getPath());
		print '<img class="ccm-output-image" alt="' . $alt . '" src="' . $obj->getRelativePath() . '" width="' . $s[0] . '" height="' . $s[1] . '" />';
	}


}