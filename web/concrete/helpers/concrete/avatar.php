<?
/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteAvatarHelper {
	/**
	* Gets the default avatar
	* @return array $aDir
	*/
	function getStockAvatars() {
		$f = Loader::helper('file');
		$aDir = $f->getDirectoryContents(DIR_FILES_AVATARS_STOCK);
		return $aDir;			
	}
	/** 
	* Outputs the final user avatar
	* @param user object $uo
	* @return string $str
	*/
	function outputUserAvatar($uo, $suppressNone = false, $aspectRatio = 1.0) {	
		if (is_object($uo) && $uo->hasAvatar()) {
			if (file_exists(DIR_FILES_AVATARS . '/' . $uo->getUserID() . '.jpg')) {
				$size = DIR_FILES_AVATARS . '/' . $uo->getUserID() . '.jpg';
				$src = REL_DIR_FILES_AVATARS . '/' . $uo->getUserID() . '.jpg';
			} else {
				// legacy
				$size = DIR_FILES_AVATARS . '/' . $uo->getUserID() . '.gif';
				$src = REL_DIR_FILES_AVATARS . '/' . $uo->getUserID() . '.gif';
			}
			if (file_exists($size)) {
				$isize = getimagesize($size);
				$isize[0] = round($isize[0]*$aspectRatio);
				$isize[1] = round($isize[1]*$aspectRatio);
				
				$str = '<img class="u-avatar" src="' . $src . '" width="' . $isize[0] . '" height="' . $isize[1] . '" alt="' . $uo->getUserName() . '" />';
				return $str;
			}
		}

		if(Config::get('GRAVATAR_FALLBACK')) {
		  return $this->get_gravatar( $uo->getUserEmail(), AVATAR_WIDTH, Config::get('GRAVATAR_IMAGE_SET'), Config::get('GRAVATAR_MAX_LEVEL'), true, $atts = array('alt' => $uo->getUserName()) );
		}

		if (!$suppressNone) {
			return $this->outputNoAvatar($aspectRatio);
		}
	}
	/**
	* gets the image path for a users avatar
	* @param user object $uo
	* @param bool $withNoCacheStr
	* @return string $src
	*/
	public function getImagePath($uo,$withNoCacheStr=true) {
		if (!$uo->hasAvatar()) {
			return false;
		}
		
		$cacheStr = "?" . time();
		if (file_exists(DIR_FILES_AVATARS . '/' . $uo->getUserID() . '.jpg')) {
			$base = DIR_FILES_AVATARS . '/' . $uo->getUserID() . '.jpg';
			$src = REL_DIR_FILES_AVATARS . '/' . $uo->getUserID() . '.jpg';
		} else {
			$base = DIR_FILES_AVATARS . '/' . $uo->getUserID() . '.gif';
			$src = REL_DIR_FILES_AVATARS . '/' . $uo->getUserID() . '.gif';
		}
		if($withNoCacheStr) $src .= $cacheStr;
		if (!file_exists($base)) {
			return "";
		} else {
			return $src;
		}
	}

	/**
	* What to show if the user has no avatar
	* @param int $aspectRatio
	* @return string $str
	*/
	function outputNoAvatar($aspectRatio = 1.0) {
		$str = '<img class="u-avatar" src="' . AVATAR_NONE . '" width="' . AVATAR_WIDTH*$aspectRatio . '" height="' . AVATAR_HEIGHT*$aspectRatio . '" alt="" />';
		return $str;
	}
	
	/**
	* Makes the provided image the avatar for the user
	* It needs to make some various sizes of the image
	* @param string $pointer
	* @param int $uID
	* @return int $uHasAvatar
	*/
	function processUploadedAvatar($pointer, $uID) {
		$uHasAvatar = 0;
		$imageSize = getimagesize($pointer);
		$oWidth = $imageSize[0];
		$oHeight = $imageSize[1];
		
		
		$finalWidth = 0;
		$finalHeight = 0;

		// first, if what we're uploading is actually smaller than width and height, we do nothing
		if ($oWidth < AVATAR_WIDTH && $oHeight < AVATAR_HEIGHT) {
			$finalWidth = $oWidth;
			$finalHeight = $oHeight;
		} else {
			// otherwise, we do some complicated stuff
			// first, we subtract width and height from original width and height, and find which difference is g$
			$wDiff = $oWidth - AVATAR_WIDTH;
			$hDiff = $oHeight - AVATAR_HEIGHT;
			if ($wDiff > $hDiff) {
				// there's more of a difference between width than height, so if we constrain to width, we sh$
				$finalWidth = AVATAR_WIDTH;
				$finalHeight = $oHeight / ($oWidth / AVATAR_WIDTH);
			} else {
				// more of a difference in height, so we do the opposite
				$finalWidth = $oWidth / ($oHeight / AVATAR_HEIGHT);
				$finalHeight = AVATAR_HEIGHT;
			}
		}
		
		$image = imageCreateTrueColor($finalWidth, $finalHeight);
		$white = imagecolorallocate($image, 255, 255, 255);
		imagefill($image, 0, 0, $white);

		switch($imageSize[2]) {
			case IMAGETYPE_GIF:
				$im = imageCreateFromGIF($pointer);
				break;
			case IMAGETYPE_JPEG:
				$im = imageCreateFromJPEG($pointer);
				break;
			case IMAGETYPE_PNG:
				$im = imageCreateFromPNG($pointer);
				break;
		}
		
		
		$newPath = DIR_FILES_AVATARS . '/' . $uID . '.jpg';
		
		if ($im) {
			$res = imageCopyResampled($image, $im, 0, 0, 0, 0, $finalWidth, $finalHeight, $oWidth, $oHeight);
			if ($res) {
				$res2 = imageJPEG($image, $newPath, Loader::helper('image')->defaultJpegCompression());
				if ($res2) {
					$uHasAvatar = 1;
				}
			}
		}

		return $uHasAvatar;
	}
	/**
	* Removes the avatar for the given user
	* @param user object $ui
	*/
	function removeAvatar($ui) {
		if (is_object($ui)) {
			$uID = $ui->getUserID();
		} else {
			$uID = $ui;
		}
		$db = Loader::db();
		$db->query("update Users set uHasAvatar = 0 where uID = ?", array($uID));
	}
	/**
	* Updates the avatar for the given user with the image given in $pointer
	* @param string $pointer
	* @param int $uID
	* @return int $uHasAvatar
	*/
	function updateUserAvatar($pointer, $uID) {
		$uHasAvatar = $this->processUploadedAvatar($pointer, $uID);
		$db = Loader::db();
		$db->query("update Users set uHasAvatar = {$uHasAvatar} where uID = ?", array($uID));
		return $uHasAvatar;
	}
	/**
	* Updates the avatar for the given user with a stock image thats given with $pointer
	* @param string $pointer
	* @param int $uID
	*/
	function updateUserAvatarWithStock($pointer, $uID) {
		if ($pointer != "") {
			if (file_exists(DIR_FILES_AVATARS_STOCK . '/' . $pointer)) {
				$uHasAvatar = $this->processUploadedAvatar(DIR_FILES_AVATARS_STOCK . '/' . $pointer, $uID);
				$db = Loader::db();
				$db->query("update Users set uHasAvatar = {$uHasAvatar} where uID = ?", $uID);
			}
		}
	}

  /**
   * Get either a Gravatar URL or complete image tag for a specified email address.
   *
   * @param string $email The email address
   * @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
   * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
   * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
   * @param bool $img True to return a complete IMG tag False for just the URL
   * @param array $atts Optional, additional key/value attributes to include in the IMG tag
   * @return String containing either just a URL or a complete image tag
   * @source http://gravatar.com/site/implement/images/php/
   */
  function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
	  $url = '//www.gravatar.com/avatar/';
	  $url .= md5( strtolower( trim( $email ) ) );
	  $url .= "?s=$s&d=$d&r=$r";
	  if ( $img ) {
		  $url = '<img src="' . $url . '"';
		  foreach ( $atts as $key => $val )
			  $url .= ' ' . $key . '="' . $val . '"';
		  $url .= ' />';
	  }
	  return $url;
  }

}

?>
