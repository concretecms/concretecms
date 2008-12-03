<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
/**
 * @package Helpers
 * @subpackage Validation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Helper elements for validating uploaded and existing files in Concrete.
 * @package Helpers
 * @subpackage Validation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class ValidationFileHelper {

	/** 
	 * Tests whether the passed item a valid image.
	 * @param $pathToImage
	 * @return bool
	 */
	public function image($pathToImage) {
	
		/* compatibility if exif functions not available (--enable-exif) */
		if ( ! function_exists( 'exif_imagetype' ) ) {
			function exif_imagetype ( $filename ) {
				if ( ( list($width, $height, $type, $attr) = getimagesize( $filename ) ) !== false ) {
					return $type;
				}
				return false;
			}
		}
	
		$val = @exif_imagetype($pathToImage);
		return (in_array($val, array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG)));
	}
	
	/** 
	 * Tests whether a file exists
	 * @todo Should probably have a list of valid file types that could be passed
	 * @return bool
	 */
	public function file($pathToFile) {
		return file_exists($pathToFile);
	}
	
	
	/** 
	 * Tests whether the passed filename has a valid file extension
	 */
	public function filetype($file) {
		$ext = str_replace('*.', '', UPLOAD_FILE_EXTENSIONS_ALLOWED);
		$ext = strtolower($ext);
		$exta = explode(';', $ext);
		
		$ext = strtolower(strrchr($file, '.'));
		return in_array(substr($ext, 1), $exta);
	}
}