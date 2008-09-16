<?

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
	
}