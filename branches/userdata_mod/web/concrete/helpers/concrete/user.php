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
class ConcreteUserHelper {

	function outputUserAvatar($uo, $suppressNone = false, $aspectRatio = 1.0) {			
		if ($uo->hasAvatar()) {
			$size = DIR_FILES_AVATARS . '/' . $uo->getUserID() . '.gif';
			$isize = getimagesize($size);
			$isize[0] = round($isize[0]*$aspectRatio);
			$isize[1] = round($isize[1]*$aspectRatio);
			
			$str = '<img class="u-avatar" src="' . REL_DIR_FILES_AVATARS . '/' . $uo->getUserID() . '.gif" width="' . $isize[0] . '" height="' . $isize[1] . '" alt="' . $uo->getUserName() . '" />';
			return $str;
		} else if (!$suppressNone) {
			return $this->outputNoAvatar($aspectRatio);
		}
	}
	
	function outputNoAvatar($aspectRatio = 1.0) {
		$str = '<img class="u-avatar" src="' . AVATAR_NONE . '" width="' . AVATAR_WIDTH*$aspectRatio . '" height="' . AVATAR_HEIGHT*$aspectRatio . '" alt="" />';
		return $str;
	}
	
	function getOnlineNow($uo, $showSpacer = true) {
		$ul = 0;
		if (is_object($uo)) {
			// user object
			$ul = $uo->getLastOnline();
		} else if (is_numeric($uo)) {
			$db = Loader::db();
			$ul = $db->getOne("select uLastOnline from Users where uID = {$uo}");
		}

		$online = (time() - $ul) <= ONLINE_NOW_TIMEOUT;			
		
		if ($online) {
			
			return ONLINE_NOW_SRC_ON;
		} else {
			if ($showSpacer) {
				return ONLINE_NOW_SRC_OFF;
			}
			
		}
	}

}
