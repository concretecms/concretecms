<?php

namespace Concrete\Core\Application\Service;
use Concrete\Core\File\StorageLocation\StorageLocation;
use Loader;
use Config;
use \Concrete\Core\Authentication\AuthenticationType;
class Avatar {

	/**
	* Outputs the final user avatar
	* @param user object $uo
	* @return string $str
	*/

	function outputUserAvatar($uo, $suppressNone = false, $aspectRatio = 1.0) {
		if (is_object($uo)) {
			$ati = $this->getAuthTypeImagePath($uo);
            $width = Config::get('concrete.icons.user_avatar.width') * $aspectRatio;
            $height = Config::get('concrete.icons.user_avatar.height') * $aspectRatio;

			if ($uo->hasAvatar()) {
                $src = $this->getImagePath($uo);
                $str = '<img class="u-avatar" src="' . $src . '" width="' . $width . '" height="' . $height . '" '
                    . 'alt="' . $uo->getUserName() . '" />';

                return $str;
			} else if(Config::get('concrete.user.gravatar.enabled')) {
                return $this->get_gravatar( $uo->getUserEmail(),
                    Config::get('concrete.icons.user_avatar.width'),
                    Config::get('concrete.user.gravatar.image_set'),
                    Config::get('concrete.user.gravatar.max_level'),
                    true, $atts = array('alt' => $uo->getUserName())
                );
            } else {
				if ($ati) {
					return "<img class='u-authType-avatar' src='$ati'>";
				}
			}
		}

		if (!$suppressNone) {
			return $this->outputNoAvatar($aspectRatio);
		}
	}

	public function getAuthTypeImagePath($uo) {
		$lat = $uo->getUserObject()->getLastAuthType();
		if ($lat > 1) {
			try {
				$at = AuthenticationType::getByID($lat);
				if (method_exists($at->controller, 'getUserImagePath')) {
					$uimgpath = $at->controller->getUserImagePath($uo);
					return $uimgpath;
				}
			} catch(Exception $e) {}
		}
		return false;
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
        $fsl = StorageLocation::getDefault();
        $fs = $fsl->getFileSystemObject();
        if ($fs->has(REL_DIR_FILES_AVATARS . '/' . $uo->getUserID() . '.jpg')) {
            $configuration = $fsl->getConfigurationObject();
            $src = $configuration->getPublicURLToFile(REL_DIR_FILES_AVATARS . '/' . $uo->getUserID() . '.jpg');
            $cacheStr = "?" . time();
            if($withNoCacheStr) $src .= $cacheStr;

            return $src;
        }
	}

	/**
	* What to show if the user has no avatar
	* @param int $aspectRatio
	* @return string $str
	*/
	function outputNoAvatar($aspectRatio = 1.0) {
		$str = '<img class="u-avatar" src="' . Config::get('concrete.icons.user_avatar.default') .
            '" width="' . Config::get('concrete.icons.user_avatar.width') * $aspectRatio . '" height="' .
            Config::get('concrete.icons.user_avatar.height') * $aspectRatio . '" alt="" />';
		return $str;
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
