<?php 
namespace Concrete\Core\User;
use Loader;
class ValidationHash {

	/**
	 * generates a random string
     * @param int $len
	 * @return string
	 */
	protected static function generate($len = 64) {
		return Loader::helper('validation/identifier')->getString($len);
	}
	
	/**
	 * Removes old entries for the supplied type
	*/
	protected function removeExpired($type) {
		$lifetime = '5184000'; // 60 days
		do {
			if($type == UVTYPE_CHANGE_PASSWORD) {
				$lifetime = USER_CHANGE_PASSWORD_URL_LIFETIME;
				break;
			}
			if($type == UVTYPE_LOGIN_FOREVER) {
				$lifetime = USER_FOREVER_COOKIE_LIFETIME;
				break;
			}
			break;
		} while (false);
		$db = loader::db();
		$db->query('DELETE FROM UserValidationHashes WHERE type = ? AND uDateGenerated <= ?', array($type, $lifetime));
	}

    /**
     * adds a hash to the lookup table for a user and type, removes any other existing hashes for the same user and type
     * @param int $uID
     * @param int $type
     * @param bool $singeHashAllowed
     * @param int $hashLength
     * @return string
     */
    public static function add($uID, $type, $singeHashAllowed = false, $hashLength = 64) {
		self::removeExpired($type);
		$hash = self::generate($hashLength);
		$db = Loader::db();
		if($singeHashAllowed) {
			$db->Execute("DELETE FROM UserValidationHashes WHERE uID = ? AND type = ?", array($uID, $type));
		}
		$db->Execute("insert into UserValidationHashes (uID, uHash, uDateGenerated, type) values (?, ?, ?, ?)", array($uID, $hash, time(), intval($type)));
		return $hash;
	}
	
	/**
	 * gets the users id for a given hash and type
	 * @param string $hash
	 * @param int $type
	 * @return int | false
	*/
	public static function getUserID($hash, $type) {
		self::removeExpired($type);
		$db = Loader::db();
		$uID = $db->getOne("SELECT uID FROM UserValidationHashes WHERE uHash = ? AND type = ?", array($hash, $type));
		if(is_numeric($uID) && $uID > 0) {
			return $uID;
		} else {
			return false;
		}
	}
}