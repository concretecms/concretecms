<?
/**
 * @package Helpers
 * @category Concrete
 * @author Jeremy Logan <jeremy.logan@gmail.com>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions useful functions for working with mime-types.
 * @package Helpers
 * @category Concrete
 * @author Jeremy Logan <jeremy.logan@gmail.com>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Helper_Mime {

	static $mime_types_and_extensions = array(
			'application/atom+xml'          => 'atom',
			'application/mac-binhex40'      => 'hqx',
			'application/mathml+xml'        => 'mathml',
			'application/msword'            => 'doc',
			'application/oda'               => 'oda',
			'application/ogg'               => 'ogx',
			'application/pdf'               => 'pdf',
			'application/postscript'        => 'ps',
			'application/rdf+xml'           => 'rdf',
			'application/smil'              => 'smil',
			'application/x-director'        => 'dxr',
			'application/x-dvi'             => 'dvi',
			'application/x-futuresplash'    => 'spl',
			'application/x-javascript'      => 'js',
			'application/x-latex'           => 'latex',
			'application/x-shockwave-flash' => 'swf',
			'application/x-stuffit'         => 'sit',
			'application/x-tar'             => 'tar',
			'application/x-tex'             => 'tex',
			'application/x-texinfo'         => 'texinfo',
			'application/xhtml+xml'         => 'xhtml',
			'application/xml'               => 'xsl',
			'application/xml-dtd'           => 'dtd',
			'application/xslt+xml'          => 'xslt',
			'application/zip'               => 'zip',
			'audio/midi'                    => 'midi',
			'audio/mp4a-latm'               => 'm4p',
			'audio/mpeg'                    => 'mpga',
			'audio/x-aiff'                  => 'aiff',
			'audio/x-mpegurl'               => 'm3u',
			'audio/x-pn-realaudio'          => 'ram',
			'audio/x-wav'                   => 'wav',
			'audio/ogg'                     => 'ogg',
			'audio/ogg'                     => 'oga',
			'chemical/x-pdb'                => 'pdb',
			'chemical/x-xyz'                => 'xyz',
			'image/bmp'                     => 'bmp',
			'image/cgm'                     => 'cgm',
			'image/gif'                     => 'gif',
			'image/ief'                     => 'ief',
			'image/jp2'                     => 'jp2',
			'image/jpeg'                    => 'jpg',
			'image/pict'                    => 'pict',
			'image/png'                     => 'png',
			'image/svg+xml'                 => 'svg',
			'image/tiff'                    => 'tiff',
			'image/vnd.djvu'                => 'djvu',
			'image/vnd.wap.wbmp'            => 'wbmp',
			'image/x-cmu-raster'            => 'ras',
			'image/x-icon'                  => 'ico',
			'image/x-macpaint'              => 'pntg',
			'image/x-portable-anymap'       => 'pnm',
			'image/x-portable-bitmap'       => 'pbm',
			'image/x-portable-graymap'      => 'pgm',
			'image/x-portable-pixmap'       => 'ppm',
			'image/x-quicktime'             => 'qtif',
			'image/x-xbitmap'               => 'xbm',
			'image/x-xpixmap'               => 'xpm',
			'model/vrml'                    => 'wrl',
			'text/css'                      => 'css',
			'text/html'                     => 'html',
			'text/plain'                    => 'txt',
			'text/richtext'                 => 'rtx',
			'text/rtf'                      => 'rtf',
			'text/sgml'                     => 'sgml',
			'video/mp4'                     => 'mp4',
			'video/mpeg'                    => 'mpg',
			'video/quicktime'               => 'qt',
			'video/x-m4v'                   => 'm4v',
			'video/x-msvideo'               => 'avi',
			'video/ogg'                     => 'ogv',
			'video/webm'                    => 'webm',
			'video/x-ms-wmv'		=> 'wmv'
		);
		
	public function mimeFromExtension($extension) {
		$extension = strtolower($extension);
		$mime = array_search($extension, MimeHelper::$mime_types_and_extensions);
		return $mime;
		
	}
	
	/** 
	 * Converts a known mime-type into it's common file extension. 
	 * @todo: Maybe add more mime-types?
	 * @param string $mimeType
	 * @return string $extension or bolean false
	 */
	public function mimeToExtension($mimeType) {		
		if (array_key_exists($mimeType, MimeHelper::$mime_types_and_extensions)) 
			return MimeHelper::$mime_types_and_extensions[$mimeType];
		return false;
	}
	
}
?>
